<?php

return [
    '/'                                              => 'site/index',
    'contact'                                        => 'site/contact',
    'import'                                         => 'site/import',
    'login'                                          => 'site/login',
    'profile'                                        => 'site/profile',
    'passwordChange'                                 => 'site/profile_password_change',
    'captcha'                                                                              => 'site/captcha',


    'password/recover'                               => 'auth/password_recover',
    'password/recover/activate/<code:\\w+>'          => 'auth/password_recover_activate',

    'registration'                                   => 'auth/registration',
    'registrationActivate/<code:\\w+>'               => 'auth/registration_activate',
    'login2'                                         => 'auth/login',
    'loginAjax'                                      => 'auth/login_ajax',
    'logout'                                         => 'auth/logout',
    'auth'                                           => 'auth/auth',

    // superadmin
    'superAdmin'                                     => 'superadmin/index',
    'adminUsers'                                     => 'superadmin/users',
    'adminUsers/add'                                 => 'superadmin/users_add',
    'adminUsers/<id:\\d+>/edit'                      => 'superadmin/users_edit',
    'adminUsers/<id:\\d+>/delete'                    => 'superadmin/users_delete',

    // stock
    'stock'                                          => 'superadmin_stock/index',
    'stock/add'                                      => 'superadmin_stock/add',
    'stock/<id:\\d+>/edit'                           => 'superadmin_stock/edit',
    'stock/<id:\\d+>/delete'                         => 'superadmin_stock/delete',
    'stock/<id:\\d+>/kurs/add'                       => 'superadmin_stock/kurs_add',
    'stock/<id:\\d+>/kurs/edit'                      => 'superadmin_stock/kurs_edit',
    'stock/kurs/update'                              => 'superadmin_stock/kurs_update',
    'stock/<id:\\d+>/prognosis/add'                  => 'superadmin_stock/prognosis_add',
    'stock/<id:\\d+>/prognosis/edit'                 => 'superadmin_stock/prognosis_edit',
    'stock/prognosis/update'                         => 'superadmin_stock/prognosis_update',
    'stock/<id:\\d+>/graph'                          => 'superadmin_stock/graph',

    'log'                                            => 'superadmin/log',


    'settings'                                       => 'superadmin/settings',

    // admin
    'admin'                                          => 'admin/index',

    'news'                                           => 'admin/news',
    'news/add'                                       => 'admin/news_add',
    'news/<id:\\d+>/edit'                            => 'admin/news_edit',
    'news/<id:\\d+>/delete'                          => 'admin/news_delete',

    // supermoderator
    'superModerator'                                 => 'supermoderator/index',
    'moderators'                                     => 'supermoderator/moderators',
    'moderators/add'                                 => 'supermoderator/moderators_add',
    'moderators/<id:\\d+>/edit'                      => 'supermoderator/moderators_edit',
    'moderators/<id:\\d+>/delete'                    => 'supermoderator/moderators_delete',
    'moderators/settings'                            => 'supermoderator/moderators_settings',
    'moderators/setting/add'                         => 'supermoderator/moderators_setting_add',
    'moderators/setting/<id:\\d+>/edit'              => 'supermoderator/moderators_setting_edit',
    'moderators/setting/<id:\\d+>/remove'            => 'supermoderator/moderators_setting_remove',
    'moderators/<id:\\d+>/violations'                => 'supermoderator/moderator_violations',

    // moderator
    'moderator'                                      => 'moderator/work',
    'moderator/obj/<pid:\\d+>/<type:\\w+>/<id:\\d+>' => 'moderator/view_object',
    'moderator/p'                                    => 'moderator/profile',
    'moderator/<id:\\d+>/history'                    => 'moderator/history',

    // superbuh
    'superBuh'                                       => 'superbuh/index',

    // buh
    'buh'                                            => 'buh/index',

    // editor
    'editor'                                         => 'editor/index',
];
