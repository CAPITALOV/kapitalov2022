<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $end integer до какого оплачен кабинет */

$this->title = 'Пополнение счета';
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>1 мес - 100 $ <a href="//www.paypal.com" class="btn btn-default" target="_blank">Оплатить</a></p>
</div>