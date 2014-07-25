
$(document).ready(function() {

    /**
     * Load paginate.
     */
    if ($('#div_table_advocate #table_advocate tbody tr').length != 0)
    {
        $('#div_table_advocate').append($('.pagination'));
        $('.pagination').attr("style", "display: block; float: right;");
        $('#div_table_advocate').append($('<div style="clear: both;"></div>'));
    }

    /**
     * Show or hide div new advocate.
     */
    $('#btn_new_advocate').click(function() {
        $('#new_advocate_container').show();
    });
    $('#btn_close_advocate').click(function() {
        $('#new_advocate_container').hide();
    });

    /**
     * Create advocate.
     */
    $('#btn1_new_advocate').click(function() {
        var isValid = validateNewAdvocate();
        if (isValid) {
            var data = {
                name: $('#name').val(),
                last_name: $('#last_name').val(),
                email: $('#email').val()
            };
            var stepRequest = $.ajax({
                url: 'ajax/manage_advocate_ajax.php?method=createAdvocate',
                data: {'data': data},
                type: 'POST',
                beforeSend: function() {
                    $('#btn1_new_advocate').button('loading');
                    $('#btn1_new_advocate').removeClass('btn-primary');
                    $('#btn1_new_advocate').addClass('btn-info');
                }
            });
            stepRequest.done(function(data) {
                var data = jQuery.parseJSON(data);
                if (data.success) {
                    window.location = 'index.php';
                }
            });
        }
    });

    /**
     * Search advocate.
     */
    $('#btn_search_advocate').click(function() {
        if ($('#inputName').val() != '' || $('#inputLastname').val() != '' || $('#inputEmail').val() != '')
        {
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
                    $('#table_advocate td').remove();
                    if (data.message.total != 0)
                    {
                        $('#table_advocate').append('<tbody id="example4">');
                        $.each(data.message.results, function(i, elem) {
                            if (typeof elem._campaign_contract === 'undefined')
                                campaign_contract = '';
                            else
                                campaign_contract = elem._campaign_contract.name;
                            row_advocate1 = $('<tr>' +
                                    '<td>' + elem.name + '</td>' +
                                    '<td>' + elem.lastname + '</td>' +
                                    '<td>' + elem.email + '</td>' +
                                    '<td> Genius referrals </td>' +
                                    '<td>' + campaign_contract + '</td>' +
                                    '<td>' + dateFormat(new Date(elem.created), "mediumDate") + '</td>');
                            row_advocate2 = $('<td class="actions">' +
                                    '<a id="' + elem.token + '" href="refer_friend_program.php?advocate_token=' + elem.token + '" title="Refer a friend program" data-toggle="modal"><span class="glyphicon glyphicon-chevron-down"></span></a>' +
                                    '<a id="' + elem.token + '" href="#" title="Create referrer" data-toggle="modal" onclick="createReferral(\'' + elem.token + '\')"><span class="glyphicon glyphicon-pencil"></span></a>');
                            row_advocate3 = $('<a id="' + elem.token + '" href="#" title="Process bonus" data-toggle="modal" onclick="processBonus(\'' + elem.token + '\')"><span class="glyphicon glyphicon-retweet"></span></a>' +
                                    '<a id="' + elem.token + '" href="#" title="Checkup bonus" data-toggle="modal" onclick="checkupBonus(\'' + elem.token + '\')"><span class="glyphicon glyphicon-check"></span></a>');

                            $('#table_advocate').append(row_advocate1);
                            row_advocate1.append(row_advocate2);
                            if (typeof elem._advocate_referrer !== 'undefined')
                                row_advocate2.append(row_advocate3);
                        });

                        $('#no_result_found').hide();
                        $('#unknow_error').hide();
                    }
                    else
                    {
                        $('#no_result_found').show();
                        $('#unknow_error').hide();
                    }

                    $('.pagination').remove();
                });
                stepRequest.fail(function(data) {
                    $('#no_result_found').hide();
                    $('#unknow_error').show();

                    $('.pagination').remove();
                });
            }
        }
    });
});

/**
 * Validate form_new_advocate.
 */
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

/**
 * Validate form_seach_advocate.
 */
function validateSearchAdvocate()
{
    $('#form_seach_advocate').validate({
        rules: {
            'inputEmail': {email: true}
        }
    });
    return $('#form_seach_advocate').valid();
}

/**
 * Load modal add referrer.
 */
function createReferral(advocate_token)
{
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
}

/**
 * Load modal checkup bonus.
 */
function checkupBonus(advocate_token)
{
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
}

/**
 * Load modal process bonus.
 */
function processBonus(advocate_token)
{
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
}
