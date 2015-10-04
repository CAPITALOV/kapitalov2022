<?php

use yii\helpers\Url;
use yii\helpers\Html;
/** @var $items array cap_stock */
/** @var $red array  */
/** @var $blue array  */
/** @var $kurs array  */
/** @var $this yii\web\View  */


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
        })
JS
);

?>

<h1 class="page-header"><?= $this->title ?></h1>
<style>
    .tableMy .date {
        font-size: 80%;
    }
</style>
<table class="table tableMy table-striped" style="width:100%;">
    <thead>
        <tr>
            <th>Действует?</th>
            <th>Наименование</th>
            <th>График</th>
            <th>red</th>
            <th>blue</th>
            <th>Имортировать</th>
            <th>kurs</th>
            <th>Импортировать</th>
        </tr>
    </thead>
    <?php foreach ($items as $item) { ?>
        <tr>
            <td>
                <?php
                if (\yii\helpers\ArrayHelper::getValue($item, 'is_enabled', 0) == 1) {
                    $model->is_enabled = true;
                } else {
                    $model->is_enabled = false;
                }
                ?>
                <?= \cs\Widget\CheckBox2\CheckBox::widget([
                    'model'     => $model,
                    'attribute' => 'is_enabled',
                    'options' => ['data' => [
                        'id' => $item['id']
                    ]]
                ]) ?>
            </td>
            <td>
                <a href="<?= Url::to([
                    'superadmin_stock/edit',
                    'id' => $item['id']
                ]) ?>">
                    <?= $item['name'] ?>
                </a>
            </td>
            <td>
                <a href="<?= Url::to([
                    'cabinet/stock_item',
                    'id' => $item['id']
                ]) ?>">
                    График
                </a>
            </td>
            <td class="date">
                <?php
                foreach($red as $row) {
                    if ($row['stock_id'] == $item['id']) {
                        $min = (new DateTime($row['min']))->format('d.m.Y');
                        $max = (new DateTime($row['max']))->format('d.m.Y');
                        echo Html::a("{$min} ... {$max}",['superadmin_stock/show', 'id' => $item['id'], 'color'=>'red']);
                    }
                }
                ?>
                <div class="row col-lg-12" style="margin-top: 4px;">
                    <a
                        href="<?= Url::to([
                        'superadmin_stock/prognosis_delete_red',
                        'id' => $item['id']
                    ]) ?>"
                        class="btn btn-default btn-xs"
                        onmouseover="$(this).removeClass('btn-default').addClass('btn-danger')"
                        onmouseout="$(this).removeClass('btn-danger').addClass('btn-default')"
                        >
                        Удалить
                    </a>
                </div>

            </td>
            <td class="date">
                <?php
                foreach($blue as $row) {
                    if ($row['stock_id'] == $item['id']) {
                        $min = (new DateTime($row['min']))->format('d.m.Y');
                        $max = (new DateTime($row['max']))->format('d.m.Y');
                        echo Html::a("{$min} ... {$max}",['superadmin_stock/show', 'id' => $item['id'], 'color'=>'blue']);
                    }
                }
                ?>
                <div class="row col-lg-12" style="margin-top: 4px;">
                    <a
                        href="<?= Url::to([
                            'superadmin_stock/prognosis_delete_blue',
                            'id' => $item['id']
                        ]) ?>"
                        class="btn btn-default btn-xs"
                        onmouseover="$(this).removeClass('btn-default').addClass('btn-danger')"
                        onmouseout="$(this).removeClass('btn-danger').addClass('btn-default')"
                        >
                        Удалить
                    </a>
                </div>
            </td>
            <td>
                <a href="<?= Url::to([
                    'superadmin_stock/import',
                    'id' => $item['id']
                ]) ?>" class="btn btn-default">
                    Импортировать
                </a>
            </td>

            <td class="date">
                <?php
                foreach($kurs as $row) {
                    if ($row['stock_id'] == $item['id']) {
                        $min = (new DateTime($row['min']))->format('d.m.Y');
                        $max = (new DateTime($row['max']))->format('d.m.Y');
                        echo "{$min} ... {$max}";
                    }
                }
                ?>
                <div class="row col-lg-12" style="margin-top: 4px;">
                    <a
                        href="<?= Url::to([
                            'superadmin_stock/kurs_delete',
                            'id' => $item['id']
                        ]) ?>"
                        class="btn btn-default btn-xs"
                        onmouseover="$(this).removeClass('btn-default').addClass('btn-danger')"
                        onmouseout="$(this).removeClass('btn-danger').addClass('btn-default')"
                        >
                        Удалить
                    </a>
                </div>
            </td>
            <td>
                <a href="<?= Url::to([
                    'superadmin_stock/import_kurs',
                    'id' => $item['id']
                ]) ?>" class="btn btn-default">
                    Импортировать
                </a>
            </td>
        </tr>

    <?php
    }?>
</table>


<a href="<?= Url::to(['superadmin_stock/add']) ?>" class="btn btn-default">Добавить</a>
