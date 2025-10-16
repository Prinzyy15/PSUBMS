<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\DB;

// create a fake user object similar to Auth user
$user = new stdClass();
$user->name = '2025-0003';
$user->role = 'parent';
$user->id = 9999;

$controller = new LoginController();
// Create a minimal Request instance
$request = Illuminate\Http\Request::create('/login', 'POST');
// Call authenticated
$reflection = new ReflectionMethod(LoginController::class, 'authenticated');
$reflection->setAccessible(true);
$reflection->invoke($controller, $request, $user);

// Inspect the student now
// Inspect the student now
$student = DB::table('students')->where('student_number', '2025-0003')->first();
if (!$student) {
	echo "Student 2025-0003 not found after repair.\n";
	exit(1);
}

$parent = DB::table('parents')->where('parent_id', $student->student_parent)->first();
$contacts = DB::table('contacts')->where('contact_student_id', $student->student_id)->get();

echo "STUDENT AFTER REPAIR:\n"; print_r($student);
echo "PARENT AFTER REPAIR:\n"; print_r($parent);
echo "CONTACTS AFTER REPAIR:\n"; print_r($contacts->toArray());
