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
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        {{ Form::open(array('url' => 'login', 'method' => 'post', 'role' => 'form')) }}

            <div class="form-group @if ($errors->has('email')) has-error @endif">
                <label>Email</label>
                <input class="form-control" name="email" tabindex="1" />
                @if ($errors->has('email'))<p class="help-block">{{ $errors->first('email') }}</p>@endif
            </div>

            <div class="form-group @if ($errors->has('email')) has-error @endif">
                <label>Password</label>
                <input class="form-control" type="password" name="password" tabindex="2" />
                @if ($errors->has('password'))<p class="help-block">{{ $errors->first('password') }}</p>@endif
            </div>

            <input type="submit" class="btn btn-primary" value="Login" />

        {{ Form::close() }}
    </div>
</div>
@stop