@extends('layout')

@section('meta')
<title>Doolox New Website</title>
<meta name="description" content="Doolox New Website / Doolox is a free and Open Source WordPress management tool and website builder available both as a SaaS and for download.">
@stop

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
                {{ Form::text('name', Input::old('name'), array('class' => 'form-control', 'tabindex' => 1)) }}
                @if ($errors->has('name'))<p class="help-block">{{ $errors->first('name') }}</p>@endif
            </div>

            <div class="form-group @if ($errors->has('url')) has-error @endif">
                {{ Form::label('url', 'URL *') }}
                {{ Form::text('url', Input::old('url'), array('class' => 'form-control', 'tabindex' => 2)) }}
                @if ($errors->has('url'))<p class="help-block">{{ $errors->first('url') }}</p>@endif
            </div>

            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {{ Form::checkbox('doolox_node', 'true', Input::old('doolox_node', true), array('tabindex' => 3)) }}
                        Install <a href="https://wordpress.org/plugins/doolox-node/" target="_blank">Doolox Node</a> automatically (credentials will not be stored)
                    </label>
                </div>
            </div>

            <div class="form-group @if ($errors->has('username')) has-error @endif">
                {{ Form::label('username', 'Username') }}
                {{ Form::text('username', Input::old('username'), array('class' => 'form-control', 'tabindex' => 4)) }}
                @if ($errors->has('username'))<p class="help-block">{{ $errors->first('username') }}</p>@endif
            </div>

            <div class="form-group @if ($errors->has('password')) has-error @endif">
                {{ Form::label('password', 'Password') }}
                {{ Form::password('password', array('class' => 'form-control', 'tabindex' => 5)) }}
                @if ($errors->has('password'))<p class="help-block">{{ $errors->first('password') }}</p>@endif
            </div>

            <input type="hidden" name="admin_url" value="" />            
            <input type="hidden" name="user_id" value="{{ Sentry::getUser()->id }}" />

            <input type="submit" class="btn btn-primary" value="Add Website" tabindex="6" />

        {{ Form::close() }}
    </div>
</div>

@stop