@extends('admin.layouts.dashboard')

@section('title', 'Edit promotion')

@section('main')

<div class="right_col" role="main">


    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">

                <div class="x_title">
                    <h3>Edit Promotions</h3>
                    <div class="clearfix"></div>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <?php echo implode('. ', $errors->all(':message')); ?>
                </div>
                @endif


                {{ Form::open(array('method' => 'PATCH', 'route' => array('promotions.update', $record->id), 'class'=>'normal_form', 'id'=>'pv_edit_promotion')) }}
                <div class="row">
                    <div class="col-sm-12">

                        <?php echo $form_edit; ?>
                        
                        <p class="col-sm-12 form-error-msg-submit error"></p>
                        <div class="col-sm-12 col-md-12"> 
                            <button type="submit" name="save" class="btn btn-primary">Save</button>
                            @if(isset($display_recalculate_button) && $display_recalculate_button)
                            <button type="submit" name="re_run" class="btn btn-danger">Recalculate</button>
                            @endif
                        </div>
                    </div>

                </div>
                {{ Form::close() }}
            </div>
        </div>

    </div>

</div>


@stop