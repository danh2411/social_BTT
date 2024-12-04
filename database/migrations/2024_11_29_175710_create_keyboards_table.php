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
        Schema::create('keyboards', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('size')->nullable();
            $table->string('mode')->nullable();
            $table->date('date_released')->nullable();
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->json('plates')->nullable();
            $table->json('mountings')->nullable();
            $table->json('type')->nullable();
            $table->string('country')->nullable();
            $table->json('colors')->nullable();
            $table->json('prices')->nullable();
            $table->json('galleries')->nullable();
            $table->json('videos')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('keyboards');
    }
};
