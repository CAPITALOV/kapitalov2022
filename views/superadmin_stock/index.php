<?php

use yii\helpers\Url;
use app\services\GsssHtml;
use yii\helpers\Html;

$this->title = 'Курсы';

$this->registerJsFile('/js/pages/admin_events/index.js', [
    'depends' => [
        'app\assets\AppAsset',
    ]
]);
?>

<div class="container">
    <div class="page-header">
        <h1><?= $this->title ?></h1>
    </div>


    <table class="table">
        <?php
        foreach ($items as $item) {
            ?>
            <tr>
                <td>
                    <a href="<?= Url::to([
                        'superadmin_stock/edit',
                        'id' => $item['id']
                    ]) ?>" id="newsItem-<?= $item['id'] ?>">
                        <?= $item['name'] ?>
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