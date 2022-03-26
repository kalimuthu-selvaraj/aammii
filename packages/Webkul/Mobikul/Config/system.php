<?php
    return [
        [
            'key'       => 'mobikul',
            'name'      => 'Mobikul',
            'sort'      => 6,
        ], [
            'key'       => 'mobikul.mobikul',
            'name'      => 'Mobikul',
            'sort'      => 1,
        ], [
            'key'       => 'mobikul.mobikul.basicinformation',
            'name'      => 'Mobikul Basic Configuration',
            'sort'      => 1,
            'fields'    => [
                [
                    'name'          => 'username',
                    'title'         => 'Username',
                    'type'          => 'text',
                    'validation'    => 'required',
                    'channel_based' => true
                ], [
                    'name'          => 'password',
                    'title'         => 'Password',
                    'type'          => 'password',
                    'validation'    => 'required',
                    'channel_based' => true
                ], [
                    'name'          => 'current_page_size',
                    'title'         => 'Collection Page Size',
                    'type'          => 'text',
                    'validation'    => 'required',
                    'channel_based' => true
                ], [
                    'name'          => 'enable_random_feature',
                    'title'         => 'Enable random featured product in home page?',
                    'type'          => 'text',
                    'type'          => 'select',
                    'options'       => [
                        [
                            'title'     => 'Yes',
                            'value'     => 1
                        ], [
                            'title'     => 'No',
                            'value'     => 0
                        ]
                    ],
                    'channel_based' => true,
                    'locale_based'  => false,
                ], [
                    'name'          => 'allowed_cms_pages',
                    'title'         => 'Allowed CMS Pages',
                    'type'          => 'multiselect',
                    'validation'    => 'required',
                    'channel_based' => true,
                    'locale_based'  => false,
                    'repository'    => 'Webkul\Mobikul\Repositories\CarouselImagesRepository@getCMSPages'
                  ],
            ]
        ], [
            'key'       => 'mobikul.mobikul.pushnotification',
            'name'      => 'FCM Push Notification Parameters',
            'sort'      => 2,
            'fields'    => [
                [
                    'name'          => 'apikey',
                    'title'         => 'ApI Key',
                    'type'          => 'text',
                    'validation'    => '',
                    'channel_based' => true
                ], [
                    'name'          => 'android_topic',
                    'title'         => 'Android Topic',
                    'type'          => 'text',
                    'validation'    => '',
                    'channel_based' => true
                ], [
                    'name'          => 'ios_topic',
                    'title'         => 'iOS Topic',
                    'type'          => 'text',
                    'validation'    => '',
                    'channel_based' => true
                ],
            ]
        ], [
            'key'       => 'mobikul.mobikul.razorpay_mobile',
            'name'      => 'Mobikul Razor Pay Credentials',
            'sort'      => 2,
            'fields'    => [
                [
                    'name'          => 'title',
                    'title'         => 'Title',
                    'type'          => 'depends',
                    'depend'        => 'status:1',
                    'validation'    => 'required_if:status,1',
                    'channel_based' => true,
                ], [
                    'name'          => 'description',
                    'title'         => 'Description',
                    'type'          => 'depends',
                    'depend'        => 'status:1',
                    'validation'    => 'required_if:status,1',
                    'channel_based' => true,
                ], [
                    'name'          => 'merchant_id',
                    'title'         => 'Merchant Id',
                    'type'          => 'depends',
                    'depend'        => 'status:1',
                    'validation'    => 'required_if:status,1',
                    'channel_based' => true,
                ], [
                    'name'          => 'merchant_secret',
                    'title'         => 'Merchant Secret',
                    'type'          => 'depends',
                    'depend'        => 'status:1',
                    'validation'    => 'required_if:status,1',
                    'channel_based' => true
                ], [
                    'name'          => 'status',
                    'title'         => 'admin::app.admin.system.status',
                    'type'          => 'boolean',
                    'validation'    => 'required',
                    'channel_based' => true,
                ],
            ]
        ],
];