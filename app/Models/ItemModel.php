<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Item;
use App\Entities\Items;
use Iterator;

class ItemModel extends Model
{
    protected $table = 'items'; // اسم الجدول الحقيقي
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = Items::class;
    protected $allowedFields = ['name', 'minor_category_id'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'name' => 'permit_empty|max_length[255]',
        'minor_category_id' => 'required|integer|is_not_unique[minor_category.id]'
    ];
    protected $validationMessages = [
        'name' => [
            'max_length' => 'اسم الصنف لا يمكن أن يزيد عن 255 حرف'
        ],
        'minor_category_id' => [
            'required' => 'التصنيف الفرعي مطلوب',
            'integer' => 'التصنيف الفرعي يجب أن يكون رقم صحيح',
            'is_not_unique' => 'التصنيف الفرعي المحدد غير موجود'
        ]
    ];
}
