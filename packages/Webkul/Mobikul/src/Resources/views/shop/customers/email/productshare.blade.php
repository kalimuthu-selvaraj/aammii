@component('shop::emails.layouts.master')

    <div>
        <div style="text-align: center;">
            <a href="{{ config('app.url') }}">
                @include ('shop::emails.layouts.logo')
            </a>
        </div>

        <div  style="font-size:16px; color:#242424; font-weight:600; margin-top: 60px; margin-bottom: 15px">
            {!! __('mobikul-api::app.api.catalog.product-share.text-hello',['receiver_name' => $data['receiverName']]) !!}
        </div>

        <div>
            {!! __('mobikul-api::app.api.catalog.product-share.message-product-share',['sender_name' => $data['senderName']]) !!}
        </div>

        <div  style="margin-top: 40px; text-align: center">
            <a href="{{ route('shop.productOrCategory.index', $data['urlKey']) }}" style="font-size: 16px;
            color: #FFFFFF; text-align: center; background: #0031F0; padding: 10px 100px;text-decoration: none;">
                {!! $data['productName'] !!}
            </a>
        </div>
    </div>

@endcomponent