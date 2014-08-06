<?php

require_once '../vendor/autoload.php';

use GeniusReferrals\GRPHPAPIClient;
use Guzzle\Http\Client;

/**
 * All ajax traffic comes through here.
 */
class refer_friend_program_ajax {

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

        session_start();

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
     * Create paypal account.
     * 
     * @param  array $data *must be* $_POST['data']
     * @return string JSON string of response object
     */
    public function createPaypalAccount($data) {

        $strPaypalUsername = $data['paypal_username'];
        $strPaypalDescription = $data['paypal_description'];
        $boolPaypalActive = $data['paypal_is_active'];

        if (!empty($_SESSION['advocate_token'])) {

            $strGRAdvocateToken = $_SESSION['advocate_token'];

            if ($boolPaypalActive === '1') {

                $response = $this->objGeniusReferralsAPIClient->getAdvocatePaymentMethods($this->strAccount, $strGRAdvocateToken, 1, 50, 'is_active::true');
                $arrPaymentMethodsTrue = json_decode($response);

                if (!empty($arrPaymentMethodsTrue->data->results)) {
                    foreach ($arrPaymentMethodsTrue->data->results as $obj) {
                        $aryPaymentMethodData = array('advocate_payment_method' => array(
                                'username' => $obj->username,
                                'description' => $obj->description));
                        $this->objGeniusReferralsAPIClient->putAdvocatePaymentMethod($this->strAccount, $strGRAdvocateToken, $obj->id, $aryPaymentMethodData);
                    }
                }
            }
            if ($boolPaypalActive === '1') {
                $aryPaymentMethodData = array('advocate_payment_method' => array(
                        'username' => $strPaypalUsername,
                        'description' => $strPaypalDescription,
                        'is_active' => true));
            } else {
                $aryPaymentMethodData = array('advocate_payment_method' => array(
                        'username' => $strPaypalUsername,
                        'description' => $strPaypalDescription));
            }
            $objResponse = $this->objGeniusReferralsAPIClient->postAdvocatePaymentMethod($this->strAccount, $strGRAdvocateToken, $aryPaymentMethodData);
            $intResponseCode = $this->objGeniusReferralsAPIClient->getResponseCode();

            if ($intResponseCode == '201') {
                $aryPaymentMethods = $this->objGeniusReferralsAPIClient->getAdvocatePaymentMethods($this->strAccount, $strGRAdvocateToken, 1, 50);
                $aryPaymentMethods = json_decode($aryPaymentMethods);
                return $this->success($aryPaymentMethods->data->results);
            }
        }
    }

    /**
     * Activate or desactivate paypal account.
     * 
     * @param  array $data *must be* $_POST['data']
     * @return string JSON string of response object
     */
    public function activateDesactivatePaypalAccount($data) {
        $strPaypalUsername = $data['username'];
        $strPaypalDescription = $data['description'];
        $boolPaypalActive = $data['is_active'];
        $intPaymentMethodId = $data['payment_method_id'];

        if (!empty($_SESSION['advocate_token'])) {

            $strGRAdvocateToken = $_SESSION['advocate_token'];

            if ($boolPaypalActive === '1') {
                $response = $this->objGeniusReferralsAPIClient->getAdvocatePaymentMethods($this->strAccount, $strGRAdvocateToken, 1, 50, 'is_active::true');
                $arrPaymentMethodsTrue = json_decode($response);

                if (!empty($arrPaymentMethodsTrue->data->results)) {
                    foreach ($arrPaymentMethodsTrue->data->results as $obj) {
                        $aryPaymentMethodData = array('advocate_payment_method' => array(
                                'username' => $obj->username,
                                'description' => $obj->description));
                        $this->objGeniusReferralsAPIClient->putAdvocatePaymentMethod($this->strAccount, $strGRAdvocateToken, $obj->id, $aryPaymentMethodData);
                    }
                }
            }
            if ($boolPaypalActive === '1') {
                $aryPaymentMethodData = array('advocate_payment_method' => array(
                        'username' => $strPaypalUsername,
                        'description' => $strPaypalDescription,
                        'is_active' => true));
            } else {
                $aryPaymentMethodData = array('advocate_payment_method' => array(
                        'username' => $strPaypalUsername,
                        'description' => $strPaypalDescription));
            }
            $this->objGeniusReferralsAPIClient->putAdvocatePaymentMethod($this->strAccount, $strGRAdvocateToken, $intPaymentMethodId, $aryPaymentMethodData);
            $intResponseCode = $this->objGeniusReferralsAPIClient->getResponseCode();

            if ($intResponseCode == '204') {
                $aryPaymentMethods = $this->objGeniusReferralsAPIClient->getAdvocatePaymentMethods($this->strAccount, $strGRAdvocateToken, 1, 50);
                $aryPaymentMethods = json_decode($aryPaymentMethods);
                return $this->success($aryPaymentMethods->data->results);
            }
        }
    }

    /**
     * Redeem bonuses.
     * 
     * @param  array $data *must be* $_POST['data']
     * @return string JSON string of response object
     */
    public function redeemBonuses($data) {

        $strGRAdvocateToken = $_SESSION['advocate_token'];
        $intAmountRedeem = $data['amount_redeem'];
        $strRedemptionType = $data['redemption_type'];
        $strPaypalAccount = $data['paypal_account'];

        if ($strPaypalAccount != '') {

            $arrRedemptionRequest = array("redemption_request" => array(
                    "advocate_token" => $strGRAdvocateToken,
                    "request_status_slug" => "requested",
                    "request_action_slug" => $strRedemptionType,
                    "currency_code" => "USD",
                    "amount" => $intAmountRedeem,
                    "description" => "cash o pay-out",
                    "advocates_paypal_username" => $strPaypalAccount
            ));
        } else {
            $arrRedemptionRequest = array("redemption_request" => array(
                    "advocate_token" => $strGRAdvocateToken,
                    "request_status_slug" => "requested",
                    "request_action_slug" => $strRedemptionType,
                    "currency_code" => "USD",
                    "amount" => $intAmountRedeem,
                    "description" => "cash o pay-out",
            ));
        }

        $objResponse = $this->objGeniusReferralsAPIClient->postRedemptionRequest($this->strAccount, $arrRedemptionRequest);
        $intResponseCode = $this->objGeniusReferralsAPIClient->getResponseCode();

        if ($intResponseCode == '201') {

            $arrLocation = $objResponse->getHeader('Location')->raw();
            $strLocation = $arrLocation[0];
            $arrParts = explode('/', $strLocation);
            $intRedemptionRequestId = end($arrParts);

            $objRedemptionRequest = $this->objGeniusReferralsAPIClient->getRedemptionRequest($this->strAccount, $intRedemptionRequestId);
            $objRedemptionRequest = json_decode($objRedemptionRequest);
            return $this->success($objRedemptionRequest->data);
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
$ajax = new refer_friend_program_ajax($_GET['method']);

