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
                        <th class="header">Type <i class="fa"></i></th>
                        <th class="header" style="text-align: center;">Login <i class="fa"></i></th>
                    </tr>
                </thead>
                <tbody>
@foreach($sites as $site)
                    <tr>
                        <td><a href="{{ url('site', array('id' => $site->id)) }}">{{ $site->name }}</a></td>
                        <td><a href="http://{{ $site->url }}" target="_blank">{{ $site->full_domain }}</a></td>
                        <td>@if ($site->local)<span class="label label-success">Local</span>@else<span class="label label-danger">Remote</span>@endif</td>
                        <td align="center">
@if($site->connected)
                                <a class="btn btn-primary btn-xs" href="javascript: void null;" onclick="wplogin({{ $site->id }}, '{{ $site->url }}');">Login</a>
@else
                                <span class="label label-danger">Not Connected</span>
@endif
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
    <input type="hidden" name="data" id="ciphertext" >
</form>
@stop