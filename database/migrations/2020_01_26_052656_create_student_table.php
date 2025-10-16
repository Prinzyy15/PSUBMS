<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('student_id');
            $table->string('student_number');
            $table->string('student_password')->nullable();
            $table->longText('student_avatar')->nullable();
            $table->string('student_fname');
            $table->string('student_mname')->nullable();
            $table->string('student_lname');
            $table->enum('student_gender', ['m', 'f']);
            $table->string('student_dob');
            $table->longText('student_address');
            $table->integer('student_parent');
            $table->integer('student_year');
            $table->integer('student_block');
            $table->integer('student_course');
            $table->enum('student_status', ['dropped','graduated','active','notactive']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
