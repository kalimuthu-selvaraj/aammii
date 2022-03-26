@extends('mobikul::admin.layouts.content')

@section('page_title')
    {{ __('mobikul::app.mobikul.carousel.assign') }}
@stop

@section('content')

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>
                    <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ url('/admin/carousel') }}';"></i>
                    {{ __('mobikul::app.mobikul.carousel.assign') }} {{ $carousel['type'] == 'image_type' ? 'Image' : 'Product'}}
                </h1>
            </div>
        </div>

        <div class="page-content">
            @if ($carousel['type'] == 'image_type')
                @inject('assignCarouselImagesDataGrid','Webkul\Mobikul\DataGrids\AssignCarouselImagesDataGrid')
                {!! $assignCarouselImagesDataGrid->render() !!}
            @elseif ($carousel['type'] == 'product_type')
                @inject('productDataGrid','Webkul\Mobikul\DataGrids\ProductDataGrid')
                    {!! $productDataGrid->render() !!}
            @else
                @php
                    session()->flash('warning', trans('mobikul::app.mobikul.carousel.error-assign-featured'));
                @endphp
                <script type="text/javascript">
                    window.location = "{{ url('/admin/carousel') }}";
                </script>
            @endif
        </div>
    </div>

@endsection
