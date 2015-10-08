<?php

use yii\helpers\Url;
use yii\helpers\Html;

/** @var $items array cap_stock */
/** @var $red array */
/** @var $blue array */
/** @var $kurs array */
/** @var $this yii\web\View */

$this->title = 'Котировки';
$model = new \app\models\Form\StockAll();
$this->registerJs(<<<JS
$('input[name=\"StockAll[is_enabled]\"]').change(function() {
    var t = $(this);
    var id = t.data('id');
    ajaxJson({
        url: 'stock/toggle',
        data:{
            id: id,
            is_enabled: t.is(':checked')? 1 : 0
        },
        success: function(ret) {
            alert('ok');
        }
    });
});
$('.glyphicon').tooltip();
JS
);

$url = new \cs\services\Url();
$params = [];
foreach ($url->params as $name => $value) {
    if (\yii\helpers\StringHelper::startsWith($name, 'Stock')) {
        $params[ substr($name, 6, strlen($name) - 6 - 1) ] = $value;
    }
}
$query = \app\models\Stock::query();
$v = \yii\helpers\ArrayHelper::getValue($params, 'name', '');
if ($v != '') {
    $query->andWhere(['like', 'name', $v]);
}
?>

<h1 class="page-header"><?= $this->title ?></h1>
<style>
    .tableMy .date {
        font-size: 80%;
    }
</style>

<?=
\yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query'      => $query,
        'pagination' => [
            'pageSize' => 20,
        ],
    ]),
    'filterModel'  => new \app\models\Form\Stock(),
    'tableOptions' => [
        'class' => 'table tableMy table-striped table-hover',
    ],
    'columns'      => [
        [
            'header'  => 'Действует?',
            'content' => function ($item) {
                $model = new \app\models\Form\StockAll();
                if (\yii\helpers\ArrayHelper::getValue($item, 'is_enabled', 0) == 1) {
                    $model->is_enabled = true;
                } else {
                    $model->is_enabled = false;
                }

                return \cs\Widget\CheckBox2\CheckBox::widget([
                    'model'     => $model,
                    'attribute' => 'is_enabled',
                    'options'   => ['data' => [
                        'id' => $item['id']
                    ]]]);
            }
        ],
        'name:text:Наименование',
        [
            'header'  => Html::tag('span', null, [
                'class' => 'glyphicon glyphicon-edit',
                'title' => 'Редактировать'
            ]),
            'content' => function ($item) {
                return Html::a(Html::tag('span', null, [
                    'class' => 'glyphicon glyphicon-edit',
                    'title' => 'Редактировать'
                ]), [
                    'superadmin_stock/edit',
                    'id' => $item['id'],
                ], [
                    'class' => 'btn btn-default btn-xs',
                ]);
            }
        ],
        [
            'header'  => Html::tag('span', null, [
                'class' => 'glyphicon glyphicon-signal',
                'title' => 'График'
            ]),
            'content' => function ($item) {
                return Html::a(Html::tag('span', null, [
                    'class' => 'glyphicon glyphicon-signal',
                    'title' => 'График'
                ]), [
                    'superadmin_stock/graph2',
                    'id' => $item['id'],
                ], [
                    'class' => 'btn btn-default btn-xs',
                ]);
            }
        ],
        [
            'header'         => 'Красный',
            'contentOptions' => [
                'class' => 'date'
            ],
            'content'        => function ($item) {
                $row = \app\models\StockPrognosisRed::query()
                    ->select([
                        'MIN(`date`) as min',
                        'MAX(`date`) as max',
                    ])
                    ->groupBy('stock_id')
                    ->andWhere(['stock_id' => $item['id']])
                    ->one();
                $lines = [];
                if ($row['min']) {
                    $min = (new DateTime($row['min']))->format('d.m.Y');
                    $max = (new DateTime($row['max']))->format('d.m.Y');
                    $lines[] = Html::a("{$min} ... {$max}", ['superadmin_stock/show', 'id' => $item['id'], 'color' => 'red']);
                    $lines[] = Html::a(Html::tag('span', null, [
                        'class' => 'glyphicon glyphicon-remove-sign',
                        'title' => 'Удалить'
                    ]), [
                        'superadmin_stock/prognosis_delete_red',
                        'id' => $item['id']
                    ], [
                        'class'       => "btn btn-default btn-xs",
                        'onmouseover' => "$(this).removeClass('btn-default').addClass('btn-danger')",
                        'onmouseout'  => "$(this).removeClass('btn-danger').addClass('btn-default')",
                        'style'       => 'margin-left: 10px;'
                    ]);
                }

                return join("\r", $lines);
            }
        ],

        [
            'header'         => 'Синий',
            'contentOptions' => [
                'class' => 'date'
            ],
            'content'        => function ($item) {
                $row = \app\models\StockPrognosisBlue::query()
                    ->select([
                        'MIN(`date`) as min',
                        'MAX(`date`) as max',
                    ])
                    ->andWhere(['stock_id' => $item['id']])
                    ->one();
                $lines = [];
                if ($row['min']) {
                    $min = (new DateTime($row['min']))->format('d.m.Y');
                    $max = (new DateTime($row['max']))->format('d.m.Y');
                    $lines[] = Html::a("{$min} ... {$max}", ['superadmin_stock/show', 'id' => $item['id'], 'color' => 'blue']);
                    $lines[] = Html::a(Html::tag('span', null, [
                        'class' => 'glyphicon glyphicon-remove-sign',
                        'title' => 'Удалить'
                    ]), [
                        'superadmin_stock/prognosis_delete_blue',
                        'id' => $item['id']
                    ], [
                        'class'       => "btn btn-default btn-xs",
                        'onmouseover' => "$(this).removeClass('btn-default').addClass('btn-danger')",
                        'onmouseout'  => "$(this).removeClass('btn-danger').addClass('btn-default')",
                        'style'       => 'margin-left: 10px;'
                    ]);
                }

                return join("\r", $lines);
            }
        ],
        [
            'header'  => Html::tag('span', null, [
                'class' => 'glyphicon glyphicon-log-in',
                'title' => 'Имортировать'
            ]),
            'content' => function ($item) {
                return Html::a(Html::tag('span', null, [
                    'class' => 'glyphicon glyphicon-log-in',
                    'title' => 'Имортировать'
                ]), [
                    'superadmin_stock/import',
                    'id' => $item['id'],
                ], [
                    'class' => 'btn btn-default btn-xs',
                ]);
            }
        ],
        [
            'header'         => 'Курс',
            'contentOptions' => [
                'class' => 'date'
            ],
            'content'        => function ($item) {
                $row = \app\models\StockKurs::query()
                    ->select([
                        'MIN(`date`) as min',
                        'MAX(`date`) as max',
                    ])
                    ->groupBy('stock_id')
                    ->andWhere(['stock_id' => $item['id']])
                    ->one();
                $lines = [];
                if ($row['min']) {
                    $min = (new DateTime($row['min']))->format('d.m.Y');
                    $max = (new DateTime($row['max']))->format('d.m.Y');
                    $lines[] = "{$min} ... {$max}";
                    $lines[] = Html::a(Html::tag('span', null, [
                        'class' => 'glyphicon glyphicon-remove-sign',
                        'title' => 'Удалить'
                    ]), [
                        'superadmin_stock/kurs_delete',
                        'id' => $item['id']
                    ], [
                        'class'       => "btn btn-default btn-xs",
                        'onmouseover' => "$(this).removeClass('btn-default').addClass('btn-danger')",
                        'onmouseout'  => "$(this).removeClass('btn-danger').addClass('btn-default')",
                        'style'       => 'margin-left: 10px;'
                    ]);
                }

                return join("\r", $lines);
            }
        ],

        [
            'header'  => Html::tag('span', null, [
                'class' => 'glyphicon glyphicon-log-in',
                'title' => 'Имортировать'
            ]),
            'content' => function ($item) {
                return Html::a(Html::tag('span', null, [
                    'class' => 'glyphicon glyphicon-log-in',
                    'title' => 'Имортировать'
                ]), [
                    'superadmin_stock/import_kurs',
                    'id' => $item['id'],
                ], [
                    'class' => 'btn btn-default btn-xs',
                ]);
            }
        ],
    ],
])
?>

<a href="<?= Url::to(['superadmin_stock/add']) ?>" class="btn btn-default">Добавить</a>
