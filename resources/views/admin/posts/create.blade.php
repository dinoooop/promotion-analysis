@extends('layouts.dashboard')

@section('title', 'Create Post')

@section('main')


<div class="right_col" role="main">


    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">

                <div class="x_title">
                    <h2>Create Posts</h2>
                    <div class="clearfix"></div>
                </div>
                
                @if ($errors->any())
                <div class="alert alert-danger">
                    <?php echo implode('. ', $errors-> all(':message')); ?>
                </div>
                @endif
                
                <div class="row" ng-controller="CreatePosts">
                    <div class="col-md-6 col-sm-12">
                        {{ Form::open(array('route' => 'posts.store', 'class'=>'form-horizontal', 'name'=>'pv_create_post', 'novalidate' => 'novalidate')) }}
                            {{$form_create}}
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@stop