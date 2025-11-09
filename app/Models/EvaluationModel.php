<?php
namespace App\Models;

use CodeIgniter\Model;

class EvaluationModel extends Model
{
    protected $table = 'evaluation'; 
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array'; 
    protected $allowedFields = [
        'item_order_id',
        'handled_by',
        'notes',
        'attachment',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = false;
}
