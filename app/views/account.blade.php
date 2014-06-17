@extends('layout')

@section('meta')
<title>Doolox Account</title>
<meta name="description" content="Doolox Account / Doolox is a free and Open Source WordPress management tool and website builder available both as a SaaS and for download.">
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>Doolox Account <small>Manage Your Account Data</small></h1>
        <ol class="breadcrumb">
            <li><i class="fa fa-dashboard"></i> <a href="{{ url() }}">Dashboard</a></li>
            <li class="active"><i class="fa fa-edit"></i> Update Account</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        {{ Form::model($user, array('route' => 'user.account', 'role' => 'form')) }}

            <p class="help-block">required fields are marked with *</p>

            <div class="form-group @if ($errors->has('email')) has-error @endif">
                {{ Form::label('email', 'Email *') }}
                {{ Form::text('email', null, array('class' => 'form-control', 'tabindex' => 1)) }}
                @if ($errors->has('email'))<p class="help-block">{{ $errors->first('email') }}</p>@endif
            </div>

            <div class="form-group @if(Session::has('error')) has-error @endif">
                <label>Password</label>
                <input class="form-control" type="password" name="password1" tabindex="2" />
            </div>

            <div class="form-group @if(Session::has('error')) has-error @endif">
                <label>Password Repeat</label>
                <input class="form-control" type="password" name="password2" tabindex="3" />
                @if(Session::has('error'))<p class="help-block">{{ Session::get('error') }}</p>@endif
            </div>

            <input type="submit" class="btn btn-primary" value="Update Account" tabindex="4" />

        {{ Form::close() }}
    </div>
@if(Config::get('doolox.saas'))
    <div class="col-lg-6">
        <h4>Doolox Plan</h4>
        <div class="well">
            <p>
                <label>Your Doolox Plan:</label> {{ $user->plan }}
            </p>
            <a class="btn btn-primary btn-lg" href="{{ route('doolox.upgrade') }}">Upgrade Doolox Plan</a>
        </div>
    </div>
@endif
</div>
@stop