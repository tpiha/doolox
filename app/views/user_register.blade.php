@extends('layout')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>Doolox Login <small>Login To Access Your Dashboard</small></h1>
        <ol class="breadcrumb">
            <li class="active"><i class="fa fa-sign-in"></i> Login</li>
        </ol>
@if(Session::has('error'))
        <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ Session::get('error') }}
        </div>
@endif
@if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ Session::get('success') }}
        </div>
@endif
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        {{ Form::open(array('url' => 'register-post', 'method' => 'post', 'role' => 'form')) }}

            <p class="help-block">required fields are marked with *</p>

            <div class="form-group @if ($errors->has('email')) has-error @endif">
                {{ Form::label('email', 'Email *') }}
                {{ Form::text('email', null, array('class' => 'form-control')) }}
                @if ($errors->has('email'))<p class="help-block">{{ $errors->first('email') }}</p>@endif
            </div>

            <div class="form-group @if ($errors->has('password1')) has-error @endif">
                {{ Form::label('password1', 'Password *') }}
                {{ Form::password('password1', array('class' => 'form-control')) }}
                @if ($errors->has('password1'))<p class="help-block">{{ $errors->first('password1') }}</p>@endif
            </div>

            <div class="form-group @if ($errors->has('password2')) has-error @endif">
                {{ Form::label('password2', 'Password Repeat *') }}
                {{ Form::password('password2', array('class' => 'form-control')) }}
                @if ($errors->has('password2'))<p class="help-block">{{ $errors->first('password2') }}</p>@endif
            </div>

            <input type="submit" class="btn btn-primary" value="Register" />&nbsp;&nbsp;or&nbsp;&nbsp;<a class="btn btn-success" href="{{ route('user.login') }}">Login</a>

        {{ Form::close() }}
    </div>
</div>
@stop