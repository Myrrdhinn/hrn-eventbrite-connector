<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \GuzzleHttp\Client;
use \App\Attendees;
use \App\Questions;

class RequestController extends Controller
{
    public function index(Request $request){
    	if(isset($request)){
			$payload = $request->all();
			if(isset($payload['api_url'])){
				$client = new Client(['verify' => false]);

                //Based on the webhooks action we get data from different urls
                //But in the end, we should always get attendee data 
                switch ($payload['config']['action']) {
                    case 'order.placed':
                        $moo = $client->request('GET', $payload['api_url'].'?expand=attendees.promotional_code', [
                            'headers' => [
                                'Authorization' => 'Bearer '.config('app.eventbrite')
                            ]
                        ]);

                        break;
                     case 'attendee.updated':
                        $moo = $client->request('GET', $payload['api_url'], [
                            'headers' => [
                                'Authorization' => 'Bearer '.config('app.eventbrite')
                            ]
                        ]);

                        break; 

                    default:
                        $moo = $client->request('GET', $payload['api_url'].'?expand=attendees.promotional_code', [
                            'headers' => [
                                'Authorization' => 'Bearer '.config('app.eventbrite')
                            ]
                        ]);

                        break;
                }



				if (isset($moo)){
					$body = $moo->getBody();
					$attendee = json_decode($body);

					// $this->processData($attendee, $payload['config']['action']);
					$this->processData($attendee, $payload['config']['action']);			 
				}
					
			} else {
			  echo 'Moo!';
			}
			 

    	}
    



    }
   

protected function GeraHash($qtd){ 
	$fake = '@fake'.rand(10,1000).'.not';

//Under the string $Caracteres you write all the characters you want to be used to randomly generate the code. 
$Caracteres = 'ABCDEFGHIJKLMOPQRSTUVXWYZ0123456789'; 
$QuantidadeCaracteres = strlen($Caracteres); 
$QuantidadeCaracteres--; 

$Hash=NULL; 
    for($x=1;$x<=$qtd;$x++){ 
        $Posicao = rand(0,$QuantidadeCaracteres); 
        $Hash .= substr($Caracteres,$Posicao,1); 
    } 

return $Hash.$fake; 
}

   protected function processData($data, $mode){



				//$attendees = New Attendees;

   	    		 switch ($mode) {
    		 	case 'order.placed':
    		 		foreach ($data->attendees as $key => $attendee) {
    		 			$city = isset($attendee->profile->addresses->work->city) ? $attendee->profile->addresses->work->city : null;
    		 			$postal_code = isset($attendee->profile->addresses->work->postal_code) ? $attendee->profile->addresses->work->postal_code : null;
    		 			$address = isset($attendee->profile->addresses->work->address_1) ? $attendee->profile->addresses->work->address_1 : null;
    		 			$country = isset($attendee->profile->addresses->work->country) ? $attendee->profile->addresses->work->country : null;
    		 			$discount = isset($attendee->promotional_code->code) ? $attendee->promotional_code->code : null;
    		 			

    		 			Attendees::create([
    		 				'user_id' => $attendee->id,
    		 				'order_id' => $attendee->order_id,
    		 				'event_id' => $attendee->event_id,
    		 				'first_name' =>$attendee->profile->first_name,
    		 				'last_name' =>$attendee->profile->last_name,
    		 				'full_name' =>$attendee->profile->name,
    		 				'work_phone' =>$attendee->profile->work_phone,
    		 				'company' =>$attendee->profile->company,
    		 				'email' =>$attendee->profile->email,
    		 				'fake_email' =>$this->GeraHash(8),
    		 				'job_title' =>$attendee->profile->job_title,
    		 				'city' =>$city,
    		 				'postal_code' =>$postal_code,
    		 				'address' =>$address,
    		 				'country' =>$country,
    		 				'attendee_status' => $attendee->status,
    		 				'ticket_type' =>$attendee->ticket_class_name,
    		 				'discount_code' =>$discount,
    		 				'ticket_base_price' =>$attendee->costs->base_price->major_value, 
    		 				'ticket_eb_fee' =>$attendee->costs->eventbrite_fee->major_value,
    		 				'ticket_payment_fee' =>$attendee->costs->payment_fee->major_value,
    		 				'ticket_tax' => $attendee->costs->tax->major_value,
    		 				'ticket_gross' => $attendee->costs->gross->major_value,
                            'payload_mode' => $mode
    		 				]);


                        foreach ($attendee->answers as $akey => $questions) {
                           if(isset($questions->answer)){
                            Questions::create([
                                    'attendee_id' => $attendee->id,
                                    'event_id' => $attendee->event_id,
                                    'question_id' => $questions->question_id,
                                    'answer' => $questions->answer,
                                ]);

                           }
                        }
    		 			
    		 		}
    		 		

    		 		break;
      		 	case 'order.refunded':
    		 		//$attendees->refund($data);

                    Attendees::where('user_id', $data->id)
                        ->orderBy('updated_at', 'desc')
                        ->update([
                        'refunded' =>'TRUE',
                        'attendee_status' => $data->status,
                        'payload_mode' => $mode
                        ]);

    		 		//Esetleg notification handler ide
    		 		break;
      		 	case 'order.updated':
    		 		 //$attendees->order_update($data);
    		 		//Esetleg notification handler ide
    		 		break;     		 		  
      		 	case 'attendee.updated':
                     $attendeeOne = Attendees::where('user_id', $data->id)
                     ->orderBy('created_at','DESC')
                     ->limit(1)
					 ->get();
                     if(isset($attendeeOne[0])){
						
                        //If we already have a record we proceed, if not, there's a good chance that 
                        //This request is triggered together with the new registration hook, so we can just ignore it
                        //The second if: 
                        //If critical fields not matching, aka, the ticket owner is replaced with an another,
                        //we create a new entry to be able to show it in event log
                        //if not, we just simply update the current entry
                        if(($attendeeOne[0]['first_name'] != $data->profile->first_name) || ($attendeeOne[0]['last_name'] != $data->profile->last_name) || ($attendeeOne[0]['email'] != $data->profile->email) || ($attendeeOne[0]['attendee_status'] != $data->status)){

                                $city = isset($data->profile->addresses->work->city) ? $data->profile->addresses->work->city : null;
                                $postal_code = isset($data->profile->addresses->work->postal_code) ? $data->profile->addresses->work->postal_code : null;
                                $address = isset($data->profile->addresses->work->address_1) ? $data->profile->addresses->work->address_1 : null;
                                $country = isset($data->profile->addresses->work->country) ? $data->profile->addresses->work->country : null;
                                $discount = isset($data->promotional_code->code) ? $data->promotional_code->code : null;
                                

                                Attendees::create([
                                    'user_id' => $data->id,
                                    'order_id' => $data->order_id,
                                    'event_id' => $data->event_id,
                                    'first_name' =>$data->profile->first_name,
                                    'last_name' =>$data->profile->last_name,
                                    'full_name' =>$data->profile->name,
                                    'work_phone' =>$data->profile->work_phone,
                                    'company' =>$data->profile->company,
                                    'email' =>$data->profile->email,
                                    'fake_email' =>$attendeeOne->fake_email,
                                    'job_title' =>$data->profile->job_title,
                                    'city' =>$city,
                                    'postal_code' =>$postal_code,
                                    'address' =>$address,
                                    'country' =>$country,
                                    'attendee_status' => $data->status,
                                    'ticket_type' =>$data->ticket_class_name,
                                    'discount_code' =>$discount,
                                    'ticket_base_price' =>$data->costs->base_price->major_value, 
                                    'ticket_eb_fee' =>$data->costs->eventbrite_fee->major_value,
                                    'ticket_payment_fee' =>$data->costs->payment_fee->major_value,
                                    'ticket_tax' => $data->costs->tax->major_value,
                                    'ticket_gross' => $data->costs->gross->major_value,
                                    'refunded' => $data->refunded,
                                    'payload_mode' => $mode
                                    ]);


                        }else {

                            //if noting major changes, we just update the existing data
                                $city = isset($data->profile->addresses->work->city) ? $data->profile->addresses->work->city : null;
                                $postal_code = isset($data->profile->addresses->work->postal_code) ? $data->profile->addresses->work->postal_code : null;
                                $address = isset($data->profile->addresses->work->address_1) ? $data->profile->addresses->work->address_1 : null;
                                $country = isset($data->profile->addresses->work->country) ? $data->profile->addresses->work->country : null;
                                $discount = isset($data->promotional_code->code) ? $data->promotional_code->code : null;
                                

                                Attendees::where('user_id', $data->id)
                                    ->orderBy('created_at', 'desc')
                                    ->update([
                                    'work_phone' =>$data->profile->work_phone,
                                    'company' =>$data->profile->company,
                                    'job_title' =>$data->profile->job_title,
                                    'city' =>$city,
                                    'postal_code' =>$postal_code,
                                    'address' =>$address,
                                    'country' =>$country,
                                    'ticket_type' =>$data->ticket_class_name,
                                    'discount_code' =>$discount,
                                    'ticket_base_price' =>$data->costs->base_price->major_value, 
                                    'ticket_eb_fee' =>$data->costs->eventbrite_fee->major_value,
                                    'ticket_payment_fee' =>$data->costs->payment_fee->major_value,
                                    'ticket_tax' => $data->costs->tax->major_value,
                                    'ticket_gross' => $data->costs->gross->major_value,
                                    'payload_mode' => $mode
                                    ]);

                                    foreach ($data->answers as $akey => $questions) {
                                       if(isset($questions->answer)){
                                        Questions::where('question_id', $questions->question_id)
                                                ->where('attendee_id', $data->id)
                                                ->update([
                                                    'answer' => $questions->answer
                                            ]);

                                       }
                                    }  


                        }


                     }

    		 		break;  

    		 }

   } 
}
