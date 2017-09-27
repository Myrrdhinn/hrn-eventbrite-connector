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
use Illuminate\Support\Facades\Log; 
use GuzzleHttp\Exception\ClientException;

class RegistrationsController {
	
    public function index(){
    	$mailArray = [];
    	$attendeeID = [];

    	//subMinutes(5)
		$attendeeList = Attendees::where('updated_at', '>=', Carbon::now()->subMinutes(10))->with('Questions')
		->orderBy('updated_at', 'ASC')
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
			'DirectPhone__c' => $attendees->mobile_phone, //mobile phone			
			'City' => $attendees->city, //City
			"MailingCountry__c" => $this->code_to_country($attendees->country),
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
			'Robot_Source__c' => $this->getSource($attendees),
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
		$this->sheetUpdate($attendeeList);
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

    $firstOrderID = $attendeeList[0]->order_id; 
	
	foreach ($attendeeList as $key => $att) {
	
	//Separate Emails by order
	if ($firstOrderID != $att->order_id){
		if ($AttendeeData){
		
			$attmodel = \App\Attendees::first();

			$attmodel->notify(new OrderUpdates($AttendeeData));
			
			$AttendeeData = [];
			$firstOrderID = $att->order_id;
		}
		
	
	}
	
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
					$TempContainer->original = $TempAttendee[1];
				}

				$TempContainer->salesforce = '';

				
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

 public function code_to_country( $code ){

    $code = strtoupper($code);

 $countryList = array(
        'AF' => 'Afghanistan',
        'AX' => 'Aland Islands',
        'AL' => 'Albania',
        'DZ' => 'Algeria',
        'AS' => 'American Samoa',
        'AD' => 'Andorra',
        'AO' => 'Angola',
        'AI' => 'Anguilla',
        'AQ' => 'Antarctica',
        'AG' => 'Antigua and Barbuda',
        'AR' => 'Argentina',
        'AM' => 'Armenia',
        'AW' => 'Aruba',
        'AU' => 'Australia',
        'AT' => 'Austria',
        'AZ' => 'Azerbaijan',
        'BS' => 'Bahamas',
        'BH' => 'Bahrain',
        'BD' => 'Bangladesh',
        'BB' => 'Barbados',
        'BY' => 'Belarus',
        'BE' => 'Belgium',
        'BZ' => 'Belize',
        'BJ' => 'Benin',
        'BM' => 'Bermuda',
        'BT' => 'Bhutan',
        'BO' => 'Bolivia',
        'BA' => 'Bosnia and Herzegovina',
        'BW' => 'Botswana',
        'BV' => 'Bouvet Island (Bouvetoya)',
        'BR' => 'Brazil',
        'IO' => 'British Indian Ocean Territory (Chagos Archipelago)',
        'VG' => 'British Virgin Islands',
        'BN' => 'Brunei Darussalam',
        'BG' => 'Bulgaria',
        'BF' => 'Burkina Faso',
        'BI' => 'Burundi',
        'KH' => 'Cambodia',
        'CM' => 'Cameroon',
        'CA' => 'Canada',
        'CV' => 'Cape Verde',
        'KY' => 'Cayman Islands',
        'CF' => 'Central African Republic',
        'TD' => 'Chad',
        'CL' => 'Chile',
        'CN' => 'China',
        'CX' => 'Christmas Island',
        'CC' => 'Cocos (Keeling) Islands',
        'CO' => 'Colombia',
        'KM' => 'Comoros the',
        'CD' => 'Congo',
        'CG' => 'Congo the',
        'CK' => 'Cook Islands',
        'CR' => 'Costa Rica',
        'CI' => 'Cote d\'Ivoire',
        'HR' => 'Croatia',
        'CU' => 'Cuba',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'DJ' => 'Djibouti',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'EC' => 'Ecuador',
        'EG' => 'Egypt',
        'SV' => 'El Salvador',
        'GQ' => 'Equatorial Guinea',
        'ER' => 'Eritrea',
        'EE' => 'Estonia',
        'ET' => 'Ethiopia',
        'FO' => 'Faroe Islands',
        'FK' => 'Falkland Islands (Malvinas)',
        'FJ' => 'Fiji the Fiji Islands',
        'FI' => 'Finland',
        'FR' => 'France',
        'GF' => 'French Guiana',
        'PF' => 'French Polynesia',
        'TF' => 'French Southern Territories',
        'GA' => 'Gabon',
        'GM' => 'Gambia the',
        'GE' => 'Georgia',
        'DE' => 'Germany',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GR' => 'Greece',
        'GL' => 'Greenland',
        'GD' => 'Grenada',
        'GP' => 'Guadeloupe',
        'GU' => 'Guam',
        'GT' => 'Guatemala',
        'GG' => 'Guernsey',
        'GN' => 'Guinea',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HT' => 'Haiti',
        'HM' => 'Heard Island and McDonald Islands',
        'VA' => 'Holy See (Vatican City State)',
        'HN' => 'Honduras',
        'HK' => 'Hong Kong',
        'HU' => 'Hungary',
        'IS' => 'Iceland',
        'IN' => 'India',
        'ID' => 'Indonesia',
        'IR' => 'Iran',
        'IQ' => 'Iraq',
        'IE' => 'Ireland',
        'IM' => 'Isle of Man',
        'IL' => 'Israel',
        'IT' => 'Italy',
        'JM' => 'Jamaica',
        'JP' => 'Japan',
        'JE' => 'Jersey',
        'JO' => 'Jordan',
        'KZ' => 'Kazakhstan',
        'KE' => 'Kenya',
        'KI' => 'Kiribati',
        'KP' => 'Korea',
        'KR' => 'Korea',
        'KW' => 'Kuwait',
        'KG' => 'Kyrgyz Republic',
        'LA' => 'Lao',
        'LV' => 'Latvia',
        'LB' => 'Lebanon',
        'LS' => 'Lesotho',
        'LR' => 'Liberia',
        'LY' => 'Libyan Arab Jamahiriya',
        'LI' => 'Liechtenstein',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'MO' => 'Macao',
        'MK' => 'Macedonia',
        'MG' => 'Madagascar',
        'MW' => 'Malawi',
        'MY' => 'Malaysia',
        'MV' => 'Maldives',
        'ML' => 'Mali',
        'MT' => 'Malta',
        'MH' => 'Marshall Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MU' => 'Mauritius',
        'YT' => 'Mayotte',
        'MX' => 'Mexico',
        'FM' => 'Micronesia',
        'MD' => 'Moldova',
        'MC' => 'Monaco',
        'MN' => 'Mongolia',
        'ME' => 'Montenegro',
        'MS' => 'Montserrat',
        'MA' => 'Morocco',
        'MZ' => 'Mozambique',
        'MM' => 'Myanmar',
        'NA' => 'Namibia',
        'NR' => 'Nauru',
        'NP' => 'Nepal',
        'AN' => 'Netherlands Antilles',
        'NL' => 'Netherlands',
        'NC' => 'New Caledonia',
        'NZ' => 'New Zealand',
        'NI' => 'Nicaragua',
        'NE' => 'Niger',
        'NG' => 'Nigeria',
        'NU' => 'Niue',
        'NF' => 'Norfolk Island',
        'MP' => 'Northern Mariana Islands',
        'NO' => 'Norway',
        'OM' => 'Oman',
        'PK' => 'Pakistan',
        'PW' => 'Palau',
        'PS' => 'Palestinian Territory',
        'PA' => 'Panama',
        'PG' => 'Papua New Guinea',
        'PY' => 'Paraguay',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'PN' => 'Pitcairn Islands',
        'PL' => 'Poland',
        'PT' => 'Portugal',
        'PR' => 'Puerto Rico',
        'QA' => 'Qatar',
        'RE' => 'Reunion',
        'RO' => 'Romania',
        'RU' => 'Russia',
        'RW' => 'Rwanda',
        'BL' => 'Saint Barthelemy',
        'SH' => 'Saint Helena',
        'KN' => 'Saint Kitts and Nevis',
        'LC' => 'Saint Lucia',
        'MF' => 'Saint Martin',
        'PM' => 'Saint Pierre and Miquelon',
        'VC' => 'Saint Vincent and the Grenadines',
        'WS' => 'Samoa',
        'SM' => 'San Marino',
        'ST' => 'Sao Tome and Principe',
        'SA' => 'Saudi Arabia',
        'SN' => 'Senegal',
        'RS' => 'Serbia',
        'SC' => 'Seychelles',
        'SL' => 'Sierra Leone',
        'SG' => 'Singapore',
        'SK' => 'Slovakia (Slovak Republic)',
        'SI' => 'Slovenia',
        'SB' => 'Solomon Islands',
        'SO' => 'Somalia, Somali Republic',
        'ZA' => 'South Africa',
        'GS' => 'South Georgia and the South Sandwich Islands',
        'ES' => 'Spain',
        'LK' => 'Sri Lanka',
        'SD' => 'Sudan',
        'SR' => 'Suriname',
        'SJ' => 'Svalbard & Jan Mayen Islands',
        'SZ' => 'Swaziland',
        'SE' => 'Sweden',
        'CH' => 'Switzerland',
        'SY' => 'Syrian Arab Republic',
        'TW' => 'Taiwan',
        'TJ' => 'Tajikistan',
        'TZ' => 'Tanzania',
        'TH' => 'Thailand',
        'TL' => 'Timor-Leste',
        'TG' => 'Togo',
        'TK' => 'Tokelau',
        'TO' => 'Tonga',
        'TT' => 'Trinidad and Tobago',
        'TN' => 'Tunisia',
        'TR' => 'Turkey',
        'TM' => 'Turkmenistan',
        'TC' => 'Turks and Caicos Islands',
        'TV' => 'Tuvalu',
        'UG' => 'Uganda',
        'UA' => 'Ukraine',
        'AE' => 'United Arab Emirates',
        'GB' => 'United Kingdom',
        'US' => 'United States',
        'UM' => 'United States Minor Outlying Islands',
        'VI' => 'United States Virgin Islands',
        'UY' => 'Eastern Republic of Uruguay',
        'UZ' => 'Uzbekistan',
        'VU' => 'Vanuatu',
        'VE' => 'Venezuela',
        'VN' => 'Vietnam',
        'WF' => 'Wallis and Futuna',
        'EH' => 'Western Sahara',
        'YE' => 'Yemen',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe'
    );

    if( !$countryList[$code] ) return $code;
    else return $countryList[$code];
    }
	
public function sheetUpdate($attendeeList){
	
	include_once("GoogleUpload.php");
	include_once("GoogleUpdate.php");
}	

}