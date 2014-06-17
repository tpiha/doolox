@extends('layout')

@section('meta')
<title>Doolox Update User</title>
<meta name="description" content="Doolox Update User / Doolox is a free and Open Source WordPress management tool and website builder available both as a SaaS and for download.">
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>New User <small>Add New Doolox User</small></h1>
        <ol class="breadcrumb">
            <li><i class="fa fa-dashboard"></i> <a href="{{ url() }}">Dashboard</a></li>
            <li class="active"><i class="fa fa-user"></i> New User</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        {{ Form::model($user, array('route' => array('user.user_update', $user->id), 'role' => 'form')) }}

            <p class="help-block">required fields are marked with *</p>

            <div class="form-group @if ($errors->has('email')) has-error @endif">
                {{ Form::label('email', 'Email *') }}
                {{ Form::text('email', null, array('class' => 'form-control', 'tabindex' => 1)) }}
                @if ($errors->has('email'))<p class="help-block">{{ $errors->first('email') }}</p>@endif
            </div>

            <div class="form-group @if ($errors->has('password1')) has-error @endif">
                {{ Form::label('password1', 'Password') }}
                {{ Form::password('password1', array('class' => 'form-control', 'tabindex' => 3)) }}
                @if ($errors->has('password1'))<p class="help-block">{{ $errors->first('password1') }}</p>@endif
            </div>

            <div class="form-group @if ($errors->has('password2')) has-error @endif">
                {{ Form::label('password2', 'Password Repeat') }}
                {{ Form::password('password2', array('class' => 'form-control', 'tabindex' => 3)) }}
                @if ($errors->has('password2'))<p class="help-block">{{ $errors->first('password2') }}</p>@endif
            </div>

            <input type="submit" class="btn btn-primary" value="Update User" tabindex="4" />&nbsp;&nbsp;or&nbsp;&nbsp;<a tabindex="5" class="text-danger" href="javascript: void null;" onclick="bootbox.confirm('Are you sure you want to delete this user?', function(result) { if (result) { window.location.href = '{{ route('user.user_delete', $user->id) }}'; }});">Delete</a>

        {{ Form::close() }}
    </div>
</div>
@stop