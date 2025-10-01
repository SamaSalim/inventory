<?php

namespace App\Controllers\Item;

use App\Controllers\BaseController;
use App\Models\ItemOrderModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;
use finfo;

class Attachment extends BaseController
{
    private ItemOrderModel $model;

    public function __construct()
    {
        $this->model = new ItemOrderModel();
    }

    /**
     * Upload attachments for a specific item return
     * This handles multiple files per item using asset_num as identifier
     */
    public function upload()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً'
            ]);
        }

        $loggedEmployeeId = session()->get('employee_id');
        $assetNums = $this->request->getPost('asset_nums');
        $comments  = $this->request->getPost('comments');

        if (empty($assetNums) || !is_array($assetNums)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'لم يتم تحديد أي عناصر للترجيع'
            ]);
        }

        // Get usage status for "رجيع" (returned)
        $usageStatusModel = new \App\Models\UsageStatusModel();
        $returnedStatus = $usageStatusModel->where('usage_status', 'رجيع')->first();
        
        if (!$returnedStatus) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'حالة "رجيع" غير موجودة في النظام'
            ]);
        }

        // Create upload directory if it doesn't exist
        $uploadPath = WRITEPATH . 'uploads/return_attachments';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $successCount = 0;
        $failedItems  = [];
        $allFiles = $this->request->getFiles();

        foreach ($assetNums as $assetNum) {
            // Find the item by asset number
            $originalItem = $this->model->where('asset_num', $assetNum)->first();
            
            if (!$originalItem) {
                $failedItems[] = "الأصل رقم: $assetNum غير موجود";
                continue;
            }

            // === FILE UPLOAD HANDLING ===
            $uploadedFileNames = [];
            
            if (isset($allFiles['attachments'][$assetNum])) {
                foreach ($allFiles['attachments'][$assetNum] as $file) {
                    
                    if (!$file->isValid()) {
                        $error_code = $file->getError();
                        if ($error_code !== UPLOAD_ERR_NO_FILE) {
                            $failedItems[] = "خطأ في الملف للأصل: $assetNum - " . $file->getErrorString();
                        }
                        continue;
                    }

                    // Validate file size (5MB max)
                    if ($file->getSizeByUnit("mb") > 5) {
                        $failedItems[] = "الملف كبير جداً للأصل: $assetNum - " . $file->getName();
                        continue;
                    }

                    // Validate file type
                    $allowedMimes = [
                        "image/png", 
                        "image/jpeg", 
                        "image/jpg",
                        "application/pdf",
                        "application/msword",
                        "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                    ];

                    if (!in_array($file->getMimeType(), $allowedMimes)) {
                        $failedItems[] = "نوع ملف غير مسموح للأصل: $assetNum - " . $file->getName();
                        continue;
                    }

                    // Generate unique filename: assetNum_timestamp_randomName
                    $newName = $assetNum . '_' . time() . '_' . $file->getRandomName();
                    
                    // Move file to upload directory
                    if ($file->move($uploadPath, $newName)) {
                        $uploadedFileNames[] = $newName;
                    } else {
                        $failedItems[] = "فشل رفع الملف للأصل: $assetNum - " . $file->getName();
                    }
                }
            }

            // Determine attachment path
            // If new files uploaded, use them; otherwise keep existing attachment
            $attachmentPath = !empty($uploadedFileNames) 
                ? implode(',', $uploadedFileNames) 
                : $originalItem->attachment;

            // === UPDATE ITEM RECORD ===
            $updateData = [
                'created_by'      => $loggedEmployeeId,
                'usage_status_id' => $returnedStatus->id,
                'note'            => $comments[$assetNum] ?? 'تم الترجيع',
                'attachment'      => $attachmentPath,
                'updated_at'      => date('Y-m-d H:i:s')
            ];

            // Use protect(false) to allow updating the attachment field directly
            $updated = $this->model->protect(false)
                                   ->update($originalItem->item_order_id, $updateData);

            if ($updated) {
                $successCount++;
            } else {
                $failedItems[] = "فشل تحديث الأصل رقم: $assetNum";
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'فشل في حفظ البيانات'
            ]);
        }

        $message = "تم تحديث $successCount عنصر بنجاح";
        if (!empty($failedItems)) {
            $message .= "\n\nفشل التحديث: " . implode(', ', $failedItems);
        }

        return $this->response->setJSON([
            'success'        => true,
            'message'        => $message,
            'updated_count'  => $successCount,
            'failed_count'   => count($failedItems)
        ]);
    }

    /**
     * Get/serve attachment file with proper headers
     */
    public function get($itemOrderId)
    {
        $item = $this->model->find($itemOrderId);

        if (!$item) {
            throw new PageNotFoundException("Item with id $itemOrderId not found");
        }

        if ($item->attachment) {
            // Handle comma-separated multiple files - serve the first one
            $files = explode(',', $item->attachment);
            $firstFile = trim($files[0]);
            
            $path = WRITEPATH . "uploads/return_attachments/" . $firstFile;

            if (!is_file($path)) {
                throw new PageNotFoundException("File not found");
            }

            $finfo = new finfo(FILEINFO_MIME);
            $type = $finfo->file($path);

            header("Content-Type: $type");
            header("Content-Length: " . filesize($path));
            header("Content-Disposition: inline; filename=\"" . basename($firstFile) . "\"");

            readfile($path);
            exit;
        }

        throw new PageNotFoundException("No attachment found for this item");
    }

    /**
     * Delete attachment(s) for an item
     */
    public function delete($itemOrderId)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->back()->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $item = $this->model->find($itemOrderId);

        if (!$item) {
            throw new PageNotFoundException("Item with id $itemOrderId not found");
        }

        if ($item->attachment) {
            // Delete all files (comma-separated)
            $files = explode(',', $item->attachment);
            
            foreach ($files as $filename) {
                $filename = trim($filename);
                $path = WRITEPATH . "uploads/return_attachments/" . $filename;
                
                if (is_file($path)) {
                    unlink($path);
                }
            }

            // Clear attachment field in database
            $this->model->protect(false)
                        ->update($itemOrderId, ['attachment' => null]);

            return redirect()->back()
                             ->with('message', 'تم حذف المرفقات بنجاح');
        }

        return redirect()->back()
                         ->with('error', 'لا توجد مرفقات لحذفها');
    }

    /**
     * Download a specific attachment file
     */
    public function download($itemOrderId, $fileIndex = 0)
    {
        $item = $this->model->find($itemOrderId);

        if (!$item) {
            throw new PageNotFoundException("Item with id $itemOrderId not found");
        }

        if ($item->attachment) {
            $files = explode(',', $item->attachment);
            
            if (!isset($files[$fileIndex])) {
                throw new PageNotFoundException("File not found");
            }

            $filename = trim($files[$fileIndex]);
            $path = WRITEPATH . "uploads/return_attachments/" . $filename;

            if (!is_file($path)) {
                throw new PageNotFoundException("File not found on server");
            }

            return $this->response->download($path, null);
        }

        throw new PageNotFoundException("No attachment found for this item");
    }
}