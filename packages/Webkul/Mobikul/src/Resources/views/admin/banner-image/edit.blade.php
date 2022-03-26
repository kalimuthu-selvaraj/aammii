@extends('mobikul::admin.layouts.content')

@section('page_title')
    {{ __('mobikul::app.mobikul.banner-image.edit-banner') }}
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

            $bannerImageTranslation = $bannerImage->translations->where('channel', $channel)->where('locale', $locale)->first();
            
        @endphp
        
        <form method="POST" action="" @submit.prevent="onSubmit" enctype="multipart/form-data">

            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ url('/admin/bannerimage') }}';"></i>
                        {{ __('mobikul::app.mobikul.banner-image.add-banner') }}
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
                        {{ __('mobikul::app.mobikul.banner-image.create-btn-title') }}
                    </button>
                </div>
            </div>

            <div class="page-content">

                <div class="form-container">
                    @csrf()
                    <input name="_method" type="hidden" value="PUT">
                    
                    <div class="control-group" :class="[errors.has('name') ? 'has-error' : '']">
                        <label for="name" class="required">{{ __('mobikul::app.mobikul.banner-image.banner-title') }}</label>
                        <input type="text" v-validate="'required'" class="control" id="name" name="name" value="{{ old($locale)['name'] ?? ($bannerImageTranslation['name'] ?? '') }}" data-vv-as="&quot;{{ __('mobikul::app.mobikul.banner-image.banner-title') }}&quot;" v-slugify-target="'slug'"/>
                        <span class="control-error" v-if="errors.has('name')">@{{ errors.first('name') }}</span>
                    </div>

                    <div class="control-group" :class="[errors.has('image') ? 'has-error' : '']">
                        <label for="image" class="required">
                            {{ __('mobikul::app.mobikul.banner-image.add-image') }}
                        </label>

                        <image-wrapper :button-label="'{{ __('mobikul::app.mobikul.banner-image.add-image') }}'" input-name="image" :multiple="false" :images='"{{ $bannerImage->image_url }}"'></image-wrapper>
                        
                        <span class="control-error" v-if="{!! $errors->has('image.*') !!}">
                            @foreach ($errors->get('image.*') as $key => $message)
                                @php echo str_replace($key, 'Image', $message[0]); @endphp
                            @endforeach
                        </span>
                    </div>

                    <div class="control-group" :class="[errors.has('sort_order') ? 'has-error' : '']">
                        <label for="sort_order" class="required">
                            {{ __('mobikul::app.mobikul.banner-image.sort-order') }}
                        </label>

                        <input type="text" valid="" id="sort_order" class="control" name="sort_order" v-validate="'required|numeric'"  value="{{ $bannerImage['sort_order'] ?? old('sort_order') }}" data-vv-as="&quot;{{ __('mobikul::app.mobikul.banner-image.sort-order') }}&quot;" />

                        <span class="control-error" v-if="errors.has('sort_order')">@{{ errors.first('sort_order') }}</span>
                    </div>

                    <option-wrapper></option-wrapper>

                    <div class="control-group" :class="[errors.has('channels[]') ? 'has-error' : '']" >
                        <label for="channels" class="required">
                            {{ __('mobikul::app.mobikul.notification.store-view') }}
                        </label>

                        <select  v-validate="'required'" id="channels" class="control" name="channels[]" multiple="multiple" data-vv-as="&quot;{{ __('mobikul::app.mobikul.notification.store-view') }}&quot;">
                            @foreach ($channels as $channelDetail)
                                <option value="{{ $channelDetail->code }}"
                                    @if ( in_array($channelDetail->code, $bannerImage->bannerImageChannelsArray())) selected @endif >
                                    {{ $channelDetail->name }}
                                </option>
                            @endforeach
                        </select>

                        <span class="control-error" v-if="errors.has('channels[]')">@{{ errors.first('channels[]') }}
                        </span>
                    </div>

                    <div class="control-group" :class="[errors.has('status') ? 'has-error' : '']">
                        <label for="status" class="required">
                            {{ __('mobikul::app.mobikul.banner-image.banner-status') }}
                        </label>

                        <select class="control" name="status" v-validate="'required'" data-vv-as="&quot;{{ __('mobikul::app.mobikul.banner-image.banner-status') }}&quot;">
                            <option value=""></option>
                            <option value="1" {{ $bannerImage->status == 1 ? 'selected' : '' }}>{{ __('mobikul::app.mobikul.banner-image.status.enabled') }}</option>
                            <option value="0" {{ $bannerImage->status == 0 ? 'selected' : '' }}>{{ __('mobikul::app.mobikul.banner-image.status.disabled') }}</option>
                        </select>
                        <span class="control-error" v-if="errors.has('status')">@{{ errors.first('status') }}</span>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop

@push('scripts')
    <script type="text/x-template" id="options-template">
        <div>
            <div class="control-group" :class="[errors.has('type') ? 'has-error' : '']">
                <label for="type" class="required">
                    {{ __('mobikul::app.mobikul.banner-image.banner-type') }}
                </label>

                <select class="control"  id="type" name="type" v-validate="'required'" data-vv-as="&quot;{{ __('mobikul::app.mobikul.banner-image.banner-type') }}&quot;" value="{{ old('type') ?? $bannerImage['type'] }}">

                    <option value=""></option>
                    <option value="product" {{ $bannerImage->type == 'product' ? 'selected' : '' }} >{{ __('mobikul::app.mobikul.banner-image.banner-image-type-option.product') }}</option>
                    <option value="category" {{ $bannerImage->type == 'category' ? 'selected' : '' }}>{{ __('mobikul::app.mobikul.banner-image.banner-image-type-option.category') }}</option>
                </select>
                <span class="control-error" v-if="errors.has('type')">@{{ errors.first('type') }}</span>
            </div>

            <div class="control-group" :class="[errors.has('product_category_id') ? 'has-error' : '']" id="product_category">
                <label for="product_category_id" class="required">
                    {{ __('mobikul::app.mobikul.banner-image.product-cat-id') }}
                </label>

                <input type="text" id="product_category_id" class="control" name="product_category_id" v-validate="'required'"  value="{{ old('product_category_id') ?? $bannerImage['product_category_id'] }}" data-vv-as="&quot;{{ __('mobikul::app.mobikul.banner-image.product-cat-id') }}&quot;" @keyup="checkIdExistOrNot" v-model="productCategoryInputBox" />

                <span class="control-error" v-if="errors.has('product_category_id')">@{{ errors.first('product_category_id') }}</span>

                <span class="control-error" v-show="! isValid">@{{ message }}</span>
            </div>
        </div>
    </script>

    <script>

        Vue.component('option-wrapper', {

            template: '#options-template',

            inject: ['$validator'],

            data: function(data) {
                return {
                    productCategoryInputBox : {{ $bannerImage['product_category_id'] }},
                    message: '',
                    isValid: false,
                    bannerImageType: '{{  $bannerImage['product_category_id'] }}',
                }
            },

            methods: {
                //id exist or not
                checkIdExistOrNot(event) {
                    this_this = this;
                    var selectedType = this_this.bannerImageType;
                    var givenValue = this_this.productCategoryInputBox;
                    var spaceCount = (givenValue.split(" ").length - 1);

                    if (spaceCount > 0) {
                        this_this.isValid = true;
                        return false;
                    }

                    this_this.$http.post("{{ route('mobikul.notification.cat-product-id') }}",{givenValue:givenValue, selectedType:selectedType})

                    .then(response => {
                        if(response.data.value) {
                            $('#product_category').removeClass('has-error');
                            this_this.isValid = response.data.value;
                            this_this.message = response.data.message;
                        } else {
                            $('#product_category').addClass('has-error');
                            this_this.message = response.data.message;
                            this_this.isValid = response.data.value;
                        }
                    }).catch(function (error) {
                        currentObj.output = error;
                    });
                },
            },
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

                window.location.href = "{{ route('mobikul.banner-image.edit', $bannerImage->id)  }}" + query;
            })
        });
    </script>
@endpush
