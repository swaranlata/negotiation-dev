<?php
require 'connect.php';
$encoded_data = file_get_contents('php://input');
$data = $_GET;
$error=0;
global $wpdb;
if(empty($data['userID'])){
    $error=1;
}
if(empty($error)){
    $loggedInUser=AuthUser($data['userID'],array());
    $getLastInsertedData=$wpdb->get_results('select * from `nat_results` where `userID`="'.$data['userID'].'"');
    $array=array();
     if(!empty($getLastInsertedData)){
        $getLastInsertedDataAll=convert_array($getLastInsertedData);
        foreach($getLastInsertedDataAll as $key=>$getLastInsertedData){
            $array[$key]=$getLastInsertedData;
            $array[$key]['resultID']=$getLastInsertedData['id'];
            $array[$key]['resultDateTime']=date('d/m/Y',strtotime($getLastInsertedData['created']));
            $array[$key]['Concession']=$getLastInsertedData['Concession'].'%';
            $array[$key]['Combat']=$getLastInsertedData['Combat'].'%';
            $array[$key]['SMARTnership']=$getLastInsertedData['SMARTnership'].'%';
            $array[$key]['Bluff']=$getLastInsertedData['Bluff'].'%';
            $array[$key]['Stress']['Combat']=$getLastInsertedData['Stress_combat'].'%';
            $array[$key]['Stress']['Flight']=$getLastInsertedData['Stress_flights'].'%';            
            unset($array[$key]['id']);
            unset($array[$key]['created']);
            unset($array[$key]['userID']);  
            unset($array[$key]['Stress_combat']);  
            unset($array[$key]['Stress_flights']);  
        }     
      response(1,$array,'No Error Found.');   
    }else{
      response(0,array(),'No Result Found.');   
    }
}else{
     response(0,array(),'Please enter the required fields.');     
}      
?>
