@extends('mobikul::admin.layouts.content')

@section('page_title')
    {{ __('mobikul::app.mobikul.carousel.manage') }}
@stop

@section('content')

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('mobikul::app.mobikul.carousel.manage') }}</h1>
            </div>
            <div class="page-action">
                <a href="{{ route('mobikul.carousel.image.create') }}" class="btn btn-lg btn-primary">
                    {{ __('mobikul::app.mobikul.banner-image.add-image') }}
                </a>
            </div>
        </div>

        <div class="page-content">
            @inject('carouselImagesDataGrid','Webkul\Mobikul\DataGrids\CarouselImagesDataGrid')
            {!! $carouselImagesDataGrid->render() !!}
        </div>
    </div>

@endsection
