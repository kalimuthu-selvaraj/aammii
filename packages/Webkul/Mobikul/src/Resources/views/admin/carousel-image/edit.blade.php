@extends('mobikul::admin.layouts.content')

@section('page_title')
    {{ __('mobikul::app.mobikul.carousel.edit-carousel-image') }}
@stop

@section('content')
    <div class="content">
        <form method="POST" action="{{ route('mobikul.carousel.image.update') }}" @submit.prevent="onSubmit" enctype="multipart/form-data">

            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ url('/admin/image-carousel') }}';"></i>
                        {{ __('mobikul::app.mobikul.carousel.edit-carousel-image') }}
                    </h1>
                </div>
                <div class="page-action">
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('mobikul::app.mobikul.carousel.save-image') }}
                    </button>
                </div>
            </div>

            <div class="page-content">

                <div class="form-container">
                    @csrf()

                    <input type="hidden" name="id" value="{{ $data->id }}" />

                    <div class="control-group" :class="[errors.has('image') ? 'has-error' : '']">
                        <label for="image" class="required">
                            {{ __('mobikul::app.mobikul.carousel.images') }}
                        </label>

                        <image-wrapper :button-label="'{{ __('mobikul::app.mobikul.carousel.images') }}'" input-name="image" :multiple="false" :images='"{{ url('storage/'.$data->image) }}"'></image-wrapper>

                        <span class="control-error" v-if="errors.has('image')">@{{ errors.first('image') }}</span>
                    </div>

                    <div class="control-group" :class="[errors.has('title') ? 'has-error' : '']">
                        <label for="title" class="required">
                            {{ __('mobikul::app.mobikul.carousel.title') }}
                        </label>

                        <input type="text" id="title" class="control" name="title" v-validate="'required'"  value="{{ $data['title'] ?? old('title') }}" data-vv-as="&quot;{{ __('mobikul::app.mobikul.carousel.title') }}&quot;" />

                        <span class="control-error" v-if="errors.has('title')">@{{ errors.first('title') }}</span>
                    </div>
                    <option-wrapper></option-wrapper>

                    <div class="control-group" :class="[errors.has('status') ? 'has-error' : '']">
                        <label for="status" class="required">
                            {{ __('mobikul::app.mobikul.carousel.status') }}
                        </label>

                        <select class="control" name="status" v-validate="'required'" data-vv-as="&quot;{{ __('mobikul::app.mobikul.carousel.status') }}&quot;">
                            <option value=""></option>
                            <option value="1" {{ $data->status == 1 ? 'selected' : '' }} >{{ __('mobikul::app.mobikul.notification.status.enabled') }}</option>
                            <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>{{ __('mobikul::app.mobikul.notification.status.disabled') }}</option>
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
                    {{ __('mobikul::app.mobikul.carousel.carousel-image-type') }}
                </label>

                <select class="control" id="type" name="type" v-validate="'required'" data-vv-as="&quot;{{ __('mobikul::app.mobikul.carousel.carousel-image-type') }}&quot;" @change="showHideOptions($event)" v-model="carouselImageType">

                    <option value=""></option>
                    <option value="product">{{ __('mobikul::app.mobikul.notification.notification-type-option.product') }}</option>
                    <option value="category" >{{ __('mobikul::app.mobikul.notification.notification-type-option.category') }}</option>
                </select>
                <span class="control-error" v-if="errors.has('type')">@{{ errors.first('type') }}</span>
            </div>

            <div class="control-group" :class="[errors.has('product_category_id') ? 'has-error' : '']" v-show="showProductCategory" id="product_category">
                <label for="product_category_id" class="required">
                    {{ __('mobikul::app.mobikul.notification.product-cat-id') }}
                </label>

                <input type="text" valid="" id="product_category_id" class="control" name="product_category_id" v-validate="showProductCategory ? 'required' : ''"  value="{{ old('product_category_id') }}" data-vv-as="&quot;{{ __('mobikul::app.mobikul.notification.product-cat-id') }}&quot;" @keyup="checkIdExistOrNot" v-model="productCategoryInputBox" />

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
                    showProductCategory: true,
                    valid: '',
                    carouselImageType : '{{$data['type']}}',
                    productCategoryInputBox : '{{ $data['product_category_id'] }}',
                    message: '',
                    isValid: false,
                }
            },

            methods: {
                showHideOptions: function (event) {
                    this_this = this;
                    this_this.carouselImageType =  event.target.value;

                    if (event.target.value == 'custom_collection') {
                        this_this.showProductCategory = false;
                    } else {
                        this_this.showProductCategory = true;
                    }
                },

                //id exist or not
                checkIdExistOrNot(event) {
                    this_this = this;
                    var selectedType = this_this.carouselImageType;
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
    @endpush