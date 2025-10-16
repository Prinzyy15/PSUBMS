<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateViolationStatusEnumInViolationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Change enum values to match those used in code
        Schema::table('violations', function (Blueprint $table) {
            $table->enum('violation_status', ['settled', 'pending'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('violations', function (Blueprint $table) {
            $table->enum('violation_status', ['active', 'notactive'])->default('active')->change();
        });
    }
}
