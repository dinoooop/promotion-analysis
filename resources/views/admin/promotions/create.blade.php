@extends('admin.layouts.dashboard')

@section('title', 'Create Promotion')

@section('main')


<div class="right_col" role="main">


    <?php echo App\Temp::step_progress('step_1'); ?>
    
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">



                <div class="x_title">
                    <h3 class="pull-left">Create a Promotion</h3>
                    <a href="{{url('admin/multiples/create')}}" class="btn btn-info pull-right">Click here to add multiple promotions.</a>
                    <div class="clearfix"></div>
                </div>



                @if ($errors->any())
                <div class="alert alert-danger">
                    <?php echo implode('. ', $errors->all(':message')); ?>
                </div>
                @endif

                <div class="row">
                    <div class="col-sm-12">
                        {{ Form::open(array('route' => 'promotions.store', 'id' => 'pv_create_promotion', 'class'=>'normal_form')) }}
                        <?php echo $form_create; ?>
                        <p class="col-sm-12 form-error-msg-submit error"></p>
                        {{ Form::close() }}
                    </div>
                    
                </div>
            </div>
        </div>

    </div>

</div>

@stop