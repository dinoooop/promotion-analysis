@extends('admin.layouts.dashboard')

@section('title', 'Import multiple items')

@section('main')


<div class="right_col" role="main">


    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">

                <div class="x_title">
                    <h3>Import Multiple Items</h3>
                    <div class="clearfix"></div>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <?php echo implode('. ', $errors->all(':message')); ?>
                </div>
                @endif



                @include('admin/tmp/multiples/create_items')


            </div>
        </div>

    </div>

</div>

@stop