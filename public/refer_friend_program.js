
$(document).ready(function() {

    $('#paypal_account_actions').click(function(e) {
        var stepRequest = $.ajax({
            type: "GET",
            url: 'paypal_account_list.php'
        });
        $('#paypalAccountModal').modal('show');
        stepRequest.done(function(response) {
            if (response) {
                $('#paypalAccountModal').html(response);
            }
        });
    });

    $('#btn_redeem_bonuses').click(function(e) {
        var isValid = validate();
        if (isValid) {
            var data = {
                amount_redeem: $('#amount_redeem').val(),
                redemption_type: $('#redemption_type').val(),
                paypal_account: $('#paypal_account').val()
            };
            var stepRequest = $.ajax({
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
            stepRequest.done(function(data) {
                var data = jQuery.parseJSON(data);
                if (data.success) {
                    $.each(data.message, function(i, elem) {
                        row_redemption = $('<tr>' +
                                '<td>' + elem.created + '</td>' +
                                '<td>' + elem.amount + '</td>' +
                                '<td> Referral </td>' +
                                '<td>' + elem._advocate.name + '</td>' +
                                '<td>' + elem.request_status_slug + '</td>' +
                                '<td>' + elem.request_action_slug + '</td>' +
                                '</tr>');
                        $('#table_redemption').append(row_redemption);
                    });
                    $('#amount_redeem').val('');
                    $('#redemption_type').val();
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