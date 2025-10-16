<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Auth\LoginController;

// Settings for the test
$testStudentNumber = 'TEST-'.time();
$testPhone = '+639171234567'; // sample PH number

try {
    DB::beginTransaction();

    // 1) Create a student
    $studentId = DB::table('students')->insertGetId([
        'student_number' => $testStudentNumber,
        'student_fname' => 'Test',
        'student_mname' => 'E',
        'student_lname' => 'Student',
        'student_gender' => 'm',
        'student_dob' => '2010-01-01',
        'student_address' => 'Test Address',
        'student_parent' => 0,
        'student_year' => 1,
        'student_block' => 1,
        'student_course' => 1,
        'student_status' => 'active',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    // 2) Create parent record (parent_student_id set, but student.student_parent still 0)
    $parentId = DB::table('parents')->insertGetId([
        'parent_student_id' => $studentId,
        'parent_fname' => 'Test',
        'parent_mname' => 'P',
        'parent_lname' => 'Parent',
        'parent_phone' => $testPhone,
        'parent_username' => $testStudentNumber,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    // 3) Create a user record for the parent (username same as student_number)
    $userId = DB::table('users')->insertGetId([
        'name' => $testStudentNumber,
        'email' => $testStudentNumber . '@example.com',
        'password' => bcrypt($testStudentNumber),
        'role' => 'parent',
        'status' => 'active'
    ]);

    echo "Created test student: $testStudentNumber (id: $studentId)\n";
    echo "Created test parent: id $parentId, phone $testPhone\n";
    echo "Created test user: id $userId\n";

    // 4) Simulate parent login - invoke the authenticated method
    $controller = new LoginController();
    $request = Illuminate\Http\Request::create('/login', 'POST');
    $user = new stdClass();
    $user->name = $testStudentNumber;
    $user->role = 'parent';

    $reflection = new ReflectionMethod(LoginController::class, 'authenticated');
    $reflection->setAccessible(true);
    $reflection->invoke($controller, $request, $user);

    // 5) Inspect results
    $student = DB::table('students')->where('student_id', $studentId)->first();
    $parent = DB::table('parents')->where('parent_id', $parentId)->first();
    $contacts = DB::table('contacts')->where('contact_student_id', $studentId)->get();

    echo "\nAfter simulated login:\n";
    print_r($student);
    print_r($parent);
    print_r($contacts->toArray());

    // Commit transaction so test data is visible (you can change this to roll back if you prefer cleanup)
    DB::commit();
    echo "\nTest committed.\n";
} catch (Exception $e) {
    DB::rollBack();
    echo "Test failed: " . $e->getMessage() . "\n";
}
