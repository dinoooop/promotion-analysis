$(function () {

    var LBFU = {
        new_post: '', // Store ajax returns
        file_ext: '', // To store the file extension of uploading file
        progress_percentage: 0,
        html_progress_percentage: '',
        html_bar: '',
        html_percent: '',
        html_upload_button: '',
        html_cancel_button: $(".lb-cancel-button"),
        html_ok_button: '', // OK button of lightbox
        html_modal: '', // Main div
        html_uploading_file: '', // Html input field for file
        html_post_content: '', // Html textarea
        html_post_file_preview: '', // Html textarea
        croppie: '',
        is_active_croppie: '',
        html_croppie: '',
    }

    function crop_image(callback) {
        LBFU.html_croppie.croppie('result', 'canvas').then(function (html) {

            var url = appConst.url_create_image;
            LBFU.new_post.file_data = html;

            $.post(url, LBFU.new_post, function (response) {
                callback();
            });

        });
    }

    function define_lbfu_values(modal) {

        LBFU.html_cancel_button = modal.find(".lb-cancel-button");
        LBFU.html_uploading_file = modal.find(".post-file");
        LBFU.html_post_content = modal.find(".post-content");
        LBFU.html_post_file_preview = modal.find(".post-file-preview");
        LBFU.html_percent = modal.find(".js-percent");
        LBFU.html_bar = modal.find(".bar");
        LBFU.html_ok_button = modal.find(".lb-ok-button");
        LBFU.html_conditions = modal.find(".conditions");
        LBFU.html_upload_button = modal.find(".upload_new");
        LBFU.html_progress_percentage = modal.find(".post-progress");
        // Check crop set?
        LBFU.html_croppie = modal.find(".demo");
        
        if (LBFU.html_croppie.length) {
            LBFU.is_active_croppie = true;
            LBFU.crop_width = LBFU.html_croppie.attr("data-crop_width");
            LBFU.crop_height = LBFU.html_croppie.attr("data-crop_height");
            
        }


    }

    $("body").on("click", ".upload_new", function (event) {

        LBFU.html_modal = $(this).parents(".modal");
        define_lbfu_values(LBFU.html_modal);

        if (!$(event.target).is(LBFU.html_uploading_file)) {
            LBFU.html_uploading_file.trigger('click');
        }

    });

    var postObj = {
        resetLightBox: function () {

            LBFU.html_post_content.val("");
            LBFU.html_uploading_file.val("");
            LBFU.html_post_file_preview.html("");
            LBFU.html_upload_button.show();

            LBFU.html_upload_button.html('<p>Upload File</p>');
            LBFU.html_uploading_file.prop("disabled", false);
            LBFU.html_progress_percentage.hide();

            var percentVal = '0%';
            LBFU.html_bar.width(percentVal);
            LBFU.html_percent.html(percentVal);
            if (LBFU.is_active_croppie) {
                LBFU.croppie.croppie('destroy');
            }

        },
    }

    LBFU.html_cancel_button.click(function () {
        LBFU.html_uploading_file.val("");

        postObj.resetLightBox();

        if (parseInt(LBFU.progress_percentage) == 100) {

            $.post(appConst.url_delete_uploaded_files, LBFU.new_post, function (response) {
                //
            }, 'json');
        }
    });

    $('body').on("change", ".post-file", function (evt) {

        if (!LBFU.is_direct) {

            LBFU.html_modal = $(this).parents(".modal");
            define_lbfu_values(LBFU.html_modal);

        }

        var files = evt.target.files;
        var file = files[0];

        var megabyte = 1000 * 1024;
        var fsize = Math.round(file.size / megabyte);

        LBFU.file_ext = file.name.split('.').pop().toLowerCase();

        var upload_file_type = LBFU.html_conditions.data("upload_file_type");
        upload_file_type = upload_file_type.split(',');
        var upload_file_size = LBFU.html_conditions.data("upload_file_size");



        if (fsize > upload_file_size || upload_file_type.indexOf(LBFU.file_ext) < 0) {
            //alert('Please upload a valid file');
            swal('Error!', 'Please upload a valid file');
        } else {
            LBFU.html_upload_button.html('<p>Uploading ...</p>');
            LBFU.html_uploading_file.prop("disabled", true);
            LBFU.html_progress_percentage.show();
            startupload(files);
        }

    });


    function startupload(files) {

        var percentVal = '0%';
        LBFU.html_bar.width(percentVal);
        LBFU.html_percent.html(percentVal);

        for (var i = 0; i < files.length; i++) {

            (function (i) {

                new jsUpload({
                    file: files[i],
                    logger: function (message) {
                        //document.getElementById("log-" + i).innerHTML = document.getElementById("log-" + i).innerHTML + message + "<br />";
                    },
                    progressHandler: function (percentVal, serverResponse) {

                        if (isNaN(percentVal)) {
                            percentVal = 100;
                        }

                        LBFU.html_bar.width(percentVal + '%');
                        LBFU.html_percent.html(percentVal + '%');
                        LBFU.progress_percentage = percentVal;

                        if (percentVal == 100 && typeof serverResponse != "undefined" && serverResponse.action == "complete") {

                            var data = {
                                action: 'complete',
                                file: serverResponse.file,
                                file_ext: LBFU.file_ext,
                            }

                            LBFU.html_ok_button.prop('disabled', false)

                            $.post(appConst.url_move_uploaded_file, data, function (response) {
                                LBFU.html_upload_button.hide();
                                if (LBFU.is_active_croppie) {
                                    LBFU.html_croppie.show();
                                    LBFU.croppie = LBFU.html_croppie.croppie({
                                        url: response.post_preview,
                                        enableZoom: false,
                                        showZoomer: false,
                                        viewport: {
                                            width: LBFU.crop_width,
                                            height: LBFU.crop_height
                                        }
                                    });

                                } else {
                                    LBFU.html_post_file_preview.html("<img src='" + response.post_preview + "' height='200px' style='margin:0 auto; display:block;'>");
                                }

                                LBFU.new_post = response;


                            }, 'json');

                        }
                    },
                    // pauseButton: document.getElementById('pausebutton-' + i)

                });

            })(i);
        }
    }



    // Cutom code --------------------------------------------------------------



    // Admin: Create new video upload
    $("#pv_form_videos_file .lb-ok-button").click(function () {

        var url = appConst.url_preview_uploaded_file;

        $.post(url, LBFU.new_post, function (response) {

            postObj.resetLightBox();
            $(".upload-file-preview").html(response);
            $("[name='file']").val(LBFU.new_post.file);

        }, 'html');
    });
    $("#pv_form_ads_ad_file .lb-ok-button").click(function () {

        crop_image(function () {

            var url = appConst.url_preview_uploaded_file;

            $.post(url, LBFU.new_post, function (response) {

                postObj.resetLightBox();
                $(".upload-file-preview").html(response);
                $("[name='ad_file']").val(LBFU.new_post.file);

            }, 'html');
        });
    });



});