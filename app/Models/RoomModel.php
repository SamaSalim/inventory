<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Room;

class RoomModel extends Model
{
    protected $table = 'room';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = Room::class;
    protected $allowedFields = ['code', 'section_id'];
    protected $useTimestamps = false; // No timestamps in migration

    protected $validationRules = [
        'code' => 'required|max_length[50]|is_unique[room.code,id,{id}]',
        'section_id' => 'required|integer|is_not_unique[section.id]'
    ];
    protected $validationMessages = [
        'code' => [
            'required' => 'رمز الغرفة مطلوب',
            'max_length' => 'رمز الغرفة لا يمكن أن يزيد عن 50 حرف',
            'is_unique' => 'رمز الغرفة موجود مسبقاً'
        ],
        'section_id' => [
            'required' => 'القسم مطلوب',
            'integer' => 'القسم يجب أن يكون رقم صحيح',
            'is_not_unique' => 'القسم المحدد غير موجود'
        ]
    ];


    // جلب رمز موقع كامل للغرفة
    public function getFullLocationCode($roomId)
    {
        $row = $this->select('
                building.code AS building_code,
                floor.code AS floor_code,
                section.code AS section_code,
                room.code AS room_code
            ')
            ->join('section', 'section.id = room.section_id')
            ->join('floor', 'floor.id = section.floor_id')
            ->join('building', 'building.id = floor.building_id')
            ->where('room.id', $roomId)
            ->first();
        
        if ($row) {
            return $row->building_code    . '-' .
                   $row->floor_code . '-' .
                   $row->section_code   . '-' .
                   $row->room_code;
        }

        return null;
    }


}


