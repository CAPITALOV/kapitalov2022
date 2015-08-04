<?php

return [
    '/'                                              => 'site/index',
    'contact'                                        => 'site/contact',
    'login'                                          => 'site/login',
    'logout'                                         => 'site/logout',
    'profile'                                        => 'site/profile',
    'passwordChange'                                 => 'site/profile_password_change',

    // superadmin
    'superAdmin'                                     => 'superadmin/index',
    'adminUsers'                                     => 'superadmin/users',
    'adminUsers/add'                                 => 'superadmin/users_add',
    'adminUsers/<id:\\d+>/edit'                      => 'superadmin/users_edit',
    'adminUsers/<id:\\d+>/delete'                    => 'superadmin/users_delete',

    // communities
    'communities'                                    => 'superadmin_communities/index',
    'communities/add'                                => 'superadmin_communities/add',
    'communities/<id:\\d+>/edit'                     => 'superadmin_communities/edit',
    'communities/<id:\\d+>/delete'                   => 'superadmin_communities/delete',
    'communities/sort'                               => 'superadmin_communities/sort',

    // votingPriceList
    'votingPriceList'                                => 'superadmin_voting_price_list/index',
    'votingPriceList/add'                            => 'superadmin_voting_price_list/add',
    'votingPriceList/<id:\\d+>/edit'                 => 'superadmin_voting_price_list/edit',
    'votingPriceList/<id:\\d+>/delete'               => 'superadmin_voting_price_list/delete',
    'votingPriceList/sort'                           => 'superadmin_voting_price_list/sort',

    'components'                                     => 'superadmin/components',
    'components/install'                             => 'superadmin/components_install',
    'components/<id:\\d+>/upgrade'                   => 'superadmin/components_upgrade',
    'components/<id:\\d+>/public'                    => 'superadmin/components_public',
    'components/<id:\\d+>/unpublic'                  => 'superadmin/components_unpublic',
    'components/<id:\\d+>/config/get'                => 'superadmin/components_config_get',
    'components/<id:\\d+>/config/set'                => 'superadmin/components_config_set',

    'modules'                                        => 'superadmin/modules',
    'modules/install'                                => 'superadmin/modules_install',

    'clearCache'                                     => 'superadmin/clear_cache',
    'clearCache/ajax'                                => 'superadmin/clear_cache_ajax',


    'modules/<id:\\d+>/extraPosition'                => 'superadmin/modules_extra_position',
    'modules/<id:\\d+>/extraPosition/delete'         => 'superadmin/modules_extra_position_delete',
    'modules/<id:\\d+>/extraPosition/update'         => 'superadmin/modules_extra_position_update',

    'modules/<id:\\d+>/upgrade'                      => 'superadmin/modules_upgrade',
    'modules/<id:\\d+>/public'                       => 'superadmin/modules_public',
    'modules/<id:\\d+>/unpublic'                     => 'superadmin/modules_unpublic',
    'modules/<id:\\d+>/edit'                         => 'superadmin/modules_edit',
    'modules/<id:\\d+>/config/get'                   => 'superadmin/modules_config_get',
    'modules/<id:\\d+>/config/set'                   => 'superadmin/modules_config_set',

    'plugins'                                        => 'superadmin/plugins',
    'plugins/install'                                => 'superadmin/plugins_install',
    'plugins/<id:\\d+>/upgrade'                      => 'superadmin/plugins_upgrade',
    'plugins/<id:\\d+>/public'                       => 'superadmin/plugins_public',
    'plugins/<id:\\d+>/unpublic'                     => 'superadmin/plugins_unpublic',
    'plugins/<id:\\d+>/config/get'                   => 'superadmin/plugins_config_get',
    'plugins/<id:\\d+>/config/set'                   => 'superadmin/plugins_config_set',

    'topMenu'                                        => 'superadmin/top_menu',
    'topMenu/resort'                                 => 'superadmin/top_menu_resort',
    'topMenu/<id:\\d+>/public'                       => 'superadmin/top_menu_public',
    'topMenu/<id:\\d+>/unpublic'                     => 'superadmin/top_menu_unpublic',
    'topMenu/add'                                    => 'superadmin/top_menu_add',
    'topMenu/<id:\\d+>/edit'                         => 'superadmin/top_menu_edit',
    'topMenu/<id:\\d+>/delete'                       => 'superadmin/top_menu_delete',

    'userMenu'                                       => 'superadmin/user_menu',
    'userMenu/add'                                   => 'superadmin/user_menu_add',
    'userMenu/resort'                                => 'superadmin/user_menu_resort',
    'userMenu/<id:\\d+>/edit'                        => 'superadmin/user_menu_edit',
    'userMenu/<id:\\d+>/delete'                      => 'superadmin/user_menu_delete',

    'jsLogger'                                       => 'superadmin/js_logger',
    'jsLogger/delete/<id:\\d+>'                      => 'superadmin/js_logger_delete',
    'jsLogger/deleteAll'                             => 'superadmin/js_logger_delete_all',

    'log'                                            => 'superadmin/log',
    'cron'                                           => 'superadmin/cron',
    'cron/add'                                       => 'superadmin/cron_add',
    'cron/<id:\\d+>/edit'                            => 'superadmin/cron_edit',
    'cron/<id:\\d+>/delete'                          => 'superadmin/cron_delete',
    'cron/<id:\\d+>/execute'                         => 'superadmin/cron_execute',


    'settings'                                       => 'superadmin/settings',

    'check_files/db'                                 => 'superadmin/check_db',
    'check_files/recheck_records/<type:\\w+>'        => 'superadmin/recheck_records',
    'check_files/recheck_all_records'                => 'superadmin/recheck_all_records',
    'check_files/del_all_db_rows'                    => 'superadmin/del_all_db_rows',
    'check_files/del_all_founded_records'            => 'superadmin/del_all_founded_records',
    'check_files/del_db_row/<id:\\d+>'               => 'superadmin/del_db_row',
    'check_files/del_founded_row/<id:\\d+>'          => 'superadmin/del_founded_row',
    'check_files/del_db_rows'                        => 'superadmin/del_db_rows',
    'check_files/del_founded_rows'                   => 'superadmin/del_founded_rows',
    'check_files/find_records/<qty:\\d+>'            => 'superadmin/find_records',
    'check_files/files'                              => 'superadmin/check_files',
    'check_files/recheck_folder/<num:\\d+>'          => 'superadmin/recheck_folder',
    'check_files/recheck_all_folders'                => 'superadmin/recheck_all_folders',
    'check_files/find_files/<qty:\\d+>'              => 'superadmin/find_files',
    'check_files/del_all_files'                      => 'superadmin/del_all_files',
    'check_files/del_all_records'                    => 'superadmin/del_all_records',
    'check_files/del_file/<rowId:\\d+>'              => 'superadmin/del_file',
    'check_files/del_record/<rowId:\\d+>'            => 'superadmin/del_record',
    'check_files/del_files'                          => 'superadmin/del_files',
    'check_files/del_records'                        => 'superadmin/del_records',

    // admin
    'admin'                                          => 'admin/index',
    'users'                                          => 'admin/users',
    'gifts'                                          => 'admin/gifts',
    'gifts/<id:\\d+>/add'                            => 'admin/gifts_add',
    'gifts/<id:\\d+>/edit'                           => 'admin/gifts_edit',
    'gifts/<id:\\d+>/delete'                         => 'admin/gifts_delete',
    'users/<id:\\d+>/block'                          => 'admin/users_block',
    'users/<id:\\d+>/unblock'                        => 'admin/users_unblock',
    'users/<id:\\d+>/edit'                           => 'admin/users_edit',
    'users/<id:\\d+>/objects'                        => 'admin/objects',
    'users/objects/fileList/<id:\\d+>/delete'        => 'admin/objects_files_delete',
    'users/objects/fileList/<id:\\d+>/block'         => 'admin/objects_files_block',
    'users/objects/videoList/<id:\\d+>/delete'       => 'admin/objects_video_delete',
    'users/objects/videoList/<id:\\d+>/block'        => 'admin/objects_video_block',
    'users/objects/votingList/<id:\\d+>/delete'      => 'admin/objects_voting_delete',

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

    //moderation api
    'api/moderation/addviolation'                    => 'apimoderation/add_violation',
    'api/moderation/moderator.violate'               => 'apimoderation/add_moderator_violation',
    'api/moderation/apply.action'                    => 'apimoderation/apply_moderation_action',
    'api/moderation/stats'                           => 'apimoderation/stats_chart',
    'api/moderation/eventlistener'                   => 'apimoderation/event_listener',

    // superbuh
    'superBuh'                                       => 'superbuh/index',

    // buh
    'buh'                                            => 'buh/index',

    // editor
    'editor'                                         => 'editor/index',
];
