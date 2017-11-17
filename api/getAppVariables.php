<?php
require 'connect.php';
$data = $_GET;
$error=0;
global $wpdb;
if(empty($data['userID'])){
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
    $loggedInUser=AuthUser($data['userID'],array());
    if(!empty($data['role'])){
        $checkEmployer=isEmployer($data['userID']);
        if(empty($checkEmployer)){
              response(0,array(),'You are not employer yet.');  
        }  
    }
    $variables=array();
    $query='select * from `nat_variables` where `userID` in("'.$data['userID'].'","0") and `role` in("'.$data['role'].'","2")';
    $getPredefined=$wpdb->get_results($query);
    if(!empty($getPredefined)){
         $getPredefined=convert_array($getPredefined);
         $countResults=0;
         foreach($getPredefined as $k=>$v){
             if($v['predefined']=='true'){
                $predefine='1'; 
             }else{
                $predefine='0';  
             }
            $variables[$countResults]['variableID']=$v['id'];   
            $variables[$countResults]['variableName']=$v['variableName'];
            $variables[$countResults]['isAppVariable']=$predefine;
            $getVariableSelected=checkIsSelected($v['id'],$data['userID'],$data['role']);
            $variables[$countResults]['variableSelected']="$getVariableSelected";   
            $countResults++;                
         }   
    }
    if(!empty($variables)){
        response(1,$variables,'No Error Found.');  
    }else{
        response(0,array(),'No Variables found.');  
    }     
}else{
  response(0,array(),'Please enter the required fields.');  
}
?>