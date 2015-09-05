<?php

return [
    '/'                                     => 'landing/index',
    'contact'                               => 'site/contact',
    'price'                                 => 'site/price',
    'captcha'                               => 'site/captcha',
    'time'                                  => 'site/time',
    'log'                                   => 'site/log',
    'logDb'                                 => 'site/log_db',

    'about'                                 => 'site/about',

    'password/recover'                      => 'auth/password_recover',
    'password/recover/activate/<code:\\w+>' => 'auth/password_recover_activate',
    'changeEmail/activate/<code:\\w+>'      => 'auth/change_email_activate',

    'registration'                          => 'auth/registration',
    'registration/<code:\\w+>'              => 'auth/registration_referal',
    'registrationActivate/<code:\\w+>'      => 'auth/registration_activate',
    'login'                                 => 'auth/login',
    'loginAjax'                             => 'auth/login_ajax',
    'logout'                                => 'auth/logout',
    'auth'                                  => 'auth/auth',

    // superadmin
    'superAdmin'                            => 'superadmin/index',
    'admin/referal'                         => 'superadmin/referal',
    'admin/referal/delete'                         => 'superadmin/referal_delete',

    'requests'                              => 'superadmin_requests/index',
    'requests/activate/<hash:\\w+>'         => 'superadmin_requests/activate',
    'requests/activate'                     => 'superadmin_requests/activate_ajax',
    'requests/delete'                       => 'superadmin_requests/delete_ajax',

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
    'stock/<id:\\d+>/kurs/delete'           => 'superadmin_stock/kurs_delete',
    'stock/kurs/update'                     => 'superadmin_stock/kurs_update',
    'stock/<id:\\d+>/prognosis/add'         => 'superadmin_stock/prognosis_add',
    'stock/<id:\\d+>/prognosis/edit'        => 'superadmin_stock/prognosis_edit',
    'stock/prognosis/update'                => 'superadmin_stock/prognosis_update',
    'stock/<id:\\d+>/graph'                 => 'superadmin_stock/graph',

    // designer
    'designer'                              => 'designer/index',
    'designer/landing'                      => 'designer/landing',

    // cabinet
    'cabinet'                               => 'cabinet/index',
    'cabinet/savePng'                       => 'cabinet/save_png',
    'stockList'                             => 'cabinet/stock_list',
    'stockList/<id:\\d+>'                   => 'cabinet/stock_item',
    'stockList2/<id:\\d+>'                  => 'cabinet/stock_item2',
    'stockList3/<id:\\d+>'                  => 'cabinet/stock_item3',
    'stockList/graphAjax'                   => 'cabinet/graph_ajax',
    'cabinet/changeEmail'                   => 'cabinet/change_email',

    'profile'                               => 'cabinet/profile',
    'passwordChange'                        => 'cabinet/profile_password_change',


    'searchStockAutocomplete'               => 'cabinet/search_stock_autocomplete',
    'cabinet/passwordChange'                => 'cabinet/password_change',
    'cabinet/profile'                       => 'cabinet/profile',
    'cabinet/profile/unLinkSocialNetWork'   => 'cabinet/profile_unlink_social_network',
    'cabinet/profile/subscribe'             => 'cabinet/profile_subscribe',

    'cabinet/wallet/add/<id:\\d+>'          => 'cabinet_wallet/add',
    'cabinet/wallet/add/step1'              => 'cabinet_wallet/add_step1',

    'money/history'                         => 'money/history',

    'chat'                                  => 'cabinet_chat/index',
    'chat/send'                             => 'cabinet_chat/send',
    'chat/list'                             => 'superadmin_chat/index',
    'chat/user/<id:\\d+>'                   => 'superadmin_chat/user',
    'chat/getNewMessages'                   => 'cabinet_chat/get_new_messages',
    'chat/test'                             => 'cabinet_chat/test',

    'yandexMoney'                           => 'yandex_money/auth',
    'yandexMoney/test1'                     => 'yandex_money/test1',
    'yandexMoney/test2'                     => 'yandex_money/test2',
];
