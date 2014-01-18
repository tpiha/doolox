@extends('layout')

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
        {{ Form::open(array('route' => 'user.user_new', 'role' => 'form')) }}

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

            <input type="submit" class="btn btn-primary" value="Add User" />

        {{ Form::close() }}
    </div>
</div>
@stop

@section('specific')
<script type="text/javascript">
    $('.dropdown-menu').show();
</script>
@stop