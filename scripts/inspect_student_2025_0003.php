<?php
// Bootstrap the full Laravel application so we can use the DB facade with the project's configuration
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$student = DB::table('students')->where('student_number', '2025-0003')->first();
if (!$student) {
    echo "Student 2025-0003 not found\n";
    exit(1);
}

$parent = DB::table('parents')->where('parent_id', $student->student_parent)->first();
$contacts = DB::table('contacts')->where('contact_student_id', $student->student_id)->get();

echo "STUDENT:\n";
print_r($student);
echo "\nPARENT:\n";
print_r($parent);
echo "\nCONTACTS:\n";
print_r($contacts->toArray());

return 0;
