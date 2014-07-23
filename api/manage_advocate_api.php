<?php

require_once './vendor/autoload.php';

use GeniusReferrals\GRPHPAPIClient;

class manage_advocate_api {

    protected $objGeniusReferralsAPIClient;
    protected $strUsername;
    protected $strAuthToken;
    protected $strAccount;
    protected $strCampaign;
    protected $strWidgetsPackage;

    public function __construct() {

        if (file_exists(__DIR__ . '/../config/config.php')) {
            require __DIR__ . '/../config/config.php';
            $this->strUsername = $apiConfig['gr_username'];
            $this->strAuthToken = $apiConfig['gr_auth_token'];
            $this->strAccount = $apiConfig['gr_rfp_account'];
            $this->strCampaign = $apiConfig['gr_rfp_campaign'];
            $this->strWidgetsPackage = $apiConfig['gr_rfp_widgets_package'];
        }
        
        // Create a new GRPHPAPIClient object
        $this->objGeniusReferralsAPIClient = new GRPHPAPIClient($this->strUsername, $this->strAuthToken);
    }

    public function getAdvocates() {

        try {
            $arrAdvocate = $this->objGeniusReferralsAPIClient->getAdvocates($this->strAccount, 1, 50);
            $arrAdvocate = json_decode($arrAdvocate);
            return $arrAdvocate->data->results;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    public function getCampaigns() {

        try {
            $arrCampaigns = $this->objGeniusReferralsAPIClient->getCampaigns($this->strAccount);
            $arrCampaigns = json_decode($arrCampaigns);
            return $arrCampaigns->data->results;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    public function getReferralOrigins() {

        try {
            $arrReferralOrigins = $this->objGeniusReferralsAPIClient->getReferralOrigins();
            $arrReferralOrigins = json_decode($arrReferralOrigins);
            return $arrReferralOrigins->data;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

}
