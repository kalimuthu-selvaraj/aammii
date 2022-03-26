@component('shop::emails.layouts.master')

<div style="text-align: center;">
    <a href="{{ config('app.url') }}">
        <img src="{{ bagisto_asset('images/logo.svg') }}">
    </a>
</div>

<div style="padding: 30px;">
    <div style="font-size: 20px;color: #242424;line-height: 30px;margin-bottom: 34px;">
        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            {!! __('mobikul-api::app.api.sales.share-wishlist.message-wishlist-share-1', ['sender_name' => $recipientData['customerName']]) !!}
        </p>
    </div>
    <div style="width: 100%;overflow-x: auto;">
        <table style="width: 100%;border-collapse: collapse;text-align: left; margin-top: 10px;">
            <thead>
                <tr>
                    <th style="font-weight: 700;padding: 12px 10px;background: #f8f9fa;color: #3a3a3a;">
                        {!! __('mobikul-api::app.api.sales.share-wishlist.message-wishlist-share-2', [
                            'sender_name'   => $recipientData['customerName'],
                            'store_name'    => $recipientData['storeName']
                            ]) !!}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($recipientData['productData'] as $key => $productFlat)
                    <tr>
                        <td style="padding: 10px;border-bottom: solid 1px #d3d3d3;color: #3a3a3a;vertical-align: top;">
                            {!! '<a href="' . route('shop.productOrCategory.index', $productFlat['url_key']) . '">' . $productFlat['name'] . '</a>' !!}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div
        style="margin-top: 40px;font-size: 16px;color: #5E5E5E;line-height: 24px;display: block; width: 100%; float: left">

        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            {!! __('shop::app.mail.order.help', [
                'support_email' => '<a style="color:#0041FF" href="mailto:' . config('mail.from.address') . '">' . config('mail.from.address'). '</a>'
                ])
            !!}
        </p>

        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            {!! __('mobikul-api::app.api.sales.share-wishlist.message-wishlist-share-3') !!}
        </p>
    </div>

</div>

@endcomponent
