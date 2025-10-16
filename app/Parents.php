<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Parents extends Model
{
	protected $table = 'parents';
	protected $primaryKey = 'parent_id';
	public $incrementing = true;
	protected $keyType = 'int';

	public function new($data)
	{    
		$parent = new Parents;
		$parent->parent_student_id = $data['parent_student_id'];
		$parent->parent_fname = $data['parent_fname'];
		$parent->parent_mname = $data['parent_mname'];
		$parent->parent_lname = $data['parent_lname'];        

		$student = DB::table('students')->where('student_id', $data['parent_student_id'])->first();
		$parent->parent_username = $student->student_number;

		if($parent->save()) {
			$parentUser = \App\User::create([
				'name' => $student->student_number,
				'email' => $student->student_number . '@parent.local',
				'password' => bcrypt($student->student_number),
				'role' => 'parent',
				'status' => 'active',
			]);

			DB::table('students')
				->where('student_id', $data['parent_student_id'])
				->update(['student_parent' => $parent->parent_id]);

			$status = 'success';
			$message = 'Parent has been added!';
		} else {
			$status = 'error';
			$message = 'Ooopps! Something went wrong!';
		}
		return [
			'status' => $status,
			'message' => $message
		];
	}
}
