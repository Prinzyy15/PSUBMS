<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Find students where students.student_parent is null/0 but a parents record exists with parent_student_id pointing to them
$rows = DB::table('students as s')
    ->leftJoin('parents as p', 'p.parent_student_id', '=', 's.student_id')
    ->leftJoin('users as u', DB::raw("u.name"), '=', DB::raw('CAST(p.parent_username AS CHAR)'))
    ->where(function($q){
        $q->whereNull('s.student_parent')->orWhere('s.student_parent', 0);
    })
    ->whereNotNull('p.parent_id')
    ->select('s.student_id','s.student_number','s.student_fname','s.student_lname','s.created_at as student_created_at',
             'p.parent_id','p.parent_student_id','p.parent_phone','p.parent_username','p.created_at as parent_created_at',
             'u.id as user_id','u.name as user_name','u.email as user_email')
    ->orderByDesc('s.created_at')
    ->get();

if ($rows->isEmpty()) {
    echo "No unlinked students found (where parent exists but students.student_parent is empty).\n";
    exit(0);
}

foreach ($rows as $r) {
    echo "---\n";
    echo "Student: {$r->student_number} (id: {$r->student_id}) - {$r->student_fname} {$r->student_lname} - created: {$r->student_created_at}\n";
    echo "Parent: id {$r->parent_id} parent_student_id: {$r->parent_student_id} phone: {$r->parent_phone} username: {$r->parent_username} created: {$r->parent_created_at}\n";
    if ($r->user_id) {
        echo "User: id {$r->user_id} name: {$r->user_name} email: {$r->user_email}\n";
    } else {
        echo "User: (no user found matching parent_username)\n";
    }
}

return 0;
