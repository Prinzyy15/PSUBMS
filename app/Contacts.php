<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contacts extends Model
{
    protected $table = 'contacts';    

    public function new($data)
    {    	
    	$contact = new Contacts;
		$contact->contact_student_id = $data['contact_student_id'];
		$contact->contact_number_label = $data['contact_number_label'];	
		$contact->contact_number = $data['contact_number'];	

		if($contact->save())
		{
			$status = 'success';
			$message = 'Contact has been added!';
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
