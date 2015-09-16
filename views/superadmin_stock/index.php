<?php

use yii\helpers\Url;
use yii\helpers\Html;
/** @var $items array cap_stok */
/** @var $red array  */
/** @var $blue array  */
/** @var $kurs array  */


$this->title = 'Курсы';
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
            <th>Наименование</th>
            <th>График</th>
            <th>red</th>
            <th>blue</th>
            <th>Имортировать</th>
            <th>kurs</th>
            <th>Импортировать</th>
        </tr>
    </thead>
    <?php
    foreach ($items as $item) {
        ?>
        <tr>
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
