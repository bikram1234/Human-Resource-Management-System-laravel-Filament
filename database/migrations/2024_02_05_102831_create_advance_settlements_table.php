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
        Schema::create('advance_settlements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('date'); 
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('mas_employees')->onDelete('cascade');
            $table->uuid('loan_advance_id')->required();
            $table->foreign('loan_advance_id')->references('id')->on('apply_loan_advances')->onDelete('cascade');
            $table->uuid('loantype_id');
            $table->foreign('loantype_id')->references('id')->on('loan_advancetypes')->onDelete('cascade');
            $table->decimal('advance_amount', 10, 2)->default(0);
            $table->decimal('balance_amount', 10, 2)->nullable(); // Make it nullable
            $table->string('attachment')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected','paid'])->default('pending');
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advance_settlements');
    }
};
