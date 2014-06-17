@extends('layout')

@section('meta')
<title>Doolox Domain Management</title>
<meta name="description" content="Doolox Domain Management / Doolox is a free and Open Source WordPress management tool and website builder available both as a SaaS and for download.">
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>Domain Management <small>Manage Your Doolox Domains</small></h1>
        <ol class="breadcrumb">
            <li><i class="fa fa-dashboard"></i> <a href="{{ url() }}">Dashboard</a></li>
            <li class="active"><i class="fa fa-users"></i> Domain Management</li>
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
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-hover table-striped tablesorter">
                <thead>
                    <tr>
                        <th class="header">Domain <i class="fa"></i></th>
                        <th class="header">Activated <i class="fa"></i></th>
                        <th class="header" style="text-align: center;">Delete <i class="fa"></i></th>
                    </tr>
                </thead>
                <tbody>
@foreach($domains as $domain)
                    <tr>
                        <td>{{ $domain->url }}</td>
                        <td>@if ($domain->activated)<span class="label label-success">Yes</span>@else<span class="label label-danger">No</span>@endif</td>
                        <td align="center">@if($domain->url != Config::get('doolox.system_domain'))<a href="javascript: void null;" onclick="bootbox.confirm('Are you sure you want to delete this domain?', function(result) { if (result) { window.location.href = '{{ route('domain.domain_delete', array('id' => $domain->id)) }}'; }});"><i class="fa fa-minus-square"></i></a>@endif</td>
                    </tr>
@endforeach
                </tbody>
            </table>
        </div>
        <a href="{{ route('domain.domain_new') }}" class="btn btn-primary">Add New Domain</a>
    </div>
</div>
@stop