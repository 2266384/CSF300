<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uprn')->unique();
            $table->string('house_number')->nullable();
            $table->string('house_name')->nullable();
            $table->string('street');
            $table->string('town')->nullable();
            $table->string('parish')->nullable();
            $table->string('county')->nullable();
            $table->string('postcode', 8);
            $table->unsignedBigInteger('occupier')->nullable();
            $table->timestamps();

            $table->foreign('occupier')->references('id')->on('customers')
                ->onUpdate('cascade')->onDelete('cascade');

        });

        /**
         * Add constraint to check House Number AND House Name are not both NULL values
         * One of the two may be NULL or both can have values but both cannot be NULL
         */
        DB::statement("ALTER TABLE properties ADD CHECK(house_number IS NOT NULL OR house_name IS NOT NULL)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
