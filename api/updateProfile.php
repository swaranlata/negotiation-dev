<?php
require 'connect.php';
$encoded_data = file_get_contents('php://input');
$data = json_decode($encoded_data,true);
$error=0;
if(empty($data['userID'])){
    $error=1;
}
if(empty($data['name'])){
    $error=1;
}
if(!empty($error)){
   response(0,null,'Please enter the required fields.'); 
}else{
    $loggedInUser=AuthUser($data['userID'],'string');
    $loggedInUser=convert_array($loggedInUser);
    if(!empty($data['name'])){
      wp_update_user(array('ID' => $data['userID'], 'display_name' => $data['name']));  
    }     
    if(!empty($data['profile'])){
        $profilePicBase64 = $data['profile'];
        $directory = "/" . date(Y) . "/" . date(m) . "/";
        $wp_upload_dir = wp_upload_dir();
        $datacheck = base64_decode($profilePicBase64);
        $filename = time() . ".png";
        $fileurl = "../wp-content/uploads" . $directory . $filename;
        $filetype = wp_check_filetype(basename($fileurl), null);
        file_put_contents($fileurl, $datacheck);
        $attachment = array(
            'guid' => $wp_upload_dir['url'] . '/' . basename($fileurl),
            'post_mime_type' => $filetype['type'],
            'post_title' => preg_replace('/\.[^.]+$/', '', basename($fileurl)),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment($attachment, $fileurl,$data['userID']);
        require_once('../wp-admin/includes/image.php' );
        $attach_data = wp_generate_attachment_metadata($attach_id, $fileurl);
        wp_update_attachment_metadata($attach_id, $attach_data);
        update_user_meta($data['userID'],'user_image', $attach_id);
    }
    $user_image = "";
    $image = get_user_meta($data['userID'], 'user_image', true);
    if (!empty($image)) {
      $user_image = get_post_field('guid', $image);
    }
    response(1,$user_image,'No Error Found.');   
 }
?>