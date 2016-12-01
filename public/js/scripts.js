$(function () {

    /**
     * 
     * When submit a normal form
     */

    $("form.normal_form").submit(function (e) {
        var $form = $(this);
        var error = $form.cu_validate_form();

        if (error) {

            $form.find("[type='submit']").fieldMsgError('Error: Please fill the required fields with valid information.');
            e.preventDefault();

        } else {
            $form.find("[type='submit']").fieldMsgError('');
        }
    });


    /**
     * 
     * 
     * Alert when delete a table record (admin)
     */
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



    /**
     * 
     * 
     * Change promotion status (active, sleep)
     */
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



    /**
     * 
     * 
     * auto_populate
     * Auto populate item fields
     */

    $("#pv_form_item_material_id").blur(function () {
        var $field = $(this);
        var msg = 'Please wait... this form may auto-populate with the given material id.';
        $field.fieldMsg(msg);
        var data = {
            material_id: $field.val(),
            action: 'auto_populate',
        };

        var auto_populate_items = [
            'product_name',
            'x_plant_material_status',
            'x_plant_status_date',
            'asin',
        ];

        $.get(appConst.url_ajax, data, function (response) {
            if (response.status) {
                $.each(auto_populate_items, function (index, value) {
                    $("[name='" + value + "']").val(response.result[value]);

                });
                setTimeout(function () {
                    $field.fieldMsg('');
                }, 3000);
            } else {
                $.each(auto_populate_items, function (index, value) {
                    $("[name='" + value + "']").val('');
                });
                $field.fieldMsg('Sorry, no match record found.');
            }
        }, 'json');
    });

    /**
     * 
     * 
     * 
     * auto_complete
     * Form input field like retailer 
     */
    var auto_complete_item = '';
    $(".auto-complete").focus(function () {
        auto_complete_col = $(this).data('coll');
    });
    $(".auto-complete").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: appConst.url_ajax,
                dataType: "json",
                data: {
                    term: request.term,
                    col: auto_complete_col,
                    action: 'auto_complete',
                },
                success: function (data) {
                    response(data.result);
                }
            });
        },
        minLength: 2,
    });


});