@extends('admin::layouts.content')

@section('page_title')
    
    {{ __('orderbulkupdate::app.admin.bulk-upload.index') }}
@stop
<style>
.control-group.date:after, .control-group.datetime:after{
	top: 33% !important;
}
</style>
@section('content')
	<div class="account-layout">
	<!-- download samples -->
	<accordian :title="'{{ __('orderbulkupdate::app.admin.bulk-upload.upload-files.download-orders') }}'" :active="true">
	<div slot="body">
		<div class="import-product">
			<form action="{{ route('download-order-update-sample-files') }}" method="post">
				<div class="account-table-content">
					@csrf
					<date-filter></date-filter>
					<div class="control-group">
  						<div class="mt-10">
 								<button type="submit" class="btn btn-lg btn-primary">
									{{ __('orderbulkupdate::app.admin.bulk-upload.upload-files.download-sample') }}
								</button>
 						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	</accordian>

	<!-- Import New products -->
	<accordian :title="'{{ __('orderbulkupdate::app.admin.bulk-upload.upload-files.bulk-upload') }}'" :active="true">
		<div slot="body">
			<div class="import-new-products">
				<form method="POST" action="{{ route('import-update-order-form-submit') }}" enctype="multipart/form-data">
					@csrf
 
					<div class="page-content">
						<div class="control-group {{ $errors->first('file_path') ? 'has-error' :'' }}">
							<label class="required">{{ __('bulkupload::app.admin.bulk-upload.upload-files.file') }} </label>

							<input type="file" class="control" name="file_path" id="file">

							<span class="control-error">{{ $errors->first('file_path') }}</span>
						</div>
					</div>

					<div class="page-action">
						<button type="submit" class="btn btn-lg btn-primary">
						{{ __('orderbulkupdate::app.admin.bulk-upload.upload-files.upload')  }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</accordian>
	</div>
@stop
@push('scripts')

<script type="text/x-template" id="date-filter-template">
<div>
<div class="control-group date"> 
<date hide-remove-button="1"><input type="text" class="control" id="start_date" name="start" value="{{ isset($startDate) ? $startDate->format('Y-m-d') : date('Y-m-d') }}" placeholder="{{ __('admin::app.dashboard.from') }}" v-model="start"/></date>
</div>

<div class="control-group date">
<date hide-remove-button="1"><input type="text" class="control" id="end_date" name="end" value="{{ isset($endDate) ? $endDate->format('Y-m-d') : date('Y-m-d') }}" placeholder="{{ __('admin::app.dashboard.to') }}" v-model="end"/></date>
</div>
 
</div>
</script>
<script>
	Vue.component('date-filter', {

		template: '#date-filter-template',

		data: function() {
			return {
				start: "{{ isset($startDate) ? $startDate->format('Y-m-d') : date('Y-m-d') }}",
				end: "{{ isset($endDate) ? $endDate->format('Y-m-d') : date('Y-m-d') }}",
			}
		},

		methods: {
		 
		}
	});
</script>
@endpush