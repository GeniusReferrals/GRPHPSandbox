
$(document).ready(function() {

    $('#btn_new_paypal_account').click(function(e) {
        var data = {
            paypal_username: $('#paypal_username').val(),
            paypal_description: $('#paypal_description').val(),
            paypal_is_active: $('#paypal_is_active').val()
        };
        var stepRequest = $.ajax({
            type: "POST",
            url: 'ajax/refer_friend_program_ajax.php?method=createPaypalAccount',
            data: {'data': data},
            beforeSend: function() {
                $('#btn_new_paypal_account').button('loading');
                $('#btn_new_paypal_account').removeClass('btn-primary');
                $('#btn_new_paypal_account').addClass('btn-info');
            },
            complete: function() {
                $('#btn_new_paypal_account').button('reset');
                $('#btn_new_paypal_account').removeClass('btn-info');
                $('#btn_new_paypal_account').addClass('btn-primary');
            }
        });
        stepRequest.done(function(response) {
            if (response) {
                $('#div_table_payment').html(response);
                if ($('#redeem_bonuses_paypal_account option').length == 0)
                    $('#redeem_bonuses_paypal_account').append('<option value="">Choose</option>');
                $('#redeem_bonuses_paypal_account').append('<option value="' + $('#form_paypal_account #paypal_username').val() + '">' + $('#form_paypal_account #paypal_username').val() + '</option>');
                $('#form_paypal_account #paypal_username').val('');
                $('#form_paypal_account #paypal_description').val('');
                document.getElementById('paypal_is_active').selectedIndex = 0;
                $('#newPaypalAccountModal').modal('hide');
            }
        });
    });
});