<?php
//$strAdvocateToken = $_GET['advocate_token'];
//$_SESSION['strAdvocateToken'] = $strAdvocateToken;
session_start();
$_SESSION['strAdvocateToken'] = '312313131313131';

//include './api/refer_friend_program_api.php';
//$api = new refer_friend_program_api();
//$arrAdvocatesShareLinks = $api->getAdvocatesShareLinks($strAdvocateToken);
//$arrReferralsSummaryPerOriginReport = $api->getReferralsSummaryPerOriginReport($strAdvocateToken);
//$arrBonusesSummaryPerOriginReport = $api->getBonusesSummaryPerOriginReport($strAdvocateToken);
//$objAdvocate = $api->getAdvocate($strAdvocateToken);
//$arrRedemptionRequests = $api->getRedemptionRequests($strAdvocateToken);
//$arrAdvocatePaymentMethods = $api->getAdvocatePaymentMethods($strAdvocateToken);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>GRPHPSandbox</title>

        <link href="public/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <link href="public/bootstrap/css/jumbotron-narrow.css" rel="stylesheet">

        <link href="public/css/styles.css" rel="stylesheet">

    </head>

    <body>

        <div class="container">
            <div class="header">
                <ul class="nav nav-pills pull-right">
                    <li class="active"><a href="index.php">Manage advocate</a></li>
                </ul>
                <h3 class="text-muted">GRPHPSandbox</h3>
            </div>

            <div class="row marketing">
                <div class="col-xs-12">
                    <ul class="nav nav-tabs">
                        <li id="overview_tab" class="active">
                            <a href="#content_tab_overview" data-toggle="tab">Overview</a>
                        </li>
                        <li id="referral_tools_tab">
                            <a href="#content_tab_referral_tools" data-toggle="tab">Referral tools</a>
                        </li>
                        <li id="bonuses_earned_tab">
                            <a href="#content_tab_bonuses_earned" data-toggle="tab">Bonuses earned</a>
                        </li>
                        <li id="redeem_bonuses_tab">
                            <a href="#content_tab_redeem_bonuses" data-toggle="tab">Redeem bonuses</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="content_tab_overview">
                            <?php include './tab_overview.php'; ?>
                        </div>
                        <div class="tab-pane" id="content_tab_referral_tools">
                            <?php include './tab_referral_tools.php'; ?>
                        </div>
                        <div class="tab-pane" id="content_tab_bonuses_earned">
                            <?php include './tab_bonuses_earned.php'; ?>
                        </div>
                        <div class="tab-pane" id="content_tab_redeem_bonuses">
                            <?php include './tab_redeem_bonuses.php'; ?>
                        </div>
                    </div>

                    <div class="modal fade" id="paypalAccountModal" tabindex="-1" role="dialog" aria-labelledby="paypalAccountLabel" aria-hidden="true"></div>
                    <div class="modal fade" id="newPaypalAccountModal" tabindex="-1" role="dialog" aria-labelledby="newPaypalAccountLabel" aria-hidden="true"></div>
                </div>
            </div>

            <div class="footer">
                <ul class="nav nav-pills pull-left">
                    <li class="active"><a href="index.php">Manage advocate</a></li>
                </ul>
                <div style="clear: both; text-align: center;">
                    <p>Copyright ©2014 GRPHPSandbox. All rights reserved.</p>
                </div>
            </div>

            <input type="hidden" id="data" data-amcharts-images="public/images/amcharts/"/>
            <input type="hidden" id="averages_share_daily_participation" data-averages-share=""/>
            <input type="hidden" id="totals_share_daily_participation" data-totals-share=""/>
            <input type="hidden" id="averages_click_daily_participation" data-averages-click=""/>
            <input type="hidden" id="totals_click_daily_participation" data-totals-click=""/>
            <input type="hidden" id="averages_daily_participation" data-averages-participation=""/>
            <input type="hidden" id="totals_daily_participation" data-totals-participation=""/>
            <input type="hidden" id="averages_bonuses_daily_given" data-averages-bonuses=""/>
            <input type="hidden" id="totals_bonuses_daily_given" data-totals-bonuses=""/>

        </div> <!-- /container -->

        <script src="public/js/jquery-2.0.3.min.js"></script>

        <script src="public/js/jquery.validate.min.js"></script>

        <script src="public/js/jquery.validate.defaults.js"></script>

        <script src="public/bootstrap/js/bootstrap.min.js"></script>

        <script src="public/js/date.format.js"></script>
        
        <script src="public/js/amcharts/amcharts.js"></script>
        <script src="public/js/amcharts/pie.js"></script>
        <script src="public/js/amcharts/serial.js"></script>
        <script src="public/js/charts.js"></script>
        
        <script src="public/js/refer_friend_program.js"></script>

        <script src="public/js/paypal_account_list.js"></script>

    </body>
</html>

