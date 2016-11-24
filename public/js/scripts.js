$(function () {

    $("form.normal_form").submit(function (e) {
        var $form = $(this);
        var error = $form.cu_validate_form();

        if (error) {
            $form.find("[type='submit']").before('<p class="error form-error-submit">Error: Please fill the required fields with valid information.</p>');
            e.preventDefault();
        } else {
            $(".form-error-submit").remove();
        }
    });


    $(".row-delete").click(function (e) {

        e.preventDefault();

        var $row = $(this);

        swal({
            title: "Are you sure?",
            text: "You can not undo this action!",
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
                data: {_token: appConst.token},
                success: function (result) {
                    swal("Deleted!", "That record has been deleted successfully!!!", "success");
                }
            });

        });
    });



    $(".ajax-promotion-status").click(function () {
        var id = $(this).data('pid');
        var new_status = $(this).data('status');
        var url = appConst.url_update_promotion_status + '/' + id + '/' + new_status;

        if (new_status == 'active') {
            $(this).data('status', 'sleep');
            $(this).html("Stop Promotion");
        } else {
            $(this).data('status', 'active');
            $(this).html("Start Promotion");
        }

        $.get(url, function (response) {
            console.log(response);
        });



    });

});