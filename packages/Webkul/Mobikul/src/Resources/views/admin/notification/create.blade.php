@extends('mobikul::admin.layouts.content')

@section('page_title')
    {{ __('mobikul::app.mobikul.notification.new-notification') }}
@stop

@section('content')
    <div class="content">
        <form method="POST" action="{{ route('mobikul.notification.store') }}" @submit.prevent="onSubmit" enctype="multipart/form-data">

            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ url('/admin/notification') }}';"></i>
                        {{ __('mobikul::app.mobikul.notification.new-notification') }}
                    </h1>
                </div>
                <div class="page-action">
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('mobikul::app.mobikul.notification.create-btn-title') }}
                    </button>
                </div>
            </div>

            <div class="page-content">

                <div class="form-container">
                    @csrf()

                    <div class="control-group" :class="[errors.has('title') ? 'has-error' : '']">
                        <label for="title" class="required">
                            {{ __('mobikul::app.mobikul.notification.notification-title') }}
                        </label>

                        <input type="text" class="control" name="title" v-validate="'required'" value="{{ old('title') }}" data-vv-as="&quot;{{ __('mobikul::app.mobikul.notification.notification-title') }}&quot;">

                        <span class="control-error" v-if="errors.has('title')">@{{ errors.first('title') }}</span>
                    </div>

                    <div class="control-group" :class="[errors.has('content') ? 'has-error' : '']">
                        <label for="content" class="required">
                            {{ __('mobikul::app.mobikul.notification.notification-content') }}
                        </label>
                        <textarea  class="control" name="content" v-validate="'required'" data-vv-as="&quot;{{ __('mobikul::app.mobikul.notification.notification-content') }}&quot;" cols="30" rows="10">{{ old('content') }}</textarea>
                        <span class="control-error" v-if="errors.has('content')">@{{ errors.first('content') }}</span>
                    </div>

                    <div class="control-group" :class="[errors.has('image') ? 'has-error' : '']">
                        <label for="image" class="required">
                            {{ __('mobikul::app.mobikul.notification.notification-image') }}
                        </label>
                        <image-wrapper :button-label="'{{ __('mobikul::app.mobikul.notification.notification-image') }}'" input-name="image" :multiple="false" ></image-wrapper>

                        <span class="control-error" v-if="errors.has('image')">@{{ errors.first('image') }}</span>
                    </div>

                    <option-wrapper></option-wrapper>

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
                        <label for="status">
                            {{ __('mobikul::app.mobikul.notification.notification-status') }}
                        </label>

                        <select class="control" name="status" data-vv-as="&quot;{{ __('mobikul::app.mobikul.notification.notification-status') }}&quot;">
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
    <script type="text/x-template" id="options-template">
        <div>
            <div class="control-group" :class="[errors.has('type') ? 'has-error' : '']">
                <label for="type" class="required">
                    {{ __('mobikul::app.mobikul.notification.notification-type') }}
                </label>

                <select class="control" id="type" name="type" v-validate="'required'" data-vv-as="&quot;{{ __('mobikul::app.mobikul.notification.notification-type') }}&quot;" @change="showHideOptions($event)" v-model="notificationType">

                    <option value="">{{ __('mobikul::app.mobikul.notification.notification-type-option.select') }}</option>
                    <option value="product">{{ __('mobikul::app.mobikul.notification.notification-type-option.product') }}</option>
                    <option value="category">{{ __('mobikul::app.mobikul.notification.notification-type-option.category') }}</option>
                    <option value="others">{{ __('mobikul::app.mobikul.notification.notification-type-option.others') }}</option>
                    <option value="custom_collection">{{ __('mobikul::app.mobikul.notification.notification-type-option.custom-collection') }}</option>
                </select>
                <span class="control-error" v-if="errors.has('type')">@{{ errors.first('type') }}</span>
            </div>

            <div class="control-group" :class="[errors.has('product_category_id') ? 'has-error' : '']" v-if="showProductCategory" id="product_category">
                <label for="product_category_id" class="required">
                    {{ __('mobikul::app.mobikul.notification.product-cat-id') }}
                </label>

                <input type="text" valid="" id="product_category_id" class="control" name="product_category_id" v-validate="showProductCategory ? 'required' : ''"  value="{{ old('product_category_id') }}" data-vv-as="&quot;{{ __('mobikul::app.mobikul.notification.product-cat-id') }}&quot;" @keyup="checkIdExistOrNot" v-model="productCategoryInputBox" placeholder="{{ __('mobikul::app.mobikul.notification.product-cat-id') }}" />

                <span class="control-error" v-if="errors.has('product_category_id')">@{{ errors.first('product_category_id') }}</span>

                <span class="control-error" v-show="! isValid">@{{ message }}</span>

            </div>

            <div class="control-group" :class="[errors.has('custom_collection') ? 'has-error' : '']" v-if="(notificationType == 'custom_collection')">
                    <label for="custom_collection" class="required">
                        {{ __('mobikul::app.mobikul.notification.collection-autocomplete') }}
                    </label>
        
                    <input type="text" class="control" autocomplete="off" v-model="search_term" placeholder="{{ __('mobikul::app.mobikul.notification.collection-search-hint') }}" v-on:keyup="searchCollection">
        
                    <div class="linked-product-search-result">
                        <ul>
                            <li v-for='(collection_val, index) in collections' v-if='collections.length' @click="addCollection(collection_val)">
                                @{{ collection_val.name }}
                            </li>
        
                            <li v-if='! collections.length && search_term.length && ! is_searching'>
                                {{ __('mobikul::app.mobikul.notification.no-collection-found') }}
                            </li>
        
                            <li v-if="is_searching && search_term.length">
                                {{ __('admin::app.catalog.products.searching') }}
                            </li>
                        </ul>
                    </div>
                    
                    <input type="hidden" name="custom_collection" v-if="addedCollection.id" :value="addedCollection.id" v-validate="'required'" id="custom_collection" data-vv-as="&quot;{{ __('mobikul::app.mobikul.notification.collection-autocomplete') }}&quot;" />
                    
                    <input type="hidden" name="custom_collection" v-if="!addedCollection.id" value="" v-validate="'required'" id="custom_collection" data-vv-as="&quot;{{ __('mobikul::app.mobikul.notification.collection-autocomplete') }}&quot;" />
            
                    <span class="filter-tag" style="text-transform: capitalize; margin-top: 10px; margin-right: 0px; justify-content: flex-start" v-if="addedCollection.id">
                        <span class="wrapper" style="margin-left: 0px; margin-right: 10px;">
                            @{{ addedCollection.name }}
                        <span class="icon cross-icon" @click="removeCollection(addedCollection)"></span>
                        </span>
                    </span>
                    <span class="control-error" v-if="errors.has('custom_collection')">@{{ errors.first('custom_collection') }}</span>
                </div>

        </div>
    </script>

    <script>

        Vue.component('option-wrapper', {

            template: '#options-template',

            inject: ['$validator'],

            data: function(data) {
                return {
                    showProductCategory: false,
                    valid: '',
                    notificationType : '',
                    productCategoryInputBox : '',
                    message: '',
                    isValid: false,
                    collections: [],
                    search_term: '',
                    addedCollection: {},
                    is_searching: false,
                    brand:  {},
                }
            },

            methods: {
                showHideOptions: function (event) {
                    this_this = this;
                    this_this.notificationType = event.target.value;

                    this_this.showProductCategory = false;
                    if (event.target.value == 'product' || event.target.value == 'category' ) {
                        this_this.showProductCategory = true;
                    }
                },

                //id exist or not
                checkIdExistOrNot(event) {
                    this_this = this;
                    var selectedType = this_this.notificationType;
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
                
                addCollection: function (collection) {
                    this.addedCollection = collection;
                    this.search_term = '';
                    this.collections = [];
                },

                removeCollection: function (collection) {
                    this.addedCollection = {};
                },

                searchCollection: function () {
                    this_this = this;

                    this.is_searching = true;

                    if (this.search_term.length >= 1) {
                        this.$http.get ("{{ route('mobikul.custom-collection.search') }}", {params: {query: this.search_term}})
                            .then (function(response) {

                                if ( this_this.addedCollection ) {
                                    for (var collectionId in response.data) {
                                        if (response.data[collectionId].id == this_this.addedCollection.id) {
                                            response.data.splice(collectionId, 1);
                                        }
                                    }
                                }

                                this_this.collections = response.data;

                                this_this.is_searching = false;
                            })

                            .catch (function (error) {
                                this_this.is_searching = false;
                            })
                    } else {
                        this_this.collections = [];
                        this_this.is_searching = false;
                    }
                }
            },
        });

    </script>
@endpush
