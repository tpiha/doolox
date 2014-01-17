@extends('layout')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>Doolox Login <small>Login To Access Your Dashboard</small></h1>
        <ol class="breadcrumb">
            <li class="active"><i class="fa fa-sign-in"></i> Login</li>
        </ol>    
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        {{ Form::open(array('url' => 'login', 'method' => 'post', 'role' => 'form')) }}

            <div class="form-group @if ($errors->has('email')) has-error @endif">
                <label>Email</label>
                <input class="form-control" name="email" />
                @if ($errors->has('email'))<p class="help-block">{{ $errors->first('email') }}</p>@endif
            </div>

            <div class="form-group @if ($errors->has('email')) has-error @endif">
                <label>Password</label>
                <input class="form-control" type="password" name="password" />
                @if ($errors->has('password'))<p class="help-block">{{ $errors->first('password') }}</p>@endif
            </div>

            <input type="submit" class="btn btn-default" value="login" />

        {{ Form::close() }}
    </div>
</div>
@stop