<?php

namespace App\Controllers\Return;

use App\Controllers\BaseController;
use App\Models\{
    ItemOrderModel,
    ItemModel,
    MinorCategoryModel,
    MajorCategoryModel,
    UsageStatusModel,
    EmployeeModel,
    UserModel,
    PermissionModel,
    RoomModel,
    ReturnedItemsModel
};

class Email extends BaseController
{
    /**
     * Send email notification when items are returned
     * Routes to IT_specialist (status 7) or super_warehouse (status 2)
     * 
     * @param array $itemOrderIds Array of item order IDs that were returned
     * @param string $notes Return notes/reason
     * @param string|null $attachment Attachment filename(s)
     * @return bool Success status
     */
    public function sendReturnNotification($itemOrderIds, $notes = '', $attachment = null)
    {
        try {
            // Ensure $itemOrderIds is an array
            if (!is_array($itemOrderIds)) {
                $itemOrderIds = [$itemOrderIds];
            }

            if (empty($itemOrderIds)) {
                log_message('error', 'No item order IDs provided for email notification');
                return false;
            }

            $itemOrderModel = new ItemOrderModel();
            $itemModel = new ItemModel();
            $employeeModel = new EmployeeModel();
            
            // Get all item orders
            $itemOrders = $itemOrderModel->whereIn('item_order_id', $itemOrderIds)->findAll();
            
            if (empty($itemOrders)) {
                log_message('error', "No item orders found for IDs: " . implode(', ', $itemOrderIds));
                return false;
            }
            
            // Get employee who returned the items (from first item)
            $returnedBy = $employeeModel->where('emp_id', $itemOrders[0]->created_by)->first();
            
            // Separate items by status
            $itemsForIT = [];
            $itemsForWarehouse = [];
            
            foreach ($itemOrders as $itemOrder) {
                if ($itemOrder->usage_status_id == 7) {
                    $itemsForIT[] = $itemOrder;
                } elseif ($itemOrder->usage_status_id == 2) {
                    $itemsForWarehouse[] = $itemOrder;
                }
            }
            
            $emailsSent = 0;
            
            // Send to IT specialists if there are items with status 7
            if (!empty($itemsForIT)) {
                $emailsSent += $this->sendToITSpecialists($itemsForIT, $returnedBy);
            }
            
            // Send to super_warehouse if there are items with status 2
            if (!empty($itemsForWarehouse)) {
                $emailsSent += $this->sendToSuperWarehouse($itemsForWarehouse, $returnedBy);
            }
            
            log_message('info', "Return notification sent to {$emailsSent} recipients for " . count($itemOrders) . " items");
            
            return $emailsSent > 0;
            
        } catch (\Exception $e) {
            log_message('error', 'Return Email Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send notification to IT specialists for items with status 7
     * 
     * @param array $itemOrders Array of item orders
     * @param object $returnedBy Employee who returned the items
     * @return int Number of emails sent
     */
    private function sendToITSpecialists($itemOrders, $returnedBy)
    {
        $itemModel = new ItemModel();
        
        // Get IT specialist users (role_id = 7 assuming IT_specialist role)
        $recipients = $this->getITSpecialistUsers();
        
        if (empty($recipients)) {
            log_message('warning', 'No IT_specialist users found to send return notification');
            return 0;
        }
        
        // Prepare items details for email
        $itemsDetails = [];
        foreach ($itemOrders as $itemOrder) {
            $item = $itemModel->find($itemOrder->item_id);
            
            $itemsDetails[] = [
                'asset_num' => $itemOrder->asset_num,
                'serial_num' => $itemOrder->serial_num,
                'item_name' => $item->name ?? 'غير محدد',
                'brand' => $itemOrder->brand ?? 'N/A',
            ];
        }
        
        $returnData = [
            'items' => $itemsDetails,
            'returned_by' => $returnedBy->name ?? 'غير معروف',
            'order_id' => $itemOrders[0]->item_order_id,
            'recipient_type' => 'IT'
        ];
        
        // Send email to all IT specialists
        $emailsSent = 0;
        foreach ($recipients as $recipient) {
            if ($this->sendEmail($recipient, $returnData)) {
                $emailsSent++;
            }
        }
        
        $assetNumbers = array_column($itemsDetails, 'asset_num');
        log_message('info', "Return notification sent to {$emailsSent} IT specialists for items: " . implode(', ', $assetNumbers));
        
        return $emailsSent;
    }
    
    /**
     * Send notification to super warehouse for items with status 2
     * 
     * @param array $itemOrders Array of item orders
     * @param object $returnedBy Employee who returned the items
     * @return int Number of emails sent
     */
    private function sendToSuperWarehouse($itemOrders, $returnedBy)
    {
        $itemModel = new ItemModel();
        
        // Get super_warehouse users
        $recipients = $this->getSuperWarehouseUsers();
        
        if (empty($recipients)) {
            log_message('warning', 'No super_warehouse users found to send return notification');
            return 0;
        }
        
        // Prepare items details for email
        $itemsDetails = [];
        foreach ($itemOrders as $itemOrder) {
            $item = $itemModel->find($itemOrder->item_id);
            
            $itemsDetails[] = [
                'asset_num' => $itemOrder->asset_num,
                'serial_num' => $itemOrder->serial_num,
                'item_name' => $item->name ?? 'غير محدد',
                'brand' => $itemOrder->brand ?? 'N/A',
            ];
        }
        
        $returnData = [
            'items' => $itemsDetails,
            'returned_by' => $returnedBy->name ?? 'غير معروف',
            'order_id' => $itemOrders[0]->item_order_id,
            'recipient_type' => 'warehouse'
        ];
        
        // Send email to all super_warehouse users
        $emailsSent = 0;
        foreach ($recipients as $recipient) {
            if ($this->sendEmail($recipient, $returnData)) {
                $emailsSent++;
            }
        }
        
        $assetNumbers = array_column($itemsDetails, 'asset_num');
        log_message('info', "Return notification sent to {$emailsSent} super_warehouse users for items: " . implode(', ', $assetNumbers));
        
        return $emailsSent;
    }
    
    /**
     * Get all users with IT_specialist role
     * 
     * @return array Array of user objects with email addresses
     */
    private function getITSpecialistUsers()
    {
        $permissionModel = new PermissionModel();
        $employeeModel = new EmployeeModel();
        
        // Get all employees with IT_specialist role (adjust role name as needed)
        $permissions = $permissionModel
            ->select('permission.emp_id, employee.name, employee.email')
            ->join('employee', 'employee.emp_id = permission.emp_id')
            ->join('role', 'role.id = permission.role_id')
            ->where('role.name', 'IT_specialist') // Adjust role name if different
            ->where('employee.email IS NOT NULL')
            ->where('employee.email !=', '')
            ->findAll();
        
        return $permissions;
    }
    
    /**
     * Get all users with super_warehouse role
     * 
     * @return array Array of user objects with email addresses
     */
    private function getSuperWarehouseUsers()
    {
        $permissionModel = new PermissionModel();
        $employeeModel = new EmployeeModel();
        
        // Get all employees with super_warehouse role
        $permissions = $permissionModel
            ->select('permission.emp_id, employee.name, employee.email')
            ->join('employee', 'employee.emp_id = permission.emp_id')
            ->join('role', 'role.id = permission.role_id')
            ->where('role.name', 'super_warehouse')
            ->where('employee.email IS NOT NULL')
            ->where('employee.email !=', '')
            ->findAll();
        
        return $permissions;
    }
    
    /**
     * Send individual email
     * 
     * @param object $recipient User object with email
     * @param array $returnData Return data with items array and recipient_type
     * @return bool Success status
     */
    private function sendEmail($recipient, $returnData)
    {
        try {
            $email = \Config\Services::email();
            
            $email->setTo($recipient->email);
            
            // Different subject based on recipient type
            if ($returnData['recipient_type'] == 'IT') {
                $email->setSubject('إشعار إرجاع أصول - يتطلب تقرير IT - KAMC Inventory System');
            } else {
                $email->setSubject('إشعار إرجاع أصول - KAMC Inventory System');
            }
            
            $message = $this->buildEmailHTML($recipient, $returnData);
            $email->setMessage($message);
            
            if ($email->send()) {
                log_message('info', "Return email sent successfully to {$recipient->email}");
                return true;
            } else {
                log_message('error', 'Email sending failed to ' . $recipient->email . ': ' . $email->printDebugger(['headers']));
                return false;
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Email Error for ' . $recipient->email . ': ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Build HTML email content
     * 
     * @param object $recipient User receiving the email
     * @param array $returnData Return data with items array and recipient_type
     * @return string HTML content
     */
    private function buildEmailHTML($recipient, $returnData)
    {
        $baseUrl = base_url();
        
        // Different URLs and messages based on recipient type
        if ($returnData['recipient_type'] == 'IT') {
            $reviewUrl = $baseUrl . "ITSpecialist/dashboard"; // Adjust to your IT specialist route
            $headerText = 'إشعار إرجاع أصول';
            $actionText = 'يرجى مراجعة الأصول وإنشاء التقرير اللازم';
            $buttonText = 'مراجعة الأصول وإنشاء التقرير';
        } else {
            $reviewUrl = $baseUrl . "AssetsHistory";
            $headerText = 'إشعار إرجاع أصول';
            $actionText = 'يرجى مراجعة النظام لقبول أو رفض الطلب';
            $buttonText = 'مراجعة النظام لقبول أو رفض الطلب';
        }
        
        // Build items table rows
        $itemsList = '';
        foreach ($returnData['items'] as $item) {
            $itemsList .= "
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd;'>{$item['item_name']}</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>{$item['asset_num']}</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>{$item['serial_num']}</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>{$item['brand']}</td>
                </tr>
            ";
        }
        
        $html = "
            <html dir='rtl'>
            <head>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        direction: rtl; 
                        background-color: #f5f5f5;
                        margin: 0;
                        padding: 0;
                    }
                    .container { 
                        max-width: 600px; 
                        margin: 20px auto; 
                        background-color: white;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    }
                    .header { 
                        background-color:  #0896baff; 
                        color: white; 
                        padding: 30px 15px; 
                        text-align: center;
                        font-size: 28px;
                        font-weight: bold;
                    }
                    .content { 
                        padding: 30px 20px; 
                        background-color: #f9f9f9; 
                    }
                    table { 
                        width: 100%; 
                        border-collapse: collapse; 
                        margin: 15px 0; 
                        background-color: white;
                    }
                    th { 
                        background-color: #0896baff; 
                        color: white; 
                        padding: 12px 10px; 
                        text-align: right;
                        font-weight: bold;
                    }
                    td { 
                        padding: 10px; 
                        border: 1px solid #ddd; 
                    }
                    .info-row {
                        margin: 10px 0;
                        line-height: 1.6;
                    }
                    .action-notice {
                        background-color: #fff3cd;
                        border: 2px solid #ffc107;
                        border-radius: 8px;
                        padding: 15px;
                        margin: 20px 0;
                        text-align: center;
                        font-weight: bold;
                        color: #856404;
                    }
                    .btn-container {
                        text-align: center;
                        margin: 30px 0;
                    }
                    .btn {
                        display: inline-block;
                        background-color: #0896baff;
                        color: white !important;
                        padding: 15px 40px;
                        text-decoration: none;
                        border-radius: 5px;
                        font-weight: bold;
                        font-size: 16px;
                    }
                    .btn:hover {
                        background-color: #0896baff;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        {$headerText}
                    </div>
                    <div class='content'>
                        <p class='info-row'>عزيزي/عزيزتي <strong>{$recipient->name}</strong>,</p>
                        <p class='info-row'>تم إرجاع أصول جديدة من قبل: <strong>{$returnData['returned_by']}</strong></p>
                        <p class='info-row'><strong>رقم الطلب:</strong> {$returnData['order_id']}</p>
                        
                        <div class='action-notice'>
                            <i class='fas fa-exclamation-triangle'></i> {$actionText}
                        </div>
                        
                        <h3 style='color: #0896baff; margin-top: 25px;'>تفاصيل الأصول المرجعة:</h3>
                        <table>
                            <tr>
                                <th>اسم الصنف</th>
                                <th>رقم الأصل</th>
                                <th>الرقم التسلسلي</th>
                                <th>العلامة التجارية</th>
                            </tr>
                            {$itemsList}
                        </table>
                        
                        <div class='btn-container'>
                            <a href='{$reviewUrl}' class='btn'>{$buttonText}</a>
                        </div>
                        
                        <p style='text-align: center; margin-top: 30px; color: #666;'>
                            شكراً لك،<br>
                            <strong>KAMC - نظام إدارة العهد</strong>
                        </p>
                    </div>
                </div>
            </body>
            </html>
        ";

        return $html;
    }
    
    /**
     * Send status update email (accept/reject)
     * 
     * @param int $itemOrderId Item order ID
     * @param string $status 'accepted' or 'rejected'
     * @param string $note Optional note from reviewer
     * @return bool Success status
     */
    public function sendStatusUpdateNotification($itemOrderId, $status, $note = '')
    {
        try {
            $itemOrderModel = new ItemOrderModel();
            $employeeModel = new EmployeeModel();
            
            $itemOrder = $itemOrderModel->find($itemOrderId);
            
            if (!$itemOrder) {
                return false;
            }
            
            // Get the employee who originally returned the item
            $employee = $employeeModel->where('emp_id', $itemOrder->created_by)->first();
            
            if (!$employee || empty($employee->email)) {
                log_message('warning', "No email found for employee {$itemOrder->created_by}");
                return false;
            }
            
            $email = \Config\Services::email();
            $email->setTo($employee->email);
            
            $statusText = $status === 'accepted' ? 'قبول' : 'رفض';
            $email->setSubject("تحديث حالة الإرجاع: {$statusText} - KAMC Inventory System");
            
            $message = $this->buildStatusUpdateHTML($employee, $itemOrder, $status, $note);
            $email->setMessage($message);
            
            if ($email->send()) {
                log_message('info', "Status update email sent to {$employee->email} for item {$itemOrder->asset_num}");
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            log_message('error', 'Status Update Email Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Build status update email HTML
     */
    private function buildStatusUpdateHTML($employee, $itemOrder, $status, $note)
    {
        $statusText = $status === 'accepted' ? 'تم قبول' : 'تم رفض';
        $statusColor = $status === 'accepted' ? '#28a745' : '#dc3545';
        
        $noteSection = '';
        if (!empty($note)) {
            $noteSection = "
                <div style='background-color: #f8f9fa; padding: 15px; border-right: 4px solid {$statusColor}; margin: 15px 0;'>
                    <h4 style='color: {$statusColor}; margin-top: 0;'>ملاحظات المراجع:</h4>
                    <p style='margin: 10px 0; line-height: 1.6;'>{$note}</p>
                </div>
            ";
        }
        
        return "
            <html dir='rtl'>
            <head>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        direction: rtl; 
                        background-color: #f5f5f5;
                        margin: 0;
                        padding: 0;
                    }
                    .container { 
                        max-width: 600px; 
                        margin: 20px auto; 
                        background-color: white;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    }
                    .header { 
                        background-color: {$statusColor}; 
                        color: white; 
                        padding: 30px 15px; 
                        text-align: center;
                        font-size: 28px;
                        font-weight: bold;
                    }
                    .content { 
                        padding: 30px 20px; 
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        تحديث حالة الإرجاع
                    </div>
                    <div class='content'>
                        <p style='font-size: 16px; line-height: 1.8;'>
                            عزيزي/عزيزتي <strong>{$employee->name}</strong>،
                        </p>
                        <p style='font-size: 16px; line-height: 1.8;'>
                            <strong style='color: {$statusColor};'>{$statusText}</strong> طلب إرجاع الأصل رقم: <strong>{$itemOrder->asset_num}</strong>
                        </p>
                        
                        {$noteSection}
                        
                        <div style='text-align: center; margin: 30px 0; color: #666;'>
                            <p>شكراً لك،<br><strong>KAMC - نظام إدارة العهد</strong></p>
                        </div>
                    </div>
                </div>
            </body>
            </html>
        ";
    }
    
    /**
     * Send notification from IT to super_warehouse after report completion
     * This is called when IT specialist completes their work and sends to warehouse
     * 
     * @param array $itemOrderIds Array of item order IDs
     * @param string $itReport IT report/notes
     * @return bool Success status
     */
    public function sendITCompletionNotification($itemOrderIds, $itReport = '')
    {
        try {
            if (!is_array($itemOrderIds)) {
                $itemOrderIds = [$itemOrderIds];
            }

            if (empty($itemOrderIds)) {
                return false;
            }

            $itemOrderModel = new ItemOrderModel();
            $itemModel = new ItemModel();
            
            $itemOrders = $itemOrderModel->whereIn('item_order_id', $itemOrderIds)->findAll();
            
            if (empty($itemOrders)) {
                return false;
            }
            
            // Get super_warehouse users
            $recipients = $this->getSuperWarehouseUsers();
            
            if (empty($recipients)) {
                log_message('warning', 'No super_warehouse users found for IT completion notification');
                return false;
            }
            
            // Prepare items details
            $itemsDetails = [];
            foreach ($itemOrders as $itemOrder) {
                $item = $itemModel->find($itemOrder->item_id);
                
                $itemsDetails[] = [
                    'asset_num' => $itemOrder->asset_num,
                    'serial_num' => $itemOrder->serial_num,
                    'item_name' => $item->name ?? 'غير محدد',
                    'brand' => $itemOrder->brand ?? 'N/A',
                ];
            }
            
            $data = [
                'items' => $itemsDetails,
                'it_report' => $itReport,
                'order_id' => $itemOrders[0]->item_order_id
            ];
            
            // Send email to all super_warehouse users
            $emailsSent = 0;
            foreach ($recipients as $recipient) {
                if ($this->sendITCompletionEmail($recipient, $data)) {
                    $emailsSent++;
                }
            }
            
            log_message('info', "IT completion notification sent to {$emailsSent} super_warehouse users");
            
            return $emailsSent > 0;
            
        } catch (\Exception $e) {
            log_message('error', 'IT Completion Email Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send IT completion email to warehouse
     */
    private function sendITCompletionEmail($recipient, $data)
    {
        try {
            $email = \Config\Services::email();
            
            $email->setTo($recipient->email);
            $email->setSubject('إشعار: تم إكمال تقرير IT للأصول المرجعة - KAMC Inventory System');
            
            $baseUrl = base_url();
            $reviewUrl = $baseUrl . "AssetsHistory";
            
            $itemsList = '';
            foreach ($data['items'] as $item) {
                $itemsList .= "
                    <tr>
                        <td style='padding: 10px; border: 1px solid #ddd;'>{$item['item_name']}</td>
                        <td style='padding: 10px; border: 1px solid #ddd;'>{$item['asset_num']}</td>
                        <td style='padding: 10px; border: 1px solid #ddd;'>{$item['serial_num']}</td>
                        <td style='padding: 10px; border: 1px solid #ddd;'>{$item['brand']}</td>
                    </tr>
                ";
            }
            
            $reportSection = '';
            if (!empty($data['it_report'])) {
                $reportSection = "
                    <div style='background-color: #e7f3ff; padding: 15px; border-right: 4px solid #0896baff; margin: 20px 0;'>
                        <h4 style='color: #0896baff; margin-top: 0;'>  ملاحظة الدعم الفني </h4>
                        <p style='margin: 10px 0; line-height: 1.6; white-space: pre-wrap;'>{$data['it_report']}</p>
                    </div>
                ";
            }
            
            $message = "
                <html dir='rtl'>
                <head>
                    <style>
                        body { 
                            font-family: Arial, sans-serif; 
                            direction: rtl; 
                            background-color: #f5f5f5;
                            margin: 0;
                            padding: 0;
                        }
                        .container { 
                            max-width: 600px; 
                            margin: 20px auto; 
                            background-color: white;
                            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                        }
                        .header { 
                            background-color: #28a745; 
                            color: white; 
                            padding: 30px 15px; 
                            text-align: center;
                            font-size: 28px;
                            font-weight: bold;
                        }
                        .content { 
                            padding: 30px 20px; 
                            background-color: #f9f9f9; 
                        }
                        table { 
                            width: 100%; 
                            border-collapse: collapse; 
                            margin: 15px 0; 
                            background-color: white;
                        }
                        th { 
                            background-color: #0896baff; 
                            color: white; 
                            padding: 12px 10px; 
                            text-align: right;
                            font-weight: bold;
                        }
                        .btn {
                            display: inline-block;
                            background-color: #0896baff;
                            color: white !important;
                            padding: 15px 40px;
                            text-decoration: none;
                            border-radius: 5px;
                            font-weight: bold;
                            font-size: 16px;
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                               تم مراجعة الأصل من طرف الدعم الفني
                        </div>
                        <div class='content'>
                            <p style='margin: 10px 0; line-height: 1.6;'>عزيزي/عزيزتي <strong>{$recipient->name}</strong>,</p>
                            <p style='margin: 10px 0; line-height: 1.6;'>تم  اكمال تقرير الدعم الفني  للأصل المرجع</p>
                            
                            {$reportSection}
                            
                            <h3 style='color: #0896baff; margin-top: 25px;'>تفاصيل الأصول:</h3>
                            <table>
                                <tr>
                                    <th>اسم الصنف</th>
                                    <th>رقم الأصل</th>
                                    <th>الرقم التسلسلي</th>
                                    <th>العلامة التجارية</th>
                                </tr>
                                {$itemsList}
                            </table>
                            
                            <div style='text-align: center; margin: 30px 0;'>
                                <a href='{$reviewUrl}' class='btn'>مراجعة النظام لقبول أو رفض الطلب</a>
                            </div>
                            
                            <p style='text-align: center; margin-top: 30px; color: #666;'>
                                شكراً لك،<br>
                                <strong>KAMC - نظام إدارة المستودعات</strong>
                            </p>
                        </div>
                    </div>
                </body>
                </html>
            ";
            
            $email->setMessage($message);
            
            if ($email->send()) {
                log_message('info', "IT completion email sent to {$recipient->email}");
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            log_message('error', 'IT Completion Email Error: ' . $e->getMessage());
            return false;
        }
    }

public function sendReissueNotification($orderId, $receiverUserId, $itemOrderIds)
{
    try {
        if (!is_array($itemOrderIds)) {
            $itemOrderIds = [$itemOrderIds];
        }

        if (empty($itemOrderIds)) {
            log_message('error', 'No item order IDs provided for reissue notification');
            return false;
        }

        $itemOrderModel = new ItemOrderModel();
        $itemModel = new ItemModel();
        $employeeModel = new EmployeeModel();
        $userModel = new UserModel();
        $orderModel = new OrderModel();
        
        // Get the order details
        $order = $orderModel->find($orderId);
        
        if (!$order) {
            log_message('error', "Order not found for ID: {$orderId}");
            return false;
        }
        
        // Get sender (from_user_id)
        $sender = $employeeModel->where('emp_id', $order->from_user_id)->first();
        if (!$sender) {
            $sender = $userModel->where('user_id', $order->from_user_id)->first();
        }
        
        // Get receiver
        $receiver = $employeeModel->where('emp_id', $receiverUserId)->first();
        if (!$receiver) {
            $receiver = $userModel->where('user_id', $receiverUserId)->first();
        }
        
        if (!$receiver || empty($receiver->email)) {
            log_message('warning', "No email found for receiver: {$receiverUserId}");
            return false;
        }
        
        // Get all reissued items
        $itemOrders = $itemOrderModel->whereIn('item_order_id', $itemOrderIds)->findAll();
        
        if (empty($itemOrders)) {
            log_message('error', "No item orders found for IDs: " . implode(', ', $itemOrderIds));
            return false;
        }
        
        // Prepare items details for email
        $itemsDetails = [];
        foreach ($itemOrders as $itemOrder) {
            $item = $itemModel->find($itemOrder->item_id);
            
            $itemsDetails[] = [
                'asset_num' => $itemOrder->asset_num,
                'serial_num' => $itemOrder->serial_num,
                'item_name' => $item->name ?? 'غير محدد',
                'brand' => $itemOrder->brand ?? 'N/A',
                'model' => $itemOrder->model_num ?? 'N/A'
            ];
        }
        
        $emailData = [
            'receiver' => $receiver,
            'sender_name' => $sender->name ?? 'غير معروف',
            'order_id' => $orderId,
            'items' => $itemsDetails,
            'items_count' => count($itemsDetails)
        ];
        
        // Send the email
        if ($this->sendReissueEmail($emailData)) {
            log_message('info', "Reissue notification sent successfully to {$receiver->email} for order {$orderId}");
            return true;
        }
        
        return false;
        
    } catch (\Exception $e) {
        log_message('error', 'Reissue Email Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Build and send reissue email
 * 
 * @param array $emailData Email data containing receiver, sender, items, etc.
 * @return bool Success status
 */
private function sendReissueEmail($emailData)
{
    try {
        $email = \Config\Services::email();
        
        $email->setTo($emailData['receiver']->email);
        $email->setSubject('إشعار: تم إعادة صرف أصول جديدة لك - KAMC Inventory System');
        
        $baseUrl = base_url();
        $reviewUrl = $baseUrl . "employee/dashboard"; // Adjust to your employee dashboard route
        
        // Build items table rows
        $itemsList = '';
        foreach ($emailData['items'] as $item) {
            $itemsList .= "
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd;'>{$item['item_name']}</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>{$item['asset_num']}</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>{$item['serial_num']}</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>{$item['brand']}</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>{$item['model']}</td>
                </tr>
            ";
        }
        
        $message = "
            <html dir='rtl'>
            <head>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        direction: rtl; 
                        background-color: #f5f5f5;
                        margin: 0;
                        padding: 0;
                    }
                    .container { 
                        max-width: 600px; 
                        margin: 20px auto; 
                        background-color: white;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    }
                    .header { 
                        background-color: #0896baff; 
                        color: white; 
                        padding: 30px 15px; 
                        text-align: center;
                        font-size: 28px;
                        font-weight: bold;
                    }
                    .content { 
                        padding: 30px 20px; 
                        background-color: #f9f9f9; 
                    }
                    table { 
                        width: 100%; 
                        border-collapse: collapse; 
                        margin: 15px 0; 
                        background-color: white;
                    }
                    th { 
                        background-color: #0896baff; 
                        color: white; 
                        padding: 12px 10px; 
                        text-align: right;
                        font-weight: bold;
                    }
                    td { 
                        padding: 10px; 
                        border: 1px solid #ddd; 
                    }
                    .info-row {
                        margin: 10px 0;
                        line-height: 1.6;
                    }
                    .highlight-box {
                        background-color: #e7f3ff;
                        border: 2px solid #0896baff;
                        border-radius: 8px;
                        padding: 15px;
                        margin: 20px 0;
                        text-align: center;
                    }
                    .count-badge {
                        display: inline-block;
                        background-color: #28a745;
                        color: white;
                        padding: 5px 15px;
                        border-radius: 20px;
                        font-weight: bold;
                        font-size: 18px;
                        margin: 10px 0;
                    }
                    .btn-container {
                        text-align: center;
                        margin: 30px 0;
                    }
                    .btn {
                        display: inline-block;
                        background-color: #0896baff;
                        color: white !important;
                        padding: 15px 40px;
                        text-decoration: none;
                        border-radius: 5px;
                        font-weight: bold;
                        font-size: 16px;
                    }
                    .btn:hover {
                        background-color: #0678a0;
                    }
                    .footer {
                        background-color: #f8f9fa;
                        padding: 20px;
                        text-align: center;
                        border-top: 2px solid #0896baff;
                        margin-top: 20px;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        📦 إعادة صرف أصول
                    </div>
                    <div class='content'>
                        <p class='info-row'>عزيزي/عزيزتي <strong>{$emailData['receiver']->name}</strong>,</p>
                        
                        <div class='highlight-box'>
                            <p style='margin: 5px 0; font-size: 16px;'>تم إعادة صرف أصول جديدة لك من قبل:</p>
                            <p style='margin: 5px 0; font-size: 20px; color: #0896baff;'><strong>{$emailData['sender_name']}</strong></p>
                            <div class='count-badge'>{$emailData['items_count']} عنصر</div>
                        </div>
                        
                        <p class='info-row'><strong>رقم الطلب:</strong> <span style='color: #0896baff; font-size: 18px;'>#{$emailData['order_id']}</span></p>
                        
                        <h3 style='color: #0896baff; margin-top: 25px; border-bottom: 2px solid #0896baff; padding-bottom: 10px;'>
                            تفاصيل الأصول المعاد صرفها:
                        </h3>
                        <table>
                            <tr>
                                <th>اسم الصنف</th>
                                <th>رقم الأصل</th>
                                <th>الرقم التسلسلي</th>
                                <th>العلامة التجارية</th>
                                <th>الموديل</th>
                            </tr>
                            {$itemsList}
                        </table>
                        
                        <div style='background-color: #fff3cd; border: 2px solid #ffc107; border-radius: 8px; padding: 15px; margin: 20px 0;'>
                            <p style='margin: 0; color: #856404; font-weight: bold; text-align: center;'>
                                ⚠️ يرجى مراجعة النظام لقبول أو رفض الطلب
                            </p>
                        </div>
                        
                        <div class='btn-container'>
                            <a href='{$reviewUrl}' class='btn'>مراجعة الطلب في النظام</a>
                        </div>
                        
                        <div class='footer'>
                            <p style='margin: 5px 0; color: #666;'>
                                للاستفسارات، يرجى التواصل مع المخزن الرئيسي
                            </p>
                            <p style='margin: 5px 0; font-weight: bold;'>
                                KAMC - نظام إدارة المستودعات
                            </p>
                        </div>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        $email->setMessage($message);
        
        if ($email->send()) {
            log_message('info', "Reissue email sent successfully to {$emailData['receiver']->email}");
            return true;
        } else {
            log_message('error', 'Reissue email sending failed to ' . $emailData['receiver']->email . ': ' . $email->printDebugger(['headers']));
            return false;
        }
        
    } catch (\Exception $e) {
        log_message('error', 'Reissue Email Error: ' . $e->getMessage());
        return false;
    }
}
}