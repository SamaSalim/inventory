<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Building;

class BuildingModel extends Model
{
    protected $table = 'building';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = Building::class;
    protected $allowedFields = ['code'];
    protected $useTimestamps = false; // No timestamps 
    protected $validationRules = [
        'code' => 'required|max_length[50]|is_unique[building.code,id,{id}]'
    ];
    protected $validationMessages = [
        'code' => [
            'required' => 'رمز المبنى مطلوب',
            'max_length' => 'رمز المبنى لا يمكن أن يزيد عن 50 حرف',
            'is_unique' => 'رمز المبنى موجود مسبقاً'
        ]
    ];
}
