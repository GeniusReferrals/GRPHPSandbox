<?php

require_once '../vendor/autoload.php';

use GeniusReferrals\GRPHPAPIClient;

class manage_advocate_ajax {

    protected $response;
    protected $objGeniusReferralsAPIClient;

    public function __construct($method = NULL) {

        session_start();

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

        //preparing the data to be sent on the request
        $strAdvocateData = array('advocate' => array(
                'name' => $data['name'],
                'lastname' => $data['last_name'],
                'email' => $data['email'],
                'payout_threshold' => 20));

        $objResponse = $this->objGeniusReferralsAPIClient->postAdvocate('genius-referrals', $strAdvocateData);
        $intResponseCode = $this->objGeniusReferralsAPIClient->getResponseCode();

        if ($intResponseCode == '201') {
            //getting the advocate token from the Location header
            $arrLocation = $objResponse->getHeader('Location')->raw();
            $strLocation = $arrLocation[0];
            $arrParts = explode('/', $strLocation);
            $strAdvocateToken = end($arrParts);

            $_SESSION['strAdvocateToken'] = $strAdvocateToken;

            //Updating the advocate currency
            $arrParams = array('currency_code' => 'USD');
            $objResponse = $this->objGeniusReferralsAPIClient->patchAdvocate('genius-referrals', $strAdvocateToken, $arrParams);
            $intResponseCode1 = $this->objGeniusReferralsAPIClient->getResponseCode();

            if ($intResponseCode1 == '204') {
                $objAdvocate = $this->objGeniusReferralsAPIClient->getAdvocate('genius-referrals', $strAdvocateToken);
                $objAdvocate = json_decode($objAdvocate);
                $this->success($objAdvocate->data);
            }
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

        if (!empty($filters))
            $arrAdvocate = $this->objGeniusReferralsAPIClient->getAdvocates('genius-referrals', 1, 50, $filters);
        else
            $arrAdvocate = $this->objGeniusReferralsAPIClient->getAdvocates('genius-referrals', 1, 50);
        $arrAdvocate = json_decode($arrAdvocate);
        $this->success($arrAdvocate->data->results);
    }

    public function createReferral($data) {

        if (!empty($_SESSION['strAdvocateToken'])) {

            $strAdvocateToken = $_SESSION['strAdvocateToken'];
            $strFilters = array();
            $strFilters['email'] = $data['email_advocate_referrer'];

            $objAdvocate = $this->objGeniusReferralsAPIClient->getAdvocates('genius-referral', 1, 1, $strFilters);
            $objAdvocate = json_decode($objAdvocate);
            
            $aryReferrals = array();
            $aryReferrals['referred_advocate_token'] = $strAdvocateToken;
            $aryReferrals['referral_origin_slug'] = $data['referral_origin_slug'];
            $aryReferrals['campaign_slug'] = $data['campaign_slug'];
            $aryReferrals['http_referer'] = $_SERVER['HTTP_REFERER'];

            $objResponse = $this->objGeniusReferralsAPIClient->postReferral('genius-referral', $objAdvocate->data->results[0]->token, $aryReferrals);
            $intResponseCode = $this->objGeniusReferralsAPIClient->getResponseCode();
        }
        if ($intResponseCode == '201') {
            $this->success(array('OK'));
        }
    }

    public function processBonus($data) {

        //preparing the data to be sent on the request
        $arrReferral = array("bonus" => array(
                "advocate_token" => $data['advocate_token'],
                "reference" => $data['reference'],
                "amount_of_payments" => $data['amount_payments'],
                "payment_amount" => $data['payment_amount']
        ));

        //trying to give a bonus to the advocate's referrer
        $objResponse = $this->objGeniusReferralsAPIClient->postBonuses('genius-referrals', $arrReferral);
        $intResponseCode = $this->objGeniusReferralsAPIClient->getResponseCode();

        if ($intResponseCode == '201') {
            $arrLocation = $objResponse->getHeader('Location')->raw();
            $strLocation = $arrLocation[0];
            $arrParts = explode('/', $strLocation);
            $intBonusId = end($arrParts);

            $objBonus = $this->objGeniusReferralsAPIClient->getBonus('genius-referrals', $intBonusId);
            $objBonus = json_decode($objBonus);
            $objAdvocateReferrer = $this->objGeniusReferralsAPIClient->getAdvocate('genius-referrals', $objBonus->data->referred_advocate_token);
            $objAdvocateReferrer = json_decode($objAdvocateReferrer);

            return $this->success(array('status' => 'Success',
                        'bonus_amount' => $objBonus->data->amount,
                        'advocates_referrer_token' => $objBonus->data->referred_advocate_token,
                        'advocates_referrer_name' => $objAdvocateReferrer->data->name));
        } else {
            return $this->success(array('status' => 'Fail'));
        }
    }

    public function checkupBonus($data) {

        //preparing the data to be sent on the request
        $arrReferral = array("bonus" => array(
                "advocate_token" => $data['advocate_token'],
                "reference" => $data['reference'],
                "amount_of_payments" => $data['amount_payments'],
                "payment_amount" => $data['payment_amount']
        ));

        //trying to give a bonus to the advocate's referrer
        $responseCheckup = $this->objGeniusReferralsAPIClient->getBonusesCheckup('genius_referrals', $arrReferral);
        echo'<pre>';
        print_r($responseCheckup);die;
        $objCheckup = json_decode($responseCheckup);
        $intResponseCode = $this->objGeniusReferralsAPIClient->getResponseCode();

        if ($intResponseCode == '200') {

            if (!empty($objCheckup->data->advocate_referrer_token)) {
                $objAdvocate = $this->objGeniusReferralsAPIClient->getAdvocate('genius-referrals', $objCheckup->data->advocate_referrer_token);
                $objAdvocate = json_decode($objAdvocate);
            }
            if (!empty($objCheckup->data->campaign_slug)) {
                $objCampaign = $this->objGeniusReferralsAPIClient->getCampaign('genius-referrals', $objCheckup->data->campaign_slug);
                $objCampaign = json_decode($objCampaign);
            }

            return $this->success(array('status' => $objCheckup->data->result,
                        'reference' => $objCheckup->data->reference,
                        'referrer_name' => isset($objAdvocate->data->name) ? $objAdvocate->data->name : '',
                        'referrer_slug' => isset($objAdvocate->data->token) ? $objAdvocate->data->token : '',
                        'campaing_name' => isset($objCampaign->data->name) ? $objCampaign->data->name : '',
                        'campaing_slug' => isset($objCampaign->data->slug) ? $objCampaign->data->slug : '',
                        'message' => $objCheckup->data->message,
                        'trace' => isset($objCheckup->data->trace) ? $objCheckup->data->trace : array()
            ));
        } else {
            return $this->success(array('status' => 'Fail'));
        }
    }

    protected function success($data) {
        $this->response->success = TRUE;
        $this->response->message = $data;

        die(json_encode($this->response));
    }

    protected function failure($data) {
        $this->response->success = FALSE;
        $this->response->message = $data;

        die(json_encode($this->response));
    }

}

$ajax = new manage_advocate_ajax($_GET['method']);

