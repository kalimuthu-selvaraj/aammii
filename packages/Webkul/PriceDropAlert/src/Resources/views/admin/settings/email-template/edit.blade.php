@extends('admin::layouts.content')

@section('page_title')
    {{ __('price_drop::app.admin.email-template.edit-title') }}
@stop

@section('content')
    <div class="content">
        <?php $locale = request()->get('locale') ?: app()->getLocale(); ?>
        <?php $channel = request()->get('channel') ?: core()->getDefaultChannelCode(); ?>

        <form method="POST" action="" @submit.prevent="onSubmit" enctype="multipart/form-data">

            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ url('/admin/dashboard') }}';"></i>

                        {{ __('price_drop::app.admin.email-template.edit-title') }}
                    </h1>

                    <div class="control-group">
                        <select class="control" id="locale-switcher" onChange="window.location.href = this.value">
                            @foreach (core()->getAllLocales() as $localeModel)

                                <option value="{{ route('admin.price-alert.email-template.update', $email_template->id) . '?locale=' . $localeModel->code }}" {{ ($localeModel->code) == $locale ? 'selected' : '' }}>
                                    {{ $localeModel->name }}
                                </option>

                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="page-action">
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('price_drop::app.admin.email-template.save-btn-title') }}
                    </button>
                </div>
            </div>

            <div class="page-content">
                <div class="form-container">
                    @csrf()
                    <input name="_method" type="hidden" value="PUT">

                    {!! view_render_event('bagisto.admin.settings.email_templates.edit_form_accordian.general.before') !!}

                    <accordian :title="'{{ __('price_drop::app.admin.email-template.general') }}'" :active="true">
                        <div slot="body">
                            
                            {!! view_render_event('bagisto.admin.settings.email_templates.edit_form_accordian.general.before', ['email_template' => $email_template]) !!}

                            <div class="control-group" :class="[errors.has('{{$locale}}[name]') ? 'has-error' : '']">
                                <label for="name" class="required">{{ __('price_drop::app.admin.email-template.name') }}
                                    
                                    <?php
                                        $channel_locale = [];
                                        array_push($channel_locale, $channel);
                                        array_push($channel_locale, $locale);
                                    ?>

                                    @if (count($channel_locale))
                                        <span class="locale">[{{ implode(' - ', $channel_locale) }}]</span>
                                    @endif
                                </label>
                                <input type="text" v-validate="'required|max:100'" class="control" id="name" name="{{$locale}}[name]" value="{{ old($locale)['name'] ?? ($email_template->translate($locale)['name'] ?? '') }}" data-vv-as="&quot;{{ __('price_drop::app.admin.email-template.name') }}&quot;" />
                                <span class="control-error" v-if="errors.has('{{$locale}}[name]')">@{{ errors.first('{!!$locale!!}[name]') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('{{$locale}}[subject]') ? 'has-error' : '']">
                                <label for="subject" class="required">{{ __('price_drop::app.admin.email-template.subject') }}
                                    @if (count($channel_locale))
                                        <span class="locale">[{{ implode(' - ', $channel_locale) }}]</span>
                                    @endif
                                </label>
                                <input type="text" v-validate="'required|max:100'" class="control" id="subject" name="{{$locale}}[subject]" value="{{ old($locale)['subject'] ?? ($email_template->translate($locale)['subject'] ?? '') }}" data-vv-as="&quot;{{ __('price_drop::app.admin.email-template.subject') }}&quot;" />
                                <span class="control-error" v-if="errors.has('{{$locale}}[subject]')">@{{ errors.first('{!!$locale!!}[subject]') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('{{$locale}}[message]') ? 'has-error' : '']">
                                <label for="message" class="required">{{ __('price_drop::app.admin.email-template.message') }}
                                    @if (count($channel_locale))
                                        <span class="locale">[{{ implode(' - ', $channel_locale) }}]</span>
                                    @endif
                                </label>
                                <textarea v-validate="'required|max:1000'" class="control" id="message" name="{{$locale}}[message]" data-vv-as="&quot;{{ __('price_drop::app.admin.email-template.message') }}&quot;">{{ old($locale)['message'] ?? ($email_template->translate($locale)['message'] ?? '') }}</textarea>
                                <span class="control-error" v-if="errors.has('{{$locale}}[message]')">@{{ errors.first('{!!$locale!!}[message]') }}</span>
                            </div>
                            

                            <div class="control-group" :class="[errors.has('status') ? 'has-error' : '']">
                                <?php $selectedOption = old('status') ?: $email_template['status'] ?>
                                
                                <label for="status" >{{ __('price_drop::app.admin.email-template.status') }}</label>
                                <label class="switch">
                                    <input type="checkbox" class="control" id="status" name="status" data-vv-as="&quot;{{ __('price_drop::app.admin.email-template.status') }}&quot;" {{ $selectedOption ? 'checked' : ''}} value="1">
                                    <span class="slider round"></span>
                                </label>
                            </div>                            

                            {!! view_render_event('bagisto.admin.settings.email_templates.edit_form_accordian.general.controls.after') !!}

                        </div>
                    </accordian>

                    {!! view_render_event('bagisto.admin.settings.email_templates.edit_form_accordian.general.after') !!}

                </div>
            </div>

        </form>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('vendor/webkul/admin/assets/js/tinyMCE/tinymce.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            tinymce.init({
                selector: 'textarea#message',
                height: 200,
                width: "100%",
                plugins: 'image imagetools media wordcount save fullscreen code',
                toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent  | removeformat | code',
                image_advtab: true
            });
        });
    </script>
@endpush