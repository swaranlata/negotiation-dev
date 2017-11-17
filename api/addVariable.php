<?php
require 'connect.php';
$encoded_data = file_get_contents('php://input');
$data = json_decode($encoded_data,true);
$error=0;
global $wpdb;
if(empty($data['userID'])){
    $error=1;
}
if(empty($data['variableName'])){
    $error=1;
}
$roleArray=array(0,1);
if($data['role']!=''){
   if(!in_array($data['role'],$roleArray)){
      $error=1;  
   } 
}else{
    $error=1; 
}
if(empty($error)){
    $loggedInUser=AuthUser($data['userID'],'string');
    if(!empty($data['role'])){
        $checkEmployer=isEmployer($data['userID']);
        if(empty($checkEmployer)){
              response(0,null,'You are not employer yet.');  
        }  
    }
    $getAllVariable=getAllVariableAccRole($data['userID'],$data['role']);
    if(in_array(strtolower(trim($data['variableName'])),$getAllVariable)){
       response(0,null,'Variable name already exists.');    
    }    
    $var=$wpdb->query('insert into `nat_variables`(`variableName`,`predefined`,`userID`,`role`)  values("'.$data['variableName'].'","false","'.$data['userID'].'","'.$data['role'].'")');
    $varId=$wpdb->insert_id;
   /* if(empty($data['role'])){
      $fieldName='employeeId';  
    }else{
      $fieldName='employerId';
    }
     $queryInsert='insert into `nat_variables_values` (`senderId`,`receiverId`,`variableID`,`isNegotiate`,`variableAmount`,`role`) values("'.$data['userID'].'","","'.$varId.'","0","0","'.$data['role'].'")';
    $var=$wpdb->query($queryInsert); */
    $result['variableID']="$varId";
    $result['variableName']=$data['variableName'];
    $result['variableSelected']="0";
    $result['isAppVariable']="0";
    response(1,$result,'No Error Found.');   
}else{
  response(0,null,'Please enter the required fields.');  
}
?>