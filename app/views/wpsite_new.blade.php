@extends('layout')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>Create <small>Create WordPress Website</small></h1>
        <ol class="breadcrumb">
            <li><i class="fa fa-dashboard"></i> <a href="{{ url() }}">Dashboard</a></li>
            <li class="active"><i class="fa fa-plus-square"></i> New Website</li>
        </ol>
    </div>
</div><!-- /.row -->


<div class="row">
    
    <div class="col-lg-6">
        {{ Form::open(array('route' => 'doolox.wpsite_new', 'method' => 'post', 'role' => 'form')) }}

            <p class="help-block">required fields are marked with *</p>

            <div class="form-group @if ($errors->has('name')) has-error @endif">
                {{ Form::label('name', 'Name *') }}
                {{ Form::text('name', null, array('class' => 'form-control')) }}
                @if ($errors->has('name'))<p class="help-block">{{ $errors->first('name') }}</p>@endif
            </div>

            <div class="form-group @if ($errors->has('url')) has-error @endif">
                {{ Form::label('url', 'URL *') }}
                {{ Form::text('url', null, array('class' => 'form-control')) }}
                @if ($errors->has('url'))<p class="help-block">{{ $errors->first('url') }}</p>@endif
            </div>

            <div class="form-group @if ($errors->has('username')) has-error @endif">
                {{ Form::label('username', 'Username *') }}
                {{ Form::text('username', null, array('class' => 'form-control')) }}
                @if ($errors->has('username'))<p class="help-block">{{ $errors->first('username') }}</p>@endif
            </div>

            <div class="form-group @if ($errors->has('password')) has-error @endif">
                {{ Form::label('password', 'Password *') }}
                {{ Form::password('password', array('class' => 'form-control')) }}
                @if ($errors->has('password'))<p class="help-block">{{ $errors->first('password') }}</p>@endif
            </div>

            <div class="form-group @if ($errors->has('admin_url')) has-error @endif">
                {{ Form::label('admin_url', 'Admin URL') }}
                {{ Form::text('admin_url', null, array('class' => 'form-control')) }}
            </div>

            <input type="submit" class="btn btn-primary" value="Add Website" />

        {{ Form::close() }}
    </div>
</div>

@stop