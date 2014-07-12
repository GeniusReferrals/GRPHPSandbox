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

        //Test authentication
        $strResponse = $this->objGeniusReferralsAPIClient->testAuthentication();
        $intResponseCode = $this->objGeniusReferralsAPIClient->getResponseCode();

        if ($intResponseCode == 200) {
            $arrAdvocate = $this->objGeniusReferralsAPIClient->getAdvocates('genius-referrals');
            $arrAdvocate = json_decode($arrAdvocate);
            return $arrAdvocate->data->results;
        }
    }
}
