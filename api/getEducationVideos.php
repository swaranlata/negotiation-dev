<?php
require 'connect.php';
$data = $_GET;
$error=0;
global $wpdb;
if(empty($data['userID'])){
    $error=1;
}
if(empty($error)){
    $loggedInUser=AuthUser($data['userID'],'array');
    $rows = get_field('links',126);     
    if(!empty($rows)){
        foreach($rows as $key=>$val){
        $getCoverImage=$val['link'];
        if (strpos($getCoverImage,'?v') !== false) {
           $coverImage=explode('?v=',$getCoverImage);
        }
        if (strpos($getCoverImage,'https://youtu.be/') !== false) {
           $coverImage=explode('https://youtu.be/',$getCoverImage);
        }
        $thumbImage='http://img.youtube.com/vi/'.@$coverImage[1].'/0.jpg';  
        $array[$key]['image']=$thumbImage;
        $array[$key]['videoLink']=$val['link'];            
        }
         response(1,$array,'No error found.');
    }else
    {
      response(0,array(),'No Data found.');  
    }
 
}else{
  response(0,array(),'Please enter required fields.');    
}

?>