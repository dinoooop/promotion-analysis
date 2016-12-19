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
                            <tr>
                                <td><input type="text" name="exist[<?php echo $record->id; ?>][0]" value="{{ $record->material_id }}" class="form-control auto-complete" data-coll="material_id"></td>
                                <td><input type="text" name="exist[<?php echo $record->id; ?>][1]" value="{{ $record->asin }}" class="form-control auto-complete" data-coll="asin"></td>
                                <td><input type="text" name="exist[<?php echo $record->id; ?>][2]" value="{{ $record->promotions_startdate }}" class="form-control date-picker-tool"></td>
                                <td><input type="text" name="exist[<?php echo $record->id; ?>][3]" value="{{ $record->promotions_enddate }}" class="form-control date-picker-tool"></td>
                                <td><input type="text" name="exist[<?php echo $record->id; ?>][4]" value="{{ $record->promotions_budget }}" class="form-control"></td>
                                <td><input type="text" name="exist[<?php echo $record->id; ?>][5]" value="{{ $record->promotions_projected_sales }}" class="form-control"></td>
                                <td><input type="text" name="exist[<?php echo $record->id; ?>][6]" value="{{ $record->promotions_expected_lift }}" class="form-control"></td>
                                <td><input type="text" name="exist[<?php echo $record->id; ?>][7]" value="{{ $record->promotions_budget_type }}" class="form-control"></td>
                                <td><input type="text" name="exist[<?php echo $record->id; ?>][8]" value="{{ $record->funding_per_unit }}" class="form-control"></td>
                                <td><input type="text" name="exist[<?php echo $record->id; ?>][9]" value="{{ $record->forecasted_qty }}" class="form-control"></td>
                                <td><input type="text" name="exist[<?php echo $record->id; ?>][10]" value="{{ $record->forecasted_unit_sales }}" class="form-control"></td>
                                <td><a class="btn btn-danger row-delete-no-alert" href="{{route('items.destroy', array($record->id))}}" data-modal_id="{{$record->id}}"><i class="fa fa-trash"></i></a></td>
                            </tr>
                            @endforeach
                            <?php echo App\Temp::dynamic_table_form(0); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <input type="hidden" name="promotions_id" value="{{ $promotion->id }}">
                    <input type="hidden" name="action" value="pv_create_item_tbform">
                    <?php // echo $button_update_promotion_status; ?>

                    <a type="button" class="btn btn-primary add-item pull-left">Add Item [+]</a>
                    
                    @if($item_edit_mode_view)
                    <input type="submit" name="item_edit_mode_view"  class="btn btn-danger prepare-promotions-results pull-right" value="Save">
                    @else
                    <button type="submit" class="btn btn-danger prepare-promotions-results pull-right">Prepare Promotions Results <i class="fa fa-angle-double-right" aria-hidden="true"></i></button>
                    @endif
                    
                </div>
            </div>

            {{ Form::close() }}




        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">{{ $records->appends(['pid' => $promotion->id])->links() }}</div>
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