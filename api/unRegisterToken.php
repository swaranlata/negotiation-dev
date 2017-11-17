<?php
require 'connect.php';
$encoded_data = file_get_contents('php://input');
$data = json_decode($encoded_data,true);
$error=0;
if(empty($data['userID'])){
    $error=1;
}
if(!empty($error)){
   response(0,null,'Please enter the required fields.'); 
}else{
  $loggedInUser=AuthUser($data['userID'],'string');
  update_user_meta($data['userID'],'tokenID','');   
  response(1,"Token Unregister successfully.",'No Error Found');      
}
?>