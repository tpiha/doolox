@extends('layout')

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
        {{ Form::open(array('route' => 'doolox.install2', 'role' => 'form')) }}
            <div class="form-group">
                <label>Which database to use?</label>
                <div class="radio" style="margin-top: 0px;">
                    <label>
                        {{ Form::radio('database', 'sqlite', true, array('tabindex' => 1)) }}
                        SQLite
                    </label>
                </div>
                <div class="radio">
                    <label>
                        {{ Form::radio('database', 'mysql', false) }}
                        MySQL
                    </label>
                </div>
                <div class="radio">
                    <label>
                        {{ Form::radio('database', 'postgresql', false) }}
                        PostgreSQL
                    </label>
                </div>
            </div>
        {{ Form::close() }}
    </div>
</div>
@stop