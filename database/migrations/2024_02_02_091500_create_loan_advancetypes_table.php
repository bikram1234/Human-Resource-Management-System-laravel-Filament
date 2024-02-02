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
        Schema::create('loan_advancetypes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('condition');
            $table->tinyInteger("status")->comment("1 for active, 0 for In-active");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_advancetypes');
    }
};
