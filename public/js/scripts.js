$(function () {
    $("form.normal_form").submit(function (e) {
        var $form = $(this);
        var error = $form.cu_validate_form();
        if (error) {
            e.preventDefault();
        }
    });


    $(".row-delete").click(function (e) {

        e.preventDefault();

        var $row = $(this);

        swal({
            title: "Are you sure?",
            text: "You will not able to undo this action!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false
        }, function () {
            var url = $row.attr("href");
            $row.parents("tr").remove();
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {_token : appConst.token},
                success: function (result) {
                    swal("Deleted!", "That record has been deleted successfully!!!", "success");
                }
            });

        });
    });

    /**
     * Set ad location on load
     */
    if($("#pv_form_ads_ad_location").length){
    $(this).ajax_get_request('select_option/ad_location_size', function (response) {
        var ad_location = $("#pv_form_ads_ad_location").val();
        var set = response.result[ad_location];
        var $demo = $("#pv_form_ads_ad_file").find(".demo");
        $demo.attr("data-crop_width", set.width);
        $demo.attr("data-crop_height", set.height);
    });
    }


    /**
     * 
     * On change ad location
     */
    $("#pv_form_ads_ad_location").change(function () {
        var ad_location = $(this).val();
        $("#pv_form_ads_ad_file").next().find(".upload-file-preview").html("");
        $("#pv_form_ads_ad_file").next().find("[name='ad_file']").val("");
        $(this).ajax_get_request('select_option/ad_location_size', function (response) {
            var set = response.result[ad_location];
            var $demo = $("#pv_form_ads_ad_file").find(".demo");
            $demo.attr("data-crop_width", set.width);
            $demo.attr("data-crop_height", set.height);
        });


    });
    


    
});