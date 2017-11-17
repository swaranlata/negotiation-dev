<?php
require 'connect.php';
$encoded_data = file_get_contents('php://input');
$data = json_decode($encoded_data,true);
$error=0;
global $wpdb;
if(empty($data['userID'])){
    $error=1;
}
if(empty($data['Variables'])){
    $error=1;
}
if(empty($error)){
    $loggedInUser=AuthUser($data['userID'],'string');
    $loggedInUser=convert_array($loggedInUser); 
    $checkEmployerID=$wpdb->get_row('select * from `nat_emp_relationships` where `employee_Id`="'.$data['userID'].'"');
    if(!empty($checkEmployerID)){
        $checkEmployerID=convert_array($checkEmployerID);
        /* if(!empty($data['Variables'])){
                foreach($data['Variables'] as $key=>$val){                   updateVariableDetails($val['variableID'],$val['variableName'],$val['price'],$data['userID'],$checkEmployerID['employer_Id']);
                }   
            } 
            $message=$loggedInUser['data']['display_name']. ' requested for negotiation.';
            sendPushNotification($emailIDExist['ID'],$message,$data['userID'],1);  
            response(1,'Request Successfully Sent.','No Error Found.');  */   
        checkMyVariableList($checkEmployerID['employer_Id'],$data['userID'],$data['Variables']);
        $checkSelectedVariable=checkSelectedVariable($data['Variables'],$data['userID'],$checkEmployerID['employer_Id']); if(empty($checkSelectedVariable)){
            if(!empty($data['Variables'])){
                foreach($data['Variables'] as $key=>$val){                   updateVariableDetails($val['variableID'],$val['variableName'],$val['price'],$data['userID'],$checkEmployerID['employer_Id'],'sendRequest');
                }   
            } 
            $message=$loggedInUser['data']['display_name']. ' requested for negotiation.';
            sendPushNotification($checkEmployerID['employer_Id'],$message,$data['userID'],1);  
            response(1,'Update Request Successfully Sent.','No Error Found.');  
        }else{
            response(0,"0",'We have found some already negotiated variables.');   
        } 
       /* $getVariable=checkMyVariable($data['userID'],$checkEmployerID['employer_Id']);
        $processFurther=1;
        if(!empty($getVariable)){
            foreach($data['Variables'] as $keys=>$vals){ 
                if(!in_array($vals['variableID'],$getVariable)){
                   $processFurther=0;
                } 
            } 
        }else{
        $processFurther=0;  
        }  
        if(!empty($processFurther)){         
                                           
       }
        else{
            response(0,"0",'Please check your selected variable list.'); 
       }  */  
    }
    $checkPendingEmployerID=$wpdb->get_row('select * from `nat_pending_employers` where `userID`="'.$data['userID'].'"');
    if(!empty($checkPendingEmployerID)){
    $checkPendingEmployerID=convert_array($checkPendingEmployerID);
    checkMyVariableList('',$data['userID'],$data['Variables']);
    if(!empty($data['Variables'])){
        foreach($data['Variables'] as $key=>$val){                      updateVariableDetails($val['variableID'],$val['variableName'],$val['price'],$data['userID'],'-1','sendRequest');
        }   
    } 
    response(1,'Request Successfully Sent.','No Error Found.');  
    }
}else{
  response(0,"0",'Please enter the required fields.');  
}
?>