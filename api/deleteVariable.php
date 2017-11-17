<?php
require 'connect.php';
$data = $_GET;
$error=0;
global $wpdb;
if(empty($data['userID'])){
    $error=1;
}
if(empty($data['variableID'])){
    $error=1;
}
$roleArray=array(0,1);
if($data['deleteStatus']!=''){
   if(!in_array($data['deleteStatus'],$roleArray)){
      $error=1;  
   } 
}else{
    $error=1; 
}
if(empty($error)){
    $loggedInUser=AuthUser($data['userID'],'string');
    $getAllVariablesGet=getAllVariablesGet();
    if(!in_array($data['variableID'],$getAllVariablesGet)){
       response(0,"0",'You have selected invalid variable to delete.');   
    }
    $getVar=$wpdb->get_row('select * from `nat_variables` where `id`="'.$data['variableID'].'" and `userID`="'.$data['userID'].'"');
    if(!empty($getVar)){
    $query='select * from `nat_variables_values` where `variableID`="'.$data['variableID'].'" and `receiverId`!="0"';
    $getVarMainTable=$wpdb->get_results($query);
    if(!empty($getVarMainTable)){
        if(empty($data['deleteStatus'])){
          response(0,"1",'Are you sure to delete the selected data?');      
        }else{
          $wpdb->query('delete from `nat_variables_values` where `variableID`="'.$data['variableID'].'" and (`senderId`="'.$data['userID'].'" or `receiverId`="'.$data['userID'].'")'); 
          $wpdb->query('delete from `nat_variables` where `id`="'.$data['variableID'].'"');  
        }                     
    }else{
       $wpdb->query('delete from `nat_variables_values` where `variableID`="'.$data['variableID'].'" and (`senderId`="'.$data['userID'].'" or `receiverId`="'.$data['userID'].'")'); 
       $wpdb->query('delete from `nat_variables` where `id`="'.$data['variableID'].'"'); 
    } 
    response(1,"Variable deleted successfully.",'No Error found.'); 
   }else{
      response(0,"0",'You are requested to delete others variable.');    
   }
}else{
  response(0,"0",'Please enter the required fields.');  
}
?>