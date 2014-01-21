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
@if($sites->count())
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
@foreach($sites as $site)
                    <tr>
                        <td><a href="{{ url('site', array('id' => $site->id)) }}">{{ $site->name }}</a></td>
                        <td>{{ str_replace('http://', '', str_replace('https://', '', $site->url)) }}</td>
                        <td>
                                <input type="hidden" value="{{ $site->url }}wp-login.php" id="url-{{ $site->id }}" />
                                <input type="hidden" value="{{ $site->username }}" id="username-{{ $site->id }}" />
                                <input type="hidden" value="{{ $site->password }}" id="password-{{ $site->id }}" />
                                <a href="javascript: void null;" onclick="wplogin({{ $site->id }});" class="btn btn-primary btn-xs">Login</a>
                        </td>
                    </tr>
@endforeach
                </tbody>
            </table>
        </div>
        <a class="btn btn-success" href="{{ route('doolox.site_install') }}">Install New Website</a>&nbsp;&nbsp;or&nbsp;&nbsp;<a href="{{ route('doolox.site_new') }}" class="btn btn-primary">Add Existing</a>
@else
<div class="bs-example">
              <div class="jumbotron">
                <h1>Welcome!</h1>
                <p>Welcome to your Doolox dashboard! Use the buttons bellow to install a new WordPress website or to add some existing ones to Doolox.</p>
                <p><a class="btn btn-success btn-lg" href="{{ route('doolox.site_install') }}">Install New Website</a>&nbsp;&nbsp;or&nbsp;&nbsp;<a class="btn btn-primary" href="{{ route('doolox.site_new') }}">Add Existing</a></p>
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