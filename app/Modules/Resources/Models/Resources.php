<?php

namespace App\Modules\Resources\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Resources extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'resourceable_id',
        'resourceable_type',
        'path',
        'description',
        'meta_data',
        'updated_at',
        'created_at',
    ];

    protected $casts = [
        'meta_data' => 'array', // Chuyển meta_data thành mảng
    ];

    // Khai báo các trường cần được chuyển thành đối tượng Carbon
    protected $dates = ['updated_at', 'created_at'];
    public function resourceable()
    {
        return $this->morphTo();
    }
}

