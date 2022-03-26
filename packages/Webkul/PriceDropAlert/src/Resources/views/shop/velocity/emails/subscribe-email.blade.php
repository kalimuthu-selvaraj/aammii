@component('shop::emails.layouts.master')

    <div>
        <div style="text-align: center;">
            <a href="{{ config('app.url') }}">
                @if ($logo = core()->getCurrentChannel()->logo_url)
                    <img src="{{ $logo }}" alt="{{ config('app.name') }}" style="height: 40px; width: 110px;"/>
                @else
                    <img src="{{ config('app.url') . 'themes/default/assets/images/logo.svg' }}">
                @endif
            </a>
        </div>

        <div>
            {!! $data['message'] !!}
        </div>

        @if ( isset($data['token']) )
            <div  style="margin-top: 40px; text-align: center">
                <a href="{{ route('shop.price-drop-alert.product.unsubscribe', $data['token']) }}" style="font-size: 16px;
                color: #FFFFFF; text-align: center; background: #0031F0; padding: 10px 100px;text-decoration: none;">
                    {!! __('shop::app.mail.customer.subscription.unsubscribe') !!}
                </a>
            </div>
        @endif
    </div>

@endcomponent