<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$username = '2025-0003';

echo "Investigating username: $username\n\n";

// 1. Find user by username
$user = DB::table('users')->where('name', $username)->first();
if ($user) {
    echo "User found in 'users' table:\n";
    print_r($user);
} else {
    echo "User not found in 'users' table.\n";
}

// 2. Find parent by parent_username
$parent = DB::table('parents')->where('parent_username', $username)->first();
if ($parent) {
    echo "\nParent found in 'parents' table:\n";
    print_r($parent);

    // 3. Find student by student_parent
    $student_by_parent_link = DB::table('students')->where('student_parent', $parent->parent_id)->first();
    if ($student_by_parent_link) {
        echo "\nStudent found via student_parent = " . $parent->parent_id . ":\n";
        print_r($student_by_parent_link);
    } else {
        echo "\nNo student found with student_parent = " . $parent->parent_id . "\n";
    }

    // 4. Find student by parent_student_id
    $student_by_parent_student_id = DB::table('students')->where('student_id', $parent->parent_student_id)->first();
    if ($student_by_parent_student_id) {
        echo "\nStudent found via parent_student_id = " . $parent->parent_student_id . ":\n";
        print_r($student_by_parent_student_id);
    } else {
        echo "\nNo student found with student_id = " . $parent->parent_student_id . "\n";
    }

} else {
    echo "\nParent not found in 'parents' table.\n";
}

// 5. Find student with student_id 24 (from log)
$student_24 = DB::table('students')->where('student_id', 24)->first();
if ($student_24) {
    echo "\nStudent found with student_id = 24 (from log):\n";
    print_r($student_24);
} else {
    echo "\nNo student found with student_id = 24 (from log).\n";
}

// 6. Find parent with parent_id 12 (from log)
$parent_12 = DB::table('parents')->where('parent_id', 12)->first();
if ($parent_12) {
    echo "\nParent found with parent_id = 12 (from log):\n";
    print_r($parent_12);
} else {
    echo "\nNo parent found with parent_id = 12 (from log).\n";
}