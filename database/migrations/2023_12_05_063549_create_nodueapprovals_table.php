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
        Schema::create('nodueapprovals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->uuid('no_due_id');
            $table->text('remark')->nullable();
            $table->foreign('user_id')->references('id')->on('mas_employees')->onDelete('cascade');
            $table->foreign('no_due_id')->references('id')->on('nodues')->onDelete('cascade');
            $table->enum('status1', ['pending', 'approved','rejected'])->default('pending'); 
            $table->enum('status2', ['pending', 'approved','rejected'])->default('pending'); 
            $table->json('approver_user_id')->nullable();
            $table->json('department_approval_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nodueapprovals');
    }
};
