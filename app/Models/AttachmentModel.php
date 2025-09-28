<?php
namespace App\Models;

use App\Entities\Attachment;
use CodeIgniter\Model;

class AttachmentModel extends Model
{
    protected $table = 'attachments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = Attachment::class;
    protected $allowedFields = ['file_name', 'file_path'];
        
    // Dates
    protected $useTimestamps = false;

    
    // Validation
    protected $validationRules = [
        'file_name' => 'required|max_length[255]',
        'file_path' => 'required|max_length[255]'
    ];
    protected $validationMessages = [
        'file_name' => [
            'required' => 'اسم الملف مطلوب',
            'max_length' => 'اسم الملف لا يمكن أن يزيد عن 255 حرف'
        ],
        'file_path' => [
            'required' => 'مسار الملف مطلوب',
            'max_length' => 'مسار الملف لا يمكن أن يزيد عن 255 حرف'
        ]
    ];
}