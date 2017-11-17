<?php
require 'connect.php';
$data = $_GET;
$error=0;
global $wpdb;
if(empty($data['userID'])){
    $error=1;
}
if(empty($data['resourcesName'])){
    $error=1;
}
if(empty($error)){
    $loggedInUser=AuthUser($data['userID'],'string');
    $pageName=array('style','concession','opening','checklist','question','agenda','placetonegotiate','combativenegotiator');
    if(!in_array(strtolower($data['resourcesName']),$pageName)){
     response(0,null,'Please check your resource name.');     
    }
    $pagesArray=array(
       '13'=>'style',
       '18'=>'concession',
       '20'=>'opening',
       '22'=>'checklist',
       '24'=>'question',
       '26'=>'agenda',
       '28'=>'placetonegotiate',
       '30'=>'combativenegotiator',
   );
   foreach($pagesArray as $key=>$val){
      if($val==strtolower($data['resourcesName'])){
        $pageId=$key;  
      } 
   }
    $pageData=get_page($pageId);
    if(!empty($pageData)){
        $pageData=convert_array($pageData);
        $getCoverImage=get_field('video_url',$pageData['ID']);
        if (strpos($getCoverImage,'?v') !== false) {
           $coverImage=explode('?v=',$getCoverImage);
        }
        if (strpos($getCoverImage,'https://youtu.be/') !== false) {
           $coverImage=explode('https://youtu.be/',$getCoverImage);
        }
        $thumbImage='http://img.youtube.com/vi/'.@$coverImage[1].'/0.jpg';
        $ID=$pageData['ID'];
        $pageContent['resourceId']="$ID";
        $pageContent['resourcesContent']=$pageData['post_content'];
        $pageContent['resourcesTitle']=$pageData['post_title'];
        $pageContent['resourcesimage']=$thumbImage;
        $pageContent['resourcesLink']=$getCoverImage;
      response(1,$pageContent,'No error found.');
    }else{
      response(0,null,'No data found.');
    }
}else{
  response(0,null,'Please enter required fields.');    
}
?>