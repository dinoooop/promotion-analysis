@extends('admin.layouts.dashboard')

@section('title', 'Edit post')

@section('main')

<div class="right_col" role="main">


    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                
                
                
                <div class="x_title">
                    <h2>Edit Promotions</h2>
                    <div class="clearfix"></div>
                </div>
                
                @if ($errors->any())
                <div class="alert alert-danger">
                    <?php echo implode('. ', $errors-> all(':message')); ?>
                </div>
                @endif
                
                
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        {{ Form::open(array('method' => 'PATCH', 'route' => array('promotions.update', $record->id), 'class'=>'form-horizontal', 'name'=>'pv_create_post', 'novalidate' => 'novalidate')) }}
                        <?php echo $form_edit; ?>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>


@stop