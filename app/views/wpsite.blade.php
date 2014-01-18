@extends('layout')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>Update <small>Update Your WordPress Website</small></h1>
        <ol class="breadcrumb">
            <li class="active"><i class="fa fa-dashboard"></i> Update Website</li>
        </ol>
    </div>
</div><!-- /.row -->


<div class="row">
    
    <div class="col-lg-6">
        {{ Form::model($wpsite, array('route' => array('doolox.wpsite', $wpsite->id), 'role' => 'form')) }}

            <div class="form-group @if ($errors->has('email')) has-error @endif">
                {{ Form::label('name', 'Website Name') }}
                {{ Form::text('name', null, array('class' => 'form-control')) }}
            </div>

            <div class="form-group @if ($errors->has('email')) has-error @endif">
                {{ Form::label('url', 'Website URL') }}
                {{ Form::text('url', null, array('class' => 'form-control')) }}
            </div>

            <div class="form-group @if ($errors->has('email')) has-error @endif">
                {{ Form::label('admin_url', 'Website Admin URL') }}
                {{ Form::text('admin_url', null, array('class' => 'form-control')) }}
            </div>

            <input type="submit" class="btn btn-primary" value="Update Website" />

        {{ Form::close() }}
    </div>
</div>

@stop