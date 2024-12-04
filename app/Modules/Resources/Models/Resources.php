<?php

namespace App\Modules\Resources\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Resources extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'resourceable_id',
        'resourceable_type',
        'path',
        'description',
        'meta_data',
    ];

    protected $casts = [
        'meta_data' => 'array', // Chuyển meta_data thành mảng
    ];

    public function resourceable()
    {
        return $this->morphTo();
    }
}

