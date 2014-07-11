<?php

require_once './vendor/autoload.php';

use GeniusReferrals\GRPHPAPIClient;

class api {

    public function getAdvocates() {

        // Create a new GRPHPAPIClient object
        $objGeniusReferralsAPIClient = new GRPHPAPIClient('alain@hlasolutionsgroup.com', '8450103c06dbd58add9d047d761684096ac560ca');

        //Test authentication
        $strResponse = $objGeniusReferralsAPIClient->testAuthentication();
        $intResponseCode = $objGeniusReferralsAPIClient->getResponseCode();

        if ($intResponseCode == 200) {
            //getting advocates
            $arrAdvocate = $objGeniusReferralsAPIClient->getAdvocates('genius-referrals');
            $arrAdvocate = json_decode($arrAdvocate);
            return $arrAdvocate->data->results;
        }
    }

}
