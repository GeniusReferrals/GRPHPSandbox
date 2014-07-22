<?php

require_once '../vendor/autoload.php';

use GeniusReferrals\GRPHPAPIClient;
use Guzzle\Http\Client;

class refer_friend_program_ajax {

    protected $response;
    protected $objGeniusReferralsAPIClient;
    protected $strUsername;
    protected $strAuthToken;
    protected $strAccount;
    protected $strCampaign;
    protected $strWidgetsPackage;
    protected $strApiUrl;
    protected $strApiTokenKey;
    protected $strApiTokenValue;

    public function __construct($method = NULL) {

        session_start();

        if (file_exists(__DIR__ . '/../config/config.php')) {
            require __DIR__ . '/../config/config.php';
            $this->strUsername = $apiConfig['gr_username'];
            $this->strAuthToken = $apiConfig['gr_auth_token'];
            $this->strAccount = $apiConfig['gr_rfp_account'];
            $this->strCampaign = $apiConfig['gr_rfp_campaign'];
            $this->strWidgetsPackage = $apiConfig['gr_rfp_widgets_package'];
            $this->strApiUrl = $apiConfig['api_url'];
            $this->strApiTokenKey = $apiConfig['api_token_key'];
            $this->strApiTokenValue = $apiConfig['api_token_value'];
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

    public function createPaypalAccount($data) {

        $strPaypalUsername = $data['paypal_username'];
        $strPaypalDescription = $data['paypal_description'];
        $boolPaypalActive = $data['paypal_is_active'];

        try {
            if (!empty($_SESSION['strAdvocateToken'])) {

                $strGRAdvocateToken = $_SESSION['strAdvocateToken'];

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
        } catch (Exception $exc) {
            $this->failure($exc->getMessage());
        }
    }

    public function getShareDailyParticipation($data) {
        $strGRAdvocateToken = $_SESSION['strAdvocateToken'];

        $aryShareDailyParticipation = $this->getShareDailyParticipationClient('', $this->strAccount, '', '', $strGRAdvocateToken);
        $aryShareDailyTotals = $this->convertReportShareDailyParticipationToArray($aryShareDailyParticipation->data);
        $aryShareDailyAverage = $this->getAverageShareDailyParticipation($aryShareDailyParticipation->data);

        return $this->success(array(json_encode($aryShareDailyAverage), json_encode($aryShareDailyTotals)));
    }

    public function getClickDailyParticipation($data) {
        $strGRAdvocateToken = $_SESSION['strAdvocateToken'];

        $aryClickDailyParticipation = $this->getClickDailyParticipationClient('', $this->strAccount, '', '', $strGRAdvocateToken);
        $aryClickDailyTotals = $this->convertReportClickDailyParticipationToArray($aryClickDailyParticipation->data);
        $aryClickDailyAverage = $this->getAverageClickDailyParticipation($aryClickDailyParticipation->data);

        return $this->success(array(json_encode($aryClickDailyAverage), json_encode($aryClickDailyTotals)));
    }

    public function getReferralDailyParticipation($data) {
        $strGRAdvocateToken = $_SESSION['strAdvocateToken'];

        $aryReferralDailyParticipation = $this->getReferralDailyParticipationClient('', $this->strAccount, '', '', $strGRAdvocateToken);
        $aryDailyParticipationTotals = $this->convertReportDataToArray($aryReferralDailyParticipation->data);
        $aryDailyParticipationAverage = $this->getAverage($aryReferralDailyParticipation->data);

        return $this->success(array(json_encode($aryDailyParticipationAverage), json_encode($aryDailyParticipationTotals)));
    }

    public function getBonusesDailyGiven($data) {
        $strGRAdvocateToken = $_SESSION['strAdvocateToken'];

        $aryBonusesDailyGiven = $this->getBonusesDailyGivenClient('', $this->strAccount, '', '', $strGRAdvocateToken);
        $aryBonusesDailyGivenTotals = $this->convertReportDataToArray($aryBonusesDailyGiven->data);
        $aryBonusesDailyGivenAverage = $this->getAverage($aryBonusesDailyGiven->data);

        return $this->success(array(json_encode($aryBonusesDailyGivenAverage), json_encode($aryBonusesDailyGivenTotals)));
    }

    private function getShareDailyParticipationClient($client_slug = '', $client_account_slug = '', $program_id = '', $campaign_slug = '', $advocate_token = '', $from = '', $to = '') {
        $client = new Client();
        $request = $client->get($this->strApiUrl . '/reports/share-daily-participation', array('HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
            $this->strApiTokenKey => $this->strApiTokenValue), array('query' => array('client_slug' => $client_slug,
                'client_account_slug' => $client_account_slug,
                'program_id' => $program_id,
                'campaign_slug' => $campaign_slug,
                'advocate_token' => $advocate_token,
                'from' => $from,
                'to' => $to)));
        $response = $request->send();
        return json_decode($response->getBody(true));
    }

    private function getClickDailyParticipationClient($client_slug = '', $client_account_slug = '', $program_id = '', $campaign_slug = '', $advocate_token = '', $from = '', $to = '') {
        $client = new Client();
        $request = $client->get($this->strApiUrl . '/reports/click-daily-participation', array('HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
            $this->strApiTokenKey => $this->strApiTokenValue), array('query' => array('client_slug' => $client_slug,
                'client_account_slug' => $client_account_slug,
                'program_id' => $program_id,
                'campaign_slug' => $campaign_slug,
                'advocate_token' => $advocate_token,
                'from' => $from,
                'to' => $to)));
        $response = $request->send();
        return json_decode($response->getBody(true));
    }

    private function getReferralDailyParticipationClient($client_slug = '', $client_account_slug = '', $program_id = '', $campaign_slug = '', $advocate_token = '', $from = '', $to = '') {
        $client = new Client();
        $request = $client->get($this->strApiUrl . '/reports/referral-daily-participation', array('HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
            $this->strApiTokenKey => $this->strApiTokenValue), array('query' => array('client_slug' => $client_slug,
                'client_account_slug' => $client_account_slug,
                'program_id' => $program_id,
                'campaign_slug' => $campaign_slug,
                'advocate_token' => $advocate_token,
                'from' => $from,
                'to' => $to)));
        $response = $request->send();
        return json_decode($response->getBody(true));
    }

    private function getBonusesDailyGivenClient($client_slug = '', $client_account_slug = '', $program_id = '', $campaign_slug = '', $advocate_token = '', $from = '', $to = '') {
        $client = new Client();
        $request = $client->get($this->strApiUrl . '/reports/bonuses-daily-given', array('HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
            $this->strApiTokenKey => $this->strApiTokenValue), array('query' => array('client_slug' => $client_slug,
                'client_account_slug' => $client_account_slug,
                'program_id' => $program_id,
                'campaign_slug' => $campaign_slug,
                'advocate_token' => $advocate_token,
                'from' => $from,
                'to' => $to)));
        $response = $request->send();
        return json_decode($response->getBody(true));
    }

    public function convertReportShareDailyParticipationToArray($aryShareDailyParticipation) {
        $aryData = array();
        $key = 0;
        for ($i = count($aryShareDailyParticipation) - 1; $i >= 0; $i--) {
            $aryData[$key]['facebook_share'] = $aryShareDailyParticipation[$i]->facebook_share;
            $aryData[$key]['twitter_post'] = $aryShareDailyParticipation[$i]->twitter_post;
            $aryData[$key]['google_plus'] = $aryShareDailyParticipation[$i]->google_plus;
            $aryData[$key]['linkedin_post'] = $aryShareDailyParticipation[$i]->linkedin_post;
            $aryData[$key]['pin_it'] = $aryShareDailyParticipation[$i]->pin_it;

            $date = new \DateTime($aryShareDailyParticipation[$i]->date);
            $year = $date->format('Y');
            $month = $date->format('m');
            $day = $date->format('d');

            $dateTime = new \DateTime();
            $dateTime->setDate($year, $month, $day + 1);
            $date_result = $dateTime->format('Y-m-d');

            $aryData[$key]['date'] = $date_result;
            $key++;
        }
        return $aryData;
    }

    public function convertReportClickDailyParticipationToArray($aryClickDailyParticipation) {
        $aryData = array();
        $key = 0;
        for ($i = count($aryClickDailyParticipation) - 1; $i >= 0; $i--) {
            $aryData[$key]['facebook_share'] = $aryClickDailyParticipation[$i]->facebook_share;
            $aryData[$key]['twitter_post'] = $aryClickDailyParticipation[$i]->twitter_post;
            $aryData[$key]['google_plus'] = $aryClickDailyParticipation[$i]->google_plus;
            $aryData[$key]['linkedin_post'] = $aryClickDailyParticipation[$i]->linkedin_post;
            $aryData[$key]['pin_it'] = $aryClickDailyParticipation[$i]->pin_it;
            $aryData[$key]['personal_url'] = $aryClickDailyParticipation[$i]->personal_url;
            $aryData[$key]['direct_email'] = $aryClickDailyParticipation[$i]->direct_email;

            $date = new \DateTime($aryClickDailyParticipation[$i]->date);
            $year = $date->format('Y');
            $month = $date->format('m');
            $day = $date->format('d');

            $dateTime = new \DateTime();
            $dateTime->setDate($year, $month, $day + 1);
            $date_result = $dateTime->format('Y-m-d');

            $aryData[$key]['date'] = $date_result;
            $key++;
        }
        return $aryData;
    }

    public function convertReportDataToArray($aryDailyParticipation) {
        $aryData = array();
        $key = 0;
        for ($i = count($aryDailyParticipation) - 1; $i >= 0; $i--) {
            $aryData[$key]['facebook_share'] = $aryDailyParticipation[$i]->facebook_share;
            $aryData[$key]['twitter_post'] = $aryDailyParticipation[$i]->twitter_post;
            $aryData[$key]['google_plus'] = $aryDailyParticipation[$i]->google_plus;
            $aryData[$key]['linkedin_post'] = $aryDailyParticipation[$i]->linkedin_post;
            $aryData[$key]['pin_it'] = $aryDailyParticipation[$i]->pin_it;
            $aryData[$key]['personal_url'] = $aryDailyParticipation[$i]->personal_url;
            $aryData[$key]['direct_email'] = $aryDailyParticipation[$i]->direct_email;
            $aryData[$key]['other'] = $aryDailyParticipation[$i]->other;

            $date = new \DateTime($aryDailyParticipation[$i]->date);
            $year = $date->format('Y');
            $month = $date->format('m');
            $day = $date->format('d');

            $dateTime = new \DateTime();
            $dateTime->setDate($year, $month, $day + 1);
            $date_result = $dateTime->format('Y-m-d');

            $aryData[$key]['date'] = $date_result;
            $key++;
        }
        return $aryData;
    }

    private function getAverageShareDailyParticipation($objData) {
        $aryAverage = array();
        $intFacebook = 0;
        $intTwitter = 0;
        $intGoogle = 0;
        $intLinkedIn = 0;
        $intPinIt = 0;
        foreach ($objData as $obj) {
            $intFacebook+=$obj->facebook_share;
            $intTwitter+=$obj->twitter_post;
            $intGoogle+=$obj->google_plus;
            $intLinkedIn+=$obj->linkedin_post;
            $intPinIt+=$obj->pin_it;
        }
        $intTotal = $intFacebook + $intTwitter + $intGoogle + $intLinkedIn + $intPinIt;
        $aryAverage[] = array('name' => 'Facebook share', 'value' => $intTotal === 0 ? 0 : round(($intFacebook * 100) / $intTotal, 2));
        $aryAverage[] = array('name' => 'Twitter post', 'value' => $intTotal === 0 ? 0 : round(($intTwitter * 100) / $intTotal, 2));
        $aryAverage[] = array('name' => 'Google plus', 'value' => $intTotal === 0 ? 0 : round(($intGoogle * 100) / $intTotal, 2));
        $aryAverage[] = array('name' => 'LinkedIn post', 'value' => $intTotal === 0 ? 0 : round(($intLinkedIn * 100) / $intTotal, 2));
        $aryAverage[] = array('name' => 'Pin it', 'value' => $intTotal === 0 ? 0 : round(($intPinIt * 100) / $intTotal, 2));
        return $aryAverage;
    }

    private function getAverageClickDailyParticipation($objData) {
        $aryAverage = array();
        $intFacebook = 0;
        $intTwitter = 0;
        $intGoogle = 0;
        $intLinkedIn = 0;
        $intPinIt = 0;
        $intPurl = 0;
        $intEmail = 0;
        foreach ($objData as $obj) {
            $intFacebook+=$obj->facebook_share;
            $intTwitter+=$obj->twitter_post;
            $intGoogle+=$obj->google_plus;
            $intLinkedIn+=$obj->linkedin_post;
            $intPinIt+=$obj->pin_it;
            $intPurl+=$obj->personal_url;
            $intEmail+=$obj->direct_email;
        }
        $intTotal = $intFacebook + $intTwitter + $intGoogle + $intLinkedIn + $intPinIt + $intPurl + $intEmail;
        $aryAverage[] = array('name' => 'Facebook share', 'value' => $intTotal === 0 ? 0 : round(($intFacebook * 100) / $intTotal, 2));
        $aryAverage[] = array('name' => 'Twitter post', 'value' => $intTotal === 0 ? 0 : round(($intTwitter * 100) / $intTotal, 2));
        $aryAverage[] = array('name' => 'Google plus', 'value' => $intTotal === 0 ? 0 : round(($intGoogle * 100) / $intTotal, 2));
        $aryAverage[] = array('name' => 'LinkedIn post', 'value' => $intTotal === 0 ? 0 : round(($intLinkedIn * 100) / $intTotal, 2));
        $aryAverage[] = array('name' => 'Pin it', 'value' => $intTotal === 0 ? 0 : round(($intPinIt * 100) / $intTotal, 2));
        $aryAverage[] = array('name' => 'PURL', 'value' => $intTotal === 0 ? 0 : round(($intPurl * 100) / $intTotal, 2));
        $aryAverage[] = array('name' => 'Email', 'value' => $intTotal === 0 ? 0 : round(($intEmail * 100) / $intTotal, 2));
        return $aryAverage;
    }

    private function getAverage($objData) {
        $aryAverage = array();
        $intFacebook = 0;
        $intTwitter = 0;
        $intGoogle = 0;
        $intLinkedIn = 0;
        $intPinIt = 0;
        $intPurl = 0;
        $intEmail = 0;
        $intOther = 0;
        foreach ($objData as $obj) {
            $intFacebook+=$obj->facebook_share;
            $intTwitter+=$obj->twitter_post;
            $intGoogle+=$obj->google_plus;
            $intLinkedIn+=$obj->linkedin_post;
            $intPinIt+=$obj->pin_it;
            $intPurl+=$obj->personal_url;
            $intEmail+=$obj->direct_email;
            $intOther+=$obj->other;
        }
        $intTotal = $intFacebook + $intTwitter + $intGoogle + $intLinkedIn + $intPinIt + $intPurl + $intEmail + $intOther;
        $aryAverage[] = array('name' => 'Facebook share', 'value' => $intTotal === 0 ? 0 : round(($intFacebook * 100) / $intTotal, 2));
        $aryAverage[] = array('name' => 'Twitter post', 'value' => $intTotal === 0 ? 0 : round(($intTwitter * 100) / $intTotal, 2));
        $aryAverage[] = array('name' => 'Google plus', 'value' => $intTotal === 0 ? 0 : round(($intGoogle * 100) / $intTotal, 2));
        $aryAverage[] = array('name' => 'LinkedIn post', 'value' => $intTotal === 0 ? 0 : round(($intLinkedIn * 100) / $intTotal, 2));
        $aryAverage[] = array('name' => 'Pin it', 'value' => $intTotal === 0 ? 0 : round(($intPinIt * 100) / $intTotal, 2));
        $aryAverage[] = array('name' => 'PURL', 'value' => $intTotal === 0 ? 0 : round(($intPurl * 100) / $intTotal, 2));
        $aryAverage[] = array('name' => 'Email', 'value' => $intTotal === 0 ? 0 : round(($intEmail * 100) / $intTotal, 2));
        $aryAverage[] = array('name' => 'Other', 'value' => $intTotal === 0 ? 0 : round(($intOther * 100) / $intTotal, 2));
        return $aryAverage;
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

