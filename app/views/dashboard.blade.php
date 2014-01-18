@extends('layout')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>Doolox Dashboard <small>Your WordPress Websites</small></h1>
        <ol class="breadcrumb">
            <li class="active"><i class="fa fa-dashboard"></i> Dashboard</li>
        </ol>
@if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ Session::get('success') }}
        </div>
@endif
    </div>
</div><!-- /.row -->


<div class="row">
    <div class="col-lg-12">
        <h2>Websites</h2>
        <div class="table-responsive">
            <table class="table table-hover table-striped tablesorter">
                <thead>
                    <tr>
                        <th class="header">Name <i class="fa"></i></th>
                        <th class="header">URL <i class="fa"></i></th>
                        <th class="header">Login <i class="fa"></i></th>
                    </tr>
                </thead>
                <tbody>
@foreach($wpsites as $wpsite)
                    <tr>
                        <td><a href="{{ url('wpsite', array('id' => $wpsite->id)) }}">{{ $wpsite->name }}</a></td>
                        <td>{{ str_replace('http://', '', str_replace('https://', '', $wpsite->url)) }}</td>
                        <td>
                            {{ Form::open(array('url' => $wpsite->url . 'wp-login.php', 'method' => 'post', 'id' => 'login-form-' . (string) $wpsite->id, 'target' => 'blank')) }}
                                <input type="hidden" name="log" value="{{ $wpsite->username }}" />
                                <input type="hidden" name="pwd" value="{{ $wpsite->password }}" />
                                <a href="javascript: void null;" onclick="$('#login-form-{{ $wpsite->id }}').submit();" class="btn btn-primary btn-xs">Login</a>
                            {{ Form::close() }}
                        </td>
                    </tr>
@endforeach
                </tbody>
            </table>
        </div>
        <a href="{{ route('doolox.wpsite_new') }}" class="btn btn-primary">Add New Website</a>
    </div>
</div>

@stop