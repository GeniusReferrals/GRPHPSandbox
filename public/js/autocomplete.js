$(document).ready(function() {

    /**
     * Search advocate referer.
     */
    $("#advocate_referrer").autocomplete({
        source: function(request, response) {
            var request = $.ajax({
                type: "POST",
                url: 'ajax/manage_advocate_ajax.php?method=searchAdvocateReferer',
                data: {'data': {'email': request.term}}
            });
            request.done(function(data, status, xhr) {
                var data = jQuery.parseJSON(data);
                response(data);
            });
        },
        focus: function() {
            return false;
        }
    });
});
