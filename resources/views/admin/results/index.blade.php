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

            @if(App\Dot::is_amazon($promotion))
                @include('admin/results/tmp-retailer/amazon')
            @else
                @include('admin/results/tmp-retailer/walmart')
            @endif

        </div>
    </div>

</div>
@stop