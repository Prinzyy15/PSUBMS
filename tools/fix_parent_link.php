<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

DB::transaction(function () {
    // 1. Link the student to the correct parent
    DB::table('students')
        ->where('student_id', 24)
        ->update(['student_parent' => 12]);

    echo "Student with ID 24 linked to parent with ID 12.\n";

    // 2. Delete the incorrect parent record
    DB::table('parents')->where('parent_id', 9)->delete();

    echo "Incorrect parent record with ID 9 deleted.\n";
});

echo "Data fixed successfully.\n";
