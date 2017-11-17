<?php
require 'connect.php';
$encoded_data = file_get_contents('php://input');
$data = $_GET;
$error=0;
if(empty($data['userID'])){
    $error=1;
}
if(empty($error)){
  $loggedInUser=AuthUser($data['userID'],array());
  $checkEmployer=isEmployer($data['userID']);
  if(empty($checkEmployer)){
    response(0,array(),'You are not employer.');  
  }
  $employeeList=getEmployeeList($data['userID']);
  $employee=array();
  if(!empty($employeeList)){
      $employeeList=convert_array($employeeList);
      foreach($employeeList as $key=>$val){
        $userDetail = convert_array(get_user_by('id',$val['employee_Id']));
        $image = get_user_meta($val['employee_Id'], 'user_image', true);
        $user_image =DEFAULT_IMAGE;
        if (!empty($image)) {
          $user_image = get_post_field('guid',$image);
        }
        $employee[$key]['employeeID']=$val['employee_Id']; 
        $employee[$key]['employeeName']=$userDetail['data']['display_name']; 
        $employee[$key]['employeeProfile']=$user_image;        
      }
    response(1,$employee,'No Error Found.'); 
  }else{
    response(0,array(),'No Employee Found.');    
  }
 }else{
    response(0,array(),'Please enter the required fields.');  
}
?>