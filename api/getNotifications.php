<?php
require 'connect.php';
$data = $_GET;
$error=0;
global $wpdb;
if(empty($data['userID'])){
    $error=1;
}
$array=array(0,1);
if($data['role']!=''){
    if(!in_array($data['role'],$array)){
       $error=1;    
    }    
}else{
   $error=1;  
}
if(empty($error)){
       $loggedInUser=AuthUser($data['userID'],'array');
       $rows=$wpdb->get_results('select * from `nat_notifications` where `receiverId`="'.$data['userID'].'" and `role`="'.$data['role'].'" order by id desc'); 
       $arra=array();
       if(!empty($rows)){
            $rows=convert_array($rows);
            foreach($rows as $k=>$v){
                $arra[$k]['notificationID']=$v['id'];
                $arra[$k]['employeeID']="";
                if($data['role']==1){
                 $arra[$k]['employeeID']=$v['senderId'];   
                }                
                $arra[$k]['title']=$v['message'];
            }
           response(1,$arra,'No Error Found.');
        }else{
           response(0,array(),'No Data Found.');
        }
    }else{        
         response(0,array(),'Please enter required fields.');    
    }


?>