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
        Schema::create('advance_approvals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('applied_advance_id');
            $table->foreign("applied_advance_id")->references("id")->on("apply_advances")->onDelete('RESTRICT')->onUpdate('CASCADE');            
            $table->string('level1')->default('pending');
            $table->string('level2')->default('pending');
            $table->string('level3')->default('pending');
            $table->string('remark')->nullable();
            $table->unsignedBigInteger("created_by")->index();
            $table->unsignedBigInteger("edited_by")->index()->nullable();
            $table->foreign("created_by")->references("id")->on("mas_employees")->onDelete('RESTRICT')->onUpdate('CASCADE');
            $table->foreign("edited_by")->references("id")->on("mas_employees")->onDelete('RESTRICT')->onUpdate('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advance_approvals');
    }
};
