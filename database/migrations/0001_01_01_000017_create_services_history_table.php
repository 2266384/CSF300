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

        // This table only needs to be created on mySQL
        $db_type = DB::connection()->getDriverName();

        if ($db_type === 'mysql') {
            Schema::create('services_history', function (Blueprint $table) {
                $table->unsignedBigInteger('id');
                $table->unsignedBigInteger('registration_id');
                $table->string('code');
                $table->string('temp_end_date',)->nullable();
                $table->boolean('active')->default(true);
                $table->unsignedBigInteger('lastupdate_id');
                $table->string('lastupdate_type');
                $table->dateTime('valid_from');
                $table->dateTime('valid_to');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services_history');
    }
};
