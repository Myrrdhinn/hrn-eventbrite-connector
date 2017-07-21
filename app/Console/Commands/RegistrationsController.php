<?php 
namespace App\Console\Commands;
use \App\Attendees;
use \App\Questions;
use \App\SalesTeam;
use Carbon\Carbon;
use Forrest;
use Illuminate\Notifications\Notifiable;
use App\Notifications\Refund; 
use App\Notifications\OrderUpdates; 

class RegistrationsController {
	
    public function index(){
    	$mailArray = [];
    	$attendeeID = [];

    	//subMinutes(5)
		$attendeeList = Attendees::where('updated_at', '>=', Carbon::now()->subMinutes(10))->with('Questions')
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
			//'OwnerId' => '005D0000002fz5j',
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
			'LeadSource' => $this->getSource($attendees),
			'Hidden_Source__c' => $this->getSource($attendees)
	];				


				//send the data to pardot
				//$this->sendData($data);
Forrest::sobjects('Lead/Eventbrite_Attendee_ID__c/'.$attendees->user_id,[
    'method' => 'PATCH',
    'body'   => $data]);

	
		if ( $attendees->refunded != "FALSE"){
				
			$Owner_RAW = Forrest::query("SELECT OwnerId FROM Lead WHERE Eventbrite_Attendee_ID__c='".$attendees->user_id."'");
			if (isset($Owner_RAW['records'][0]['OwnerId'])){
				  $owner = $Owner_RAW['records'][0]['OwnerId'];
				  $this->notification($attendees, $owner);
			}else {
				$Owner_Cont = Forrest::query("SELECT OwnerId FROM Contact WHERE Eventbrite_Attendee_ID__c='".$attendees->user_id."'");
				if (isset($Owner_Cont['records'][0]['OwnerId'])){
				  $owner = $Owner_Cont['records'][0]['OwnerId'];
				  $this->notification($attendees, $owner);
				}
			}			
			
	
	}	

			
		}

		//return $attendeeList;

		//config('eventbrite.questions')

		$this->emailUpdate($attendeeList);
		
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

public function notification($data, $owner){


		//\Notification::send(SalesTeam::where('sales_userid', '=', $owner)->get(), new Refund($data));
		

}

public function emailUpdate($attendeeList){

if (isset($attendeeList[0])){

$AttendeeData = [];

	foreach ($attendeeList as $key => $att) {
		$TempContainer = [];

		$TempAttendee = Attendees::where('user_id', '=', $att->user_id)
		->orderBy('updated_at', 'DESC')
		->limit(2)
		->get();

		$TempContainer = new \stdClass;
		if($TempAttendee[0]->payload_mode != 'attendee.updated_misc'){
			//Make sure we only send email notification if something significant happens like a name or phone update
			//If it just a question update, then no one cares :P
			
				$TempContainer->updated = $TempAttendee[0];

				if (isset($TempAttendee[1])){
					$TempContainer->original = $TempAttendee[0];
				}



				$Id_RAW = Forrest::query("SELECT Id FROM Lead WHERE Eventbrite_Attendee_ID__c='".$att->user_id."'");
				if (isset($Id_RAW['records'][0]['Id'])){
					  $Id = $Id_RAW['records'][0]['Id'];
					  $TempContainer->salesforce = $Id;
				}

				
				array_push($AttendeeData, $TempContainer);

		}


	}

	if ($AttendeeData){
	
		$attmodel = \App\Attendees::first();

		$attmodel->notify(new OrderUpdates($AttendeeData));
	}


} 
	

}

}