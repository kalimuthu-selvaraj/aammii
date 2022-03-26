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
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>{{ __('inventorybulkupdate::app.admin.bulk-upload.messages.sorry') }}</strong> {{ __('inventorybulkupdate::app.admin.bulk-upload.messages.problem-with-below-records') }}
            </div>
            <div class="table" style="padding: 20px 0px; ">
            <table class="table">
            <thead>
                <tr >
                    @foreach ($errData[0] as $va=>$key)
                    <th class="grid_head">{{$va}}</th>
                    @endforeach
               </tr>
            </thead>
            <tbody>
            @foreach ($errData as $data)
                <tr>
                @foreach ($data as $va=>$key)
                <td>{{$key}}</td>
                @endforeach
                </tr>

            @endforeach
                </tbody>
            </table>
       </div>
        <div class="page-action">
            <a href="{{route('admin.inventorybulkupdate.index')}}" class="btn btn-lg btn-primary">
            {{ __('inventorybulkupdate::app.admin.bulk-upload.upload-files.ok')  }}
            </a>
        </div>
    </div>

@stop