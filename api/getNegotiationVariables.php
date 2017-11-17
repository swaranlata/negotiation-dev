<?php
require 'connect.php';
$data = $_GET;
$error=0;
global $wpdb;
if(empty($data['userID'])){
    $error=1;
}
if(empty($error)){
    $loggedInUser=AuthUser($data['userID'],array());  
    $variables=array();
    if(!empty($data['userID']) and !empty($data['employeeID'])){//Employer Case
        $checkEmp=isEmployer($data['userID']);
        if(empty($checkEmp)){
           response(0,array(),'you are not employer.');    
        }
        $query='select * from `nat_variables_values` where (`receiverId`="'.$data['userID'].'" and `senderId`="'.$data['employeeID'].'" and `receiverId`!="0") or (`senderId`="'.$data['userID'].'" and `receiverId`="'.$data['employeeID'].'"  and `receiverId`!="0") group by `variableID`';
         //$query='select * from `nat_variables_values` where (`receiverId`="'.$data['userID'].'" and `senderId`="'.$data['employeeID'].'" and `variableAmount`!="0" and  `variableAmount`!="") or (`senderId`="'.$data['userID'].'" and `receiverId`="'.$data['employeeID'].'" and `variableAmount`!="0" and  `variableAmount`!="") group by `variableID`';
        $results=$wpdb->get_results($query); 
        if(!empty($results)){
        $results=convert_array($results);
        $counter=0;
        foreach($results as $key=>$val){
           $temp=0;
           $variableName=getVarName($val['variableID']);
           $variables[$counter]['variableID']=$val['variableID'];   
           $variables[$counter]['variableName']=$variableName;  
           $variables[$counter]['isNegotiate']=$val['isNegotiate'];
           $queryTest='select * from `nat_variables_values` where `receiverId`="'.$data['employeeID'].'" and `senderId`="'.$data['userID'].'" and `variableID`="'.$val['variableID'].'"';
               $rows=$wpdb->get_row($queryTest); 
               if(!empty($rows)){
                   $rows=convert_array($rows);
                   $variables[$counter]['price']=$rows['variableAmount'];
               }else{
                   $variables[$counter]['price']="";
                } 
            $counter++;
         }        
     }  
    }else{//Employee Case
        $getEmployerDetails=getEmployerDetails($data['userID']);
        if(!empty($getEmployerDetails)){
           $query='select * from `nat_variables_values` where (`senderId`="'.$data['userID'].'" and `receiverId`="'.$getEmployerDetails.'" and `receiverId`!="0") or (`senderId`="'.$getEmployerDetails.'" and `receiverId`="'.$data['userID'].'" and `receiverId`!="0") group by `variableID`';
            $results=$wpdb->get_results($query); 
            if(!empty($results)){
                $results=convert_array($results);
                $counterPlus=0;
                foreach($results as $key=>$val){
                    $variableName=getVarName($val['variableID']);
                    $variables[$counterPlus]['variableID']=$val['variableID'];   
                    $variables[$counterPlus]['variableName']=$variableName;  
                    $variables[$counterPlus]['isNegotiate']=$val['isNegotiate'];
                    $queryTest='select * from `nat_variables_values` where `receiverId`="'.$getEmployerDetails.'" and `senderId`="'.$data['userID'].'" and `variableID`="'.$val['variableID'].'"';
                    $rows=$wpdb->get_row($queryTest); 
                    if(!empty($rows)){
                       $rows=convert_array($rows);
                       $variables[$counterPlus]['price']=$rows['variableAmount'];
                    }else{
                       $variables[$counterPlus]['price']="";
                    } 
                    $counterPlus++; 
                }        
            }
        }else{
          $query='select * from `nat_variables_values` where `senderId`="'.$data['userID'].'" and `receiverId`="-1" group by `variableID`';  
          $results=$wpdb->get_results($query); 
          if(!empty($results)){
            $results=convert_array($results);
            $counterPlus=0;
            foreach($results as $key=>$val){
                    $variableName=getVarName($val['variableID']);
                    $variables[$counterPlus]['variableID']=$val['variableID'];   
                    $variables[$counterPlus]['variableName']=$variableName;  
                    $variables[$counterPlus]['isNegotiate']=$val['isNegotiate'];
                    $queryTest='select * from `nat_variables_values` where `receiverId`="-1" and `senderId`="'.$data['userID'].'" and `variableID`="'.$val['variableID'].'"';
                    $rows=$wpdb->get_row($queryTest); 
                     if(!empty($rows)){
                       $rows=convert_array($rows);
                       $variables[$counterPlus]['price']=$rows['variableAmount'];
                    }else{
                       $variables[$counterPlus]['price']="";
                    } 
                    $counterPlus++; 
               }        
         }  
            
            
            
            
        }
     /* $query='select * from `nat_variables_values` where (`senderId`="'.$data['userID'].'" and `variableAmount`!="0" and  `variableAmount`!="") or (`senderId`="'.$getEmployerDetails.'" and `receiverId`="'.$data['userID'].'" and `variableAmount`!="0" and `variableAmount`!="") group by `variableID`';  */
      
    }    
    if(!empty($variables)){
        response(1,$variables,'No Error Found.');  
    }else{
        response(0,array(),'No variables to negotiate.');  
    }     
}else{
  response(0,array(),'Please enter the required fields.');  
}
?>