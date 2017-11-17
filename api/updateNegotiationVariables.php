<?php
require 'connect.php';
$encoded_data = file_get_contents('php://input');
$data = json_decode($encoded_data,true);
$error=0;
global $wpdb;
if(empty($data['userID'])){
    $error=1;
}
if(empty($data['employeeID'])){
    $error=1;
}
if(empty($data['Variables'])){
    $error=1;
}
if(empty($error)){
     $loggedInUser=AuthUser($data['userID'],'string');
     $loggedInUser=convert_array($loggedInUser);
     $isEmployer=isEmployer($data['userID']);
     if(empty($isEmployer)){
       response(0,null,'You are not employer.');    
     }
     $getEmployerDetails=getEmployerDetails($data['employeeID']);
     if($getEmployerDetails!=$data['userID']){
       response(0,null,'You are not employer of selected employee.');     
     }
     $employerId=$data['userID'];
     $employeeID=$data['employeeID'];
     /*  $getVariable=checkMyVariable($employeeID,$employerId);*/
    /* $processFurther=1;
     if(!empty($getVariable)){
        foreach($data['Variables'] as $keys=>$vals){ 
            if(!in_array($vals['variableID'],$getVariable)){
                $processFurther=0;
            } 
        } 
     }else{
       $processFurther=0;  
     }
    if(!empty($processFurther)){ */
      /* }else{
        response(0,null,'Please check your selected variables.');   
    }   */
        checkMyVariableList($employerId,$employeeID,$data['Variables']);
        $checkSelectedVariable=checkSelectedVariable($data['Variables'],$employerId,$employeeID);
        if(!empty($checkSelectedVariable)){
             response(0,null,'Please check your variables.We have found some already selected variables.');   
        }else{
            foreach($data['Variables'] as $keys=>$vals){                                                  updateVariableDetails($vals['variableID'],$vals['variableName'],$vals['price'],$employerId,$employeeID,'updateRequest');
            } 
            $message=$loggedInUser['data']['display_name']. ' requested for negotiation.';
            sendPushNotification($employeeID,$message,$employerId,0);
         response(1,"Updated successfully.",'No error found.');  
       }
  }else{
     response(0,null,'Please enter the required fields.');  
}
?>