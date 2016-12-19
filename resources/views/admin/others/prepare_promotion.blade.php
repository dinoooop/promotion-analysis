@extends('admin.layouts.dashboard')

@section('title', 'Prepare promotions results')

@section('main')


<div class="right_col" role="main">


    <?php echo App\Temp::step_progress('step_3'); ?>
    
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">

                <div class="x_title">
                    <h3>Prepare Promotions Results</h3>
                    <div class="clearfix"></div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h2>Success!!!</h2>
                        <p>You have successfully added the promotion {{$promotion->promotions_name}}.
                            <br/>{{$message_level_of_promotions}}
                            <br/>{{$message_start_time}}</p>
                        
                        <h2>Promotion details</h2>
                        @include('admin/tmp/promotion_item')
                        
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@stop