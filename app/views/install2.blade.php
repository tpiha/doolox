@extends('layout')

@section('meta')
<title>Doolox Installation Wizard</title>
<meta name="description" content="Doolox Installation Wizard / Doolox is a free and Open Source WordPress management tool and website builder available both as a SaaS and for download.">
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>Install Doolox <small>Doolox Installation Wizard</small></h1>
        <ol class="breadcrumb">
            <li class="active"><i class="fa fa-gears"></i> Install (Step 2)</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
@if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ Session::get('success') }}
        </div>
@else

        {{ Form::open(array('route' => 'doolox.install2', 'role' => 'form')) }}

            <div class="form-group">
                <label>Which database to use?</label>
                <div class="radio" style="margin-top: 0px;">
                    <label>
                        {{ Form::radio('database', 'sqlite', (Input::old('database', 'sqlite') == 'sqlite'), array('tabindex' => 1)) }}
                        SQLite
                    </label>
                </div>
                <div class="radio">
                    <label>
                        {{ Form::radio('database', 'mysql', (Input::old('database') == 'mysql')) }}
                        MySQL
                    </label>
                </div>
                <div class="radio">
                    <label>
                        {{ Form::radio('database', 'pgsql', (Input::old('database') == 'pgsql')) }}
                        PostgreSQL
                    </label>
                </div>
                @if ($errors->has('database'))<div class="form-group has-error">
                <p class="help-block">{{ $errors->first('database') }}</p>
                </div>@endif
            </div>

            <div id="dbdata" style="display: none;">
                <div class="form-group @if ($errors->has('dbhost')) has-error @endif">
                    <label>Database Host</label>
                    {{ Form::text('dbhost', Input::old('dbhost'), array('class' => 'form-control', 'tabindex' => 2)) }}
                    @if ($errors->has('dbhost'))<p class="help-block">{{ $errors->first('dbhost') }}</p>@endif
                </div>

                <div class="form-group @if ($errors->has('dbname')) has-error @endif">
                    <label>Database Name</label>
                    {{ Form::text('dbname', Input::old('dbname'), array('class' => 'form-control', 'tabindex' => 3)) }}
                    @if ($errors->has('dbname'))<p class="help-block">{{ $errors->first('dbname') }}</p>@endif
                </div>

                <div class="form-group @if ($errors->has('dbuser')) has-error @endif">
                    <label>Database Username</label>
                    {{ Form::text('dbuser', Input::old('dbuser'), array('class' => 'form-control', 'tabindex' => 4)) }}
                    @if ($errors->has('dbuser'))<p class="help-block">{{ $errors->first('dbuser') }}</p>@endif
                </div>

                <div class="form-group @if ($errors->has('dbpass')) has-error @endif">
                    <label>Database Password</label>
                    {{ Form::text('dbpass', Input::old('dbpass'), array('class' => 'form-control', 'tabindex' => 5)) }}
                    @if ($errors->has('dbpass'))<p class="help-block">{{ $errors->first('dbpass') }}</p>@endif
                </div>
            </div>

            <input type="submit" class="btn btn-success" value="Finish Installation" tabindex="6" />

        {{ Form::close() }}

@endif
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('input[type=radio][name=database]').change(function() {
            if (this.value == 'mysql' || this.value == 'pgsql') {
                $('#dbdata').fadeIn();                
            }
            else {
                $('#dbdata').fadeOut();
            }
    });
});
</script>
@stop