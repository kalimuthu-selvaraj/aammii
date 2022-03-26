@extends('mobikul::admin.layouts.content')

@section('page_title')
    {{ __('mobikul::app.mobikul.carousel.edit') }}
@stop

@section('content')
    <div class="content">
        @php
            $locale = request()->get('locale') ?: app()->getLocale();
            $channel = request()->get('channel') ?: core()->getDefaultChannelCode();

            $channelLocales = app('Webkul\Core\Repositories\ChannelRepository')->findOneByField('code', $channel)->locales;

            if (! $channelLocales->contains('code', $locale)) {
                $locale = config('app.fallback_locale');
            }

            $carouselTranslation = $carousel->translations->where('channel', $channel)->where('locale', $locale)->first();
            
        @endphp
        <form method="POST" action="{{ route('mobikul.carousel.update') }}" @submit.prevent="onSubmit" enctype="multipart/form-data">

            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ url('/admin/featuredcategories') }}';"></i>
                        {{ __('mobikul::app.mobikul.carousel.edit') }}
                    </h1>

                    <div class="control-group">
                        <select class="control" id="channel-switcher" name="channel">
                            @foreach (core()->getAllChannels() as $channelModel)

                                <option
                                    value="{{ $channelModel->code }}" {{ ($channelModel->code) == $channel ? 'selected' : '' }}>
                                    {{ core()->getChannelName($channelModel) }}
                                </option>

                            @endforeach
                        </select>
                    </div>

                    <div class="control-group">
                        <select class="control" id="locale-switcher" name="locale">
                            @foreach ($channelLocales as $localeModel)

                                <option
                                    value="{{ $localeModel->code }}" {{ ($localeModel->code) == $locale ? 'selected' : '' }}>
                                    {{ $localeModel->name }}
                                </option>

                            @endforeach
                        </select>
                    </div>
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
                    <input name="_method" type="hidden" value="PUT">
                    <input type="hidden" value="{{ $carousel['id'] }}" name="carousel_id" />
                    
                    <div class="control-group" :class="[errors.has('title') ? 'has-error' : '']">
                        <label for="title" class="required">{{ __('mobikul::app.mobikul.carousel.title') }}</label>
                        
                        <input type="text" v-validate="'required'" class="control" id="title" name="title" value="{{ old('title') ?? ($carouselTranslation['title'] ?? '') }}" data-vv-as="&quot;{{ __('mobikul::app.mobikul.carousel.title') }}&quot;" v-slugify-target="'slug'"/>
                        <span class="control-error" v-if="errors.has('title')">@{{ errors.first('title') }}</span>
                    </div>

                    <div class="control-group" :class="[errors.has('type') ? 'has-error' : '']">
                        <label for="type" class="required">
                            {{ __('mobikul::app.mobikul.carousel.type') }}
                        </label>

                        <select class="control" name="type" v-validate="'required'" data-vv-as="&quot;{{ __('mobikul::app.mobikul.carousel.type') }}&quot;">
                            <option value=""></option>
                            <option value="image_type" {{ $carousel->type == 'image_type' ? 'selected' : '' }}>{{ __('mobikul::app.mobikul.carousel.image-type') }}</option>
                            <option value="product_type" {{ $carousel->type == 'product_type' ? 'selected' : '' }}>{{ __('mobikul::app.mobikul.carousel.product-type') }}</option>
                            <option value="featured" {{ $carousel->type == 'featured' ? 'selected' : '' }}>{{ __('mobikul::app.mobikul.carousel.featured') }}</option>
                            <option value="top_offered" {{ $carousel->type == 'top_offered' ? 'selected' : '' }}>{{ __('mobikul::app.mobikul.carousel.top-offered') }}</option>
                        </select>
                        <span class="control-error" v-if="errors.has('type')">@{{ errors.first('type') }}</span>
                    </div>

                    <div class="control-group" :class="[errors.has('image') ? 'has-error' : '']">
                        <label for="image" class="required">
                            {{ __('mobikul::app.mobikul.carousel.background-image') }}
                        </label>

                        <image-wrapper :button-label="'{{ __('mobikul::app.mobikul.carousel.background-image') }}'" input-name="image" :multiple="false" :images='"{{ url('storage/'.$carousel->image) }}"'></image-wrapper>

                        <span class="control-error" v-if="errors.has('image')">@{{ errors.first('image') }}</span>
                    </div>

                    <div class="control-group">
                        <label for="background_color" >
                            {{ __('mobikul::app.mobikul.carousel.background-color') }}
                        </label>
                        <input type="text" id="background_color" class="control" name="background_color" value="{{ $carousel['background_color'] ?? old('background_color') }}" />
                        <input type="color" style="display:none;" value="" name="color" id="color" style="width:10px;" />
                    </div>

                    <div class="control-group" :class="[errors.has('sort_order') ? 'has-error' : '']">
                        <label for="sort_order" class="required">
                            {{ __('mobikul::app.mobikul.banner-image.sort-order') }}
                        </label>

                        <input type="text" id="sort_order" class="control" name="sort_order" v-validate="'required'"  value="{{ $carousel['sort_order'] ?? old('sort_order') }}" data-vv-as="&quot;{{ __('mobikul::app.mobikul.banner-image.sort-order') }}&quot;" />

                        <span class="control-error" v-if="errors.has('sort_order')">@{{ errors.first('sort_order') }}</span>
                    </div>

                    <div class="control-group" :class="[errors.has('channels[]') ? 'has-error' : '']" >
                        <label for="reseller" class="required">
                            {{ __('mobikul::app.mobikul.notification.store-view') }}
                        </label>

                        <select  v-validate="'required'" id="channels" class="control" name="channels[]" multiple="multiple" data-vv-as="&quot;{{ __('mobikul::app.mobikul.notification.store-view') }}&quot;">
                            @foreach ($channels as $channelDetail)
                                <option value="{{ $channelDetail->code }}"
                                    @if ( in_array($channelDetail->code, $carousel->carouselChannelsArray())) selected @endif >
                                    {{ $channelDetail->name }}
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
                            <option value="1" {{ $carousel->status == 1 ? 'selected' : '' }}> {{ __('mobikul::app.mobikul.notification.status.enabled') }}</option>
                            <option value="0" {{ $carousel->status == 0 ? 'selected' : '' }}>{{ __('mobikul::app.mobikul.notification.status.disabled') }}</option>
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

    <script>
        $(document).ready(function () {
            $('#channel-switcher, #locale-switcher').on('change', function (e) {
                $('#channel-switcher').val()

                if (event.target.id == 'channel-switcher') {
                    let locale = "{{ app('Webkul\Core\Repositories\ChannelRepository')->findOneByField('code', $channel)->locales->first()->code }}";

                    $('#locale-switcher').val(locale);
                }

                var query = '?channel=' + $('#channel-switcher').val() + '&locale=' + $('#locale-switcher').val();

                window.location.href = "{{ route('mobikul.carousel.edit', $carousel->id)  }}" + query;
            })
        });
    </script>
@endpush