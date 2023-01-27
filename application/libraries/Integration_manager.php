<?php

class Integration_manager {
    private $integrations;

    public function __construct() {
        $this->integrations = array();
        $this->addIntegration("Facebook lead ads","Instant alerts for Facebook Lead Ads.",'facebook.jpeg','plugin/facebook/leadads',array('leads','facebook'),'Leads','Upcoming');
        $this->addIntegration("99Acres","Online Platform To Real Estate Developers, Brokers and Owners For Listing Their Property.",'99acres.jpeg','plugin/acres99',array('leads','99acres'),'Leads');
        $this->addIntegration("IndiaMart","IndiaMART is India's largest online B2B marketplace, connecting buyers with suppliers.",'indiamart.jpeg','plugin/indiamart',array('leads','Indiamart'),'Leads');
        $this->addIntegration("LinkedIn","LinkedIn is one of the most trusted networks for building connections, enhancing your networking opportunities and finding the right career.",'linkedin.png','plugin/indiamart',array('leads','LinkedIn'),'Leads','Upcoming');

        $this->addIntegration("Whatsapp Business API","Send Automated Updates, Reminders on WhatsApp & Provide seamless Customer Experience.",'whatsapp.jpeg','plugin/whatsapp',array('notification','whatsapp'),'Message');
        $this->addIntegration("Daffytel","Daffytel Cloud Telephony in Chennai is one of the leading businesses in the Telecommunication Services.",'daffytel.png','plugin/sms/daffytel',array('notification','sms','Daffytel'),'Message');
        
        $this->addIntegration("Daffytel","Daffytel Cloud Telephony in Chennai is one of the leading businesses in the Telecommunication Services.",'daffytel.png','call_settings/enable_call',array('ivr','Daffytel'),'Call');
        $this->addIntegration("Telecmi","TeleCMI is the Leading IVR Service Provider in Chennai, Mumbai & Bangalore.",'telecmi.png','call_settings/enable_call',array('ivr','telecmi'),'Call');
        $this->addIntegration("Tata Tele Business Services","Tata Tele Business Services Limited, formally known as Tata Tele Services Limited, is an Indian broadband, telecommunications and cloud service provider based in Mumbai.",'tatateleservices.jpeg','call_settings/enable_call',array('ivr','tata tele services'),'Call');
    }

    public function addIntegration($name, $description, $logo, $targetUrl, $tags = [], $category = "",$label='') {
        $this->integrations[] = array(
            "name" => $name,
            "description" => $description,
            "logo" => $logo,
            "tags" => $tags,
            "category" => $category,
            "targetUrl" => $targetUrl,
            "label" => $label,
        );
    }

    public function getIntegrations() {
        return $this->groupByCategory($this->integrations);
    }

    public function searchIntegrations($searchTerm, $searchBy = "all") {
        $results = array();
        
        foreach ($this->integrations as $integration) {
            $found = false;
            if($searchBy === 'all' || $searchBy === 'name_description') {
                if (stripos($integration["name"], $searchTerm) !== false || stripos($integration["description"], $searchTerm) !== false) {
                    $integration['name'] =$this->highlightSearchTerm($integration['name'], $searchTerm);
                    $integration['description'] =$this->highlightSearchTerm($integration['description'], $searchTerm);
                    $found =true;
                }
            }
            
            if(($searchBy === 'all' || $searchBy === 'tags') && $integration['tags']) {
                if (count(array_intersect($integration["tags"], array($searchTerm))) > 0 ) {
                    foreach ($integration['tags'] as $key => $tag) {
                        $integration['tags'][$key] =$this->highlightSearchTerm($integration['tags'][$key], $searchTerm);
                    }
                    $found =true;
                }
            }
            if($searchBy === 'all' || $searchBy === 'category') {
                if (strtolower($integration["category"]) == strtolower($searchTerm)) {
                    $integration['category'] =$this->highlightSearchTerm($integration['category'], $searchTerm);
                    $found =true;
                }
            }

            if($found){
                $results[] = $integration;
            }
        }
        return $this->groupByCategory($results);
    }

    public function highlightSearchTerm($string, $searchTerm) {
        $pos = mb_stripos($string, $searchTerm);
        if($searchTerm =='' || $pos =='')
            return $string;
        $actualString =substr($string,$pos,strlen($searchTerm));
        $highlighted_string =  substr($string, 0, $pos) . '<mark>' . $actualString . '</mark>' . substr($string, $pos + strlen($searchTerm));
        return $highlighted_string;
    }


    public function groupByCategory($integrations) {
        $grouped_integrations = array();
        foreach ($integrations as $integration) {
            if (!isset($grouped_integrations[$integration['category']])) {
                $grouped_integrations[$integration['category']] = array();
            }
            $grouped_integrations[$integration['category']][] = $integration;
        }
        return $grouped_integrations;
    }

    public function extractTags() {
        $integrations = $this->getIntegrations();
        $tags = array();
        foreach ($integrations as $integration) {
            $tags = array_merge($tags, $integration['tags']);
        }
        return array_unique($tags);
    }

    public function extractCategories() {
        $integrations = $this->getIntegrations();
        $categories = array();
        foreach ($integrations as $integration) {
            $categories[] = $integration['category'];
        }
        return array_unique($categories);
    }

    public function filter($categories = [], $tags = []) {
        $integrations = $this->getIntegrations();
        $filtered_integrations = array();
        foreach ($integrations as $integration) {
            if (empty($categories) || in_array($integration['category'], $categories)) {
                if (empty($tags) || count(array_intersect($integration['tags'], $tags)) > 0) {
                    $filtered_integrations[] = $integration;
                }
            }
        }
        return $this->groupByCategory($filtered_integrations);
    }

}
