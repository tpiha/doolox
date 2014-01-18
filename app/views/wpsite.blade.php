@extends('layout')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>Update <small>Update Your WordPress Website</small></h1>
        <ol class="breadcrumb">
            <li><i class="fa fa-dashboard"></i> <a href="{{ url() }}">Dashboard</a></li>
            <li class="active"><i class="fa fa-edit"></i> Update Website</li>
        </ol>
    </div>
</div><!-- /.row -->


<div class="row">
    
    <div class="col-lg-6">
        {{ Form::model($wpsite, array('route' => array('doolox.wpsite', $wpsite->id), 'role' => 'form')) }}

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

            <div class="form-group @if ($errors->has('email')) has-error @endif">
                {{ Form::label('admin_url', 'Admin URL') }}
                {{ Form::text('admin_url', null, array('class' => 'form-control')) }}
            </div>

            <input type="submit" class="btn btn-primary" value="Update Website" />&nbsp;&nbsp;or&nbsp;&nbsp;<a class="text-danger" href="{{ route('doolox.wpsite_delete', $wpsite->id) }}">Delete</a>

        {{ Form::close() }}
    </div>
</div>

@stop