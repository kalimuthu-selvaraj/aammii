<?php

return [
    'api'   => [
        'auth'      => [
            'invalid-auth'      => 'Warning: You are not authorized to use Mobikul APIs.',
            'required-token'    => 'Warning: token parameter is required.',
            'invalid-store'     => 'Warning: You are requesting to an invalid store.',
        ],

        'customer'  => [
            'get-customer'  => [
                'success-exists'    => 'Success: The customer is exists with this :customer_email email address.',
                'error-exists'      => 'Warning: no customer found with this :customer_email email address',
            ],

            'save-info'  => [
                'success-save'  => 'Success: Customer account information saved successfully.',
                'error-save'    => 'Warning: Failed to save details, try again.',
            ],

            'forgot-password'   => [
                'success-sent-email'    => 'Success: If there is an account associated with you, will receive an email with a link to reset your password.',
            ],

            'login'     => [
                'success-login'             => 'Success: Logged in successfully.',
                'error-parameters'          => 'Warning: Invalid parameters passed.',
                'error-username-password'   => 'Warning: Invalid username and password, try again.',
                'error-app-auth'            => 'Warning: Missing or invalid parameter apiKey / apiPassword',
                'error-create-account'      => 'Warning: Customer not created, try again.',
            ],

            'address-info' => [
                'error-invalid-token'   => 'Warning: Invalid Token.',
                'error-expire-token'    => 'Warning: Token has been expired.',
                'error-login'           => 'Warning: Customer is not login.',
            ],

            'address-form-data' => [
                'not-found' => 'Warning: Address not found.',
            ],

            'save-address'  => [
                'success-saved'     => 'Success: Customer address has been saved.',
                'success-updated'   => 'Success: Customer address updated successfully.',
            ],

            'order-list'    => [
                'success-order-list'    => 'Success: Customer order list fetched successfully.',
                'error-response'        => 'Warning: :response.',
                'order-not-found'       => 'Warning: Increament Id not found in record.',
                'success-reorder'       => 'Success: Product added to cart successfully.',
            ],

            'invoice-view' => [
                'error-invalid-invoice' => 'Warning: Invoice id not found in record.',
            ],

            'shipment-view' => [
                'error-invalid-shipment'    => 'Warning: Shipment id not found in record.',
            ],

            'review'    => [
                'success-save-review'   => 'Success: Your review has been accepted for moderation.',
                'error-rating'          => 'Warning: Rating value should be less than or equal to 5.',
                'error-no-product'      => 'Warning: There is no product exist in the record.',
                'error-no-review'       => 'Warning: There is no review found.',
                'error-review-create'   => 'Warning: There are some issue in posting review, try again.',
                'error-guest-review'    => 'Warning: Guest user are not allow to post review.',
                'review-date'           => 'Your Review (submitted on :review_date).',
            ],

            'wishlist'  => [
                'empty-wishlist'        => 'Warning: There is no product found in wishlist.',
                'success-wishlist'      => 'Success: Customer\'s wishlist fetch successfully..',
            ],

            'download'  => [
                'success-download'      => 'Success: Download successfully.',
                'error-download-auth'   => 'Warning: you are not authorized to download link.',
            ],

            'wishlist-to-cart'  => [
                'success-moved'     => 'Success: Wishlist product moved to cart successfully.',
                'invalid-params'    => 'Warning: No wishlist product found with the provided item id.',
            ],

            'remove-from-wishlist'  => [
                'success-removed'   => 'Success: Item deleted from wishlist successfully.',
                'no-item-found'     => 'Warning: No wishlist item found.',
            ]
        ],

        'checkout'  => [
            'create-account'    => [
                'error-order-not-found' => 'Warning: no such order found.',
            ],

            'review-and-payment'    => [
                'success-payment'       => 'Success: Cart and Payment data fetched successfully.',
                'error-empty-cart'      => 'Warning: No item found in the cart.',
                'error-save-shipping'   => 'Warning: There are some error in saving selected shipping method.',
            ],

            'place-razorpay-order'   => [
                'error-payment-method'    => 'Warning: Selected payment must be razorpay_mobile.',
                'error-empty-cart'     => 'Warning: No item found in the cart.',
                'went-wrong'           => 'Something went wrong while creating razorpay order.'
            ],

            'place-order'   => [
                'error-save-payment'    => 'Warning: There are some error in saving selected payment method.',
                'error-create-order'    => 'Warning: There are some error in creating order.',
            ],

            'wishlist-from-cart'    => [
                'success-move-to-wishlist'  => 'Success: Item has been moved to Wishlist successfully.',
                'error-invalid-item-id'     => 'Warning: Invalid item id.',
            ],

            'remove-cart-item'  => [
                'success-cart-empty'        => 'Suceess: Cart is empty now.',
                'success-remove-cart-item'  => 'Success: Cart item has been removed.',
                'error-cart-empty'          => 'Warning: There are some error, try again.',
            ],

            'add-to-cart'   => [
                'success-add-to-cart'   => 'Success: Product :product_name added to cart successfully.',
                'error-invalid-product' => 'Warning: Invalid product id provided.',
            ],

            'update-cart'   => [
                'success-update-cart'   => 'Success: Cart updated sucessfully.',
            ],

            'change-order-status'   => [
                'error-order-not-found'     => 'Warning: Other not found with this :increment_id incrementId.',
                'error-not-auth-customer'   => 'Warning: You are not authorize to change the status of this order.',
                'error-already-set'         => 'Warning: Order status already set to :order_status.',
                'error-already-invoiced'    => 'Warning: All the items of #:increment_id Order are already invoiced, So you can not change order status to :order_status.',
                'success-status-changed'    => 'Success: Order status has been changed to :order_status for #:increment_id.',
                'error-order-status'        => 'Warning: This order can not change to :order_status.',
                'error-invalid-status'      => 'Warning: This order did not use in Bagisto.'
            ],

            'shipping-methods'   => [
                'error-guest-product'   => 'Warning: Your cart contains product(s), which are not allowed for guest checkout.',
                'no-shipping-address'    => 'Warning: No shipping address found with this :address_id address id.',
                'error-shipping-address'    => 'Warning: There are some error in getting shipping methods.',
                'error-shipping-rates'  => 'Warning: There are some error in getting shipping rates.',
            ],

            'coupon'    => [
                'no-coupon-applied' => 'Warning: there is no coupon applied on cart.',
                'remove-success'    => 'Success: coupon (i.e. :couponCode) removed successfully.',
            ]
        ],

        'extra' => [
            'logout'    => [
                'success-logout'    => 'Success: You have logged out successfully.',
            ],

            'custom-collection'    => [
                'error-not-found'   => 'Warning: No custom collection record found.',
            ],

            'notification'  => [
                'error-not-found'   => 'Warning: No notification record found.',
            ],
            'register-device' => [
                'success-register' => 'Success: Device registered successfully.',
                'already-register' => 'Success: This Device alredy registered in record.',
            ]
        ],

        'sales' => [
            'all-to-cart'   => [
                'success-move-to-cart'  => 'Success: :count product(s) added to cart successfully.',
            ],

            'guest-view'    => [
                'success-valid-details'     => 'Success: You have provided correct information.',
                'error-incorrect-details'   => 'Warning: You entered incorrect data. Please try again',
            ],

            'share-wishlist'    => [
                'email-subject'             => 'Take a look at :sender_name\'s Wishlist',
                'message-wishlist-share-1'  => 'Hi, Message from :sender_name, check my wishlist.',
                'message-wishlist-share-2'  => ':sender_name: wants to share this Wishlist from Store :store_name with you.',
                'message-wishlist-share-3'  => 'Thank You.',
                'error-empty-wishlist'      => 'Warning: Customer wishlist is empty.',
                'success-shared-wishlist'   => 'Success: Customer\'s wishlist shared successfully.',
            ]
        ],

        'catalog'   => [
            'add-to-compare'    => [
                'already-added'         => 'Success: product already added to the compare list.',
                'success-added-compare' => 'Success: Product added to the compare list successfully.',
                'error-request'         => 'Warning: You are requesting wrong resource.',
            ],

            'add-to-wishlist'   => [
                'success-added' => 'Success: Item added to wishlist successfully',
                'error-added'   => 'Warning: Product is not added to wishlist.',
            ],

            'remove-from-compare'   => [
                'success-removed'           => 'Success: You have removed product from the compare list.',
                'error-product-not-found'   => 'Warning: you provided wrong information.',
            ],

            'product-share' => [
                'success-email-send'    => 'Success: Out of :total recipient(s), the product has been shared with :send recipient(s).',
                'error-email-send'      => 'Warning: The product is not able to share with the recipient.',
                'email-subject'         => 'Product Share Email!',
                'text-hello'            => 'Hello :receiver_name,',
                'message-product-share' => ':sender_name shared a product with you.<br> You can visit the product by following the below link: <br>',
            ]
        ],

        'index' => [
            'upload-profile-pic'    => [
                'success-profile-uploaded'  => 'Success: Customer profile image uploaded sucessfully.',
            ],

            'upload-banner-pic' => [
                'success-banner-uploaded'  => 'Success: Customer banner image uploaded sucessfully.',
            ]
        ],

        'product-alert' => [
            'price'  => [
                'subscribe-success' => 'Success: You saved the alert subscription.',
                'un-subscribe-success' => 'Success: You have un-subscribed alert successfully.',
                'invalid-product'   => 'Warning: No product found with this :product_id product id.',
            ]
        ],

        'contact'   => [
            'success-email' => 'Success: Thanks for contacting us with comments and questions. We will reply to you very soon.'
        ]
    ],

    'security-warning' => 'Suspicious activity found!!!',
    'nothing-to-delete' => 'Nothing to delete',

    'layouts' => [
        'my-account' => 'حسابي',
        'profile' => 'الملف الشخصي',
        'address' => 'العنوان',
        'reviews' => 'المراجعات',
        'wishlist' => 'قائمة الأمنيات',
        'orders' => 'الطبات',
    ],

    'common' => [
        'error' => 'حدث خطأ. الرجاء المحاولة لاحقاً.',
        'no-result-found' => 'لا توجد نتائج.'
    ],

    'home' => [
        'page-title' => 'عنوان الصفحة',
        'featured-products' => 'المنتجات المميزة',
        'new-products' => 'المنتجات الجديدة',
        'verify-email' => 'تحقق من حساب بريدك الإلكتروني',
        'resend-verify-email' => 'إعادة التحقق من البريد الإلكتروني'
    ],

    'header' => [
        'title' => 'الحساب',
        'dropdown-text' => 'إدارة عربة التسوق ، الطلبات قائمة الأمنيات',
        'sign-in' => 'تسجيل الدخول',
        'sign-up' => 'حساب جديد',
        'account' => 'الحساب',
        'cart' => 'عربة التسوق',
        'profile' => 'الملف الشخصي',
        'wishlist' => 'قائمة الأمنيات',
        'logout' => 'تسجيل الخروج',
        'search-text' => 'ابحث عن منتج'
    ],

    'minicart' => [
        'view-cart' => 'عرض عربة التسوق',
        'checkout' => 'الدفع',
        'cart' => 'عربة التسوق',
        'zero' => '0'
    ],

    'footer' => [
        'subscribe-newsletter' => 'اشترك في الرسائل الدورية.',
        'subscribe' => 'اشترك',
        'locale' => 'اللغة',
        'currency' => 'العملة',
    ],

    'subscription' => [
        'unsubscribe' => 'الإلغاء',
        'subscribe' => 'اشترك',
        'subscribed' => 'تم الاشتراك بنجاح',
        'not-subscribed' => 'لا يمكنك الاشتراك ، حاول مرة أخرى لاحقاً',
        'already' => 'أنت مشترك بالفعل في قائمة اشتراكاتنا',
        'unsubscribed' => 'تم إلغاء الاشتراك.',
        'already-unsub' => 'أنت بالفعل غير مشترك',
    ],

    'search' => [
        'no-results' => 'لا توجد نتائج',
        'page-title' => 'بحث',
        'found-results' => 'تم العثور على نتائج البحث',
        'found-result' => 'تم العثور على نتيجة البحث'
    ],

    'reviews' => [
        'title' => 'المراجعات',
        'add-review-page-title' => 'إضافة مراجعة/تقييم',
        'write-review' => 'اكتب مراجعة/تقييم',
        'review-title' => 'عنوان المراجعة',
        'product-review-page-title' => 'استعراض المنتجات',
        'rating-reviews' => 'التقييم والمراجعات',
        'submit' => 'إرسال',
        'delete-all' => 'حذف كل المراجعات',
        'ratingreviews' => ':rating تقيمات & :review باء-الاستعراضات',
        'star' => 'نجم',
        'percentage' => ':percentage %',
        'id-star' => 'نجم'
    ],

    'customer' => [
        'signup-text' => [
            'account_exists' => 'لديك حساب؟',
            'title' => 'تسجيل الدخول'
        ],

        'signup-form' => [
            'page-title' => 'حساب جديد',
            'title' => 'حساب جديد',
            'firstname' => 'الاسم الأول',
            'lastname' => 'الاسم الأخير',
            'email' => 'البريد الإلكتروني',
            'password' => 'كلمة الدخول',
            'confirm_pass' => 'تأكيد كلمة الدخول',
            'button_title' => 'تسجيل حساب جديد',
            'agree' => 'موافق',
            'terms' => 'الشروط',
            'conditions' => 'الشروط',
            'using' => 'باستخدام هذا الموقع',
            'agreement' => 'اتفاق',
            'success' => 'الحساب أنشئ بنجاح, تم إرسال بريد إلكتروني إلى حسابك للتحقق',
            'success-verify-email-not-sent' => 'الحساب أنشئ بنجاح, لكن البريد الإلكتروني لم يتم إرساله',
            'failed' => 'خطأ! لا يمكن إنشاء حسابك ، حاول مرة أخرى لاحقا',
            'already-verified' => 'حسابك تم التحقق منه بالفعل أو الرجاء محاولة إرسال بريد إلكتروني جديد للتحقق مرة أخرى',
            'verification-not-sent' => 'خطأ! مشكلة في إرسال البريد الإلكتروني للتحقق ، حاول مرة أخرى في وقت لاحق',
            'verification-sent' => 'تم ارسال معلومات التحقق إلى برديك الالكتروني.',
            'verified' => 'تم التحقق من حسابك قم بتسجيل الدخول الآن',
            'verify-failed' => 'لا يمكننا التحقق من بريدك الإلكتروني',
            'dont-have-account' => 'ليس لديك حساب',
        ],

        'login-text' => [
            'no_account' => 'ليس لديك حساب',
            'title' => 'إنشاء حساب جديد',
        ],

        'login-form' => [
            'page-title' => 'تسجيل الدخول',
            'title' => 'تسجيل الدخول',
            'email' => 'البريد الإلكتروني',
            'password' => 'كلمة الدخول',
            'forgot_pass' => 'نسيت كلمة الدخول؟',
            'button_title' => 'تسجيل الدخول',
            'remember' => 'تذكريني',
            'footer' => '© 2019  جميع الحقوق محفوظة',
            'invalid-creds' => 'الرجاء التحقق من معلومات الدخول',
            'verify-first' => 'الرجاء قم بتفعيل حسابك.',
            'resend-verification' => 'إعادة إرسال البريد الإلكتروني للتحقق مرة أخرى'
        ],

        'forgot-password' => [
            'title' => 'استرجع كلمة الدخول',
            'email' => 'البريد الإلكتروني',
            'submit' => 'إسترجاع',
            'page_title' => 'استرجع كلمة الدخول'
        ],

        'reset-password' => [
            'title' => 'تعيين كلمة الدخول',
            'email' => 'البريد الإلكتروني ',
            'password' => 'كلمة الدخول',
            'confirm-password' => 'تأكيد كلمة الدخول',
            'back-link-title' => 'تسجيل الدخول',
            'submit-btn-title' => 'تعيين كلمة الدخول'
        ],

        'account' => [
            'dashboard' => 'الملف الشخصي',
            'menu' => 'القائمة',

            'profile' => [
                'index' => [
                    'page-title' => 'الملف الشخصي',
                    'title' => 'الملف الشخصي',
                    'edit' => 'تعديل',
                ],

                'edit-success' => 'جاري تحديث الملف بنجاح',
                'edit-fail' => 'خطأ! الملف الشخصي لا يمكن تحديثه ، رجاء حاول مرة أخرى لاحقا',
                'unmatch' => 'كلمة الدخول القديمة لا تتطابق',

                'fname' => 'الاسم الأول',
                'lname' => 'الاسم الأخير',
                'gender' => 'نوع الجنس',
                'dob' => 'تاريخ الميلاد',
                'phone' => 'الهاتف',
                'email' => 'البريد الإلكتروني',
                'opassword' => 'كلمة الدخول القديمة',
                'password' => 'كلمة الدخول',
                'cpassword' => 'تأكيد كلمة الدخول',
                'submit' => 'تحديث الملف الشخصي',

                'edit-profile' => [
                    'title' => 'تعديل الملف الشخصي',
                    'page-title' => 'تعديل الملف الشخصي'
                ]
            ],

            'address' => [
                'index' => [
                    'page-title' => 'عنوان العميل',
                    'title' => 'العنوان',
                    'add' => 'أضف العنوان',
                    'edit' => 'تعديل',
                    'empty' => 'ليس لديك أي عناوين محفوظة هنا ، من فضلك حاول أن تنشئها بالضغط على الرابط بالأسفل',
                    'create' => 'عنوان جديد',
                    'delete' => 'احذف',
                    'make-default' => 'افتراضي',
                    'default' => 'افتراضي',
                    'contact' => 'معلومات الإتصال',
                    'confirm-delete' =>  'هل تريد حقا حذف هذا العنوان؟',
                    'default-delete' => 'لا يمكن تغيير العنوان الافتراضي',
                    'enter-password' => 'Enter Your Password.',
                ],

                'create' => [
                    'page-title' => 'إضاف عنوان',
                    'title' => 'أضف العنوان',
                    'address1' => 'العنوان سطر 1',
                    'country' => 'البلد',
                    'city' => 'المدينة',
                    'state' => 'المنطقة/الولاية',
                    'select-state' => 'اختر منطقة أو ولاية أو مقاطعة',
                    'postcode' => 'الرمز البريدي',
                    'phone' => 'الهاتف',
                    'submit' => 'احفظ العنوان',
                    'success' => 'تم إضافة العنوان بنجاح.',
                    'error' => 'لا يمكن إضافة العنوان.'
                ],

                'edit' => [
                    'page-title' => 'تعديل العنوان',
                    'title' => 'تعديل العنوان',
                    'submit' => 'احفظ العنوان',
                    'success' => 'العنوان تم تحديثه بنجاح.'
                ],
                'delete' => [
                    'success' => 'تم حذف العنوان بنجاح.',
                    'failure' => 'لا يمكن حذف العنوان',
                    'wrong-password' => 'Wrong Password !'
                ]
            ],

            'order' => [
                'index' => [
                    'page-title' => 'طلبات العملاء',
                    'title' => 'الطلبات',
                    'order_id' => 'ترتيب',
                    'date' => 'التاريخ',
                    'status' => 'الحالة',
                    'total' => 'المجموع'
                ],

                'view' => [
                    'page-tile' => 'ترتيب #:order_id',
                    'info' => 'معلومات',
                    'placed-on' => 'وضع على',
                    'products-ordered' => 'المنتجات المطلوبة',
                    'invoices' => 'الفواتير',
                    'shipments' => 'الشحنات',
                    'SKU' => 'SKU',
                    'product-name' => 'الاسم',
                    'qty' => 'الكمية',
                    'item-status' => 'حالة البند',
                    'item-ordered' => 'أمر(:qty_ordered)',
                    'item-invoice' => '3-الفواتير(:qty_invoiced)',
                    'item-shipped' => 'شحنت(:qty_shipped)',
                    'item-canceled' => 'ملغاة(:qty_canceled)',
                    'item-refunded' => 'Refunded (:qty_refunded)',
                    'price' => 'السعر',
                    'total' => 'المجموع',
                    'subtotal' => 'المجموع الفرعي',
                    'shipping-handling' => 'الشحن والتوصيل',
                    'tax' => 'الضرائب',
                    'discount' => 'تخفيض',
                    'tax-percent' => 'نسبة الضرائب',
                    'tax-amount' => 'المبلغ الضريبي',
                    'discount-amount' => 'مبلغ الخصم',
                    'grand-total' => 'المجموع الكلي',
                    'total-paid' => 'المجموع المدفوع',
                    'total-refunded' => 'مجموع المبالغ المستردة',
                    'total-due' => 'المجموع المستحق',
                    'shipping-address' => 'عنوان الشحن',
                    'billing-address' => 'عنوان الفواتير',
                    'shipping-method' => 'طريقة الشحن',
                    'payment-method' => 'طريقة الدفع',
                    'individual-invoice' => 'فاتورة #:invoice_id',
                    'individual-shipment' => 'الشحن #:shipment_id',
                    'print' => 'اطبع',
                    'invoice-id' => 'رقم الفاتورة',
                    'order-id' => 'ترتيب ',
                    'order-date' => 'تاريخ الطلب',
                    'bill-to' => 'الفاتورة إلى',
                    'ship-to' => 'يشحن إلى',
                    'contact' => 'معلومات الإتصال',
                    'refunds' => 'المسترجع',
                    'individual-refund' => 'مسترجع #:refund_id',
                    'adjustment-refund' => 'تعديلات عملية الإسترجاع',
                    'adjustment-fee' => 'تكلفة تعديلات عملية الإسترجاع'
                ]
            ],

            'wishlist' => [
                'page-title' => 'Customer - Wishlist',
                'title' => 'قائمة الأمنيات',
                'deleteall' => 'احذف الكل',
                'moveall' => 'نقل الكل إلى عربة التوسق',
                'move-to-cart' => 'نقل إلى عربة التسوق',
                'error' => 'لا يمكن إضافة المنتج إلى قائمة الأمنيات ، الرجاء المحاولة لاحقا',
                'add' => 'تم إضافة العنصر بنجاح إلى قائمة الأمنيات',
                'remove' => 'تم حذف العنصر بنجاح من قائمة الأمنيات',
                'moved' => 'تم نقل البند بنجاح إلى قائمة الأمنيات',
                'move-error' => 'لا يمكن نقل العنصر إلى قائمة الأمنيات ، رجاء حاول مرة أخرى لاحقا',
                'success' => 'البند مضاف بنجاح إلى قائمة الأمنيات',
                'failure' => 'لا يمكن إضافة العنصر إلى قائمة الأمنيات ، رجاء حاول مرة أخرى لاحقا',
                'already' => 'العنصر موجود بالفعل في قائمة أمنياتك',
                'removed' => 'البند حذف بنجاح من قائمة الأمنيات',
                'remove-fail' => 'لا يمكن حذف العنصر من قائمة الأماني ، الرجاء المحاولة لاحقا',
                'empty' => 'You do not have any items in your Wishlist',
                'remove-all-success' => 'كل الأشياء من قائمة أمانيك قد أزيلت',
            ],

            'downloadable_products' => [
                'title' => 'Downloadable Products',
                'order-id' => 'Order Id',
                'date' => 'Date',
                'name' => 'Title',
                'status' => 'Status',
                'pending' => 'Pending',
                'available' => 'Available',
                'expired' => 'Expired',
                'remaining-downloads' => 'Remaining Downloads',
                'unlimited' => 'Unlimited',
                'download-error' => 'Download link has been expired.'
            ],

            'review' => [
                'index' => [
                    'title' => 'المراجعات',
                    'page-title' => 'مراجعات العملاء'
                ],

                'view' => [
                    'page-tile' => 'مراجعة #:id',
                ]
            ]
        ]
    ],

    'products' => [
        'layered-nav-title' => 'المنتجات',
        'price-label' => 'أقل من',
        'remove-filter-link-title' => 'امسح الكل',
        'sort-by' => 'افرز حسب',
        'from-a-z' => 'من a-z',
        'from-z-a' => 'من ز-أ',
        'newest-first' => 'الأحدث أولا',
        'oldest-first' => 'الأكبر أولا',
        'cheapest-first' => 'الأرخص أولا',
        'expensive-first' => 'الأغلى أولا',
        'show' => 'اعرض',
        'pager-info' => 'عرض :showing of :total Items',
        'description' => 'الوصف',
        'specification' => 'مواصفات',
        'total-reviews' => ':total المراجعات',
        'total-rating' => ':total_rating تقييمات & :total_reviews مراجعات',
        'by' => 'من قبل :name',
        'up-sell-title' => 'وجدنا منتجات أخرى قد ترغب!',
        'reviews-title' => 'المراجعات',
        'write-review-btn' => 'اكتب مراجعة',
        'choose-option' => 'اختر ',
        'sale' => 'بيع',
        'new' => 'جديد',
        'empty' => 'لا توجد منتجات متاحة في هذه الفئة',
        'add-to-cart' => 'أضف إلى العربة',
        'buy-now' => 'اشتر الآن',
        'whoops' => 'خطأ!',
        'quantity' => 'الكمية',
        'in-stock' => 'متوفر',
        'out-of-stock' => 'غير متوفر',
        'view-all' => 'عرض الكل',
        'less-quantity' => 'Quantity can not be less than one.',
        'starting-at' => 'Starting at',
        'customize-options' => 'Customize Options',
        'choose-selection' => 'Choose a selection',
        'your-customization' => 'Your Customization',
        'total-amount' => 'Total Amount',
        'none' => 'None',
        'less-quantity' => 'الكمية لايمكن ان تكون أقل من واحد.'
    ],

    // 'reviews' => [
    //     'empty' => 'أنت لم تراجع أي منتج لحد الآن'
    // ]

    'buynow' => [
        'no-options' => 'رجاء تحديد خيارات قبل شراء هذا المنتج'
    ],


    'checkout' => [
        'cart' => [
            'integrity' => [
                'missing_fields' =>'إنتهاك سلامة نظام العربة ، بعض الحقول المطلوبة مفقودة',
                'missing_options' =>'إنتهاك سلامة نظام العربة ، الخيارات مفقودة لمنتج قابل للتهيئة',
                'missing_links' => 'Downloadable links are missing for this product.',
                'qty_missing' => 'Atleast one product should have more than 1 quantity.'
            ],
            'create-error' => 'صادفت بعض القضايا أثناء صناعة السيارات',
            'title' => 'عربة التسوق',
            'empty' => 'عربة تسوقك فارغة',
            'update-cart' => 'تحديث عربة',
            'continue-shopping' => 'واصل التسوق',
            'proceed-to-checkout' => 'انتقل إلى الخروج',
            'remove' => 'احذف',
            'remove-link' => 'احذف',
            'move-to-wishlist' => 'انقل إلى قائمة الأمنيات',
            'move-to-wishlist-success' => 'نقل العنصر إلى قائمة الأمنيات',
            'move-to-wishlist-error' => 'لا يستطيع انقل عنصر إلى رجاء حاول ثانية لاحقا',
            'add-config-warning' => 'الرجاء اختيار الخيار قبل إضافة إلى العربة',
            'quantity' => [
                'quantity' => 'الكمية',
                'success' => 'العنصر(ق) من العربة تم تحديثه بنجاح',
                'illegal' => 'الكمية لا يمكن أن تكون أقل من واحد',
                'inventory_warning' => 'الكمية المطلوبة غير متوفرة ، الرجاء المحاولة لاحقا',
                'error' => 'لا يستطيع تحديث عنصر s في الوقت الحالي رجاء حاول ثانية لاحقا'
            ],
            'item' => [
                'error_remove' => 'لا عناصر لإزالتها من العربة',
                'success' => 'تم بنجاح إضافة العنصر إلى العربة',
                'success-remove' => 'تم إزالة العنصر بنجاح من العربة',
                'error-add' => 'لا يمكن إضافة العنصر إلى العربة ، رجاء حاول مرة أخرى ',
            ],
            'quantity-error' => 'الكمية المطلوبة غير متوفرة',
            'cart-subtotal' => 'المجموع الفرعي للمشتريات',
            'cart-remove-action' => 'هل تريد حقا أن تسمح هذا ؟',
            'partial-cart-update' => 'تم تحديث بعض المنتجات.'
        ],

        'onepage' => [
            'title' => 'الدفع',
            'information' => 'معلومات',
            'shipping' => 'الشحن',
            'payment' => 'الدفع',
            'complete' => 'اكتمل',
            'billing-address' => 'عنوان الفواتير',
            'sign-in' => 'تسجيل الدخول',
            'first-name' => 'الاسم الأول',
            'last-name' => 'الاسم الأخير',
            'email' => 'البريد الإلكتروني',
            'address1' => 'العنوان',
            'city' => 'المدينة',
            'state' => 'المنطقة/الولاية',
            'select-state' => 'اختر منطقة أو ولاية أو مقاطعة',
            'postcode' => 'الرمز البريدي ',
            'phone' => 'الهاتف',
            'country' => 'البلد',
            'order-summary' => 'معلومات الطلب',
            'shipping-address' => 'عنوان الشحن',
            'use_for_shipping' => 'إشحن إلى هذا العنوان',
            'continue' => 'إستمرار',
            'shipping-method' => 'طريقة الشحن',
            'payment-information' => 'معلومات الدفع',
            'payment-method' => 'طريقة الدفع',
            'summary' => 'المعلومات',
            'price' => 'السعر',
            'quantity' => 'الكمية',
            'contact' => 'معلومات الإتصال',
            'place-order' => 'إكمال الطلب'
        ],

        'total' => [
            'order-summary' => 'معلومات الطلب',
            'sub-total' => 'العناصر',
            'grand-total' => 'المجموع الكلي',
            'delivery-charges' => 'رسوم التسليم',
            'tax' => 'الضرائب',
            'discount' => 'التخفيض',
            'price' => 'السعر '
        ],

        'success' => [
            'title' => 'تم الدفع بنجاح',
            'thanks' => 'شكرا على طلبك!',
            'order-id-info' => 'رقم الطلب هو #:order_id',
            'info' => 'سنرسل لك بريدا الكترونيا ، تفاصيل طلباتك و معلومات التعقب'
        ]
    ],

    'mail' => [
        'order' => [
            'subject' => 'تأكيد الطلب الجديد',
            'heading' => 'تأكيد الطلب!',
            'dear' => 'عزيزي :customer_name',
            'greeting' => 'شكرا على طلبك :order_id placed on :created_at',
            'summary' => 'معلومات الطلب',
            'shipping-address' => 'عنوان الشحن',
            'billing-address' => 'عنوان الفواتير',
            'contact' => 'معلومات الإتصال',
            'shipping' => 'الشحن',
            'payment' => 'الدفع',
            'price' => 'السعر',
            'quantity' => 'الكمية',
            'subtotal' => 'المجموع الفرعي',
            'shipping-handling' => 'الشحن والتوصيل',
            'tax' => 'الضرائب',
            'discount' => 'التخفيض',
            'grand-total' => 'المجموع الكلي',
            'final-summary' => 'شكرا لإظهارك إهتمامك بمتجرنا سنرسل لك رقم التتبع بمجرد شحنه',
            'help' => 'إذا كنت بحاجة إلى أي نوع من المساعدة يرجى الاتصال بنا على: support_email',
            'thanks' => 'شكرا!'
        ],

        'invoice' => [
            'heading' => 'فاتورتك #:invoice_id لطلبك #:order_id',
            'subject' => 'فاتورة لطلبك #:order_id',
            'summary' => 'موجز الفاتورة',
        ],

        'shipment' => [
            'heading' => 'شحنتك #:shipment_id لطلبك #:order_id',
            'subject' => 'شحنة لطلبك #:order_id',
            'summary' => 'موجز الشحن',
            'carrier' => 'الناقل',
            'tracking-number' => 'رقم التتبع'
        ],

        'refund' => [
            'heading' => 'المسترجع #:refund_id لطبلك #:order_id',
            'subject' => 'المسترجع لطلبك #:order_id',
            'summary' => 'تفاصيل المسترجع',
            'adjustment-refund' => 'تعديلات عملية الإسترجاع',
            'adjustment-fee' => 'تكلفة تعديلات عملية الإسترجاع'
        ],

        'forget-password' => [
            'dear' => 'عزيزي :name',
            'info' => 'أنت تستلم هذا البريد الإلكتروني لأننا تلقينا طلب إعادة ضبط كلمة الدخول لحسابك',
            'reset-password' => 'أعد ضبط كلمة الدخول',
            'final-summary' => 'إذا لم تطلب إعادة تعيين كلمة الدخول ، لا إجراء آخر مطلوب',
            'thanks' => 'شكرا!'
        ]
    ],

    'webkul' => [
        'copy-right' => 'حقوق الملكية محفوظة 2019'
    ],

    'response' => [
        'create-success' => ':name إنشء بنجاح.',
        'update-success' => ':name تم تعديله بنحاح.',
        'delete-success' => ':name تم مسحه بنجاح.',
        'submit-success' => ':name تم الإرسال بنجاح.'
    ],
];