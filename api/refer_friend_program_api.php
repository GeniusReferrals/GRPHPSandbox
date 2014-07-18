<?php

require_once './vendor/autoload.php';

use GeniusReferrals\GRPHPAPIClient;

class refer_friend_program_api {

    protected $objGeniusReferralsAPIClient;

    public function __construct() {

        session_start();

        // Create a new GRPHPAPIClient object
        $this->objGeniusReferralsAPIClient = new GRPHPAPIClient('alain@hlasolutionsgroup.com', '8450103c06dbd58add9d047d761684096ac560ca');
    }

    /**
     * tab Referral tools
     */
    public function getAdvocatesShareLinks() {

        try {
            if (!empty($_SESSION['strAdvocateToken'])) {

                $strGRAdvocateToken = $_SESSION['strAdvocateToken'];
                $arrAdvocatesShareLinks = $this->objGeniusReferralsAPIClient->getAdvocatesShareLinks('genius-referrals', $strGRAdvocateToken);
                $arrAdvocatesShareLinks = json_decode($arrAdvocatesShareLinks);

                $codeContents = $arrAdvocatesShareLinks->data->{'get-15-for-90-days-1'}->{'genius-referrals-default-2'}->{'personal'};
                if (file_exists(__DIR__ . '\..\library\phpqrcode\qrlib.php')) {
                    require __DIR__ . '\..\library\phpqrcode\qrlib.php';
                    $tempDir = __DIR__ . '\..\uploads/' . $strGRAdvocateToken . '.png';
                    \QRcode::png($codeContents, $tempDir, QR_ECLEVEL_H);
                }

                if (isset($arrAdvocatesShareLinks->data))
                    return $arrAdvocatesShareLinks->data;
                else
                    return array();
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /**
     * tab Bonuses earned
     */
    public function getReferralsSummaryPerOriginReport() {

        try {
            if (!empty($_SESSION['strAdvocateToken'])) {

                $strGRAdvocateToken = $_SESSION['strAdvocateToken'];

                $arrReferralsSummary = $this->objGeniusReferralsAPIClient->getReferralsSummaryPerOriginReport($strGRAdvocateToken);
                $arrReferralsSummary = json_decode($arrReferralsSummary);
                return $this->convertSummaryPerOrigin($arrReferralsSummary->data);
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    public function getBonusesSummaryPerOriginReport() {

        try {
            if (!empty($_SESSION['strAdvocateToken'])) {

                $strGRAdvocateToken = $_SESSION['strAdvocateToken'];

                $arrBonusesSummary = $this->objGeniusReferralsAPIClient->getBonusesSummaryPerOriginReport($strGRAdvocateToken);
                $arrBonusesSummary = json_decode($arrBonusesSummary);
                return $this->convertSummaryPerOrigin($arrBonusesSummary->data);
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /**
     * tab Redeem your bonuses
     */
    public function getAdvocate() {

        try {
            if (!empty($_SESSION['strAdvocateToken'])) {

                $strGRAdvocateToken = $_SESSION['strAdvocateToken'];

                $objAdvocate = $this->objGeniusReferralsAPIClient->getAdvocate('genius-referrals', $strGRAdvocateToken);
                $objAdvocate = json_decode($objAdvocate);
                return $objAdvocate->data;
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    public function getRedemptionRequests() {

        try {
            if (!empty($_SESSION['strAdvocateToken'])) {

                $strGRAdvocateToken = $_SESSION['strAdvocateToken'];

                $objAdvocate = $this->objGeniusReferralsAPIClient->getAdvocate('genius-referrals', $strGRAdvocateToken);
                $objAdvocate = json_decode($objAdvocate);

                $arrRedemptionRequests = $this->objGeniusReferralsAPIClient->getRedemptionRequests('genius-referrals', 1, 10, 'email::' . $objAdvocate->data->email . '');
                $arrRedemptionRequests = json_decode($arrRedemptionRequests);
                return $arrRedemptionRequests->data->results;
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    public function getAdvocatePaymentMethods() {

        try {
            if (!empty($_SESSION['strAdvocateToken'])) {

                $strGRAdvocateToken = $_SESSION['strAdvocateToken'];

                $aryPaymentMethods = $this->objGeniusReferralsAPIClient->getAdvocatePaymentMethods('genius-referrals', $strGRAdvocateToken, 1, 50);
                $aryPaymentMethods = json_decode($aryPaymentMethods);
                return $aryPaymentMethods->data->results;
            }
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
