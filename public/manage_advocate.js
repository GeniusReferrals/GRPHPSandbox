
$(document).ready(function() {

    $('#btn_new_advocate').click(function() {
        $('#new_advocate_container').show();
    });

    $('#btn_close_advocate').click(function() {
        $('#new_advocate_container').hide();
    });

    $('#btn1_new_advocate').click(function() {
        var isValid = validateNewAdvocate();
        if (isValid) {
            var data = {
                name: $('#name').val(),
                last_name: $('#last_name').val(),
                email: $('#email').val(),
            };
            var stepRequest = $.ajax({
                url: 'ajax/manage_advocate_ajax.php?method=createAdvocate',
                data: {'data': data},
                type: 'POST',
                beforeSend: function() {
                    $('#btn1_new_advocate').button('loading');
                    $('#btn1_new_advocate').removeClass('btn-primary');
                    $('#btn1_new_advocate').addClass('btn-info');
                },
                complete: function() {
                    $('#btn1_new_advocate').button('reset');
                    $('#btn1_new_advocate').removeClass('btn-info');
                    $('#btn1_new_advocate').addClass('btn-primary');
                }
            });
            stepRequest.done(function(data) {
                var data = jQuery.parseJSON(data);
                if (data.success) {
//                    if (data.message._campaign_contract.name === 'undefined')
                    campaign_contract = '';
//                    else
//                        campaign_contract = data.message._campaign_contract.name;
                    row_advocate = $('<tr>' +
                            '<td>' + data.message.name + '</td>' +
                            '<td>' + data.message.lastname + '</td>' +
                            '<td>' + data.message.email + '</td>' +
                            '<td>' + data.message.email + '</td>' +
                            '<td>' + campaign_contract + '</td>' +
                            '<td>' + data.message.created + '</td>' +
                            '<td>Actions</td>' +
                            '</tr>');
                    $('#table_advocate').append(row_advocate);
                    $('#name').val('');
                    $('#last_name').val('');
                    $('#email').val('');
                    $('#new_advocate_container').hide();
                }
            });
        }
    });

    $('#btn_search_advocate').click(function() {
        var isValid = validateSearchAdvocate();
        if (isValid) {
            var data = {
                name: $('#inputName').val(),
                last_name: $('#inputLastname').val(),
                email: $('#inputEmail').val()
            };
            var stepRequest = $.ajax({
                url: 'ajax/manage_advocate_ajax.php?method=searchAdvocates',
                data: {'data': data},
                type: 'POST',
                beforeSend: function() {
                    $('#btn_search_advocate').button('loading');
                    $('#btn_search_advocate').removeClass('btn-primary');
                    $('#btn_search_advocate').addClass('btn-info');
                },
                complete: function() {
                    $('#btn_search_advocate').button('reset');
                    $('#btn_search_advocate').removeClass('btn-info');
                    $('#btn_search_advocate').addClass('btn-primary');
                }
            });
            stepRequest.done(function(data) {
                var data = jQuery.parseJSON(data);
                if (data.success) {
                    $('#table_advocate td').remove();
                    $.each(data.message, function(i, elem) {
//                        if (elem._campaign_contract.name != '')
//                            campaign_contract = elem._campaign_contract.name;
//                        else
                        campaign_contract = '';
                        row_advocate = $('<tr>' +
                                '<td>' + elem.name + '</td>' +
                                '<td>' + elem.lastname + '</td>' +
                                '<td>' + elem.email + '</td>' +
                                '<td>' + elem.email + '</td>' +
                                '<td>' + campaign_contract + '</td>' +
                                '<td>' + elem.created + '</td>' +
                                '<td>Actions</td>' +
                                '</tr>');
                        $('#table_advocate').append(row_advocate);
                    });
                    $('#inputName').val('');
                    $('#inputLastname').val('');
                    $('#inputEmail').val('');
                }
            });
        }
    });
});

$('.create_referral').click(function(e) {
    e.preventDefault();
    advocate_token = $(this).attr('id');

    var stepRequest = $.ajax({
        type: "GET",
        url: 'create_referral.php',
        data: {'advocate_token': advocate_token}
    });
    $('#createReferralModal').modal('show');
    stepRequest.done(function(response) {
        if (response) {
            $('#createReferralModal').html(response);
        }
    });
});

$('.checkup_bonus').click(function(e) {
    e.preventDefault();
    advocate_token = $(this).attr('id');

    var stepRequest = $.ajax({
        type: "GET",
        url: 'checkup_bonus.php',
        data: {'advocate_token': advocate_token}
    });
    $('#checkupBonusModal').modal('show');
    stepRequest.done(function(response) {
        if (response) {
            $('#checkupBonusModal').html(response);
            $('#checkupBonusModal').html(response);
            $('#checkupBonusModal #reference').val('');
            $('#checkupBonusModal #amount_payments').val('');
            $('#checkupBonusModal #payment_amount').val('');
            $('#checkupBonusModal #container_status_success').css('display', 'none');
            $('#checkupBonusModal #container_status_fail').css('display', 'none');
        }
    });
});

$('.process_bonus').click(function(e) {
    e.preventDefault();
    advocate_token = $(this).attr('id');

    var stepRequest = $.ajax({
        type: "GET",
        url: 'process_bonus.php',
        data: {'advocate_token': advocate_token}
    });
    $('#processBonusModal').modal('show');
    stepRequest.done(function(response) {
        if (response) {
            $('#processBonusModal').html(response);
            $('#processBonusModal #reference').val('');
            $('#processBonusModal #amount_payments').val('');
            $('#processBonusModal #payment_amount').val('');
            $('#processBonusModal #container_status_success').css('display', 'none');
            $('#processBonusModal #container_status_fail').css('display', 'none');
        }
    });
});

function validateNewAdvocate()
{
    $('#form_new_advocate').validate({
        rules: {
            'name': {required: true},
            'last_name': {required: true},
            'email': {required: true, email: true}
        }
    });
    return $('#form_new_advocate').valid();
}

function validateSearchAdvocate()
{
    $('#form_seach_advocate').validate({
        rules: {
            'inputEmail': {email: true}
        }
    });
    return $('#form_seach_advocate').valid();
}