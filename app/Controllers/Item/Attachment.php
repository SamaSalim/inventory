<?php

namespace App\Controllers\Item;

use App\Controllers\BaseController;
use App\Models\ItemOrderModel;
use CodeIgniter\Exceptions\PageNotFoundException;

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
        $generateForm = $this->request->getPost('generate_form');
        $itemData = $this->request->getPost('item_data');
        $actions = $this->request->getPost('actions');
        $reasons = $this->request->getPost('reasons');

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

        // Collect all IT items that need form generation
        $itItems = [];
        foreach ($assetNums as $assetNum) {
            $originalItem = $this->model
                ->select('item_order.*, minor_category.name as minor_category_name')
                ->join('items', 'items.id = item_order.item_id')
                ->join('minor_category', 'minor_category.id = items.minor_category_id')
                ->where('item_order.asset_num', $assetNum)
                ->first();

            if (!$originalItem) {
                continue;
            }

            $isIT = ($originalItem->minor_category_name === 'IT');
            if ($isIT && isset($generateForm[$assetNum]) && $generateForm[$assetNum] == '1') {
                $itItems[] = [
                    'assetNum' => $assetNum,
                    'name' => $itemData[$assetNum]['name'] ?? '',
                    'category' => $itemData[$assetNum]['category'] ?? '',
                    'assetType' => $itemData[$assetNum]['asset_type'] ?? '',
                    'notes' => $comments[$assetNum] ?? 'تم الترجيع',
                    'actions' => $actions[$assetNum] ?? [],
                    'reasons' => $reasons ?? []
                ];
            }
        }

        foreach ($assetNums as $assetNum) {
            $originalItem = $this->model
                ->select('item_order.*, minor_category.name as minor_category_name')
                ->join('items', 'items.id = item_order.item_id')
                ->join('minor_category', 'minor_category.id = items.minor_category_id')
                ->where('item_order.asset_num', $assetNum)
                ->first();

            if (!$originalItem) {
                $failedItems[] = "الأصل رقم: $assetNum غير موجود";
                continue;
            }

            $isIT = ($originalItem->minor_category_name === 'IT');
            $uploadedFileNames = [];

            // Generate form for IT items with all items data
            if ($isIT && isset($generateForm[$assetNum]) && $generateForm[$assetNum] == '1') {
                try {
                    $formPath = $this->generateReturnForm(
                        $assetNum,
                        $itemData[$assetNum] ?? [],
                        $actions[$assetNum] ?? [],
                        $comments[$assetNum] ?? 'تم الترجيع',
                        $itItems,
                        $reasons ?? []
                    );
                    
                    if ($formPath) {
                        $uploadedFileNames[] = basename($formPath);
                    }
                } catch (\Exception $e) {
                    $failedItems[] = "فشل إنشاء النموذج للأصل: $assetNum - " . $e->getMessage();
                    continue;
                }
            }

            // Handle uploaded files
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

                    if ($isIT) {
                        $allowedMimes = [
                            "image/png", "image/jpeg", "image/jpg", "image/gif",
                            "application/pdf", "application/msword",
                            "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                        ];
                    } else {
                        $allowedMimes = [
                            "image/png", "image/jpeg", "image/jpg", "image/gif"
                        ];
                    }

                    if (!in_array($file->getMimeType(), $allowedMimes)) {
                        $fileType = $isIT ? "غير مسموح" : "يجب أن يكون صورة";
                        $failedItems[] = "نوع ملف $fileType للأصل: $assetNum - " . $file->getName();
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

    public function generateReturnForm($assetNum, $itemData, $actions, $notes, $allItems = [], $reasons = [])
    {
        $uploadPath = WRITEPATH . 'uploads/return_attachments';
        
        $html = $this->buildFormHTML($assetNum, $itemData, $actions, $notes, $allItems, $reasons);
        
        $filename = 'form_' . $assetNum . '_' . time() . '.html';
        $fullPath = $uploadPath . '/' . $filename;
        
        if (file_put_contents($fullPath, $html) === false) {
            throw new \RuntimeException("Failed to create form file");
        }
        
        return $fullPath;
    }

    private function buildFormHTML($assetNum, $itemData, $actions, $notes, $allItems = [], $reasons = [])
    {
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');
        $baseUrl = base_url();
        $logoUrl = $baseUrl . 'public/assets/images/Kamc Logo Guideline-04.png';
        $cssUrl = $baseUrl . 'public/assets/css/components/print_form_style.css';
        
        // Build table rows dynamically
        $tableRows = '';
        
        if (!empty($allItems)) {
            // Multiple items - generate row for each with proper notes mapping
            foreach ($allItems as $index => $item) {
                $rowNum = $index + 1;
                $itemActions = $item['actions'] ?? [];
                
                $itemFixChecked = isset($itemActions['fix']) && $itemActions['fix'] == '1' ? '✓' : '';
                $itemSellChecked = isset($itemActions['sell']) && $itemActions['sell'] == '1' ? '✓' : '';
                $itemDestroyChecked = isset($itemActions['destroy']) && $itemActions['destroy'] == '1' ? '✓' : '';
                
                // Get notes for THIS specific item by its assetNum - this is the key fix!
                $itemNotes = 'تم الترجيع';
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
                    <td class=\"check-mark\">{$itemFixChecked}</td>
                    <td class=\"check-mark\">{$itemSellChecked}</td>
                    <td class=\"check-mark\">{$itemDestroyChecked}</td>
                    <td class=\"notes-cell\">{$itemNotes}</td>
                </tr>";
            }
        } else {
            // Single item fallback
            $fixChecked = isset($actions['fix']) && $actions['fix'] == '1' ? '✓' : '';
            $sellChecked = isset($actions['sell']) && $actions['sell'] == '1' ? '✓' : '';
            $destroyChecked = isset($actions['destroy']) && $actions['destroy'] == '1' ? '✓' : '';
            
            $displayNotes = !empty($notes) ? htmlspecialchars($notes, ENT_QUOTES, 'UTF-8') : 'تم الترجيع';
            
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
                    <td class=\"check-mark\">{$fixChecked}</td>
                    <td class=\"check-mark\">{$sellChecked}</td>
                    <td class=\"check-mark\">{$destroyChecked}</td>
                    <td class=\"notes-cell\">{$displayNotes}</td>
                </tr>";
        }
        
        // Build reasons table
        $reasonsPurposeEnd = (isset($reasons['purpose_end']) && $reasons['purpose_end'] == '1') ? '✓' : '';
        $reasonsExcess = (isset($reasons['excess']) && $reasons['excess'] == '1') ? '✓' : '';
        $reasonsUnfit = (isset($reasons['unfit']) && $reasons['unfit'] == '1') ? '✓' : '';
        $reasonsDamaged = (isset($reasons['damaged']) && $reasons['damaged'] == '1') ? '✓' : '';
        
        $reasonsTable = "
        <div style=\"display: flex; justify-content: center; margin: 15px 0;\">
            <table style=\"width: 50%; border-collapse: collapse; text-align: center;\">
                <thead>
                    <tr style=\"background: #2c3e50; color: white; border: 1px solid #2c3e50;\">
                        <th style=\"padding: 8px; border: 1px solid #2c3e50; font-weight: bold; font-size: 12px;\">انتهاء الغرض</th>
                        <th style=\"padding: 8px; border: 1px solid #2c3e50; font-weight: bold; font-size: 12px;\">فائض</th>
                        <th style=\"padding: 8px; border: 1px solid #2c3e50; font-weight: bold; font-size: 12px;\">عدم الصلاحية</th>
                        <th style=\"padding: 8px; border: 1px solid #2c3e50; font-weight: bold; font-size: 12px;\">تالف</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style=\"border: 1px solid #2c3e50;\">
                        <td style=\"padding: 15px; border: 1px solid #2c3e50; font-size: 18px; font-weight: bold;\">{$reasonsPurposeEnd}</td>
                        <td style=\"padding: 15px; border: 1px solid #2c3e50; font-size: 18px; font-weight: bold;\">{$reasonsExcess}</td>
                        <td style=\"padding: 15px; border: 1px solid #2c3e50; font-size: 18px; font-weight: bold;\">{$reasonsUnfit}</td>
                        <td style=\"padding: 15px; border: 1px solid #2c3e50; font-size: 18px; font-weight: bold;\">{$reasonsDamaged}</td>
                    </tr>
                </tbody>
            </table>
        </div>";
        
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
                            <div class="field-line"></div>
                        </div>
                        <div class="header-field">
                            <span class="field-label">التاريخ:</span>
                            <div class="field-line"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="text-align: center;">
                <h3 style="color: #040f49ff; margin-top: -10px; font-size: 20px;">اسباب الارجاع</h3>
                {$reasonsTable}
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
                        <th class="col-unit-price">للإصلاح</th>
                        <th class="col-unit-price">للبيع</th>
                        <th class="col-unit-price">للإتلاف</th>
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
                                الاسم: <span class="signature-line"></span><br>
                                التاريخ: <span class="signature-line"></span>
                            </div>
                        </td>
                        <td>
                            <div class="signature-title-cell">المستلم / أمين / مأمور المستودع</div>
                            <div class="signature-fields">
                                الاسم: <span class="signature-line"></span><br>
                                التاريخ: <span class="signature-line"></span>
                            </div>
                        </td>
                        <td>
                            <div class="signature-title-cell">مدير إدارة المستودعات</div>
                            <div class="signature-fields">
                                الاسم: <span class="signature-line"></span><br>
                                التاريخ: <span class="signature-line"></span>
                            </div>
                        </td>
                        <td>
                            <div class="signature-title-cell">لجنة فحص الرجيع</div>
                            <div class="signature-fields">
                                الاسم: <span class="signature-line"></span><br>
                                التاريخ: <span class="signature-line"></span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="header-fields">
                <div class="header-field">
                    <span class="field-label">صاحب الصلاحية:</span>
                    <div class="field-line"></div>
                </div>
                <div class="header-field">
                    <span class="field-label">التوقيع:</span>
                    <div class="field-line"></div>
                </div>
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
        $actions = $this->request->getPost('actions');
        $reasons = $this->request->getPost('reasons');
        $allItems = $this->request->getPost('all_items');
        
        if (empty($assetNum) || empty($itemData)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' => 'بيانات غير كاملة'
                ]);
        }
        
        try {
            $html = $this->buildFormHTML($assetNum, $itemData, $actions, '', $allItems ?? [], $reasons ?? []);
            
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