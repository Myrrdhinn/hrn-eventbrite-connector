<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Attendees;
use \App\Questions;
use Carbon\Carbon; 

class TestController extends Controller
{
  public function index(){
    	$mailArray = [];
    	$attendeeID = [];

    	//subMinutes(5)
		$attendeeList = Attendees::where('updated_at', '>=', Carbon::now()->subMinutes(10))->with('Questions')
		->orderBy('updated_at', 'DESC')
		->get();
			
			dd($attendeeList);

		foreach ($attendeeList as $key => $attendees) {

		if(!in_array($attendees->user_id, $attendeeID)){

				if(in_array($attendees->email, $mailArray)){

						$email = $attendees->fake_email;
				} else {

						$email = $attendees->email;
				}

			$data = array('369871_2761pi_369871_2761' => $email, //email
						  '369871_2763pi_369871_2763' => $attendees->email, //original email
			              '369871_2755pi_369871_2755' => $attendees->first_name, //first name
			              '369871_2757pi_369871_2757' => $attendees->last_name, //last name
			              '369871_2759pi_369871_2759' => $attendees->company, //company
			              '369871_2771pi_369871_2771' => $attendees->job_title, //Job Title
			              '369871_2769pi_369871_2769' => $attendees->work_phone, //Phone
			              '369871_2783pi_369871_2783' => $this->QuestionHandler($attendees, 'category'), //registrant category
			              '369871_2803pi_369871_2803' => $this->QuestionHandler($attendees, 'employeenum'), //employee num
			              '369871_2791pi_369871_2791' => $this->QuestionHandler($attendees, 'budget'), //revenue
			              //'145751_15912pi_145751_15912' => $this->QuestionHandler($attendees, 'industry'), //industry
			              '369871_2789pi_369871_2789' => $this->QuestionHandler($attendees, 'corevendors'), //core vendors
			              '369871_2797pi_369871_2797' => $this->QuestionHandler($attendees, 'solutions'), //solutions
			              '369871_2801pi_369871_2801' => $this->getSource($attendees), //Lead Source
			              '369871_2785pi_369871_2785' => $attendees->ticket_type, //Ticket Type
			              '369871_2781pi_369871_2781' => $attendees->attendee_status, //Status
			              '369871_2795pi_369871_2795' => $this->QuestionHandler($attendees, 'finance'), //Person authorized to sign budget
			              '369871_2793pi_369871_2793' => $this->QuestionHandler($attendees, 'boss'), //Boss job title
			              '369871_2799pi_369871_2799' => $this->QuestionHandler($attendees, 'interests'), //key investments
			              '369871_2787pi_369871_2787' => $attendees->discount_code, //key investments
			              
			              
			             );		

				array_push($mailArray, $attendees->email);
				array_push($attendeeID, $attendees->user_id);
				
				//send the data to pardot
				$this->sendData($data);

			}
		}

		//return $attendeeList;

		//config('eventbrite.questions')
		
    }

   protected function QuestionHandler($attendees, $type){

     	foreach ($attendees->questions as $questions){
   		 	foreach (config(`eventbrite.questions.{{$type}}`) as $data){
        
            	if($questions->question_id == $data){
            	  return  $questions->answer; 
            	}
        	}

    	}      
	}

   protected function getSource($attendees){

    
   		 foreach (config('eventbrite.events') as $data){
        
            if($attendees->event_id == $data['code']){
              return  $data['source']; 
            }
        }     
	}	


	protected function sendData($data){
		$url = 'http://go.pardot.com/l/369871/2017-06-02/lk8';
		
		// use key 'http' even if you send the request to https://...
		$options = array(
		    'http' => array(
		        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		        'method'  => 'POST',
		        'content' => http_build_query($data)
		    )
		);
		$context  = stream_context_create($options);
		 
		$result = file_get_contents($url, false, $context);
		if ($result === FALSE) { /* Handle error */ }
		    
		    
		

	} 


}
