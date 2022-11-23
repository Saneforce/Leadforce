<?php

class Sms_daffytel
{
    // 1dfd967ea87d4a153a7ef7618095ade7
    public $endpoint ='https://portal.daffytel.com/api/v2/';

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model('sms_model');
        $this->configuration =array();
        $smsconfiguration =$this->ci->sms_model->getConfig();
        if($smsconfiguration ){
            if($smsconfiguration['provider'] =='daffytel'){
                $this->configuration =$smsconfiguration;
            }
        }
    }
    public function requestUrl($slug)
    {
        return $this->endpoint.$slug;
    }
    public function getTemplates()
    {
        
    }

    public function getTemplateDetails($DTL_template_id)
    {
        if($this->configuration){
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->requestUrl('sms/templates?filter[template_id]='.$DTL_template_id),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: '.$this->configuration['access_token']
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $response =json_decode($response,true);
            if($response && $response['rows']['data']){
                return $response['rows']['data'][0];
            }
        }
        
        return array();
    }

    public function send_daffy($fields)
    {
        if($this->configuration){
            $curl = curl_init();
        
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://portal.daffytel.com/api/v2/sms/template',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>json_encode($fields),
            CURLOPT_HTTPHEADER => array(
                'Authorization: '.$this->configuration['access_token'],
                'Content-Type: application/json'
            ),
            ));
    
            $response = curl_exec($curl);
            
            curl_close($curl);
            // pre($response);
        }
        
    }

    public function send($to,$template,$smsFields=array())
    {
        if($this->configuration){
            $this->ci->load->model('sms_model');
            $template_details =$this->ci->sms_model->getTemplate($template);
            $daffyTemplate =$this->getTemplateDetails($template);
            $service ='T';
            if($template_details && $daffyTemplate){
                if($template_details->route =='Transactional'){
                    $service ='T';
                }elseif($template_details->route =='Promotional'){
                    $service ='P';
                }
                $fields =array(
                    'service'=>$service,
                    'template_id'=>$daffyTemplate['id'],
                    'variables'=>$smsFields,
                    'to'=>array($to),
                );
                // pr($fields);
                // pre('sending sms to '.$to.' ...........');
                $this->send_daffy($fields);
            }
        }
        
        return ;
    }
}
