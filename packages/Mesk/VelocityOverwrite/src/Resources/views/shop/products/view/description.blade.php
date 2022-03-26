{!! view_render_event('bagisto.shop.products.view.description.before', ['product' => $product]) !!}

    <accordian :title="'{{ __('shop::app.products.description') }}'" :active="true">
        <div slot="header">
            <h3 class="no-margin display-inbl">
                {{ __('velocity::app.products.details') }}
            </h3>

            <i class="rango-arrow"></i>
        </div>

        <div slot="body">
            <div class="full-description mt-3">
                {!! $product->description !!}
            </div>
        </div>
    </accordian>

{!! view_render_event('bagisto.shop.products.view.description.after', ['product' => $product]) !!}