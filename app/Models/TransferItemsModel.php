<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\TransferItems;

class  TransferItemsModel extends Model
{
    protected $table         = 'transfer_items';
    protected $primaryKey    = 'transfer_item_id';
    protected $useAutoIncrement = true;
    protected $returnType    = TransferItems::class;
    protected $allowedFields = ['item_order_id','from_user_id', 'to_user_id', 'order_status_id', 'note'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'item_order_id' => 'required|integer|is_not_unique[item_order.item_order_id]',
        'from_user_id' => 'required|max_length[50]|is_not_unique[users.user_id]',
        'to_user_id' => 'required|max_length[50]|is_not_unique[users.user_id]',
        'order_status_id' => 'required|integer|is_not_unique[order_status.id]',
        'note' => 'permit_empty'
    ];
    protected $validationMessages = [
        'item_order_id' => [
            'required' => 'رقم طلب الصنف مطلوب',
            'integer' => 'رقم طلب الصنف يجب أن يكون رقم صحيح',
            'is_not_unique' => 'رقم طلب الصنف المحدد غير موجود'
        ],
        'to_user_id' => [
            'required' => 'الموظف المستلم مطلوب',
            'max_length' => 'رقم الموظف المستلم لا يمكن أن يزيد عن 50 حرف',
            'is_not_unique' => 'الموظف المستلم غير موجود'
        ],
        'order_status_id' => [
            'required' => 'حالة الطلب مطلوبة',
            'integer' => 'حالة الطلب يجب أن تكون رقم صحيح',
            'is_not_unique' => 'حالة الطلب المحددة غير موجودة'
        ],
        'from_user_id' => [
            'required' => 'الموظف المرسل مطلوب',
            'max_length' => 'رقم الموظف المرسل لا يمكن أن يزيد عن 50 حرف',
            'is_not_unique' => 'الموظف المرسل غير موجود'
        ],
    ];
}
