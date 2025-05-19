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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('registration_id');
            $table->string('code');
            $table->string('temp_end_date',)->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('lastupdate_id');
            $table->string('lastupdate_type');

            $table->foreign('registration_id')->references('id')->on('registrations');
            $table->foreign('code')->references('code')->on('service_codes');

        });

        /**
         * Use DB functions to turn the table into a System Versioned table and create the
         * linked history table
         * The command should only run when we're connecting to the PROD database (MS SQL)
         */

        $db_type = DB::connection()->getDriverName();

        if ($db_type === 'sqlsrv') {
            // Create the Valid_From and Valid_To columns for recording updates
            DB::statement("
                ALTER TABLE services
                ADD
                    valid_from datetime2(2) GENERATED ALWAYS AS ROW START NOT NULL DEFAULT GETUTCDATE(),
                    valid_to datetime2(2) GENERATED ALWAYS AS ROW END NOT NULL DEFAULT CONVERT(datetime2(2), '9999-12-31 23:59:59.9999999'),
                    PERIOD FOR SYSTEM_TIME (valid_from, valid_to)
            ");

            // Turn on System Versioning
            DB::statement("
                ALTER TABLE services
                SET (SYSTEM_VERSIONING = ON (HISTORY_TABLE = dbo.services_history))
            ");
        } else if ($db_type === 'mysql') {

            /*
             * Add validfrom and validto datetime columns
             * ValidFrom should default to the Current Timestamp when its created or updated
             * ValidTo should always be 31st December 9999
             */
            DB::statement("
            ALTER TABLE services
            ADD COLUMN valid_from DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            ADD COLUMN valid_to DATETIME DEFAULT '9999-12-31 23:59:59'
            ");

            /*
             * Create trigger on the table to copy data to the needs_history table before updating the record
             * The valid_from should be the valid_from date of the source table
             * The valid_to should use the Current Timestamp so the records can be presented chronologically
             */
            DB::statement("
            CREATE TRIGGER before_services_update
            BEFORE UPDATE ON services
            FOR EACH ROW
            BEGIN
                INSERT INTO services_history (id, registration_id, code, temp_end_date, active, lastupdate_id, lastupdate_type, valid_from, valid_to)
                VALUES (OLD.id, OLD.registration_id, OLD.code, OLD.temp_end_date, OLD.active, OLD.lastupdate_id, OLD.lastupdate_type, OLD.valid_from, CURRENT_TIMESTAMP);
            END;
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        $db_type = DB::connection()->getDriverName();

        if ($db_type === 'sqlsrv') {

            // Turn off System Versioning
            DB::statement("ALTER TABLE services SET (SYSTEM_VERSIONING = OFF)");
            Schema::dropIfExists('services');
            Schema::dropIfExists('services_history');
        }
    }
};
