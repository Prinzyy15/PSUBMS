<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'contact_us';   

    public function new($data)
    {    	
    	$message = new Message;
		$message->cu_name = $data['cu_name'];
		$message->cu_content = $data['cu_content'];
		$message->cu_status = 'active';

		if($message->save())
		{
			$status = 'success';
			$message = 'message has been added!';
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
