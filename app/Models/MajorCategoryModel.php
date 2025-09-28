<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\MajorCategory;

class MajorCategoryModel extends Model
{
    protected $table = 'major_category';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $returnType = MajorCategory::class;
    protected $allowedFields = ['name'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'name' => 'required|max_length[255]|is_unique[major_category.name,id,{id}]'
    ];
    protected $validationMessages = [
        'name' => [
            'required' => 'اسم التصنيف الرئيسي مطلوب',
            'max_length' => 'اسم التصنيف الرئيسي لا يمكن أن يزيد عن 255 حرف',
            'is_unique' => 'اسم التصنيف الرئيسي موجود مسبقاً'
        ]
    ];
}
