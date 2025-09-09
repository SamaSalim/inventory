<?php

namespace App\Models;

use App\Entities\ItemOrder;
use CodeIgniter\Model;

class ItemOrderModel extends Model
{
    protected $table = 'item_order';
    protected $primaryKey = 'item_order_id';
    protected $useAutoIncrement = true;
    protected $returnType = ItemOrder::class;
    protected $allowedFields = [
        'order_id',
        'item_id',
        'brand',
        'quantity',
        'model_num',
        'asset_num',
        'serial_num',
        'old_asset_num',
        'room_id',
        'assets_type',
        'created_by',
        'usage_status_id',
        'note'
    ];

    // Dates
    protected $useTimestamps = true;


    // Validation
    protected $validationRules = [
        'order_id' => 'required|integer|is_not_unique[order.order_id]',
        'item_id' => 'required|integer|is_not_unique[items.id]',
        'brand' => 'permit_empty|max_length[50]',
        'quantity' => 'permit_empty|integer',
        'model_num' => 'permit_empty|max_length[100]',
        'asset_num' => 'required|max_length[100]|is_unique[item_order.asset_num,item_order_id,{item_order_id}]',
        'serial_num' => 'required|max_length[100]|is_unique[item_order.serial_num,item_order_id,{item_order_id}]',
        'old_asset_num' => 'permit_empty|max_length[100]',
        'room_id' => 'required|integer|is_not_unique[room.id]',
        'assets_type' => 'permit_empty|in_list[غير محدد,عهدة عامة,عهدة خاصة]',
        'created_by' => 'permit_empty|max_length[50]',
        'usage_status_id' => 'required|integer|is_not_unique[usage_status.id]',
        'note' => 'permit_empty'
    ];
    protected $validationMessages = [
        'order_id' => [
            'required' => 'رقم الطلب مطلوب',
            'integer' => 'رقم الطلب يجب أن يكون رقم صحيح',
            'is_not_unique' => 'رقم الطلب المحدد غير موجود'
        ],
        'item_id' => [
            'required' => 'الصنف مطلوب',
            'integer' => 'الصنف يجب أن يكون رقم صحيح',
            'is_not_unique' => 'الصنف المحدد غير موجود'
        ],
        'brand' => [
            'max_length' => 'اسم الماركة لا يمكن أن يزيد عن 50 حرف'
        ],
        'quantity' => [
            'integer' => 'الكمية يجب أن تكون رقم صحيح'
        ],
        'model_num' => [
            'max_length' => 'رقم الموديل لا يمكن أن يزيد عن 100 حرف'
        ],
        'asset_num' => [
            'required' => 'رقم الأصل مطلوب',
            'max_length' => 'رقم الأصل لا يمكن أن يزيد عن 100 حرف',
            'is_unique' => 'رقم الأصل موجود مسبقاً'
        ],
        'serial_num' => [
            'required' => 'الرقم التسلسلي مطلوب',
            'max_length' => 'الرقم التسلسلي لا يمكن أن يزيد عن 100 حرف',
            'is_unique' => 'الرقم التسلسلي موجود مسبقاً'
        ],
        'assets_type' => [
            'in_list' => 'نوع الأصل يجب أن يكون أحد القيم: غير محدد، عهدة عامة، عهدة خاصة'
        ],
        'old_asset_num' => [
            'max_length' => 'رقم الأصل القديم لا يمكن أن يزيد عن 100 حرف'
        ],
        'room_id' => [
            'required' => 'الغرفة مطلوبة',
            'integer' => 'الغرفة يجب أن تكون رقم صحيح',
            'is_not_unique' => 'الغرفة المحددة غير موجودة'
        ],
        'created_by' => [
            'max_length' => 'اسم المنشئ لا يمكن أن يزيد عن 50 حرف'
        ],
        'usage_status_id' => [
            'required' => 'حالة الاستخدام مطلوبة',
            'integer' => 'حالة الاستخدام يجب أن تكون رقم صحيح',
            'is_not_unique' => 'حالة الاستخدام المحددة غير موجودة'
        ]
    ];
}
