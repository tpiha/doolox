@extends('layout')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>Install <small>Install New WordPress Website</small></h1>
        <ol class="breadcrumb">
            <li><i class="fa fa-dashboard"></i> <a href="{{ url() }}">Dashboard</a></li>
            <li class="active"><i class="fa fa-plus-square"></i> Install Website</li>
        </ol>
    </div>
</div><!-- /.row -->


<div class="row">
    
    <div class="col-lg-6">
        <div class="bs-example">
            <ul class="nav nav-tabs" style="margin-bottom: 15px;">
@if (Config::get('doolox.hosting'))
                <li class="active"><a href="#doolox" data-toggle="tab">Install On Doolox</a></li>
                <li><a href="#ftp" data-toggle="tab">Install On Remote FTP</a></li></li>
@else
                <li class="active"><a href="#ftp" data-toggle="tab">Install On Remote FTP</a></li></li>
@endif
            </ul>
            <div id="myTabContent" class="tab-content">
@if (Config::get('doolox.hosting'))
                <div class="tab-pane fade active in" id="doolox">
                    {{ Form::open(array('route' => 'doolox.site_install', 'method' => 'post', 'role' => 'form')) }}

                    <div class="form-group @if ($errors->has('name')) has-error @endif">
                        {{ Form::label('domain', 'Domain') }}
                        {{ Form::select('domain', array('1' => 'doolox.loc'), '1', array('class' => 'form-control')); }}
                    </div>

                    <div class="form-group @if ($errors->has('url')) has-error @endif">
                        {{ Form::label('url', 'URL') }}
                        <div class="input-group">
                            {{ Form::text('url', '.doolox.loc', array('class' => 'form-control', 'onclick' => 'update_caret();', 'onfocus' => 'update_caret();', 'onkeyup' => 'update_caret();', 'onkeydown' => 'update_caret();')) }}
                            <span class="input-group-btn" style="vertical-align: bottom;">
                                <img src="{{ url() }}/images/ajax-loader.gif" class="domain-ajax-loader" id="ajax-loader" alt="" /><button class="btn btn-default" type="button" onclick="check_domain();"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                        @if ($errors->has('url'))<p class="help-block">{{ $errors->first('url') }}</p>@endif
                    </div>
                    <div class="bs-example" id="domain-free" style="display: none;"><div class="alert alert-dismissable alert-success"><button type="button" class="close" data-dismiss="alert">×</button><p>This domain is free and you can register it.</p></div></div>
                    <div class="bs-example" id="domain-taken" style="display: none;"><div class="alert alert-dismissable alert-danger"><button type="button" class="close" data-dismiss="alert">×</button><p>This domain is taken. You can use it if you are the owner.</p></div></div>
                    <div class="bs-example" id="domain-invalid" style="display: none;"><div class="alert alert-dismissable alert-danger"><button type="button" class="close" data-dismiss="alert">×</button><p>This domain is not valid.</p></div></div>
                    <div class="bs-example" id="domain-doolox" style="display: none;"><div class="alert alert-dismissable alert-danger"><button type="button" class="close" data-dismiss="alert">×</button><p>This domain is already used on Doolox. You can't use it.</p></div></div>
                    <div id="owner-parent" style="display: none;" class="checkbox"><label><input type="checkbox" value="" name="owner" id="id_owner">Are you the owner of this domain?</label></div>
                    <input type="submit" class="btn btn-primary" value="Continue">
                    {{ Form::close() }}
                </div>
                <div class="tab-pane fade" id="ftp">
                    <p>Food truck fixie locavore, accusamus mcsweeney's marfa nulla single-origin coffee squid. Exercitation +1 labore velit, blog sartorial PBR leggings next level wes anderson artisan four loko farm-to-table craft beer twee. Qui photo booth letterpress, commodo enim craft beer mlkshk aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthetic magna delectus mollit.</p>
                </div>
@else
                <div class="tab-pane fade active in" id="ftp">
                    <p>Food truck fixie locavore, accusamus mcsweeney's marfa nulla single-origin coffee squid. Exercitation +1 labore velit, blog sartorial PBR leggings next level wes anderson artisan four loko farm-to-table craft beer twee. Qui photo booth letterpress, commodo enim craft beer mlkshk aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthetic magna delectus mollit.</p>
                </div>
@endif
            </div>
        </div>
    </div>

</div>

@stop