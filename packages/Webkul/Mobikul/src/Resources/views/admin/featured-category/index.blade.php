@extends('mobikul::admin.layouts.content')

@section('page_title')
    {{ __('mobikul::app.mobikul.category.manage') }}
@stop

@section('content')

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('mobikul::app.mobikul.category.manage') }}</h1>
            </div>
            <div class="page-action">
                <a href="{{ route('mobikul.featured-category.create') }}" class="btn btn-lg btn-primary">
                    {{ __('mobikul::app.mobikul.category.add-category') }}
                </a>
            </div>
        </div>

        <div class="page-content">
            @inject('featuredCategoriesDataGrid','Webkul\Mobikul\DataGrids\FeaturedCategoriesDataGrid')
            {!! $featuredCategoriesDataGrid->render() !!}
        </div>
    </div>

@endsection
