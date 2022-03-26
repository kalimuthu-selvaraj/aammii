@extends('mobikul::admin.layouts.content')

@section('page_title')
    {{ __('mobikul::app.mobikul.custom-collection.add-title') }}
@stop

@section('content')
    <div class="content">

        <form method="POST" action="{{ route('mobikul.custom-collection.store') }}" @submit.prevent="onSubmit" enctype="multipart/form-data">

            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ url('/admin/custom-collection') }}';"></i>

                        {{ __('mobikul::app.mobikul.custom-collection.add-title') }}
                    </h1>
                </div>

                <div class="page-action">
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('mobikul::app.mobikul.custom-collection.save-btn-title') }}
                    </button>
                </div>
            </div>

            <div class="page-content">
                <div class="form-container">
                    @csrf()

                    {!! view_render_event('bagisto.mobikul.custom-collection.create_form_accordian.general.before') !!}

                    <accordian :title="'{{ __('mobikul::app.mobikul.custom-collection.general') }}'" :active="true">
                        <div slot="body">

                            {!! view_render_event('bagisto.mobikul.custom-collection.create_form_accordian.general.controls.before') !!}

                            <div class="control-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label for="name" class="required">{{ __('mobikul::app.mobikul.custom-collection.name') }}</label>
                                <input type="text" v-validate="'required'" class="control" id="name" name="name" value="{{ old('name') }}" data-vv-as="&quot;{{ __('mobikul::app.mobikul.custom-collection.name') }}&quot;" />
                                <span class="control-error" v-if="errors.has('name')">@{{ errors.first('name') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('status') ? 'has-error' : '']">
                                <label for="status" class="required">{{ __('mobikul::app.mobikul.custom-collection.status') }}</label>
                                <select class="control" v-validate="'required'" id="status" name="status" data-vv-as="&quot;{{ __('mobikul::app.mobikul.custom-collection.status') }}&quot;">
                                    <option value=""></option>
                                    <option value="1">
                                        {{ __('mobikul::app.mobikul.custom-collection.enabled') }}
                                    </option>
                                    <option value="0">
                                        {{ __('mobikul::app.mobikul.custom-collection.disabled') }}
                                    </option>
                                </select>
                                <span class="control-error" v-if="errors.has('status')">@{{ errors.first('status') }}</span>
                            </div>

                            <product-collection></product-collection>

                            {!! view_render_event('bagisto.mobikul.custom-collection.create_form_accordian.general.controls.after') !!}

                        </div>
                    </accordian>

                    {!! view_render_event('bagisto.mobikul.custom-collection.create_form_accordian.general.after') !!}

                </div>
            </div>

        </form>
    </div>
@stop

@push('scripts')

<script type="text/x-template" id="product-collection-template">
    <div>
        <div class="control-group" :class="[errors.has('product_collection') ? 'has-error' : '']">
            <label for="product_collection" class="required">{{ __('mobikul::app.mobikul.custom-collection.choose-collection') }}</label>
            
            <select class="control" id="product_collection" v-validate="'required'" name="product_collection" v-model="collection_value" data-vv-as="&quot;{{ __('mobikul::app.mobikul.custom-collection.choose-collection') }}&quot;" >
            
                <option value="">
                    {{ __('mobikul::app.mobikul.custom-collection.select') }}
                </option>
                <option value="product_ids">
                    {{ __('mobikul::app.mobikul.custom-collection.product-names') }}
                </option>
                <option value="latest_product_count">
                    {{ __('mobikul::app.mobikul.custom-collection.latest-product-count') }}
                </option>
                <option value="product_attributes">
                    {{ __('mobikul::app.mobikul.custom-collection.product-attributes') }}
                </option>
            </select>
            
            <span class="control-error" v-if="errors.has('product_collection')">@{{ errors.first('product_collection') }}</span>
        </div>

        <div class="control-group" :class="[errors.has('product_ids') ? 'has-error' : '']" v-if="(collection_value == 'product_ids')">
            <label for="product_ids" class="required">
                {{ __('mobikul::app.mobikul.custom-collection.product-names-autocomplete') }}
            </label>

            <input type="text" class="control" autocomplete="off" v-model="search_term" placeholder="{{ __('admin::app.catalog.products.product-search-hint') }}" v-on:keyup="search">

            <div class="linked-product-search-result">
                <ul>
                    <li v-for='(product, index) in products' v-if='products.length' @click="addProduct(product)">
                        @{{ product.name }}
                    </li>

                    <li v-if='! products.length && search_term.length && ! is_searching'>
                        {{ __('admin::app.catalog.products.no-result-found') }}
                    </li>

                    <li v-if="is_searching && search_term.length">
                        {{ __('admin::app.catalog.products.searching') }}
                    </li>
                </ul>
            </div>

            <input type="hidden" name="product_ids[]" v-for='(product, index) in addedProducts' v-if="addedProducts.length" :value="product.id" v-validate="'required'" id="product_ids" data-vv-as="&quot;{{ __('mobikul::app.mobikul.custom-collection.product-ids-autocomplete') }}&quot;" />

            <span class="filter-tag" style="text-transform: capitalize; margin-top: 10px; margin-right: 0px; justify-content: flex-start" v-if="addedProducts.length">
                <span class="wrapper" style="margin-left: 0px; margin-right: 10px;" v-for='(product, index) in addedProducts'>
                    @{{ product.name }}
                <span class="icon cross-icon" @click="removeProduct(product)"></span>
                </span>
            </span>
            <span class="control-error" v-if="errors.has('product_ids')">@{{ errors.first('product_ids') }}</span>
        </div>
        
        <div class="control-group" :class="[errors.has('latest_count') ? 'has-error' : '']" v-if="(collection_value == 'latest_product_count')">
            <label for="latest_count" class="required">{{ __('mobikul::app.mobikul.custom-collection.latest-product-count') }}</label>
            <input type="text" v-validate="'required|numeric|min_value:1'" class="control" id="latest-count" name="latest_count" value="{{ old('latest_count') }}" data-vv-as="&quot;{{ __('mobikul::app.mobikul.custom-collection.latest-product-count') }}&quot;" placeholder="{{ __('mobikul::app.mobikul.custom-collection.placeholder-latest-count') }}" />
            <span class="control-error" v-if="errors.has('latest_count')">@{{ errors.first('latest_count') }}</span>
        </div>

        <div v-if="(collection_value == 'product_attributes')">
            <div class="control-group" :class="[errors.has('attributes') ? 'has-error' : '']">
                <label for="attributes" class="required">{{ __('mobikul::app.mobikul.custom-collection.product-attributes') }}</label>
                
                <select class="control" id="attributes" v-validate="'required'" name="attributes" v-model="attributes" data-vv-as="&quot;{{ __('mobikul::app.mobikul.custom-collection.product-attributes') }}&quot;" >
                    <option value="">
                        {{ __('mobikul::app.mobikul.custom-collection.select') }}
                    </option>
                    <option value="price">
                        {{ __('mobikul::app.mobikul.custom-collection.price') }}
                    </option>

                    <option value="brand">
                            {{ __('mobikul::app.mobikul.custom-collection.brand') }}
                    </option>

                    <option value="sku">
                            {{ __('mobikul::app.mobikul.custom-collection.sku') }}
                    </option>
                </select>
                
                <span class="control-error" v-if="errors.has('attributes')">@{{ errors.first('attributes') }}</span>
            </div>

            <div  v-if="(attributes == 'price')" style="display:inline-flex;width:100%;">
                <div class="control-group" :class="[errors.has('price_from') ? 'has-error' : '']">
                    <label for="price_from" class="required">{{ __('mobikul::app.mobikul.custom-collection.price-from') }}</label>
                    <input type="text" v-validate="'required|decimal:4|min_value:0.0001'" class="control" id="latest-count" name="price_from" value="{{ old('price_from') }}" data-vv-as="&quot;{{ __('mobikul::app.mobikul.custom-collection.price-from') }}&quot;" placeholder="{{ __('mobikul::app.mobikul.custom-collection.price-from') }}" />
                    <span class="control-error" v-if="errors.has('price_from')">@{{ errors.first('price_from') }}</span>
                </div>

                <div class="control-group" :class="[errors.has('price_to') ? 'has-error' : '']">
                    <label for="price_to" class="required">{{ __('mobikul::app.mobikul.custom-collection.price-to') }}</label>
                    <input type="text" v-validate="'required|decimal:4|min_value:0.0001'" class="control" id="latest-count" name="price_to" value="{{ old('price_to') }}" data-vv-as="&quot;{{ __('mobikul::app.mobikul.custom-collection.price-to') }}&quot;" placeholder="{{ __('mobikul::app.mobikul.custom-collection.price-to') }}" />
                    <span class="control-error" v-if="errors.has('price_to')">@{{ errors.first('price_to') }}</span>
                </div>
            </div>

            <div class="control-group" :class="[errors.has('brand') ? 'has-error' : '']" v-if="(attributes == 'brand')">
                <label for="brand" class="required">
                    {{ __('mobikul::app.mobikul.custom-collection.brand-autocomplete') }}
                </label>
    
                <input type="text" class="control" autocomplete="off" v-model="search_term" placeholder="{{ __('mobikul::app.mobikul.custom-collection.brand-search-hint') }}" v-on:keyup="searchBrand">
    
                <div class="linked-product-search-result">
                    <ul>
                        <li v-for='(brand_value, index) in brands' v-if='brands.length' @click="addBrand(brand_value)">
                            @{{ brand_value.name }}
                        </li>
    
                        <li v-if='! brands.length && search_term.length && ! is_searching'>
                            {{ __('mobikul::app.mobikul.custom-collection.no-brand-found') }}
                        </li>
    
                        <li v-if="is_searching && search_term.length">
                            {{ __('admin::app.catalog.products.searching') }}
                        </li>
                    </ul>
                </div>
                
                <input type="hidden" name="brand" v-if="addedBrand.id" :value="addedBrand.id" v-validate="'required'" id="brand" data-vv-as="&quot;{{ __('mobikul::app.mobikul.custom-collection.brand-autocomplete') }}&quot;" />
                
                <input type="hidden" name="brand" v-if="!addedBrand.id" value="" v-validate="'required'" id="brand" data-vv-as="&quot;{{ __('mobikul::app.mobikul.custom-collection.brand-autocomplete') }}&quot;" />
        
                <span class="filter-tag" style="text-transform: capitalize; margin-top: 10px; margin-right: 0px; justify-content: flex-start" v-if="addedBrand.id">
                    <span class="wrapper" style="margin-left: 0px; margin-right: 10px;">
                        @{{ addedBrand.name }}
                    <span class="icon cross-icon" @click="removeBrand(addedBrand)"></span>
                    </span>
                </span>
                <span class="control-error" v-if="errors.has('brand')">@{{ errors.first('brand') }}</span>
            </div>

            <div class="control-group" :class="[errors.has('sku') ? 'has-error' : '']" v-if="(attributes == 'sku')">
                <label for="sku" class="required">{{ __('mobikul::app.mobikul.custom-collection.product-sku') }}</label>
                <input type="text" v-validate="'required'" class="control" id="latest-count" name="sku" value="{{ old('sku') }}" data-vv-as="&quot;{{ __('mobikul::app.mobikul.custom-collection.product-sku') }}&quot;" placeholder="{{ __('mobikul::app.mobikul.custom-collection.placeholder-product-sku') }}" />
                <span class="control-error" v-if="errors.has('sku')">@{{ errors.first('sku') }}</span>
            </div>
        </div>

    </div>
</script>

<script>

    Vue.component('product-collection', {

        template: '#product-collection-template',

        inject: ['$validator'],

        data: function() {
            return {
                collection_value: '',

                attributes: '',
                
                products: [],

                brands: [],

                search_term: '',

                addedProducts: [],
                
                addedBrand: {},

                is_searching: false,

                productIds: @json([]),

                brand:  {},
            }
        },

        created: function () {

            if (this.productIds.length >= 1) {
                for (var index in this.productIds) {
                    this.addedProducts.push(this.productIds[index]);
                }
            }

            if ( this.brand.id ) {
                this.addedBrand = this.brand;
            }
        },

        methods: {
            addProduct: function (product) {
                this.addedProducts.push(product);
                this.search_term = '';
                this.products = [];
            },

            removeProduct: function (product) {
                for (var index in this.addedProducts) {
                    if (this.addedProducts[index].id == product.id ) {
                        this.addedProducts.splice(index, 1);
                    }
                }
            },

            search: function () {
                this_this = this;

                this.is_searching = true;

                if (this.search_term.length >= 1) {
                    this.$http.get ("{{ route('admin.catalog.products.productlinksearch') }}", {params: {query: this.search_term}})
                        .then (function(response) {

                            if (this_this.addedProducts.length) {
                                for (var product in this_this.addedProducts) {
                                    for (var productId in response.data) {
                                        if (response.data[productId].id == this_this.addedProducts[product].id) {
                                            response.data.splice(productId, 1);
                                        }
                                    }
                                }
                            }

                            this_this.products = response.data;

                            this_this.is_searching = false;
                        })

                        .catch (function (error) {
                            this_this.is_searching = false;
                        })
                } else {
                    this_this.products = [];
                    this_this.is_searching = false;
                }
            },
            
            addBrand: function (brand) {
                this.addedBrand = brand;
                this.search_term = '';
                this.brands = [];
            },

            removeBrand: function (brand) {
                this.addedBrand = {};
            },

            searchBrand: function () {
                this_this = this;

                this.is_searching = true;

                if (this.search_term.length >= 1) {
                    this.$http.get ("{{ route('mobikul.custom-collection.attributes.brandsearch') }}", {params: {query: this.search_term}})
                        .then (function(response) {

                            if ( this_this.addedBrand ) {
                                for (var brandId in response.data) {
                                    if (response.data[brandId].id == this_this.addedBrand.id) {
                                        response.data.splice(brandId, 1);
                                    }
                                }
                            }

                            this_this.brands = response.data;

                            this_this.is_searching = false;
                        })

                        .catch (function (error) {
                            this_this.is_searching = false;
                        })
                } else {
                    this_this.brands = [];
                    this_this.is_searching = false;
                }
            }
        }
    });

</script>

@endpush