@extends('admin.layouts.dashboard')

@section('title', 'Preparation')

@section('main')

<div class="right_col" role="main">

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">

                <div class="x_title">
                    <h3>Preparation table</h3>
                    <div class="clearfix"></div>
                </div>


                @include('admin/tmp/promotion_item')

                @if (count($records))
                @if($promotion->retailer == 'Walmart')
                @include('admin/results/tmp-retailer/preparation-walmart')
                @else
                @include('admin/results/tmp-retailer/preparation-amazon')
                @endif
                @else
                <p>There are no records available for this preparation</p>
                @endif

            </div>
        </div>






    </div>
</div>
@stop