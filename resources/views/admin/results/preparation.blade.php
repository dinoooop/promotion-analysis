@extends('admin.layouts.dashboard')

@section('title', 'Preparation')

@section('main')

<div class="right_col" role="main">

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">

                <div class="x_title">
                    <h3>{{$heading}}</h3>
                    <div class="clearfix"></div>
                </div>
                
                @include('admin/tmp/promotion_item')
                
                @include($template)
                
            </div>
        </div>






    </div>
</div>
@stop