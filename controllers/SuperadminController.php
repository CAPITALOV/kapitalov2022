<?php

/**
 * Класс для действий Superadmin
 *
 */

namespace app\controllers;

use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use app\models\User;
use app\models\AdminUserForm;
use app\controllers\BaseController;
use app\models\Component;
use app\models\Module;
use app\models\Plugin;
use app\models\TopMenu;
use app\models\CronAction;
use app\models\UserMenu;
use app\models\Form\Settings as FormSettings;
use app\models\Form\CronAction as FormCronAction;
use app\models\Form\UserMenu as FormUserMenu;
use app\models\Form\TopMenu as FormTopMenu;
use app\models\Form\Module as FormModule;
use app\models\Form\Component as FormComponent;
use app\models\Form\Plugin as FormPlugin;
use app\models\JsLoggerItem;
use Suffra\Config as SuffraConfig;
use app\service\CheckFiles;

class SuperadminController extends SuperadminBaseController
{
    const MEMCACHE_KEY_ALL_ITEMS = '\cmsCore::getAll/items';
    
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Выводит форму добавления пользователя и добавляет через POST
     */
    public function actionUsers_add()
    {
        $model = new AdminUserForm();
        if ($model->load(Yii::$app->request->post()) && $model->insert()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            self::log('Добавил пользователя административной панели');

            return $this->refresh();
        } else {
            return $this->render('users_add', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Выводит форму редактирования пользователя и обновляет через POST
     */
    public function actionUsers_edit($id)
    {
        $model = AdminUserForm::find($id);
        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            self::log('Отредактировал пользователя административной панели');

            return $this->refresh();
        } else {
            return $this->render('users_edit', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Удалает пользователя админки
     */
    public function actionUsers_delete($id)
    {
        $user = User::findIdentity($id);
        $userId = $user->id;
        $userName = $user->username;
        $user->delete();
        self::log('Удалил пользователя id=' . $userId . ' name=' . $userName);

        return $this->jsonSuccess();
    }

    /**
     * Выводит список компонентов
     */
    public function actionComponents()
    {
        $this->actionComponentsUpdateVersions();

        $folderComponents = $this->actionComponents_getAllFolders();
        $dbComponents = $this->actionComponents_getAllInstallPlugins();
        $newComponents = array_diff($folderComponents, $dbComponents);

        return $this->render('components', [
            'gridViewOptions' => [
                'dataProvider' => new ActiveDataProvider([
                    'query'      => (new Query())->select('id, title, link, author, published, version, system, version_dev')->from('cms_components')->orderBy('id'),
                    'pagination' => [
                        'pageSize' => 50,
                    ],
                ]),
                'columns'      => [
                    'id:text:#',
                    'title:text:Имя',
                    'link:text:Ссылка',
                    'author:text:Автор',
                    'version:text:Версия',
                    'version_dev:text:version_dev',
                    [
                        'label'   => 'Обновить',
                        'content' => function ($model, $key, $index, $column) {
                            if ($model['version_dev']) {
                                if ($model['version'] == $model['version_dev']) {
                                    return '&nbsp;';
                                }
                                else {
                                    return Html::button('Обновить', [
                                        'class' => 'btn btn-primary btn-update',
                                        'data'  => ['id' => $model['id']],
                                    ]);
                                }
                            }
                            else {
                                return '&nbsp;';
                            }
                        }
                    ],
                    'system:text:system',
                    [
                        'label'   => 'конфигурировать',
                        'content' => function ($model, $key, $index, $column) {
                            return Html::button('Конфигурировать', [
                                'class' => 'btn btn-primary btn-config',
                                'data'  => [
                                    'id'     => $model['id'],
                                    'toggle' => 'modal',
                                    'target' => '#myModal',
                                ],
                            ]);
                        }
                    ],
                    [
                        'label'   => 'Публикация',
                        'content' => function ($model, $key, $index, $column) {
                            if ($model['published'] == '1') {
                                return Html::button('снять с публикации', [
                                    'class' => 'btn btn-primary btn-unpublic',
                                    'data'  => ['id' => $model['id'],],
                                ]);
                            }
                            else {
                                return Html::button('опубликовать', [
                                    'class' => 'btn btn-primary btn-public',
                                    'data'  => ['id' => $model['id'],],
                                ]);
                            }
                        }
                    ],
                ],
            ],
            'newItems'        => [
                'dataProvider' => new ArrayDataProvider([
                    'allModels'  => array_map(function ($_) { return ['name' => $_]; }, $newComponents),
                    'pagination' => [
                        'pageSize' => 50,
                    ],
                ]),
                'columns'      => [
                    'name:text:Имя',
                    [
                        'label'   => 'Установить',
                        'content' => function ($model, $key, $index, $column) {
                            return Html::button('Установить', [
                                'class'   => 'btn btn-primary btn-install',
                                'data-id' => $model['name'],
                            ]);
                        }
                    ],
                ],
            ],
        ]);
    }

    /**
     * Выдает все компоненты в папке компонентов
     * @return array ['component1', 'component2', ...]
     */
    private function actionComponents_getAllFolders()
    {
        $basePath = SuffraConfig::getBasePath();
        $all = scandir($basePath . '/components');
        $all = array_diff($all, ['..', '.', 'CVS']);
        $ret = [];
        foreach($all as $item) {
            if (!is_file($basePath . '/components/' . $item)) $ret[] = $item;
        }
        return $ret;
    }

    /**
     * Выдает все установленные компоненты
     * @return array ['component1', 'component2', ...]
     */
    private function actionComponents_getAllInstallPlugins()
    {
        return (new Query())->select('link')->from('cms_components')->column();
    }


    /**
     * Получает и обновляет текущие версии компонентов
     * поле 'version_dev'
     * @return boolean результат выполнения операции
     */
    private function actionComponentsUpdateVersions() {
        $table = 'cms_components';

        // обновляю версии компонентов
        $rows = (new Query())
            ->select('id, link as name')
            ->from($table)
            ->all();
        $versions = FormComponent::getCurrentVersions($rows);

        (new Query())->createCommand()->update($table,['version_dev' => null])->execute();
        $command = (new Query())->createCommand();
        foreach ($versions as $id => $version) {
            $command->update($table, [
                'version_dev' => $version
            ], ['id' => $id])->execute();
        }

        return true;
    }

    /**
     * Устанавливает компонент
     * AJAX
     *
     * REQUEST:
     * - name - string - название компонента `cms_components`.`title`
     */
    public function actionComponents_install()
    {
        $name = \Yii::$app->request->post('name');
        $result = FormComponent::install($name);
        if (!$result['status']) {
            return self::jsonError($result['data']);
        }
        self::clearCache();
        return self::jsonSuccess();
    }

    private static function clearCache()
    {
        Yii::$app->cache->delete(self::MEMCACHE_KEY_ALL_ITEMS);
    }

    /**
     * Выводит страницу очищения кеша
     */
    public function actionClear_cache()
    {
        return $this->render('clear_cache');
    }

    /**
     * Очищает кеш. Удаляет memcache ключ '\cmsCore::getAll/items'
     * AJAX
     */
    public function actionClear_cache_ajax()
    {
        /** @var \yii\caching\MemCache $cache */
        $cache = Yii::$app->cache;
        $cache->getMemcache()->delete('\cmsCore::getAll/items');

        return self::jsonSuccess();
    }

    /**
     * Обновляет компонент
     */
    public function actionComponents_upgrade($id) {
        $form = FormComponent::find($id);

        self::log('Обновил компонет "'. $form->link . '"');

        return $this->jsonController(
            $form->upgrade()
        );
    }

    /**
     * Выводит лог действий пользователя
     *
     */
    public function actionLog() {
        $uid = Yii::$app->request->get('userId');
        $query = (new Query())
            ->select('*')
            ->from('adm_log')->orderBy(['datetime' => SORT_DESC]);
        $columns = [
            'id:text:#',
            'description:text:description',
            [
                'attribute' => 'datetime',
                'format'    => ['date', 'php:Y-m-d H:i:s']
            ]
        ];
        if ($uid) {
            $query->where(['user_id' => $uid]);
        } else {
            $columns[] = 'user_id:text:user_id';
        }

        return $this->render('log', [
                'gridViewOptions' => [
                    'dataProvider' => new ActiveDataProvider([
                        'query'      => $query,
                        'pagination' => [
                            'pageSize' => 50,
                        ],
                    ]),
                    'columns'      => $columns,
                ],
                'users'           => [
                    'dataProvider' => new ActiveDataProvider([
                        'query'      => (new Query())
                            ->select('id,name_first,name_last')
                            ->from('adm_user')->orderBy('id'),
                        'pagination' => [
                            'pageSize' => 50,
                        ],
                    ]),
                    'columns'      => [
                        'id:text:#',
                        'name_first:text:Имя',
                        'name_last:text:Фамилия',
                        [
                            'label'   => 'Показать',
                            'content' => function ($model, $key, $index, $column) {
                                $url = Url::to(['superadmin/log', 'userId' => $model['id']]);

                                return '<a class="btn btn-primary" href="' . $url . '">Показать</a>';
                            }
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Выдает конфиг компонента
     */
    public function actionComponents_config_get($id)
    {
        return $this->jsonSuccess(Component::find($id)->getConfig());
    }

    /**
     * Сохраняет конфиг компонента
     */
    public function actionComponents_config_set($id)
    {
        if (Component::find($id)->setConfig(
            Yii::$app->request->post('config')
        )) {
            self::clearCache();

            return $this->jsonSuccess();
        } else {
            return $this->jsonError('Не удалось обновить конфиг');
        }
    }

    /**
     * Публикует компонент
     */
    public function actionComponents_public($id)
    {
        Component::find($id)->publicate();

        return $this->jsonSuccess();
    }

    /**
     * Снимает с публикации компонент
     */
    public function actionComponents_unpublic($id)
    {
        Component::find($id)->unPublicate();

        return $this->jsonSuccess();
    }

    /**
     * Выводит список модулей
     *
     */
    public function actionModules()
    {
        $this->actionModulesUpdateVersions();

        $folderModules = $this->actionModules_getAllFolders();
        $dbModules = $this->actionModules_getAllInstallPlugins();
        $newModules = array_diff($folderModules, $dbModules);

        return $this->render('modules', [
            'gridViewOptions' => [
                'dataProvider' => new ActiveDataProvider([
                    'query'      => (new Query())->select('*')->from('cms_modules')->orderBy('id')->where(''),
                    'pagination' => [
                        'pageSize' => 100,
                    ],
                ]),
                'columns'      => [
                    'id:text:#',
                    'position:text:position',
                    'name:text:name',
                    'title:text:Имя',
                    'is_external:text:is_external',
                    [
                        'label'   => 'Редактировать',
                        'content' => function ($model, $key, $index, $column) {
                            $url = Url::to([
                                    'modules_edit',
                                    'id' => $model['id']
                                ]);

                            return '<a href="' . $url . '" class="btn btn-primary" >Редактировать</a>';
                        }
                    ],
                    [
                        'label'   => 'конфигурировать',
                        'content' => function ($model, $key, $index, $column) {
                            return '<button type="button" class="btn btn-primary btn-config" data-toggle="modal" data-target="#myModal" data-id="' . $model['id'] . '">конфигурировать</button>';
                        }
                    ],
                    [
                        'label'   => 'Публикация',
                        'content' => function ($model, $key, $index, $column) {
                            if ($model['published'] == '1') {
                                return '<button type="button" class="btn btn-primary btn-unpublic" data-id="' . $model['id'] . '">снять с публикации</button>';
                            }
                            else {
                                return '<button type="button" class="btn btn-primary btn-public" data-id="' . $model['id'] . '">опубликовать</button>';
                            }
                        }
                    ],
                    'version:text:version',
                    'version_dev:text:version_dev',
                    [
                        'label'   => 'Обновить',
                        'content' => function ($model, $key, $index, $column) {
                            if ($model['version_dev']) {
                                if ($model['version'] == $model['version_dev']) {
                                    return '&nbsp;';
                                }
                                else {
                                    return '<button type="button" class="btn btn-primary btn-update" data-id="' . $model['id'] . '">Обновить</button>';
                                }
                            }
                            else {
                                return '&nbsp;';
                            }
                        }
                    ],
                    'showtitle:text:showtitle',
                    'user:text:user',
                    'original:text:original',
                    'css_prefix:text:css_prefix',
                    'cache:text:cache',
                    'cachetime:text:cachetime',
                    'cacheint:text:cacheint',
                    'template:text:template',
                    'is_strict_bind:text:is_strict_bind',
                ],
            ],
            'newItems'        => [
                'dataProvider' => new ArrayDataProvider([
                    'allModels'  => array_map(function ($_) { return ['name' => $_]; }, $newModules),
                    'pagination' => [
                        'pageSize' => 50,
                    ],
                ]),
                'columns'      => [
                    'name:text:Имя',
                    [
                        'label'   => 'Установить',
                        'content' => function ($model, $key, $index, $column) {
                            return Html::button('Установить', [
                                'class'   => 'btn btn-primary btn-install',
                                'data-id' => $model['name'],
                            ]);
                        }
                    ],
                ],
            ],
        ]);
    }

    /**
     * Выдает все модули в папке модлей
     * @return array ['module1','module2', ...]
     */
    private function actionModules_getAllFolders()
    {
        $basePath = SuffraConfig::getBasePath();
        $all = scandir($basePath . '/modules');
        $all = array_diff($all, ['..', '.', 'CVS']);
        $ret = [];
        foreach($all as $item) {
            if (!is_file($basePath . '/modules/' . $item)) $ret[] = $item;
        }
        return $ret;
    }

    /**
     * Выдает все установленные модули
     * @return array ['module1','module2', ...]
     */
    private function actionModules_getAllInstallPlugins()
    {
        return ArrayHelper::getColumn(
            (new Query())->select('content')->from('cms_modules')->where('LEFT(content,4) = "mod_"')->all(),
            'content'
        );
    }

    /**
     * Получает и обновляет текущие версии компонентов
     * поле 'version_dev'
     * @return boolean результат выполнения операции
     */
    private function actionModulesUpdateVersions()
    {
        $table = 'cms_modules';

        // обновляю версии компонентов
        $rows = (new Query())
            ->select('id, content as name')
            ->from($table)
            ->all();
        $versions = FormModule::getCurrentVersions($rows);

        (new Query())->createCommand()->update($table,['version_dev' => null])->execute();
        $command = (new Query())->createCommand();
        foreach ($versions as $id => $version) {
            $command->update($table, [
                'version_dev' => $version
            ], ['id' => $id])->execute();
        }

        return true;
    }


    /**
     * Инсталлирует модуль
     * AJAX
     *
     */
    public function actionModules_install()
    {
        $name = \Yii::$app->request->post('name');
        $result = FormModule::install($name);
        if (!$result['status']) {
            return self::jsonError($result['data']);
        }
        self::clearCache();
        return self::jsonSuccess();
    }

    /**
     * Выводит форму редактирования пользователя и обновляет через POST
     */
    public function actionModules_edit($id)
    {
        $model = FormModule::find($id);
        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('modules_edit', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Выводит форму редактирования пользователя и обновляет через POST
     */
    public function actionModules_extra_position($id)
    {
        $model = new \app\models\Form\ModuleBind();
        $model->module_id = $id;
        if ($model->load(Yii::$app->request->post()) && $model->insert($id)) {
            self::log('Добавил extra_position '. "\r\n" . VarDumper::dumpAsString([
                    'position'  => $model->position,
                    'module_id' => $model->module_id,
                    'menu_id'   => $model->menu_id,
                ]));
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('modules_extra_position', [
                'model' => $model,
                'rows'  => (new Query())->select('*')->from('cms_modules_bind')->where([
                    'module_id' => $id
                ])->all(),
            ]);
        }
    }

    public function actionModules_extra_position_delete($id)
    {
        $id = Yii::$app->request->post('id');
        \app\models\Form\ModuleBind::delete($id);
        self::log('Удалил extra_position ID='. $id);

        return self::jsonSuccess();
    }

    public function actionModules_extra_position_update($id)
    {
        \app\models\Form\ModuleBind::update([
            'menu_id' => Yii::$app->request->post('menu_id'),
            'position' => Yii::$app->request->post('position'),
        ], Yii::$app->request->post('id'));
        self::log('Обновил extra_position ID='. Yii::$app->request->post('id'));

        return self::jsonSuccess();
    }

    /**
     * Выдает конфиг модуля
     */
    public function actionModules_config_get($id) {
        return $this->jsonSuccess(
            Module::find($id)->getConfig()
        );
    }

    /**
     * Сохраняет конфиг модуля
     */
    public function actionModules_config_set($id)
    {
        if (Module::find($id)->setConfig(
            Yii::$app->request->post('config')
        )) {
            return $this->jsonSuccess();
        } else {
            return $this->jsonError('Не удалось обновить конфиг');
        }
    }

    /**
     * Публикует модуль
     */
    public function actionModules_public($id)
    {
        Module::find($id)->publicate();
        return $this->jsonSuccess();
    }

    /**
     * Снимает с публикации модуль
     */
    public function actionModules_unpublic($id)
    {
        Module::find($id)->unPublicate();
        return $this->jsonSuccess();
    }

    /**
     * Обновляет модуль
     */
    public function actionModules_upgrade($id)
    {
        return $this->jsonController(
            FormModule::find($id)->upgrade()
        );
    }

    /**
     * Выводит элеименты верхнего меню для управления
     */
    public function actionTop_menu()
    {
        return $this->render('top_menu', [
                'gridViewOptions' => [
                    'dataProvider' => new ActiveDataProvider([
                        'query'      => (new Query())
                            ->select('*')
                            ->from('cms_menu')
                            ->orderBy('ordering')
                            ->where(''),
                        'pagination' => [
                            'pageSize' => 50,
                        ],
                    ]),
                    'columns'      => [
                        'id:text:#',
                        'menu:text:menu',
                        'title:text:title',
                        'link:text:link',
                        'linktype:text:linktype',
                        'linkid:text:linkid',
                        'target:text:target',
                        [
                            'label'   => 'Редактировать',
                            'content' => function ($model, $key, $index, $column) {
                                $url = Url::to(['superadmin/top_menu_edit', 'id' => $model['id']]);

                                return '<a href="' . $url . '" class="btn btn-primary" >Редактировать</a>';
                            }
                        ],
                        [
                            'label'   => 'Удалить',
                            'content' => function ($model, $key, $index, $column) {
                                return '<button type="button" class="btn btn-primary btn-topMenuDelete" data-id="' . $model['id'] . '">Удалить</button>';
                            }
                        ],
                        [
                            'label'   => 'Публикация',
                            'content' => function ($model, $key, $index, $column) {
                                if ($model['published'] == '1') {
                                    return '<button type="button" class="btn btn-primary btn-unpublic" data-id="' . $model['id'] . '">снять с публикации</button>';
                                } else {
                                    return '<button type="button" class="btn btn-primary btn-public" data-id="' . $model['id'] . '">опубликовать</button>';
                                }
                            }
                        ],                        
                        'access_list:text:access_list',                        
						'html_id:text:html_id',
                    ],
                ]
            ]
        );
    }

    /**
     * Публикует элемент меню
     */
    public function actionTop_menu_public($id)
    {
        TopMenu::find($id)->publicate();
        Yii::$app->cache->delete(self::MEMCACHE_KEY_ALL_ITEMS);
        return $this->jsonSuccess();
    }

    /**
     * Снимает с публикации элемент меню
     */
    public function actionTop_menu_unpublic($id)
    {
        TopMenu::find($id)->unPublicate();
        Yii::$app->cache->delete(self::MEMCACHE_KEY_ALL_ITEMS);
        return $this->jsonSuccess();
    }

    /**
     * Удаляет элемент меню
     */
    public function actionTop_menu_delete($id)
    {
        TopMenu::find($id)->delete();
        Yii::$app->cache->delete(self::MEMCACHE_KEY_ALL_ITEMS);
        return $this->jsonSuccess();
    }

    /*
     * Обновляет сортировку
     * post['ids'] = [1,2,3, ...] массив идентификаторов строк по возрастанию сортировки
     */
    public function actionTop_menu_resort()
    {
        $ids = Yii::$app->request->post('ids');
        TopMenu::resort($ids);
        Yii::$app->cache->delete(self::MEMCACHE_KEY_ALL_ITEMS);
        return $this->jsonSuccess();
    }

    /**
     * Выводит форму и добавляет значения
     */
    public function actionTop_menu_add()
    {
        $model = new FormTopMenu();
        if ($model->load(Yii::$app->request->post()) && $model->insert()) {
            Yii::$app->cache->delete(self::MEMCACHE_KEY_ALL_ITEMS);
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('top_menu_add', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Выводит форму редактирования меню пользователя и обновляет через POST
     */
    public function actionTop_menu_edit($id)
    {
        $model = FormTopMenu::find($id);
        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->cache->delete(self::MEMCACHE_KEY_ALL_ITEMS);
            return $this->redirect(Url::to(['superadmin/top_menu']));
        } else {
            return $this->render('top_menu_edit', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Выводит элеименты меню пользователя для управления
     */
    public function actionUser_menu()
    {
        return $this->render('user_menu', [
                'gridViewOptions' => [
                    'dataProvider' => new ActiveDataProvider([
                        'query'      => (new Query())
                            ->select('*')
                            ->from('cms_user_menu')
                            ->orderBy('category,ord'),
                        'pagination' => [
                            'pageSize' => 50,
                        ],
                    ]),
                    'columns'      => [
                        'id:text:#',
                        'category:text:category',
                        'url:text:url',
                        'a_id:text:a_id',
                        'title:text:title',
                        'img:text:img',
                        [
                            'label'   => 'Редактировать',
                            'content' => function ($model, $key, $index, $column) {
                                $url = Url::to(['superadmin/user_menu_edit', 'id' => $model['id']]);

                                return '<a href="' . $url . '" class="btn btn-primary" >Редактировать</a>';
                            }
                        ],
                        [
                            'label'   => 'Удалить',
                            'content' => function ($model, $key, $index, $column) {
                                return '<button type="button" class="btn btn-primary btn-userMenuDelete" data-id="' . $model['id'] . '">Удалить</button>';
                            }
                        ],
                        'disable:text:disable',
                    ],
                ]
            ]
        );
    }

    /**
     * Удаляет запись из меню пользователя
     */
    public function actionUser_menu_delete($id)
    {
        return self::jsonSuccess(
            UserMenu::find($id)->delete()
        );
    }

    /**
     * Выводит форму и добавляет значения
     */
    public function actionUser_menu_add()
    {
        $model = new FormUserMenu();
        if ($model->load(Yii::$app->request->post()) && $model->insert()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('users_menu_add', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Выводит форму редактирования меню пользователя и обновляет через POST
     */
    public function actionUser_menu_edit($id)
    {
        $model = FormUserMenu::find($id);
        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            return $this->redirect(Url::to(['superadmin/user_menu']));
        } else {
            return $this->render('users_menu_edit', [
                'model' => $model,
            ]);
        }
    }

    /*
     * Обновляет сортировку
     * post['ids'] = [1,2,3, ...] массив идентификаторов строк по возрастанию сортировки
     */
    public function actionUser_menu_resort()
    {
        $ids = Yii::$app->request->post('ids');
        UserMenu::resort($ids);
        return $this->jsonSuccess();
    }

    /**
     * Выводит лог JS
     */
    public function actionJs_logger()
    {
        return $this->render('js_logger', [
                'gridViewOptions' => [
                    'dataProvider' => new ActiveDataProvider([
                        'query'      => (new Query())
                            ->select('*')
                            ->from('log_javascript')->orderBy('id'),
                        'pagination' => [
                            'pageSize' => 50,
                        ],
                    ]),
                    'columns'      => [
                        'id:text:#',
                        'user_id:text:user_id',
                        'message:text:message',
                        'url:text:url',
                        'line:text:line',
                        [
                            'label'   => 'удалить',
                            'content' => function ($model, $key, $index, $column) {
                                return '<button type="button" class="btn btn-primary jsLoggerDelete"  data-id="'.$model['id'].'">удалить</button>';
                            }
                        ],
                        'HTTP_USER_AGENT:text:HTTP_USER_AGENT',
                        'HTTP_REFERER:text:HTTP_REFERER',
                        'REMOTE_ADDR:text:REMOTE_ADDR',
                        'dt:text:Дата',
                    ],
                ]
            ]
        );
    }

    /**
     * Удаляет запись из js logger таблицы
     */
    public function actionJs_logger_delete($id)
    {
        return self::jsonSuccess(
            JsLoggerItem::find($id)->delete()
        );
    }

    /**
     * Удаляет все записи из js logger таблицы
     */
    public function actionJs_logger_delete_all($id)
    {
        return self::jsonSuccess(
            JsLoggerItem::deleteAll()
        );
    }

    /**
     * Выводит список плагинов
     *
     */
    public function actionPlugins()
    {
        $this->actionPluginsUpdateVersions();

        $folderPlugins = $this->actionPlugins_getAllFolders();
        $dbPlugins = $this->actionPlugins_getAllInstallPlugins();
        $newPlugins = array_diff($folderPlugins, $dbPlugins);
        
        return $this->render('plugins', [
                'gridViewOptions' => [
                    'dataProvider' => new ActiveDataProvider([
                        'query'      => (new Query())
                            ->select('*')
                            ->from('cms_plugins')->orderBy('id')
                            ->where(''),
                        'pagination' => [
                            'pageSize' => 50,
                        ],
                    ]),
                    'columns'      => [
                        'id:text:#',
                        'plugin:text:Имя',
                        'title:text:Имя',
                        'author:text:Автор',
                        'version:text:Версия',
                        'version_dev:text:version_dev',
                        [
                            'label'   => 'Обновить',
                            'content' => function ($model, $key, $index, $column) {
                                if ($model['version_dev']) {
                                    if ($model['version'] == $model['version_dev']) {
                                        return '&nbsp;';
                                    } else {
                                        return '<button type="button" class="btn btn-primary btn-update" data-id="' . $model['id'] . '">Обновить</button>';
                                    }
                                } else {
                                    return '&nbsp;';
                                }
                            }
                        ],
                        'plugin_type:text:Тип',
                        [
                            'label'   => 'конфигурировать',
                            'content' => function ($model, $key, $index, $column) {
                                return '<button type="button" class="btn btn-primary btn-config" data-toggle="modal" data-target="#myModal" data-id="' . $model['id'] . '">конфигурировать</button>';
                            }
                        ],
                        [
                            'label'   => 'Публикация',
                            'content' => function ($model, $key, $index, $column) {
                                if ($model['published'] == '1') {
                                    return '<button type="button" class="btn btn-primary btn-unpublic" data-id="' . $model['id'] . '">снять с публикации</button>';
                                } else {
                                    return '<button type="button" class="btn btn-primary btn-public" data-id="' . $model['id'] . '">опубликовать</button>';
                                }
                            }
                        ],
                    ],
                ],
                'newItems'        => [
                    'dataProvider' => new ArrayDataProvider([
                        'allModels'  => array_map(function($_){return ['name' => $_];},$newPlugins),
                        'pagination' => [
                            'pageSize' => 50,
                        ],
                    ]),
                    'columns'      => [
                        'name:text:Имя',
                        [
                            'label'   => 'Установить',
                            'content' => function ($model, $key, $index, $column) {
                                return Html::button('Установить', [
                                    'class'   => 'btn btn-primary btn-install',
                                    'data-id' => $model['name'],
                                ]);
                            }
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Выдает все плагины в папке плагинов
     * @return array ['plugin1','plugin2', ...]
     */
    private function actionPlugins_getAllFolders()
    {
        $basePath = SuffraConfig::getBasePath();
        $all = scandir($basePath . '/plugins');
        return array_diff($all, ['..', '.', 'CVS']);
    }

    /**
     * Выдает все установленные плагины
     * @return array ['plugin1','plugin2', ...]
     */
    private function actionPlugins_getAllInstallPlugins()
    {
        return ArrayHelper::getColumn(
            (new Query())->select('plugin')->from('cms_plugins')->all(),
            'plugin'
        );
    }

    /**
     * Получает и обновляет текущие версии плагонов
     * поле 'version_dev'
     * @return boolean результат выполнения операции
     */
    private function actionPluginsUpdateVersions()
    {
        $table = 'cms_plugins';

        // обновляю версии компонентов
        $rows = (new Query())
            ->select('id, plugin as name')
            ->from($table)
            ->all();
        $versions = FormPlugin::getCurrentVersions($rows);

        (new Query())->createCommand()->update($table,['version_dev' => null])->execute();
        $command = (new Query())->createCommand();
        foreach ($versions as $id => $version) {
            $command->update($table, [
                'version_dev' => $version
            ], ['id' => $id])->execute();
        }

        return true;
    }

    /**
     * AJAX
     * Устанавливает плагин
     * Request
     * post name - string название плагина
     */
    public function actionPlugins_install()
    {
        $name = \Yii::$app->request->post('name');
        $result = FormPlugin::install($name);
        if (!$result['status']) {
            return self::jsonError($result['data']);
        }
        self::clearCache();
        return self::jsonSuccess();
    }

    /**
     * Выдает конфиг плагина
     */
    public function actionPlugins_config_get($id)
    {
        return $this->jsonSuccess(
            Plugin::find($id)->getConfig()
        );
    }

    /**
     * Сохраняет конфиг плагина
     */
    public function actionPlugins_config_set($id)
    {
        if (Plugin::find($id)->setConfig(
            Yii::$app->request->post('config')
        )) {
            return $this->jsonSuccess();
        } else {
            return $this->jsonError('Не удалось обновить конфиг');
        }
    }

    /*
     * Публикует плагин
     */
    public function actionPlugins_public($id) {
        Plugin::find($id)->publicate();
        return $this->jsonSuccess();
    }

    /*
     * Снимает с публикации плагин
     */
    public function actionPlugins_unpublic($id) {
        Plugin::find($id)->unPublicate();
        return $this->jsonSuccess();
    }

    /**
     * Обновляет плагин
     */
    public function actionPlugins_upgrade($id) {
        return $this->jsonController(
            FormPlugin::find($id)->upgrade()
        );
    }

    /**
     * Выводит таблицу админов
     */
    public function actionUsers() {
        return $this->render('users', [
                'gridViewOptions' => [
                    'dataProvider' => new ActiveDataProvider([
                        'query'      => (new Query())
                            ->select('*')
                            ->from('adm_user')->orderBy('id')
                            ->where(''),
                        'pagination' => [
                            'pageSize' => 50,
                        ],
                    ]),
                    'columns'      => [
                        'id:text:#',
                        'email:text:email',
                        'name_first:text:Имя',
                        'name_last:text:Фамилия',
                        [
                            'label'   => 'редактировать',
                            'content' => function ($model, $key, $index, $column) {
                                return '<a class="btn btn-primary" href="' . Url::to(['superadmin/users_edit', 'id' => $model['id']]) . '">редактировать</a>';
                            }
                        ],
                        [
                            'label'   => 'удалить',
                            'content' => function ($model, $key, $index, $column) {
                                return '<button type="button" class="btn btn-primary moderatorsDelete" data-id="'.$model['id'].'">удалить</button>';
                            }
                        ],
                    ],
                ]
            ]
        );
    }

    /**
     * Выводит список задач крона
     */
    public function actionCron() {
        return $this->render('cron', [
                'gridViewOptions' => [
                    'dataProvider' => new ActiveDataProvider([
                        'query'      => (new Query())
                            ->select('*')
                            ->from('cms_cron_jobs')->orderBy('id'),
                        'pagination' => [
                            'pageSize' => 50,
                        ],
                    ]),
                    'columns'      => [
                        'id:text:#',
                        'comment:text:Название',
                        'job_interval:text:Период исполнения (ч)',
                        [
                            'label' => 'Активен?',
                            'content' => function($_){
                                return Html::tag('span',(new \yii\i18n\Formatter())->asBoolean($_['is_enabled']), ['class' => 'label label-' . ($_['is_enabled'] ? 'success' : 'default')]);
                            }
                        ],
                        [
                            'attribute' => 'job_run_date',
                            'format'    => ['date', 'php:Y-m-d H:i:s'],
                            'label' => 'Последний запуск',
                        ],
                        [
                            'label'   => 'редактировать',
                            'content' => function ($model, $key, $index, $column) {
                                return '<a class="btn btn-primary" href="' . Url::to(['superadmin/cron_edit', 'id' => $model['id']]) . '">редактировать</a>';
                            }
                        ],
                        [
                            'label'   => 'удалить',
                            'content' => function ($model, $key, $index, $column) {
                                return '<button type="button" class="btn btn-primary buttonDelete" data-id="'.$model['id'].'">удалить</button>';
                            }
                        ],
                        [
                            'label'   => 'Запустить',
                            'content' => function ($model, $key, $index, $column) {
                                return '<button type="button" class="btn btn-primary " data-toggle="modal" data-target="#myModal" data-id="'.$model['id'].'">Запустить</button>';
                            }
                        ],
                    ],
                ]
            ]
        );
    }

    /**
     * Выводит форму и добавляет значения
     */
    public function actionCron_add() {
        $model = new FormCronAction();
        if ($model->load(Yii::$app->request->post()) && $model->insert()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('cron_add', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Выводит форму редактирования задачи крон и обновляет через POST
     * @param integer $id идентификатор задачи крон
     * @return \yii\web\Response|string
     */
    public function actionCron_edit($id)
    {
        $model = FormCronAction::find($id);
        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('cron_edit', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Удаляет запись из задач крон
     * @param integer $id идентификатор задачи крон
     * @return string JSON
     */
    public function actionCron_delete($id) {
        return self::jsonSuccess(
            CronAction::find($id)->delete()
        );
    }

    /**
     * Удаляет запись из задач крон
     * @param integer $id идентификатор задачи крон
     * @return string JSON
     */
    public function actionCron_execute($id) {
        return self::jsonSuccess(
            CronAction::find($id)->execute()
        );
    }

    /**
     * Выводит форму редактирования настроек сайта
     * @return \yii\web\Response|string
     */
    public function actionSettings()
    {
        $model = new FormSettings();
        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            $model->init();
            return $this->render('settings', [
                'model' => $model,
            ]);
        }
    }
	
	public function actionCheck_db() {
		$dData = CheckFiles::getData();
		$config = CheckFiles::config();
		$page = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;
		$perPage = $config['perPage'];
		$sql = "CREATE TABLE IF NOT EXISTS `records_for_delete` (`id` INT NOT NULL AUTO_INCREMENT, `added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, `deleted` TIMESTAMP NULL DEFAULT NULL, `table_name` VARCHAR(63) NOT NULL DEFAULT '', `record_id` INT NOT NULL DEFAULT 0, `hash_field` VARCHAR(15) NOT NULL DEFAULT '', `hash_value` VARCHAR(127) NOT NULL DEFAULT '', `file_path` VARCHAR(255) NOT NULL DEFAULT '', `reason` TEXT, PRIMARY KEY(`id`)) DEFAULT CHARACTER SET = utf8 DEFAULT COLLATE = utf8_general_ci";
		(new Query)->createCommand()->setSql($sql)->execute();
		$total = (int) (new Query)->select('COUNT(*) AS `cnt`')->from('records_for_delete')->where('`deleted` IS NULL')->scalar();
		$pages = ceil($total / $perPage);
		if($page > $pages) {
			$page = $pages;
		}
		if($page < 1) {
			$page = 1;
		}
		$start = (int) (($page - 1) * $perPage);
		$records = (new Query)->select('*')->from('records_for_delete')->where('`deleted` IS NULL')->orderBy('id')->limit($perPage)->offset($start)->all();
		$pager = CheckFiles::getPagesLinks($page, $pages);
		return $this->render(
			'check_db',
			[
				'dData' => $dData,
				'records' => $records,
				'pager' => $pager,
				'queryLimit' => $config['queryLimit']
			]
		);
	}
	
	public function actionRecheck_records($type) {
		$dData = CheckFiles::getData();
		foreach($dData as $key => $val) {
			if($key === $type) {
				$dData[$key] = 0;
			}
		}
		CheckFiles::saveData($dData);
		$this->redirect('/check_files/db');
	}
	
	public function actionRecheck_all_records() {
		$dData = CheckFiles::getData();
		foreach($dData as $key => $val) {
			$dData[$key] = 0;
		}
		CheckFiles::saveData($dData);
		$this->redirect('/check_files/db');
	}
	
	public function actionFind_records($qty) {
		$queryLimit = (int) $qty;
		return CheckFiles::CheckFiles($queryLimit);
	}
	
	public function actionDel_all_db_rows() {
		$rowsData = ArrayHelper::index((new Query)->select('*')->from('records_for_delete')->where('`deleted` IS NULL')->orderBy('id')->all(), 'id');
		if($rowsData) {
			$moveToTemp = [];
			foreach($rowsData as $rowData) {
				if(!isset($moveToTemp[$rowData['table_name']])) {
					$moveToTemp[$rowData['table_name']] = ['ids' => [], 'records_ids' => []];
				}
				$moveToTemp[$rowData['table_name']]['ids'][] = (int) $rowData['id'];
				$moveToTemp[$rowData['table_name']]['records_ids'][] = (int) $rowData['record_id'];
			}
			if($moveToTemp) {
				$funcNames = CheckFiles::funcNames();
				foreach($moveToTemp as $tableName => $tableIds) {
					$func = (isset($funcNames[$tableName]) and method_exists('\app\service\CheckFiles', $funcNames[$tableName])) ? $funcNames[$tableName] : '';
					if($func) {
						$moveToTempRows = ArrayHelper::index((new Query)->select('*')->from($tableName)->where('`id` IN (' . implode(', ', $tableIds['records_ids']) . ')')->all(), 'id');
						CheckFiles::$func($moveToTempRows);
						(new Query)->createCommand()->setSql("UPDATE `records_for_delete` SET `deleted` = NOW() WHERE `id` IN (" . implode(', ', $tableIds['ids']) . ")")->execute();
					}
				}
			}
		}
		$this->redirect('/check_files/db');
	}
	
	public function actionDel_all_founded_records() {
		(new Query)->createCommand()->delete('records_for_delete', '`deleted` IS NULL')->execute();
		$this->redirect('/check_files/db');
	}
	
	public function actionDel_db_row($id) {
		$id = (int) $id;
		if($id > 0) {
			$rowData = (new Query)->select('*')->from('records_for_delete')->where("`id` = {$id}")->one();
			if($rowData) {
				$funcNames = CheckFiles::funcNames();
				$func = (isset($funcNames[$rowData['table_name']]) and method_exists('\app\service\CheckFiles', $funcNames[$rowData['table_name']])) ? $funcNames[$rowData['table_name']] : '';
				$rowId = (int) $rowData['record_id'];
				$moveToTempRow = ArrayHelper::index((new Query)->select('*')->from($rowData['table_name'])->where("`id` = {$rowId}")->all(), 'id');
				if($func) {
					CheckFiles::$func($moveToTempRow);
					(new Query)->createCommand()->setSql("UPDATE `records_for_delete` SET `deleted` = NOW() WHERE `id` = {$id}")->execute();
				}
			}
		}
		$this->redirect('/check_files/db');
	}
	
	public function actionDel_founded_row($id) {
		$id = (int) $id;
		if($id > 0) {
			(new Query)->createCommand()->delete('records_for_delete', "`id` = {$id} AND `deleted` IS NULL")->execute();
		}
		$this->redirect('/check_files/db');
	}
	
	public function actionDel_db_rows() {
		$ids = isset($_REQUEST['records_ids']) ? $_REQUEST['records_ids'] : [];
		if($ids) {
			foreach($ids as $key => $val) {
				$ids[$key] = (int) $val;
			}
			$rowsData = ArrayHelper::index((new Query)->select('*')->from('records_for_delete')->where('`id` IN (' . implode(', ', $ids) . ')')->orderBy('id')->all(), 'id');
			$moveToTemp = [];
			foreach($rowsData as $id => $rowData) {
				if(!isset($moveToTemp[$rowData['table_name']])) {
					$moveToTemp[$rowData['table_name']] = ['ids' => [], 'records_ids' => []];
				}
				$moveToTemp[$rowData['table_name']]['ids'][] = (int) $rowData['id'];
				$moveToTemp[$rowData['table_name']]['records_ids'][] = (int) $rowData['record_id'];
			}
			if($moveToTemp) {
				$funcNames = CheckFiles::funcNames();
				foreach($moveToTemp as $tableName => $tableIds) {
					$func = (isset($funcNames[$tableName]) and method_exists('\app\service\CheckFiles', $funcNames[$tableName])) ? $funcNames[$tableName] : '';
					if($func) {
						$moveToTempRows = ArrayHelper::index((new Query)->select('*')->from($tableName)->where('`id` IN (' . implode(', ', $tableIds['records_ids']) . ')')->all(), 'id');
						CheckFiles::$func($moveToTempRows);
						(new Query)->createCommand()->setSql("UPDATE `records_for_delete` SET `deleted` = NOW() WHERE `id` IN (" . implode(', ', $tableIds['ids']) . ")")->execute();
					}
				}
			}
		}
		$this->redirect('/check_files/db');
	}
	
	public function actionDel_founded_rows() {
		$ids = isset($_REQUEST['records_ids']) ? $_REQUEST['records_ids'] : [];
		if($ids) {
			foreach($ids as $key => $val) {
				$ids[$key] = (int) $val;
			}
			(new Query)->createCommand()->delete('records_for_delete', '`id` IN (' . implode(', ', $ids) . ') AND `deleted` IS NULL')->execute();
		}
		$this->redirect('/check_files/db');
	}
	
	public function actionCheck_files() {
		$fData = CheckFiles::getDelData();
		$config = CheckFiles::config();
		$page = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;
		$perPage = $config['perPage'];
		$sql = "CREATE TABLE IF NOT EXISTS `files_for_delete` (`id` INT NOT NULL AUTO_INCREMENT, `added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, `deleted` TIMESTAMP NULL DEFAULT NULL, `file_type` VARCHAR(15) NOT NULL DEFAULT '', `file_path` VARCHAR(255) NOT NULL DEFAULT '', `reason` TEXT, PRIMARY KEY(`id`)) DEFAULT CHARACTER SET = utf8 DEFAULT COLLATE = utf8_general_ci";
		(new Query)->createCommand()->setSql($sql)->execute();
		$total = (int) (new Query)->select('COUNT(*)')->from('files_for_delete')->where('`deleted` IS NULL')->scalar();
		$pages = ceil($total / $perPage);
		if($page > $pages) {
			$pages = $page;
		}
		if($page < 1) {
			$page = 1;
		}
		$start = (int) (($page - 1) * $perPage);
		$files = (new Query)->select('*')->from('files_for_delete')->where('`deleted` IS NULL')->orderBy('id')->limit($perPage)->offset($start)->all();
		$usersPath = $config['suffraPath'] . '/upload/users';
		$dirs = array_slice(scandir($usersPath), 2);
		$unchecked = [];
		foreach($dirs as $dir) {
			if(!in_array($dir, $fData['checked'])) {
				$unchecked[] = $dir;
			}
		}
		$pager = CheckFiles::getPagesLinks($page, $pages);
		return $this->render(
			'check_files',
			[
				'checked' => $fData['checked'],
				'queryLimitDel' => $config['queryLimitDel'],
				'unchecked' => $unchecked,
				'pager' => $pager,
				'files' => $files
			]
		);
	}
	
	public function actionRecheck_folder($num) {
		$data = CheckFiles::getDelData();
		foreach($data['checked'] as $key => $val) {
			if($val === $num) {
				unset($data['checked'][$key]);
				break;
			}
		}
		CheckFiles::saveDelData($data);
		$this->redirect('/check_files/files');
	}
	
	public function actionRecheck_all_folders() {
		CheckFiles::saveDelData(['checked' => []]);
		$this->redirect('/check_files/files');
	}
	
	public function actionFind_files($qty) {
		$queryLimit = (int) $qty;
		return CheckFiles::DelFiles($queryLimit);
	}
	
	public function actionDel_all_files() {
		$files = (new Query)->select('*')->from('files_for_delete')->all();
		$recordsIds = [];
		foreach($files as $file) {
			$recordsIds[] = (int) $file['id'];
			unlink($file['file_path']);
		}
		$sql = "UPDATE `files_for_delete` SET `deleted` = NOW() WHERE `id` IN (" . implode(', ', $recordsIds) . ")";
		(new Query)->createCommand()->setSql($sql)->execute();
		$this->redirect('/check_files/files');
	}
	
	public function actionDel_all_records() {
		(new Query)->createCommand()->delete('files_for_delete', '`deleted` IS NULL')->execute();
		$this->redirect('/check_files/files');
	}
	
	public function actionDel_file($rowId) {
		$id = (int) $rowId;
		if($id > 0 and ($file = (new Query)->select('*')->from('files_for_delete')->where("`id` = {$id}")->one())) {
			unlink($file['file_path']);
			(new Query)->createCommand()->update('files_for_delete', ['deleted' => new \yii\db\Expression('NOW()')], "`id` = {$id}")->execute();
		}
		$this->redirect('/check_files/files');
	}
	
	public function actionDel_record($rowId) {
		$id = (int) $rowId;
		if($id > 0) {
			(new Query)->createCommand()->delete('files_for_delete', "`id` = {$id} AND `deleted` IS NULL")->execute();
		}
		$this->redirect('/check_files/files');
	}
	
	public function actionDel_files() {
		$ids = (isset($_REQUEST['files_ids']) and is_array($_REQUEST['files_ids'])) ? $_REQUEST['files_ids'] : false;
		if($ids) {
			foreach($ids as $key => $val) {
				$ids[$key] = (int) $val;
			}
			$files = ArrayHelper::index((new Query)->select('*')->from('files_for_delete')->where('`id` IN (' . implode(', ', $ids) . ')')->all(), 'id');
			foreach($files as $file) {
				unlink($file['file_path']);
			}
			(new Query)->createCommand()->update('files_for_delete', ['deleted' => new \yii\db\Expression('NOW()')], '`id` IN (' . implode(', ', array_keys($files)) . ')')->execute();
		}
		$this->redirect('/check_files/files');
	}
	
	public function actionDel_records() {
		$ids = (isset($_REQUEST['files_ids']) and is_array($_REQUEST['files_ids'])) ? $_REQUEST['files_ids'] : false;
		if($ids) {
			foreach($ids as $key => $val) {
				$ids[$key] = (int) $val;
			}
			(new Query)->createCommand()->delete('files_for_delete', '`id` IN (' . implode(', ', $ids) . ') AND `deleted` IS NULL')->execute();
		}
		$this->redirect('/check_files/files');
	}
}
