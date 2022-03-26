@extends('mobikul::admin.layouts.content')

@section('page_title')
    {{ __('mobikul::app.mobikul.carousel.new-carousel') }}
@stop

@section('content')
    <style>
        table:nth-last-child(th) {
            background: red;
          }
    </style>
    <div class="content">
        <form method="POST" action="{{ route('mobikul.carousel.store') }}" @submit.prevent="onSubmit" enctype="multipart/form-data">

            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ url('/admin/featuredcategories') }}';"></i>
                        {{ __('mobikul::app.mobikul.carousel.new-carousel') }}
                    </h1>
                </div>
                <div class="page-action">
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('mobikul::app.mobikul.carousel.save') }}
                    </button>
                </div>
            </div>

            <div class="page-content">

                <div class="form-container">
                    @csrf()
                    <div class="control-group" :class="[errors.has('title') ? 'has-error' : '']">
                        <label for="title" class="required">
                            {{ __('mobikul::app.mobikul.carousel.title') }}
                        </label>

                        <input type="text" id="title" class="control" name="title" v-validate="'required'"  value="{{ old('title') }}" data-vv-as="&quot;{{ __('mobikul::app.mobikul.carousel.title') }}&quot;" />

                        <span class="control-error" v-if="errors.has('title')">@{{ errors.first('title') }}</span>
                    </div>

                    <div class="control-group" :class="[errors.has('type') ? 'has-error' : '']">
                        <label for="type" class="required">
                            {{ __('mobikul::app.mobikul.carousel.type') }}
                        </label>

                        <select class="control" name="type" v-validate="'required'" data-vv-as="&quot;{{ __('mobikul::app.mobikul.carousel.type') }}&quot;">
                            <option value=""></option>
                            <option value="image_type">{{ __('mobikul::app.mobikul.carousel.image-type') }}</option>
                            <option value="product_type">{{ __('mobikul::app.mobikul.carousel.product-type') }}</option>
                            <option value="featured">{{ __('mobikul::app.mobikul.carousel.featured') }}</option>
                            <option value="top_offered">{{ __('mobikul::app.mobikul.carousel.top-offered') }}</option>
                        </select>
                        <span class="control-error" v-if="errors.has('type')">@{{ errors.first('type') }}</span>
                    </div>

                    <div class="control-group" :class="[errors.has('image') ? 'has-error' : '']">
                        <label for="image">
                            {{ __('mobikul::app.mobikul.carousel.background-image') }}
                        </label>
                        <image-wrapper :button-label="'{{ __('mobikul::app.mobikul.carousel.background-image') }}'" input-name="image" :multiple="false" ></image-wrapper>

                        <span class="control-error" v-if="errors.has('image')">@{{ errors.first('image') }}</span>
                    </div>

                    <div class="control-group">
                        <label for="background_color">{{ __('mobikul::app.mobikul.carousel.background-color') }}</label>
                        <input type="text" id="background_color" class="control" name="background_color" value="{{ old('background_color') }}" />
                        <input type="color" style="display:none;" value="" name="color" id="color" style="width:10px;" />
                    </div>

                    <div class="control-group" :class="[errors.has('sort_order') ? 'has-error' : '']">
                        <label for="sort_order" class="required">
                            {{ __('mobikul::app.mobikul.banner-image.sort-order') }}
                        </label>

                        <input type="text" id="sort_order" class="control" name="sort_order" v-validate="'required'"  value="{{ old('sort_order') }}" data-vv-as="&quot;{{ __('mobikul::app.mobikul.banner-image.sort-order') }}&quot;" />

                        <span class="control-error" v-if="errors.has('sort_order')">@{{ errors.first('sort_order') }}</span>
                    </div>

                    <div class="control-group" :class="[errors.has('channels[]') ? 'has-error' : '']" >
                        <label for="reseller" class="required">
                            {{ __('mobikul::app.mobikul.notification.store-view') }}
                        </label>

                        <select  v-validate="'required'" id="channels" class="control" name="channels[]" multiple="multiple" data-vv-as="&quot;{{ __('mobikul::app.mobikul.notification.store-view') }}&quot;">
                            @foreach ($channels as $channel)
                                <option value="{{ $channel->code }}">
                                    {{ $channel->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="control-error" v-if="errors.has('channels[]')">@{{ errors.first('channels[]') }}
                        </span>
                    </div>

                    <div class="control-group" :class="[errors.has('status') ? 'has-error' : '']">
                        <label for="status" class="required">
                            {{ __('mobikul::app.mobikul.carousel.status') }}
                        </label>

                        <select class="control" name="status" v-validate="'required'" data-vv-as="&quot;{{ __('mobikul::app.mobikul.carousel.status') }}&quot;">
                            <option value=""></option>
                            <option value="1">{{ __('mobikul::app.mobikul.notification.status.enabled') }}</option>
                            <option value="0">{{ __('mobikul::app.mobikul.notification.status.disabled') }}</option>
                        </select>
                        <span class="control-error" v-if="errors.has('status')">@{{ errors.first('status') }}</span>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#background_color").click(function() {
                $("#color").show();
                var colorInput = document.getElementById("color");
                var theColor = colorInput.value;
                colorInput.addEventListener("input", function() {

                $("#background_color").val(colorInput.value);
                }, false);
              });
        });
    </script>
@endpush