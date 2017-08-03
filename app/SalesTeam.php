<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class SalesTeam extends Model
{
   use Notifiable;

    //
	
	     // Specify Slack Webhook URL to route notifications to
    public function routeNotificationForSlack()
    {
         return $this->slack_webhook;
    }		 
  
}
