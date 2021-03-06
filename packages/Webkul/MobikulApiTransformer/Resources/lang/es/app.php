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
    
    'security-warning' => 'Actividad sospechosa detectada!!!',
    'nothing-to-delete' => 'Nada que eliminar',

    'layouts' => [
        'my-account' => 'Mi Cuenta',
        'profile' => 'Perfil',
        'address' => 'Direcci??n',
        'reviews' => 'Opiniones',
        'wishlist' => 'Lista de deseos',
        'orders' => 'Pedidos',
    ],

    'common' => [
        'error' => 'Algo ha ido mal, por favor prueba m??s tarde.'
    ],

    'home' => [
        'page-title' => config('app.name') . ' - Inicio',
        'featured-products' => 'Productos Destacados',
        'new-products' => 'Nuevos Productos',
        'verify-email' => 'Verifica tu cuenta de correo',
        'resend-verify-email' => 'Reenviar correo de verificaci??n'
    ],

    'header' => [
        'title' => 'Cuenta',
        'dropdown-text' => 'Gestionar carrito, pedidos y lista de deseos',
        'sign-in' => 'Entrar',
        'sign-up' => 'Reg??strate',
        'account' => 'Cuenta',
        'cart' => 'Cesta',
        'profile' => 'Perfil',
        'wishlist' => 'Lista de deseos',
        'logout' => 'Salir',
        'search-text' => 'Buscar productos'
    ],

    'minicart' => [
        'view-cart' => 'Ver Cesta',
        'checkout' => 'Hacer pedido',
        'cart' => 'Cesta',
        'zero' => '0'
    ],

    'footer' => [
        'subscribe-newsletter' => 'Suscr??bete a la Newsletter',
        'subscribe' => 'Suscr??bete',
        'locale' => 'Idioma',
        'currency' => 'Moneda',
    ],

    'subscription' => [
        'unsubscribe' => 'Darse de baja',
        'subscribe' => 'Suscr??bete',
        'subscribed' => 'Te has suscrito a la newsletter',
        'not-subscribed' => 'No se pudo suscribir a la newsletter, int??ntalo de nuevo m??s tarde',
        'already' => 'Ya est??s suscrito',
        'unsubscribed' => 'Te has desuscrito',
        'already-unsub' => 'Ya est??s desuscrito',
        'not-subscribed' => '??Error! El correo no se puede enviar actualmente, int??ntalo de nuevo m??s tarde'
    ],

    'search' => [
        'no-results' => 'No hay resultados',
        'page-title' => 'B??squeda',
        'found-results' => 'No hay resultados de b??squeda',
        'found-result' => 'Resultados de la b??squeda'
    ],

    'reviews' => [
        'title' => 'T??tulo',
        'add-review-page-title' => 'A??adir opini??n',
        'write-review' => 'Escribir una opini??n',
        'review-title' => 'T??tulo de la opini??n',
        'product-review-page-title' => 'Opini??n del producto',
        'rating-reviews' => 'Calificaci??n y opiniones',
        'submit' => 'ENVIAR',
        'delete-all' => 'Todas las opiniones se han eliminado con ??xito',
        'ratingreviews' => ':rating calificaciones & :review opiniones',
        'star' => 'Star',
        'percentage' => ':percentage %',
        'id-star' => 'star',
        'name' => 'Nombre'
    ],

    'customer' => [
        'signup-text' => [
            'account_exists' => 'Ya tienes una cuenta',
            'title' => 'Entrar'
        ],

        'signup-form' => [
            'page-title' => 'Cliente - Formulario de registro',
            'title' => 'Reg??strate',
            'firstname' => 'Nombre',
            'lastname' => 'Apellido',
            'email' => 'Email',
            'password' => 'Contrase??a',
            'confirm_pass' => 'Confirma la contrase??a',
            'button_title' => 'Registro',
            'agree' => 'De acuerdo',
            'terms' => 'T??rminos',
            'conditions' => 'Condiciones',
            'using' => 'Mediante el uso de este sitio web',
            'agreement' => 'Acuerdo',
            'success' => 'Cuenta creada exitosamente',
            'success-verify' => 'Cuenta creada con ??xito, se ha enviado un correo electr??nico para su verificaci??n.',
            'success-verify-email-unsent' => 'Cuenta creada correctamente, pero no se envi?? el correo electr??nico de verificaci??n',
            'failed' => '??Error! No se puede crear su cuenta, intente nuevamente m??s tarde',
            'already-verified' => 'Su cuenta ya est?? verificada o intente enviar un nuevo correo electr??nico de verificaci??n nuevamente',
            'verification-not-sent' => '??Error! Problema al enviar el correo electr??nico de verificaci??n, intente nuevamente m??s tarde',
            'verification-sent' => 'El correo de verificaci??n ha sido enviado',
            'verified' => 'Su cuenta ha sido verificada, intente iniciar sesi??n ahora',
            'verify-failed' => 'No podemos verificar su cuenta de correo',
            'dont-have-account' => 'No tienes cuenta con nosotros',
            'customer-registration' => 'Registrado con ??xito'
        ],

        'login-text' => [
            'no_account' => 'No tienes una cuenta',
            'title' => 'Reg??strate',
        ],

        'login-form' => [
            'page-title' => 'Cliente-Formulario de registro',
            'title' => 'Entrar',
            'email' => 'Correo electr??nico',
            'password' => 'Contrase??a',
            'forgot_pass' => '??Has olvidado la contrase??a?',
            'button_title' => 'Entrar',
            'remember' => 'Recu??rdame',
            'footer' => '?? Copyright :year Webkul Software, All rights reserved',
            'invalid-creds' => 'Por favor, verifica tus credenciales e intenta de nuevo',
            'verify-first' => 'Verifica tu correo electr??nico primero',
            'not-activated' => 'La activaci??n de la cuenta ser?? aprovada por el administrador',
            'resend-verification' => 'Se ha reenviado un correo de verificaci??n'
        ],

        'forgot-password' => [
            'title' => 'Recuperar contrase??a',
            'email' => 'Correo electr??nico',
            'submit' => 'ENVIAR',
            'page_title' => 'Cliente - Formulario de contrase??a olvidada'
        ],

        'reset-password' => [
            'title' => 'Restablecer contrase??a',
            'email' => 'Correo registrado',
            'password' => 'Contrase??a',
            'confirm-password' => 'Confirma la contrase??a',
            'back-link-title' => 'Reinicia sesi??n',
            'submit-btn-title' => 'Restablecer contrase??a'
        ],

        'account' => [
            'dashboard' => 'Cliente - Editar perfil',
            'menu' => 'Menu',

            'profile' => [
                'index' => [
                    'page-title' => 'Cliente - Perfil',
                    'title' => 'Perfil',
                    'edit' => 'Editar',
                ],

                'edit-success' => 'Perfil actualizado exitosamente',
                'edit-fail' => '??Error! El perfil no puede ser actualizado, por favor, int??ntalo m??s tarde',
                'unmatch' => 'La anterior contrase??a no coincide',

                'fname' => 'Nombre',
                'lname' => 'Apellido',
                'gender' => 'G??nero',
                'dob' => 'Fecha de nacimiento',
                'phone' => 'M??vil',
                'email' => 'Correo electr??nico',
                'opassword' => 'Contrase??a anterior',
                'password' => 'Contrase??a',
                'cpassword' => 'Confirma la contrase??a',
                'submit' => 'Perfil actualizado',

                'edit-profile' => [
                    'title' => 'Editar Perfil',
                    'page-title' => 'Cliente - Formulario de edici??n de perfil'
                ]
            ],

            'address' => [
                'index' => [
                    'page-title' => 'Cliente - Direcci??n',
                    'title' => 'Direcci??n',
                    'add' => 'A??adir Direcci??n',
                    'edit' => 'Editar',
                    'empty' => 'No tienes ninguna direcci??n guardada, por favor, crea una clicando en el enlace de abajo',
                    'create' => 'Crear Direcci??n',
                    'delete' => 'Eliminar',
                    'make-default' => 'Elegir por defecto',
                    'default' => 'Por defecto',
                    'contact' => 'Contacto',
                    'confirm-delete' =>  '??Quieres eleminar esta direcci??n?',
                    'default-delete' => 'La direcci??n por defecto no puede ser cambiada',
                    'enter-password' => 'Enter Your Password.',
                ],

                'create' => [
                    'page-title' => 'Cliente - Formulario de direcci??n',
                    'title' => 'A??adir direcci??n',
                    'street-address' => 'Calle',
                    'country' => 'Pa??s',
                    'state' => 'Estado',
                    'select-state' => 'Selecciona una regi??n, estado o provincia',
                    'city' => 'Ciudad',
                    'postcode' => 'C??digo postal',
                    'phone' => 'Tel??fono',
                    'submit' => 'Guardar direcci??n',
                    'success' => 'La direcci??n se ha a??adido correctamente.',
                    'error' => 'La direcci??n no se puede a??adir.'
                ],

                'edit' => [
                    'page-title' => 'Cliente - Editar Direcci??n',
                    'title' => 'Editar Direcci??n',
                    'street-address' => 'Calle',
                    'submit' => 'Guardar direcci??n',
                    'success' => 'Direcci??n actualizada exitosamente.',
                ],
                'delete' => [
                    'success' => 'Direcci??n eliminada correctamente',
                    'failure' => 'La direcci??n no puede ser eliminada',
                    'wrong-password' => 'Wrong Password !'
                ]
            ],

            'order' => [
                'index' => [
                    'page-title' => 'Cliente - Pedidos',
                    'title' => 'Pedidos',
                    'order_id' => 'ID Pedido',
                    'date' => 'Fecha',
                    'status' => 'Estado',
                    'total' => 'Total',
                    'order_number' => 'N??mero de pedido'
                ],

                'view' => [
                    'page-tile' => 'Pedido #:order_id',
                    'info' => 'Informaci??n',
                    'placed-on' => 'Ubicaci??n',
                    'products-ordered' => 'Productos pedidos',
                    'invoices' => 'Facturas',
                    'shipments' => 'Env??os',
                    'SKU' => 'SKU',
                    'product-name' => 'Nombre',
                    'qty' => 'Qty',
                    'item-status' => 'Estado Item',
                    'item-ordered' => 'Ordenado (:qty_ordered)',
                    'item-invoice' => 'Facturado (:qty_invoiced)',
                    'item-shipped' => 'Enviado (:qty_shipped)',
                    'item-canceled' => 'Cancelado (:qty_canceled)',
                    'price' => 'Precio',
                    'total' => 'Total',
                    'subtotal' => 'Subtotal',
                    'shipping-handling' => 'Env??o y Manipulaci??n',
                    'tax' => 'Impuesto',
                    'discount' => 'Descuento',
                    'tax-percent' => 'Porcentaje IVA',
                    'tax-amount' => 'Cantidad impuesto',
                    'discount-amount' => 'Cantidad descontada',
                    'grand-total' => 'Total',
                    'total-paid' => 'Total 	Pago',
                    'total-refunded' => 'Total Reembolsado',
                    'total-due' => 'Total',
                    'shipping-address' => 'Direcci??n de env??o',
                    'billing-address' => 'Direcci??n de facturaci??n',
                    'shipping-method' => 'M??todo de env??o',
                    'payment-method' => 'Forma de pago',
                    'individual-invoice' => 'Factura #:invoice_id',
                    'individual-shipment' => 'Env??o #:shipment_id',
                    'print' => 'Imprimir',
                    'invoice-id' => 'Factura Id',
                    'order-id' => 'Pedido Id',
                    'order-date' => 'Fecha pedido',
                    'bill-to' => 'Facturar a',
                    'ship-to' => 'Env??o a',
                    'contact' => 'Contacto'
                ]
            ],

            'wishlist' => [
                'page-title' => 'Customer - Wishlist',
                'title' => 'Lista de deseos',
                'deleteall' => 'Eliminar todo',
                'moveall' => 'Mover todos los productos a la cesta',
                'move-to-cart' => 'Mover a la cesta',
                'error' => 'No se puede agregar el producto a la lista de deseos por problemas desconocidos, int??ntelo m??s tarde.',
                'add' => 'Art??culo a??adido a la lista de deseos',
                'remove' => 'Art??culo eliminado de la lista de deseos',
                'moved' => 'Art??culo movido a la cesta exitosamente',
                'move-error' => 'El art??culo no se puede a??adir a la lista de deseos, por favor int??ntalo m??s tarde',
                'success' => 'Art??culo a??adido a la lista de deseos',
                'failure' => 'El art??culo no se puede a??adir a la lista de deseos, por favor int??ntalo m??s tarde',
                'already' => 'Este art??culo ya est?? en tu lista de deseos.',
                'removed' => 'Art??culo eliminado de la lista de deseos',
                'remove-fail' => 'El art??culo no se puede eliminar de la lista de deseos, por favor int??ntalo m??s tarde',
                'empty' => 'No tiene ning??n producto en su lista de deseos',
                'remove-all-success' => 'Todos los art??culos de su lista de deseos han sido eliminados',
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
                    'title' => 'Opiniones',
                    'page-title' => 'Cliente - Opiniones'
                ],

                'view' => [
                    'page-tile' => 'Opini??n #:id',
                ]
            ]
        ]
    ],

    'products' => [
        'layered-nav-title' => 'Comprado por',
        'price-label' => 'Tan bajo como',
        'remove-filter-link-title' => 'Limpiar todo',
        'sort-by' => 'Ordenar por',
        'from-a-z' => 'De A-Z',
        'from-z-a' => 'De Z-A',
        'newest-first' => 'Lo m??s nuevo primero',
        'oldest-first' => 'Lo m??s antiguo primero',
        'cheapest-first' => 'Lo m??s barato primero',
        'expensive-first' => 'Lo m??s caro primero',
        'show' => 'Show',
        'pager-info' => 'Mostrar :showing of :total Items',
        'description' => 'Descripci??n',
        'specification' => 'Especificaciones',
        'total-reviews' => ':total Reviews',
        'total-rating' => ':total_rating Ratings & :total_reviews Reviews',
        'by' => 'Por :name',
        'up-sell-title' => '??Hemos encontrado otros productos que te pueden gustar!',
        'related-product-title' => 'Productos relacionados',
        'cross-sell-title' => 'M??s opciones',
        'reviews-title' => 'Calificaci??n y Opiniones',
        'write-review-btn' => 'Escribe una valoraci??n',
        'choose-option' => 'Elige una opci??n',
        'sale' => 'En venta',
        'new' => 'Nuevo',
        'empty' => 'No hay prodcutos disponibles en esta categor??a',
        'add-to-cart' => 'A??adir a la cesta',
        'buy-now' => 'Comprar ahora',
        'whoops' => 'Ups!',
        'quantity' => 'Cantidad',
        'in-stock' => 'En estoc',
        'out-of-stock' => 'Sin estoc',
        'view-all' => 'Ver todo',
        'select-above-options' => 'Primero selecciona las opciones de arriba.',
        'less-quantity' => 'La cantidad no debe ser inferior a uno.'
    ],

    // 'reviews' => [
    //     'empty' => 'A??n no has valorado ning??n producto'
    // ]

    'buynow' => [
        'no-options' => 'Por favor selecciona las opciones antes de comprar este producto'
    ],


    'checkout' => [
        'cart' => [
            'integrity' => [
                'missing_fields' =>'Faltan algunos campos requeridos',
                'missing_options' =>'Faltan opciones configurables del producto',
            ],
            'create-error' => 'Se encontraron problemas con la cesta de compra',
            'title' => 'Cesta de la compra',
            'empty' => 'Tu cesta est?? vac??a',
            'update-cart' => 'Actualizar cesta',
            'continue-shopping' => 'Seguir comprando',
            'proceed-to-checkout' => 'Continuar con el pago',
            'remove' => 'Eliminar',
            'remove-link' => 'Eliminar',
            'move-to-wishlist' => 'Mover a la lista de deseos',
            'move-to-wishlist-success' => 'Art??culo movido a la lista de deseos',
            'move-to-wishlist-error' => 'El art??culo no se puede a??adir a la lista de deseos, por favor int??ntalo m??s tarde',
            'add-config-warning' => 'Por favor selecciona las opciones antes de a??adir a la cesta',
            'quantity' => [
                'quantity' => 'Cantidad',
                'success' => 'Cesta actualizada exitosamente',
                'illegal' => 'La cantidad no puede ser menor que uno',
                'inventory_warning' => 'La cantidad solicitada no est?? disponible, int??ntelo m??s tarde',
                'error' => 'No se pueden actualizar los art??culos, int??ntelo m??s tarde'
            ],
            'item' => [
                'error_remove' => 'No hay art??culos que eliminar en la cesta',
                'success' => 'El art??culp se a??adi?? a la cesta',
                'success-remove' => 'El art??culo se elimin?? de la cesta',
                'error-add' => 'El art??culo no se puede a??adir a la cesta, int??ntelo m??s tarde',
            ],
            'quantity-error' => 'La cantidad solicitada no est?? disponible',
            'cart-subtotal' => 'Subtotal',
            'cart-remove-action' => '??Realmente quieres hacer esto?',
            'partial-cart-update' => 'Solo algunos de los productos se han actualizado',
        ],

        'onepage' => [
            'title' => 'Revisar',
            'information' => 'Informaci??n',
            'shipping' => 'Env??o',
            'payment' => 'Pago',
            'complete' => 'Completado',
            'billing-address' => 'Direcci??n de facturaci??n',
            'sign-in' => 'Entrar',
            'first-name' => 'Nombre',
            'last-name' => 'Apellido',
            'email' => 'Correo electr??nico',
            'address1' => 'Calle',
            'city' => 'Ciudad',
            'state' => 'Estado',
            'select-state' => 'Selecciona una regi??n, estado o provincia',
            'postcode' => 'C??digo postal',
            'phone' => 'Tel??fono',
            'country' => 'Pa??s',
            'order-summary' => 'Resumen del pedido',
            'shipping-address' => 'Direcci??n de env??o',
            'use_for_shipping' => 'Enviar a esta direcci??n',
            'continue' => 'Continuar',
            'shipping-method' => 'Seleccionar m??todo de env??o',
            'payment-methods' => 'Seleccionar forma de pago',
            'payment-method' => 'Forma de pago',
            'summary' => 'Resumen del pedido',
            'price' => 'Precio',
            'quantity' => 'Cantidad',
            'billing-address' => 'Direcci??n de facturaci??n',
            'shipping-address' => 'Direcci??n de env??o',
            'contact' => 'Contacto',
            'place-order' => 'Realizar pedido',
            'new-address' => 'A??adir nueva direcci??n',
            'save_as_address' => 'Guardar direcci??n',
            'apply-coupon' => 'Aplicar cup??n',
            'amt-payable' => 'Cantidad a pagar',
            'got' => 'Tienes',
            'free' => 'Gratis',
            'coupon-used' => 'Cup??n usado',
            'applied' => 'Aplicado',
            'back' => 'Volver',
            'cash-desc' => 'Pago en efectivo',
            'money-desc' => 'Transferencia bancaria',
            'paypal-desc' => 'Paypal',
            'free-desc' => 'Env??o gratuito',
            'flat-desc' => 'Esta es una tarifa plana'
        ],

        'total' => [
            'order-summary' => 'Resumen del pedido',
            'sub-total' => 'Art??culos',
            'grand-total' => 'Total',
            'delivery-charges' => 'Gastos de env??o',
            'tax' => 'Impuesto',
            'discount' => 'Descuento',
            'price' => 'Precio',
            'disc-amount' => 'Cantidad descontada',
            'new-grand-total' => 'Total',
            'coupon' => 'Cup??n',
            'coupon-applied' => 'Cup??n aplicado',
            'remove-coupon' => 'Eliminar cup??n',
            'cannot-apply-coupon' => 'No se puede aplicar cup??n'
        ],

        'success' => [
            'title' => 'Pedido realizado correctamente',
            'thanks' => '??Gracias por tu pedido!',
            'order-id-info' => 'Tu n??mero de pedido es #:order_id',
            'info' => 'Te enviaremos un correo electr??nico con los detalles de tu pedido y la informaci??n de seguimiento'
        ]
    ],

    'mail' => [
        'order' => [
            'subject' => 'Nuevo pedido confirmado',
            'heading' => '??Pedido Confirmado!',
            'dear' => 'Estimado/a :customer_name',
            'dear-admin' => 'Estimado/a :admin_name',
            'greeting' => 'Gracias por tu pedido :order_id placed on :created_at',
            'greeting-admin' => 'Pedido n??mero :order_id placed on :created_at',
            'summary' => 'Resumen del pedido',
            'shipping-address' => 'Direcci??n de env??o',
            'billing-address' => 'Direcci??n de facturaci??n',
            'contact' => 'Contacto',
            'shipping' => 'M??todo de env??o',
            'payment' => 'Forma de pago',
            'price' => 'Precio',
            'quantity' => 'Cantidad',
            'subtotal' => 'Subtotal',
            'shipping-handling' => 'Env??o y manipulaci??n',
            'tax' => 'Impuesto',
            'discount' => 'Descuento',
            'grand-total' => 'Total',
            'final-summary' => 'Gracias por tu pedido, te enviaremos el n??mero de seguimiento una vez enviado',
            'help' => 'Si necesitas ayuda contacta con nosotros a trav??s de :support_email',
            'thanks' => '??Gracias!',
            'cancel' => [
                'subject' => 'Confirmaci??n de pedido cancelado',
                'heading' => 'Pedido cancelado',
                'dear' => 'Estimado/a :customer_name',
                'greeting' => 'Tu pedido con el n??mero #:order_id placed on :created_at ha sido cancelado',
                'summary' => 'Resumen del pedido',
                'shipping-address' => 'Direcci??n de env??o',
                'billing-address' => 'Direcci??n de facturaci??n',
                'contact' => 'Contacto',
                'shipping' => 'M??todo de env??o',
                'payment' => 'Forma de pago',
                'subtotal' => 'Subtotal',
                'shipping-handling' => 'Env??o y Manipulaci??n',
                'tax' => 'Impuesto',
                'discount' => 'Descuento',
                'grand-total' => 'Total',
                'final-summary' => 'Gracias por tu inter??s en nuestra tienda',
                'help' => 'Si necesitas ayuda contacta con nosotros a trav??s de :support_email',
                'thanks' => '??Gracias!',
            ]
        ],
        'invoice' => [
            'heading' => 'Tu factura #:invoice_id for Order #:order_id',
            'subject' => 'Factura de tu pedido #:order_id',
            'summary' => 'Resumen de pedido',
        ],
        'shipment' => [
            'heading' => 'El Env??o #:shipment_id  ha sido generado por el pedido #:order_id',
            'inventory-heading' => 'Nuevo env??o #:shipment_id ha sido generado por el pedido #:order_id',
            'subject' => 'Env??o de tu pedido #:order_id',
            'inventory-subject' => 'Nuevo env??o ha sido generado por el pedido #:order_id',
            'summary' => 'Resumen de env??o',
            'carrier' => 'Transportista',
            'tracking-number' => 'N??mero de seguimiento',
            'greeting' => 'El pedido :order_id ha sido enviado a :created_at',
        ],
        'forget-password' => [
            'subject' => 'Restablecer contrase??a cliente',
            'dear' => 'Estimado/a :name',
            'info' => 'Te hemos enviado este correo porque hemos recibido una solicitud para restablecer la contrase??a de tu cuenta',
            'reset-password' => 'Restablecer contrase??a',
            'final-summary' => 'Si no has solicitado cambiar de contrase??a, ninguna acci??n es requerida por tu parte.',
            'thanks' => '??Gracias!'
        ],
        'customer' => [
            'new' => [
                'dear' => 'Estimado/a :customer_name',
                'username-email' => 'Nombre de usuario/Email',
                'subject' => 'Nuevo registro de cliente',
                'password' => 'Contrase??a',
                'summary' => 'Tu cuenta ha sido creada en Bassar.
                Los detalles de tu cuenta puedes verlos abajo: ',
                'thanks' => '??Gracias!',
            ],

            'registration' => [
                'subject' => 'Nuevo registro de cliente',
                'customer-registration' => 'Cliente registrado exitosamente',
                'dear' => 'Estimado/a :customer_name',
                'greeting' => '??Bienvenido y gracias por registrarte en Bassar!',
                'summary' => 'Your account has now been created successfully and you can login using your email address and password credentials. Upon logging in, you will be able to access other services including reviewing past orders, wishlists and editing your account information.',
                'thanks' => '??Gracias!',
            ],

            'verification' => [
                'heading' => 'Bassar - Verificaci??n por correo',
                'subject' => 'Verificaci??n por correo',
                'verify' => 'Verifica tu cuenta',
                'summary' => 'Este mensaje es para verificar que esta direcci??n de mail es tuya.
                Por favor, clica el bot??n de abajo para verificar tu cuenta.'
            ],

            'subscription' => [
                'subject' => 'Subscripci??n mail',
                'greeting' => ' Bienvenido a Bassar - Subscripci??n por mail',
                'unsubscribe' => 'Darse de baja',
                'summary' => 'Gracias por ponernos en tu bandeja de entrada. Ha pasado un tiempo desde que ley?? el ??ltimo correo electr??nico de Bassar, y no queremos abrumar su bandeja de entrada. Si ya no quiere recibir
                las ??ltimas noticias de marketing, haga clic en el bot??n de abajo.'
            ]
        ]
    ],

    'webkul' => [
        'copy-right' => '?? Copyright :year Webkul Software, All rights reserved',
    ],

    'response' => [
        'create-success' => ':name creado correctamente.',
        'update-success' => ':name actualizado correctamente.',
        'delete-success' => ':name eliminado correctamente.',
        'submit-success' => ':name enviado correctamente.'
    ],
];
