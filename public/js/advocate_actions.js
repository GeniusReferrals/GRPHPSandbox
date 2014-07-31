$(document).ready(function() {

    /**
     * Add referrer.
     */
    $('#btn_create_referral').click(function(e) {
        e.preventDefault();
        var isValid = validateCreateReferral();
        if (isValid)
        {
            advocate_token = $('input#advocate_token').val();
            email_advocate_referrer = $('input#advocate_referrer').val();
            campaign_slug = $(' select#campaing :selected').val();
            referral_origin_slug = $('select#network :selected').val();

            var request = $.ajax({
                type: "POST",
                url: 'ajax/manage_advocate_ajax.php?method=createReferral',
                data: {'data': {'advocate_token': advocate_token,
                        'email_advocate_referrer': email_advocate_referrer,
                        'campaign_slug': campaign_slug,
                        'referral_origin_slug': referral_origin_slug}},
                beforeSend: function() {
                    $('#btn_create_referral').button('loading');
                    $('#btn_create_referral').removeClass('btn-primary');
                    $('#btn_create_referral').addClass('btn-info');
                },
                complete: function() {
                    $('#btn_create_referral').button('reset');
                    $('#btn_create_referral').removeClass('btn-info');
                    $('#btn_create_referral').addClass('btn-primary');
                }
            });
            request.done(function(data) {
                $('#createReferralModal #advocate_referrer').val('');
                document.getElementById('campaing').selectedIndex = 0;
                document.getElementById('network').selectedIndex = 0;

                $('#createReferralModal').modal('hide');
            });
        }
    });

    /**
     * Checkup bonus.
     */
    $('#btn_checkup_bonus').click(function(e) {
        e.preventDefault();

        $('#checkupBonusModal #status_success span#lb_status').html('');
        $('#checkupBonusModal #status_success span#lb_reference').html('');
        $('#checkupBonusModal #status_success .advocate_details').html('');
        $('#checkupBonusModal #status_success .advocate_details').attr('id', '');
        $('#checkupBonusModal #status_success .btn-details-campaign').html('');
        $('#checkupBonusModal #status_success .btn-details-campaign').attr('id', '');
        $('#checkupBonusModal #status_success span#lb_message').html('');
        $('#checkupBonusModal #container_status_success #div_trace ul').html('');

        $('#checkupBonusModal #status_fail span#lb_status').html('');
        $('#checkupBonusModal #status_fail span#lb_reference').html('');
        $('#checkupBonusModal #status_fail .advocate_details').html('');
        $('#checkupBonusModal #status_fail .advocate_details').attr('id', '');
        $('#checkupBonusModal #status_fail .btn-details-campaign').html('');
        $('#checkupBonusModal #status_fail .btn-details-campaign').attr('id', '');
        $('#checkupBonusModal #status_fail span#lb_message').html('');
        $('#checkupBonusModal #container_status_fail #div_trace ul').html('');

        $('#checkupBonusModal #container_status_fail #div_trace').css('display', 'none');
        $('#checkupBonusModal #container_status_success #div_trace').css('display', 'none');
        $('#checkupBonusModal #container_status_success').css('display', 'none');
        $('#checkupBonusModal #container_status_fail').css('display', 'none');

        var isValid = validateCheckupBonus();
        if (isValid)
        {
            advocate_token = $('input#advocate_token').val();
            reference = $('#checkupBonusModal #reference').val();
            amount_payments = $('#checkupBonusModal #amount_payments').val();
            payment_amount = $('#checkupBonusModal #payment_amount').val();

            var request = $.ajax({
                type: "POST",
                url: 'ajax/manage_advocate_ajax.php?method=checkupBonus',
                data: {'data': {'reference': reference,
                        'amount_payments': amount_payments,
                        'payment_amount': payment_amount,
                        'advocate_token': advocate_token}},
                beforeSend: function() {
                    $('#btn_checkup_bonus').button('loading');
                    $('#btn_checkup_bonus').removeClass('btn-primary');
                    $('#btn_checkup_bonus').addClass('btn-info');
                },
                complete: function() {
                    $('#btn_checkup_bonus').button('reset');
                    $('#btn_checkup_bonus').removeClass('btn-info');
                    $('#btn_checkup_bonus').addClass('btn-primary');
                }
            });
            request.done(function(data) {
                var data = jQuery.parseJSON(data);
                if (data.success) {
                    if (data.message.status == 'success') {
                        $('#checkupBonusModal #status_success span#lb_status').html('Success');
                        $('#checkupBonusModal #status_success span#lb_reference').html(data.message.reference);
                        $('#checkupBonusModal #status_success .advocate_details').html(data.message.referrer_name);
                        $('#checkupBonusModal #status_success .advocate_details').attr('id', data.message.referrer_slug);
                        $('#checkupBonusModal #status_success .btn-details-campaign').html(data.message.campaing_name);
                        $('#checkupBonusModal #status_success .btn-details-campaign').attr('id', data.message.campaing_slug);
                        $('#checkupBonusModal #status_success span#lb_message').html(data.message.message);
                        $('#checkupBonusModal #container_status_success #div_trace ul').html('');
                        if (data.message.trace != '')
                        {
                            $.each(data.message.trace, function(i, elem) {
                                li = $('<li></li>').html(elem);
                                $('#checkupBonusModal #container_status_success #div_trace ul').append(li);
                            });
                            $('#checkupBonusModal #container_status_success #div_trace').css('display', 'block');
                            $('#checkupBonusModal #container_status_fail #div_trace').css('display', 'none');
                        }
                        $('#checkupBonusModal #container_status_success').css('display', 'block');
                        $('#checkupBonusModal #container_status_fail').css('display', 'none');
                    }
                    else if (data.message.status == 'fail') {
                        $('#checkupBonusModal #status_fail span#lb_status').html('Fail');
                        $('#checkupBonusModal #status_fail span#lb_reference').html(data.message.reference);
                        $('#checkupBonusModal #status_fail .advocate_details').html(data.message.referrer_name);
                        $('#checkupBonusModal #status_fail .advocate_details').attr('id', data.message.referrer_slug);
                        $('#checkupBonusModal #status_fail .btn-details-campaign').html(data.message.campaing_name);
                        $('#checkupBonusModal #status_fail .btn-details-campaign').attr('id', data.message.campaing_slug);
                        $('#checkupBonusModal #status_fail span#lb_message').html(data.message.message);
                        $('#checkupBonusModal #container_status_fail #div_trace ul').html('');
                        if (data.message.trace != '')
                        {
                            $.each(data.message.trace, function(i, elem) {
                                li = $('<li></li>').html(elem);
                                $('#checkup_bonus #container_status_fail #div_trace ul').append(li);
                            });
                            $('#checkupBonusModal #container_status_fail #div_trace').css('display', 'block');
                            $('#checkupBonusModal #container_status_success #div_trace').css('display', 'none');
                        }
                        $('#checkupBonusModal #container_status_fail').css('display', 'block');
                        $('#checkupBonusModal #container_status_success').css('display', 'none');
                    }
                }
            });
        }
    });

    /**
     * Process bonus.
     */
    $('#btn_process_bonus').click(function(e) {
        e.preventDefault();

        $('#processBonusModal #status_success span#lb_status').html('');
        $('#processBonusModal #status_success span#lb_bonus_amount').html('');
        $('#processBonusModal #status_success span#lb_advocates_referrer').html('');
        $('#processBonusModal #status_fail span#lb_status').html('');

        $('#processBonusModal #container_status_success').css('display', 'none');
        $('#processBonusModal #container_status_fail').css('display', 'none');

        var isValid = validateProcessBonus();
        if (isValid)
        {
            reference = $('#processBonusModal #reference').val();
            amount_payments = $('#processBonusModal #amount_payments').val();
            payment_amount = $('#processBonusModal #payment_amount').val();
            advocate_token = $('input#advocate_token').val();

            var request = $.ajax({
                type: "POST",
                url: 'ajax/manage_advocate_ajax.php?method=processBonus',
                data: {'data': {'reference': reference,
                        'amount_payments': amount_payments,
                        'payment_amount': payment_amount,
                        'advocate_token': advocate_token}},
                beforeSend: function() {
                    $('#btn_process_bonus').button('loading');
                    $('#btn_process_bonus').removeClass('btn-primary');
                    $('#btn_process_bonus').addClass('btn-info');
                },
                complete: function() {
                    $('#btn_process_bonus').button('reset');
                    $('#btn_process_bonus').removeClass('btn-info');
                    $('#btn_process_bonus').addClass('btn-primary');
                }
            });
            request.done(function(data) {
                var data = jQuery.parseJSON(data);
                if (data.success) {
                    if (data.message.status == 'Success') {
                        $('#processBonusModal #status_success span#lb_status').html(data.message.status);
                        $('#processBonusModal #status_success span#lb_bonus_amount').html(data.message.bonus_amount);
                        $('#processBonusModal #status_success span#lb_advocates_referrer').html(data.message.advocates_referrer_name);

                        $('#processBonusModal #container_status_success').css('display', 'block');
                        $('#processBonusModal #container_status_fail').css('display', 'none');
                    }
                    else if (data.message.status == 'Fail') {
                        $('#processBonusModal #status_fail span#lb_status').html(data.message.status);

                        $('#processBonusModal #container_status_fail').css('display', 'block');
                        $('#processBonusModal #container_status_success').css('display', 'none');
                    }
                }
            });
        }
    });
});

/**
 * Validate form add referrer.
 */
function validateCreateReferral()
{
    $('#form_create_referral').validate({
        rules: {
            "advocate_referrer": {required: true},
            "campaing": {required: true},
            "network": {required: true}
        }
    });
    return $('#form_create_referral').valid();
}

/**
 * Validate form checkup bonus.
 */
function validateCheckupBonus()
{
    $('#form_checkup_bonus').validate({
        rules: {
            'reference': {required: true},
            'amount_payments': {digits: true},
            'payment_amount': {number: true}
        }
    });
    return $('#form_checkup_bonus').valid();
}

/**
 * Validate form process bonus.
 */
function validateProcessBonus()
{
    $('#form_process_bonus').validate({
        rules: {
            'reference': {required: true},
            'amount_payments': {digits: true},
            'payment_amount': {number: true}
        }
    });
    return $('#form_process_bonus').valid();
}
