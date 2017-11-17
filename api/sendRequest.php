<?php
require 'connect.php';
$encoded_data = file_get_contents('php://input');
$data = json_decode($encoded_data,true);
$error=0;
global $wpdb;
if(empty($data['userID'])){
    $error=1;
}
if(empty($data['email'])){
    $error=1;
}
if(empty($data['Variables'])){
    $error=1;
}
$updateEmployer=array(0,1);
if($data['updateEmployer']!=''){
    if(!in_array($data['updateEmployer'],$updateEmployer)){
      $error=1;   
    }
}else{
    $error=1;   
}
if(empty($error)){
    $data['email']=strtolower($data['email']);
    if(filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false) {
         response(0,"0",'Please enter the valid email.');  
    } 
    $loggedInUser=AuthUser($data['userID'],'string');
    $loggedInUser=convert_array($loggedInUser);
    $emailIDExist=get_user_by('email',strtolower($data['email']));
    if(!empty($emailIDExist)){//Yes        
        $emailIDExist=convert_array($emailIDExist);
        if($emailIDExist['ID']==$data['userID']){
            response(0,"0",'You cannot send request to yourself.');   
        }
        $checkEmployerRecord=$wpdb->get_results('select * from `nat_emp_relationships` where `employee_Id`="'.$emailIDExist['ID'].'" and  `employer_Id`="'.$data['userID'].'"');
        if(!empty($checkEmployerRecord)){
            response(0,"0",'You cant send request to your employee.');     
        }
        $userHadAlreadyEmployer=$wpdb->get_row('select * from `nat_emp_relationships` where `employee_Id`="'.$data['userID'].'"');  
        if(!empty($userHadAlreadyEmployer)){//Yes
            $userHadAlreadyEmployer=convert_array($userHadAlreadyEmployer);
            if($userHadAlreadyEmployer['employer_Id']==$emailIDExist['ID']){//Employer and Requested employer same
                if(!empty($data['updateEmployer'])){//1         
                    // echo "1";  
                        checkMyVariableList($emailIDExist['ID'],$data['userID'],$data['Variables']);
                        update_user_meta($emailIDExist['ID'],'is_employer',1);  
                        $deleteQuery='delete from `nat_variables_values` where (`senderId`="'.$data['userID'].'" and `receiverId`="'.$emailIDExist['ID'].'") or (`receiverId`="'.$data['userID'].'" and `senderId`="'.$emailIDExist['ID'].'")';
                        $wpdb->query($deleteQuery);
                        if(!empty($data['Variables'])){
                            foreach($data['Variables'] as $key=>$val){                  updateVariableDetails($val['variableID'],$val['variableName'],$val['price'],$data['userID'],$emailIDExist['ID'],'sendRequest');
                            }   
                        }
                        $message=$loggedInUser['data']['display_name']. ' requested for negotiation.';
                        sendPushNotification($emailIDExist['ID'],$message,$data['userID'],1);  
                        response(1,'Request Successfully Sent.','No Error Found.');
                   /* $getVariable=checkMyVariable($data['userID'],$emailIDExist['ID']);
                    $processFurther=1;
                    if(!empty($getVariable)){
                    foreach($data['Variables'] as $keys=>$vals){ 
                    if(!in_array($vals['variableID'],$getVariable)){
                        $processFurther=0;
                    } 
                    } 
                    }else{
                    $processFurther=0;  
                    } 
                    if(!empty($processFurther)){
                         
                        
                    }else{
                      response(0,"0",'Please check your variable list.');                           
                    }*/
                }else{//0
                    // echo "2"; 
                 response(0,"1",'Current Employer.');     
                }                  
            }else{//New Employer
               if(!empty($data['updateEmployer'])){//1
                   $checkOldEmployer=checkOldEmployer($data['userID']);
                       checkMyVariableList($emailIDExist['ID'],$data['userID'],$data['Variables']);
                       update_user_meta($emailIDExist['ID'],'is_employer',1); 
                       $wpdb->query('update `nat_emp_relationships` set `employer_Id`="'.$emailIDExist['ID'].'" where `employee_Id`="'.$data['userID'].'"'); 
                       $deleteQuery='delete from `nat_variables_values` where (`senderId`="'.$data['userID'].'" and `receiverId`="'.$checkOldEmployer.'") or (`receiverId`="'.$data['userID'].'" and `senderId`="'.$checkOldEmployer.'")';
                        $wpdb->query($deleteQuery);
                        if(!empty($data['Variables'])){
                            foreach($data['Variables'] as $key=>$val){                  updateVariableDetails($val['variableID'],$val['variableName'],$val['price'],$data['userID'],$emailIDExist['ID'],'sendRequest');
                            }   
                        }
                        $message=$loggedInUser['data']['display_name']. ' requested for negotiation.';
                        sendPushNotification($emailIDExist['ID'],$message,$data['userID'],1);  
                        response(1,'Request Successfully Sent.','No Error Found.');  
                   /* $getVariable=checkMyVariable($data['userID'],$emailIDExist['ID']);
                    $processFurther=1;
                    if(!empty($getVariable)){
                    foreach($data['Variables'] as $keys=>$vals){ 
                    if(!in_array($vals['variableID'],$getVariable)){
                        $processFurther=0;
                    } 
                    } 
                    }else{
                    $processFurther=0;  
                    } 
                    if(!empty($processFurther)){
                                               
                    }else{
                      response(0,"0",'Please check your variable list.');     
                    }*/
                  }
                else{//0
                 response(0,"1",'You have already some other Employer.');   
               }                  
            }
        }
        else{//No             
            $checkPendingEmployer=checkPendingEmployer($data['userID']);
            if(!empty($checkPendingEmployer)){
              if(!empty($data['updateEmployer'])){//1
                 // echo "4";
                $wpdb->query('delete from `nat_pending_employers` where `userID`="'.$data['userID'].'"');
                $deleteQuery='delete from `nat_variables_values` where `senderId`="'.$data['userID'].'" and `receiverId`="-1"';
                $wpdb->query($deleteQuery);
                checkMyVariableList($emailIDExist['ID'],$data['userID'],$data['Variables']);
                update_user_meta($emailIDExist['ID'],'is_employer',1); 
                $query=$wpdb->query('insert into `nat_emp_relationships`(`employee_Id`,`employer_Id`) values("'.$data['userID'].'","'.$emailIDExist['ID'].'")');  
                if(!empty($data['Variables'])){
                    foreach($data['Variables'] as $key=>$val){                  updateVariableDetails($val['variableID'],$val['variableName'],$val['price'],$data['userID'],$emailIDExist['ID'],'sendRequest');
                     }   
                }
                $message=$loggedInUser['data']['display_name']. ' requested for negotiation.';
                sendPushNotification($emailIDExist['ID'],$message,$data['userID'],1);  
                response(1,'Request Successfully Sent.','No Error Found.');  
              }else{//0
                 // echo "5";
                response(0,"1",'You have already requested some other Employer.');    
              }                  
            }else{
              //  echo "6";
                checkMyVariableList($emailIDExist['ID'],$data['userID'],$data['Variables']);
                update_user_meta($emailIDExist['ID'],'is_employer',1); 
                $query=$wpdb->query('insert into `nat_emp_relationships`(`employee_Id`,`employer_Id`) values("'.$data['userID'].'","'.$emailIDExist['ID'].'")');  
                if(!empty($data['Variables'])){
                    foreach($data['Variables'] as $key=>$val){                  updateVariableDetails($val['variableID'],$val['variableName'],$val['price'],$data['userID'],$emailIDExist['ID'],'sendRequest');
                    }   
                }
                $message=$loggedInUser['data']['display_name']. ' requested for negotiation.';
                sendPushNotification($emailIDExist['ID'],$message,$data['userID'],1);  
                response(1,'Request Successfully Sent.','No Error Found.');
           }             
        }        
    }else{//No  
        //echo "no emal extsts";
        $userHadAlreadyEmployer=$wpdb->get_row('select * from `nat_emp_relationships` where `employee_Id`="'.$data['userID'].'"');
        if(!empty($userHadAlreadyEmployer)){
           $userHadAlreadyEmployer=convert_array($userHadAlreadyEmployer);  
           if(!empty($data['updateEmployer'])){//1
               //echo "1";
               checkMyVariableList('',$data['userID'],$data['Variables']);
               $checkOldEmployer=checkOldEmployer($data['userID']);
               $addEmployeeRequest=addEmployeeRequest($data['userID'],$data['email']); 
               $wpdb->query('delete from `nat_emp_relationships` where `employee_Id`="'.$data['userID'].'"'); 
               $deleteQuery='delete from `nat_variables_values` where (`senderId`="'.$data['userID'].'" and `receiverId`="'.$checkOldEmployer.'") or (`receiverId`="'.$data['userID'].'" and `senderId`="'.$checkOldEmployer.'")';
                $wpdb->query($deleteQuery);
                if(!empty($data['Variables'])){
                foreach($data['Variables'] as $key=>$val){                        updateVariableDetails($val['variableID'],$val['variableName'],$val['price'],$data['userID'],'-1','sendRequest');
                }   
                }
                $textMessage='Hello,<br> you are invited by '.$loggedInUser['data']['display_name'].' to join the Negotiation Assessment Tools App.<br>Regards,<br>Negotiation Assessment Team';
                send_email($data['email'],FROM_EMAIL,'Invitation from Negotiation Assessment Tools App',$textMessage);
                response(1,"Request Successfully Sent.",'No Email Found.');                
           }else{//0
              // echo "0";
             response(0,"1",'You have already some other Employer.');  
           }           
        }
        else{
           /* When Email id not exists in database*/
            $userHadAlreadyRequestedEmail=$wpdb->get_row('select * from `nat_pending_employers` where `userID`="'.$data['userID'].'"');           
            if(!empty($userHadAlreadyRequestedEmail)){
                if(!empty($data['updateEmployer'])){//1
                        checkMyVariableList('',$data['userID'],$data['Variables']);
                        $deleteQuery='delete from `nat_variables_values` where `senderId`="'.$data['userID'].'"';
                        $wpdb->query($deleteQuery);
                        $addEmployeeRequest=addEmployeeRequest($data['userID'],$data['email']);  
                        if(!empty($data['Variables'])){
                        foreach($data['Variables'] as $key=>$val){                        updateVariableDetails($val['variableID'],$val['variableName'],$val['price'],$data['userID'],'-1','sendRequest');
                        }   
                      }
                    $textMessage='Hello,<br> you are invited by '.$loggedInUser['data']['display_name'].' to join the Negotiation Assessment Tools App.<br>Regards,<br>Negotiation Assessment Team';
                    send_email($data['email'],FROM_EMAIL,'Invitation from Negotiation Assessment Tools App',$textMessage);
                    response(1,"Request Successfully Sent.",'No Email Found.'); 
                 }else{//0
                   response(0,"1",'eeror');    
                 }                
            }else{
                checkMyVariableList('',$data['userID'],$data['Variables']);
                $addEmployeeRequest=addEmployeeRequest($data['userID'],$data['email']);  
                    if(!empty($data['Variables'])){
                    foreach($data['Variables'] as $key=>$val){                        updateVariableDetails($val['variableID'],$val['variableName'],$val['price'],$data['userID'],'-1','sendRequest');
                    }   
                }
                $textMessage='Hello,<br> you are invited by '.$loggedInUser['data']['display_name'].' to join the Negotiation Assessment Tools App.<br>Regards,<br>Negotiation Assessment Team';
                send_email($data['email'],FROM_EMAIL,'Invitation from Negotiation Assessment Tools App',$textMessage);
                response(1,"Request Successfully Sent.",'No Email Found.');  
           }  
      }
    }
}else{
  response(0,"0",'Please enter the required fields.');  
}
?>