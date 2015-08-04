<?php

/**
 * Класс для действий Superadmin
 *
 */

namespace app\controllers;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\VarDumper;

class Superadmin_voting_price_listController extends SuperadminBaseController
{
    public function actionIndex()
    {
        return $this->render([
            'rateAdd'            => (new Query())->select('cnt')->from('cms_goods_vote_type')->where(['type' => 0])->scalar(),
            'rateBonus'          => (new Query())->select('cnt')->from('cms_goods_vote_type')->where(['type' => 3])->scalar(),
            'answersCounterList' => [
                'dataProvider' => new ActiveDataProvider([
                    'query'      => (new Query())
                        ->select('*')
                        ->from('cms_goods_vote_type')
                        ->where([
                            'type'    => 1,
                            'enabled' => 1,
                        ])
                        ->orderBy([
                            'cnt' => SORT_ASC,
                        ])
                    ,
                ]),
                'rowOptions' => function ($model, $key, $index, $grid){
                    return [
                        'data' => [
                            'id' => $model['id']
                        ],
                    ];

                },
                'tableOptions' => [
                    'class' => 'table table-striped table-bordered',
                    'id' => 'tableSort'
                ],
                'columns'      => [
                    'id:text:#',
                    'cnt:text:Кол-во ответов',
                    'price:text:Множитель',
                    [
                        'label'   => 'Редактировать',
                        'content' => function ($model, $key, $index, $column) {
                            return Html::a('Редактировать', [
                                'superadmin_voting_price_list/edit',
                                'id'   => $model['id'],
                                'type' => 1
                            ], ['class' => 'btn btn-primary']);
                        }
                    ],
                    [
                        'label'   => 'Удалить',
                        'content' => function ($model, $key, $index, $column) {
                            return Html::button('Удалить', [
                                    'class' => 'btn btn-primary buttonDelete',
                                    'data'  => ['id' => $model['id']],
                                ]);
                        }
                    ],

                ],
            ],
            'priceActionList' => [
                'dataProvider' => new ActiveDataProvider([
                    'query'      => (new Query())
                        ->select('*')
                        ->from('cms_goods_vote_type')
                        ->where([
                            'type'    => 2,
                            'enabled' => 1,
                        ])
                        ->orderBy([
                            'cnt' => SORT_ASC,
                        ])
                    ,
                ]),
                'rowOptions' => function ($model, $key, $index, $grid){
                    return [
                        'data' => [
                            'id' => $model['id']
                        ],
                    ];

                },
                'tableOptions' => [
                    'class' => 'table table-striped table-bordered',
                    'id' => 'tableSort'
                ],
                'columns'      => [
                    'id:text:#',
                    'cnt:text:Цена ответа',
                    'price:text:Множитель',
                    [
                        'label'   => 'Редактировать',
                        'content' => function ($model, $key, $index, $column) {
                            return Html::a('Редактировать', [
                                'superadmin_voting_price_list/edit',
                                'id'   => $model['id'],
                                'type' => 2
                            ], ['class' => 'btn btn-primary']);
                        }
                    ],
                    [
                        'label'   => 'Удалить',
                        'content' => function ($model, $key, $index, $column) {
                            return Html::button('Удалить', [
                                'class' => 'btn btn-primary buttonDelete',
                                'data'  => ['id' => $model['id']],
                            ]);
                        }
                    ],
                ],
            ],
        ]);
    }

    /**
     * Выводит форму добавления
     */
    public function actionAdd()
    {
        $model = new \app\models\Form\VotingRateList();
        if ($model->load(Yii::$app->request->post()) && $model->insert()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            self::log('Добавил множитель в таблицу множителей');

            return $this->refresh();
        }
        else {
            return $this->render([
                'model' => $model,
            ]);
        }
    }

    /**
     * Выводит форму редактирования
     */
    public function actionEdit($id)
    {
        $model = \app\models\Form\VotingRateList::find($id);
        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            self::log('Отредактировал множитель в таблице множителей');

            return $this->refresh();
        }
        else {
            return $this->render([
                'model' => $model,
            ]);
        }
    }

    /**
     * Удалает
     */
    public function actionDelete($id)
    {
        $item = \app\models\VotingRateList::find($id);
        $item->delete();
        self::log('Удалил категорию в сообществах');

        return self::jsonSuccess();
    }
}
