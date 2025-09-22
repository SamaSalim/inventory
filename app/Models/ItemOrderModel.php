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

    // ✅ Validation rules محسنة
    protected $validationRules = [
        'order_id' => 'required|integer|is_not_unique[order.order_id]',
        'item_id' => 'required|integer|is_not_unique[items.id]',
        'brand' => 'permit_empty|max_length[50]',
        'quantity' => 'permit_empty|integer',
        'model_num' => 'permit_empty|max_length[100]',
        'asset_num' => 'required|max_length[100]',  // ✅ إزالة is_unique
        'serial_num' => 'required|max_length[100]', // ✅ إزالة is_unique
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
            'max_length' => 'رقم الأصل لا يمكن أن يزيد عن 100 حرف'
        ],
        'serial_num' => [
            'required' => 'الرقم التسلسلي مطلوب',
            'max_length' => 'الرقم التسلسلي لا يمكن أن يزيد عن 100 حرف'
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

    // ✅ دالة مخصصة للتحقق من التكرار عند التحديث
    public function updateWithUniqueCheck($id, $data)
    {
        // التحقق من تكرار رقم الأصول
        if (isset($data['asset_num'])) {
            $duplicateAsset = $this->where('asset_num', $data['asset_num'])
                                   ->where('item_order_id !=', $id)
                                   ->first();
            if ($duplicateAsset) {
                return [
                    'success' => false,
                    'message' => 'رقم الأصل موجود مسبقاً'
                ];
            }
        }

        // التحقق من تكرار الرقم التسلسلي
        if (isset($data['serial_num'])) {
            $duplicateSerial = $this->where('serial_num', $data['serial_num'])
                                    ->where('item_order_id !=', $id)
                                    ->first();
            if ($duplicateSerial) {
                return [
                    'success' => false,
                    'message' => 'الرقم التسلسلي موجود مسبقاً'
                ];
            }
        }

        // تحديث البيانات
        $result = $this->update($id, $data);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'تم التحديث بنجاح'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'فشل في التحديث'
            ];
        }
    }

    // ✅ دالة مخصصة للإدراج مع التحقق من التكرار
    public function insertWithUniqueCheck($data)
    {
        // التحقق من تكرار رقم الأصول
        if (isset($data['asset_num'])) {
            $duplicateAsset = $this->where('asset_num', $data['asset_num'])->first();
            if ($duplicateAsset) {
                return [
                    'success' => false,
                    'message' => 'رقم الأصل موجود مسبقاً'
                ];
            }
        }

        // التحقق من تكرار الرقم التسلسلي
        if (isset($data['serial_num'])) {
            $duplicateSerial = $this->where('serial_num', $data['serial_num'])->first();
            if ($duplicateSerial) {
                return [
                    'success' => false,
                    'message' => 'الرقم التسلسلي موجود مسبقاً'
                ];
            }
        }

        // إدراج البيانات
        $result = $this->insert($data);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'تم الإدراج بنجاح',
                'id' => $result
            ];
        } else {
            return [
                'success' => false,
                'message' => 'فشل في الإدراج'
            ];
        }
    }

// جلب رمز الموقع
public function getFullLocationCode($itemOrderId)
{
    $row = $this->select('
            building.code AS building_code,
            floor.code AS floor_code,
            section.code AS section_code,
            room.code AS room_code
        ')
        ->join('room', 'room.id = item_order.room_id')
        ->join('section', 'section.id = room.section_id')
        ->join('floor', 'floor.id = section.floor_id')
        ->join('building', 'building.id = floor.building_id')
        ->where('item_order.item_order_id', $itemOrderId)
        ->first();

    if ($row) {
        return $row->building_code . '-' .
               $row->floor_code    . '-' .
               $row->section_code  . '-' .
               $row->room_code;
    }

    return null; // لو ما لقى الطلب
}






    // دالة للحصول على قيم enum للـ assets_type
    public function getAssetsTypeEnum()
    {
        try {
            $query = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'assets_type'");
            $row = $query->getRow();
            
            $custodyTypes = [];
            if ($row) {
                $enumStr = $row->Type; // مثال: enum('غير محدد','عهدة عامة','عهدة خاصة')
                $enumStr = str_replace(["enum(", ")", "'"], "", $enumStr);
                $values = explode(",", $enumStr);

                foreach ($values as $value) {
                    $custodyTypes[] = [
                        'id' => trim($value),
                        'name' => trim($value)
                    ];
                }
            }
            
            return $custodyTypes;
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting assets_type enum: ' . $e->getMessage());
            return [];
        }
    }

    // دالة للبحث عن العناصر بواسطة asset_num
    public function findByAssetNum($assetNum)
    {
        return $this->where('asset_num', $assetNum)->first();
    }

    // دالة للبحث عن العناصر بواسطة serial_num
    public function findBySerialNum($serialNum)
    {
        return $this->where('serial_num', $serialNum)->first();
    }

    // دالة للحصول على عناصر طلب معين
    public function getOrderItems($orderId)
    {
        return $this->where('order_id', $orderId)->findAll();
    }

    // دالة للحصول على العناصر مع تفاصيل الصنف
    public function getItemsWithDetails($orderId = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('item_order.*, items.name as item_name, room.code as room_code');
        $builder->join('items', 'item_order.item_id = items.id', 'left');
        $builder->join('room', 'item_order.room_id = room.id', 'left');
        
        if ($orderId) {
            $builder->where('item_order.order_id', $orderId);
        }
        
        return $builder->get()->getResultArray();
    }
}



