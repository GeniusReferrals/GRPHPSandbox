<?php

require_once '../vendor/autoload.php';

use GeniusReferrals\GRPHPAPIClient;

class manage_advocate_ajax {

    protected $response;
    protected $objGeniusReferralsAPIClient;
    protected $strUsername;
    protected $strAuthToken;
    protected $strAccount;
    protected $strCampaign;
    protected $strWidgetsPackage;


    public function __construct($method = NULL) {

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

        $objResponse = $this->objGeniusReferralsAPIClient->postAdvocate($this->strAccount, $strAdvocateData);
        $intResponseCode = $this->objGeniusReferralsAPIClient->getResponseCode();

        if ($intResponseCode == '201') {
            //getting the advocate token from the Location header
            $arrLocation = $objResponse->getHeader('Location')->raw();
            $strLocation = $arrLocation[0];
            $arrParts = explode('/', $strLocation);
            $strAdvocateToken = end($arrParts);

            //Updating the advocate currency
            $arrParams = array('currency_code' => 'USD');
            $objResponse = $this->objGeniusReferralsAPIClient->patchAdvocate($this->strAccount, $strAdvocateToken, $arrParams);
            $intResponseCode1 = $this->objGeniusReferralsAPIClient->getResponseCode();

            if ($intResponseCode1 == '204') {
                $objAdvocate = $this->objGeniusReferralsAPIClient->getAdvocate($this->strAccount, $strAdvocateToken);
                $objAdvocate = json_decode($objAdvocate);
                return $this->success($objAdvocate->data);
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
            $arrAdvocate = $this->objGeniusReferralsAPIClient->getAdvocates($this->strAccount, 1, 50, $filters);
        else
            $arrAdvocate = $this->objGeniusReferralsAPIClient->getAdvocates($this->strAccount, 1, 50);

        $arrAdvocate = json_decode($arrAdvocate);
        return $this->success($arrAdvocate->data->results);
    }

    public function searchAdvocateReferer($data) {

        $filters = "email::" . $data['email'];

        $arrAdvocate = $this->objGeniusReferralsAPIClient->getAdvocates($this->strAccount, 1, 50, $filters);

        $arrEmail = array();
        if (!empty($arrAdvocate->data->results)) {
            foreach ($arrAdvocate->data->results as $objAdvocate) {
                $arrEmail[] = $objAdvocate->email;
            }
        }
        $arrEmail = json_decode($arrEmail);
        return $this->success($arrEmail);
    }

    public function createReferral($data) {


        $strAdvocateToken = $data['advocate_token'];
        $strFilters = array();
        $strFilters['email'] = $data['email_advocate_referrer'];

        $objAdvocate = $this->objGeniusReferralsAPIClient->getAdvocates($this->strAccount, 1, 1, $strFilters);
        $objAdvocate = json_decode($objAdvocate);

        $aryReferrals = array();
        $aryReferrals['referred_advocate_token'] = $strAdvocateToken;
        $aryReferrals['referral_origin_slug'] = $data['referral_origin_slug'];
        $aryReferrals['campaign_slug'] = $data['campaign_slug'];
        $aryReferrals['http_referer'] = $_SERVER['HTTP_REFERER'];

        $objResponse = $this->objGeniusReferralsAPIClient->postReferral($this->strAccount, $objAdvocate->data->results[0]->token, $aryReferrals);
        $intResponseCode = $this->objGeniusReferralsAPIClient->getResponseCode();
        if ($intResponseCode == '201') {
            return $this->success(array('OK'));
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
        $objResponse = $this->objGeniusReferralsAPIClient->postBonuses($this->strAccount, $arrReferral);
        $intResponseCode = $this->objGeniusReferralsAPIClient->getResponseCode();

        if ($intResponseCode == '201') {
            $arrLocation = $objResponse->getHeader('Location')->raw();
            $strLocation = $arrLocation[0];
            $arrParts = explode('/', $strLocation);
            $intBonusId = end($arrParts);

            $objBonus = $this->objGeniusReferralsAPIClient->getBonus($this->strAccount, $intBonusId);
            $objBonus = json_decode($objBonus);
            $objAdvocateReferrer = $this->objGeniusReferralsAPIClient->getAdvocate($this->strAccount, $objBonus->data->referred_advocate_token);
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
        $responseCheckup = $this->objGeniusReferralsAPIClient->getBonusesCheckup($this->strAccount, $arrReferral);
        $objCheckup = json_decode($responseCheckup);
        $intResponseCode = $this->objGeniusReferralsAPIClient->getResponseCode();

        if ($intResponseCode == '200') {

            if (!empty($objCheckup->data->advocate_referrer_token)) {
                $objAdvocate = $this->objGeniusReferralsAPIClient->getAdvocate($this->strAccount, $objCheckup->data->advocate_referrer_token);
                $objAdvocate = json_decode($objAdvocate);
            }
            if (!empty($objCheckup->data->campaign_slug)) {
                $objCampaign = $this->objGeniusReferralsAPIClient->getCampaign($this->strAccount, $objCheckup->data->campaign_slug);
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

