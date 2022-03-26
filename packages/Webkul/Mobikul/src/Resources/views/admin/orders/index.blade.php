@extends('mobikul::admin.layouts.content')

@section('page_title')
    {{ __('admin::app.sales.orders.title') }}
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('admin::app.sales.orders.title') }}</h1>
            </div>
        </div>

        <div class="page-content">
            @inject('orderGrid', 'Webkul\Mobikul\DataGrids\OrderDataGrid')
            {!! $orderGrid->render() !!}
        </div>
    </div>
@stop

@push('scripts')
    @include('admin::export.export', ['gridName' => $orderGrid])
@endpush