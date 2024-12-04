<?php

namespace App\Modules\Newsletter\Models;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{


    // Các thuộc tính có thể lưu trữ
    protected $fillable = [
        'user_id', 'title', 'content', 'tags', 'thumbnail',
        'location', 'creator', 'option', 'like', 'interact',
        'note', 'flag',
    ];

    // Truyền các trường JSON tự động parse
    protected $casts = [
        'option' => 'array',
    ];

    // Mối quan hệ ngược với bảng users
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
