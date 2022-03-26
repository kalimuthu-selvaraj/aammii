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
        'my-account' => 'Minha Conta',
        'profile' => 'Perfil',
        'address' => 'Endereço',
        'reviews' => 'Avaliação',
        'wishlist' => 'Lista de Desejos',
        'orders' => 'Pedidos',
    ],

    'common' => [
        'error' => 'Algo deu errado, por favor, tente novamente mais tarde.',
        'no-result-found' => 'We could not find any records.'
    ],

    'home' => [
        'page-title' => config('app.name') . ' - Home',
        'featured-products' => 'Produtos em Destaque',
        'new-products' => 'Novos Produtos',
        'verify-email' => 'Verifique sua Conta de E-mail',
        'resend-verify-email' => 'Reenviar Email de Verificação'
    ],

    'header' => [
        'title' => 'Conta',
        'dropdown-text' => 'Gerenciar Carrinho, Pedidos & Lista de Desejos',
        'sign-in' => 'Entrar',
        'sign-up' => 'Criar Conta',
        'account' => 'Conta',
        'cart' => 'Carrinho',
        'profile' => 'Perfil',
        'wishlist' => 'Lista de Desejos',
        'cart' => 'Carrinho',
        'logout' => 'Sair',
        'search-text' => 'Pesquisar produtos aqui'
    ],

    'minicart' => [
        'view-cart' => 'Visualizar Carrinho',
        'checkout' => 'Finalizar Compra',
        'cart' => 'Carrinho',
        'zero' => '0'
    ],

    'footer' => [
        'subscribe-newsletter' => 'Assinar Newsletter',
        'subscribe' => 'Assinar',
        'locale' => 'Idioma',
        'currency' => 'Moeda',
    ],

    'subscription' => [
        'unsubscribe' => 'Cancelar Inscrição',
        'subscribe' => 'Inscrever',
        'subscribed' => 'Você está agora inscrito nos e-mails de newsletter',
        'not-subscribed' => 'Você não pode se inscrever, tente novamente após algum tempo',
        'already' => 'Você já está inscrito em nossa lista de assinaturas',
        'unsubscribed' => 'Você não está inscrito em nossa lista de assinaturas',
        'already-unsub' => 'Você não está mais inscrito em nossa lista de assinaturas',
        'not-subscribed' => 'Erro! Email não pode ser enviado, por favor, tente novamente mais tarde'
    ],

    'search' => [
        'no-results' => 'Nenhum resultado encontrado',
        'page-title' => 'Buscar',
        'found-results' => 'Resultados da pesquisa encontrados',
        'found-result' => 'Resultado da pesquisa encontrado'
    ],

    'reviews' => [
        'title' => 'Título',
        'add-review-page-title' => 'Adicionar Avaliação',
        'write-review' => 'Escreva uma avaliação',
        'review-title' => 'Dê um título a sua avaliação',
        'product-review-page-title' => 'Avaliação do Produto',
        'rating-reviews' => 'Notas & Avaliação',
        'submit' => 'ENVIAR',
        'delete-all' => 'Todas Avaliações foram excluídas com sucesso',
        'ratingreviews' => ':rating Nota & :review Avaliação',
        'star' => 'Estrela',
        'percentage' => ':percentage %',
        'id-star' => 'estrela',
        'name' => 'Nome'
    ],

    'customer' => [
        'signup-text' => [
            'account_exists' => 'Já tem uma conta',
            'title' => 'Entrar'
        ],

        'signup-form' => [
            'page-title' => 'Cliente - Formulário de Cadastro',
            'title' => 'Cadastrar',
            'firstname' => 'Nome',
            'lastname' => 'Sobrenome',
            'email' => 'Email',
            'password' => 'Senha',
            'confirm_pass' => 'Confirmar Senha',
            'button_title' => 'Cadastrar',
            'agree' => 'Concordo',
            'terms' => 'Termos',
            'conditions' => 'Condições',
            'using' => 'usando este site',
            'agreement' => 'Acordo',
            'success' => 'Conta criado com sucesso, um e-mail foi enviado para sua verificação de conta',
            'success-verify-email-not-sent' => 'Conta criada com sucesso, mas o email de verificação não foi enviado',
            'failed' => 'Erro! Não é possível criar sua conta, tente novamente mais tarde',
            'already-verified' => 'Sua conta já foi confirmada ou tente enviar novamente novo de e-mail de confirmação',
            'verification-not-sent' => 'Erro! Problema ao enviar e-mail de verificação, tente novamente mais tarde',
            'verification-sent' => 'E-mail de verificação enviado',
            'verified' => 'Sua Conta Foi Verificada, Tente Entrar Agora',
            'verify-failed' => 'Não podemos verificar sua conta de e-mail',
            'dont-have-account' => 'Você não tem conta conosco',
        ],

        'login-text' => [
            'no_account' => 'Não tem conta',
            'title' => 'Cadastrar',
        ],

        'login-form' => [
            'page-title' => 'Cliente - Login',
            'title' => 'Entrar',
            'email' => 'Email',
            'password' => 'Senha',
            'forgot_pass' => 'Esqueceu sua Senha?',
            'button_title' => 'Entrar',
            'remember' => 'Lembrar de mim',
            'footer' => '© Copyright :year Webkul Software, Todos os direitos reservados',
            'invalid-creds' => 'Por favor, verifique suas credenciais e tente novamente',
            'verify-first' => 'Verifique seu e-mail primeiro',
            'resend-verification' => 'Reenviar email de verificação novamente'
        ],

        'forgot-password' => [
            'title' => 'Recuperar Senha',
            'email' => 'Email',
            'submit' => 'Enviar',
            'page_title' => 'Esqueci minha Senha'
        ],

        'reset-password' => [
            'title' => 'Redefinir Senha',
            'email' => 'Email registrado',
            'password' => 'Senha',
            'confirm-password' => 'Confirmar Senha',
            'back-link-title' => 'Voltar para Login',
            'submit-btn-title' => 'Redefinir Senha'
        ],

        'account' => [
            'dashboard' => 'Cliente - Perfil',
            'menu' => 'Menu',

            'profile' => [
                'index' => [
                    'page-title' => 'Cliente - Perfil',
                    'title' => 'Perfil',
                    'edit' => 'Editar',
                ],

                'edit-success' => 'Perfil Atualizado com Sucesso',
                'edit-fail' => 'Erro! O perfil não pode ser atualizado, por favor, tente novamente mais tarde',
                'unmatch' => 'A senha antiga não corresponde',

                'fname' => 'Nome',
                'lname' => 'Sobrenome',
                'gender' => 'Gênero',
                'dob' => 'Data de Nascimento',
                'phone' => 'Telefone',
                'email' => 'Email',
                'opassword' => 'Senha antiga',
                'password' => 'Senha',
                'cpassword' => 'Confirmar Senha',
                'submit' => 'Atualizar Perfil',

                'edit-profile' => [
                    'title' => 'Editar Perfil',
                    'page-title' => 'Cliente - Editar Perfil'
                ]
            ],

            'address' => [
                'index' => [
                    'page-title' => 'Cliente - Endereço',
                    'title' => 'Endereço',
                    'add' => 'Adicionar Endereço',
                    'edit' => 'Editar',
                    'empty' => 'Você não tem nenhum endereço salvo aqui, por favor tente criá-lo clicando no link abaixo',
                    'create' => 'Criar Endereço',
                    'delete' => 'Deletar',
                    'make-default' => 'Definir como Padrão',
                    'default' => 'Padrão',
                    'contact' => 'Contato',
                    'confirm-delete' =>  'Você realmente deseja excluir este endereço?',
                    'default-delete' => 'O endereço padrão não pode ser alterado',
                    'enter-password' => 'Enter Your Password.',
                ],

                'create' => [
                    'page-title' => 'Cliente - Adicionar Endereço',
                    'title' => 'Novo Endereço',
                    'address1' => 'Endereço Linha 1',
                    'street-address' => 'Endereço',
                    'country' => 'País',
                    'state' => 'Estado',
                    'select-state' => 'Select a region, state or province',
                    'city' => 'Cidade',
                    'postcode' => 'CEP',
                    'phone' => 'Telefone',
                    'submit' => 'Salvar Endereço',
                    'success' => 'Endereço foi adicionado com sucesso.',
                    'error' => 'Endereço não pode ser adicionado.'
                ],

                'edit' => [
                    'page-title' => 'Cliente - Editar Endereço',
                    'title' => 'Editar Endereço',
                    'submit' => 'Salvar Endereço',
                    'success' => 'Endereço Atualizado com sucesso.'
                ],
                'delete' => [
                    'success' => 'Endereço Excluído com sucesso',
                    'failure' => 'Endereço não pode ser adicionado',
                    'wrong-password' => 'Wrong Password !'
                ]
            ],

            'order' => [
                'index' => [
                    'page-title' => 'Cliente - Pedidos',
                    'title' => 'Pedidos',
                    'order_id' => 'Pedido ID',
                    'date' => 'Data',
                    'status' => 'Status',
                    'total' => 'Total'
                ],

                'view' => [
                    'page-tile' => 'Pedido #:order_id',
                    'info' => 'Informação',
                    'placed-on' => 'Criado em',
                    'products-ordered' => 'Produtos Pedidos',
                    'invoices' => 'Faturas',
                    'shipments' => 'Entregas',
                    'SKU' => 'SKU',
                    'product-name' => 'Nome',
                    'qty' => 'Qtd',
                    'item-status' => 'Item Status',
                    'item-ordered' => 'Pedidos (:qty_ordered)',
                    'item-invoice' => 'Faturados (:qty_invoiced)',
                    'item-shipped' => 'enviados (:qty_shipped)',
                    'item-canceled' => 'Cancelados (:qty_canceled)',
                    'item-refunded' => 'Refunded (:qty_refunded)',
                    'price' => 'Preço',
                    'total' => 'Total',
                    'subtotal' => 'Subtotal',
                    'shipping-handling' => 'Entrega & Manuseio',
                    'tax' => 'Imposto',
                    'discount' => 'Discount',
                    'tax-percent' => 'Percentagem de imposto',
                    'tax-amount' => 'Valor de Imposto',
                    'discount-amount' => 'Valor de Desconto',
                    'grand-total' => 'Total',
                    'total-paid' => 'Total Pago',
                    'total-refunded' => 'Total Estornado',
                    'total-due' => 'Total Devido',
                    'shipping-address' => 'Endereço de Entrega',
                    'billing-address' => 'Endereço de Cobrança',
                    'shipping-method' => 'Método de Entrega',
                    'payment-method' => 'Método de Pagamento',
                    'individual-invoice' => 'Fatura #:invoice_id',
                    'individual-shipment' => 'Entrega #:shipment_id',
                    'print' => 'Imprimir',
                    'invoice-id' => 'Fatura Id',
                    'order-id' => 'Pedido Id',
                    'order-date' => 'Pedido Date',
                    'bill-to' => 'Cobrança de',
                    'ship-to' => 'Enviar para',
                    'contact' => 'Contato',
                    'refunds' => 'Refunds',
                    'individual-refund' => 'Refund #:refund_id',
                    'adjustment-refund' => 'Adjustment Refund',
                    'adjustment-fee' => 'Adjustment Fee',
                ]
            ],

            'wishlist' => [
                'page-title' => 'Customer - Wishlist',
                'title' => 'Lista de Desejos',
                'deleteall' => 'Excluir Tudo',
                'moveall' => 'Adicionar todos ao Carrinho',
                'move-to-cart' => 'Adicionar ao Carrinho',
                'error' => 'Não é possível adicionar o produto a lista de Desejos devido a problemas desconhecidos, por favor tente mais tarde',
                'add' => 'Item adicionado com sucesso a Lista de Desejos',
                'remove' => 'Item removido com sucesso da Lista de Desejos',
                'moved' => 'Item movido com sucesso para Lista de Desejos',
                'move-error' => 'Item não pode ser movido para Lista de Desejos, por favor, tente novamente mais tarde',
                'success' => 'Item adicionado com sucesso a Lista de Desejos',
                'failure' => 'Item não pode ser adicionado à Lista de Desejos, por favor, tente novamente mais tarde',
                'already' => 'Item já presente em sua lista de desejos',
                'removed' => 'Item removido com sucesso da Lista de Desejos',
                'remove-fail' => 'Item não pode ser removido da lista de desejos, por favor, tente novamente mais tarde',
                'empty' => 'Você não tem nenhum item em sua Lista de Desejos',
                'remove-all-success' => 'Todos os itens da sua lista de desejos foram removidos',
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
                    'title' => 'Avaliação',
                    'page-title' => 'Cliente - Avaliação'
                ],

                'view' => [
                    'page-tile' => 'Avaliação #:id',
                ]
            ]
        ]
    ],

    'products' => [
        'layered-nav-title' => 'Compre por',
        'price-label' => 'Tão baixo quanto',
        'remove-filter-link-title' => 'Limpar Todos',
        'sort-by' => 'Ordernar por',
        'from-a-z' => 'De A-Z',
        'from-z-a' => 'De Z-A',
        'newest-first' => 'Novos Primeiros',
        'oldest-first' => 'Antigos Primeiros',
        'cheapest-first' => 'Mais baratos primeiros',
        'expensive-first' => 'Mas caros primeiros',
        'show' => 'Visualiar',
        'pager-info' => 'Mostrando :showing de um :total de Itens',
        'description' => 'Descrição',
        'specification' => 'Especificação',
        'total-reviews' => ':total Avaliação',
        'total-rating' => ':total_rating Notas & :total_reviews Avaliações',
        'by' => 'Por :name',
        'up-sell-title' => 'Encontramos outros produtos que você pode gostar!',
        'related-product-title' => 'Produtos Relacionados',
        'cross-sell-title' => 'Mais escolhas',
        'reviews-title' => 'Classificações & Avaliação',
        'write-review-btn' => 'Escreva uma Avaliação',
        'choose-option' => 'Escolha uma opção',
        'sale' => 'Promoção',
        'new' => 'Novo',
        'empty' => 'Nenhum produto disponível nesta categoria',
        'add-to-cart' => 'Adicionar ao Carrinho',
        'buy-now' => 'Comprar Agora',
        'whoops' => 'Oppss!',
        'quantity' => 'Quantidade',
        'in-stock' => 'Em Estoque',
        'out-of-stock' => 'Fora de Estoque',
        'view-all' => 'Ver Tudo',
        'select-above-options' => 'Por favor, selecione as opções acima primeiro.',
        'less-quantity' => 'Quantity can not be less than one.',
        'starting-at' => 'Starting at',
        'customize-options' => 'Customize Options',
        'choose-selection' => 'Choose a selection',
        'your-customization' => 'Your Customization',
        'total-amount' => 'Total Amount',
        'none' => 'None'
    ],

    // 'reviews' => [
    //     'empty' => 'Você ainda não avaliou qualquer produto'
    // ]

    'buynow' => [
        'no-options' => 'Por favor, selecione as opções antes de comprar este produto'
    ],


    'checkout' => [
        'cart' => [
            'integrity' => [
                'missing_fields' =>'Violação de integridade do sistema de carrinho, alguns campos obrigatórios ausentes',
                'missing_options' =>'Violação de Integridade do Sistema de Carrinho, Faltam Opções para o Produto Configurável',
                'missing_links' => 'Downloadable links are missing for this product.',
                'qty_missing' => 'Atleast one product should have more than 1 quantity.'
            ],

            'create-error' => 'Encontrou algum problema ao fazer a instância do carrinho',
            'title' => 'Carrinho de Compras',
            'empty' => 'Seu carrinho de compras está vazio',
            'update-cart' => 'Atualizar Carrinho',
            'continue-shopping' => 'Continuar Comprando',
            'proceed-to-checkout' => 'Finalizar Compra',
            'remove' => 'Remover',
            'remove-link' => 'Remover',
            'move-to-wishlist' => 'Mover para Lista de Desejos',
            'move-to-wishlist-success' => 'Item Movido para Lista de Desejos',
            'move-to-wishlist-error' => 'Não foi possivel Mover Item para Lista de Desejos, Por favor, tente novamente mais tarde',
            'add-config-warning' => 'Por favor, selecione a opção antes de adicionar ao carrinho',
            'quantity' => [
                'quantity' => 'Quantidade',
                'success' => 'Carrinho Item(s) Atualizados com Sucesso!',
                'illegal' => 'Quantidade não pode ser menor que um',
                'inventory_warning' => 'A quantidade solicitada não está disponível, por favor, tente novamente mais tarde',
                'error' => 'Não é possível atualizar o item(s) no momento, por favor, tente novamente mais tarde'
            ],

            'item' => [
                'error_remove' => 'Nenhum item para remover do carrinho',
                'success' => 'Item foi adicionado com sucesso ao carrinho',
                'success-remove' => 'Item foi removido com sucesso do carrinho',
                'error-add' => 'Item não pode ser adicionado ao carrinho, por favor, tente novamente mais tarde',
            ],

            'quantity-error' => 'Quantidade solicitada não está disponível',
            'cart-subtotal' => 'Subtotal do carrinho',
            'cart-remove-action' => 'Você realmente quer fazer isso ?',
            'partial-cart-update' => 'Only some of the product(s) were updated'
        ],

        'onepage' => [
            'title' => 'Finalização Compra',
            'information' => 'Informação',
            'shipping' => 'Entrega',
            'payment' => 'Pagamento',
            'complete' => 'Completo',
            'billing-address' => 'Endereço de Cobrança',
            'sign-in' => 'Entrar',
            'first-name' => 'Nome',
            'last-name' => 'Sobrenome',
            'email' => 'E-mail',
            'address1' => 'Endereço',
            'address2' => 'Endereço 2',
            'city' => 'Cidade',
            'state' => 'Estado',
            'select-state' => 'Selecione uma região, estado e província',
            'postcode' => 'CEP',
            'phone' => 'Telefone',
            'country' => 'País',
            'order-summary' => 'Resumo do Pedido',
            'shipping-address' => 'Endereço de Entrega',
            'use_for_shipping' => 'Enviar para esse endereçõ',
            'continue' => 'Continuar',
            'shipping-method' => 'Selecione o Método de Entrega',
            'payment-methods' => 'Selecione o Método de Pagamento',
            'payment-method' => 'Método de Pagamento',
            'summary' => 'Resumo do Pedido',
            'price' => 'Preço',
            'quantity' => 'Quantidade',
            'billing-address' => 'Endereço de Cobrança',
            'shipping-address' => 'Endereço de Entrega',
            'contact' => 'Contato',
            'place-order' => 'Enviar Pedido',
            'new-address' => 'Add Novo Endereço',
            'save_as_address' => 'Salvar Endereço'
        ],

        'total' => [
            'order-summary' => 'Resumo do Pedido',
            'sub-total' => 'Itens',
            'grand-total' => 'Total',
            'delivery-charges' => 'Taxas de Entrega',
            'tax' => 'Imposto',
            'discount' => 'Discount',
            'price' => 'preço'
        ],

        'success' => [
            'title' => 'Pedido enviado com sucesso!',
            'thanks' => 'Obrigado pelo seu pedido!',
            'order-id-info' => 'Seu ID do Pedido é #:order_id',
            'info' => 'Nós lhe enviaremos por e-mail, detalhes do seu pedido e informações de rastreamento'
        ]
    ],

    'mail' => [
        'order' => [
            'subject' => 'Confirmação de Novo Pedido',
            'heading' => 'Confirmação de Pedido!',
            'dear' => 'Caro :customer_name',
            'greeting' => 'Obrigado pelo seu Pedido :order_id realizado em :created_at',
            'summary' => 'Resumo do Pedido',
            'shipping-address' => 'Endereço de Entrega',
            'billing-address' => 'Endereço de Cobrança',
            'contact' => 'Contato',
            'shipping' => 'Entrega',
            'payment' => 'Pagamento',
            'price' => 'Preço',
            'quantity' => 'Quantidade',
            'subtotal' => 'Subtotal',
            'shipping-handling' => 'Envio & Manuseio',
            'tax' => 'Imposto',
            'discount' => 'Discount',
            'grand-total' => 'Total',
            'final-summary' => 'Obrigado por mostrar o seu interesse em nossa loja nós lhe enviaremos o número de rastreamento assim que for despachado',
            'help' => 'Se você precisar de algum tipo de ajuda, por favor entre em contato conosco :support_email',
            'thanks' => 'Muito Obrigado!'
        ],

        'invoice' => [
            'heading' => 'Sua Fatura #:invoice_id do Pedido #:order_id',
            'subject' => 'Fatura do seu pedido #:order_id',
            'summary' => 'Resumo da Fatura',
        ],

        'refund' => [
            'heading' => 'Your Refund #:refund_id for Order #:order_id',
            'subject' => 'Refund for your order #:order_id',
            'summary' => 'Summary of Refund',
            'adjustment-refund' => 'Adjustment Refund',
            'adjustment-fee' => 'Adjustment Fee'
        ],

        'shipment' => [
            'heading' => 'Sua Entrega #:shipment_id do Pedido #:order_id',
            'subject' => 'Entrega do seu pedido #:order_id',
            'summary' => 'Resumo da Entrega',
            'carrier' => 'Transportadora',
            'tracking-number' => 'Código de Rastreio'
        ],

        'forget-password' => [
            'dear' => 'Caro :name',
            'info' => 'Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para sua conta',
            'reset-password' => 'Redefinir Senha',
            'final-summary' => 'Se você não solicitou uma redefinição de senha, nenhuma ação adicional é necessária',
            'thanks' => 'Muito Obrigado!'
        ]
    ],

    'webkul' => [
        'copy-right' => '© Copyright :year Webkul Software, Todos os Direitos Reservados',
    ],

    'response' => [
        'create-success' => ':name criado com sucesso.',
        'update-success' => ':name atualizado com sucesso.',
        'delete-success' => ':name excluído com sucesso.',
        'submit-success' => ':name enviado com sucesso.'
    ],
];