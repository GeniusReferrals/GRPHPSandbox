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

    public function searchAdvocates() {

        $strName = $data['name'];
        $strLastName = $data['last_name'];
        $strEmail = $data['email'];

        $arrAdvocate = $this->objGeniusReferralsAPIClient->getAdvocates('genius-referrals', 1, 20, 'name::'+$strName+'|lastname::'+$strLastName+'|email::'+$strEmail+'');
        $arrAdvocate = json_decode($arrAdvocate);
        return $arrAdvocate->data->results;
    }

    /**
     * this helper function allows other ajax methods
     * to know whether they have been passed all required post params
     *
     * @author Daniel Walker <daniel.walker@assistrx.com>
     * @since  5/15/13
     * @param  array $expected the keys required to be passed
     * @param  array $data the post data
     * @throws exception If a required key is not found in the data
     * @return void
     */
    protected function expects($expected, $data) {
        foreach ($expected as $variable_name) {
            if (!array_key_exists($variable_name, $data)) {
                throw new Exception("Error - you must pass {$variable_name} in \$_POST['data']");
            }
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

$ajax = new manage_advocate_ajax($_GET['method']);

