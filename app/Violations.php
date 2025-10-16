<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Violations extends Model
{
	protected $table = 'violations';    

	public function new($data)
	{    	
		$_status = (isset($data['violation_status']))? $data['violation_status'] : 'pending';
		$violation = new Violations;
		$violation->student_id = $data['student_id'];
		$violation->violation_type_id = $data['violation_type_id'];
		$violation->violation_remarks = $data['violation_remarks'];
		$violation->violation_status = $_status;
		$violation->violation_actions = $data['violation_actions'];
		$violation->violation_created_by = $data['violation_created_by'];

		if($violation->save())
		{
			$status = 'success';
			$message = ' New violation record has been added!';
		}
		else
		{
			$status = 'error';
			$message = 'Ooopps! Something went wrong!';
		}
		return [
			'status' => $status,
			'message' => $message
		];
	}
}
