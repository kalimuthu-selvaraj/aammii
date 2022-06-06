@extends('admin::layouts.content')

@section('page_title')
    {{ __('admin::app.sales.orders.title') }}
@stop
<style>
.table table thead th{
	padding:12px 4px !important;
}
.table table tbody td{
	padding:4px !important;
}
</style>
@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('admin::app.sales.orders.title') }}</h1>
            </div>

            <div class="page-action">
                <div class="export-import" @click="showModal('downloadDataGrid')">
                    <i class="export-icon"></i>
                    <span>
                        {{ __('admin::app.export.export') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="page-content">
            <datagrid-plus src="{{ route('admin.sales.orders.index') }}"></datagrid-plus>
        </div>
    </div>

    <modal id="downloadDataGrid" :is-open="modalIds.downloadDataGrid">
        <h3 slot="header">{{ __('admin::app.export.download') }}</h3>
        <div slot="body">
            <export-form></export-form>
        </div>
    </modal>

@stop

@push('scripts')
    @include('admin::export.orderexport', ['gridName' => app('Webkul\Admin\DataGrids\OrderReportExportDataGrid')])
@endpush