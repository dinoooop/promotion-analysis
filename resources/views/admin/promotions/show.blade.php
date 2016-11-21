@extends('admin.layouts.dashboard')

@section('title', 'Edit post')

@section('main')


<div class="right_col" role="main">


    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                
                
                
                <div class="x_title">
                    <h2>{{ $record->promotions_name }}</h2>
                    <div class="clearfix"></div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <p>{{ $record->promotions_description }}</p>
                        <p>Start Date: {{ $record->promotions_startdate }}</p>
                        <p>End Date: {{ $record->promotions_enddate }}</p>
                        <p>Promotion Type: {{ $record->promotions_type }}</p>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <p>Retailer: {{ $record->retailer }}</p>
                        <p>Promotion Status: {{ $record->promotions_status }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>


@stop