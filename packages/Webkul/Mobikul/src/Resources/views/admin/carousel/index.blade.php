@extends('mobikul::admin.layouts.content')

@section('page_title')
    {{ __('mobikul::app.mobikul.carousel.manage-carousel') }}
@stop

@section('content')

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('mobikul::app.mobikul.carousel.manage-carousel') }}</h1>
            </div>
            <div class="page-action">
                <a href="{{ route('mobikul.carousel.create') }}" class="btn btn-lg btn-primary">
                    {{ __('mobikul::app.mobikul.carousel.add-carousel') }}
                </a>
            </div>
        </div>

        <div class="page-content">
            @inject('carouselDataGrid','Webkul\Mobikul\DataGrids\CarouselDataGrid')
            {!! $carouselDataGrid->render() !!}
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
