@extends('layout')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>User Management <small>Manage Your Doolox Users</small></h1>
        <ol class="breadcrumb">
            <li><i class="fa fa-dashboard"></i> <a href="{{ url() }}">Dashboard</a></li>
            <li class="active"><i class="fa fa-users"></i> User Management</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-hover table-striped tablesorter">
                <thead>
                    <tr>
                        <th class="header">Email <i class="fa"></i></th>
                        <th class="header" style="text-align: center;">Edit <i class="fa"></i></th>
                        <th class="header" style="text-align: center;">Delete <i class="fa"></i></th>
                    </tr>
                </thead>
                <tbody>
@foreach($users as $user)
                    <tr>
                        <td>{{ $user->email }}</td>
                        <td align="center"><a href="#"><i class="fa fa-pencil-square"></i></a></td>
                        <td align="center"><a href="#"><i class="fa fa-minus-square"></i></a></td>
                    </tr>
@endforeach
                </tbody>
            </table>
        </div>
        <a href="{{ route('user.user_new') }}" class="btn btn-primary">Add New User</a>
    </div>
</div>
@stop

@section('specific')
<script type="text/javascript">
    $('.dropdown-menu').show();
</script>
@stop