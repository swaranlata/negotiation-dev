<?php
require 'connect.php';
$encoded_data = file_get_contents('php://input');
$data = json_decode($encoded_data,true);
$error=0;
if(empty($data['email'])){
    $error=1;
}
if(empty($data['password'])){
    $error=1;
}
if(empty($error)){
     $data['email']=strtolower($data['email']);
     if (filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false) {
        response(0,null,'Please enter the valid email.');  
     }  
    $credentials['user_login']=$data['email'];
    $credentials['user_password']=$data['password'];
    $loginResponse=wp_signon($credentials);
    if(!empty($loginResponse)){
    $loginResponse=convert_array($loginResponse);  
        if(isset($loginResponse['ID']) and !empty($loginResponse['ID'])){
          $user_id =$loginResponse['ID'];
          $user_image = "";
          $image = get_user_meta($user_id, 'user_image', true);
          if (!empty($image)) {
             $user_image = get_post_field('guid', $image);
           }
          update_user_meta($user_id,'tokenID',$data['tokenID']);
          update_user_meta($user_id,'deviceType',$data['deviceType']);
          $response=array(
              'userID'=>"$user_id",
              'name'=>$loginResponse['data']['display_name'],
              'isPushNotificationsEnabled'=>isPushNotificationsEnabled($user_id),
              'profile'=>$user_image
          );
          response(1,$response,'Login success');  
        }else{
         response(0,null,'Invalid email and password combination.');  
        }
    }
}else{
  response(0,null,'Please enter the required fields.');  
}
?>