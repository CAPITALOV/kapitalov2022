<?php

/**
 * Класс для действий Supermoderator
 *
 */

namespace app\controllers;

use app\models\Form\AdminUser;
use app\models\Form\ModeratorSetting;
use app\models\ModerationManager;
use app\models\Translator as T;
use app\models\User;
use app\models\UserSuffra;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;

class SupermoderatorController extends BaseController {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function($rule, $action) {
                            if (!Yii::$app->user->isGuest) {
                                return Yii::$app->user->identity->hasRole(User::ROLE_SUPER_MODERATOR);
                            }

                            return false;
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {
        return $this->render('index');
    }

    /**
     * Выводит модераторов
     */
    public function actionModerators() {
        $sort = new Sort([
            'attributes' => [
                'e' => [
                    'asc' => ['email' => SORT_ASC],
                    'desc' => ['email' => SORT_DESC],
                    'default' => SORT_DESC,
                    'label' => T::t('Email')
                ],
                'v' => [
                    'asc' => ['violations' => SORT_ASC],
                    'desc' => ['violations' => SORT_DESC],
                    'default' => SORT_DESC,
                    'label' => T::t('Violations')
                ],
                'a' => [
                    'asc' => ['actions' => SORT_ASC],
                    'desc' => ['actions' => SORT_DESC],
                    'default' => SORT_DESC,
                    'label' => T::t('Actions')
                ]
            ]
        ]);

        return $this->render('moderators', [
                    'gridViewOptions' => [
                        'dataProvider' => new ActiveDataProvider([
                            'query' => (new ModerationManager())->setReturnQueryInstance(true)
                                    ->getModerators()
                                    ->orderBy($sort->orders),
                            'pagination' => [
                                'pageSize' => 20,
                            ],
                                ]),
                        'columns' => [
                            'id:text:id',
                            [
                                'header' => $sort->link('e'),
                                'attribute' => 'email',
                                'label' => T::t('Email'),
                                'content' => function($model) {
                                    return Html::a($model['email'], Url::to(['supermoderator/moderators_edit', 'id' => $model['id']]));
                                }
                                    ],
                                    'name_first:text:Имя',
                                    'name_last:text:Фамилия',
                                    [
                                        'header' => $sort->link('v'),
                                        'label' => T::t('violations'),
                                        'content' => function ($model) {
                                            if (0 == $model['violations'])
                                                return '';
                                            return '<a href="' . Url::to(['supermoderator/moderator_violations', 'id' => $model['id']]) . '"><span class="label label-warning">' . $model['violations'] . '</span></a>';
                                        }
                                            ],
                                            [
                                                'header' => $sort->link('a'),
                                                'label' => T::t('actions'),
                                                'attribute' => 'actions',
                                                'content' => function ($model) {
                                                    if (0 == $model['actions'])
                                                        return '';
                                                    return '<a href="' . Url::to(['moderator/history', 'id' => $model['id']]) . '"><span class="label label-warning">' . $model['actions'] . '</span></a>';
                                                }
                                                    ],
                                                    [
                                                        'label' => T::t('Actions'),
                                                        'content' => function ($model, $key, $index, $column) {
                                                            $url = Url::to(['supermoderator/moderators_edit', 'id' => $model['id']]);
                                                            return '<a class="btn btn-primary"  href="' . $url . '">редактировать</a>&nbsp;'
                                                                    . '<button type="button" class="btn btn-primary moderatorsDelete"  data-id="' . $model['id'] . '">удалить</button>';
                                                        }
                                                            ],
                                                        ],
                                                    ]
                                                        ]
                                        );
                                    }

                                    public function actionModerators_settings() {
                                        $gridOpts = [
                                            'dataProvider' => new ActiveDataProvider([
                                                'query' => (new Query())->select(['s.*', new Expression('CONCAT_WS(" ", u.name_first,u.name_last) as username')])
                                                        ->from(ModeratorSetting::TABLE . ' s')
                                                        ->leftJoin(User::TABLE . ' u', 'u.id=s.moderator_id'),
                                                'pagination' => [
                                                    'pageSize' => 20,
                                                ],
                                                    ]),
                                            'columns' => [
                                                'id',
//                                                [
//                                                    'label' => T::t('Moderator'),
//                                                    'attribute' => 'username',
//                                                    'content' => function($model) {
//                                                        return Html::a(Url::to(['supermoderator/moderators_edit', 'id' => $model['moderator_id']]));
//                                                    }
//                                                        ],
                                                'key:text:' . T::t('Setting'),
                                                'value:text:' . T::t('Value'),
                                                [
                                                    'label' => T::t('Actions'),
                                                    'content' => function($model) {
                                                        return implode('&nbsp;', [
                                                            Html::a(T::t(T::t('Edit')), Url::to(['supermoderator/moderators_setting_edit', 'id' => $model['id']]), ['class' => 'btn btn-primary']),
                                                            Html::a(T::t('Remove'), Url::to(['supermoderator/moderators_setting_remove', 'id' => $model['id']]), ['class' => 'btn btn-default'])
                                                                ]
                                                        );
                                                    }
                                                        ]
                                                    ]
                                                ];

                                                return $this->render('moderators_settings', [
                                                            'gridOpts' => $gridOpts
                                                ]);
                                            }

                                            public function actionModerators_setting_add() {
                                                $model = new ModeratorSetting();
                                                if ($model->load(Yii::$app->request->post()) && $model->insert()) {
                                                    Yii::$app->session->addFlash('flash', [
                                                        'type' => 'success',
                                                        'message' => T::t('Setting was successfully added')
                                                    ]);
                                                    $this->redirect(Url::to(['supermoderator/moderators_settings']));
                                                } else {
                                                    return $this->render('moderators_setting_add', [
                                                                'model' => $model
                                                    ]);
                                                }
                                            }

                                            public function actionModerators_setting_remove($id) {
                                                (new Query())->createCommand()->delete(ModeratorSetting::TABLE, 'id=' . $id)->execute();
                                                Yii::$app->session->addFlash('flash', [
                                                    'type' => 'success',
                                                    'message' => T::t('Setting was removed')
                                                ]);
                                                $this->redirect(Url::to(['supermoderator/moderators_settings']));
                                            }

                                            public function actionModerators_setting_edit($id) {
                                                /* @todo why this happens ? *
                                                  $model = ModeratorSetting::find()->where(['id'=>$id])->one(); */
                                                $model = new ModeratorSetting((new Query())->select('*')->from(ModeratorSetting::TABLE)->where('id=' . $id)->one());

                                                if (Yii::$app->request->getIsPost() && $model->update(false, Yii::$app->request->post($model->formName()))) {
                                                    Yii::$app->session->setFlash('setting_updated');
                                                    self::log(T::t('Moderator setting was updated'));
                                                    return $this->refresh();
                                                } else {
                                                    return $this->render('moderators_setting_edit', [
                                                                'model' => $model,
                                                    ]);
                                                }
                                            }

                                            public function actionModerator_violations($id) {
                                                $gridOpts = [
                                                    'dataProvider' => new ActiveDataProvider([
                                                        'query' => (new ModerationManager())->setReturnQueryInstance(true)
                                                                ->getModeratorViolations($id),
                                                        'pagination' => [
                                                            'pageSize' => 20,
                                                        ],
                                                            ]),
                                                    'columns' => [
                                                        'id',
                                                        'user_name:text:' . T::t('User'),
                                                        [
                                                            'label' => T::t('Violation'),
                                                            'content' => function($model) {
                                                                return Html::textarea('msg', $model['msg'], ['rows' => 3]);
                                                            }
                                                                ]
                                                            ]
                                                        ];
                                                        return $this->render('moderators_violations', [
                                                                    'gridOpts' => $gridOpts,
                                                                    'moderator' => User::findIdentity($id)
                                                        ]);
                                                    }

                                                    /**
                                                     * Выводит форму добавления пользователя и добавляет через POST
                                                     */
                                                    public function actionModerators_add() {
                                                        $model = new AdminUser();
                                                        if ($model->load(Yii::$app->request->post()) && $model->insert()) {
                                                            Yii::$app->session->setFlash('contactFormSubmitted');

                                                            return $this->refresh();
                                                        } else {
                                                            return $this->render('moderators_add', [
                                                                        'model' => $model,
                                                            ]);
                                                        }
                                                    }

                                                    /**
                                                     * Выводит форму редактирования пользователя и обновляет через POST
                                                     */
                                                    public function actionModerators_edit($id) {
                                                        $model = AdminUser::find($id);
                                                        if ($model->load(Yii::$app->request->post()) && $model->update()) {
                                                            Yii::$app->session->setFlash('contactFormSubmitted');

                                                            return $this->refresh();
                                                        } else {
                                                            return $this->render('moderators_edit', [
                                                                        'model' => $model,
                                                            ]);
                                                        }
                                                    }

                                                    /**
                                                     * Удаляет пользователя
                                                     */
                                                    public function actionModerators_delete($id) {
                                                        User::findIdentity($id)->delete();
                                                        return $this->jsonSuccess();
                                                    }

                                                    public function actionMod_profile($id) {
                                                        $profile = UserSuffra::find($id);

                                                        return $this->render('mod_profile', ['profile' => $profile]);
                                                    }

                                                    /**
                                                     * Логирует действие пользователя
                                                     */
                                                    public function log($description) {
                                                        parent::logAction(Yii::$app->user->identity->getId(), $description);
                                                    }

                                                }
                                                
