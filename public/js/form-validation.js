$.fn.extend({
    // Form validation
    cu_validate_form: function (requiredFields) {

        var error = [];

        var input = $(this).find(":input");

        var fields = []

        input.each(function () {
            if (typeof $(this).attr('name') != "undefined") {
                fields.push($(this).attr('name'));
            }

        });

        for (var i = 0; i < fields.length; i++) {
            var $field = $(this).find("[name='" + fields[i] + "']");
            var dot = $(this).cu_custom_validation(fields[i]);
            error.push(dot);
        }

        return (error.indexOf(1) < 0) ? false : true;


    },
    cu_display_error: function (message) {

        if (typeof message != "undefined" && message == 0) {
            $(this).parents(".form-group").addClass("has-error");
            return false;
        }

        if (typeof message == "undefined" || message == "") {
            var message = $(this).attr("error-msg");
            if (typeof message == "undefined" || message == "") {
                message = "This field is required"
            }

        }



        var next = $(this).next(".help-block");

        if (next.length == 0) {
            $(this).parents(".form-group").addClass("has-error");
            var html = '';
            html = '<span class="help-block animated fadeInDown" style="display:block">' + message + '</span>';
            $(this).after(html);
        } else {
            next.text(message);
        }
    },
    cu_remove_error: function () {
        var next = $(this).next(".help-block");
        $(this).parents(".form-group").removeClass("has-error");
        if (next.length != 0) {
            next.remove();
        }

    },
    cu_require: function () {

        // Return error status

        var value = $(this).cu_getVal();

        if (value == null || value.length == 0 || value == "" || value == 0) {
            return 1;
        } else {
            return 0;
        }

    },
    cu_getType: function () {
        var type = "";
        var tagName = $(this).prop("tagName");
        if (tagName == 'SELECT') {
            type = "select";
        }
        if (tagName == 'TEXTAREA') {
            type = "textarea";
        }
        if (tagName == 'INPUT') {
            type = $(this).attr('type');
        }
        return type;
    },
    //Replace the place holder with null string
    cu_getVal: function () {

        var type = $(this).cu_getType();

        switch (type) {

            case 'select':
                var val = $(this).val();
                return val;
                break;

            case 'checkbox':
                if ($(this).prop("checked")) {
                    return 1;
                } else {
                    return 0;
                }
                break;

            case 'select':
                if ($(this).prop("checked")) {
                    return 1;
                } else {
                    return 0;
                }
                break;

            default :
                var val = $(this).val();
                if (val == $(this).attr('placeholder'))
                    return '';
                else
                    return val;
        }



    },
    IsEmail: function (email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    },
    cu_custom_validation: function (name) {

        var $form = $(this);


        var $field = $form.find("[name='" + name + "']");

        if ($field.length == 0) {

            return 0; // no errors
        }

        // select form
        var error = 0;

        switch ($form.attr('id')) {

            case "pv_create_promotion":

                var value = $field.cu_getVal();
                var required_fileds = ["promotions_name", "promotions_startdate", "promotions_enddate"];
                if (required_fileds.indexOf(name) != -1) {
                    error = $field.cu_require();
                    $field.cu_error_switch(error);
                }

                if (!error) {
                    switch (name) {

                        case 'promotions_enddate':

                            error = $field.cu_require();
                            $field.cu_error_switch(error, "Please ender the promotion date");
                            var start_date = $form.find("[name='promotions_startdate']").val();
                            var date_one = new Date(start_date);
                            var date_two = new Date(value);

                            error = (date_one <= date_two) ? 0 : 1;
                            var message = "Please enter valid promotion duration";
                            $field.cu_error_switch(error, message);
                            break;
                    }

                }
                break;

            

            case "pv_form_edit_user":
                var value = $field.cu_getVal();
                var required_fileds = ["name", "username", "email"];
                if (required_fileds.indexOf(name) != -1) {
                    error = $field.cu_require();
                    $field.cu_error_switch(error);
                }
                if (!error) {

                    switch (name) {
                        case 'confirm_password':
                            var password = $form.find("[name='password']").val();
                            if (password != '') {
                                error = $field.cu_require();
                                var message = "please confirm the password";
                                $field.cu_error_switch(error, message)
                                if (!error) {
                                    var confirm_password = value;

                                    error = (password == confirm_password) ? 0 : 1;
                                    var message = "Password not matching";
                                    $field.cu_error_switch(error, message)
                                }
                            }
                            break;
                    }

                }
                break;
            case "pv_create_user":



                var value = $field.cu_getVal();
                var required_fileds = ["name", "username"];
                if (required_fileds.indexOf(name) != -1) {
                    error = $field.cu_require();
                    $field.cu_error_switch(error);
                }

                // Checking for custom error
                if (!error) {

                    switch (name) {


                        case "phone":
                            if (value != "") {
                                var pat_phone = /^([\d]{10}|[\d]{2,3}-[\d]{6,8})$/;
                                error = (pat_phone.test(value)) ? 0 : 1;
                                var message = "phone number not correct";
                                $field.cu_error_switch(error, message);
                            }
                            break;

                        case "email":

                            error = (this.IsEmail(value)) ? 0 : 1;
                            message = "Please enter a valid email";
                            $field.cu_error_switch(error, message)

                            break;

                        case 'category[]':
                            error = $field.cu_require();
                            $field.cu_error_switch(error);

                            if (!error) {
                                error = ($("[name='category[]'] option:selected").length > 2) ? 1 : 0;
                                var message = "More than two categories are not allowed";
                                $field.cu_error_switch(error, message);
                            }
                            break;


                        case 'password':
                            error = $field.cu_require();
                            $field.cu_error_switch(error, message)


                            if (!error) {
                                var password = value;
                                var confirm_password = $form.find("[name='confirm_password']").val();

                                error = (password == confirm_password) ? 0 : 1;
                                var message = "Password not matching";
                                $field.cu_error_switch(error, message)
                            }
                            break;

                        case 'confirm_password':
                            error = $field.cu_require();
                            var message = "please confirm the password";
                            $field.cu_error_switch(error, message)
                            break;

                        case "agree":
                            error = $field.cu_require();
                            $field.cu_error_switch(error, 0);
                            break;

                        case "country":
                            var country_id = $("[name='country_id']").val();
                            error = (country_id == '') ? 1 : 0;
                            var message = "Please provide a valid country";
                            $field.cu_error_switch(error, message)
                            break;

                        case "state":
                            var state_id = $("[name='state_id']").val();
                            error = (state_id == '') ? 1 : 0;
                            var message = "Please provide a valid state";
                            $field.cu_error_switch(error, message)
                            break;

                        case "city":
                            var city_id = $("[name='city_id']").val();
                            error = (city_id == '') ? 1 : 0;
                            var message = "Please provide a valid city";
                            $field.cu_error_switch(error, message)
                            break;

                    }
                    break;
                }

            case "form_login":

                var value = $field.cu_getVal();

                // Required error

                var required_fileds = ["email", "password"];

                if (required_fileds.indexOf(name) != -1) {
                    error = $field.cu_require();
                    console.log(error);
                    $field.cu_error_switch(error);
                }


                // Checking for custom error
                if (!error) {

                    switch (name) {

                        case "email":

                            error = (this.IsEmail(value)) ? 0 : 1;
                            var message = "Please enter a valid email";
                            $field.cu_error_switch(error, message)

                            break;


                    }
                    break;
                }
                break;

            case 'form_password_reset':
                var value = $field.cu_getVal();


                switch (name) {

                    case 'password':
                        error = $field.cu_require();
                        var message = "Please enter your new password";
                        $field.cu_error_switch(error, message);


                        if (!error) {
                            var password = value;
                            var confirm_password = $form.find("[name='confirm_password']").val();

                            error = (password == confirm_password) ? 0 : 1;
                            var message = "Password not matching";
                            $field.cu_error_switch(error, message)
                        }
                        break;

                    case 'confirm_password':
                        error = $field.cu_require();
                        var message = "Please confirm your new password";
                        $field.cu_error_switch(error, message)
                        break;


                }
                break;


            case 'form_forget_password':
                var value = $field.cu_getVal();
                switch (name) {

                    case 'email':
                        error = $field.cu_require();
                        var message = "Please enter your email";
                        $field.cu_error_switch(error, message);

                        if (!error) {
                            error = (this.IsEmail(value)) ? 0 : 1;
                            var message = "Please enter a valid email";
                            $field.cu_error_switch(error, message)
                        }

                        break;


                }
                break;
            case 'form_post_status':
                var value = $field.cu_getVal();
                switch (name) {

                    case 'post_content':
                        error = $field.cu_require();
                        var message = "Please enter your thoughts";
                        $field.cu_error_switch(error, message);
                        break;


                }
                break;

            case 'parent-comment-form':
                var value = $field.cu_getVal();
                switch (name) {

                    case 'comment':
                        error = $field.cu_require();
                        var message = "Please enter the comment";
                        $field.cu_error_switch(error, message);
                        break;


                }
                break;
            case 'form_reply':
                var value = $field.cu_getVal();
                switch (name) {

                    case 'comment':
                        error = $field.cu_require();
                        var message = "Please enter the comment";
                        $field.cu_error_switch(error, message);
                        break;


                }
                break;
            case 'form_edit_profile':
                var value = $field.cu_getVal();

                var required_fileds = ["name", "phone", "email"];

                if (required_fileds.indexOf(name) != -1) {
                    error = $field.cu_require();
                    $field.cu_error_switch(error);
                }

                switch (name) {

                    case 'category[]':
                        error = $field.cu_require();
                        $field.cu_error_switch(error);

                        if (!error) {
                            error = ($("[name='category[]'] option:selected").length > 2) ? 1 : 0;
                            var message = "More than two categories are not allowed";
                            $field.cu_error_switch(error, message);
                        }
                        break;




                }
                break;

            case 'aw_form_save_filter' :
                var value = $field.cu_getVal();

                var required_fileds = ["title", "description"];

                if (required_fileds.indexOf(name) != -1) {
                    error = $field.cu_require();
                    $field.cu_error_switch(error);
                }

                break;
        }


        return (error) ? 1 : 0;


    },
    cu_error_switch: function (error, message) {
        if (error) {
            $(this).cu_display_error(message);
        } else {
            $(this).cu_remove_error();
        }
    }
});
