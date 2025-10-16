<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ViolationType extends Model
{
    protected $table = 'violation_type';

    public function new($data)
    {    	
    	$vt = new ViolationType;
		$vt->vt_label = $data['vt_label'];
		$vt->vt_code = $data['vt_code'];
		$vt->vt_desc = $data['vt_desc'];

		if($vt->save())
		{
			$status = 'success';
			$message = ' Violation has been added!';
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
