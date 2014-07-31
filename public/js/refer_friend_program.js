
$(document).ready(function() {

    /**
     * Show or hide paypal account.
     */
    $('select#redemption_type').change(function() {
        if ($(this).val() == 'pay-out')
        {
            document.getElementById('paypal_account').selectedIndex = 0;
            $('#container_paypal_account').attr('style', 'display:block');
        }
        else
        {
            document.getElementById('paypal_account').selectedIndex = 0;
            $('#container_paypal_account').attr('style', 'display:none');
        }
    });

    /**
     * Load modal paypal account.
     */
    $('#paypal_account_actions').click(function(e) {
        var request = $.ajax({
            type: "GET",
            url: 'paypal_account_list.php'
        });
        $('#paypalAccountModal').modal('show');
        request.done(function(response) {
            if (response) {
                $('#paypalAccountModal').html(response);
            }
        });
    });

    /**
     * Redeem bonuses.
     */
    $('#btn_redeem_bonuses').click(function(e) {
        var isValid = validate();
        if (isValid) {
            var data = {
                amount_redeem: $('#amount_redeem').val(),
                redemption_type: $('#redemption_type').val(),
                paypal_account: $('#paypal_account').val()
            };
            var request = $.ajax({
                url: 'ajax/refer_friend_program_ajax.php?method=redeemBonuses',
                data: {'data': data},
                type: 'POST',
                beforeSend: function() {
                    $('#btn_redeem_bonuses').button('loading');
                    $('#btn_redeem_bonuses').removeClass('btn-primary');
                    $('#btn_redeem_bonuses').addClass('btn-info');
                },
                complete: function() {
                    $('#btn_redeem_bonuses').button('reset');
                    $('#btn_redeem_bonuses').removeClass('btn-info');
                    $('#btn_redeem_bonuses').addClass('btn-primary');
                }
            });
            request.done(function(data) {
                var data = jQuery.parseJSON(data);
                if (data.success) {
                    row_redemption = $('<tr>' +
                            '<td>' + dateFormat(new Date(data.message.created), "mediumDate") + '</td>' +
                            '<td>' + data.message.amount + '</td>' +
                            '<td> Referral </td>' +
                            '<td>' + data.message._advocate.name + '</td>' +
                            '<td>' + data.message.request_status_slug + '</td>' +
                            '<td>' + data.message.request_action_slug + '</td>' +
                            '</tr>');
                    $('#table_redemption').append(row_redemption);

                    $('#amount_redeem').val('');
                    document.getElementById('redemption_type').selectedIndex = 0;
                    document.getElementById('paypal_account').selectedIndex = 0;
                }
            });
        }
    });

    $('#referral_tools_next').click(function() {
        $('#overview_tab').removeClass('active');
        $('#referral_tools_tab').addClass('active');
        $('#bonuses_earned_tab').removeClass('active');
        $('#redeem_bonuses_tab').removeClass('active');

        $('#content_tab_overview').removeClass('active');
        $('#content_tab_referral_tools').addClass('active');
        $('#content_tab_bonuses_earned').removeClass('active');
        $('#content_tab_redeem_bonuses').removeClass('active');
    });
    $('#bonuses_earned_next').click(function() {
        $('#overview_tab').removeClass('active');
        $('#referral_tools_tab').removeClass('active');
        $('#bonuses_earned_tab').addClass('active');
        $('#redeem_bonuses_tab').removeClass('active');

        $('#content_tab_overview').removeClass('active');
        $('#content_tab_referral_tools').removeClass('active');
        $('#content_tab_bonuses_earned').addClass('active');
        $('#content_tab_redeem_bonuses').removeClass('active');
    });
    $('#redeem_bonuses_next').click(function() {
        $('#overview_tab').removeClass('active');
        $('#referral_tools_tab').removeClass('active');
        $('#bonuses_earned_tab').removeClass('active');
        $('#redeem_bonuses_tab').addClass('active');

        $('#content_tab_overview').removeClass('active');
        $('#content_tab_referral_tools').removeClass('active');
        $('#content_tab_bonuses_earned').removeClass('active');
        $('#content_tab_redeem_bonuses').addClass('active');
    });

    /**
     * Load graph share daily participation.
     */
    flag_shares_participation = true;
    $('#shares_participation_tab').on('shown.bs.tab', function(e) {

        if (flag_shares_participation)
        {
            var request = $.ajax({
                type: "POST",
                data: {'data': ''},
                url: 'ajax/refer_friend_program_ajax.php?method=getShareDailyParticipation'
            });
            request.done(function(data) {
                var data = jQuery.parseJSON(data);
                $('#averages_share_daily_participation').attr('data-averages-share', data.message[0]);
                $('#totals_share_daily_participation').attr('data-totals-share', data.message[1]);

                generateChartPie("pie-chart-share-daily", $('#averages_share_daily_participation').data('averages-share'));
                generateChartSerial("serial-chart-share-daily", $('#totals_share_daily_participation').data('totals-share'));

                flag_shares_participation = false;
            });
        }

    });

    /**
     * Load graph click daily participation.
     */
    flag_clicks_participation = true;
    $('#clicks_participation_tab').on('shown.bs.tab', function(e) {

        if (flag_clicks_participation)
        {
            var request = $.ajax({
                type: "POST",
                data: {'data': ''},
                url: 'ajax/refer_friend_program_ajax.php?method=getClickDailyParticipation'
            });
            request.done(function(data) {
                var data = jQuery.parseJSON(data);
                $('#averages_click_daily_participation').attr('data-averages-click', data.message[0]);
                $('#totals_click_daily_participation').attr('data-totals-click', data.message[1]);

                generateChartPie("pie-chart-click-daily", $('#averages_click_daily_participation').data('averages-click'));
                generateChartSerial("serial-chart-click-daily", $('#totals_click_daily_participation').data('totals-click'));

                flag_clicks_participation = false;
            });
        }
    });

    /**
     * Load graph referral daily participation.
     */
    flag_referral_participation = true;
    $('#referral_participation_tab').on('shown.bs.tab', function(e) {

        if (flag_referral_participation)
        {
            var request = $.ajax({
                type: "POST",
                data: {'data': ''},
                url: 'ajax/refer_friend_program_ajax.php?method=getReferralDailyParticipation'
            });
            request.done(function(data) {
                var data = jQuery.parseJSON(data);
                $('#averages_daily_participation').attr('data-averages-participation', data.message[0]);
                $('#totals_daily_participation').attr('data-totals-participation', data.message[1]);

                generateChartPie("pie-chart-referral", $('#averages_daily_participation').data('averages-participation'));
                generateChartSerial("serial-chart-referral", $('#totals_daily_participation').data('totals-participation'));

                flag_referral_participation = false;
            });
        }
    });

    /**
     * Load graph bonuses daily given.
     */
    flag_bonuses_given = true;
    $('#bonuses_given_tab').on('shown.bs.tab', function(e) {

        if (flag_bonuses_given)
        {
            var request = $.ajax({
                type: "POST",
                data: {'data': ''},
                url: 'ajax/refer_friend_program_ajax.php?method=getBonusesDailyGiven'
            });
            request.done(function(data) {
                var data = jQuery.parseJSON(data);
                $('#averages_bonuses_daily_given').attr('data-averages-bonuses', data.message[0]);
                $('#totals_bonuses_daily_given').attr('data-totals-bonuses', data.message[1]);

                generateChartPie("pie-chart-bonuses", $('#averages_bonuses_daily_given').data('averages-bonuses'));
                generateChartSerial("serial-chart-bonuses", $('#totals_bonuses_daily_given').data('totals-bonuses'));

                flag_bonuses_given = false;
            });
        }
    });

});

/**
 * Validate form_redeem_bonuses.
 */
function validate()
{
    $('#form_redeem_bonuses').validate({
        rules: {
            'amount_redeem': {required: true},
            'redemption_type': {required: true},
            'paypal_account': {required: true, email: true}
        }
    });
    return $('#form_redeem_bonuses').valid();
}