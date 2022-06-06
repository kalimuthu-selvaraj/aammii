@extends('admin::layouts.content')

@section('page_title')
    
    {{ __('inventorybulkupdate::app.admin.bulk-upload.index') }}
@stop

@section('content')


       <!-- Import New products -->
       <div class="import-new-products mt-45">
        <div class="heading mb-25">
            <h1>{{ __('inventorybulkupdate::app.admin.bulk-upload.manage-inventory-bulk-update') }}</h1>
        </div>

        <div class="import-new-products">
                    <form method="POST" action="{{ route('import-update-inventory-form-submit') }}" enctype="multipart/form-data">
                        @csrf
                        <?php $familyId = app('request')->input('family') ?>

                        <div class="page-content">
                           <a href="{{ route('download-inventory-update-sample-files') }}" class="pull-right">{{ __('inventorybulkupdate::app.admin.bulk-upload.upload-files.download-sample') }}</a>

                           <div class="control-group {{ $errors->first('attr_name') ? 'has-error' :'' }}">
                                <label class="required">{{ __('inventorybulkupdate::app.admin.bulk-upload.upload-files.attr-name') }} </label>
                                <select class="control" id="attr_name-id" name="attr_name" >
                                @foreach ($attrList as $attr)
                                    <option
                                        value="{{ $attr->code }}" {{  ($attr->code == "barcode") ? 'selected' : '' }}>
                                        {{ $attr->code }}
                                    </option>
                                @endforeach
                                

                                <span class="control-error">{{ $errors->first('attr_name') }}</span>
                            </div>

                            <div class="control-group {{ $errors->first('file_path') ? 'has-error' :'' }}">
                                <label class="required">{{ __('inventorybulkupdate::app.admin.bulk-upload.upload-files.file') }} </label>

                                <input type="file" class="control" name="file_path" id="file">

                                <span class="control-error">{{ $errors->first('file_path') }}</span>
                            </div>

                           
                        </div>

                        <div class="page-action">
                            <button type="submit" class="btn btn-lg btn-primary">
                            {{ __('inventorybulkupdate::app.admin.bulk-upload.upload-files.save')  }}
                            </button>
                        </div>
                    </form>
                </div>
    </div>

@stop