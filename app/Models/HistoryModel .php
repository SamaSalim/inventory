<?php

namespace App\Models;

use App\Entities\History;
use CodeIgniter\Model;

class HistoryModel extends Model
{
    protected $table = 'history';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = History::class;
    protected $allowedFields = ['item_order_id'];



    // Dates
    protected $useTimestamps = true;


    // Validation
    protected $validationRules = [
        'item_order_id' => 'required|integer|is_not_unique[item_order.item_order_id]'
    ];
    protected $validationMessages = [
        'item_order_id' => [
            'required' => 'رقم طلب الصنف مطلوب',
            'integer' => 'رقم طلب الصنف يجب أن يكون رقم صحيح',
            'is_not_unique' => 'رقم طلب الصنف المحدد غير موجود'
        ]
    ];
}
