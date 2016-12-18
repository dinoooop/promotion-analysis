@extends('admin.layouts.dashboard')

@section('title', 'Create Promotion')

@section('main')


<div class="right_col" role="main">


    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">

                <div class="x_title">
                    <h2>Create a Promotion</h2>
                    <div class="clearfix"></div>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <?php echo implode('. ', $errors->all(':message')); ?>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        {{ Form::open(array('route' => 'promotions.store', 'id' => 'pv_create_promotion', 'class'=>'normal_form')) }}
                        <?php echo $form_create; ?>
                        {{ Form::close() }}
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <a href="{{url('admin/multiples/create')}}" class="btn btn-default pull-right">Click here to add multiple promotion.</a>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@stop