<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Questions extends Model
{
    	protected $fillable = [ 
    						'attendee_id',
    						'event_id',
    		 				'question_id',
    		 				'answer',
    		 				];
}
