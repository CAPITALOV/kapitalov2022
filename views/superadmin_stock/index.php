<?php

use yii\helpers\Url;
use yii\helpers\Html;
/** @var $items array cap_stok */
$this->title = 'Курсы';
?>

<h1 class="page-header"><?= $this->title ?></h1>
<style>
    .tableMy .date {
        font-size: 80%;
    }
</style>
<table class="table tableMy" style="width:100%;">
    <tr>
        <th>Наименование</th>
        <th>График</th>
        <th>add</th>
        <th>edit</th>
        <th>Имортировать</th>
        <th>red</th>
        <th>blue</th>
        <th>kurs</th>
    </tr>
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
                    'superadmin_stock/graph',
                    'id' => $item['id']
                ]) ?>">
                    График
                </a>
            </td>
            <td>
                <a href="<?= Url::to([
                    'superadmin_stock/prognosis_add',
                    'id' => $item['id']
                ]) ?>">
                    add
                </a>
            </td>
            <td>
                <a href="<?= Url::to([
                    'superadmin_stock/prognosis_edit',
                    'id' => $item['id']
                ]) ?>">
                    edit
                </a>
            </td>
            <td>
                <a href="<?= Url::to([
                    'superadmin_stock/import',
                    'id' => $item['id']
                ]) ?>">
                    Импортировать
                </a>
            </td>
            <td class="date">
                <?php
                foreach($red as $row) {
                    if ($row['stock_id'] == $item['id']) {
                        $min = (new DateTime($row['min']))->format('d.m.Y');
                        $max = (new DateTime($row['max']))->format('d.m.Y');
                        echo "{$min} ... {$max}";
                    }
                }
                ?>
            </td>
            <td class="date">
                <?php
                foreach($blue as $row) {
                    if ($row['stock_id'] == $item['id']) {
                        $min = (new DateTime($row['min']))->format('d.m.Y');
                        $max = (new DateTime($row['max']))->format('d.m.Y');
                        echo "{$min} ... {$max}";
                    }
                }
                ?>
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
            </td>
        </tr>

    <?php
    }?>
</table>


<a href="<?= Url::to(['superadmin_stock/add']) ?>" class="btn btn-default">Добавить</a>
