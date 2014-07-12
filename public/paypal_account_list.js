
$(document).ready(function() {

    $('#new_paypal_account_ajax').click(function(e) {
        e.preventDefault();
        var stepRequest = $.ajax({
            type: "GET",
            url: 'new_paypal_account.php'
        });
        $('#newPaypalAccountModal').modal('show');
        stepRequest.done(function(response) {
            if (response) {
                $('#newPaypalAccountModal').html(response);
            }
        });
    });

    $('.activate_desactivate').click(function(e) {
        e.preventDefault();
        id = $(this).attr('id');
        var data = {
            payment_method_id: $(this).attr('id'),
            description: $(this).data('name'),
            username: $(this).data('email'),
            is_active: $(this).data('state')
        };
        var stepRequest = $.ajax({
            type: "POST",
            url: 'ajax/refer_friend_program_ajax.php?method=activateDesactivatePaypalAccount',
            data: {'data': data},
            beforeSend: function() {
                $('#' + id).button('loading');
            },
            complete: function() {
                $('#' + id).button('reset');
            }
        });
        stepRequest.done(function(response) {
            if (response) {
                $('#div_table_payment').html(response);
            }
        });
    });
});