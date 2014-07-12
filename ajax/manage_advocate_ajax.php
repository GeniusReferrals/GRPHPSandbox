<?php

require_once '../vendor/autoload.php';

use GeniusReferrals\GRPHPAPIClient;

class manage_advocate_ajax {

    protected $response;
    protected $objGeniusReferralsAPIClient;

    public function __construct($method = NULL) {

        // Create a new GRPHPAPIClient object
        $this->objGeniusReferralsAPIClient = new GRPHPAPIClient('alain@hlasolutionsgroup.com', '8450103c06dbd58add9d047d761684096ac560ca');

        // find post data
        $data = $_POST['data'];

        $this->response = new stdClass();

        // handle errors if method not set or not exists
        try {
            $result = $this->{$method}($data);

            $this->success($result);
        } catch (Exception $e) {
            // if there was an exception respond with a failure
            $this->failure($e->getMessage());
        }
    }

    public function createAdvocate($data) {

        $strName = $data['name'];
        $strLastName = $data['last_name'];
        $strEmail = $data['email'];

        try {
            //preparing the data to be sent on the request
            $strAdvocateData = array('advocate' => array(
                    'name' => $strName,
                    'lastname' => $strLastName,
                    'email' => $strEmail,
                    'payout_threshold' => 20));

            $objResponse = $this->objGeniusReferralsAPIClient->postAdvocate('genius-referrals', $strAdvocateData);
            $intResponseCode = $this->objGeniusReferralsAPIClient->getResponseCode();

            if ($intResponseCode == '201') {
                //getting the advocate token from the Location header
                $arrLocation = $objResponse->getHeader('Location')->raw();
                $strLocation = $arrLocation[0];
                $arrParts = explode('/', $strLocation);
                $strAdvocateToken = end($arrParts);

                //Updating the advocate currency
                $arrParams = array('currency_code' => 'USD');
                $objResponse = $this->objGeniusReferralsAPIClient->patchAdvocate('genius-referrals', $strAdvocateToken, $arrParams);
                $intResponseCode1 = $this->objGeniusReferralsAPIClient->getResponseCode();

//                if ($intResponseCode1 == '204') {
//                    $objCompany->setAdvocateToken($strAdvocateToken);
//                    $em = $this->container->get('doctrine')->getManager();
//                    $em->persist($objCompany);
//                    $em->flush();
//                }
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    public function searchAdvocates($data) {

        if (!empty($data['name'])) {
            $arrFilter[] = "name::" . $data['name'];
        }
        if (!empty($data['lastname'])) {
            $arrFilter[] = "lastname::" . $data['last_name'];
        }
        if (!empty($data['email'])) {
            $arrFilter[] = "email::" . $data['email'];
        }
        if (!empty($arrFilter)) {
            $filters = implode('|', $arrFilter);
        }
        $arrAdvocate = $this->objGeniusReferralsAPIClient->getAdvocates('genius-referrals', 1, 20, $filters);
        $arrAdvocate = json_decode($arrAdvocate);
        return $arrAdvocate->data->results;
    }

//    public function createReferral($data) {
//
//        //loading parameter from session
//        $strGRAdvocateReferrerToken = $data['strGRAdvocateReferrerToken'];
//        $strGRCampaignSlug = $data['strGRCampaignSlug'];
//        $strGRReferralOriginSlug = $data['strGRReferralOriginSlug'];
//
//        try {
//            //preparing the data to be sent on the request
//            $arrReferral = array("referral" => array(
//                    "referred_advocate_token" => $strGRAdvocateToken,
//                    "referral_origin_slug" => $strGRCampaignSlug,
//                    "campaign_slug" => $strGRReferralOriginSlug,
//                    "http_referer" => $_SERVER['HTTP_REFERER']
//            ));
//            $this->objGeniusReferralsAPIClient->postReferral('genius-referrals', $strGRAdvocateReferrerToken, $arrReferral);
//            $intResponseCode = $this->objGeniusReferralsAPIClient->getResponseCode();
//
//            if ($intResponseCode == '201') {
//                return true;
//            } else {
//                return false;
//            }
//        } catch (Exception $exc) {
//            echo $exc->getMessage();
//        }
//    }

//    public function processBonus($data) {
//
//        try {
//            //preparing the data to be sent on the request
//            $arrReferral = array("bonus" => array(
//                    "advocate_token" => $data['advocate_token'],
//                    "reference" => $data['reference'],
//                    "amount_of_payments" => $data['amount_payments'],
//                    "payment_amount" => $data['payment_amount']
//            ));
//
//            //trying to give a bonus to the advocate's referrer
//            $strResponse = $this->objGeniusReferralsAPIClient->postBonuses('genius-referrals', $arrReferral);
//            $intResponseCode = $this->objGeniusReferralsAPIClient->getResponseCode();
//
//            if ($intResponseCode == '201') {
//                // bonus given to the advocate's referrer
//                return true;
//            } else {
//                // there is not need to give a bonus to the advocate's referrer
//                return false;
//            }
//        } catch (Exception $exc) {
//
//            echo $exc->getMessage();
//        }
//    }

//    public function checkupBonus($data) {
//
//        try {
//            //preparing the data to be sent on the request
//            $arrReferral = array("bonus" => array(
//                    "advocate_token" => $data['advocate_token'],
//                    "reference" => $data['reference'],
//                    "amount_of_payments" => $data['amount_payments'],
//                    "payment_amount" => $data['payment_amount']
//            ));
//
//            //trying to give a bonus to the advocate's referrer
//            $strResponse = $this->objGeniusReferralsAPIClient->getBonusesCheckup('genius_referrals', $arrReferral);
//            $intResponseCode = $this->objGeniusReferralsAPIClient->getResponseCode();
//
//            if ($intResponseCode == '200') {
//                // bonus given to the advocate's referrer
//                return true;
//            } else {
//                // there is not need to give a bonus to the advocate's referrer
//                return false;
//            }
//        } catch (Exception $exc) {
//
//            echo $exc->getMessage();
//        }
//    }

    protected function success($data) {
        // use the global response object
        // this way, other methods can add to it if needed
        $this->response->success = TRUE;
        $this->response->message = $data;

        die(json_encode($this->response));
    }

    protected function failure($data) {
        // use the global response object
        // this way, other methods can add to it if needed
        $this->response->success = FALSE;
        $this->response->message = $data;

        die(json_encode($this->response));
    }

}

$ajax = new manage_advocate_ajax($_GET['method']);

