<?php
require 'connect.php';
$encoded_data = file_get_contents('php://input');
$data = json_decode($encoded_data,true);
$error=0;
global $wpdb;
if(empty($data['userID'])){
    $error=1;
}
if(empty($error)){
    /* Employee Can take test only */
    $loggedInUser=AuthUser($data['userID'],'string');
    $checkEmployer=isEmployer($emailStatus['ID']);
    if(!empty($checkEmployer)){
      response(0,null,'Only employee can give test.');  
    } 
    $wpdb->query('insert into `nat_results`(`userID`, `Concession`, `Combat`, `SMARTnership`,`Bluff`,`Stress_flights`,`Stress_combat`) values("'.$data['userID'].'","'.$data['Concession'].'","'.$data['Combat'].'","'.$data['SMARTnership'].'","'.$data['Bluff'].'","'.$data['Stress']['Flight'].'","'.$data['Stress']['Combat'].'")');
    $last_insert_id=$wpdb->insert_id;
   /* $getLastInsertedData=$wpdb->get_row('select * from `nat_results` where `id`="'.$last_insert_id.'"');
    if(!empty($getLastInsertedData)){
      $getLastInsertedData=convert_array($getLastInsertedData);
      $result=$getLastInsertedData;
      $result['resultID']=$getLastInsertedData['id'];
      $result['resultDateTime']=date('d/m/Y',strtotime($getLastInsertedData['created']));
      unset($result['id']);
      unset($result['created']);
      unset($result['userID']);
        
    }else{
      response(0,null,'No Result Found.');   
    }*/
    response(1,'Result save successfully.','No Error Found.'); 
}else{
     response(0,null,'Please enter the required fields.');     
}      
?>
