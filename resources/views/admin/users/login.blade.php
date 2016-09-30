@extends('admin.layouts.dashboard')

@section('title', 'Login')

@section('main')
<div class="row login">
    <div class="col-md-2 col-md-offset-5">
        <h1>NRI<strong>STORIES</strong></h1>
        {{ Form::open(array('action' => 'UsersController@login')) }}
        <div class="form-group">
            <input type="text" name="username" class="form-control" placeholder="Usernam">
        </div>
        <div class="form-group">
            <input type="password" name="password" class="form-control" placeholder="Password">
        </div>
        @if(isset($message))
        <p class="error">{{ $message }}</p>
        @endif
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <button type="submit" class="btn btn-info">Login</button>
        {{ Form::close() }}
    </div>
</div>
@stop