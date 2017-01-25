$.fn.extend({
    ajax_get_request: function (type, callback) {
        var url = appConst.url_ajax + type;
        $.get(url, function (response) {
            callback(response);
        }, 'json');
    },
    fieldMsg: function (msg) {
        var $next = $(this).next();
        var class_name = $next.attr("class");
        if (class_name == "field-msg") {
            $next.html(msg);
        } else {
            $(this).after('<p class="field-msg">' + msg + '</p>');
        }
    },
    fieldMsgError: function (msg) {
        var $formMsgPlace = $(".form-error-msg-submit");
        $formMsgPlace.html(msg);

    },
    serializeObject: function () {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    }

});

