<?php

namespace App\Controllers\Return;

use App\Controllers\BaseController;
use App\Models\ItemOrderModel;
use App\Models\EmployeeModel;
use App\Models\UserModel;
use App\Models\HistoryModel;

use CodeIgniter\Exceptions\PageNotFoundException;

class Attachment extends BaseController
{
    private ItemOrderModel $model;

    public function __construct()
    {
        $this->model = new ItemOrderModel();
    }

    /**
     * Helper function to check if category is special (IT/Telecom/Equipment)
     */
    private function isSpecialCategory($minorCategoryName)
    {
        $specialCategories = ['IT', 'Telecom', 'Equipment'];
        return in_array($minorCategoryName, $specialCategories);
    }

    /**
     * Helper function to delete old attachment files from disk
     */
    private function deleteOldAttachmentFiles($attachmentString)
    {
        if (empty($attachmentString)) {
            return;
        }

        $uploadPath = WRITEPATH . 'uploads/return_attachments';
        $files = explode(',', $attachmentString);

        foreach ($files as $file) {
            $file = trim($file);
            if (empty($file)) {
                continue;
            }

            $filePath = $uploadPath . '/' . $file;
            if (is_file($filePath)) {
                try {
                    unlink($filePath);
                    log_message('info', "Deleted old attachment file: {$file}");
                } catch (\Exception $e) {
                    log_message('error', "Failed to delete file {$file}: " . $e->getMessage());
                }
            }
        }
    }

    public function upload()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً'
            ]);
        }

        $loggedEmployeeId = session()->get('employee_id');
        $loggedUserId     = session()->get('user_id');
        $createdBy = $loggedEmployeeId ?? $loggedUserId;

        if (empty($createdBy)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'لم يتم التعرف على المستخدم'
            ]);
        }

        $assetNums = $this->request->getPost('asset_nums');
        $comments  = $this->request->getPost('comments');
        $itemData = $this->request->getPost('item_data');
        $isSpecialCategoryFlags = $this->request->getPost('is_special_category');
        $reasons = $this->request->getPost('reasons');

        if (empty($assetNums) || !is_array($assetNums)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'لم يتم تحديد أي عناصر للترجيع'
            ]);
        }

        $usageStatusModel = new \App\Models\UsageStatusModel();
        
        // Status 7: Under Evaluation (for IT/Telecom/Equipment)
        $underEvaluationStatus = $usageStatusModel->where('id', 7)->first();
        
        // Status 2: Returned (for other items)
        $returnedStatus = $usageStatusModel->where('id', 2)->first();

        if (!$underEvaluationStatus || !$returnedStatus) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'حالات الاستخدام غير موجودة في النظام'
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

        // Collect all successfully returned item order IDs for ONE email
        $returnedItemOrderIds = [];
        $allItemsNotes = '';

        // Initialize HistoryModel for logging
        $historyModel = new \App\Models\HistoryModel();

        foreach ($assetNums as $assetNum) {
            $originalItem = $this->model
                ->select('item_order.*, items.name as item_name, minor_category.name as minor_category_name')
                ->join('items', 'items.id = item_order.item_id')
                ->join('minor_category', 'minor_category.id = items.minor_category_id')
                ->where('item_order.asset_num', $assetNum)
                ->first();

            if (!$originalItem) {
                $failedItems[] = "الأصل رقم: $assetNum غير موجود";
                continue;
            }

            $isSpecial = $this->isSpecialCategory($originalItem->minor_category_name);
            $uploadedFileNames = [];

            // Determine which status to use based on category
            $targetStatus = $isSpecial ? $underEvaluationStatus : $returnedStatus;

            // Check if new files were uploaded for this item
            $hasNewFiles = isset($allFiles['attachments'][$assetNum]) && 
                          count($allFiles['attachments'][$assetNum]) > 0;

            // Validate file upload for non-special categories (MANDATORY)
            if (!$isSpecial && !$hasNewFiles) {
                $failedItems[] = "يجب رفع صور للأصل رقم: $assetNum";
                continue;
            }

            // For special categories: if no new files uploaded, delete old attachments
            if ($isSpecial && !$hasNewFiles && !empty($originalItem->attachment)) {
                // Delete old attachment files from disk
                $this->deleteOldAttachmentFiles($originalItem->attachment);
                log_message('info', "Deleted old attachments for special category item: {$assetNum}");
            }

            // Handle uploaded files (images, PDFs, etc.)
            if ($hasNewFiles) {
                foreach ($allFiles['attachments'][$assetNum] as $file) {
                    if (!$file->isValid()) {
                        $error_code = $file->getError();
                        if ($error_code !== UPLOAD_ERR_NO_FILE) {
                            $failedItems[] = "خطأ في الملف للأصل: $assetNum - " . $file->getErrorString();
                        }
                        continue;
                    }

                    // Check file size (max 5MB)
                    if ($file->getSizeByUnit("mb") > 5) {
                        $failedItems[] = "الملف كبير جداً للأصل: $assetNum - " . $file->getName();
                        continue;
                    }

                    // For non-special categories, only accept images
                    if (!$isSpecial) {
                        $allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                        $mimeType = $file->getMimeType();
                        
                        if (!in_array($mimeType, $allowedImageTypes)) {
                            $failedItems[] = "يجب رفع صور فقط للأصل: $assetNum";
                            continue;
                        }
                    }
                    // For special categories, accept all file types
                    
                    $newName = $assetNum . '_' . time() . '_' . $file->getRandomName();
                    if ($file->move($uploadPath, $newName)) {
                        $uploadedFileNames[] = $newName;
                    } else {
                        $failedItems[] = "فشل رفع الملف للأصل: $assetNum - " . $file->getName();
                    }
                }

                // If new files were uploaded successfully, delete old attachments
                if (!empty($uploadedFileNames) && !empty($originalItem->attachment)) {
                    $this->deleteOldAttachmentFiles($originalItem->attachment);
                }
            }

            $itemComment = isset($comments[$assetNum]) ? trim($comments[$assetNum]) : '';
            
            // Get reasons for this item
            $itemReasons = isset($reasons[$assetNum]) ? $reasons[$assetNum] : [];
            
            // Generate HTML form for this item with all data (saved separately, NOT in database)
            try {
                $formData = [
                    'name' => $originalItem->item_name,
                    'category' => $originalItem->minor_category_name,
                    'asset_type' => $originalItem->assets_type ?? 'غير محدد'
                ];
                
                $itemForForm = [
                    'assetNum' => $assetNum,
                    'name' => $originalItem->item_name,
                    'category' => $originalItem->minor_category_name,
                    'assetType' => $originalItem->assets_type ?? 'غير محدد',
                    'notes' => $itemComment,
                    'reasons' => $itemReasons
                ];
                
                // Generate the form - saved to disk only, not added to database attachment field
                $formPath = $this->generateReturnForm(
                    $assetNum,
                    $formData,
                    $itemComment,
                    [$itemForForm],
                    $createdBy
                );
                
            } catch (\Exception $e) {
                log_message('error', "Failed to generate form for {$assetNum}: " . $e->getMessage());
            }

            // Determine attachment value to save in database
            // For special categories with no new files: set to NULL (delete old)
            // For items with new files: use new files
            // For non-special categories: must have files (already validated above)
            if (!empty($uploadedFileNames)) {
                $attachmentPath = implode(',', $uploadedFileNames);
            } elseif ($isSpecial && !$hasNewFiles) {
                // Special category with no new files: clear attachment
                $attachmentPath = null;
            } else {
                // Keep existing attachment (shouldn't happen due to validation, but safe fallback)
                $attachmentPath = $originalItem->attachment ?? null;
            }
            
            $updateData = [
                'usage_status_id' => $targetStatus->id,
                'note'            => $itemComment,
                'attachment'      => $attachmentPath,
                'updated_at'      => date('Y-m-d H:i:s'),
                'created_by'      => $createdBy 
            ];

            $updated = $this->model->protect(false)
                                   ->update($originalItem->item_order_id, $updateData);

            if ($updated) {
                $successCount++;
                $returnedItemOrderIds[] = $originalItem->item_order_id;
                
                if (!empty($itemComment)) {
                    $allItemsNotes .= "أصل {$assetNum}: {$itemComment}\n";
                }

                // Log to history table
                try {
                    $historyModel->insert([
                        'item_order_id'   => $originalItem->item_order_id,
                        'usage_status_id' => $targetStatus->id,
                        'handled_by'      => $createdBy
                    ]);
                } catch (\Exception $e) {
                    log_message('error', "Failed to log history for item_order_id {$originalItem->item_order_id}: " . $e->getMessage());
                }
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

        // Send ONE email with all returned items
        if (!empty($returnedItemOrderIds)) {
            try {
                $emailController = new \App\Controllers\Return\Email();
                $emailController->sendReturnNotification(
                    $returnedItemOrderIds,
                    trim($allItemsNotes),
                    null
                );
            } catch (\Exception $e) {
                log_message('error', "Failed to send consolidated email: " . $e->getMessage());
            }
        }

        $message = "تم تحديث $successCount عنصر بنجاح وإنشاء نماذج الإرجاع وإرسال إشعار بريد إلكتروني";
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

    public function generateReturnForm($assetNum, $itemData, $notes, $allItems = [], $createdBy = null)
    {
        $uploadPath = WRITEPATH . 'uploads/return_attachments';
        
        $html = $this->buildFormHTML($assetNum, $itemData, $notes, $allItems, $createdBy);
        
        $filename = 'form_' . $assetNum . '_' . time() . '.html';
        $fullPath = $uploadPath . '/' . $filename;
        
        if (file_put_contents($fullPath, $html) === false) {
            throw new \RuntimeException("Failed to create form file");
        }
        
        return $fullPath;
    }

    private function getCreatorName($createdBy = null)
    {
        // If no createdBy provided, get from session
        if (empty($createdBy)) {
            $createdBy = session()->get('employee_id') ?? session()->get('user_id');
        }
        
        if (empty($createdBy)) {
            return 'غير معروف';
        }

        // Check if it's an employee (starts with number like '1002')
        $employeeModel = new EmployeeModel();
        $employee = $employeeModel->where('emp_id', $createdBy)->first();
        
        if ($employee) {
            return $employee->name ?? $employee['name'];
        }

        // Otherwise check users table (like 'U101', 'U102')
        $userModel = new UserModel();
        $user = $userModel->where('user_id', $createdBy)->first();
        
        if ($user) {
            return $user->name ?? $user['name'];
        }

        return 'غير معروف';
    }

    private function buildFormHTML($assetNum, $itemData, $notes, $allItems = [], $createdBy = null)
    {
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');
        $baseUrl = base_url();
        $logoUrl = $baseUrl . 'public/assets/images/Kamc Logo Guideline-04.png';
        $cssUrl = $baseUrl . 'public/assets/css/components/print_form_style.css';
        
        // Get the name from session if createdBy not provided
        if (empty($createdBy)) {
            $createdBy = session()->get('employee_id') ?? session()->get('user_id');
        }
        
        // Get the name of person who created the return
        $employeeName = $this->getCreatorName($createdBy);
        
        // Build table rows dynamically
        $tableRows = '';
        
        if (!empty($allItems)) {
            foreach ($allItems as $index => $item) {
                $rowNum = $index + 1;
                $itemReasons = $item['reasons'] ?? [];
                
                // Check for checkmarks - handle both '1' string and boolean true
                $purposeEnd = (isset($itemReasons['purpose_end']) && ($itemReasons['purpose_end'] === '1' || $itemReasons['purpose_end'] === true)) ? '✓' : '';
                $excess = (isset($itemReasons['excess']) && ($itemReasons['excess'] === '1' || $itemReasons['excess'] === true)) ? '✓' : '';
                $unfit = (isset($itemReasons['unfit']) && ($itemReasons['unfit'] === '1' || $itemReasons['unfit'] === true)) ? '✓' : '';
                $damaged = (isset($itemReasons['damaged']) && ($itemReasons['damaged'] === '1' || $itemReasons['damaged'] === true)) ? '✓' : '';
                
                $itemNotes = '';
                if (isset($item['notes']) && !empty(trim($item['notes']))) {
                    $itemNotes = htmlspecialchars(trim($item['notes']), ENT_QUOTES, 'UTF-8');
                }
                
                $itemAssetNum = htmlspecialchars($item['assetNum'] ?? '', ENT_QUOTES, 'UTF-8');
                $itemName = htmlspecialchars($item['name'] ?? '', ENT_QUOTES, 'UTF-8');
                $itemCategory = htmlspecialchars($item['category'] ?? '', ENT_QUOTES, 'UTF-8');
                $itemAssetType = htmlspecialchars($item['assetType'] ?? '', ENT_QUOTES, 'UTF-8');
                
                $tableRows .= "
                <tr>
                    <td class=\"col-serial\">{$rowNum}</td>
                    <td>{$itemAssetNum}</td>
                    <td class=\"item-description-cell\">
                        <div class=\"item-main-name\">{$itemName}</div>
                        <div class=\"item-sub-details\">{$itemCategory}</div>
                    </td>
                    <td>{$itemAssetType}</td>
                    <td>قطعة</td>
                    <td>1</td>
                    <td class=\"check-mark\">{$purposeEnd}</td>
                    <td class=\"check-mark\">{$excess}</td>
                    <td class=\"check-mark\">{$unfit}</td>
                    <td class=\"check-mark\">{$damaged}</td>
                    <td class=\"notes-cell\">{$itemNotes}</td>
                </tr>";
            }
        } else {
            $displayNotes = !empty($notes) ? htmlspecialchars($notes, ENT_QUOTES, 'UTF-8') : '';
            
            $tableRows = "
                <tr>
                    <td class=\"col-serial\">1</td>
                    <td>{$assetNum}</td>
                    <td class=\"item-description-cell\">
                        <div class=\"item-main-name\">{$itemData['name']}</div>
                        <div class=\"item-sub-details\">{$itemData['category']}</div>
                    </td>
                    <td>{$itemData['asset_type']}</td>
                    <td>قطعة</td>
                    <td>1</td>
                    <td class=\"check-mark\"></td>
                    <td class=\"check-mark\"></td>
                    <td class=\"check-mark\"></td>
                    <td class=\"check-mark\"></td>
                    <td class=\"notes-cell\">{$displayNotes}</td>
                </tr>";
        }
        
        $html = <<<HTML
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نموذج ترجيع الأصل - {$assetNum}</title>
    <link rel="stylesheet" href="{$cssUrl}">
    <style>
        @media screen {
            body { 
                font-family: 'Arial', sans-serif; 
                direction: rtl; 
                padding: 20px;
                background: #f5f5f5;
            }
            .print-container { 
                max-width: 210mm; 
                margin: 0 auto; 
                background: white;
                padding: 20px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
        }
        @media print {
            body { margin: 0; padding: 0; }
            .print-container { box-shadow: none; }
        }
        .field-value {
            display: inline-block;
            min-width: 200px;
            border-bottom: 1px solid #333;
            padding: 0 10px;
            font-weight: bold;
        }
        .signature-value {
            display: inline-block;
            min-width: 150px;
            font-weight: bold;
            border-bottom: 1px solid #333;
            padding: 2px 5px;
        }
    </style>
</head>
<body>
    <div class="print-only">
        <div class="print-container">
            <!-- Header Section -->
            <div class="print-header">
                <div class="ministry-details">
                    <div class="logo-section">
                        <div class="kamc-emblem">
                            <img src="{$logoUrl}" 
                                alt="KAMC Logo"
                                style="max-width: 350px; height: auto;">
                        </div>
                        <div class="form-title">
                            <h1 style="font-size: 25px; margin-top:-5px">مستند الارجاع</h1>
                        </div>
                    </div>
                    
                    <div class="header-fields">
                        <div class="header-field">
                            <span class="field-label">الجهة المرجعة:</span>
                            <span class="field-value">ادارة العهد</span>
                        </div>
                        <div class="header-field">
                            <span class="field-label">التاريخ:</span>
                            <span class="field-value">{$currentDate}</span>
                        </div>
                    </div>
                </div>
            </div>

            <table class="main-table">
                <thead>
                    <tr>
                        <th class="col-serial">#</th>
                        <th class="col-description">رقم الصنف</th>
                        <th class="col-description">اسم الصنف وتصنيفه</th>
                        <th class="col-model">نوع الصنف</th>
                        <th class="col-quantity-available">الوحدة</th>
                        <th class="col-quantity-requested">الكمية</th>
                        <th class="col-unit-price">انتهاء الغرض</th>
                        <th class="col-unit-price">فائض</th>
                        <th class="col-unit-price">عدم الصلاحية</th>
                        <th class="col-unit-price">تالف</th>
                        <th class="col-notes">ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    {$tableRows}
                </tbody>
            </table>

            <div class="signature-section no-page-break">
                <table class="signature-table">
                    <tr>
                        <td>
                            <div class="signature-title-cell">المسئول في الجهة المرجعة</div>
                            <div class="signature-fields">
                                الاسم: <span class="signature-value">{$employeeName}</span><br>
                                التاريخ: <span class="signature-value">{$currentDate}</span>
                            </div>
                        </td>
                        <td>
                            <div class="signature-title-cell">المستلم / أمين / مأمور المستودع</div>
                            <div class="signature-fields">
                                الاسم: <span class="signature-line"></span><br>
                                التاريخ: <span class="signature-line"></span>
                            </div>
                        </td>

                    </tr>
                </table>
            </div>


            <div class="form-footer">
                <div class="footer-warning">
                    تنبيه مهم: يرجى مراجعة جميع البنود بدقة والتأكد من صحة البيانات قبل التوقيع والاستلام
                </div>
                <div class="footer-note">
                    هذا المستند رسمي ويجب الاحتفاظ به للمراجعة والتدقيق<br>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;

        return $html;
    }

    public function printForm()
    {
        $assetNum = $this->request->getPost('asset_num');
        $itemData = $this->request->getPost('item_data');
        $allItems = $this->request->getPost('all_items');
        $createdBy = $this->request->getPost('created_by');
        
        if (empty($assetNum) || empty($itemData)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' => 'بيانات غير كاملة'
                ]);
        }
        
        try {
            $html = $this->buildFormHTML($assetNum, $itemData, '', $allItems ?? [], $createdBy);
            
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'success' => true,
                    'html' => $html
                ]);
        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'فشل إنشاء النموذج: ' . $e->getMessage()
                ]);
        }
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
    
    public function viewForm($assetNum)
    {
        $uploadPath = WRITEPATH . 'uploads/return_attachments';
        $pattern = $uploadPath . '/form_' . $assetNum . '_*.html';
        $files = glob($pattern);
        
        if (empty($files)) {
            throw new PageNotFoundException("No form found for asset: $assetNum");
        }
        
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        $latestForm = $files[0];
        
        $html = file_get_contents($latestForm);
        return $this->response->setBody($html);
    }
}