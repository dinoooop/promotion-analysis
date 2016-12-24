@extends('admin.layouts.dashboard')


@section('title', 'Promotions')

@section('main')

<div class="right_col" role="main">

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">

                <div class="x_title">
                    <h3>{{$page_heading}}</h3>
                    <div class="clearfix"></div>
                </div>



                <div id="grid"></div>
                <script id="scriptTemplate" type="text/x-kendo-template">
                    Hello, #= promotions_name #
                </script>
                <script>
                    $(document).ready(function () {

                        var scriptTemplate = kendo.template($("#scriptTemplate").html());
                        var grid = $("#grid").kendoGrid({
                            dataSource: {
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
                                            brand: {type: "string"},
                                            promotions_budget: {type: "number"},
                                            promotions_projected_sales: {type: "number"},
                                            promotions_expected_lift: {type: "number"},
                                            status: {type: "string"},
                                        }
                                    }
                                },
                                pageSize: 20,
                            },
                            height: 550,
                            filterable: true,
                            sortable: true,
                            pageable: true,
                            editable: "inline",
                            columns: [
                                {
                                    field: "id",
                                    title: "Id",
                                    filterable: false,
                                    width: 50,
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
                                    format: "{0:MM/dd/yyyy}"
                                },
                                {
                                    field: "promotions_enddate",
                                    title: "End date",
                                    format: "{0:MM/dd/yyyy}"
                                },
                                {
                                    field: "brand",
                                    title: "Brand"
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
                                    field: "status",
                                    title: "Status"
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
                                {
                                    command: "destroy",
                                    title: "&nbsp;",
                                    width: 105,
                                },
                            ]
                        }).data("kendoGrid");
                    });
                </script>


            </div>
        </div>
    </div>



</div>
@stop