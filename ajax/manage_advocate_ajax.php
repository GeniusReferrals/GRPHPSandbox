<?php

require_once '../vendor/autoload.php';

use GeniusReferrals\GRPHPAPIClient;

/**
 * All ajax traffic comes through here.
 */
class manage_advocate_ajax {

    protected $response;
    protected $objGeniusReferralsAPIClient;
    protected $strUsername;
    protected $strAuthToken;
    protected $strAccount;
    protected $strCampaign;
    protected $strWidgetsPackage;

    /**
     * when ajax traffic hits, instantiate this class
     * passing in the name of the method to run
     * this is a pseudo controller
     *
     * @param  string $method. The name of the class method to run
     */
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

    /**
     * Create advocate.
     * 
     * @param  array $data *must be* $_POST['data']
     * @return string JSON string of response object
     */
    public function createAdvocate($data) {

        $filters = "email::" . $data['email'];

        $arrAdvocate = $this->objGeniusReferralsAPIClient->getAdvocates($this->strAccount, 1, 1, $filters);
        $arrAdvocate = json_decode($arrAdvocate);

        if ($arrAdvocate->data->total == 0) {
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
        } else {
            return $this->failure('The email of advocate must be unique.');
        }
    }

    /**
     * Search advocates.
     * 
     * @param  array $data *must be* $_POST['data']
     * @return string JSON string of response object
     */
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

        $arrAdvocate = $this->objGeniusReferralsAPIClient->getAdvocates($this->strAccount, 1, 50, $filters);
        $arrAdvocate = json_decode($arrAdvocate);
        return $this->success($arrAdvocate->data);
    }

    /**
     * Search advocate referer.
     * 
     * @param  array $data *must be* $_POST['data']
     * @return string JSON string of response object
     */
    public function searchAdvocateReferer($data) {

        $filters = "email::" . $data['email'];

        $arrAdvocate = $this->objGeniusReferralsAPIClient->getAdvocates($this->strAccount, 1, 50, $filters);
        $arrAdvocate = json_decode($arrAdvocate);

        $arrEmail = array();
        if (!empty($arrAdvocate->data->results)) {
            foreach ($arrAdvocate->data->results as $objAdvocate) {
                $arrEmail[] = $objAdvocate->email;
            }
        }

        die(json_encode($arrEmail));
    }

    /**
     * Add referrer.
     * 
     * @param  array $data *must be* $_POST['data']
     * @return string JSON string of response object
     */
    public function createReferral($data) {


        $strAdvocateToken = $data['advocate_token'];
        $strFilters = array();
        $strFilters['email'] = $data['email_advocate_referrer'];

        $objAdvocate = $this->objGeniusReferralsAPIClient->getAdvocates($this->strAccount, 1, 1, $strFilters);
        $objAdvocate = json_decode($objAdvocate);

        $arrReferral = array("referral" => array(
                "referred_advocate_token" => $strAdvocateToken,
                "referral_origin_slug" => $data['referral_origin_slug'],
                "campaign_slug" => $data['campaign_slug'],
                "http_referer" => $_SERVER['HTTP_REFERER']
        ));

        $objResponse = $this->objGeniusReferralsAPIClient->postReferral($this->strAccount, $objAdvocate->data->results[0]->token, $arrReferral);
        $intResponseCode = $this->objGeniusReferralsAPIClient->getResponseCode();
        if ($intResponseCode == '201') {
            return $this->success(array('OK'));
        }
    }

    /**
     * Give bonus.
     * 
     * @param  array $data *must be* $_POST['data']
     * @return string JSON string of response object
     */
    public function processBonus($data) {

        //preparing the data to be sent on the request
        if (empty($data['amount_of_payments']) && empty($data['payment_amount'])) {
            $arrBonus = array('bonus' => array('advocate_token' => $data['advocate_token'],
                    'reference' => $data['reference']));
        } else if (empty($data['amount_of_payments'])) {
            $arrBonus = array('bonus' => array('advocate_token' => $data['advocate_token'],
                    'reference' => $data['reference'],
                    'payment_amount' => $data['payment_amount']));
        } else if (empty($data['payment_amount'])) {
            $arrBonus = array('bonus' => array('advocate_token' => $data['advocate_token'],
                    'reference' => $data['reference'],
                    'amount_of_payments' => $data['amount_payments']));
        } else {
            $arrBonus = array('bonus' => array('advocate_token' => $data['advocate_token'],
                    'reference' => $data['reference'],
                    'amount_of_payments' => $data['amount_payments'],
                    'payment_amount' => $data['payment_amount']));
        }

        //trying to give a bonus to the advocate's referrer
        $objResponse = $this->objGeniusReferralsAPIClient->postBonuses($this->strAccount, $arrBonus);
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

    /**
     * Check bonus.
     * 
     * @param  array $data *must be* $_POST['data']
     * @return string JSON string of response object
     */
    public function checkupBonus($data) {

        //preparing the data to be sent on the request
        if (empty($data['amount_payments']) && empty($data['payment_amount'])) {
            $arrReferral = array('advocate_token' => $data['advocate_token'],
                'reference' => $data['reference']);
        } else if (empty($data['amount_payments'])) {
            $arrReferral = array('advocate_token' => $data['advocate_token'],
                'reference' => $data['reference'],
                'payment_amount' => $data['payment_amount']);
        } else if (empty($data['payment_amount'])) {
            $arrReferral = array('advocate_token' => $data['advocate_token'],
                'reference' => $data['reference'],
                'amount_of_payments' => $data['amount_payments']);
        } else {
            $arrReferral = array('advocate_token' => $data['advocate_token'],
                'reference' => $data['reference'],
                'amount_of_payments' => $data['amount_payments'],
                'payment_amount' => $data['payment_amount']);
        }

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
        }
    }

    /**
     * will die exporting the json string of
     * $this->response
     *
     * sets success to true
     *
     * @param  array $data 
     * @return void
     */
    protected function success($data) {
        // use the global response object
        // this way, other methods can add to it if needed
        $this->response->success = TRUE;
        $this->response->message = $data;

        die(json_encode($this->response));
    }

    /**
     * same as self::success yet sets success to false
     *
     * @param  array $data 
     * @return void
     */
    protected function failure($data) {
        // use the global response object
        // this way, other methods can add to it if needed
        $this->response->success = FALSE;
        $this->response->message = $data;

        die(json_encode($this->response));
    }

}

/**
 * This is the key to the ignition
 * start a new instance of the ajax controller, and call the method specified
 * @var ajax_controller
 */
$ajax = new manage_advocate_ajax($_GET['method']);

