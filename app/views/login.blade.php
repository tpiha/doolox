@extends('layout')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>Doolox Login <small>Login To Access Your Dashboard</small></h1>
        <ol class="breadcrumb">
            <li class="active"><i class="fa fa-sign-in"></i> Login</li>
        </ol>    
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        {{ Form::open(array('url' => 'login', 'method' => 'post', 'role' => 'form')) }}

            <div class="form-group">
                <label>Email</label>
                <input class="form-control" />
            </div>

            <div class="form-group">
                <label>Password</label>
                <input class="form-control" type="password" />
            </div>

            <button type="submit" class="btn btn-default">Login</button>

        {{ Form::close() }}
    </div>
</div>
@stop