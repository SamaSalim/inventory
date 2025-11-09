<?php

namespace App\Controllers\Return\IT;

use App\Controllers\BaseController;
use App\Models\ItemOrderModel;
use App\Models\UsageStatusModel;
use App\Models\EmployeeModel;
use App\Models\UserModel;
use App\Models\OrderModel;
use App\Models\HistoryModel;
use App\Models\EvaluationModel;

class ReturnRequests extends BaseController
{
    protected $itemOrderModel;
    protected $usageStatusModel;
    protected $employeeModel;
    protected $userModel;
    protected $orderModel;

    public function __construct()
    {
        $this->itemOrderModel = new ItemOrderModel();
        $this->usageStatusModel = new UsageStatusModel();
        $this->employeeModel = new EmployeeModel();
        $this->userModel = new UserModel();
        $this->orderModel = new OrderModel();
    }

    public function index(): string
    {
        $filters = [
            'general_search' => $this->request->getGet('general_search'),
            'order_id' => $this->request->getGet('order_id'),
            'emp_id' => $this->request->getGet('emp_id'),
            'employee_name' => $this->request->getGet('employee_name'),
            'item_name' => $this->request->getGet('item_name'),
            'asset_num' => $this->request->getGet('asset_num'),
            'serial_num' => $this->request->getGet('serial_num'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $builder = $this->itemOrderModel
            ->select('item_order.item_order_id,
                      item_order.order_id,
                      item_order.created_by,
                      item_order.created_at,
                      item_order.usage_status_id,
                      item_order.asset_num,
                      item_order.serial_num,
                      item_order.brand,
                      item_order.model_num,
                      item_order.note,
                      item_order.attachment,
                      COALESCE(employee.name, users.name) AS employee_name,
                      COALESCE(employee.emp_id, users.user_id) AS emp_id_display,
                      COALESCE(employee.emp_dept, users.user_dept) AS department,
                      usage_status.usage_status,
                      items.name AS item_name,
                      minor_category.name AS minor_category_name')
            ->join('employee', 'employee.emp_id = item_order.created_by', 'left')
            ->join('users', 'users.user_id = item_order.created_by', 'left')
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id', 'left')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->join('minor_category', 'minor_category.id = items.minor_category_id', 'left')
            ->where('item_order.usage_status_id', 7);

        if (!empty($filters['general_search'])) {
            $searchTerm = $filters['general_search'];
            $builder->groupStart()
                ->like('item_order.item_order_id', $searchTerm)
                ->orLike('item_order.created_by', $searchTerm)
                ->orLike('employee.name', $searchTerm)
                ->orLike('users.name', $searchTerm)
                ->orLike('items.name', $searchTerm)
                ->orLike('item_order.asset_num', $searchTerm)
                ->orLike('item_order.serial_num', $searchTerm)
                ->groupEnd();
        }

        if (!empty($filters['order_id'])) {
            $builder->like('item_order.item_order_id', $filters['order_id']);
        }

        if (!empty($filters['emp_id'])) {
            $builder->groupStart()
                ->like('employee.emp_id', $filters['emp_id'])
                ->orLike('users.user_id', $filters['emp_id'])
                ->orLike('item_order.created_by', $filters['emp_id'])
                ->groupEnd();
        }

        if (!empty($filters['employee_name'])) {
            $builder->groupStart()
                ->like('employee.name', $filters['employee_name'])
                ->orLike('users.name', $filters['employee_name'])
                ->groupEnd();
        }

        if (!empty($filters['item_name'])) {
            $builder->like('items.name', $filters['item_name']);
        }

        if (!empty($filters['asset_num'])) {
            $builder->like('item_order.asset_num', $filters['asset_num']);
        }

        if (!empty($filters['serial_num'])) {
            $builder->like('item_order.serial_num', $filters['serial_num']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('DATE(item_order.created_at) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('DATE(item_order.created_at) <=', $filters['date_to']);
        }

        $returnOrders = $builder->orderBy('item_order.created_at', 'DESC')->asArray()->findAll();
        $usageStatuses = $this->usageStatusModel->asArray()->findAll();

        return view("warehouse/IT/IT_view", [
            'returnOrders' => $returnOrders,
            'usageStatuses' => $usageStatuses,
            'filters' => $filters
        ]);
    }

    public function serveAttachment($assetNum)
    {
        log_message('info', "=== IT Serving Attachment for Asset: {$assetNum} ===");
        
        // IT specialist should only see item_order attachments (user uploads)
        $item = $this->itemOrderModel
            ->select('attachment, item_order_id, asset_num')
            ->where('asset_num', $assetNum)
            ->asArray()  // THIS IS THE FIX!
            ->first();
        
        log_message('info', "Item found: " . json_encode($item));
        
        $attachment = null;
        $source = 'item_order';
        
        if ($item && !empty($item['attachment'])) {
            $attachment = $item['attachment'];
            log_message('info', "Attachment found in item_order table: {$attachment}");
        } else {
            log_message('error', "Item attachment is empty or null for asset: {$assetNum}");
        }

        if (!$attachment || $attachment === 'NULL' || trim($attachment) === '' || $attachment === '') {
            log_message('error', "No valid attachment found for asset: {$assetNum}. Attachment value: " . var_export($attachment, true));
            
            // Return a more helpful error message
            return $this->response->setStatusCode(404)->setBody(
                '❌ لا يوجد مرفق لهذا الأصل<br>' .
                '<small style="color: #666;">رقم الأصل: ' . $assetNum . '</small><br>' .
                '<small style="color: #666;">الحقل فارغ في قاعدة البيانات - لم يتم رفع أي ملف من قبل المستخدم</small>'
            );
        }

        // Handle comma-separated filenames
        $filenames = array_map('trim', explode(',', $attachment));
        $latestFile = end($filenames);
        
        log_message('info', "Latest file to serve: {$latestFile}");
        
        // Check possible paths - for IT view, check return_attachments and attachments only
        $possiblePaths = [
            WRITEPATH . 'uploads/return_attachments/' . $latestFile,
            WRITEPATH . 'uploads/attachments/' . $latestFile,
            WRITEPATH . 'uploads/' . $latestFile
        ];
        
        $filePath = null;
        foreach ($possiblePaths as $path) {
            if (is_file($path)) {
                $filePath = $path;
                log_message('info', "✓ File found at: {$path}");
                break;
            }
        }

        if (!$filePath) {
            log_message('error', "File not found. Searched for: {$latestFile}");
            return $this->response->setStatusCode(404)->setBody('❌ الملف غير موجود على السيرفر');
        }

        $extension = strtolower(pathinfo($latestFile, PATHINFO_EXTENSION));

        if ($extension === 'html' || $extension === 'htm') {
            return $this->response
                ->setHeader('Content-Type', 'text/html; charset=UTF-8')
                ->setBody(file_get_contents($filePath));
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($latestFile) . '"')
            ->setBody(file_get_contents($filePath));
    }

    // Serve evaluation report by evaluation ID
    public function serveEvaluationReport($evaluationId)
    {
        $evaluationModel = new EvaluationModel();
        $evaluation = $evaluationModel->find($evaluationId);

        if (!$evaluation || empty($evaluation['attachment'])) {
            return $this->response->setStatusCode(404)->setBody('❌ لا يوجد تقرير لهذا التقييم');
        }

        $filePath = WRITEPATH . 'uploads/evaluation_attachments/' . $evaluation['attachment'];

        if (!is_file($filePath)) {
            return $this->response->setStatusCode(404)->setBody('❌ الملف غير موجود');
        }

        return $this->response
            ->setHeader('Content-Type', 'text/html; charset=UTF-8')
            ->setBody(file_get_contents($filePath));
    }

    // For Super Warehouse - serve evaluation attachment by asset number
    public function serveEvaluationAttachment($assetNum)
    {
        log_message('info', "=== Super Warehouse Serving Evaluation Attachment for Asset: {$assetNum} ===");
        
        // Get the latest evaluation attachment for this asset
        $db = \Config\Database::connect();
        $builder = $db->table('evaluation');
        $builder->select('evaluation.attachment, evaluation.created_at')
                ->join('item_order', 'item_order.item_order_id = evaluation.item_order_id')
                ->where('item_order.asset_num', $assetNum)
                ->where('evaluation.attachment IS NOT NULL')
                ->where('evaluation.attachment !=', '')
                ->orderBy('evaluation.created_at', 'DESC')
                ->limit(1);
        
        $evaluationResult = $builder->get()->getRow();
        
        if (!$evaluationResult || empty($evaluationResult->attachment)) {
            log_message('error', "No evaluation attachment found for asset: {$assetNum}");
            return $this->response->setStatusCode(404)->setBody('❌ لا يوجد تقرير تقييم لهذا الأصل');
        }

        $latestFile = trim($evaluationResult->attachment);
        log_message('info', "Evaluation file to serve: {$latestFile}");
        
        $filePath = WRITEPATH . 'uploads/evaluation_attachments/' . $latestFile;
        
        if (!is_file($filePath)) {
            log_message('error', "Evaluation file not found: {$filePath}");
            return $this->response->setStatusCode(404)->setBody('❌ الملف غير موجود على السيرفر');
        }

        $extension = strtolower(pathinfo($latestFile, PATHINFO_EXTENSION));

        if ($extension === 'html' || $extension === 'htm') {
            return $this->response
                ->setHeader('Content-Type', 'text/html; charset=UTF-8')
                ->setBody(file_get_contents($filePath));
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($latestFile) . '"')
            ->setBody(file_get_contents($filePath));
    }

    public function generateTechnicalReport()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'طلب غير صالح']);
        }

        $assetNum = $this->request->getPost('asset_num');
        $citySerialNum = $this->request->getPost('city_serial_num');
        $ministrySerialNum = $this->request->getPost('ministry_serial_num');
        $notes = $this->request->getPost('notes');

        // Validate mandatory notes field
        if (empty(trim($notes))) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'يجب ملء حقل وصف الحالة - هذا الحقل إلزامي'
            ]);
        }

        // Get item order details - use asObject() to get object
        $itemOrder = $this->itemOrderModel
            ->select('item_order.*, items.name as item_name, 
                      COALESCE(employee.name, users.name) AS employee_name,
                      COALESCE(employee.emp_dept, users.user_dept) AS department')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->join('employee', 'employee.emp_id = item_order.created_by', 'left')
            ->join('users', 'users.user_id = item_order.created_by', 'left')
            ->where('item_order.asset_num', $assetNum)
            ->asObject()
            ->first();

        if (!$itemOrder) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'لم يتم العثور على الأصل'
            ]);
        }

        // Get current user info
        $handledBy = $this->getCurrentUserId();
        if (!$handledBy) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'لم يتم العثور على معلومات المستخدم الحالي'
            ]);
        }

        // Get handler name - use asObject()
        $handler = $this->employeeModel->where('emp_id', $handledBy)->asObject()->first();
        if (!$handler) {
            $handler = $this->userModel->where('user_id', $handledBy)->asObject()->first();
        }
        
        $handlerName = $handler ? $handler->name : 'غير محدد';

        // Generate HTML report
        $htmlContent = $this->generateHTMLForm([
            'asset_num' => $assetNum,
            'serial_num' => $itemOrder->serial_num,
            'item_name' => $itemOrder->item_name,
            'employee_name' => $itemOrder->employee_name,
            'department' => $itemOrder->department,
            'city_serial_num' => $citySerialNum,
            'ministry_serial_num' => $ministrySerialNum,
            'notes' => $notes,
            'created_by' => $itemOrder->created_by,
            'handled_by' => $handledBy,
            'handler_name' => $handlerName,
            'date' => date('Y-m-d')
        ]);

        // Save to evaluation_attachments folder
        $uploadPath = WRITEPATH . 'uploads/evaluation_attachments/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
            log_message('info', "Created directory: {$uploadPath}");
        }

        $fileName = 'technical_report_' . $assetNum . '_' . time() . '.html';
        $filePath = $uploadPath . $fileName;
        
        if (!file_put_contents($filePath, $htmlContent)) {
            log_message('error', "Failed to save file: {$filePath}");
            return $this->response->setJSON([
                'success' => false,
                'message' => 'فشل حفظ التقرير'
            ]);
        }

        log_message('info', "Technical report saved successfully: {$filePath}");

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insert into evaluation table
            $evaluationModel = new EvaluationModel();
            $evaluationData = [
                'item_order_id' => $itemOrder->item_order_id,
                'handled_by' => $handledBy,
                'notes' => $notes,
                'attachment' => $fileName,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $evaluationId = $evaluationModel->insert($evaluationData);
            log_message('info', "Evaluation record created with ID: {$evaluationId}");

            // Update item_order usage_status_id to 2 (رجيع)
            $this->itemOrderModel->update($itemOrder->item_order_id, [
                'usage_status_id' => 2,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            log_message('info', "Item order {$itemOrder->item_order_id} updated to status 2");

            // Add to history
            $historyModel = new HistoryModel();
            $historyModel->insert([
                'item_order_id' => $itemOrder->item_order_id,
                'usage_status_id' => 2,
                'handled_by' => $handledBy,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            log_message('info', "History record created for item_order_id: {$itemOrder->item_order_id}");

            $db->transComplete();

            if ($db->transStatus() === false) {
                log_message('error', "Transaction failed");
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'فشل حفظ البيانات'
                ]);
            }

            $reportUrl = base_url('return/it/returnrequests/serveEvaluationReport/' . $evaluationId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'تم إنشاء التقرير الفني بنجاح وتحديث حالة الأصل إلى "رجيع"',
                'evaluation_id' => $evaluationId,
                'report_url' => $reportUrl
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Technical Report Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ]);
        }
    }

    private function generateHTMLForm($data)
    {
        $baseUrl = base_url();
        $logoUrl = $baseUrl . 'public/assets/images/Kamc Logo Guideline-04.png';
        $cssUrl = $baseUrl . 'public/assets/css/components/print_form_style.css';
        
        $currentDate = date('Y-m-d');
        
        // Sanitize data to prevent XSS
        $assetNum = htmlspecialchars($data['asset_num'], ENT_QUOTES, 'UTF-8');
        $employeeName = htmlspecialchars($data['employee_name'], ENT_QUOTES, 'UTF-8');
        $createdBy = htmlspecialchars($data['created_by'], ENT_QUOTES, 'UTF-8');
        $department = htmlspecialchars($data['department'], ENT_QUOTES, 'UTF-8');
        $itemName = htmlspecialchars($data['item_name'], ENT_QUOTES, 'UTF-8');
        $serialNum = htmlspecialchars($data['serial_num'], ENT_QUOTES, 'UTF-8');
        $citySerialNum = htmlspecialchars($data['city_serial_num'] ?? 'غير محدد', ENT_QUOTES, 'UTF-8');
        $ministrySerialNum = htmlspecialchars($data['ministry_serial_num'] ?? 'غير محدد', ENT_QUOTES, 'UTF-8');
        $reportDate = htmlspecialchars($data['date'], ENT_QUOTES, 'UTF-8');
        $notes = nl2br(htmlspecialchars($data['notes'], ENT_QUOTES, 'UTF-8'));
        $handlerName = htmlspecialchars($data['handler_name'], ENT_QUOTES, 'UTF-8');
        $handledBy = htmlspecialchars($data['handled_by'], ENT_QUOTES, 'UTF-8');
        
        $html = <<<HTML
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير فني - {$assetNum}</title>
    <link rel="stylesheet" href="{$cssUrl}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @media screen {
            body { 
                font-family: 'Arial', sans-serif; 
                direction: rtl; 
                padding: 20px;
                background: #f5f5f5;
                font-size: 11px;
                line-height: 1.4;
                color: #2c3e50;
            }
            .print-container { 
                max-width: 210mm; 
                margin: 0 auto; 
                background: white;
                padding: 20px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                border: 2px solid #2c3e50;
            }
        }
        
        @media print {
            body { 
                margin: 0; 
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .print-container { 
                box-shadow: none;
                border: 2px solid #2c3e50;
                padding: 15px;
            }
            @page {
                margin: 15mm;
                size: A4;
            }
        }
        
        .print-header {
            border-bottom: 2px solid #34495e;
            margin-bottom: 10px;
            padding-bottom: 10px;
        }
        
        .kamc-emblem {
            text-align: center;
            margin-bottom: -40px;
        }
        
        .kamc-emblem img {
            max-width: 200px !important;
            height: auto;
            margin-bottom: -20px;
        }
        
        .form-title {
            text-align: center;
            font-size: 18px;
            font-weight: 700;
            padding: 10px;
            background: white;
            color: #2c3e50;
            border-bottom: 1px solid #bdc3c7;
            margin-bottom: 10px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            border: 2px solid #34495e;
            font-size: 11px;
        }
        
        .info-table tr {
            border-bottom: 1px solid #bdc3c7;
        }
        
        .info-table tr:last-child {
            border-bottom: none;
        }
        
        .label-cell {
            background: #34495e;
            color: white;
            padding: 8px 12px;
            font-weight: 600;
            text-align: center;
            width: 30%;
            border-left: 1px solid #bdc3c7;
            font-size: 11px;
        }
        
        .value-cell {
            padding: 8px 12px;
            text-align: right;
            color: #2c3e50;
            background: white;
            font-size: 11px;
        }
        
        .status-section {
            margin: 10px 0;
            border: 2px solid #34495e;
        }
        
        .status-title {
            background: #34495e;
            color: white;
            padding: 8px 12px;
            font-weight: 600;
            font-size: 12px;
            text-align: center;
        }
        
        .status-content {
            padding: 12px;
            min-height: 80px;
            line-height: 1.5;
            color: #2c3e50;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-size: 10px;
        }
        
        .technician-section {
            margin: 10px 0;
        }
        
        .technician-title {
            background: #34495e;
            color: white;
            padding: 8px 12px;
            font-weight: 600;
            font-size: 12px;
            border: 2px solid #34495e;
            border-bottom: none;
            text-align: center;
        }
        
        .form-footer {
            margin-top: 15px;
            text-align: center;
            font-size: 9px;
            padding: 10px;
            border: 2px solid #dc3545;
            background: #f8d7da;
            color: #721c24;
        }
        
        .footer-warning {
            font-weight: 700;
            margin-bottom: 3px;
            font-size: 10px;
        }
        
        .footer-note {
            font-size: 9px;
            line-height: 1.3;
        }
    </style>
</head>
<body>
    <div class="print-only">
        <div class="print-container">
            <!-- Header Section -->
            <div class="print-header">
                <div class="kamc-emblem">
                    <img src="{$logoUrl}" alt="KAMC Logo">
                </div>
                <div class="form-title">
                    <h1>تقرير فني عن حالة أصل</h1>
                </div>
            </div>

            <!-- Main Information Table -->
            <table class="info-table">
                <tr>
                    <td class="label-cell">صاحب الطلب</td>
                    <td class="value-cell">{$employeeName}</td>
                </tr>
                <tr>
                    <td class="label-cell">الرقم الوظيفي</td>
                    <td class="value-cell">{$createdBy}</td>
                </tr>
                <tr>
                    <td class="label-cell">الإدارة</td>
                    <td class="value-cell">{$department}</td>
                </tr>
                <tr>
                    <td class="label-cell">اسم الأصل</td>
                    <td class="value-cell">{$itemName}</td>
                </tr>
                <tr>
                    <td class="label-cell">الرقم التسلسلي للجهاز</td>
                    <td class="value-cell">{$serialNum}</td>
                </tr>
                <tr>
                    <td class="label-cell">الرقم التسلسلي للمدينة</td>
                    <td class="value-cell">{$citySerialNum}</td>
                </tr>
                <tr>
                    <td class="label-cell">الرقم التسلسلي للوزارة</td>
                    <td class="value-cell">{$ministrySerialNum}</td>
                </tr>
                <tr>
                    <td class="label-cell">التاريخ</td>
                    <td class="value-cell">{$reportDate}</td>
                </tr>
            </table>

            <!-- Status Section -->
            <div class="status-section">
                <div class="status-title">وصف الحالة</div>
                <div class="status-content">{$notes}</div>
            </div>

            <!-- Technician Section -->
            <div class="technician-section">
                <div class="technician-title">معلومات الفني المسؤول</div>
                <table class="info-table" style="margin-top: 0;">
                    <tr>
                        <td class="label-cell">اسم الفني</td>
                        <td class="value-cell">{$handlerName}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">الرقم الوظيفي</td>
                        <td class="value-cell">{$handledBy}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">الإدارة</td>
                        <td class="value-cell">إدارة الاتصالات - تقنية المعلومات</td>
                    </tr>
                    <tr>
                        <td class="label-cell">تاريخ الفحص</td>
                        <td class="value-cell">{$reportDate}</td>
                    </tr>
                </table>
            </div>

            <!-- Footer -->
            <div class="form-footer">
                <div class="footer-warning">
                    تنبيه مهم: يرجى مراجعة جميع البنود بدقة والتأكد من صحة البيانات
                </div>
                <div class="footer-note">
                    هذا التقرير رسمي ويجب الاحتفاظ به للمراجعة والتدقيق<br>
                    تم إنشاء هذا التقرير تلقائياً بواسطة نظام إدارة العهد
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;

        return $html;
    }

    private function getCurrentUserId()
    {
        $sessionData = session()->get();
        
        log_message('debug', '=== getCurrentUserId() DEBUG ===');
        log_message('debug', 'Full session: ' . json_encode($sessionData));
        
        $possibleKeys = ['emp_id', 'user_id', 'id', 'employee_id', 'userId', 'empId'];
        
        foreach ($possibleKeys as $key) {
            $value = session()->get($key);
            log_message('debug', "Checking key '{$key}': " . ($value ?? 'NULL'));
            
            if (!empty($value)) {
                log_message('info', "Found user ID in session key '{$key}': {$value}");
                return $value;
            }
        }
        
        log_message('error', 'No valid user ID found in any session key');
        log_message('debug', '===============================');
        
        return null;
    }
}