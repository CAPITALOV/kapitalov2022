<?php

use yii\helpers\Url;
use yii\helpers\Html;

/** @var $query \yii\db\Query */

$this->title = 'Пользователи';
?>

<h1 class="page-header"><?= $this->title ?></h1>

<?= \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query'      => $query,
        'pagination' => [
            'pageSize' => 50,
        ],
    ]),
    'tableOptions' => [
        'class' => 'table table-striped table-hover'
    ],
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'header'  => 'аватар',
            'content' => function ($model, $key, $index, $column) {
                if (!is_null($model['avatar'])) {
                    return Html::img($model['avatar'], [
                        'width' => 40,
                        'style' => 'border: 1px solid #888',
                    ]);
                }

                return '';
            },
        ],
        [
            'header'    => 'имя',
            'attribute' => 'name_first',
        ],
        [
            'header'    => 'фамилия',
            'attribute' => 'name_last',
        ],
        'email',
        [
            'header'    => 'телефон',
            'attribute' => 'phone',
        ],
        [
            'header'    => 'организация',
            'attribute' => 'name_org',
        ],
    ]
]) ?>

