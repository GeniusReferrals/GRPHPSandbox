<?php

require_once './vendor/autoload.php';

use GeniusReferrals\GRPHPAPIClient;

class refer_friend_program_api {

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
     * Get advocates share links.
     * 
     * @param string $strGRAdvocateToken. The advocate token.
     * @return string
     */
    public function getAdvocatesShareLinks($strGRAdvocateToken) {

        try {
            $arrAdvocatesShareLinks = $this->objGeniusReferralsAPIClient->getAdvocatesShareLinks($this->strAccount, $strGRAdvocateToken);
            $arrAdvocatesShareLinks = json_decode($arrAdvocatesShareLinks);

            $codeContents = $arrAdvocatesShareLinks->data->{$this->strCampaign}->{$this->strWidgetsPackage}->{'personal'};
            if (file_exists(__DIR__ . '\..\library\phpqrcode\qrlib.php')) {
                require __DIR__ . '\..\library\phpqrcode\qrlib.php';
                $tempDir = __DIR__ . '\..\uploads/' . $strGRAdvocateToken . '.png';
                \QRcode::png($codeContents, $tempDir, QR_ECLEVEL_H);
            }

            if (isset($arrAdvocatesShareLinks->data))
                return $arrAdvocatesShareLinks->data;
            else
                return array();
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /**
     * Get referrals summary per origin report.
     * 
     * @param string $strGRAdvocateToken. The advocate token.
     * @return string
     */
    public function getReferralsSummaryPerOriginReport($strGRAdvocateToken) {

        try {
            $arrReferralsSummary = $this->objGeniusReferralsAPIClient->getReferralsSummaryPerOriginReport($strGRAdvocateToken);
            $arrReferralsSummary = json_decode($arrReferralsSummary);
            return $this->convertSummaryPerOrigin($arrReferralsSummary->data);
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /**
     * Get bonuses summary per origin report.
     * 
     * @param string $strGRAdvocateToken. The advocate token.
     * @return string
     */
    public function getBonusesSummaryPerOriginReport($strGRAdvocateToken) {

        try {
            $arrBonusesSummary = $this->objGeniusReferralsAPIClient->getBonusesSummaryPerOriginReport($strGRAdvocateToken);
            $arrBonusesSummary = json_decode($arrBonusesSummary);
            return $this->convertSummaryPerOrigin($arrBonusesSummary->data);
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /**
     * Get advocate.
     * 
     * @param string $strGRAdvocateToken. The advocate token.
     * @return string
     */
    public function getAdvocate($strGRAdvocateToken) {

        try {
            $objAdvocate = $this->objGeniusReferralsAPIClient->getAdvocate($this->strAccount, $strGRAdvocateToken);
            $objAdvocate = json_decode($objAdvocate);
            return $objAdvocate->data;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /**
     * Get redemption requests.
     * 
     * @param string $strGRAdvocateToken. The advocate token.
     * @return string
     */
    public function getRedemptionRequests($strGRAdvocateToken) {

        try {
            $objAdvocate = $this->objGeniusReferralsAPIClient->getAdvocate($this->strAccount, $strGRAdvocateToken);
            $objAdvocate = json_decode($objAdvocate);

            $arrRedemptionRequests = $this->objGeniusReferralsAPIClient->getRedemptionRequests($this->strAccount, 1, 10, 'email::' . $objAdvocate->data->email . '');
            $arrRedemptionRequests = json_decode($arrRedemptionRequests);
            return $arrRedemptionRequests->data->results;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /**
     * Get advocate payment methods.
     * 
     * @param string $strGRAdvocateToken. The advocate token.
     * @return string
     */
    public function getAdvocatePaymentMethods($strGRAdvocateToken) {

        try {
            $aryPaymentMethods = $this->objGeniusReferralsAPIClient->getAdvocatePaymentMethods($this->strAccount, $strGRAdvocateToken, 1, 50);
            $aryPaymentMethods = json_decode($aryPaymentMethods);
            return $aryPaymentMethods->data->results;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
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
