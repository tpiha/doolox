@extends('layout')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>New Domain <small>Add New Doolox Domain</small></h1>
        <ol class="breadcrumb">
            <li><i class="fa fa-dashboard"></i> <a href="{{ url() }}">Dashboard</a></li>
            <li class="active"><i class="fa fa-user"></i> New Domain</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        {{ Form::open(array('route' => 'domain.domain_new', 'role' => 'form')) }}

            <p class="help-block">required fields are marked with *</p>

            <div class="form-group @if ($errors->has('url')) has-error @endif">
                {{ Form::label('url', 'Domain *') }}
                <div class="input-group">
                    {{ Form::text('url', Input::old('url'), array('class' => 'form-control', 'tabindex' => 1)) }}
                    <span class="input-group-btn" style="vertical-align: bottom;">
                        <img src="{{ url() }}/images/ajax-loader.gif" class="domain-ajax-loader" id="ajax-loader" alt="" /><button tabindex="2" class="btn btn-default" type="button" onclick="check_domain();"><i class="fa fa-search"></i></button>
                    </span>
                </div>
                @if ($errors->has('url'))<p class="help-block">{{ $errors->first('url') }}</p>@endif
            </div>

            <div class="bs-example">
                <div id="domain-free" style="display: none;" class="alert alert-dismissable alert-success"><button type="button" class="close" data-dismiss="alert">×</button><p>This domain is free and you can register it.</p></div>
                <div id="domain-taken" style="display: none;" class="alert alert-dismissable alert-danger"><button type="button" class="close" data-dismiss="alert">×</button><p>This domain is taken. You can use it if you are the owner.</p></div>
                <div id="domain-invalid" style="display: none;" class="alert alert-dismissable alert-danger"><button type="button" class="close" data-dismiss="alert">×</button><p>This domain is not valid.</p></div>
                <div id="domain-doolox" style="display: none;" class="alert alert-dismissable alert-danger"><button type="button" class="close" data-dismiss="alert">×</button><p>This domain is already used on Doolox. You can't use it.</p></div>
            </div>
            <div id="owner-parent" style="display: none; margin-bottom: 20px;" class="checkbox"><label><input type="checkbox" value="1" name="owner" id="id_owner">Are you the owner of this domain?</label></div>

            <div class="form-group">
                <label>Configuration Type <a tabindex="3" href="https://www.doolox.com/how-to-add-your-existing-domain-to-doolox/" target="_blank"><i class="fa fa-question"></i></a></label>
                <div class="radio" style="margin-top: 0px;">
                    <label>
                        {{ Form::radio('type', 1, true, array('tabindex' => 4)) }}
                        Host your domain by yourself and set CNAME record (Free)
                    </label>
                </div>
                <div class="radio">
                    <label>
                        {{ Form::radio('type', 2, false) }}
                        Let us buy and setup a domain and email for you ($15.- annually)
                    </label>
                </div>
            </div>

            <input type="submit" class="btn btn-primary" value="Add Domain" tabindex="5" />

        {{ Form::close() }}

    </div>
</div>
@stop