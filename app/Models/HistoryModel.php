<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoryModel extends Model
{
    protected $table            = 'history';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['item_order_id', 'usage_status_id', 'handled_by'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'item_order_id'    => 'required|integer|is_not_unique[item_order.item_order_id]',
        'usage_status_id'  => 'required|integer|is_not_unique[usage_status.id]',
        'handled_by'       => 'required|string|max_length[50]',
    ];

    protected $validationMessages = [
        'item_order_id' => [
            'required'      => 'رقم طلب الصنف مطلوب',
            'integer'       => 'رقم طلب الصنف يجب أن يكون رقم صحيح',
            'is_not_unique' => 'رقم طلب الصنف غير موجود في قاعدة البيانات',
        ],
        'usage_status_id' => [
            'required'      => 'حالة الاستخدام مطلوبة',
            'integer'       => 'رقم الحالة يجب أن يكون رقم صحيح',
            'is_not_unique' => 'حالة الاستخدام غير موجودة في قاعدة البيانات',
        ],
        'handled_by' => [
            'required'    => 'الجهة المنفذة مطلوبة',
            'max_length'  => 'معرّف الجهة لا يمكن أن يزيد عن 50 حرفًا',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get history with related item and status information
     */
    public function getHistoryWithDetails($itemOrderId = null)
    {
        $builder = $this->db->table($this->table)
            ->select('history.*, item_order.asset_num, items.name as item_name, usage_status.usage_status')
            ->join('item_order', 'item_order.item_order_id = history.item_order_id')
            ->join('items', 'items.id = item_order.item_id')
            ->join('usage_status', 'usage_status.id = history.usage_status_id');

        if ($itemOrderId !== null) {
            $builder->where('history.item_order_id', $itemOrderId);
        }

        return $builder->orderBy('history.created_at', 'DESC')->get()->getResultArray();
    }

    /**
     * Get history for specific user
     */
    public function getHistoryByUser($handledBy)
    {
        return $this->where('handled_by', $handledBy)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get recent history entries
     */
    public function getRecentHistory($limit = 10)
    {
        return $this->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}