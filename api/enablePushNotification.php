<?php
require 'connect.php';
$data = $_GET;
$error=0;
global $wpdb;
if(empty($data['userID'])){
    $error=1;
}
$statusArray=array(0,1);
if($data['status']!=''){
   if(!in_array($data['status'],$statusArray)){
      $error=1;  
   } 
}else{
    $error=1; 
}
if(empty($error)){
    $loggedInUser=AuthUser($data['userID'],'string');
    update_user_meta($data['userID'],'is_enable_notification',$data['status']);
    response(1,"sucessfully",'No Error Found.');  
}else{
    response(0,null,'Please enter the required fields.');  
}
?>