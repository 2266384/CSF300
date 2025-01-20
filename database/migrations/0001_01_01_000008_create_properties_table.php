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
         * The command changes depending on whether we're connecting to the PROD database (MS SQL)
         * or the TEST database (MySQL)
         */

        $db_type = DB::connection()->getDriverName();

        if ($db_type === 'sqlsrv') {
            DB::statement("ALTER TABLE properties ADD CHECK(house_number IS NOT NULL OR house_name IS NOT NULL)");
        } else if ($db_type === 'mysql') {
            // Create INSERT trigger
            DB::statement("
                CREATE TRIGGER check_house_number_or_name_insert
                BEFORE INSERT ON properties
                FOR EACH ROW
                BEGIN
                    IF NEW.house_number IS NULL AND NEW.house_name IS NULL THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Either house_number or house_name must be provided';
                    END IF;
                END"
            );
            
            // Create UPDATE trigger
            DB::statement("
                CREATE TRIGGER check_house_number_or_name_update
                BEFORE UPDATE ON properties
                FOR EACH ROW
                BEGIN
                    IF NEW.house_number IS NULL AND NEW.house_name IS NULL THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Either house_number or house_name must be provided';
                    END IF;
                END"
            );
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('DROP TRIGGER IF EXISTS check_house_number_or_name_insert');
            DB::statement('DROP TRIGGER IF EXISTS check_house_number_or_name_update');
        }
        Schema::dropIfExists('properties');
    }
};
