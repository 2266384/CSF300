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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer');
            $table->string('recipient_name');
            $table->unsignedBigInteger('source');
            $table->date('consent_date')->nullable();
            $table->date('removed_date')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('customer')->references('id')->on('customers')
                ->onDelete('cascade');

            $table->foreign('source')->references('id')->on('sources')
                ->onDelete('cascade');
        });

        /**
         * Add a UNIQUE CONSTRAINT on the table so we can only have one ACTIVE = 1 record
         */
        DB::statement("
            CREATE UNIQUE NONCLUSTERED INDEX UQ_registrations_customer_active
            ON registrations(customer, active)
            WHERE active = 1;
        ");


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
