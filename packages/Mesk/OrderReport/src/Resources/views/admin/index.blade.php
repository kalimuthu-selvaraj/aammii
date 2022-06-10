@extends('admin::layouts.content')

@section('page_title')
    {{ __('orderreport::app.admin.order-report.title') }}
@stop

<style>
    .datagrid-filters {
		display: none !important;
	}
	.table table tbody td{
		padding:4px !important;
	}
	.table table thead th{
		padding:12px 5px !important;
	}
	
</style>
 
@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('orderreport::app.admin.order-report.title') }}</h1>
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
		
		<div class="import-product">
			<form id="filter_form"  action="{{ route('admin.orderreport.index') }}" method="get">
				<div class="account-table-content">
 					<date-filter></date-filter>
					<div class="control-group select" style="margin-top: -95px;margin-left: 560px;width: 350px;">
                        <label for="payment-method" class="required">{{ __('orderreport::app.admin.order-report.status') }} </label>
                        <select id="status" name="status" class="control" v-validate="'required'" data-vv-as="&quot;{{ __('admin::app.sales.transactions.payment-method') }}&quot;">
                             @foreach($status as $key => $value)
								<option value="{{ $key }}" {{ isset($filterData['status']) && $filterData['status'] == $key ? 'selected' : ''  }}>{{ $value }}</option>
							@endforeach							
                        </select>
                        <span class="control-error" v-if="errors.has('payment_method')">@{{ errors.first('payment_method') }}</span>
                    <button type="submit" class="btn btn-lg btn-primary apply-filter" style="margin-left: 16px;">{{ __('orderreport::app.admin.order-report.filter') }}</button>
					</div>
					
				</div>
			</form>
		</div>
			<div class="page-content">
				<datagrid-plus src="{{ route('admin.orderreport.index') }}"></datagrid-plus>
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

@push('scripts')

<script type="text/x-template" id="date-filter-template">
<div>

<div class="control-group date"  style="width: 350px;">
<label>{{ __('orderreport::app.admin.order-report.from-date') }} </label>
<date hide-remove-button="1"><input type="text" class="control" id="start_date" name="start_date" placeholder="{{ __('admin::app.dashboard.from') }}" v-model="start"/></date>
</div>

<div class="control-group date"  style="width: 350px;margin-top: -95px;margin-left: 280px;">
<label>{{ __('orderreport::app.admin.order-report.to-date') }} </label>
<date hide-remove-button="1"><input type="text" class="control" id="end_date" name="end_date" placeholder="{{ __('admin::app.dashboard.to') }}" v-model="end"/></date>
</div>
 
</div>
</script>
<script>
	Vue.component('date-filter', {

		template: '#date-filter-template',

		data: function() {
			return {
				start: "{{ isset($filterData['start_date']) ? $filterData['start_date'] : date('Y-m-d') }}",
				end: "{{ isset($filterData['end_date']) ? $filterData['end_date'] : date('Y-m-d') }}",
			}
		},

		methods: {
		 
		}
	});

	function parse_query_string(query) {
		var vars = query.split("&");
		var query_string = {};
		for (var i = 0; i < vars.length; i++) {
			var pair = vars[i].split("=");
			var key = decodeURIComponent(pair.shift());
			var value = decodeURIComponent(pair.join("="));
 			if (typeof query_string[key] === "undefined") {
				query_string[key] = value;
 			} else if (typeof query_string[key] === "string") {
				var arr = [query_string[key], value];
				query_string[key] = arr;
 			} else {
				query_string[key].push(value);
			}
		}
		return query_string;
	}
	$(document).ready(function(){
		var query_string = window.location.href;
		query_string = query_string.split("?").pop();
		var parsed_qs = parse_query_string(query_string);
		if(typeof(parsed_qs.start_date) !='undefined'){
			setTimeout(function(){$("#start_date").val(parsed_qs.start_date)},500);
		}
		if(typeof(parsed_qs.end_date) !='undefined'){
			setTimeout(function(){$("#end_date").val(parsed_qs.end_date)},500);
		}
		if(typeof(parsed_qs.status) !='undefined'){
			setTimeout(function(){$("#status").val(parsed_qs.status)},500);
		}
	});
</script>
@endpush