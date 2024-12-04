<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('newsletters', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('user_id'); // Foreign key liên kết với bảng users
            $table->string('title');
            $table->text('content');
            $table->string('tags')->nullable(); // Chuỗi tag, có thể lưu ở dạng JSON hoặc CSV
            $table->string('thumbnail')->nullable(); // URL ảnh thumbnail
            $table->string('location')->nullable(); // Vị trí
            $table->string('creator')->nullable(); // ID người tạo
            $table->json('option')->nullable(); // Các tùy chọn bổ sung
            $table->integer('like')->default(0); // Số lượt thích
            $table->integer('interact')->default(0); // Số lượt tương tác
            $table->text('note')->nullable(); // Ghi chú
            $table->boolean('flag')->default(false); // Cờ đánh dấu (true/false)
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('newsletters');
    }
};
