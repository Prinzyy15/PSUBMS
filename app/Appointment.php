<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointments';   

    public function new($data)
    {    	
    	$appointment = new Appointment;
		$appointment->appointment_student_id = $data['appointment_student_id'];
		$appointment->appointment_reason = $data['appointment_reason'];
		$appointment->appointment_date = $data['appointment_date'];
		$appointment->appointment_created_by = \Auth::user()->id;
		$appointment->appointment_status = 'not_yet_done';

		if($appointment->save())
		{
			$status = 'success';
			$message = 'Appointment has been added!';
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
