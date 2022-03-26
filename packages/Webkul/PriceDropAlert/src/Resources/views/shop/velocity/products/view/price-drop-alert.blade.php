@if ( core()->getConfigData('price_drop_alert.general.general.status') )
    @php
        $route = request()->route() ? request()->route()->getName() : "";
        
        if ($route != 'shop.productOrCategory.index') {
            return false;
        }
    
        $isPriceDropAlertProduct = priceDrop()->isPriceDropAlertProduct($product);
    @endphp

    @if ( $isPriceDropAlertProduct )

        {!! view_render_event('bagisto.price_drop_alert.shop.products.view.price_drop_alert.before', ['product' => $product]) !!}

        @push('css')
            <style type="text/css">
                .subscription-email-section {
                    display: inline-flex;
                    font-size: 14px;
                }
                .subscription-email-section input{
                    width: 200px;
                    float: left;
                }
                .subscription-email-section .btn-subscription {
                    padding: 0px;
                }
                .btn-subscription:focus, .btn-subscription:active:focus {
                    outline: unset;
                    box-shadow: unset;
                }
                .btn-subscription > span {
                    font-size: 50px;
                    line-height: 0.7;
                }
                .control-error {
                    display: block;
                }
            </style>
        @endpush

        <form method="POST" action="{{ route('shop.price-drop-alert.product.subscription') }}" enctype="multipart/form-data" @submit.prevent="onSubmit">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->product_id }}">
        
            <div class="subscription-email-section">

                <div class="control-group" :class="[errors.has('email') ? 'has-error' : '']">
                    <label for="email" class="mandatory hide">{{ __('price_drop::app.shop.price-drop-alert.subscription-email') }}</label>
                    
                    <input type="email" v-validate="'required|email'" class="form-style" id="email" name="email" value="" data-vv-as="&quot;{{ __('price_drop::app.shop.price-drop-alert.subscription-email') }}&quot;" placeholder="{{ __('price_drop::app.shop.price-drop-alert.placeholder-subscription-email') }}"/>
                    
                    <button type="submit" class="btn btn-subscription">
                        <span class="material-icons">email</span>
                    </button>

                    <span class="control-error" v-if="errors.has('email')">@{{ errors.first('email') }}</span>
                </div>
                
            </div>
        </form>

        {!! view_render_event('bagisto.price_drop_alert.shop.products.view.price_drop_alert.after', ['product' => $product]) !!}
    @endif
@endif