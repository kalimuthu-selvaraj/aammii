<?php

return [
    [
        'key'  => 'mobikul.price_drop_alert',
        'name' => 'price_drop::app.admin.system.price-drop-alert',
        'sort' => 2,
    ], [
        'key'    => 'mobikul.price_drop_alert.general',
        'name'   => 'price_drop::app.admin.system.general',
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'status',
                'title'         => 'price_drop::app.admin.system.status',
                'type'          => 'boolean',
                'channel_based' => true,
            ],
        ],
    ], [
        'key'    => 'mobikul.price_drop_alert.email_setting',
        'name'   => 'price_drop::app.admin.system.email-template',
        'sort'   => 2,
        'fields' => [
            [
                'name'          => 'notification_email',
                'title'         => 'price_drop::app.admin.system.notification-email',
                'type'          => 'select',
                'validation'    => 'required',
                'repository'    => 'Webkul\PriceDropAlert\PriceDropAlert@getEmailTemplates',
                'channel_based' => true,
                'locale_based'  => true,
            ],  [
                'name'          => 'subscription_email',
                'title'         => 'price_drop::app.admin.system.subscription-email',
                'type'          => 'select',
                'validation'    => 'required',
                'repository'    => 'Webkul\PriceDropAlert\PriceDropAlert@getEmailTemplates',
                'channel_based' => true,
                'locale_based'  => true,
            ],  [
                'name'          => 'unsubscription_email',
                'title'         => 'price_drop::app.admin.system.unsubscription-email',
                'type'          => 'select',
                'validation'    => 'required',
                'repository'    => 'Webkul\PriceDropAlert\PriceDropAlert@getEmailTemplates',
                'channel_based' => true,
                'locale_based'  => true,
            ],
        ],
    ]
];