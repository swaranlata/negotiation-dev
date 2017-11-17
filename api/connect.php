<?php
require '../wp-config.php';
error_reporting(1);
date_default_timezone_set("Asia/Calcutta");
define('DEFAULT_IMAGE',get_site_url().'/api/default_img.jpg');
define('FROM_EMAIL','swaran.lata@imarkclients.com');
define('FROM_NAME','Negotiation API Team');
/* Print the response */
function pr($array=null){
    echo "<pre>";
    print_r($array);
    echo "</pre>";
    die('die here');
}
/* Convert Object to Array */
function convert_array($array=null){
    $json=json_decode(json_encode($array),TRUE);
    return $json;
}
/* Response */
 function response($success = null, $result = null, $error = null) {
    echo json_encode(
      array(
        'success' => $success,
        'result' => $result,
        'error' => $error
      )
    );
    die;
 }

/* check User Authorisation */
function AuthUser($userId=null,$type=null){
    $user=get_user_by('id',$userId);   
    if(!empty($user)){
        return $user;
    }else{
        if($type=='string'){
            response(0,null,'No User Found.'); 
        }else{
            response(0,array(),'No User Found.');  
        }
    }  
}

/* Generate Random String */
function generateRandomString($length = null) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/* check push notification status*/
function isPushNotificationsEnabled($user_id=null){
  $notify=get_user_meta($user_id,'is_enable_notification',true);
    if(empty($notify)){
        $notify=0;
    }
    return "$notify";
}

/* update the variable details */
function updateVariableDetails($varId=null,$varName=null,$varValue=null,$senderId=null,$receiverId=null,$checkTypeRequest=null){
    global $wpdb;
    $role=1;
    if($checkTypeRequest=='sendRequest'){
       $role=0;  
    }
    //$results=$wpdb->get_row('select `id` from `nat_variables_values` where `senderId`="'.$senderId.'" and `receiverId`="'.$receiverId.'" and `variableID`="'.$varId.'"'); 
    $finalQuery='select `id` from `nat_variables_values` where (`senderId`="'.$senderId.'" and `receiverId`="'.$receiverId.'" and `variableID`="'.$varId.'") or (`senderId`="'.$senderId.'" and `receiverId`="0" and `variableID`="'.$varId.'")';
    $results=$wpdb->get_row($finalQuery); 
    if(!empty($results)){
        $results=convert_array($results);
        if(!empty($results['receiverId'])){
            $wpdb->query('update `nat_variables_values` set `variableAmount`="'.$varValue.'"  where `id`="'.$results['id'].'"'); 
        }else{
            $wpdb->query('update `nat_variables_values` set `role`="'.$role.'",`receiverId`="'.$receiverId.'",`variableAmount`="'.$varValue.'"  where `id`="'.$results['id'].'"'); 
        }        
    }else{
        $checkEmp=isEmployer($senderId);
        /*$role=0;
        if(!empty($checkEmp)){
          $role=1;  
        }*/       
        $date=date('Y-m-d H:i:s');
        $wpdb->query('insert into `nat_variables_values`(`senderId`,`receiverId`,`variableID`,`variableAmount`,`created`,`role`) values("'.$senderId.'","'.$receiverId.'","'.$varId.'","'.$varValue.'","'.$date.'","'.$role.'")'); 
    }
    variableSelection($varId,$senderId,$receiverId);    
}

/* Check User Status */
function isEmployer($userId=null){
   return get_user_meta($userId,'is_employer',true);
}

/* Get Employee List*/
function getEmployeeList($employerId=null){
   global $wpdb;
   $results=$wpdb->get_results('select `employee_Id` from `nat_emp_relationships` where `employer_Id`="'.$employerId.'" order by id desc'); 
   return $results;
}

/* Get Variable name*/
function getVarName($varId=null){
   global $wpdb;
   $results=$wpdb->get_row('select `variableName` from `nat_variables` where `id`="'.$varId.'"'); 
    $results=convert_array($results);
   return $results['variableName'];
}

/* IsVariableSeleted */
function IsVariableSeleted($variableId=null,$userID=null,$type=null){
  global $wpdb;
  if(!empty($type)){
      $type='employerId';        
      $variableSelected="0";      
  }else{
    $type='employeeId';   
    $result=$wpdb->get_row('select * from `nat_variables_values` where (`senderId`="'.$userID.'" and  `variableID`="'.$variableId.'") or (`receiverId`="'.$userID.'" and  `variableID`="'.$variableId.'")');
    if(!empty($result)){
      $result=convert_array($result); 
        $result['variableAmount']=str_replace('$','',$result['variableAmount']);
        if(!empty($result['variableAmount'])){
           $variableSelected="1"; 
        }else{
          $variableSelected="0";  
        }      
    }else{
        $variableSelected="0";   
    }    
  }
 return $variableSelected;  
}

/* Check My Variable */
function checkMyVariable($senderId=null,$receiverId=null){
    global $wpdb;
    $variables=array();
    $rows=$wpdb->get_results('select * from `nat_variables_values` where (`senderId`="'.$senderId.'" and (`receiverId`="'.$receiverId.'" or `receiverId`="0")) or (`senderId`="'.$receiverId.'" and (`receiverId`="'.$senderId.'" or `receiverId`="0"))');
    if(!empty($rows)){
        $rows=convert_array($rows);
        foreach($rows as $k=>$v){
        $variables[]=$v['variableID'];  
        }  
    }
    $getPredefined=$wpdb->get_results('select * from `nat_variables` where `predefined`="true"');
    if(!empty($getPredefined)){
        $getPredefined=convert_array($getPredefined);
        foreach($getPredefined as $k=>$v){
        $variables[]=$v['id'];  
        }  
    }
    if(!empty($variables)){
     $variables=array_unique($variables);   
    }
    return $variables;
}

/* Check variable is negotiated or not */
function checkSelectedVariable($variables=null,$sender=null,$receiver=null){
  global $wpdb;
  $repo=0;
  if(!empty($variables)){
    foreach($variables as $k=>$v){
        $qry='select * from `nat_variables_values` where `isNegotiate`="1" and  `variableID`="'.$v['variableID'].'" and `senderId`="'.$sender.'" and `receiverId`="'.$receiver.'"';
        $result=$wpdb->get_row($qry);
        if(!empty($result)){
          $repo=1;
         }  
    }  
    return $repo; 
  }
}

/*Update the variable status negotiate to selected*/
function variableSelection($variable=null,$senderId=null,$receiverId=null){
    global $wpdb;
    //$query='select * from `nat_variables_values` where (`senderId`="'.$senderId.'" and `receiverId`="'.$receiverId.'" and `variableID`="'.$variable.'" and `variableAmount`!="" and `variableAmount`!="0") or (`receiverId`="'.$senderId.'" and `senderId`="'.$receiverId.'" and `variableID`="'.$variable.'" and `variableAmount`!="" and `variableAmount`!="0")';
    $query='select * from `nat_variables_values` where (`senderId`="'.$senderId.'" and `receiverId`="'.$receiverId.'" and `variableID`="'.$variable.'") or (`receiverId`="'.$senderId.'" and `senderId`="'.$receiverId.'" and `variableID`="'.$variable.'")';
    $results= $wpdb->get_results($query);  
    $employeeAmount=0;
    $employerAmount=0;
    $idsArray=array();
    if(!empty($results)){
          $results=convert_array($results); 
          $employeeAmount=0;
          $employerAmount=0;
          foreach($results as $k=>$v){             
             $employer=$v['role'];
             if(!empty($employer)){
                $employerAmount=str_replace('$','',$v['variableAmount']);
             }else{
                $employeeAmount=str_replace('$','',$v['variableAmount']);
             }             
          }
         if($employerAmount>=$employeeAmount and !empty($employerAmount) and !empty($employeeAmount)){
                $idsArray[]=$v['variableID'];               
         }
        if(!empty($idsArray)){
            $idsArray=implode('","',$idsArray);
            $updateQuery='update `nat_variables_values` set `isNegotiate`="1"  where  (`senderId`="'.$senderId.'" and `receiverId`="'.$receiverId.'" and `variableID` in("'.$idsArray.'")) or (`receiverId`="'.$senderId.'" and `senderId`="'.$receiverId.'" and `variableID` in("'.$idsArray.'"))';
            $wpdb->query($updateQuery);
         }
    }
}

function sendMessageIos($token_id,$checkNotification)
{
        $title = "Negotiation Assessment";
        $description = $checkNotification;
        $userToken=convert_array(getUserToken($token_id));
        $employeeStatus=isEmployer($userToken['user_id']);
        if(!empty($employeeStatus)){
             $employee='';
        }else{
            $employee=$userToken['user_id'];
        }
        //FCM api URL	
        $url = 'https://fcm.googleapis.com/fcm/send';
        //api_key available in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
        $server_key='AAAA_t7jMjI:APA91bG6WD0TKsz3BrHBtm7UNm0B6EJ07XL4quzl62Kmg9MzfMsQuGNiVO7zwCGh1HjtJe2JgtFaHv_zaL_uOSl6T_iXeLePjRHRtOwttbsH02Ok7eDmi2ooHE-fmOsqCdrFpCFrzJGG';
        //header with content_type api key
        $fields = array (
          'to' => $token_id,
          "content_available"  => true,
          "priority" =>  "high",
          'notification' => array( 
                "sound"=>  "default",
                "badge"=>  "12",
                'title' => "$title",
                'body' => "$description",
                'employeeId'=>$employee
            )
        );
        //header with content_type api key
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key='.$server_key
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
        die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
        }


function sendMessageAndroid($token_id,$checkNotification) {
        $title = "Negotiation Assessment";
        $description = $checkNotification;
        $userToken=convert_array(getUserToken($token_id));
        $employeeStatus=isEmployer($userToken['user_id']);
        if(!empty($employeeStatus)){
             $employee='';
        }else{
            $employee=$userToken['user_id'];
        }
        //FCM api URL	
        $url = 'https://android.googleapis.com/gcm/send';
        //api_key available in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
        $server_key = 'AAAA_t7jMjI:APA91bG6WD0TKsz3BrHBtm7UNm0B6EJ07XL4quzl62Kmg9MzfMsQuGNiVO7zwCGh1HjtJe2JgtFaHv_zaL_uOSl6T_iXeLePjRHRtOwttbsH02Ok7eDmi2ooHE-fmOsqCdrFpCFrzJGG';

        //header with content_type api key
        $fields = array (
            'to' => $token_id,
            "content_available"  => true,
            "priority" =>  "high",
            'notification' => array( 
                "sound"=>  "default",
                "badge"=>  "12",
                'title' => "$title",
                'body' => "$description",
                'employeeId'=>$employee
            )
        );
        //header with content_type api key
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key='.$server_key
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
        }

function sendPushNotification($user_id=null,$message=null,$sender_id=null,$role=null){
    global $wpdb;
    $wpdb->query('insert into `nat_notifications`(`message`,`senderId`,`receiverId`,`role`) values("'.$message.'","'.$sender_id.'","'.$user_id.'","'.$role.'")');
    $checkNotoficationStatus=isPushNotificationsEnabled($user_id);
    if(!empty($checkNotoficationStatus)){
        $notifyToken=get_user_meta($user_id,'tokenID',true);
        if(!empty($notifyToken)){
            $deviceType=get_user_meta($user_id,'deviceType',true);
            $deviceType=strtolower($deviceType);
            if(!empty($deviceType)){
               if($deviceType=='ios'){
                  sendMessageIos($notifyToken,$message); 
               }else{
                  sendMessageAndroid($notifyToken,$message);
               }
            }
        }
    }
}

function getUserToken($token_id=null){
    global $wpdb;
    $row=$wpdb->get_row('select * from `nat_usermeta` where `meta_key`="tokenID" and `meta_value`="'.$token_id.'"');
    return $row;
}

function getAllVariable($userid=null){
   global $wpdb;
   $results=$wpdb->get_results('select `variableID` from `nat_variables_values` where `senderId`="'.$userid.'"');
    $variables=array();
    if(!empty($results)){
        $results=convert_array($results);
        foreach($results as $k=>$v){            
            $var=$wpdb->get_row('select `variableName` from  `nat_variables` where `id`="'.$v['variableID'].'"');
            $varArray=convert_array($var); 
            $variables[]=strtolower($varArray['variableName']);
        }
        
    }
   return $variables;
  }
function getAllVariableAccRole($userid=null,$role=null){
   global $wpdb;
   $results=$wpdb->get_results('select `variableID` from `nat_variables_values` where `senderId`="'.$userid.'" and `role`="'.$role.'"');
    $variables=array();
    if(!empty($results)){
        $results=convert_array($results);
        foreach($results as $k=>$v){            
            $var=$wpdb->get_row('select `variableName` from  `nat_variables` where `id`="'.$v['variableID'].'"');
            $varArray=convert_array($var); 
            $variables[]=strtolower($varArray['variableName']);
        }
        
    }
   return $variables;
  }

function getEmployerDetails($user_id=null){
    global $wpdb;
    $result=$wpdb->get_row('select `employer_Id` from `nat_emp_relationships` where `employee_Id`="'.$user_id.'"');
    if(!empty($result)){
        $result=convert_array($result);
        $employerId=$result['employer_Id'];
    }else{
        $employerId="";
    }
    return $employerId;
    
}

/* Get all predefined variables */
function getAllPredefinedVariables(){
    global $wpdb;
    $var=$wpdb->get_results('select `id` from  `nat_variables` where `predefined`="true"'); 
    $allPredefinedVars=array();
    if(!empty($var)){
        $var=convert_array($var);
        foreach($var as $k=>$v){
          $allPredefinedVars[]=$v['id'];  
        }        
    }
    return $allPredefinedVars;
}
/* Get all variables */
function getAllVariablesGet(){
    global $wpdb;
    $var=$wpdb->get_results('select `id` from  `nat_variables`'); 
    $allPredefinedVars=array();
    if(!empty($var)){
        $var=convert_array($var);
        foreach($var as $k=>$v){
          $allPredefinedVars[]=$v['id'];  
        }        
    }
    return $allPredefinedVars;
} 

function addEmployeeRequest($userId=null,$email=null){
  global $wpdb;
  $row=$wpdb->query('delete from `nat_pending_employers` where `userID`="'.$userId.'"');
  $wpdb->query('insert into `nat_pending_employers`(`userID`,`email`) values("'.$userId.'","'.$email.'")');
}

/* Send Email function */
function send_email($to=null,$from=null,$subject=null,$message=null){
    require 'phpmailer/PHPMailerAutoload.php';
    require 'phpmailer/class.phpmailer.php';
    $phpmailer = new PHPMailer();
    $phpmailer->From = $from;
    $phpmailer->FromName = FROM_NAME;
    $phpmailer->isSMTP();   
    $phpmailer->Host = "mail.imarkclients.com";  // specify main and backup server
    $phpmailer->SMTPAuth = true;     // turn on SMTP authentication
    $phpmailer->Username = "test@imarkclients.com";  // SMTP username
    $phpmailer->Password = "aB}enOT-!vd&"; // SMTP password                        // SMTP password
    $phpmailer->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $phpmailer->Port = 25;   
    $phpmailer->Subject = $subject;
    $phpmailer->MsgHTML($message);
    $phpmailer->SMTPDebug=false;
    $phpmailer->AddAddress($to);
    $phpmailer->isHTML(true);
    $phpmailer->Send();    
}
/* check Pending Employer*/

function checkPendingEmployer($userId=null){
    global $wpdb;
    $result=$wpdb->get_row('select * from `nat_pending_employers` where `userID`="'.$userId.'"');
    $response=0;
    if(!empty($result)){
       $response=1; 
    }
    return $response;
 }

function checkOldEmployer($userId=null)
{
    global $wpdb;
    $count=0;
    $result=$wpdb->get_row('select * from `nat_emp_relationships` where `employee_Id`="'.$userId.'"');
    if(!empty($result)){
        $result=convert_array($result);
        $qry='select * from `nat_emp_relationships` where `employer_Id`="'.$result['employer_Id'].'" and `employee_Id`!="'.$userId.'"';
        $res=$wpdb->get_results($qry);
        if(!empty($res)){
          $count=count($res);  
        }
        if(empty($count)){
         update_user_meta($result['employer_Id'],'is_employer',0);    
        }       
    }  
    return $result['employer_Id'];
}

function checkIsSelected($varId=null,$userID=null,$role=null){
    global $wpdb;
    $result=$wpdb->get_row('select * from `nat_variables_values` where `senderId`="'.$userID.'" and  `variableID`="'.$variableId.'"'); 
    $selected=0;
    if(!empty($result)){
        $result=convert_array($result);
        $selected=1;
    }
    return $selected;   
}


/* Check My Variable */
function checkMyVariableList($senderId=null,$receiverId=null,$variablesData=null){
   /*  $senderId;//employer
    $receiverId;//employee
    $variables; //variable*/
    global $wpdb;
    $variables=array();
    if(!empty($senderId)){
        $EmployerQuery='select `id` from `nat_variables` where `userID`="'.$senderId.'" and `role`="1"';
        $rows=$wpdb->get_results($EmployerQuery);
        if(!empty($rows)){
            $rows=convert_array($rows);
            foreach($rows as $k=>$v){
            $variables[]=$v['id'];  
            }  
        }
    }
    $EmployeeQuery='select `id` from `nat_variables` where `userID`="'.$receiverId.'" and `role`="0"';
    $EmployeeQueryRow=$wpdb->get_results($EmployeeQuery);   
    if(!empty($EmployeeQueryRow)){
        $EmployeeQueryRow=convert_array($EmployeeQueryRow);
        if(!empty($EmployeeQueryRow)){
            $EmployeeQueryRow=convert_array($EmployeeQueryRow);
            foreach($EmployeeQueryRow as $k=>$v){
            $variables[]=$v['id'];  
            }  
        }        
    }
    $getPredefined=$wpdb->get_results('select * from `nat_variables` where `predefined`="true"');
    if(!empty($getPredefined)){
        $getPredefined=convert_array($getPredefined);
        foreach($getPredefined as $k=>$v){
        $variables[]=$v['id'];  
        }  
    }
    if(!empty($variables)){
     $variables=array_unique($variables);   
    }
    $procedd=1;
    if(!empty($variablesData)){
       foreach($variablesData as $k=>$v){
           if(!in_array($v['variableID'],$variables)){
              $procedd=0; 
           }
       }
    }
    if(empty($procedd)){
      response(0,'0','Please check your variable list.');         
    }
}



?>