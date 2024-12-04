<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Loại tài nguyên (thumbnail, avatar, gallery, video, etc.)
            $table->unsignedBigInteger('resourceable_id'); // ID của bản ghi liên quan
            $table->string('resourceable_type'); // Loại đối tượng liên kết (brands, switches, keycaps, etc.)
            $table->string('path'); // Đường dẫn đến tài nguyên
            $table->text('description')->nullable(); // Mô tả tài nguyên
            $table->json('meta_data')->nullable(); // Dữ liệu meta (kích thước, định dạng, etc.)
            $table->string('option')->nullable(); // Dữ liệu meta (kích thước, định dạng, etc.)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resources');
    }
};
