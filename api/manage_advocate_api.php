<?php

require_once './vendor/autoload.php';

use GeniusReferrals\GRPHPAPIClient;

class manage_advocate_api {

    protected $objGeniusReferralsAPIClient;

    public function __construct() {

        // Create a new GRPHPAPIClient object
        $this->objGeniusReferralsAPIClient = new GRPHPAPIClient('alain@hlasolutionsgroup.com', '8450103c06dbd58add9d047d761684096ac560ca');
    }

    public function getAdvocates() {

        try {
            $arrAdvocate = $this->objGeniusReferralsAPIClient->getAdvocates('genius-referrals', 1, 50);
            $arrAdvocate = json_decode($arrAdvocate);
            return $arrAdvocate->data->results;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    public function getCampaigns() {
        
        try {
            $arrCampaigns = $this->objGeniusReferralsAPIClient->getCampaigns('genius-referrals');
            $arrCampaigns = json_decode($arrCampaigns);
            return $arrCampaigns->data->results;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }    
}
