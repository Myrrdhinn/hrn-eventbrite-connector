<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Forrest;

class Salesforce extends Controller
{
    public function index(){

    	$body = [];

/*$body = [
    'Phone' => '555-555-5555',
    'External_Id__c' => 'XYZ1234'];

Forrest::sobjects('Account',[
    'method' => 'patch',
    'body'   => $body]);
    	
    }*/

//return Forrest::query('SELECT Id FROM Account');
/*$body = [
    'Eventbrite_Attendee_ID__c' => '793682695'];

return Forrest::sobjects('Contact',[
    'method' => 'get',
    'body'   => $body]);
*/

	//return Forrest::query('SELECT Id FROM Contact WHERE Eventbrite_Attendee_ID__c=1');
		//return Forrest::resources();
			
		
		
		/*$body = [
		'q' => '793682695',
		'fields' => ['Eventbrite_Attendee_ID__c'],
		'sobjects' => [
				['name' => 'Contact']
		],
		'in' => 'ALL'
	
	];*/
/*
$ContactSearch = Forrest::parameterizedSearch([
    'method' => 'post',
    'body'   => $body]);
	
	 if (isset($ContactSearch['searchRecords']) && empty($ContactSearch['searchRecords'])){
		$body2 = [
			'Phone' => '555-555-5555',
			'FirstName' => 'SF',
			'LastName' => 'user',
			'Website' => 'www.test.com',
			'Company' => 'Moo',
			'NumberOfEmployees' => '1',
			'Email' => 'asd@asd.com',
			'Title' => 'asdasd',
			'Industry' => 'Art',
			'CurrencyIsoCode' => 'USD',
			'Eventbrite_Attendee_ID__c' => '793682695'];

		Forrest::sobjects('Lead',[
			'method' => 'patch',
			'body'   => $body2]);
	}	 
*/

	/*
    $arrayone = [
			'Phone' => '123123-123123', //Genereal Phone
			'FirstName' => 'Hihi', //First Name
			'LastName' => 'Hehe', //Last Name
			'Website' => 'www.test.com', //Website
			'Company' => 'Moo', //Company
			'NumberOfEmployees' => '1', //Employee num
			'Email' => 'haha@gfdgdfg.com', //email
			'Title' => 'asdasd', //Job title
			'Industry' => 'Art', //Industry
			'CurrencyIsoCode' => 'USD', //Currency 

			//'Owner' => '',
			'Eventbrite_Order_Id__c' => '012012012', //Order Id
						
			'City' => 'dfdfd', //City
			"CountryCode" => 'US',
			"Street" => "7 Forests Rd",
			"PostalCode" => "7011",

			'Eventbrite_Order_Status__c' => 'status', //Attendee status	

			'Ticket_Type__c' => '1', //Ticket Type
			'discount_code__c' => 'asdasd', //Discount code


	];

	$array2 = [
			'Phone' => '321', //Genereal Phone
			'FirstName' => 'mimoo', //First Name
			'LastName' => 'shiiii', //Last Name
			'Website' => 'www.test.com', //Website
			'Company' => 'Moo', //Company
			'NumberOfEmployees' => '1', //Employee num
			'Email' => 'testone@vasd.com', //email
			'Title' => 'asdasd', //Job title
			'Industry' => 'Art', //Industry
			'CurrencyIsoCode' => 'USD', //Currency 

			//'Owner' => '',
			'Eventbrite_Order_Id__c' => '012012012', //Order Id

			'City' => 'cvcv', //City
			"CountryCode" => 'US',
			"Street" => "cvcxxx",
			"PostalCode" => "7222",

			'Eventbrite_Order_Status__c' => 'status', //Attendee status	

			'Ticket_Type__c' => '1', //Ticket Type
			'discount_code__c' => 'asdasd', //Discount code
	];


//dd($moo);
Forrest::sobjects('Lead/Eventbrite_Attendee_ID__c/793682695',[
    'method' => 'PATCH',
    'body'   => $array2]);
	*/
	
return Forrest::query("SELECT OwnerID FROM Lead WHERE Eventbrite_Attendee_ID__c='810780180'");	
	
	}	
}

