<?php
include './api/refer_friend_program_api.php';
$api = new refer_friend_program_api();
//$arrAdvocatesShareLinks = $api->getAdvocatesShareLinks();
//$arrReferralsSummaryPerOriginReport = $api->getReferralsSummaryPerOriginReport();
//$arrBonusesSummaryPerOriginReport = $api->getBonusesSummaryPerOriginReport();
//$objAdvocate = $api->getAdvocate();
//$arrRedemptionRequests = $api->getRedemptionRequests();
//$arrAdvocatePaymentMethods = $api->getAdvocatePaymentMethods()
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

        <!-- Bootstrap core CSS -->
        <link href="public/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="public/bootstrap/css/jumbotron-narrow.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="public/styles.css" rel="stylesheet">

    </head>

    <body>

        <div class="container">
            <div class="header">
                <ul class="nav nav-pills pull-right">
                    <li><a href="index.php">Manage advocate</a></li>
                    <li class="active"><a href="refer_friend_program.php">Refer a friend program</a></li>
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

                    <!--<input type="hidden" id="data" 
                           data-amcharts-images="{{ asset("bundles/frontend/js/amcharts/images/")}}" 
                           data-loader-url="{{ asset("bundles/frontend/images/loader2.gif") }}" 
                           data-base-url="{{ app.request.getBaseURL() }}"/>
                    <input type="hidden" id="averages_share_daily_participation" data-averages-share="{{averages_share_daily_participation}}"/>
                    <input type="hidden" id="totals_share_daily_participation" data-totals-share="{{totals_share_daily_participation}}"/>
                    <input type="hidden" id="averages_click_daily_participation" data-averages-click="{{averages_click_daily_participation}}"/>
                    <input type="hidden" id="totals_click_daily_participation" data-totals-click="{{totals_click_daily_participation}}"/>
                    <input type="hidden" id="averages_daily_participation" data-averages-participation="{{averages_daily_participation}}"/>
                    <input type="hidden" id="totals_daily_participation" data-totals-participation="{{totals_daily_participation}}"/>
                    <input type="hidden" id="averages_bonuses_daily_given" data-averages-bonuses="{{averages_bonuses_daily_given}}"/>
                    <input type="hidden" id="totals_bonuses_daily_given" data-totals-bonuses="{{totals_bonuses_daily_given}}"/>-->

                    <div class="modal fade" id="paypalAccountModal" tabindex="-1" role="dialog" aria-labelledby="paypalAccountLabel" aria-hidden="true"></div>
                    <div class="modal fade" id="newPaypalAccountModal" tabindex="-1" role="dialog" aria-labelledby="newPaypalAccountLabel" aria-hidden="true"></div>
                </div>
            </div>

            <div class="footer">
                <ul class="nav nav-pills pull-left">
                    <li><a href="index.php">Manage advocate</a></li>
                    <li><a href="refer_friend_program.php">Refer a friend program</a></li>
                </ul>
                <div style="clear: both; text-align: center;">
                    <p>Copyright ©2014 GRPHPSandbox. All rights reserved.</p>
                </div>
            </div>

        </div> <!-- /container -->

        <!-- scripts at the bottom! -->
        <script src="public/jquery-2.0.3.min.js"></script>

        <!-- this script file is for global js -->
        <script src="public/refer_friend_program.js"></script>

        <!-- add bootstrap js -->
        <script src="public/bootstrap/js/bootstrap.min.js"></script>

    </body>
</html>

