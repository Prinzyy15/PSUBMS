<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Blocks extends Model
{
    protected $table = 'blocks';   

    public function new($data)
    {    	
    	$block = new Blocks;
		$block->block_name = ucwords($data['block_name']);
		$block->block_desc = $data['block_desc'];
		$block->block_status = $data['block_status'];

		if($block->save())
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
