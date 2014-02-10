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
        {{ Form::model($site, array('route' => array('doolox.site', $site->id), 'role' => 'form')) }}

            <p class="help-block">required fields are marked with *</p>

            <div class="form-group @if ($errors->has('name')) has-error @endif">
                {{ Form::label('name', 'Name *') }}
                {{ Form::text('name', null, array('class' => 'form-control', 'tabindex' => 1)) }}
                @if ($errors->has('name'))<p class="help-block">{{ $errors->first('name') }}</p>@endif
            </div>

            <div class="form-group @if ($errors->has('url')) has-error @endif">
                {{ Form::label('url', 'URL *') }}
                {{ Form::text('url', null, array('class' => 'form-control', 'disabled' => 'disabled')) }}
                @if ($errors->has('url'))<p class="help-block">{{ $errors->first('url') }}</p>@endif
            </div>

            <div class="form-group @if ($errors->has('email')) has-error @endif">
                {{ Form::label('admin_url', 'Alternative for wp-login.php') }}
                {{ Form::text('admin_url', null, array('class' => 'form-control', 'tabindex' => 2)) }}
            </div>

            <input type="submit" class="btn btn-primary" value="Update Website" tabindex="3" />

        {{ Form::close() }}
    </div>

    <div class="col-lg-6">
        <h4>Actions</h4>
        <div class="well">
            <a onclick="wpconnect({{ $site->id }}, '{{ $site->url }}', '{{ $site->admin_url }}');" href="javascript: void null;" class="btn btn-success">Connect</a>
@if($site->local)
            <a href="{{ route('doolox.site_move', $site->id) }}" class="btn btn-success">Move</a>
@endif
            <a class="btn btn-danger" href="javascript: void null;" onclick="bootbox.confirm('Are you sure you want to delete this website?', function(result) { if (result) { window.location.href = '{{ route('doolox.site_delete', $site->id) }}'; }});">Delete</a>
        </div>

        <h4>Users</h4>
        <div class="well">
            {{ Form::open(array('route' => array('doolox.site_adduser', $site->id), 'method' => 'post', 'role' => 'form')) }}
            <div class="form-group @if(Session::has('error')) has-error @endif">
                {{ Form::label('email', 'User Email *') }}
                {{ Form::text('email', null, array('class' => 'form-control')) }}
                @if(Session::has('error'))<p class="help-block">{{ Session::get('error') }}</p>@endif
            </div>
            <input type="hidden" name="id" value="{{ $site->id }}" />
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
@foreach($site->getUsers as $user)
                    <tr>
                        <td>{{ $user->email }}</td>
                        <td align="center">@if(Sentry::getUser()->email != $user->email)<a href="{{ route('doolox.site_rmuser', array('id' => $site->id,'user_id' => $user->id)) }}"><i class="fa fa-minus-square"></i></a>@endif</td>
                    </tr>
@endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
<form method="POST" action="" id="wploginform" target="blank">
    <input type="hidden" name="data" id="ciphertext" >
</form>
@stop