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

    /**
     * Create a new GRPHPAPIClient object
     * 
     */
    public function __construct() {

        if (file_exists(__DIR__ . '/../config/config.php')) {
            require __DIR__ . '/../config/config.php';
            $this->strUsername = $apiConfig['gr_username'];
            $this->strAuthToken = $apiConfig['gr_auth_token'];
            $this->strAccount = $apiConfig['gr_rfp_account'];
            $this->strCampaign = $apiConfig['gr_rfp_campaign'];
            $this->strWidgetsPackage = $apiConfig['gr_rfp_widgets_package'];
        }
        $this->objGeniusReferralsAPIClient = new GRPHPAPIClient($this->strUsername, $this->strAuthToken);
    }

    /**
     * Get advocates.
     * 
     * @param integer $page. The current page, default is 1.
     * @param integer $limit. Maximum number of results to return in the response.
     * @return string
     */
    public function getAdvocates($page, $limit) {

        try {
            $arrAdvocate = $this->objGeniusReferralsAPIClient->getAdvocates($this->strAccount, $page, $limit, '', 'created');
            $arrAdvocate = json_decode($arrAdvocate);
            return $arrAdvocate;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    public function addDOMNode($paginate, $page_active, $url) {
        $dom = new DOMDocument('1.0', 'utf-8');
        $element = $paginate->getDOMNode($dom, $page_active, $url);
        $dom->appendChild($element);
        echo $dom->saveHTML();
    }

    /**
     * Get campaigns.
     * 
     * @return string
     */
    public function getCampaigns() {

        try {
            $arrCampaigns = $this->objGeniusReferralsAPIClient->getCampaigns($this->strAccount);
            $arrCampaigns = json_decode($arrCampaigns);
            return $arrCampaigns->data->results;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /**
     * Get referral origins.
     * 
     * @return string
     */
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
