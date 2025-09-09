<?php

namespace App\Models;

use App\Entities\OrderStatus;
use CodeIgniter\Model;
use App\Entities\Status;

class OrderStatusModel  extends Model
{
    protected $table            = 'order_status';   // اسم الجدول
    protected $primaryKey       = 'id';       // المفتاح الأساسي

    protected $allowedFields    = ['status'];
    protected $returnType       = OrderStatus::class;

    protected $useTimestamps    = true;
    protected $validationRules = [
        'status' => 'permit_empty|max_length[50]|is_unique[order_status.status,id,{id}]'
    ];
    protected $validationMessages = [
        'status' => [
            'max_length' => 'حالة الطلب لا يمكن أن تزيد عن 50 حرف',
            'is_unique' => 'حالة الطلب موجودة مسبقاً'
        ]
    ];
}
