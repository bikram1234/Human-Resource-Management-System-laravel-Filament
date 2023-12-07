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
        Schema::create('nodues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('date');
            $table->text('reason')->required();
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['pending', 'approved','rejected'])->default('pending'); 
            $table->foreign('user_id')->references('id')->on('mas_employees')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nodues');
    }
};
