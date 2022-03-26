@extends('mobikul::admin.layouts.content')

@section('page_title')
    {{ __('mobikul::app.mobikul.notification.manage') }}
@stop

@section('content')

    <div class="content">
        <?php $locale = request()->get('locale') ?: null; ?>
        <?php $channel = request()->get('channel') ?: null; ?>
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('mobikul::app.mobikul.notification.manage') }}</h1>
            </div>
            <div class="page-action">
                <a href="{{ route('mobikul.notification.create') }}" class="btn btn-lg btn-primary">
                    {{ __('mobikul::app.mobikul.notification.add-notification') }}
                </a>
            </div>
        </div>

        <div class="page-content">
            @inject('notificationDataGrid','Webkul\Mobikul\DataGrids\NotificationsDataGrid')
            {!! $notificationDataGrid->render() !!}
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
