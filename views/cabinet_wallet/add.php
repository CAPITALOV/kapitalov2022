<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\authclient\widgets\AuthChoice;

/* @var $this yii\web\View */
/* @var $end integer до какого оплачен кабинет */

$this->title = 'Пополнение счета';
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>

<p>1 мес - 100 $ <a href="//www.paypal.com" class="btn btn-default" target="_blank">Оплатить</a></p>

<p><a href="/yandexMoney">Yandex</a></p>