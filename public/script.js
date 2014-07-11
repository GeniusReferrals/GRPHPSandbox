
$(document).ready(function() {

    $('#btn_new_advocate').click(function() {
        $('#new_advocate_container').show();
    });
    $('#btn_close_advocate').click(function() {
        $('#new_advocate_container').hide();
    });

    //searching for the patient
    $('.patients #patient_filter').keyup(function() {

        if (this.value.length > 2)
        {
            $.ajax({
                url: 'ajax_controller.php?method=search_patient',
                data: {'data': {'query': this.value}},
                type: 'POST'
            }).done(function(data) {
                var data = jQuery.parseJSON(data);
                if (data.success) {
                    if (data.message.amount > 0) {
                        $('.patients #no_result_found').hide();
                        $('.patients #unknow_error').hide();

                        $('.patients #patients').empty();
                        $('.patients #patients').append('<tr><th>Name</th><th>Age</th><th>Phone</th><th>Has Song</th><th>Actions</th></tr>');

                        for (var i = 0; i < data.message.amount; i++)
                        {
                            var has_favorite_song = 'NO';
                            if (data.message.patients[i].favorite_song_id) {
                                has_favorite_song = 'YES'
                            }
                            $('.patients #patients').append(
                                    '<tr class="patient">' +
                                    '<td class="patient-name">' + data.message.patients[i].patient_name + '</td>' +
                                    '<td class="patient-age">' + data.message.patients[i].patient_age + '</td>' +
                                    '<td class="patient-phone">' + data.message.patients[i].patient_phone + '</td>' +
                                    '<td class="patient-has-song">' + has_favorite_song + '</td>' +
                                    '<td class="patient-song"> <a href="songs.php?patient_id=' + data.message.patients[i].patient_id + '" title="Click to Assisgn a Song to <?php echo $patient->patient_name; ?>">Assign Song</a></td></tr>');
                        }
                    } else {
                        $('.patients #no_result_found').show();
                        $('.patients #unknow_error').hide();
                    }
                } else {
                    $('.patients #no_result_found').hide();
                    $('.patients #unknow_error').show();
                }

            });
        }
        else {
            $('.patients #no_result_found').hide();
            $('.patients #unknow_error').hide();

            $.ajax({
                url: 'ajax_controller.php?method=search_patient',
                data: {'data': {'query': ''}},
                type: 'POST'
            }).done(function(data) {
                var data = jQuery.parseJSON(data);
                if (data.success) {
                    if (data.message.amount > 0) {
                        $('.patients #no_result_found').hide();
                        $('.patients #unknow_error').hide();

                        $('.patients #patients').empty();
                        $('.patients #patients').append('<tr><th>Name</th><th>Age</th><th>Phone</th><th>Has Song</th><th>Actions</th></tr>');

                        for (var i = 0; i < data.message.amount; i++)
                        {
                            var has_favorite_song = 'NO';
                            if (data.message.patients[i].favorite_song_id) {
                                has_favorite_song = 'YES'
                            }
                            $('.patients #patients').append(
                                    '<tr class="patient">' +
                                    '<td class="patient-name">' + data.message.patients[i].patient_name + '</td>' +
                                    '<td class="patient-age">' + data.message.patients[i].patient_age + '</td>' +
                                    '<td class="patient-phone">' + data.message.patients[i].patient_phone + '</td>' +
                                    '<td class="patient-has-song">' + has_favorite_song + '</td>' +
                                    '<td class="patient-song"> <a href="songs.php?patient_id=' + data.message.patients[i].patient_id + '" title="Click to Assisgn a Song to <?php echo $patient->patient_name; ?>">Assign Song</a></td></tr>');
                        }
                    } else {
                        $('.patients #no_result_found').show();
                        $('.patients #unknow_error').hide();
                    }
                } else {
                    $('.patients #no_result_found').hide();
                    $('.patients #unknow_error').show();
                }

            });
        }
    });

});