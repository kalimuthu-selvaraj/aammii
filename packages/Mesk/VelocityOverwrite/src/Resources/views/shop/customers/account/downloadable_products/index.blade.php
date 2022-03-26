@extends('shop::customers.account.index')

@section('page_title')
    {{ __('shop::app.customer.account.downloadable_products.title') }}
@endsection

@section('page-detail-wrapper')
    <div class="account-head mb-10">
        <span class="back-icon"><a href="{{ route('customer.account.index') }}"><i class="icon icon-menu-back"></i></a></span>
        <span class="account-heading">
            {{ __('shop::app.customer.account.downloadable_products.title') }} <img loading="lazy" alt="wave" src="{{ asset('/themes/velocity/assets/images/wave-1.png') }}">
        </span>

        <div class="horizontal-rule"></div>
    </div>

    {!! view_render_event('bagisto.shop.customers.account.downloadable_products.list.before') !!}

        <div class="account-items-list">
            <div class="account-table-content">

                {!! app('Webkul\Shop\DataGrids\DownloadableProductDataGrid')->render() !!}

            </div>
        </div>

    {!! view_render_event('bagisto.shop.customers.account.downloadable_products.list.after') !!}
@endsection