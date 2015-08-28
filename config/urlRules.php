<?php

return [
    '/'                                     => 'landing/index',
    'contact'                               => 'site/contact',
    'price'                               => 'site/price',
    'captcha'                               => 'site/captcha',

    'searchStockAutocomplete'               => 'site/search_stock_autocomplete',

    'about'                                 => 'site/about',


    'password/recover'                      => 'auth/password_recover',
    'password/recover/activate/<code:\\w+>' => 'auth/password_recover_activate',

    'registration'                          => 'auth/registration',
    'registrationActivate/<code:\\w+>'      => 'auth/registration_activate',
    'login'                                 => 'auth/login',
    'loginAjax'                             => 'auth/login_ajax',
    'logout'                                => 'auth/logout',
    'auth'                                  => 'auth/auth',

    // superadmin
    'superAdmin'                            => 'superadmin/index',
    'adminUsers'                            => 'superadmin/users',
    'adminUsers/add'                        => 'superadmin/users_add',
    'adminUsers/<id:\\d+>/edit'             => 'superadmin/users_edit',
    'adminUsers/<id:\\d+>/delete'           => 'superadmin/users_delete',

    // stock
    'stock'                                 => 'superadmin_stock/index',
    'stock/add'                             => 'superadmin_stock/add',
    'stock/<id:\\d+>/edit'                  => 'superadmin_stock/edit',
    'stock/<id:\\d+>/import'                => 'superadmin_stock/import',
    'stock/<id:\\d+>/importKurs'            => 'superadmin_stock/import_kurs',
    'stock/<id:\\d+>/delete'                => 'superadmin_stock/delete',
    'stock/<id:\\d+>/deletePrognosisRed'    => 'superadmin_stock/prognosis_delete_red',
    'stock/<id:\\d+>/deletePrognosisBlue'   => 'superadmin_stock/prognosis_delete_blue',
    'stock/<id:\\d+>/kurs/add'              => 'superadmin_stock/kurs_add',
    'stock/<id:\\d+>/kurs/edit'             => 'superadmin_stock/kurs_edit',
    'stock/kurs/update'                     => 'superadmin_stock/kurs_update',
    'stock/<id:\\d+>/prognosis/add'         => 'superadmin_stock/prognosis_add',
    'stock/<id:\\d+>/prognosis/edit'        => 'superadmin_stock/prognosis_edit',
    'stock/prognosis/update'                => 'superadmin_stock/prognosis_update',
    'stock/<id:\\d+>/graph'                 => 'superadmin_stock/graph',

    // cabinet
    'cabinet'                               => 'cabinet/index',
    'stockList'                             => 'cabinet/stock_list',
    'stockList/<id:\\d+>'                   => 'cabinet/stock_item',
    'stockList2/<id:\\d+>'                  => 'cabinet/stock_item2',
    'stockList3/<id:\\d+>'                  => 'cabinet/stock_item3',
    'stockList/graphAjax'                   => 'cabinet/graph_ajax',

    'profile'                               => 'cabinet/profile',
    'passwordChange'                        => 'cabinet/profile_password_change',


    'cabinet/passwordChange'                => 'cabinet/password_change',
    'cabinet/profile'                       => 'cabinet/profile',
    'cabinet/profile/unLinkSocialNetWork'   => 'cabinet/profile_unlink_social_network',
    'cabinet/profile/subscribe'             => 'cabinet/profile_subscribe',

    'cabinet/wallet/add/<id:\\d+>'          => 'cabinet_wallet/add',

    'yandexMoney'                           => 'yandex_money/auth',
];
