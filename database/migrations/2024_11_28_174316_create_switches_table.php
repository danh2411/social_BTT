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
        Schema::create('switches', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('content')->nullable();
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->string('thumbnail')->nullable();
            $table->json('galleries')->nullable();
            $table->json('videos')->nullable();
            $table->date('date_released')->nullable();
            $table->string('kind')->nullable();
            $table->integer('type')->nullable();
            $table->string('top_housing')->nullable();
            $table->string('bot_housing')->nullable();
            $table->string('stem')->nullable();
            $table->float('travel')->nullable();
            $table->float('force')->nullable();
            $table->json('prices')->nullable();
            $table->json('votes')->nullable();
            $table->json('colors')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('switches');
    }
};
