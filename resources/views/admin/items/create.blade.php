@extends('admin.layouts.dashboard')

@section('title', 'Create Item')

@section('main')


<div class="right_col" role="main">


    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">

                <div class="x_title">
                    <h2>Create Item</h2>
                    <div class="clearfix"></div>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <?php echo implode('. ', $errors->all(':message')); ?>
                </div>
                @endif
                
                @include('admin/tmp/promotion_item')

                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        {{ Form::open(array('route' => 'items.store', 'id' => 'pv_create_item', 'class'=>'normal_form', 'novalidate' => 'novalidate')) }}
                        <?php echo $form_create; ?>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@stop