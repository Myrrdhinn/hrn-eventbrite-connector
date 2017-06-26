<?php

namespace App\Http\Controllers;

use \App\Attendees;
use Carbon\Carbon; 
use Illuminate\Http\Request;

class ListController extends Controller
{
    public function index(){
    /* $usercount = Attendees::selectRaw('COUNT(user_id) as usercount')
        ->groupBy('user_id')
		->orderBy('updated_at', 'DESC')
		->get();*/

		//$attendeeList = Attendees::whereRaw('id IN (select MAX(id) FROM attendees GROUP BY user_id)')->get();

$attendeeList = \DB::select('SELECT *, COUNT(user_id) as usercount FROM ( SELECT * FROM attendees ORDER BY created_at DESC) AS h GROUP BY user_id ORDER BY created_at DESC');

    	return view('bootstrap_tables', ['data' => $attendeeList]);
    }
}
