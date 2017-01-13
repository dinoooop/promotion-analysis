@extends('admin.layouts.dashboard')

@section('title', 'Promotion Result')

@section('main')

<div class="right_col" role="main">


    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">

            <div class="x_title">
                <h3>Promotion Result</h3>
                <div class="clearfix"></div>
            </div>


            @include('admin/tmp/promotion_item')

            @if ($records->count($records))
            @if(App\Dot::is_amazon($records[0]))
            @include('admin/results/tmp-retailer/amazon')
            @else
            @include('admin/results/tmp-retailer/walmart')
            @endif
            @else
            <p>There are no records available for this promotion result</p>
            @endif

        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">{{ $records->appends(['pid' => $promotion->id])->links() }}</div>
        </div>
    </div>



</div>
@stop