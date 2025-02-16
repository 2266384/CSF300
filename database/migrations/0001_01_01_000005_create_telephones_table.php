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
        Schema::create('telephones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('std', 5);
            $table->string('number', 6);
            $table->string('type');
            $table->boolean('default')->default(false);
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')
                ->onDelete('cascade');
        });

        /**
         * Create a unique constraint on the table to only allow customers to have
         * one DEFAULT telephone number
         * The command changes depending on whether we're connecting to the PROD database (MS SQL)
         * or the TEST database (MySQL)
         */

        $db_type = DB::connection()->getDriverName();

        if ($db_type === 'sqlsrv') {
            DB::statement("CREATE UNIQUE INDEX IX_telephones_customerid_isDefault ON telephones (customer_id) WHERE [default] = 1");
        } else if ($db_type === 'mysql') {
            DB::statement("
                ALTER TABLE telephones
                ADD is_default_condition TINYINT(1) AS (CASE WHEN `default` = 1 THEN customer_id ELSE NULL END),
                ADD UNIQUE INDEX IX_telephones_customerid_isDefault (is_default_condition);
            ");
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telephones');
    }
};
