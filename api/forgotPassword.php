<?php
require 'connect.php';
$encoded_data = file_get_contents('php://input');
$data = json_decode($encoded_data,true);
$error=0;
if(empty($data['email'])){
    $error=1;
}
if(!empty($error)){
   response(0,null,'Please enter the required fields.'); 
}else{
    if (filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false) {
     response(0,null,'Please enter the valid email.');  
    }  
    $user=get_user_by('email',$data['email']);
    if(!empty($user)){
         $user=convert_array($user);
         $password=generateRandomString(8);
         wp_set_password($password,$user['ID']);  
         $to = $data['email'];
         $subject = 'Password Update';
         $body = 'Hello '.ucfirst($user['data']['display_name']).',<br>  Your password has been updated to '.$password.'  Login with your new password.<br> Regards,<br> Negotiation Team';
         $headers = "From: swaran.lata@imarkclients.com" . "\r\n";
         $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
         $headers .= "MIME-Version: 1.0" . "\r\n";
         /* mail($to,'Negotiation Assessment Tools',$subject,$body,$headers); */
         send_email($to,FROM_EMAIL,$subject,$body);
         response(1,'Password sent to your register email ID','No Error Found.');     
    }else{
        response(0,null,'Email is not registered with us.');    
    }
 }
?>