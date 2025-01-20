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
         * The command changes depending on whether we're connecting to the PROD database (MS SQL)
         *  or the TEST database (MySQL)
         */

        $db_type = DB::connection()->getDriverName();

        if ($db_type === 'sqlsrv') {
            DB::statement("
                CREATE UNIQUE NONCLUSTERED INDEX UQ_registrations_customer_active
                ON registrations(customer, active)
                WHERE active = 1;
            ");
/*        } else if ($db_type === 'mysql') {
            DB::statement("
                ALTER TABLE registrations
                ADD active_customer_condition INT GENERATED ALWAYS AS (CASE WHEN active = 1 THEN customer ELSE NULL END) STORED,
                ADD UNIQUE INDEX UQ_registrations_customer_active (active_customer_condition)");
*/
        }



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
