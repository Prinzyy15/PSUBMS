<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
    protected $table = 'courses';   

    public function new($data)
    {    	
    	$course = new courses;
		$course->course_name = $data['course_name'];
		$course->course_desc = $data['course_desc'];
		$course->course_status = $data['course_status'];

		if($course->save())
		{
			$status = 'success';
			$message = ' Course has been added!';
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
