@extends('admin.layouts.dashboard')


@section('title', 'Promotions')

@section('main')
<?php
echo App\Temp::lightbox([
    'id' => 'show_hide_column',
    'heading' => 'Show/Hide Columns',
    'form_id' => 'show_hide_columns',
    'body' => $form_show_hide_column,
]);
?>
<div class="right_col" role="main">

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">

                <div class="x_title">
                    <h3>{{$page_heading}}</h3>
                    <div class="clearfix"></div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <a href="#" class="pull-right" data-toggle="modal" data-target="#show_hide_column"><i class="fa fa-eye-slash" aria-hidden="true"></i> Hide columns</a>
                    </div> 
                </div> 

                <div class="row">
                    <div class="col-md-12">
                        <div id="grid"></div>
                        <div id="pager"></div>
                    </div>
                </div>

                <script>
                    $(document).ready(function () {

                        var _dataSource = new kendo.data.DataSource({
                            transport: {
                                read: {
                                    url: "<?php echo $kendo_url; ?>",
                                    dataType: 'json',
                                    cache: false
                                },
                                destroy: {
                                    url: function (options) {
                                        return appConst.base_url + "/admin/promotions/" + options.id
                                    },
                                    data: {_token: appConst.token},
                                    dataType: 'json',
                                    type: 'DELETE',
                                },
                            },
                            schema: {
                                model: {
                                    id: "id",
                                    fields: {
                                        id: {editable: false, nullable: true},
                                        promotions_name: {type: "string"},
                                        promotions_startdate: {type: "date"},
                                        promotions_enddate: {type: "date"},
                                        retailer: {type: "string"},
                                        brand: {type: "string"},
                                        promotions_budget: {type: "number"},
                                        promotions_projected_sales: {type: "number"},
                                        promotions_expected_lift: {type: "number"},
                                        status: {type: "string"},
                                    }
                                }
                            },
                            pageSize: 10,
                        });


                        var grid = $("#grid").kendoGrid({
                            toolbar: ["excel"],
                            excel: {
                                fileName: "promotion-analysis.xlsx",
                                filterable: true
                            },
                            dataSource: _dataSource,
                            filterable: {extra: false},
                            height: 650,
                            sortable: true,
                            pageable: false,
                            editable: 'inline',
                            columns: [
                                {
                                    field: "id",
                                    title: "ID",
                                    sortable: true,
                                    width: 55,
                                },
                                {
                                    command: "destroy",
                                    title: "&nbsp;",
                                    width: 105,
                                },
                                {
                                    field: "promotions_name",
                                    title: "Promotions name",
                                    format: "{0:MM/dd/yyyy}",
                                    sortable: true,
                                    width: 250,
                                    template: "<a href='" + appConst.base_url + "/admin/promotions/#=id#/edit'>#=promotions_name#</a>"
                                },
                                {
                                    field: "promotions_startdate",
                                    title: "Start date",
                                    format: "{0:MM/dd/yyyy}",
                                    width: 90,
                                },
                                {
                                    field: "promotions_enddate",
                                    title: "End date",
                                    format: "{0:MM/dd/yyyy}",
                                    width: 90,
                                },
                                {
                                    field: "retailer",
                                    title: "Retailer"
                                },
                                {
                                    field: "brand",
                                    title: "Brand"
                                },
                                {
                                    field: "status",
                                    title: "Status"
                                },
                                {
                                    field: "promotions_budget",
                                    title: "Promotions Budget"
                                },
                                {
                                    field: "promotions_projected_sales",
                                    title: "Promotions projected sales"
                                },
                                {
                                    field: "promotions_expected_lift",
                                    title: "Promotions expected lift"
                                },
                                {
                                    title: "Items",
                                    width: 80,
                                    template: '<a href="' + appConst.base_url + '/admin/items?pid=#=id#&hsv=1"><i class="fa fa-database" aria-hidden="true"></i></a>'
                                },
                                <?php if (isset($display_result_view_button) && $display_result_view_button): ?>
                                {
                                    title: "Result",
                                    template: '<a href="' + appConst.base_url + '/admin/results?pid=#=id#"><i class="fa fa-database" aria-hidden="true"></i></a>',
                                    width: 60,
                                },
                                <?php endif; ?>

                            ]
                        }).data("kendoGrid");
                        
                        
                        $("#pager").kendoPager({
                            dataSource: _dataSource,
                            pageSizes: [10,20,50,100]
                          });

                        grid.bind("filterMenuInit", function (e) {
                            if (e.field == 'status') {
                                e.container.find("div.k-filter-help-text").text("Select an item from the list:");
                                e.container.find("span.k-dropdown:first").css("display", "none");
                                // Change the text field to a dropdownlist in the Role filter menu.
                                e.container.find(".k-textbox:first")
                                        .removeClass("k-textbox")
                                        .kendoDropDownList({
                                            dataSource: new kendo.data.DataSource({
                                                data: [
                                                    {title: "In-Progress"},
                                                    {title: "Completed"},
                                                    {title: "Not Processed"}
                                                ]
                                            }),
                                            dataTextField: "title",
                                            dataValueField: "title"
                                        });
                            }
                        });


                        $("#show_hide_column .lb-ok-button").click(function (e) {


                            var $form = $(this).parents("form");
                            var form_data = $form.serializeObject();
                            var hidable = [
                                'promotions_startdate',
                                'promotions_enddate',
                                'retailer',
                                'brand',
                                'status',
                                'promotions_budget',
                                'promotions_projected_sales',
                                'promotions_expected_lift'
                            ];
                            hidable.forEach(function (element) {
                                if (form_data['show_hide_column[]'].indexOf(element) >= 0) {
                                    grid.hideColumn(element);
                                } else {
                                    grid.showColumn(element);
                                }

                            });



                        });
                    });
                </script>


            </div>
        </div>
    </div>



</div>
@stop