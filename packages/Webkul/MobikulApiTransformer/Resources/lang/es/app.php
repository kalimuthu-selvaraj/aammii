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
        'address' => 'Dirección',
        'reviews' => 'Opiniones',
        'wishlist' => 'Lista de deseos',
        'orders' => 'Pedidos',
    ],

    'common' => [
        'error' => 'Algo ha ido mal, por favor prueba más tarde.'
    ],

    'home' => [
        'page-title' => config('app.name') . ' - Inicio',
        'featured-products' => 'Productos Destacados',
        'new-products' => 'Nuevos Productos',
        'verify-email' => 'Verifica tu cuenta de correo',
        'resend-verify-email' => 'Reenviar correo de verificación'
    ],

    'header' => [
        'title' => 'Cuenta',
        'dropdown-text' => 'Gestionar carrito, pedidos y lista de deseos',
        'sign-in' => 'Entrar',
        'sign-up' => 'Regístrate',
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
        'subscribe-newsletter' => 'Suscríbete a la Newsletter',
        'subscribe' => 'Suscríbete',
        'locale' => 'Idioma',
        'currency' => 'Moneda',
    ],

    'subscription' => [
        'unsubscribe' => 'Darse de baja',
        'subscribe' => 'Suscríbete',
        'subscribed' => 'Te has suscrito a la newsletter',
        'not-subscribed' => 'No se pudo suscribir a la newsletter, inténtalo de nuevo más tarde',
        'already' => 'Ya estás suscrito',
        'unsubscribed' => 'Te has desuscrito',
        'already-unsub' => 'Ya estás desuscrito',
        'not-subscribed' => '¡Error! El correo no se puede enviar actualmente, inténtalo de nuevo más tarde'
    ],

    'search' => [
        'no-results' => 'No hay resultados',
        'page-title' => 'Búsqueda',
        'found-results' => 'No hay resultados de búsqueda',
        'found-result' => 'Resultados de la búsqueda'
    ],

    'reviews' => [
        'title' => 'Título',
        'add-review-page-title' => 'Añadir opinión',
        'write-review' => 'Escribir una opinión',
        'review-title' => 'Título de la opinión',
        'product-review-page-title' => 'Opinión del producto',
        'rating-reviews' => 'Calificación y opiniones',
        'submit' => 'ENVIAR',
        'delete-all' => 'Todas las opiniones se han eliminado con éxito',
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
            'title' => 'Regístrate',
            'firstname' => 'Nombre',
            'lastname' => 'Apellido',
            'email' => 'Email',
            'password' => 'Contraseña',
            'confirm_pass' => 'Confirma la contraseña',
            'button_title' => 'Registro',
            'agree' => 'De acuerdo',
            'terms' => 'Términos',
            'conditions' => 'Condiciones',
            'using' => 'Mediante el uso de este sitio web',
            'agreement' => 'Acuerdo',
            'success' => 'Cuenta creada exitosamente',
            'success-verify' => 'Cuenta creada con éxito, se ha enviado un correo electrónico para su verificación.',
            'success-verify-email-unsent' => 'Cuenta creada correctamente, pero no se envió el correo electrónico de verificación',
            'failed' => '¡Error! No se puede crear su cuenta, intente nuevamente más tarde',
            'already-verified' => 'Su cuenta ya está verificada o intente enviar un nuevo correo electrónico de verificación nuevamente',
            'verification-not-sent' => '¡Error! Problema al enviar el correo electrónico de verificación, intente nuevamente más tarde',
            'verification-sent' => 'El correo de verificación ha sido enviado',
            'verified' => 'Su cuenta ha sido verificada, intente iniciar sesión ahora',
            'verify-failed' => 'No podemos verificar su cuenta de correo',
            'dont-have-account' => 'No tienes cuenta con nosotros',
            'customer-registration' => 'Registrado con éxito'
        ],

        'login-text' => [
            'no_account' => 'No tienes una cuenta',
            'title' => 'Regístrate',
        ],

        'login-form' => [
            'page-title' => 'Cliente-Formulario de registro',
            'title' => 'Entrar',
            'email' => 'Correo electrónico',
            'password' => 'Contraseña',
            'forgot_pass' => '¿Has olvidado la contraseña?',
            'button_title' => 'Entrar',
            'remember' => 'Recuérdame',
            'footer' => '© Copyright :year Webkul Software, All rights reserved',
            'invalid-creds' => 'Por favor, verifica tus credenciales e intenta de nuevo',
            'verify-first' => 'Verifica tu correo electrónico primero',
            'not-activated' => 'La activación de la cuenta será aprovada por el administrador',
            'resend-verification' => 'Se ha reenviado un correo de verificación'
        ],

        'forgot-password' => [
            'title' => 'Recuperar contraseña',
            'email' => 'Correo electrónico',
            'submit' => 'ENVIAR',
            'page_title' => 'Cliente - Formulario de contraseña olvidada'
        ],

        'reset-password' => [
            'title' => 'Restablecer contraseña',
            'email' => 'Correo registrado',
            'password' => 'Contraseña',
            'confirm-password' => 'Confirma la contraseña',
            'back-link-title' => 'Reinicia sesión',
            'submit-btn-title' => 'Restablecer contraseña'
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
                'edit-fail' => '¡Error! El perfil no puede ser actualizado, por favor, inténtalo más tarde',
                'unmatch' => 'La anterior contraseña no coincide',

                'fname' => 'Nombre',
                'lname' => 'Apellido',
                'gender' => 'Género',
                'dob' => 'Fecha de nacimiento',
                'phone' => 'Móvil',
                'email' => 'Correo electrónico',
                'opassword' => 'Contraseña anterior',
                'password' => 'Contraseña',
                'cpassword' => 'Confirma la contraseña',
                'submit' => 'Perfil actualizado',

                'edit-profile' => [
                    'title' => 'Editar Perfil',
                    'page-title' => 'Cliente - Formulario de edición de perfil'
                ]
            ],

            'address' => [
                'index' => [
                    'page-title' => 'Cliente - Dirección',
                    'title' => 'Dirección',
                    'add' => 'Añadir Dirección',
                    'edit' => 'Editar',
                    'empty' => 'No tienes ninguna dirección guardada, por favor, crea una clicando en el enlace de abajo',
                    'create' => 'Crear Dirección',
                    'delete' => 'Eliminar',
                    'make-default' => 'Elegir por defecto',
                    'default' => 'Por defecto',
                    'contact' => 'Contacto',
                    'confirm-delete' =>  '¿Quieres eleminar esta dirección?',
                    'default-delete' => 'La dirección por defecto no puede ser cambiada',
                    'enter-password' => 'Enter Your Password.',
                ],

                'create' => [
                    'page-title' => 'Cliente - Formulario de dirección',
                    'title' => 'Añadir dirección',
                    'street-address' => 'Calle',
                    'country' => 'País',
                    'state' => 'Estado',
                    'select-state' => 'Selecciona una región, estado o provincia',
                    'city' => 'Ciudad',
                    'postcode' => 'Código postal',
                    'phone' => 'Teléfono',
                    'submit' => 'Guardar dirección',
                    'success' => 'La dirección se ha añadido correctamente.',
                    'error' => 'La dirección no se puede añadir.'
                ],

                'edit' => [
                    'page-title' => 'Cliente - Editar Dirección',
                    'title' => 'Editar Dirección',
                    'street-address' => 'Calle',
                    'submit' => 'Guardar dirección',
                    'success' => 'Dirección actualizada exitosamente.',
                ],
                'delete' => [
                    'success' => 'Dirección eliminada correctamente',
                    'failure' => 'La dirección no puede ser eliminada',
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
                    'order_number' => 'Número de pedido'
                ],

                'view' => [
                    'page-tile' => 'Pedido #:order_id',
                    'info' => 'Información',
                    'placed-on' => 'Ubicación',
                    'products-ordered' => 'Productos pedidos',
                    'invoices' => 'Facturas',
                    'shipments' => 'Envíos',
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
                    'shipping-handling' => 'Envío y Manipulación',
                    'tax' => 'Impuesto',
                    'discount' => 'Descuento',
                    'tax-percent' => 'Porcentaje IVA',
                    'tax-amount' => 'Cantidad impuesto',
                    'discount-amount' => 'Cantidad descontada',
                    'grand-total' => 'Total',
                    'total-paid' => 'Total 	Pago',
                    'total-refunded' => 'Total Reembolsado',
                    'total-due' => 'Total',
                    'shipping-address' => 'Dirección de envío',
                    'billing-address' => 'Dirección de facturación',
                    'shipping-method' => 'Método de envío',
                    'payment-method' => 'Forma de pago',
                    'individual-invoice' => 'Factura #:invoice_id',
                    'individual-shipment' => 'Envío #:shipment_id',
                    'print' => 'Imprimir',
                    'invoice-id' => 'Factura Id',
                    'order-id' => 'Pedido Id',
                    'order-date' => 'Fecha pedido',
                    'bill-to' => 'Facturar a',
                    'ship-to' => 'Envío a',
                    'contact' => 'Contacto'
                ]
            ],

            'wishlist' => [
                'page-title' => 'Customer - Wishlist',
                'title' => 'Lista de deseos',
                'deleteall' => 'Eliminar todo',
                'moveall' => 'Mover todos los productos a la cesta',
                'move-to-cart' => 'Mover a la cesta',
                'error' => 'No se puede agregar el producto a la lista de deseos por problemas desconocidos, inténtelo más tarde.',
                'add' => 'Artículo añadido a la lista de deseos',
                'remove' => 'Artículo eliminado de la lista de deseos',
                'moved' => 'Artículo movido a la cesta exitosamente',
                'move-error' => 'El artículo no se puede añadir a la lista de deseos, por favor inténtalo más tarde',
                'success' => 'Artículo añadido a la lista de deseos',
                'failure' => 'El artículo no se puede añadir a la lista de deseos, por favor inténtalo más tarde',
                'already' => 'Este artículo ya está en tu lista de deseos.',
                'removed' => 'Artículo eliminado de la lista de deseos',
                'remove-fail' => 'El artículo no se puede eliminar de la lista de deseos, por favor inténtalo más tarde',
                'empty' => 'No tiene ningún producto en su lista de deseos',
                'remove-all-success' => 'Todos los artículos de su lista de deseos han sido eliminados',
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
                    'page-tile' => 'Opinión #:id',
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
        'newest-first' => 'Lo más nuevo primero',
        'oldest-first' => 'Lo más antiguo primero',
        'cheapest-first' => 'Lo más barato primero',
        'expensive-first' => 'Lo más caro primero',
        'show' => 'Show',
        'pager-info' => 'Mostrar :showing of :total Items',
        'description' => 'Descripción',
        'specification' => 'Especificaciones',
        'total-reviews' => ':total Reviews',
        'total-rating' => ':total_rating Ratings & :total_reviews Reviews',
        'by' => 'Por :name',
        'up-sell-title' => '¡Hemos encontrado otros productos que te pueden gustar!',
        'related-product-title' => 'Productos relacionados',
        'cross-sell-title' => 'Más opciones',
        'reviews-title' => 'Calificación y Opiniones',
        'write-review-btn' => 'Escribe una valoración',
        'choose-option' => 'Elige una opción',
        'sale' => 'En venta',
        'new' => 'Nuevo',
        'empty' => 'No hay prodcutos disponibles en esta categoría',
        'add-to-cart' => 'Añadir a la cesta',
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
    //     'empty' => 'Aún no has valorado ningún producto'
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
            'empty' => 'Tu cesta está vacía',
            'update-cart' => 'Actualizar cesta',
            'continue-shopping' => 'Seguir comprando',
            'proceed-to-checkout' => 'Continuar con el pago',
            'remove' => 'Eliminar',
            'remove-link' => 'Eliminar',
            'move-to-wishlist' => 'Mover a la lista de deseos',
            'move-to-wishlist-success' => 'Artículo movido a la lista de deseos',
            'move-to-wishlist-error' => 'El artículo no se puede añadir a la lista de deseos, por favor inténtalo más tarde',
            'add-config-warning' => 'Por favor selecciona las opciones antes de añadir a la cesta',
            'quantity' => [
                'quantity' => 'Cantidad',
                'success' => 'Cesta actualizada exitosamente',
                'illegal' => 'La cantidad no puede ser menor que uno',
                'inventory_warning' => 'La cantidad solicitada no está disponible, inténtelo más tarde',
                'error' => 'No se pueden actualizar los artículos, inténtelo más tarde'
            ],
            'item' => [
                'error_remove' => 'No hay artículos que eliminar en la cesta',
                'success' => 'El artículp se añadió a la cesta',
                'success-remove' => 'El artículo se eliminó de la cesta',
                'error-add' => 'El artículo no se puede añadir a la cesta, inténtelo más tarde',
            ],
            'quantity-error' => 'La cantidad solicitada no está disponible',
            'cart-subtotal' => 'Subtotal',
            'cart-remove-action' => '¿Realmente quieres hacer esto?',
            'partial-cart-update' => 'Solo algunos de los productos se han actualizado',
        ],

        'onepage' => [
            'title' => 'Revisar',
            'information' => 'Información',
            'shipping' => 'Envío',
            'payment' => 'Pago',
            'complete' => 'Completado',
            'billing-address' => 'Dirección de facturación',
            'sign-in' => 'Entrar',
            'first-name' => 'Nombre',
            'last-name' => 'Apellido',
            'email' => 'Correo electrónico',
            'address1' => 'Calle',
            'city' => 'Ciudad',
            'state' => 'Estado',
            'select-state' => 'Selecciona una región, estado o provincia',
            'postcode' => 'Código postal',
            'phone' => 'Teléfono',
            'country' => 'País',
            'order-summary' => 'Resumen del pedido',
            'shipping-address' => 'Dirección de envío',
            'use_for_shipping' => 'Enviar a esta dirección',
            'continue' => 'Continuar',
            'shipping-method' => 'Seleccionar método de envío',
            'payment-methods' => 'Seleccionar forma de pago',
            'payment-method' => 'Forma de pago',
            'summary' => 'Resumen del pedido',
            'price' => 'Precio',
            'quantity' => 'Cantidad',
            'billing-address' => 'Dirección de facturación',
            'shipping-address' => 'Dirección de envío',
            'contact' => 'Contacto',
            'place-order' => 'Realizar pedido',
            'new-address' => 'Añadir nueva dirección',
            'save_as_address' => 'Guardar dirección',
            'apply-coupon' => 'Aplicar cupón',
            'amt-payable' => 'Cantidad a pagar',
            'got' => 'Tienes',
            'free' => 'Gratis',
            'coupon-used' => 'Cupón usado',
            'applied' => 'Aplicado',
            'back' => 'Volver',
            'cash-desc' => 'Pago en efectivo',
            'money-desc' => 'Transferencia bancaria',
            'paypal-desc' => 'Paypal',
            'free-desc' => 'Envío gratuito',
            'flat-desc' => 'Esta es una tarifa plana'
        ],

        'total' => [
            'order-summary' => 'Resumen del pedido',
            'sub-total' => 'Artículos',
            'grand-total' => 'Total',
            'delivery-charges' => 'Gastos de envío',
            'tax' => 'Impuesto',
            'discount' => 'Descuento',
            'price' => 'Precio',
            'disc-amount' => 'Cantidad descontada',
            'new-grand-total' => 'Total',
            'coupon' => 'Cupón',
            'coupon-applied' => 'Cupón aplicado',
            'remove-coupon' => 'Eliminar cupón',
            'cannot-apply-coupon' => 'No se puede aplicar cupón'
        ],

        'success' => [
            'title' => 'Pedido realizado correctamente',
            'thanks' => '¡Gracias por tu pedido!',
            'order-id-info' => 'Tu número de pedido es #:order_id',
            'info' => 'Te enviaremos un correo electrónico con los detalles de tu pedido y la información de seguimiento'
        ]
    ],

    'mail' => [
        'order' => [
            'subject' => 'Nuevo pedido confirmado',
            'heading' => '¡Pedido Confirmado!',
            'dear' => 'Estimado/a :customer_name',
            'dear-admin' => 'Estimado/a :admin_name',
            'greeting' => 'Gracias por tu pedido :order_id placed on :created_at',
            'greeting-admin' => 'Pedido número :order_id placed on :created_at',
            'summary' => 'Resumen del pedido',
            'shipping-address' => 'Dirección de envío',
            'billing-address' => 'Dirección de facturación',
            'contact' => 'Contacto',
            'shipping' => 'Método de envío',
            'payment' => 'Forma de pago',
            'price' => 'Precio',
            'quantity' => 'Cantidad',
            'subtotal' => 'Subtotal',
            'shipping-handling' => 'Envío y manipulación',
            'tax' => 'Impuesto',
            'discount' => 'Descuento',
            'grand-total' => 'Total',
            'final-summary' => 'Gracias por tu pedido, te enviaremos el número de seguimiento una vez enviado',
            'help' => 'Si necesitas ayuda contacta con nosotros a través de :support_email',
            'thanks' => '¡Gracias!',
            'cancel' => [
                'subject' => 'Confirmación de pedido cancelado',
                'heading' => 'Pedido cancelado',
                'dear' => 'Estimado/a :customer_name',
                'greeting' => 'Tu pedido con el número #:order_id placed on :created_at ha sido cancelado',
                'summary' => 'Resumen del pedido',
                'shipping-address' => 'Dirección de envío',
                'billing-address' => 'Dirección de facturación',
                'contact' => 'Contacto',
                'shipping' => 'Método de envío',
                'payment' => 'Forma de pago',
                'subtotal' => 'Subtotal',
                'shipping-handling' => 'Envío y Manipulación',
                'tax' => 'Impuesto',
                'discount' => 'Descuento',
                'grand-total' => 'Total',
                'final-summary' => 'Gracias por tu interés en nuestra tienda',
                'help' => 'Si necesitas ayuda contacta con nosotros a través de :support_email',
                'thanks' => '¡Gracias!',
            ]
        ],
        'invoice' => [
            'heading' => 'Tu factura #:invoice_id for Order #:order_id',
            'subject' => 'Factura de tu pedido #:order_id',
            'summary' => 'Resumen de pedido',
        ],
        'shipment' => [
            'heading' => 'El Envío #:shipment_id  ha sido generado por el pedido #:order_id',
            'inventory-heading' => 'Nuevo envío #:shipment_id ha sido generado por el pedido #:order_id',
            'subject' => 'Envío de tu pedido #:order_id',
            'inventory-subject' => 'Nuevo envío ha sido generado por el pedido #:order_id',
            'summary' => 'Resumen de envío',
            'carrier' => 'Transportista',
            'tracking-number' => 'Número de seguimiento',
            'greeting' => 'El pedido :order_id ha sido enviado a :created_at',
        ],
        'forget-password' => [
            'subject' => 'Restablecer contraseña cliente',
            'dear' => 'Estimado/a :name',
            'info' => 'Te hemos enviado este correo porque hemos recibido una solicitud para restablecer la contraseña de tu cuenta',
            'reset-password' => 'Restablecer contraseña',
            'final-summary' => 'Si no has solicitado cambiar de contraseña, ninguna acción es requerida por tu parte.',
            'thanks' => '¡Gracias!'
        ],
        'customer' => [
            'new' => [
                'dear' => 'Estimado/a :customer_name',
                'username-email' => 'Nombre de usuario/Email',
                'subject' => 'Nuevo registro de cliente',
                'password' => 'Contraseña',
                'summary' => 'Tu cuenta ha sido creada en Bassar.
                Los detalles de tu cuenta puedes verlos abajo: ',
                'thanks' => '¡Gracias!',
            ],

            'registration' => [
                'subject' => 'Nuevo registro de cliente',
                'customer-registration' => 'Cliente registrado exitosamente',
                'dear' => 'Estimado/a :customer_name',
                'greeting' => '¡Bienvenido y gracias por registrarte en Bassar!',
                'summary' => 'Your account has now been created successfully and you can login using your email address and password credentials. Upon logging in, you will be able to access other services including reviewing past orders, wishlists and editing your account information.',
                'thanks' => '¡Gracias!',
            ],

            'verification' => [
                'heading' => 'Bassar - Verificación por correo',
                'subject' => 'Verificación por correo',
                'verify' => 'Verifica tu cuenta',
                'summary' => 'Este mensaje es para verificar que esta dirección de mail es tuya.
                Por favor, clica el botón de abajo para verificar tu cuenta.'
            ],

            'subscription' => [
                'subject' => 'Subscripción mail',
                'greeting' => ' Bienvenido a Bassar - Subscripción por mail',
                'unsubscribe' => 'Darse de baja',
                'summary' => 'Gracias por ponernos en tu bandeja de entrada. Ha pasado un tiempo desde que leyó el último correo electrónico de Bassar, y no queremos abrumar su bandeja de entrada. Si ya no quiere recibir
                las últimas noticias de marketing, haga clic en el botón de abajo.'
            ]
        ]
    ],

    'webkul' => [
        'copy-right' => '© Copyright :year Webkul Software, All rights reserved',
    ],

    'response' => [
        'create-success' => ':name creado correctamente.',
        'update-success' => ':name actualizado correctamente.',
        'delete-success' => ':name eliminado correctamente.',
        'submit-success' => ':name enviado correctamente.'
    ],
];
