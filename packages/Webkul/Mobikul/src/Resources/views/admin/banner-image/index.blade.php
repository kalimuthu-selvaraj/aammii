@extends('mobikul::admin.layouts.content')

@section('page_title')
    {{ __('mobikul::app.mobikul.banner-image.manage') }}
@stop

@section('content')

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('mobikul::app.mobikul.banner-image.manage') }}</h1>
            </div>
            <div class="page-action">
                <a href="{{ route('mobikul.banner-image.create') }}" class="btn btn-lg btn-primary">
                    {{ __('mobikul::app.mobikul.banner-image.add-image') }}
                </a>
            </div>
        </div>

        <div class="page-content">
            @inject('bannerImagesDataGrid','Webkul\Mobikul\DataGrids\BannerImagesDataGrid')
            {!! $bannerImagesDataGrid->render() !!}
        </div>
    </div>

@endsection

@push('scripts')
    <script>

        function reloadPage(getVar, getVal) {
            let url = new URL(window.location.href);
            url.searchParams.set(getVar, getVal);

            window.location.href = url.href;
        }

    </script>
@endpush
