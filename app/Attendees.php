<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendees extends Model
{

	protected $fillable = [ 'user_id',
    		 				'order_id',
    		 				'event_id',
    		 				'first_name',
    		 				'last_name',
    		 				'full_name',
    		 				'work_phone',
    		 				'company',
    		 				'email',
    		 				'fake_email',
    		 				'job_title',
    		 				'city',
    		 				'postal_code',
    		 				'address',
    		 				'country',
    		 				'attendee_status',
    		 				'ticket_type',
    		 				'discount_code',
    		 				'ticket_base_price', 
    		 				'ticket_eb_fee',
    		 				'ticket_payment_fee',
    		 				'ticket_tax',
    		 				'ticket_gross',
							'refunded',
							'payload_mode'];
  
      public function questions()
    {
        return $this->hasMany('App\Questions', 'attendee_id', 'user_id');
        
    } 

    public function store($data){


    }

}
