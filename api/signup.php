<?php
require 'connect.php';
$encoded_data = file_get_contents('php://input');
$data = json_decode($encoded_data,true);
global $wpdb;
$error=0;
if(empty($data['name'])){
    $error=1;
}
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
    $emailStatus=get_user_by('email',$data['email']);
    if(!empty($emailStatus)){
      response(0,null,'Email already exists.');  
    }
    $username=$data['name'].'_'.generateRandomString(4);
    $user_id = wp_create_user($username, $data['password'], $data['email']);
    wp_update_user(array(
        'ID' => $user_id, 
        'display_name' => $data['name'],
        'user_nicename' => $data['name'],
    ));
    add_user_meta($user_id,'tokenID',$data['tokenID']);
    add_user_meta($user_id,'deviceType',$data['deviceType']);
    add_user_meta($user_id, 'user_image', '');
    add_user_meta($user_id, 'is_employee',1);
    add_user_meta($user_id, 'is_employer',0);
    add_user_meta($user_id, 'is_enable_notification',1);
    $getUserRecord=$wpdb->get_row('select * from `nat_pending_employers` where `email`="'.$data['email'].'"');
    if(!empty($getUserRecord)){
        $getUserRecord=convert_array($getUserRecord);
        update_user_meta($user_id,'is_employer',1);
        $getEmpRecord = $wpdb->get_row('select * from `nat_emp_relationships` where `employee_Id`="'.$getUserRecord['userID'].'"');
        if(!empty($getEmpRecord)){
            $getEmpRecord=convert_array($getEmpRecord);
            $wpdb->query('update `nat_emp_relationships` set `employer_Id`="'.$user_id.'" where `employee_Id`="'.$getUserRecord['userID'].'"');
        }else{
            $wpdb->query('insert into `nat_emp_relationships`(`employer_Id`,`employee_Id`) values("'.$user_id.'","'.$getUserRecord['userID'].'")');  
        }
        $wpdb->query('delete from `nat_pending_employers` where `email`="'.$data['email'].'"');        
        $wpdb->query('update `nat_variables_values` set `receiverId`="'.$user_id.'" where `senderId`="'.$getUserRecord['userID'].'" and `receiverId`="-1"');        
    }
    $response=array(
      'userID'=>"$user_id",
      'name'=>$data['name'],
      'isPushNotificationsEnabled'=>isPushNotificationsEnabled($user_id),
    );    
    response(1,$response,'Login success');   
}else{
  response(0,null,'Please enter the required fields.');  
}
?>