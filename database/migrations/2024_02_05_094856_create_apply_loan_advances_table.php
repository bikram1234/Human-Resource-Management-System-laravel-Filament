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
        Schema::create('apply_loan_advances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_no')->unique();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('mas_employees')->onDelete('cascade');
            $table->date('date');
            $table->uuid('loan_type_id');
            $table->foreign('loan_type_id')->references('id')->on('loan_advancetypes')->onDelete('cascade'); 
            $table->unsignedBigInteger('budget_code');
            $table->foreign('budget_code')->references('id')->on('budget_codes')->onDelete('cascade'); 
            $table->decimal('amount', 10, 2);
            $table->string('activity');
            $table->string('subject')->nullable();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->string('attachment')->nullable();
            $table->enum('status', ['pending', 'approved','rejected', 'paid'])->default('pending'); // Add the status field
            $table->string('remark')->nullable();




            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apply_loan_advances');
    }
};
