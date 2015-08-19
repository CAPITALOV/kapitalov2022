<?php

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Курсы';
?>

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
