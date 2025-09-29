<?php 
namespace App\Models;

use App\Entities\ReturnedItem;
use CodeIgniter\Model;

class ReturnedItemModel extends Model
{
    protected $table = 'returned_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = ReturnedItem::class;
    protected $allowedFields = ['item_order_id', 'notes', 'attach_id', 'return_date'];
    
    // Dates
    protected $useTimestamps = false;
    
    // Validation
    protected $validationRules = [
        'item_order_id' => 'required|integer|is_not_unique[item_order.item_order_id]',
        'notes' => 'permit_empty',
        'attach_id' => 'required|integer|is_not_unique[attachments.id]',
        'return_date' => 'required|valid_date'
    ];
    protected $validationMessages = [
        'item_order_id' => [
            'required' => 'رقم طلب الصنف مطلوب',
            'integer' => 'رقم طلب الصنف يجب أن يكون رقم صحيح',
            'is_not_unique' => 'رقم طلب الصنف المحدد غير موجود'
        ],
        'attach_id' => [
            'required' => 'المرفق مطلوب',
            'integer' => 'المرفق يجب أن يكون رقم صحيح',
            'is_not_unique' => 'المرفق المحدد غير موجود'
        ],
        'return_date' => [
            'required' => 'تاريخ الإرجاع مطلوب',
            'valid_date' => 'تاريخ الإرجاع غير صحيح'
        ]
    ];
 
}
