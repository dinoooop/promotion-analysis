@extends('admin.layouts.dashboard')


@section('title', 'Items')

@section('main')

<div class="right_col" role="main">



    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">

            <div class="x_title">
                <h2>Promotion Items</h2>
                <div class="clearfix"></div>
            </div>
            @if(isset($promotion))
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <p>Promotion name: {{ $promotion->promotions_name }}</p>
                    <p>{{ $promotion->promotions_description }}</p>
                    <p>Start Date: {{ $promotion->promotions_startdate }}</p>
                    <p>End Date: {{ $promotion->promotions_enddate }}</p>
                    <p>Promotion Type: {{ $promotion->promotions_type }}</p>
                </div>
                <div class="col-md-6 col-sm-12">
                    <p>Retailer: {{ $promotion->retailer }}</p>
                    <p>Promotion Status: {{ $promotion->promotions_status }}</p>
                </div>
            </div>


            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <a href="{{route('items.create', ['pid' => $promotion->id])}}" class="btn btn-primary">Add Item [+]</a>
                    <?php echo $button_update_promotion_status; ?>
                </div>
            </div>

            {{ Form::open(array('route' => 'items.store', 'id' => 'pv_create_item_tbform', 'class'=>'normal_form')) }}
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Material Id</th>
                                <th>Promotions Budget</th>
                                <th>Promotions Projected Sales</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="item-content">
                            
                            @foreach ($records as $record)
                            <?php $record = App\promotions\Item::display_prepare($record) ?>
                            <tr>
                                <td><input type="text" class="form-control" name="exist[<?php echo $record->id; ?>][0]" value="{{ $record->material_id }}"></td>
                                <td><input type="text" class="form-control" name="exist[<?php echo $record->id; ?>][1]" value="{{ $record->promotions_budge }}"></td>
                                <td><input type="text" class="form-control" name="exist[<?php echo $record->id; ?>][2]" value="{{ $record->promotions_projected_sales }}"></td>
                                <td><button class="btn btn-danger remove-item-row-exist"><i class="fa fa-trash"></i></button></td>
                            </tr>
                            @endforeach
                            <tr>
                                <td><input type="text" class="form-control" name="new[0][0]" value=""></td>
                                <td><input type="text" class="form-control" name="new[0][1]" value=""></td>
                                <td><input type="text" class="form-control" name="new[0][2]" value=""></td>
                                <td><button class="btn btn-danger remove-item-row"><i class="fa fa-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <input type="hidden" name="promotions_id" value="{{ $promotion->id }}">
                    <input type="hidden" name="action" value="pv_create_item_tbform">
                    <button class="btn btn-primary pull-right">Save</button>
                    <button type="button" class="btn btn-primary add-item pull-right">+</button>    
                </div>
            </div>

            {{ Form::close() }}

            @endif


        </div>
    </div>



    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">{{ $records->links() }}</div>
        </div>
    </div>



</div>
@stop