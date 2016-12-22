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
                

               @include('admin/tmp/promotion_list_table')

                <div class="row">
                    <div class="col-md-12">
                        <div class="pull-right">{{ $records->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>



</div>
@stop