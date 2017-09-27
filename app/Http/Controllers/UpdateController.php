<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Forrest;
use Carbon\Carbon;

class UpdateController extends Controller
{
    public function index(Request $request){
		$updatedContent = '';
		
		$data = $request->all();
		foreach($data['new'] as $dataIndex => $dataNew){
		
				if (isset($dataNew) && isset($data['old'][$dataIndex])){
					
							$leadId = $dataNew['Id'];
							
							foreach($dataNew as $newId => $newVal){
									if (isset($data['old'][$dataIndex][$newId]) && ($data['old'][$dataIndex][$newId] != $newVal) && $newId != 'EventbriteSync__UniquePersonId__c' && $newId != 'Robot_Updates__c' && $newId != 'LastModifiedDate' && $newId != 'SystemModstamp' && $newId != 'Lead_Quality__c' && $newId != 'IS__c' && $newId != 'LeadScoringVisualisation__c' && $newId != 'LeadScoring__c' && $newId != 'pi__score__c' && $newId != 'pi__first_activity__c' && $newId != 'pi__last_activity__c' && $newId != 'pi__Pardot_Last_Scored_At__c' && $newId != 'pi__first_touch_url__c' && $newId != 'LastModifiedById' ){
										$updatedContent .= $newId.': [ '.$data['old'][$dataIndex][$newId].' ] - '.Carbon::now('Europe/Budapest')."\r\n";
									}
							}

							if (isset($dataNew['Robot_Updates__c'])){
								$robotData = $updatedContent.$dataNew['Robot_Updates__c'];
								
							} else {
								$robotData = $updatedContent;
							}
						
						if ($updatedContent){
								$sendData = [
										'Robot_Updates__c' => $robotData, //email

								];	

								Forrest::sobjects('Lead/Id/'.$leadId,[
									'method' => 'PATCH',
									'body'   => $sendData]);	
							
						}
								
					
				}		
		
		
		
		}
		



	
		
	
	}
	
  public function contact(Request $request){
  
  		$updatedContent = '';
		
		$data = $request->all();
		foreach($data['new'] as $dataIndex => $dataNew){
		
				if (isset($dataNew) && isset($data['old'][$dataIndex])){
					
							$contactId = $dataNew['Id'];
							
							foreach($dataNew as $newId => $newVal){
									if (isset($data['old'][$dataIndex][$newId]) && ($data['old'][$dataIndex][$newId] != $newVal) && $newId != 'EventbriteSync__UniquePersonId__c' && $newId != 'Robot_Updates__c' && $newId != 'LastModifiedDate' && $newId != 'SystemModstamp' && $newId != 'Lead_Quality__c' && $newId != 'IS__c' && $newId != 'LeadScoringVisualisation__c' && $newId != 'LeadScoring__c' && $newId != 'pi__score__c' && $newId != 'pi__first_activity__c' && $newId != 'pi__last_activity__c' && $newId != 'pi__Pardot_Last_Scored_At__c' && $newId != 'pi__first_touch_url__c' && $newId != 'LastModifiedById' ){
										$updatedContent .= $newId.': [ '.$data['old'][$dataIndex][$newId].' ] - '.Carbon::now('Europe/Budapest')."\r\n";
									}
							}

							if (isset($dataNew['Robot_Updates__c'])){
								$robotData = $updatedContent.$dataNew['Robot_Updates__c'];
								
							} else {
								$robotData = $updatedContent;
							}
						
						if ($updatedContent){
								$sendData = [
										'Robot_Updates__c' => $robotData, //email

								];	

								Forrest::sobjects('Contact/Id/'.$contactId,[
									'method' => 'PATCH',
									'body'   => $sendData]);	
							
						}
								
					
				}		
		
		
		
		}
		

  
  }
	
}



		
	
		
		
		
