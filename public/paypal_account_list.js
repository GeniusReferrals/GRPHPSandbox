
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
        stepRequest.done(function(data) {
            var data = jQuery.parseJSON(data);
            if (data.success) {
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
                            '<a type="button" id="' + elem.id + '" data-loading-text="Loading..." data-name="' + elem.description + '" data-email="' + elem.username + '" data-state="' + state + '" class="activate_desactivate" onclick="activateDesactivate(\''+ elem.id +'-'+ elem.description +'-'+ elem.username +'-'+ state +'\')">' + title + '</a>' +
                            '</td>' +
                            '</tr>');
                    $('#table_payment').append(row_account);
                });
            }
        });
    });
});

function activateDesactivate(data) {

    result = data.split('-');
    payment_method_id = result[0];
    description = result[1];
    username = result[2];
    is_active = result[3];
    
    id = payment_method_id;
    var data = {
        payment_method_id: payment_method_id,
        description: description,
        username: username,
        is_active: is_active
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
    stepRequest.done(function(data) {
        var data = jQuery.parseJSON(data);
        if (data.success) {
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
                        '<a type="button" id="' + elem.id + '" data-loading-text="Loading..." data-name="' + elem.description + '" data-email="' + elem.username + '" data-state="' + state + '" class="activate_desactivate" onclick="activateDesactivate(\''+ elem.id +'-'+ elem.description +'-'+ elem.username +'-'+ state +'\')">' + title + '</a>' +
                        '</td>' +
                        '</tr>');
                $('#table_payment').append(row_account);
            });
        }
    });
}