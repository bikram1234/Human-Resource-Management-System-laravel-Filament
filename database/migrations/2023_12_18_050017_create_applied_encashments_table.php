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
        Schema::create('applied_encashments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('date');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('mas_employees')->onDelete('cascade');
            $table->double('number_of_days'); 
            $table->double('amount'); 
            $table->string('remark')->nullable();
            $table->enum('status', ['pending', 'approved','rejected'])->default('pending'); // Add the status field            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applied_encashments');
    }
};
