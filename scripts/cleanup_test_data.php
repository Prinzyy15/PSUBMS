<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    $students = DB::table('students')->where('student_number', 'like', 'TEST-%')->get();
    if ($students->isEmpty()) {
        echo "No test students found (student_number like 'TEST-%').\n";
        DB::rollBack();
        exit(0);
    }

    $studentIds = $students->pluck('student_id')->toArray();
    $studentNumbers = $students->pluck('student_number')->toArray();

    echo "Found " . count($studentIds) . " test student(s): \n";
    foreach ($students as $s) {
        echo " - {$s->student_number} (id: {$s->student_id})\n";
    }

    // Delete contacts
    $contactsDeleted = DB::table('contacts')->whereIn('contact_student_id', $studentIds)->delete();
    echo "Deleted {$contactsDeleted} contact(s).\n";

    // Delete parents linked by parent_student_id
    $parentsDeleted = DB::table('parents')->whereIn('parent_student_id', $studentIds)->delete();
    echo "Deleted {$parentsDeleted} parent(s) with parent_student_id pointing to test students.\n";

    // Delete users where name matches student_number
    $usersDeleted = DB::table('users')->whereIn('name', $studentNumbers)->delete();
    echo "Deleted {$usersDeleted} user(s) with names matching test student numbers.\n";

    // Finally delete students
    $studentsDeleted = DB::table('students')->whereIn('student_id', $studentIds)->delete();
    echo "Deleted {$studentsDeleted} student(s).\n";

    DB::commit();
    echo "Cleanup complete.\n";
} catch (Exception $e) {
    DB::rollBack();
    echo "Cleanup failed: " . $e->getMessage() . "\n";
    exit(1);
}
