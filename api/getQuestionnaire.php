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
    $args = array(
    'post_type' => 'questions',
    'posts_per_page' =>-1,
    'post_status'=>'publish',
    'orderby' => 'date',
	'order'=> 'asc',
    );
    $questions=get_posts($args);
    $getQuestions=array();
    if(!empty($questions)){
       $questions=convert_array($questions);
       foreach($questions as $keys=>$val){
            $id=$val['ID'];
            $getQuestions[$keys]['questionID']="$id";
            $getQuestions[$keys]['question']=$val['post_title'];
            $getQuestions[$keys]['options']=array();
            $rows = get_field('options_repeater',$id);           
            $allOptions=array();
            if(!empty($rows))
            {
               $main_cat=array();
               $catCustom=array();
               foreach($rows as $key=>$r)
                {
                   $catCustom[$r['option']][]=$r;                        
                 
                }
                $k=0;                
                foreach($catCustom as $key=>$r){
                    $main_cat=array();
                    $count=count($r);
                    if($count >=1)
                    {
                        foreach($r as $keyss =>$opt)
                        {
                            $catId =$opt['option_category'][0];
                            $term = convert_array(get_term($catId,'questions'));
                            if(empty($term['parent'])){
                              $main_cat[$keyss]['category']=$term['name']; 
                              $main_cat[$keyss]['subcategory']="";
                            }else{
                              $sub_term = convert_array(get_term($catId, 'questions'));
                              $get_parent=convert_array(get_term($sub_term['parent'], 'questions'));
                              $main_cat[$keyss]['category']=$get_parent['name']; 
                              $main_cat[$keyss]['subcategory']=$sub_term['name'];  
                            }
                            $main_cat[$keyss]['Point']=(int)$opt['value'];
                            $text=strip_tags($opt['option_text']);
                        }

                    }
                    $allOptions[$k]['optionText']=$text;                   
                    $allOptions[$k]['category']=$main_cat; 
                    $allOptions[$k]['state']=0; 
                    $getQuestions[$keys]['options']=$allOptions;
                    $k++;
                }  
            }
           
        }   
       // pr($getQuestions);
       response(1,$getQuestions,'No error found.');   
           
    }else{
      response(0,array(),'No questions are found.');    
    }   
}else{
    response(0,array(),'Please enter required fields.');    
}

?>