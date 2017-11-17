<?php
require 'connect.php';
$encoded_data = file_get_contents('php://input');
$data = json_decode($encoded_data,true);
$error=0;
global $wpdb;
if(empty($data['userID'])){
    $error=1;
}
/*if(empty($data['email'])){
    $error=1;
}*/
if(empty($data['date'])){
    $error=1;
}
if(empty($data['time'])){
    $error=1;
}
if(empty($error)){
    $loggedInUser=AuthUser($data['userID'],'string');
    $loggedInUser=convert_array($loggedInUser);
    $msg=$loggedInUser['data']['display_name'];
    $getEmployer=getEmployerDetails($data['userID']);    
    if(!empty($getEmployer)){
        $emailStatus=get_user_by('id',$getEmployer);
        $emailStatus=convert_array($emailStatus);
        $checkEmployer=isEmployer($emailStatus['ID']);
        if(empty($checkEmployer)){
          response(0,null,'Email id is not related to Employer.');  
        } 
        $employeeId=$data['userID']; 
        $employerId=$emailStatus['ID']; 
        $query=$wpdb->query('insert into `nat_meetings`(`employee_id`, `employer_id`, `selected_date`, `selected_time`) values("'.$employeeId.'","'.$employerId.'","'.$data['date'].'","'.$data['time'].'")');  
        $msg='Meeting has been scheduled on '.$data['date'].' at '.$data['time'].' with '.$loggedInUser['data']['display_name'];
        sendPushNotification($emailStatus['ID'],$msg,'',1);
        $message='Hello '.$emailStatus['data']['display_name'].', <br>'.$loggedInUser['data']['display_name'].' has requested to meeting on '.$data['date'].' at '.$data['time'].'. <br> Regards, <br> Negotiation Assessment Team';
        /* mail($emailStatus['data']['user_email'],'Negotiation Assessment Tool','Meeting Schedule',$message);*/
        send_email($emailStatus['data']['user_email'],FROM_EMAIL,'Meeting Schedule',$message);
        response(1,'Meeting successfully scheduled.','No Error Found.'); 
    }else{
        response(0,null,'You have not set your employer yet.');    
    }      
}else{
  response(0,null,'Please enter the required fields.');  
}
?>
