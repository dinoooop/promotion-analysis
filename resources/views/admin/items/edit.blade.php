@extends('admin.layouts.dashboard')

@section('title', 'Edit Item')

@section('main')

<div class="right_col" role="main">


    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">



                <div class="x_title">
                    <h2>Edit Items</h2>

                    <div class="clearfix"></div>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <?php echo implode('. ', $errors->all(':message')); ?>
                </div>
                @endif

                @include('admin/tmp/promotion_item')

                <div class="row">
                    <div class="col-md-6 col-xs-12">
                        
                        {{ Form::open(array('method' => 'PATCH', 'route' => array('items.update', $record->id), 'class'=>'normal_form', 'id'=>'pv_edit_item')) }}
                        <?php echo $form_edit; ?>
                        {{ Form::close() }}
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>


@stop