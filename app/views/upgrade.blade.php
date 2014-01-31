@extends('layout')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>Upgrade Doolox Account <small>Choose a Doolox Plan</small></h1>
        <ol class="breadcrumb">
            <li><i class="fa fa-dashboard"></i> <a href="{{ url() }}">Dashboard</a></li>
            <li class="active"><i class="fa fa-edit"></i> Upgrade Account</li>
        </ol>
    </div>
</div>
<div class="row">

    <div class="col-lg-4">
        <div class="bs-example">
            <ul class="list-group">
                <li class="list-group-item">
                  <span class="badge">30</span>
                  Websites to manage
                </li>
                <li class="list-group-item">
                  <span class="badge">1</span>
                  Websites to install on Doolox
                </li>
                <li class="list-group-item">
                  <span class="badge">1 GB</span>
                  Disc space
                </li>
                <li class="list-group-item">
                  <span class="badge">$14.-</span>
                  Price (monthly)
                </li>
            </ul>
            <div class="list-group">
                <a href="https://sites.fastspring.com/doolox/instant/pro1month" class="list-group-item active">
                    <strong>Upgrade to Doolox Pro</strong>
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="bs-example">
            <ul class="list-group">
                <li class="list-group-item">
                  <span class="badge">200</span>
                  Websites to manage
                </li>
                <li class="list-group-item">
                  <span class="badge">50</span>
                  Websites to install on Doolox
                </li>
                <li class="list-group-item">
                  <span class="badge">50 GB</span>
                  Disc space
                </li>
                <li class="list-group-item">
                  <span class="badge">$29.-</span>
                  Price (monthly)
                </li>
            </ul>
            <div class="list-group">
                <a href="https://sites.fastspring.com/doolox/instant/business1month" class="list-group-item active">
                    <strong>Upgrade to Doolox Business</strong>
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="bs-example">
            <ul class="list-group">
                <li class="list-group-item">
                  <span class="badge">unlimited</span>
                  Websites to manage
                </li>
                <li class="list-group-item">
                  <span class="badge">unlimited</span>
                  Websites to install on Doolox
                </li>
                <li class="list-group-item">
                  <span class="badge">200 GB</span>
                  Disc space
                </li>
                <li class="list-group-item">
                  <span class="badge">$59.-</span>
                  Price (monthly)
                </li>
            </ul>
            <div class="list-group">
                <a href="https://sites.fastspring.com/doolox/instant/unlimited1month" class="list-group-item active">
                    <strong>Upgrade to Doolox Unlimited</strong>
                </a>
            </div>
        </div>
    </div>
</div>
@stop