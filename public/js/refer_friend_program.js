
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
});

/**
 * Validate form_redeem_bonuses.
 */
function validate()
{
    $('#form_redeem_bonuses').validate({
        rules: {
            'amount_redeem': {
                required: true,
                max: 20
            },
            'redemption_type': {required: true}
        }
    });
    return $('#form_redeem_bonuses').valid();
}