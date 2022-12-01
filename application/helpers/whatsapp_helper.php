<?php

function whatsapp_get_templates()
{
    $CI = &get_instance();
    $CI->load->model('whatsapp_model');
    $account =$CI->whatsapp_model->getSettings();
    $version ='v13.0';
    if($account){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://graph.facebook.com/'.$version.'/'.$account['waba_id'].'/message_templates',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$account['user_access_token']
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response =json_decode($response);
        if(isset($response->data)){
            return $response->data;
        }
    }
    return array();
}

function whatsapp_count_variables($message)
{
    preg_match_all("/\{([^W]+?)\}/", $message, $result);
    
    if(isset($result[0])){
        return count($result[0]);
    }
    return 0;
}

function whatsapp_send_template_message($to,$template,$variables,$header=array())
{
    $parameters =array();
    if($variables){
        foreach($variables as $variable){
            $parameters [] =array(
                'type'=>'text',
                'text'=>$variable
            );
        }
    }
    $curl = curl_init();
    $version ='v13.0';
    $CI = &get_instance();
    $CI->load->model('whatsapp_model');
    $whatsapp_account =$CI->whatsapp_model->getSettings();

    if($header){
        if($header['header_format'] =='TEXT' && $header['header_variable']){
            $headerstring ='{
                "type": "header",
                "parameters": [
                    {
                        "type": "text",
                        "text": "'.$header['header_variable'].'"
                    }
                ]
            },';
        }elseif($header['header_format'] =='DOCUMENT' && $header['header_media_link']){
            $headerstring ='{
                "type": "header",
                "parameters": [
                    {
                        "type": "document",
                        "document": {
                          "link": "'.$header['header_media_link'].'",
                          "filename": "'.$header['header_media_caption'].'"
                        }
                    }
                ]
            },';
        }elseif($header['header_format'] =='VIDEO' && $header['header_media_link']){
            $headerstring ='{
                "type": "header",
                "parameters": [
                    {
                        "type": "video",
                        "video": {
                          "link": "'.$header['header_media_link'].'"
                        }
                    }
                ]
            },';
        }elseif($header['header_format'] =='IMAGE'){
            $headerstring ='{
                "type": "header",
                "parameters": [
                    {
                        "type": "image",
                        "image": {
                          "link": "'.$header['header_media_link'].'"
                        }
                    }
                ]
            },';
        }
        
    }else{
        $headerstring ='';
    }

    if($whatsapp_account){
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://graph.facebook.com/'.$version.'/'.$whatsapp_account['phonenumber_id'].'/messages',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "messaging_product": "whatsapp",
            "recipient_type": "individual",
            "to": "'.$to.'",
            "type": "template",
            "template": {
                "name": "'.$template.'",
                "language": {
                    "code": "en_US"
                },
                "components": [
                    '.$headerstring.'
                    {
                        "type": "body",
                        "parameters": '.json_encode($parameters).'
                    }
                ]
            }
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$whatsapp_account['user_access_token']
        ),
        ));
    
        $response = curl_exec($curl);
        
        curl_close($curl);
    }
    
}