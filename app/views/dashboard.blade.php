@extends('layout')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>Doolox Dashboard <small>Your WordPress Websites</small></h1>
        <ol class="breadcrumb">
            <li class="active"><i class="fa fa-dashboard"></i> Dashboard</li>
        </ol>
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
                        <td>{{ $wpsite->name }}</td>
                        <td>{{ str_replace('http://', '', str_replace('https://', '', $wpsite->url)) }}</td>
                        <td>
                            {{ Form::open(array('url' => $wpsite->url . 'wp-login.php', 'method' => 'post', 'id' => 'login-form-' . (string) $wpsite->id, 'target' => 'blank')) }}
                                <input type="hidden" name="log" value="{{ $wpsite->username }}" />
                                <input type="hidden" name="pwd" value="{{ $wpsite->password }}" />
                                <a href="javascript: void null;" onclick="$('#login-form-{{ $wpsite->id }}').submit();">Login</a>
                            {{ Form::close() }}
                        </td>
                    </tr>
@endforeach
                </tbody>
            </table>
        </div>
        <a href="/" class="btn btn-primary">Add New Website</a>
    </div>
</div>

@stop