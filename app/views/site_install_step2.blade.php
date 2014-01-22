@extends('layout')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>Finish Installation <small>Finish Your WordPress Installation</small></h1>
        <ol class="breadcrumb">
            <li><i class="fa fa-dashboard"></i> <a href="{{ url() }}">Dashboard</a></li>
            <li class="active"><i class="fa fa-plus-square"></i> Finish Installation</li>
        </ol>
    </div>
</div><!-- /.row -->


<div class="row">
    
    <div class="col-lg-6">
        {{ Form::open(array('route' => 'doolox.site_install_step2', 'method' => 'post', 'role' => 'form')) }}

            <p class="help-block">required fields are marked with *</p>

            <div class="form-group @if ($errors->has('title')) has-error @endif">
                {{ Form::label('title', 'Site Title *') }}
                {{ Form::text('title', Input::old('title'), array('class' => 'form-control')) }}
                @if ($errors->has('title'))<p class="help-block">{{ $errors->first('title') }}</p>@endif
            </div>

            <div class="form-group @if ($errors->has('username')) has-error @endif">
                {{ Form::label('username', 'Username *') }}
                {{ Form::text('username', Input::old('username'), array('class' => 'form-control')) }}
                @if ($errors->has('username'))<p class="help-block">{{ $errors->first('username') }}</p>@endif
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

            <div class="form-group @if ($errors->has('email')) has-error @endif">
                {{ Form::label('email', 'Email *') }}
                {{ Form::text('email', Input::old('username') ? Input::old('username') : Sentry::getUser()->email, array('class' => 'form-control')) }}
                @if ($errors->has('email'))<p class="help-block">{{ $errors->first('email') }}</p>@endif
            </div>

            <input type="hidden" name="domain" value="{{ $domain }}" />
            <input type="hidden" name="url" value="{{ $url }}" />

            <input type="submit" class="btn btn-primary" value="Finish">

        {{ Form::close() }}
    </div>

</div>

@stop