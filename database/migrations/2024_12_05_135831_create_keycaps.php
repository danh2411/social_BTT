<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('keycaps');
        Schema::create('keycaps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('thumbnail')->nullable();  // Thêm thumbnail cho keycap
            $table->text('content')->nullable();
            $table->json('galleries')->nullable();  // Nếu bạn vẫn muốn lưu gallery dưới dạng JSON
            $table->json('videos')->nullable();
            $table->string('material')->nullable();
            $table->string('profile')->nullable();
            $table->date('date_released')->nullable();
            $table->string('brand')->nullable();
            $table->json('prices')->nullable();
            $table->json('votes')->nullable();
            $table->json('type')->nullable();
            $table->json('colors')->nullable();
            $table->integer('status')->default(0);
            $table->integer('count')->default(0);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keycaps');
    }
};
