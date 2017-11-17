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
    if(!empty($data['userID']) and !empty($data['employeeID'])){
        $checkEmp=isEmployer($data['userID']);
        if(empty($checkEmp)){
           response(0,array(),'you are not employer.');    
        }
        $query='select * from `nat_variables_values` where (`senderId`="'.$data['userID'].'" and `receiverId`="'.$data['employeeID'].'" and `receiverId`!="0") or (`receiverId`="'.$data['userID'].'" and `senderId`="'.$data['employeeID'].'" and `receiverId`!="0") order by id asc';   
        $resultsGet=$wpdb->get_results($query);
        $getAllVar=array();
        $getAllVariables='';
        $variables=array();
        if(!empty($resultsGet)){
            $resultsGet=convert_array($resultsGet);
            if(!empty($resultsGet)){
              foreach($resultsGet as $k=>$v){
                   $qry='select * from `nat_variables_values` where `variableID`="'.$v['variableID'].'" and ((`senderId`="'.$data['userID'].'" and  `receiverId`="'.$data['employeeID'].'") or (`receiverId`="'.$data['userID'].'" and  `senderId`="'.$data['employeeID'].'")) order by id asc';
                   $res=$wpdb->get_row($qry);
                   $res=convert_array($res);
                   if($res['senderId']==$data['userID']){
                     $getAllVar[]=$res['id'];  
                   }
                }
                if(!empty($getAllVar)){
                    $getAllVar=array_unique($getAllVar);
                    
                }
               $getAllVariables=implode('","',$getAllVar);
            } 
            if(!empty($getAllVariables)){
                $querySelect='select * from `nat_variables_values` where id in("'.$getAllVariables.'")';
                $resultsGetArray=$wpdb->get_results($querySelect);
                $countVariables=0;
                if(!empty($resultsGetArray)){
                    $resultsGetArray=convert_array($resultsGetArray);
                    foreach($resultsGetArray as $key=>$val){
                         $variableName=getVarName($val['variableID']);
                         $variables[$countVariables]['variableID']=$val['variableID'];   
                         $variables[$countVariables]['variableName']=$variableName; 
                         if(!empty($val['variableAmount'])){
                            $variables[$countVariables]['price']=$val['variableAmount']; 
                         }else{
                            $variables[$countVariables]['price']='0'; 
                         }
                         $countVariables++;
                    }  
                }
                response(1,$variables,'No Error Found.'); 
            }
            else{
                response(0,array(),'No selected variables.');   
            }
        }
        else{
              response(0,array(),'No selected variables.');  
        }     
        
    }else{
        $query='select * from `nat_variables_values` where `senderId`="'.$data['userID'].'" and `receiverId`!="0" and `role`="0" order by id asc';
        $resultsGet=$wpdb->get_results($query);
        $getAllVar=array();
        $getAllVariables='';
        $variables=array();
        if(!empty($resultsGet)){
            $resultsGet=convert_array($resultsGet);
            if(!empty($resultsGet)){
               foreach($resultsGet as $k=>$v){
                   $qry='select * from `nat_variables_values` where `variableID`="'.$v['variableID'].'" and (`senderId`="'.$v['senderId'].'" or `receiverId`="'.$v['senderId'].'" and `receiverId`!="0") order by id asc';
                   $res=$wpdb->get_row($qry);
                   $res=convert_array($res);
                   if($res['senderId']==$data['userID']){
                     $getAllVar[]=$res['id'];  
                   }
                }
               $getAllVariables=implode('","',$getAllVar);
            } 
            if(!empty($getAllVariables)){
                $querySelect='select * from `nat_variables_values` where id in("'.$getAllVariables.'")';
                $resultsGetArray=$wpdb->get_results($querySelect);
                $countVariables=0;
                if(!empty($resultsGetArray)){
                    $resultsGetArray=convert_array($resultsGetArray);
                    foreach($resultsGetArray as $key=>$val){
                         $variableName=getVarName($val['variableID']);
                         $variables[$countVariables]['variableID']=$val['variableID'];   
                         $variables[$countVariables]['variableName']=$variableName; 
                         if(!empty($val['variableAmount'])){
                            $variables[$countVariables]['price']=$val['variableAmount']; 
                         }else{
                            $variables[$countVariables]['price']='0'; 
                         }
                         $countVariables++;
                    }  
                }
                response(1,$variables,'No Error Found.'); 
            }
            else{
                response(0,array(),'No selected variables.');   
            }
        }
        else{
              response(0,array(),'No selected variables.');  
        }
     }    
}else{
  response(0,array(),'Please enter the required fields.');  
}
?>