@extends('admin::layouts.content')

@section('page_title')
    {{ __('price_drop::app.admin.price-drop-log.title') }}
@stop

@section('content')
    <div class="content" style="height: 100%;">

        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('price_drop::app.admin.price-drop-log.title') }}</h1>
            </div>
        </div>

        {!! view_render_event('bagisto.admin.settings.price-drop-alert.list.before') !!}

        <div class="page-content">
             @inject('priceDropSubscriber', 'Webkul\PriceDropAlert\DataGrids\PriceDropSubscriberDataGrid')
            {!! $priceDropSubscriber->render() !!} 
        </div>

        {!! view_render_event('bagisto.admin.settings.price-drop-alert.list.after') !!}

    </div>
@stop