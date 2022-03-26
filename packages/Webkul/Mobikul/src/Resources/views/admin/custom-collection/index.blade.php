@extends('mobikul::admin.layouts.content')

@section('page_title')
    {{ __('mobikul::app.mobikul.custom-collection.title') }}
@stop

@section('content')

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('mobikul::app.mobikul.custom-collection.title') }}</h1>
            </div>
            <div class="page-action">
                <a href="{{ route('mobikul.custom-collection.create') }}" class="btn btn-lg btn-primary">
                    {{ __('mobikul::app.mobikul.custom-collection.add-title') }}
                </a>
            </div>
        </div>

        <div class="page-content">
            @inject('customCollectionDataGrid','Webkul\Mobikul\DataGrids\CustomCollectionDataGrid')
            {!! $customCollectionDataGrid->render() !!}
        </div>
    </div>

@endsection
