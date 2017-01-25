$(function () {

    /**
     * 
     * When submit a normal form
     */

    $("form.normal_form").submit(function (e) {
        var $form = $(this);
        var error = $form.cu_validate_form();

        if (error) {

            $form.fieldMsgError('Error: There are errors in your form.');
            e.preventDefault();

        } else {
            $form.fieldMsgError('');
        }
    });


    /**
     * 
     * 
     * Alert when delete a table record (admin)
     */
    $("body").on('click', '.row-delete', function (e) {

        e.preventDefault();

        var $row = $(this);
        
        var r = confirm("Are you sure you want to delete this record?");
        if(r){
            var url = $row.attr("href");
            $row.parents("tr").remove();
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {_token: appConst.token},
                success: function (result) {
                    
                }
            });
        }


    });

    /**
     * 
     * 
     * Delete the table row (delete record from db) without any alert
     */
    $(".row-delete-no-alert").click(function (e) {

        e.preventDefault();

        var $row = $(this);

        var url = $row.attr("href");
        $row.parents("tr").remove();
        $.ajax({
            url: url,
            type: 'DELETE',
            data: {_token: appConst.token},
            success: function (result) {

            }
        });
    });

    /**
     * 
     * Delete table row only
     */
    $("body").on("click", ".remove-item-row", function () {
        $(this).parents('tr').remove();
    });



    /**
     * 
     * 
     * Change promotion status (active, sleep)
     */
    $(".ajax-promotion-status").click(function () {

        var $this = $(this);
        var id = $this.data('pid');
        var new_status = $this.data('status');
        var url = appConst.url_update_promotion_status + '/' + id + '/' + new_status;

        if (new_status == 'active') {
            $this.data('status', 'sleep');
            $this.html("Stop Processing");
        } else {
            $this.data('status', 'active');
            $this.html("Prepare Promotion Result");
        }
        $.get(url, function (response) {

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
     * auto_complete
     * Form input field like retailer 
     */
    var auto_complete_col = '';
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

    /**
     * 
     * 
     * Create dynamic table form (items)
     */
    var item_field_id = 0;
    $(".add-item").click(function () {
        item_field_id++;
        var data = {
            action: 'dynamic_table_form',
            increment: item_field_id,
        };
        $.get(appConst.url_ajax, data, function (html) {
            $("tbody#item-content").append(html);
        }, 'html');

    });

    /**
     * 
     * 
     * Create a date picker tool input field
     */
    $("body").on('focus', '.date-picker-tool', function () {
        $(this).daterangepicker({
            singleDatePicker: true,
            showDropdowns: true
        }, function (start, end, label) {
            var years = moment().diff(start, 'years');
        });
    });


//    $("name=['level_of_promotions']").change(function () {
//        var $this = $(this);
//        var val = $this.val();
//        var data = {
//            action: 'change_level_of_promotions',
//            val: val,
//        };
//        
//        $.get(appConst.url_ajax, data, function (html) {
//            $this.after(html);
//        }, 'html');
//    });

    /**
     * 
     * 
     * Check whether the items exist on submit STEP 2
     */

    $(".prepare-promotions-results").click(function (e) {

        
        var gridDataArray = $('#grid').data('kendoGrid')._data;
        
        $(".k-grid-save-changes").trigger("click");

        if (gridDataArray.length == 0) {
            alert("Please add items under this promotion.");
            e.preventDefault();
        }

        gridDataArray.forEach(function (value, index) {

            if (value.material_id == '' || value.material_id == null) {
                if (value.asin == '' || value.asin == null) {
                    alert("Please add items under this promotion.");
                    e.preventDefault();
                }
            }
        });


    });






});