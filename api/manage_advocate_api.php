<?php

require_once './vendor/autoload.php';

use GeniusReferrals\GRPHPAPIClient;

class manage_advocate_api {

    protected $objGeniusReferralsAPIClient;

    public function __construct() {

        if (file_exists(__DIR__ . '/../config/config.php')) {
            require __DIR__ . '/../config/config.php';
            $strUsername = $apiConfig['gr_username'];
            $strAuthToken = $apiConfig['gr_auth_token'];
        }
        
        // Create a new GRPHPAPIClient object
        $this->objGeniusReferralsAPIClient = new GRPHPAPIClient($strUsername, $strAuthToken);
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
