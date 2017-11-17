
<?php
        $token_id='eRktZbxCpgY:APA91bE41hUOMNMyff1c6UbZsqc_1nXM2JLJKo1nTC40rQfOoUUnUKBHTC-MaIYcrJ8tPr3mYuwG4MwlYDxikU4UEL7btS78y2x9Gad9yA6032zf0gY39iuRzIcQc_LDAIGy5jjXrUXS';
        $title = "Negotiation Assessment";
        $description = 'Hello Ankita';        
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
                //'employeeId'=>'56'
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
echo "<pre>";
print_r($result);
        if ($result === FALSE) {
        die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
/*



$title = "Negotiation";
        $description ="Hello Testing" ;

        //FCM api URL	
        $url = 'https://android.googleapis.com/gcm/send';
        //api_key available in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
        $server_key = 'AAAA_t7jMjI:APA91bG6WD0TKsz3BrHBtm7UNm0B6EJ07XL4quzl62Kmg9MzfMsQuGNiVO7zwCGh1HjtJe2JgtFaHv_zaL_uOSl6T_iXeLePjRHRtOwttbsH02Ok7eDmi2ooHE-fmOsqCdrFpCFrzJGG';
$token_id='f8_Q0CRa-0g:APA91bFV_kdEdu9ePmnIm8l9EhPM3-m7TZ9kuU1kTHdr3_XIjzAnVJhVl332neP1nbGP4ntJ0vAo1WUkzzJCIzYONgJwVZ_A_cQnp4sO11QyvpwhBkjDdiIw_FRkZkeZMbECpRMBsVVY';
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
        echo "<pre>";
print_r($result);
die;
        if ($result === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
        
*/






?>
















