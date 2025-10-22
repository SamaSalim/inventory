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
            
            // Get all super_warehouse users to send email to
            $recipients = $this->getSuperWarehouseUsers();
            
            if (empty($recipients)) {
                log_message('warning', 'No super_warehouse users found to send return notification');
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
                ];
            }
            
            $returnData = [
                'items' => $itemsDetails,
                'returned_by' => $returnedBy->name ?? 'غير معروف',
                'order_id' => $itemOrderIds[0] // Use first item order ID as reference
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
            
            return $emailsSent > 0;
            
        } catch (\Exception $e) {
            log_message('error', 'Return Email Error: ' . $e->getMessage());
            return false;
        }
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
        
        // Get all employees with super_warehouse role (role_id = 6)
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
     * @param array $returnData Return data with items array
     * @return bool Success status
     */
    private function sendEmail($recipient, $returnData)
    {
        try {
            $email = \Config\Services::email();
            
            $email->setTo($recipient->email);
            $email->setSubject('إشعار إرجاع أصول - KAMC Inventory System');
            
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
     * @param array $returnData Return data with items array
     * @return string HTML content
     */
    private function buildEmailHTML($recipient, $returnData)
    {
        $baseUrl = base_url();
        $reviewUrl = $baseUrl . "AssetsHistory";
        
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
                        إشعار إرجاع أصول
                    </div>
                    <div class='content'>
                        <p class='info-row'>عزيزي/عزيزتي <strong>{$recipient->name}</strong>,</p>
                        <p class='info-row'>تم إرجاع أصول جديدة من قبل: <strong>{$returnData['returned_by']}</strong></p>
                        <p class='info-row'><strong>رقم الطلب:</strong> {$returnData['order_id']}</p>
                        
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
                            <a href='{$reviewUrl}' class='btn'>مراجعة النظام لقبول أو رفض الطلب</a>
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
}