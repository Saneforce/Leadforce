<?php


function facebook_get_pages($userId, $accessToken)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://graph.facebook.com/'.$userId.'/accounts?access_token='.$accessToken,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return json_decode($response);
}

function facebook_get_leadgen_forms($pageId,$pageAccessToken)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://graph.facebook.com/v15.0/'.$pageId.'/leadgen_forms?access_token='.$pageAccessToken,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return json_decode($response);
}

function facebook_get_leadgen_form_details($formId,$pageAccessToken)
{
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://graph.facebook.com/v15.0/'.$formId.'?fields=questions&access_token='.$pageAccessToken,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return json_decode($response);
}

function facebook_get_page_profilelink($pageId,$pageAccessToken)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://graph.facebook.com/v4.0/'.$pageId.'/picture?access_token='.$pageAccessToken.'&redirect=0',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return json_decode($response);
}

function facebook_set_leadgen_webhook($appId,$appSecret)
{
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://graph.facebook.com/'.$appId.'/subscriptions',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('object' => 'page','callback_url' => base_url('webhooks/facebook/leadads'),'fields' => 'leadgen','include_values' => 'true','verify_token' => 'abc123','access_token' => $appId.'|'.$appSecret),
    ));
    
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response,true);
}

function facebook_get_leadgen_details($leadgenId,$accessToken)
{

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://graph.facebook.com/v15.0/'.$leadgenId.'?access_token='.$accessToken,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return json_decode($response,true);
}