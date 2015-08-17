<?php

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Курсы';
?>

<div class="container">
    <div class="col-lg-12">
        <h1 class="page-header"><?= $this->title ?></h1>
    </div>


    <table class="table" style="width:100%;">
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
                        'superadmin_stock/kurs_add',
                        'id' => $item['id']
                    ]) ?>">
                        Добавить курс
                    </a>
                </td>
                <td>
                    <a href="<?= Url::to([
                        'superadmin_stock/kurs_edit',
                        'id' => $item['id']
                    ]) ?>">
                        Редактировать курс
                    </a>
                </td>
                <td>
                    <a href="<?= Url::to([
                        'superadmin_stock/prognosis_add',
                        'id' => $item['id']
                    ]) ?>">
                        Добавить прогноз
                    </a>
                </td>
                <td>
                    <a href="<?= Url::to([
                        'superadmin_stock/prognosis_edit',
                        'id' => $item['id']
                    ]) ?>">
                        Редактировать прогноз
                    </a>
                </td>
            </tr>

        <?php
        }?>
    </table>


    <div class="col-lg-12">
        <a href="<?= Url::to(['superadmin_stock/add']) ?>" class="btn btn-default">Добавить</a>
    </div>
</div>