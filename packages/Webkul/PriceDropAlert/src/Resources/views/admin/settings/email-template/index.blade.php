@extends('admin::layouts.content')

@section('page_title')
    {{ __('price_drop::app.admin.email-template.title') }}
@stop

@section('content')
    <div class="content" style="height: 100%;">
        <?php $locale = request()->get('locale') ?: null; ?>
        <?php $channel = request()->get('channel') ?: null; ?>
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('price_drop::app.admin.email-template.title') }}</h1>

                <div class="control-group">
                    <select class="control" id="locale-switcher" name="locale" onchange="reloadPage('locale', this.value)" >
                        <option value="all" {{ ! isset($locale) ? 'selected' : '' }}>
                            {{ __('admin::app.admin.system.all-locales') }}
                        </option>

                        @foreach (core()->getAllLocales() as $localeModel)
                            <option
                                    value="{{ $localeModel->code }}" {{ (isset($locale) && ($localeModel->code) == $locale) ? 'selected' : '' }}>
                                {{ $localeModel->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="page-action">
                <a href="{{ route('admin.price-alert.email-template.create') }}" class="btn btn-lg btn-primary">
                    {{ __('price_drop::app.admin.email-template.add-btn-title') }}
                </a>
            </div>
        </div>

        {!! view_render_event('bagisto.admin.settings.email-template.list.before') !!}

        <div class="page-content">
             @inject('email_templates', 'Webkul\PriceDropAlert\DataGrids\EmailTemplateDataGrid')
            {!! $email_templates->render() !!} 
        </div>

        {!! view_render_event('bagisto.admin.settings.email-template.list.after') !!}

    </div>
@stop

@push('scripts')
    <script>
        function reloadPage(getVar, getVal) {
            let url = new URL(window.location.href);
            url.searchParams.set(getVar, getVal);

            window.location.href = url.href;
        }
        
    </script>
@endpush