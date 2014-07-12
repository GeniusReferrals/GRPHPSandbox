<?php

require_once './vendor/autoload.php';

use GeniusReferrals\GRPHPAPIClient;

class refer_friend_program_api {

    protected $objGeniusReferralsAPIClient;

    public function __construct() {
        // Create a new GRPHPAPIClient object
        $this->objGeniusReferralsAPIClient = new GRPHPAPIClient('alain@hlasolutionsgroup.com', '8450103c06dbd58add9d047d761684096ac560ca');
    }

    /**
     * tab Referral tools
     */
    public function getAdvocatesShareLinks() {

        $arrAdvocatesShareLinks = $this->objGeniusReferralsAPIClient->getAdvocatesShareLinks('genius-referrals', $strGRAdvocateToken);
        $arrAdvocatesShareLinks = json_decode($arrAdvocatesShareLinks);
        return $arrAdvocatesShareLinks->data->results;
    }

    /**
     * tab Bonuses earned
     */
    public function getReferralsSummaryPerOriginReport() {

        $arrReferralsSummary = $this->objGeniusReferralsAPIClient->getReferralsSummaryPerOriginReport($strGRAdvocateToken);
        $arrReferralsSummary = json_decode($arrReferralsSummary);
        return $this->convertSummaryPerOrigin($arrReferralsSummary->data);
    }

    public function getBonusesSummaryPerOriginReport() {

        $arrBonusesSummary = $this->objGeniusReferralsAPIClient->getBonusesSummaryPerOriginReport($strGRAdvocateToken);
        $arrBonusesSummary = json_decode($arrBonusesSummary);
        $arrBonusesSummaryPerOrigin = $this->convertSummaryPerOrigin($arrBonusesSummary->data);
    }

    /**
     * tab Redeem your bonuses
     */
    public function getAdvocate() {

        $objAdvocate = $this->objGeniusReferralsAPIClient->getAdvocate('genius-referrals', $strGRAdvocateToken);
        $objAdvocate = json_decode($objAdvocate);
        return $objAdvocate->data;
    }

    public function getRedemptionRequests() {

        $arrRedemptionRequests = $this->objGeniusReferralsAPIClient->getRedemptionRequests('genius-referrals', $page, 10, 'email::' . $objAdvocate->data->email . '');
        $arrRedemptionRequests = json_decode($arrRedemptionRequests);
        return $arrRedemptionRequests->data->results;
    }

    public function getAdvocatePaymentMethods() {

        $aryPaymentMethods = $this->objGeniusReferralsAPIClient->getAdvocatePaymentMethods('genius-referrals', $strGRAdvocateToken, 1, 50);
        $aryPaymentMethods = json_decode($aryPaymentMethods);
        return $aryPaymentMethods->data->results;
    }

    private function convertSummaryPerOrigin($arrSummaryPerOrigin) {

        $arrNetwork = array(array('slug' => 'facebook-share', 'name' => 'Facebook share'),
            array('slug' => 'twitter-post', 'name' => 'Twitter post'),
            array('slug' => 'linkedin-post', 'name' => 'LinkedIn post'),
            array('slug' => 'pin-it', 'name' => 'Pin it'),
            array('slug' => 'google-plus', 'name' => 'Google plus'),
            array('slug' => 'direct-email', 'name' => 'Email'),
            array('slug' => 'personal-url', 'name' => 'PURL'),
            array('slug' => 'other', 'name' => 'Other'));
        $arrSummaryPerOriginResult = array();
        $flag = false;

        for ($i = 0; $i < count($arrNetwork); $i++) {
            for ($j = 0; $j < count($arrSummaryPerOrigin); $j++) {
                if ($arrNetwork[$i]['slug'] == $arrSummaryPerOrigin[$j]->slug) {
                    $arrSummaryPerOriginResult[] = $arrSummaryPerOrigin[$j];
                    $flag = true;
                }
            }
            if (!$flag) {
                $objBonusResult = new \stdClass();
                $objBonusResult->name = $arrNetwork[$i]['name'];
                $objBonusResult->amount = 0;
                $arrSummaryPerOriginResult[] = $objBonusResult;
            }
            $flag = false;
        }
        return $arrSummaryPerOriginResult;
    }

}
