<?php

/**
 * Класс для действий Superbuh
 *
 */

namespace app\controllers;

use app\models\Grossbuch;
use app\models\Translator as T;
use app\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Html;

class SuperbuhController extends BaseController {

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
                        return Yii::$app->user->identity->hasRole(User::ROLE_SUPER_BUH);
                    }

                    return false;
                },
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {
        return $this->render('index', [
                    'gridViewOptions' => [
                        'dataProvider' => new ActiveDataProvider([
                            'query' => (new \yii\db\Query())->select('*')->from(Grossbuch::TABLE),
                            'pagination' => [
                                'pageSize' => 20,
                            ],
                                ]),
                        'columns' => [
                            [
                                'attribute' => 'debet',
                                'label' => T::t('Debit'),
                                'content' => function($model) {
                                    return Html::tag('span', Grossbuch::getAccountName($model['src']), ['class' => 'label label-primary']);
                                }
                                    ],
                                    [
                                        'attribute' => 'credit',
                                        'label' => T::t('Credit'),
                                        'content' => function($model) {
                                            return Html::tag('span', Grossbuch::getAccountName($model['dst']), ['class' => 'label label-success']);
                                        }
                                            ],
                                            [
                                                'attribute' => 'dsc',
                                                'label' => T::t('Debit subcount'),
                                            ],
                                            [
                                                'attribute' => 'csc',
                                                'label' => T::t('Debit subcount'),
                                            ],
                                            [
                                                'attribute' => 'dtstamp',
                                                'label' => T::t('Date of operation'),
                                            ],
                                            [
                                                'attribute' => 'val',
                                                'label' => T::t('Balls'),
                                                'content' => function($model) {
                                                    return Html::tag('h4', $model['val'], ['class' => 'text-info']);
                                                }
                                                    ],
                                                    [
                                                        'attribute' => 'desc',
                                                        'label' => T::t('Description'),
                                                        'content' => function($model) {
                                                            if (false !== strpos($model['desc'], '{')) {
                                                                $data = json_decode($model['desc'], true);
                                                                return Html::a($data['name'], isset($data['link']) ? $data['link'] : '#');
                                                            } else {
                                                                return Html::tag('blockquote', $model['desc']);
                                                            }
                                                        }
                                                    ],
                                                ],
                                            ],
                                ]);
                            }

                            /**
                             * Логирует действие пользователя
                             */
                            public function log($description) {
                                parent::logAction(Yii::$app->user->identity->getId(), $description);
                            }

                        }
                        