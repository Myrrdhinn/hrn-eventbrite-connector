<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Forrest;

class Salesforce extends Controller
{
    public function index(){

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
			
		
		
		$body = [
		'q' => '793682695',
		'fields' => ['Eventbrite_Attendee_ID__c'],
		'sobjects' => [
				['name' => 'Contact']
		],
		'in' => 'ALL'
	
	];
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
	
	
	$body = [
			'Phone' => '123123-123123',
			'FirstName' => 'Hihi',
			'LastName' => 'Hehe',
			'Website' => 'www.test.com',
			'Company' => 'Moo',
			'NumberOfEmployees' => '1',
			'Email' => 'asd@asd.com',
			'Title' => 'asdasd',
			'Industry' => 'Art',
			'CurrencyIsoCode' => 'USD',
	];
Forrest::sobjects('Lead/Eventbrite_Attendee_ID__c/793682695',[
    'method' => 'PATCH',
    'body'   => $body]);
	
	
	}	
}

