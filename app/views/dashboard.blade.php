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
@if(Session::has('error'))
        <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ Session::get('error') }}
        </div>
@endif
    </div>
</div><!-- /.row -->


<div class="row">
    <div class="col-lg-12">
@if($wpsites->count())
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
                                <input type="hidden" value="{{ $wpsite->url }}wp-login.php" id="url-{{ $wpsite->id }}" />
                                <input type="hidden" value="{{ $wpsite->username }}" id="username-{{ $wpsite->id }}" />
                                <input type="hidden" value="{{ $wpsite->password }}" id="password-{{ $wpsite->id }}" />
                                <a href="javascript: void null;" onclick="wplogin({{ $wpsite->id }});" class="btn btn-primary btn-xs">Login</a>
                        </td>
                    </tr>
@endforeach
                </tbody>
            </table>
        </div>
        <a href="{{ route('doolox.wpsite_new') }}" class="btn btn-primary">Add New Website</a>
@else
<div class="bs-example">
              <div class="jumbotron">
                <h1>Welcome!</h1>
                <p>Welcome to your Doolox dashboard! Use a button below to add your first WordPress website.</p>
                <p><a class="btn btn-primary btn-lg" href="{{ route('doolox.wpsite_new') }}">Add New Website</a></p>
              </div>
            </div>
@endif
    </div>
</div>
<form method="POST" action="" id="wploginform" target="blank">
    <input type="hidden" name="log" value="" id="log" />
    <input type="hidden" name="pwd" value="" id="pwd" />
</form>
@stop