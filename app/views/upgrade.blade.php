@extends('layout')

@section('meta')
<title>Doolox Upgrade</title>
<meta name="description" content="Doolox Upgrade / Doolox is a free and Open Source WordPress management tool and website builder available both as a SaaS and for download.">
@stop

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
                  Price / <strong>month</strong>
                </li>
            </ul>
            <div class="list-group">
                <form method="POST" action="https://sites.fastspring.com/doolox/product/pro1month?action=order&amp;referrer={{ Sentry::getUser()->id }}" target="_top" id="id_pro" onsubmit="_gaq.push(['_linkByPost', this]);">
                    <a href="javascript: void null;" onclick="$('#id_pro').submit();" class="list-group-item active">
                        <strong>Upgrade to Doolox Pro</strong>
                    </a>
                </form>
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
                  Price / <strong>month</strong>
                </li>
            </ul>
            <div class="list-group">
                <form method="POST" action="https://sites.fastspring.com/doolox/product/business1month?action=order&amp;referrer={{ Sentry::getUser()->id }}" target="_top" id="id_business" onsubmit="_gaq.push(['_linkByPost', this]);">
                    <a href="javascript: void null;" onclick="$('#id_business').submit();" class="list-group-item active">
                        <strong>Upgrade to Doolox Business</strong>
                    </a>
                </form>
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
                  Price / <strong>month</strong>
                </li>
            </ul>
            <div class="list-group">
                <form method="POST" action="https://sites.fastspring.com/doolox/product/unlimited1month?action=order&amp;referrer={{ Sentry::getUser()->id }}" target="_top" id="id_unlimited" onsubmit="_gaq.push(['_linkByPost', this]);">
                    <a href="javascript: void null;" onclick="$('#id_unlimited').submit();" class="list-group-item active">
                        <strong>Upgrade to Doolox Unlimited</strong>
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
@stop