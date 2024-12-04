<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('keycaps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('content')->nullable();
            $table->json('galleries')->nullable();
            $table->json('videos')->nullable();
            $table->string('material')->nullable();
            $table->string('profile')->nullable();
            $table->date('date_released')->nullable();
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->json('prices')->nullable();
            $table->json('votes')->nullable();
            $table->json('type')->nullable();
            $table->json('colors')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('keycaps');
    }

};
