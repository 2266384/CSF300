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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('SAP_reference')->nullable();
            $table->string('primary_title');
            $table->string('primary_forename');
            $table->string('primary_surname');
            $table->date('primary_dob')->nullable();
            $table->string('secondary_title')->nullable();
            $table->string('secondary_forename')->nullable();
            $table->string('secondary_surname')->nullable();
            $table->date('secondary_dob')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
