<?php

/**
 * Класс для действий Admin
 *
 */

namespace app\controllers;

use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use app\controllers\BaseController;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\data\ActiveDataProvider;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\UserForm;
use app\models\User;
use app\models\Gifts;
use app\models\Form\GiftsForm;
use yii\helpers\Url;
use app\models\File;
use app\models\NewsRssItem;
use app\models\Voting;
use app\models\VideoFile;
use app\models\UserSuffra;
use app\models\Form\NewsRssItem as FormNewsRssItem;

class AdminController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function($rule, $action) {
                            if (!Yii::$app->user->isGuest) {
                                return Yii::$app->user->identity->hasRole(User::ROLE_ADMIN);
                            }

                            return false;
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
	
	 public function actionGifts()
    {
        return $this->render('gifts', [
                'gridViewOptions' => [
                    'dataProvider' => new ActiveDataProvider([
                        'query'      => (new Query())
                            ->select('*')
                            ->from('cms_gifts')
                            ->orderBy('id')
                            ->where(''),
                        'pagination' => [
                            'pageSize' => 20,
                        ],
                    ]),
                    'columns'      => [
                        'id:text:#',                        
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

    public function actionUsers()
    {
        return $this->render('users', [
                'gridViewOptions' => [
                    'dataProvider' => new ActiveDataProvider([
                        'query'      => (new Query())
                            ->select('id, nickname, email, regdate, is_locked')
                            ->from('cms_users')->orderBy('id')
                            ->where(''),
                        'pagination' => [
                            'pageSize' => 20,
                            'pageParam' =>  'users-page',
                        ],
                    ]),
                    'columns'      => [
                        'id:text:#',
                        'email:text:Почта',
                        'nickname:text:Ник',
                        [
                            'label' => 'Заблокирован?',
                            'content' => function($_){
                                return \yii\helpers\Html::tag('span',(new \yii\i18n\Formatter())->asBoolean($_['is_locked']), ['class' => 'label label-' . ($_['is_locked'] ? 'success' : 'default')]);
                            }
                        ],
                        [
                            'attribute' => 'regdate',
                            'format'    => ['date', 'php:Y-m-d'],
                            'label'     => 'Дата регистрации',
                        ],
                        [
                            'label'   => 'Объекты',
                            'content' => function ($model, $key, $index, $column) {
                                $url = Url::to(['admin/objects', 'id' => $model['id']]);
                                return '<a class="btn btn-primary" href="' . $url . '">объекты</a>';
                            }
                        ],
                        [
                            'label'   => 'заблокировать',
                            'content' => function ($model, $key, $index, $column) {
                                if ($model['is_locked'] == 0) {
                                    return '<button type="button" class="btn btn-primary usersBlock" data-id="'.$model['id'].'">заблокировать</button>';
                                } else {
                                    return '<button type="button" class="btn btn-primary usersUnBlock" data-id="'.$model['id'].'">разблокировать</button>';
                                }
                            }
                        ],
                    ],
                ]
            ]
        );
    }

    public function actionUsers_edit($id)
    {
        $model = new UserForm();
        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('edit', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Блокирует пользователя
     */
    public function actionUsers_block($id)
    {
        UserSuffra::find($id)->block();
        return $this->jsonSuccess();
    }

    /**
     * Разблокирует пользователя
     */
    public function actionUsers_unblock($id)
    {
        UserSuffra::find($id)->unblock();
        return $this->jsonSuccess();
    }

    /**
     * Удаляет файл
     */
    public function actionObjects_files_delete($id) {
        File::find($id)->delete();
        return $this->jsonSuccess();
    }

    public function actionObjects($id)
    {
        $query = new Query();

        return $this->render('objects', [
            'id'        => $id,
            'fileList' => [
                'dataProvider' => new ActiveDataProvider([
                    'query'      => (new Query())
                        ->select('id, filename, moderation_status')->from('cms_user_files')->where(['user_id' => $id])->orderBy('filename'),
                    'pagination' => [
                        'pageSize' => 10,
                        'pageParam' =>  'fileList-page',
                    ],
                ]),
                'columns'      => [
                    'id:text:#',
                    'filename:text:Имя файла',
                    'moderation_status:text:moderation_status',
                    [
                        'label'   => 'удалить',
                        'content' => function ($model, $key, $index, $column) {
                            return '<button type="button" class="btn btn-primary fileListDelete" data-id="'.$model['id'].'">удалить</button>';
                        }
                    ],
//                    [
//                        'label'   => 'заблокировать',
//                        'content' => function ($model, $key, $index, $column) {
//                            return '<button type="button" class="btn btn-primary fileListBlock" data-id="' . $model['id'] . '">заблокировать</button>';
//                        }
//                    ],
                ],
            ],

            'videoList' => [
                'dataProvider' => new ActiveDataProvider([
                    'query'      => (new Query())
                        ->select('id, video_url, video_img')->from('cms_user_video')->where(['user_id' => $id]),
                    'pagination' => [
                        'pageSize' => 10,
                        'pageParam' =>  'videoList-page',
                    ],
                ]),
                'columns'      => [
                    'id:text:#',
                    'video_url:text:url',
                    'video_img:text:video_img',
                    [
                        'label'   => 'удалить',
                        'content' => function ($model, $key, $index, $column) {
                            return '<button type="button" class="btn btn-primary videoListDelete" data-id="'.$model['id'].'">удалить</button>';
                        }
                    ],
//                    [
//                        'label'   => 'заблокировать',
//                        'content' => function ($model, $key, $index, $column) {
//                            return '<button type="button" class="btn btn-primary videoListBlock" data-id="' . $model['id'] . '">заблокировать</button>';
//                        }
//                    ],
                ],
            ],
            'votingList' => [
                'dataProvider' => new ActiveDataProvider([
                    'query'      => (new Query())
                        ->select('id, name, descr, vendor, date_create,img,vote_cnt,status,answers_counter,moderation_status')->from('cms_goods')->where(['uid' => $id]),
                    'pagination' => [
                        'pageSize' => 10,
                        'pageParam' =>  'votingList-page',
                    ],
                ]),
                'columns'      => [
                    'id:text:#',
                    'name:text:Название',
                    'descr:text:Описание',
                    'vendor:text:Компания',
                    'date_create:date:Дата создания',
                    'img:text:Картинка',
                    'answers_counter:text:ответов',
                    [
                        'attribute' => 'vote_cnt',
                        'label'     => 'количество голосований',
                        'content' => function ($model, $key, $index, $column) {
                            switch($model['vote_cnt']) {
                                case 1: return 'Один раз';
                                case 2: return 'Много раз';
                            }
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'label'     => 'статус',
                        'content' => function ($model, $key, $index, $column) {
                            switch($model['status']) {
                                case 1: return 'На модерации';
                                case 2: return 'Ожидает активации';
                                case 3: return 'Активный';
                                case 4: return 'Завершен';
                            }
                        }
                    ],
                    [
                        'label'   => 'удалить',
                        'content' => function ($model, $key, $index, $column) {
                            return '<button type="button" class="btn btn-primary votingListDelete" data-id="' . $model['id'] . '">удалить</button>';
                        }
                    ],
                ],
            ],
        ]);
    }



    /**
     * Выводит элементы новостных лент для управления
     */
    public function actionNews() {
        return $this->render('news', [
                'gridViewOptions' => [
                    'dataProvider' => new ActiveDataProvider([
                        'query'      => (new Query())
                            ->select('*')
                            ->from('cms_news_rss')
                            ->orderBy('id'),
                        'pagination' => [
                            'pageSize' => 50,
                        ],
                    ]),
                    'columns'      => [
                        'id:text:#',
                        'title:text:title',
                        'description:text:description',
                        'lang_id:text:lang_id',
                        'img:text:img',
                        'link_rss:text:link_rss',
                        'link_site:text:link_site',
                        'general:text:general',
                        [
                            'label'   => 'Редактировать',
                            'content' => function ($model, $key, $index, $column) {
                                $url = Url::to(['admin/news_edit', 'id' => $model['id']]);

                                return '<a href="' . $url . '" class="btn btn-primary" >Редактировать</a>';
                            }
                        ],
                        [
                            'label'   => 'Удалить',
                            'content' => function ($model, $key, $index, $column) {
                                return '<button type="button" class="btn btn-primary btn-newsDelete" data-id="' . $model['id'] . '">Удалить</button>';
                            }
                        ],
                    ],
                ]
            ]
        );
    }

    /**
     * Удаляет новостную ленту
     */
    public function actionNews_delete($id) {
        NewsRssItem::find($id)->delete();
        return $this->jsonSuccess();
    }

    /**
     * Удаляет опрос
     */
    public function actionObjects_voting_delete($id) {
        Voting::find($id)->delete();
        return $this->jsonSuccess();
    }

    /**
     * Удаляет видео
     */
    public function actionObjects_video_delete($id) {
        VideoFile::find($id)->delete();
        return $this->jsonSuccess();
    }

    /**
     * Выводит форму и добавляет значения для новостной ленты
     */
    public function actionNews_add() {
        $model = new FormNewsRssItem();
        if ($model->load(Yii::$app->request->post()) && $model->insert()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('news_add', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Выводит форму редактирования для новостной ленты и обновляет через POST
     */
    public function actionNews_edit($id)
    {
        $model = FormNewsRssItem::find($id);
        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('news_edit', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Логирует действие пользователя
     */
    public function log($description) {
        parent::logAction(Yii::$app->user->identity->getId(), $description);
    }
}
