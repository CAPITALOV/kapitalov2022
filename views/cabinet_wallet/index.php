<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $end integer до какого оплачен кабинет */

$this->title = 'Счет';
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (is_null($end)) { ?>
        <p>Демо режим</p>
    <?php } else { ?>
        <p>Оплачено до: <?=  date('dd.mm.yyyy', $end) ?></p>
    <?php } ?>
</div>