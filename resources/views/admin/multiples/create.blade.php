@extends('admin.layouts.dashboard')

@section('title', 'Import multiple promotions/items')

@section('main')


<div class="right_col" role="main">


    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">

                <div class="x_title">
                    <h3>Import Multiple Promotions</h3>
                    <div class="clearfix"></div>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <?php echo implode('. ', $errors->all(':message')); ?>
                </div>
                @endif
                

                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <p>Adding promotions for processing is a two steps process. Note, each of the steps have sub-steps. 
                            Over all the process is very simple and will enable you to quickly add multiple number of promotions and their respective promoted items quickly in modeling database.</p>
                        <h2>Step 1: Create promotions</h2>
                        {{ Form::open(array('route' => 'multiples.store', 'id' => 'pv_create_multiple', 'class'=>'normal_form', 'enctype' => 'multipart/form-data')) }}
                        <?php echo $form_multiple_promotion; ?>
                        {{ Form::close() }}
                        <br>
                        <h2>Step 2: Add promoted items</h2>
                        {{ Form::open(array('route' => 'multiples.store', 'id' => 'pv_create_multiple', 'class'=>'normal_form', 'enctype' => 'multipart/form-data')) }}
                        <?php echo $form_multiple_items; ?>
                        {{ Form::close() }}
                        
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@stop