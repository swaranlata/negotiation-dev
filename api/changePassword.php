<?php
require 'connect.php';
$encoded_data = file_get_contents('php://input');
$data = json_decode($encoded_data,true);
$error=0;
if(empty($data['userID'])){
    $error=1;
}
if(empty($data['oldPassword'])){
    $error=1;
}
if(empty($data['newPassword'])){
    $error=1;
}
if(!empty($error)){
   response(0,null,'Please enter the required fields.'); 
}else{
    $loggedInUser=AuthUser($data['userID'],'string');
    $loggedInUser=convert_array($loggedInUser);
    $checkOldPassword= wp_check_password($data['oldPassword'],$loggedInUser['data']['user_pass'],$data['userID']);
    if(!empty($checkOldPassword)){
       wp_set_password($data['newPassword'],$data['userID']); 
       response(1,'Password changed successfully.','No Error Found.');    
    }else{
      response(0,null,'You have entered incorrect old Password.');    
    }
 }
?>