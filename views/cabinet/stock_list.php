<?php

use yii\helpers\Url;
use yii\helpers\Html;
/* @var $this yii\web\View */

$this->title = 'Курсы';


?>

<h1 class="page-header"><?= Html::encode($this->title) ?></h1>



<table class="table table-striped" style="width:100%;">
    <thead>
    <tr>
        <th>Наименование</th>
    </tr>
    </thead>
    <?php
    foreach ($items as $item) {
        ?>
        <tr>
            <td>
                <a href="<?= Url::to([
                    'cabinet/stock_item',
                    'id' => $item['id']
                ]) ?>">
                    <?= $item['name'] ?>
                </a>
            </td>
        </tr>

    <?php
    }?>
</table>
