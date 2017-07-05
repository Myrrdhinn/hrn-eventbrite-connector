<?php 
namespace App\Console\Commands;
use \App\Attendees;
use \App\Questions;
use Carbon\Carbon;
use Forrest; 

class RegistrationsController {
	
    public function index(){
    	$mailArray = [];
    	$attendeeID = [];

    	//subMinutes(5)
		$attendeeList = Attendees::where('updated_at', '>=', Carbon::now()->subHours(5))->with('Questions')
		->orderBy('updated_at', 'DESC')
		->get();


		foreach ($attendeeList as $key => $attendees) {


				
    $data = [
    		'Email' => $attendees->email, //email
			'Phone' => $attendees->work_phone, //Genereal Phone
			'FirstName' => $attendees->first_name, //First Name
			'LastName' => $attendees->last_name, //Last Name
			'Website' => '.', //Website
			'Company' => $attendees->company, //Company
			//'NumberOfEmployees' => $this->QuestionHandler($attendees, 'employeenum'), //Employee num
			'Title' => $attendees->job_title, //Job title
			'Industry' => $this->QuestionHandler($attendees, 'industry'), //Industry
			'CurrencyIsoCode' => 'EUR', //Currency 
			'OwnerId' => '005D0000002fz5j',
			'Eventbrite_Order_Id__c' => $attendees->order_id, //Order Id		
			'City' => $attendees->city, //City
			"CountryCode" => $attendees->country,
			"Street" => $attendees->address,
			"PostalCode" => $attendees->postal_code,
			'Eventbrite_Order_Status__c' => $attendees->attendee_status, //Attendee status	
			'Ticket_Type__c' => $attendees->ticket_type, //Ticket Type
			'discount_code__c' => $attendees->discount_code, //Discount code
			//'Lead_Type__c' => $this->QuestionHandler($attendees, 'attendee_category'),
			'Core_Vendors__c' => $this->QuestionHandler($attendees, 'corevendors'),
			'Interested_Solutions__c' => $this->QuestionHandler($attendees, 'solutions'),
			'authorized_person_budget__c' => $this->QuestionHandler($attendees, 'finance'),
			'job_title_of_direct_boss__c' => $this->QuestionHandler($attendees, 'boss'),
			'Key_investment_areas__c' => $this->QuestionHandler($attendees, 'interests'),
	];				


				//send the data to pardot
				//$this->sendData($data);
	Forrest::sobjects('Lead/Eventbrite_Attendee_ID__c/'.$attendees->user_id,[
    'method' => 'PATCH',
    'body'   => $data]);
	

			
		}

		//return $attendeeList;

		//config('eventbrite.questions')
		
    }

   protected function QuestionHandler($attendees, $type){
//dd(config('eventbrite.questions.'.$type));
     	foreach ($attendees->questions as $questions){
   		 	foreach (config('eventbrite.questions.'.$type) as $data){
        
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



}