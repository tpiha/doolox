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

            <input type="submit" class="btn btn-primary" value="Update Website" />&nbsp;&nbsp;or&nbsp;&nbsp;<a class="text-danger" href="javascript: void null;" onclick="bootbox.confirm('Are you sure you want to delete this website?', function(result) { if (result) { window.location.href = '{{ route('doolox.wpsite_delete', $wpsite->id) }}'; }});">Delete</a>

        {{ Form::close() }}
    </div>

    <div class="col-lg-6">
        <h4>Users</h4>
        <div class="well">
            {{ Form::open(array('route' => array('doolox.wpsite_adduser', $wpsite->id), 'method' => 'post', 'role' => 'form')) }}
            <div class="form-group @if(Session::has('error')) has-error @endif">
                {{ Form::label('email', 'User Email *') }}
                {{ Form::text('email', null, array('class' => 'form-control')) }}
                @if(Session::has('error'))<p class="help-block">{{ Session::get('error') }}</p>@endif
            </div>
            <input type="hidden" name="id" value="{{ $wpsite->id }}" />
            <input type="submit" class="btn btn-primary" value="Add User" />
            {{ Form::close() }}
            <table class="table table-hover tablesorter">
                <thead>
                    <tr>
                        <th class="header">User</th>
                        <th class="header" style="text-align: center;">Remove</th>
                    </tr>
                </thead>
                <tbody>
@foreach($wpsite->getUsers as $user)
                    <tr>
                        <td>{{ $user->email }}</td>
                        <td align="center">@if(Sentry::getUser()->email != $user->email)<a href="{{ route('doolox.wpsite_rmuser', array('id' => $wpsite->id,'user_id' => $user->id)) }}"><i class="fa fa-minus-square"></i></a>@endif</td>
                    </tr>
@endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@stop