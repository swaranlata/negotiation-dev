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
if(empty($error)){
    $loggedInUser=AuthUser($data['userID'],'string');
    $getAllVariablesGet=getAllVariablesGet();
    if(!in_array($data['variableID'],$getAllVariablesGet)){
       response(0,null,'You have selected invalid variable to delete.');   
    }
    if(!empty($data['employeeID'])){
        $getVar=$wpdb->get_row('select * from `nat_variables` where `id`="'.$data['variableID'].'" and (`userID`="'.$data['userID'].'" or `userID`="0")');
        if(!empty($getVar)){
            $getVar=convert_array($getVar);
            $query='select * from `nat_variables_values` where `variableID`="'.$data['variableID'].'" and ((`senderId`="'.$data['userID'].'" and `receiverId`="'.$data['employeeID'].'") or (`receiverId`="'.$data['userID'].'" and `senderId`="'.$data['employeeID'].'"))';
            $getVarMainTable=$wpdb->get_results($query);
            if(!empty($getVarMainTable)){
                $wpdb->query('delete from `nat_variables_values` where `variableID`="'.$data['variableID'].'" and ((`senderId`="'.$data['userID'].'" and `receiverId`="'.$data['employeeID'].'") or (`receiverId`="'.$data['userID'].'" and `senderId`="'.$data['employeeID'].'"))');
                response(1,"Variable deleted successfully.",'No Error found.'); 
            }else{
               response(0,null,'No Variables Found.');    
            } 
         }else{
               response(0,null,'No Variables Found.');    
            } 
    }else{
         $getVar=$wpdb->get_row('select * from `nat_variables` where `id`="'.$data['variableID'].'" and (`userID`="'.$data['userID'].'" or `userID`="0")');
        if(!empty($getVar)){
        $getVar=convert_array($getVar);
        $query='select * from `nat_variables_values` where `variableID`="'.$data['variableID'].'" and (`senderId`="'.$data['userID'].'" or `receiverId`="'.$data['userID'].'")';
        $getVarMainTable=$wpdb->get_results($query);
        if(!empty($getVarMainTable)){
            $wpdb->query('delete from `nat_variables_values` where `variableID`="'.$data['variableID'].'" and (`senderId`="'.$data['userID'].'" or `receiverId`="'.$data['userID'].'")');
            response(1,"Variable deleted successfully.",'No Error found.'); 
        }else{
           response(0,null,'No Variables Found.');    
        }             
       }else{
          response(0,null,'You are requested to delete others variable.');    
       }
        
    }
   
}else{
  response(0,null,'Please enter the required fields.');  
}


?>