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


public function upload()
{
    if (!session()->get('isLoggedIn')) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'يجب تسجيل الدخول أولاً'
        ]);
    }

    // Get both employee_id and user_id from session
    $loggedEmployeeId = session()->get('employee_id');
    $loggedUserId     = session()->get('user_id');

    // Prioritize employee_id, fallback to user_id
    $createdBy = $loggedEmployeeId ?? $loggedUserId;

    // Validate that at least one ID exists
    if (empty($createdBy)) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'لم يتم التعرف على المستخدم'
        ]);
    }

    $assetNums = $this->request->getPost('asset_nums');
    $comments  = $this->request->getPost('comments');

    if (empty($assetNums) || !is_array($assetNums)) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'لم يتم تحديد أي عناصر للترجيع'
        ]);
    }

    $usageStatusModel = new \App\Models\UsageStatusModel();
    $returnedStatus = $usageStatusModel->where('usage_status', 'رجيع')->first();

    if (!$returnedStatus) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'حالة "رجيع" غير موجودة في النظام'
        ]);
    }

    $uploadPath = WRITEPATH . 'uploads/return_attachments';
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }

    $this->model->transStart();

    $successCount = 0;
    $failedItems  = [];
    $allFiles = $this->request->getFiles();

    foreach ($assetNums as $assetNum) {
        $originalItem = $this->model->where('asset_num', $assetNum)->first();

        if (!$originalItem) {
            $failedItems[] = "الأصل رقم: $assetNum غير موجود";
            continue;
        }

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

                if ($file->getSizeByUnit("mb") > 5) {
                    $failedItems[] = "الملف كبير جداً للأصل: $assetNum - " . $file->getName();
                    continue;
                }

                $allowedMimes = [
                    "image/png", "image/jpeg", "image/jpg",
                    "application/pdf", "application/msword",
                    "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                ];

                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    $failedItems[] = "نوع ملف غير مسموح للأصل: $assetNum - " . $file->getName();
                    continue;
                }

                $newName = $assetNum . '_' . time() . '_' . $file->getRandomName();
                if ($file->move($uploadPath, $newName)) {
                    $uploadedFileNames[] = $newName;
                } else {
                    $failedItems[] = "فشل رفع الملف للأصل: $assetNum - " . $file->getName();
                }
            }
        }

        $attachmentPath = !empty($uploadedFileNames)
            ? implode(',', $uploadedFileNames)
            : ($originalItem->attachment ?? null);

        $updateData = [
            'usage_status_id' => $returnedStatus->id,
            'note'            => $comments[$assetNum] ?? 'تم الترجيع',
            'attachment'      => $attachmentPath,
            'updated_at'      => date('Y-m-d H:i:s'),
            'created_by'      => $createdBy 
        ];

        $updated = $this->model->protect(false)
                               ->update($originalItem->item_order_id, $updateData);

        if ($updated) {
            $successCount++;
        } else {
            $failedItems[] = "فشل تحديث الأصل رقم: $assetNum";
        }
    }

    $this->model->transComplete();

    if ($this->model->transStatus() === false) {
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