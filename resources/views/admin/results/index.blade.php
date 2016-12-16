@extends('admin.layouts.dashboard')

@section('title', 'Promotion Result')

@section('main')

<div class="right_col" role="main">


    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">

            <div class="x_title">
                <h2>Promotion Result</h2>
                <div class="clearfix"></div>
            </div>


            @include('admin/tmp/promotion_item')

            @if ($records->count())
                @if($promotion->retailer == 'Walmart')
                    @include('admin/results/tmp-retailer/walmart')
                @else
                    @include('admin/results/tmp-retailer/amazon')
                @endif
            @else
            <p>There are no records available for this promotion result</p>
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