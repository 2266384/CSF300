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
        Schema::create('needs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer');
            $table->unsignedInteger('code');
            $table->unsignedBigInteger('lastupdate_id');
            $table->string('lastupdate_type');

            $table->foreign('customer')->references('id')->on('customers');
            $table->foreign('code')->references('code')->on('need_codes');


        });

        /**
         * Use DB functions to turn the table into a System Versioned table and create the
         * linked history table
         */
        // Create the Valid_From and Valid_To columns for recording updates
        DB::statement("
            ALTER TABLE needs
            ADD
                valid_from datetime2(2) GENERATED ALWAYS AS ROW START NOT NULL DEFAULT GETUTCDATE(),
                valid_to datetime2(2) GENERATED ALWAYS AS ROW END NOT NULL DEFAULT CONVERT(datetime2(2), '9999-12-31 23:59:59.9999999'),
                PERIOD FOR SYSTEM_TIME (valid_from, valid_to)
        ");

        // Turn on System Versioning
        DB::statement("
            ALTER TABLE needs
            SET (SYSTEM_VERSIONING = ON (HISTORY_TABLE = dbo.needs_history))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Turn off System Versioning
        DB::statement("ALTER TABLE needs SET (SYSTEM_VERSIONING = OFF)");
        Schema::dropIfExists('needs');
        Schema::dropIfExists('needs_history');
    }
};
