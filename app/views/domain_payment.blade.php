@extends('layout')

@section('meta')
<title>Doolox Domain Payment</title>
<meta name="description" content="Doolox Domain Payment / Doolox is a free and Open Source WordPress management tool and website builder available both as a SaaS and for download.">
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>Domain Payment <small>Pay For Your Domain</small></h1>
        <ol class="breadcrumb">
            <li><i class="fa fa-dashboard"></i> <a href="{{ url() }}">Dashboard</a></li>
            <li class="active"><i class="fa fa-user"></i> Domain Payment</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <form action="subscription.php" method="post">
            <script
                src="https://button.paymill.com/v1/"
                id="button"
                data-label="Pay with CreditCard"
                data-title="Buy our subscription"
                data-description="It's a great product"
                data-submit-button="Subscribe 2.50 EUR/Month"
                data-amount="250"
                data-currency="EUR"
                data-public-key="9916911736996f1be3de85f9646d0ca7"
                data-elv="false" // Only for ELV payments
                data-lang="en-GB" // Optional language Code
                >
            </script>
        </form>
    </div>
</div>
@stop