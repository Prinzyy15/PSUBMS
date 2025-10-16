<?php


namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Database\Eloquent\Model;

class Students extends Model
{
	protected $primaryKey = 'student_id';
	public $incrementing = true;
	public $keyType = 'int';
	protected $table = 'students';    

	public function new($data)
	{
		try {
			DB::beginTransaction();
			$student = new self();
			if (isset($data['student_avatar'])) {
				$student->student_avatar = $data['student_avatar'];
			}
			$student->student_number = $data['student_number'];
			if (isset($data['student_password'])) {
				$student->student_password = $data['student_password'];
			}
			$student->student_fname = ucwords($data['student_fname']);
			$student->student_mname = ucwords($data['student_mname']);
			$student->student_lname = ucwords($data['student_lname']);
			$student->student_dob = $data['student_dob'];
			$student->student_address = ucwords($data['student_address']);
			$student->student_course = $data['student_course'];
			$student->student_year = $data['student_year'];
			$student->student_block = $data['student_block'];
			$student->student_status = $data['student_status'];
			$student->student_gender = $data['student_gender'];
			$student->student_parent = 0;

			$saved = $student->save();

			if ($saved) {
    $parent_fname = isset($data['parent_fname']) ? ucwords($data['parent_fname']) : 'Parent';
    $parent_mname = isset($data['parent_mname']) ? ucwords($data['parent_mname']) : '';
    $parent_lname = isset($data['parent_lname']) ? ucwords($data['parent_lname']) : $student->student_lname;

    // Check for existing parent with the same name
    $existingParent = \App\Parents::where('parent_fname', $parent_fname)
        ->where('parent_mname', $parent_mname)
        ->where('parent_lname', $parent_lname)
        ->first();

    if ($existingParent) {
        // Link student to the existing parent
        $student->student_parent = $existingParent->parent_id;
        $student->save();
        DB::commit();
        return [
            'status' => 'success',
            'message' => $student->student_fname . ' ' . $student->student_lname . ' has been added and linked to an existing parent account.',
            'parent_username' => null,
            'parent_password' => null
        ];
    } else {
        // Create a new parent user and parent record
        $parentUsername = $data['student_number'];
        $parentPassword = bcrypt($parentUsername);
        $uniqueEmail = $parentUsername . '_' . uniqid() . '@parent.local';
        
        $parentUser = \App\User::create([
            'name' => $parentUsername,
            'email' => $uniqueEmail,
            'password' => $parentPassword,
            'role' => 'parent',
            'status' => 'active',
        ]);

        $parent = new \App\Parents();
        $parent->parent_student_id = $student->student_id;
        $parent->parent_fname = $parent_fname;
        $parent->parent_mname = $parent_mname;
        $parent->parent_lname = $parent_lname;
        $parent->parent_username = $parentUsername;
        $parent->save();

        Log::info('Parent created:', ['parent' => $parent]);

        // Link the student to the newly created parent
        $student->student_parent = $parent->parent_id;
        $student->save();

        Log::info('Student linked to parent:', ['student_id' => $student->student_id, 'parent_id' => $parent->parent_id]);

        DB::commit();
        return [
            'status' => 'success',
            'message' => $student->student_fname . ' ' . $student->student_lname . ' has been added and a new parent account has been created.',
            'parent_username' => $parentUsername,
            'parent_password' => $parentUsername
        ];
    }
} else {
    DB::rollBack();
    Log::error('Failed to save student', ['data' => $data]);
    return [
        'status' => 'error',
        'message' => 'Oops! Something went wrong while saving the student.'
    ];
}
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error('Exception while adding student', ['error' => $e->getMessage(), 'data' => $data]);
			return [
				'status' => 'error',
				'message' => 'An error occurred: ' . $e->getMessage()
			];
		}
	}
}
