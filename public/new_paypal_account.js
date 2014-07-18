
$(document).ready(function() {

    $('#btn_new_paypal_account').click(function(e) {

        var isValid = validatePaypalAccount();
        if (isValid) {
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
            stepRequest.done(function(data) {
                var data = jQuery.parseJSON(data);
                if (data.success) {
                    $('#paypal_account').append('<option value="' + data.message.username + '">' + data.message.username + '</option>');

                    $('#table_payment td').remove();
                    $.each(data.message, function(i, elem) {
                        if (elem.is_active == 0)
                        {
                            icon_is_active = 'glyphicon glyphicon-remove-circle';
                            state = 1;
                            title = 'Active';
                        }

                        else
                        {
                            icon_is_active = 'glyphicon glyphicon-check';
                            state = 0;
                            title = 'Desactive';
                        }
                        row_account = $('<tr>' +
                                '<td>' + elem.description + '</td>' +
                                '<td>' + elem.username + '</td>' +
                                '<td><span class="' + icon_is_active + '"></span></td>' +
                                '<td class="actions">' +
                                '<a type="button" id="' + elem.id + '" data-loading-text="Loading..." data-name="' + elem.description + '" data-email="' + elem.username + '" data-state="' + state + '" class="activate_desactivate" onclick="activateDesactivate(\'' + elem.id + '-' + elem.description + '-' + elem.username + '-' + state + '\')">' + title + '</a>' +
                                '</td>' +
                                '</tr>');
                        $('#table_payment').append(row_account);
                    });

                    $('#form_paypal_account #paypal_username').val('');
                    $('#form_paypal_account #paypal_description').val('');
                    document.getElementById('paypal_is_active').selectedIndex = 0;
                    $('#newPaypalAccountModal').modal('hide');
                }
            });
        }
    });
});

function validatePaypalAccount()
{
    $('#form_paypal_account').validate({
        rules: {
            'paypal_username': {required: true, email: true},
            'paypal_description': {required: true},
            'paypal_is_active': {required: true}
        }
    });
    return $('#form_paypal_account').valid();
}
