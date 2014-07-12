
$(document).ready(function() {

    $('#btn_new_advocate').click(function() {
        $('#new_advocate_container').show();
    });
    $('#btn_close_advocate').click(function() {
        $('#new_advocate_container').hide();
    });

    $('#btn1_new_advocate').click(function() {
        var data = {
            name: $('#name').val(),
            last_name: $('#last_name').val(),
            email: $('#email').val(),
        };
        var stepRequest = $.ajax({
            url: 'ajax/manage_advocate_ajax.php?method=createAdvocate',
            data: {'data': data},
            type: 'POST'
        });
        stepRequest.done(function(response) {
            if (response) {
                $('#name').val('');
                $('#last_name').val('');
                $('#email').val('');
                $('#new_advocate_container').hide();
            }
        });
    });

    $('#search_advocate').click(function() {
        var data = {
            name: $('#inputName').val(),
            last_name: $('#inputLastname').val(),
            email: $('#inputEmail').val()
        };
        var stepRequest = $.ajax({
            url: 'ajax/manage_advocate_ajax.php?method=searchAdvocates',
            data: {'data': data},
            type: 'POST'
        });
        stepRequest.done(function(response) {
            if (response) {
                $('#table_advocate').append(
                        '<tr>' +
                        '<td> name </td>' +
                        '<td> Last name </td>' +
                        '<td> Email </td>' +
                        '<td> Account </td>' +
                        '<td> Campaign </td>' +
                        '<td> Creation date </td>' +
                        '<td> Actions </td>' +
                        '</tr>');
            }
        });
    });
});