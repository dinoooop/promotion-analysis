@extends('admin.layouts.dashboard')

@section('title', 'Items')

@section('main')

<div class="right_col" role="main">

    @if(!$item_edit_mode_view)
    <?php echo App\Temp::step_progress('step_2'); ?>
    @endif


    @if(isset($promotion))
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">

            <div class="x_title">
                <h3 class="pull-left">Add Promoted Items</h3>
                <a href="#" class="btn btn-info pull-right">Click here to add multiple items.</a>
                <div class="clearfix"></div>
            </div>

            @if ($errors->any())
            <div class="alert alert-danger">
                <?php echo implode('. ', $errors->all(':message')); ?>
            </div>
            @endif

            @include('admin/tmp/promotion_item')


            <!-- 
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <a href="{{route('items.create', ['pid' => $promotion->id])}}" class="btn btn-primary">Add Item [+]</a>
                </div>
            </div>
            -->

            @if($display_message_items)
            <p>Please wait. System is processing items for this promotion... </p>
            @else
            <div id="grid"></div>

            <script>
                $(document).ready(function () {



                    var dataSource = new kendo.data.DataSource({
                                transport: {
                                    read: {
                                        url: appConst.base_url + "/kendo/items?pid=<?php echo $promotions_id ?>",
                                        dataType: "json"
                                    },
                                    update: {
                                        url: function (options) {
                                            return appConst.base_url + "/admin/items/" + options.id + "?pid=<?php echo $promotions_id ?>";
                                        },
                                        data: {_token: appConst.token},
                                        dataType: 'json',
                                        type: 'PATCH',
                                    },
                                    destroy: {
                                        url: function (options) {
                                            return appConst.base_url + "/admin/items/" + options.id
                                        },
                                        data: {_token: appConst.token},
                                        dataType: 'json',
                                        type: 'DELETE',
                                        cache: false
                                    },
                                    create: {
                                        url: appConst.base_url + "/admin/items",
                                        data: {_token: appConst.token},
                                        dataType: "json",
                                        type: 'POST',
                                    },
                                    parameterMap: function (data, type) {
                                        if (type == "create") {
                                            data._token = appConst.token;
                                            data.pid = <?php echo $promotions_id ?>;
                                            return data;
                                        }
                                        
                                        
                                    }
                                },
                                batch: true,
                                pageSize: 20,
                                schema: {
                                    model: {
                                        id: "id",
                                        fields: {
                                            id: {editable: false, nullable: true},
                                            material_id: {type: "string"},
                                            asin: {type: "string"},
                                            promotions_startdate: {type: "date", format: "Y-m-d"},
                                            promotions_enddate: {type: "date", format: "Y-m-d"},
                                            promotions_budget: {type: "number"},
                                        }
                                    }
                                }
                            });

                    $("#grid").kendoGrid({
                        dataSource: dataSource,
                        navigatable: true,
                        pageable: true,
                        height: 550,
                        sortable: true,
                        filterable: true,
                        toolbar: ["create", "save", "cancel"],
                        columns: [
                            {field: "material_id", title: "Material Id", width: 120, sortable: true},
                            {field: "asin", title: "ASIN", width: 120, sortable: true},
                            {field: "promotions_startdate", title: "Start date", format: '{0:MM/dd/yyyy}', width: 150, sortable: true},
                            {field: "promotions_enddate", title: "End date", format: '{0:MM/dd/yyyy}', width: 150, sortable: true},
                            {field: "promotions_budget", title: "Promotions Budget", format: "{0:c}", width: 120, sortable: true},
                            {command: "destroy", title: "&nbsp;", width: 150}
                        ],
                        editable: "inline",
                    });
                });
            </script>
            @endif





        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">{{ $records->appends($pagination_appends)->links() }}</div>
        </div>
    </div>


    @else
    <!-- Items viewing from csv sessions - no promotion id -->
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">

            <div class="x_title">
                <h2>Promotion Items Under the CSV</h2>
                <div class="clearfix"></div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Material Id</th>
                                <th width="120">ASIN</th>
                                <th width="90">Start date</th>
                                <th width="90">End date</th>
                                <th>Promotions Budget</th>
                                <th>Promotions Projected Sales</th>
                                <th>Promotions Expected Lift</th>
                                <th>Funding per unit</th>
                                <th>Forecasted qty</th>
                                <th>Forecasted Unit Sales</th>
                            </tr>
                        </thead>

                        <tbody id="item-content">

                            @foreach ($records as $record)
                            <?php $record = App\promotions\Item::display_prepare($record) ?>
                            <tr>
                                <td>{{ $record->material_id }}</td>
                                <td>{{ $record->asin }}</td>
                                <td>{{ $record->promotions_startdate }}</td>
                                <td>{{ $record->promotions_enddate }}</td>
                                <td>{{ $record->promotions_budget }}</td>
                                <td>{{ $record->promotions_projected_sales }}</td>
                                <td>{{ $record->promotions_expected_lift }}</td>
                                <td>{{ $record->funding_per_unit }}</td>
                                <td>{{ $record->forecasted_qty }}</td>
                                <td>{{ $record->forecasted_unit_sales }}</td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    @endif



</div>
@stop