$.fn.extend({
    ajax_get_request: function (type, callback) {
        var url = appConst.url_ajax + type;
        $.get(url, function (response) {
            callback(response);
        }, 'json');
    },
    

});