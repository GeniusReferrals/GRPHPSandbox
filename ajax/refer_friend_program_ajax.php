<?php

require_once '../vendor/autoload.php';

use GeniusReferrals\GRPHPAPIClient;

class refer_friend_program_ajax {

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

    public function createPaypalAccount($data) {

        $strPaypalUsername = $data['paypal_username'];
        $strPaypalDescription = $data['paypal_description'];
        $boolPaypalActive = $data['paypal_is_active'];

        try {
            if (!empty($_SESSION['strAdvocateToken'])) {

                $strGRAdvocateToken = $_SESSION['strAdvocateToken'];

                if ($boolPaypalActive === '1') {

                    $response = $this->objGeniusReferralsAPIClient->getAdvocatePaymentMethods('genius-referrals', $strGRAdvocateToken, 1, 50, 'is_active::true');
                    $arrPaymentMethodsTrue = json_decode($response);

                    if (!empty($arrPaymentMethodsTrue->data->results)) {
                        foreach ($arrPaymentMethodsTrue->data->results as $obj) {
                            $aryPaymentMethodData = array('advocate_payment_method' => array(
                                    'username' => $obj->username,
                                    'description' => $obj->description));
                            $this->objGeniusReferralsAPIClient->putAdvocatePaymentMethod('genius-referrals', $strGRAdvocateToken, $obj->id, $aryPaymentMethodData);
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
                $objResponse = $this->objGeniusReferralsAPIClient->postAdvocatePaymentMethod('genius-referrals', $strGRAdvocateToken, $aryPaymentMethodData);
                $intResponseCode = $this->objGeniusReferralsAPIClient->getResponseCode();

                if ($intResponseCode == '201') {
                    $aryPaymentMethods = $this->objGeniusReferralsAPIClient->getAdvocatePaymentMethods('genius-referrals', $strGRAdvocateToken, 1, 50);
                    $aryPaymentMethods = json_decode($aryPaymentMethods);
                    return $aryPaymentMethods->data->results;
                }
            }
        } catch (Exception $exc) {
            $this->failure($exc->getMessage());
        }
    }

    public function activateDesactivatePaypalAccount($data) {

        $strPaypalUsername = $data['username'];
        $strPaypalDescription = $data['description'];
        $boolPaypalActive = $data['is_active'];
        $intPaymentMethodId = $data['payment_method_id'];

        try {
            if (!empty($_SESSION['strAdvocateToken'])) {

                $strGRAdvocateToken = $_SESSION['strAdvocateToken'];

                if ($boolPaypalActive === '1') {
                    $response = $this->objGeniusReferralsAPIClient->getAdvocatePaymentMethods('genius-referrals', $strGRAdvocateToken, 1, 50, 'is_active::true');
                    $arrPaymentMethodsTrue = json_decode($response);

                    if (!empty($arrPaymentMethodsTrue->data->results)) {
                        foreach ($arrPaymentMethodsTrue->data->results as $obj) {
                            $aryPaymentMethodData = array('advocate_payment_method' => array(
                                    'username' => $obj->username,
                                    'description' => $obj->description));
                            $this->objGeniusReferralsAPIClient->putAdvocatePaymentMethod('genius-referrals', $strGRAdvocateToken, $obj->id, $aryPaymentMethodData);
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
                $this->objGeniusReferralsAPIClient->putAdvocatePaymentMethod('genius-referrals', $strGRAdvocateToken, $intPaymentMethodId, $aryPaymentMethodData);
                $intResponseCode = $this->objGeniusReferralsAPIClient->getResponseCode();

                if ($intResponseCode == '204') {
                    $aryPaymentMethods = $this->objGeniusReferralsAPIClient->getAdvocatePaymentMethods('genius-referrals', $strGRAdvocateToken, 1, 50);
                    $aryPaymentMethods = json_decode($aryPaymentMethods);
                    return $aryPaymentMethods->data->results;
                }
            }
        } catch (Exception $exc) {
            $this->failure($exc->getMessage());
        }
    }

    public function redeemBonuses($data) {

        $strGRAdvocateToken = $_SESSION['strAdvocateToken'];
        $intAmountRedeem = $data['amount_redeem'];
        $strRedemptionType = $data['redemption_type'];
        $strPaypalAccount = $data['paypal_account'];

        //preparing the data to be sent on the request
        $arrRedemptionRequest = array("redemption_request" => array(
                "advocate_token" => $strGRAdvocateToken,
                "request_status_slug" => "requested",
                "request_action_slug" => $strRedemptionType,
                "currency_code" => "USD",
                "amount" => $intAmountRedeem,
                "description" => "cash o pay-out",
                "advocates_paypal_username" => $strPaypalAccount
        ));

        try {
            $objResponse = $this->objGeniusReferralsAPIClient->postRedemptionRequest('genius-referrals', $arrRedemptionRequest);
            $intResponseCode = $this->objGeniusReferralsAPIClient->getResponseCode();

            if ($intResponseCode == '201') {

                $arrLocation = $objResponse->getHeader('Location')->raw();
                $strLocation = $arrLocation[0];
                $arrParts = explode('/', $strLocation);
                $intRedemptionRequestId = end($arrParts);

                $objRedemptionRequest = $this->objGeniusReferralsAPIClient->getRedemptionRequest('genius-referrals', $intRedemptionRequestId);
                $objRedemptionRequest = json_decode($objRedemptionRequest);
                return $objRedemptionRequest->data;
            }
        } catch (Exception $exc) {
            $this->failure($exc->getMessage());
        }
    }

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

$ajax = new refer_friend_program_ajax($_GET['method']);

