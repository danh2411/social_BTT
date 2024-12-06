<?php

namespace App\Modules\Brand\Models;

use App\Modules\Resources\Models\Resources;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Brand extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong cơ sở dữ liệu.
     *
     * @var string
     */
    protected $table = 'brands';

    /**
     * Các thuộc tính có thể được gán giá trị hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'date_released',
        'thumbnail',
    ];

    /**
     * Các thuộc tính nên được ép kiểu.
     *
     * @var array
     */
    protected $casts = [
        'date_released' => 'date', // Ép kiểu cho ngày tháng
    ];

    /**
     * Truy xuất hình ảnh thumbnail cho brand.
     *
     * @return string
     */
    public function getThumbnailUrlAttribute()
    {
        // Giả sử bạn lưu đường dẫn hình ảnh thumbnail trong database,
        // Bạn có thể trả về URL của hình ảnh này.
        return asset('storage/brands/' . $this->thumbnail);
    }

    /**
     * Quan hệ với tài nguyên (nếu có).
     * Ví dụ: Một Brand có thể có nhiều tài nguyên như hình ảnh, video, v.v.
     */
    public function resources()
    {
        return $this->morphMany(Resources::class, 'resourceable');
    }
}
