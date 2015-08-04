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

class Superadmin_communitiesController extends SuperadminBaseController
{
    public function actionIndex()
    {
        return $this->render([
            'gridViewOptions' => [
                'dataProvider' => new ActiveDataProvider([
                    'query'      => (new Query())
                        ->select('*')
                        ->from('cms_communities_category')
                        ->orderBy([
                            'if(`order` is null, 1, 0)' => SORT_ASC,
                            'order'                     => SORT_ASC,
                        ])
                    ,
                    'pagination' => [
                        'pageSize' => 50,
                    ],
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
                    'title:text:Название',
                    'general:text:general',
                    [
                        'label'   => 'Редактировать',
                        'content' => function ($model, $key, $index, $column) {
                            return Html::a('Редактировать', [
                                    'superadmin_communities/edit',
                                    'id' => $model['id']
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
     * Выводит форму добавления пользователя и добавляет через POST
     */
    public function actionAdd()
    {
        $model = new \app\models\Form\Communities();
        if ($model->load(Yii::$app->request->post()) && $model->insert()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            self::log('Добавил категорию в сообщества');

            return $this->refresh();
        }
        else {
            return $this->render([
                'model' => $model,
            ]);
        }
    }

    /**
     * Выводит форму редактирования пользователя и обновляет через POST
     */
    public function actionEdit($id)
    {
        $model = \app\models\Form\Communities::find($id);
        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            self::log('Отредактировал категорию в сообществах');

            return $this->refresh();
        }
        else {
            return $this->render([
                'model' => $model,
            ]);
        }
    }

    /**
     * Удалает пользователя админки
     */
    public function actionDelete($id)
    {
        $item = \app\models\Form\Communities::find($id);
        $item->delete();
        self::log('Удалил категорию в сообществах');

        return $this->jsonSuccess();
    }

    /**
     * Делает сортировку всех строк
     * REQUEST:
     * - ids - array
     */
    public function actionSort()
    {
        \app\models\Communities::resort(self::getParam('ids'));
        self::log('Изменил сортировку в сообществах');

        return self::jsonSuccess();
    }
}
