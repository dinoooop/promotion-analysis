@extends('admin.layouts.dashboard')

@section('title', 'Edit user')

@section('main')

<div class="right_col" role="main">

    @if(isset($notification))
    <br><?php echo $notification->display_notification() ?>
    @endif


    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Edit User</h2>
                    <div class="clearfix"></div>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <?php echo implode('. ', $errors->all(':message')); ?>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        {{ Form::open(array('method' => 'PATCH', 'route' => array('users.update', $record['id']), 'id'=>'pv_form_edit_user', 'class'=>'normal_form', 'novalidate' => 'novalidate')) }}
                        <?php echo $form_edit; ?>
                        {{ Form::close() }}
                    </div>
                </div>


            </div>
        </div>
    </div>

</div>


@stop