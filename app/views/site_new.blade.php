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
        {{ Form::open(array('route' => 'doolox.site_new', 'method' => 'post', 'role' => 'form')) }}

            <p class="help-block">required fields are marked with *</p>

            <div class="form-group @if ($errors->has('name')) has-error @endif">
                {{ Form::label('name', 'Name *') }}
                {{ Form::text('name', Input::old('name'), array('class' => 'form-control')) }}
                @if ($errors->has('name'))<p class="help-block">{{ $errors->first('name') }}</p>@endif
            </div>

            <div class="form-group @if ($errors->has('url')) has-error @endif">
                {{ Form::label('url', 'URL *') }}
                {{ Form::text('url', Input::old('url'), array('class' => 'form-control')) }}
                @if ($errors->has('url'))<p class="help-block">{{ $errors->first('url') }}</p>@endif
            </div>

            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {{ Form::checkbox('doolox_node', 'true', Input::old('doolox_node', true)) }}
                        Install Doolox Node automatically
                    </label>
                </div>
            </div>

            <div class="form-group @if ($errors->has('username')) has-error @endif">
                {{ Form::label('username', 'Username') }}
                {{ Form::text('username', Input::old('username'), array('class' => 'form-control')) }}
                @if ($errors->has('username'))<p class="help-block">{{ $errors->first('username') }}</p>@endif
            </div>

            <div class="form-group @if ($errors->has('password')) has-error @endif">
                {{ Form::label('password', 'Password') }}
                {{ Form::password('password', array('class' => 'form-control')) }}
                @if ($errors->has('password'))<p class="help-block">{{ $errors->first('password') }}</p>@endif
            </div>

            <input type="hidden" name="admin_url" value="" />            
            <input type="hidden" name="user_id" value="{{ Sentry::getUser()->id }}" />

            <input type="submit" class="btn btn-primary" value="Add Website" />

        {{ Form::close() }}
    </div>
</div>

@stop