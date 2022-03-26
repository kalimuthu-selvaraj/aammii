@extends('admin::layouts.content')

@section('page_title')
    {{ __('price_drop::app.admin.email-template.add-title') }}
@stop

@section('content')
    <div class="content">

        <form method="POST" action="{{ route('admin.price-alert.email-template.create') }}" @submit.prevent="onSubmit" enctype="multipart/form-data">

            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ url('/admin/dashboard') }}';"></i>

                        {{ __('price_drop::app.admin.email-template.add-title') }}
                    </h1>
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
                    <input type="hidden" name="locale" value="all"/>

                    {!! view_render_event('bagisto.admin.settings.email_templates.create_form_accordian.general.before') !!}

                    <accordian :title="'{{ __('price_drop::app.admin.email-template.general') }}'" :active="true">
                        <div slot="body">

                            {!! view_render_event('bagisto.admin.settings.email_templates.create_form_accordian.general.controls.before') !!}

                            <div class="control-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label for="name" class="required">{{ __('price_drop::app.admin.email-template.name') }}</label>
                                <input type="text" v-validate="'required|max:100'" class="control" id="name" name="name" value="{{ old('name') }}" data-vv-as="&quot;{{ __('price_drop::app.admin.email-template.name') }}&quot;" v-slugify-target="'slug'"/>
                                <span class="control-error" v-if="errors.has('name')">@{{ errors.first('name') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('subject') ? 'has-error' : '']">
                                <label for="subject" class="required">{{ __('price_drop::app.admin.email-template.subject') }}</label>
                                <input type="text" v-validate="'required|max:100'" class="control" id="subject" name="subject" value="{{ old('subject') }}" data-vv-as="&quot;{{ __('price_drop::app.admin.email-template.subject') }}&quot;" v-slugify-target="'slug'"/>
                                <span class="control-error" v-if="errors.has('subject')">@{{ errors.first('subject') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('message') ? 'has-error' : '']">
                                <label for="message" class="required">{{ __('price_drop::app.admin.email-template.message') }}</label>
                                <textarea v-validate="'required|max:1000'" class="control" id="message" name="message" data-vv-as="&quot;{{ __('price_drop::app.admin.email-template.message') }}&quot;">{{ old('message') }}</textarea>
                                <span class="control-error" v-if="errors.has('message')">@{{ errors.first('message') }}</span>
                            </div>
                            

                            <div class="control-group" :class="[errors.has('status') ? 'has-error' : '']">
                                <?php $selectedOption = old('status') ?: '' ?>
                                
                                <label for="status" >{{ __('price_drop::app.admin.email-template.status') }}</label>
                                <label class="switch">
                                    <input type="checkbox" class="control" id="status" name="status" data-vv-as="&quot;{{ __('price_drop::app.admin.email-template.status') }}&quot;" {{ $selectedOption ? 'checked' : ''}} value="1">
                                    <span class="slider round"></span>
                                </label>
                            </div>                            

                            {!! view_render_event('bagisto.admin.settings.email_templates.create_form_accordian.general.controls.after') !!}

                        </div>
                    </accordian>

                    {!! view_render_event('bagisto.admin.settings.email_templates.create_form_accordian.general.after') !!}

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