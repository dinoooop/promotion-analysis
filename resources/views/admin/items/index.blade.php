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
            {{ Form::open(array('route' => 'items.store', 'id' => 'pv_create_item_tbform', 'class'=>'normal_form')) }}
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-striped table-bordered pa-table">
                        <thead>
                            <tr>
                                <th>Material Id</th>
                                <th width="120">ASIN</th>
                                <th width="90">Start date</th>
                                <th width="90">End date</th>
                                <th>Promotions Budget</th>
                                <th>Promotions Projected Sales</th>
                                <th>Promotions Expected Lift</th>
                                <th>Promotions Budget Type</th>
                                <th>Funding per unit</th>
                                <th>Forecasted qty</th>
                                <th>Forecasted Unit Sales</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="item-content">

                            @foreach ($records as $record)
                            <?php $record = App\promotions\Item::display_prepare($record) ?>
                            <?php echo App\Temp::dynamic_table_form_exist($record); ?>
                            @endforeach
                            <?php echo App\Temp::dynamic_table_form(0); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <a type="button" class="btn btn-default btn-lg btn-block add-item">Add Item [+]</a>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <input type="hidden" name="promotions_id" value="{{ $promotion->id }}">
                    <input type="hidden" name="action" value="pv_create_item_tbform">
                    <?php // echo $button_update_promotion_status; ?>


                    
                    @if($item_edit_mode_view)
                    
                    <button type="submit" name="item_edit_mode_view" class="btn btn-primary">Save</button>
                    <button type="submit" name="re_run" class="btn btn-danger">Recalculate</button>
                    @else
                    <button type="submit" class="btn btn-danger prepare-promotions-results pull-right">Prepare Promotions Results <i class="fa fa-angle-double-right" aria-hidden="true"></i></button>
                    @endif
                    

                </div>
            </div>

            {{ Form::close() }}
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
                                <th>Promotions Budget Type</th>
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
                                <td>{{ $record->promotions_budget_type }}</td>
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