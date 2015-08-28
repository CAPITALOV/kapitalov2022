<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $user app\models\User */

$this->title = 'Профиль пользователя' ;
$this->params['breadcrumbs'][] = $this->title;
?>


<h1 class="page-header">Профиль пользователя</h1>
<p>Почта: <?= $user->getField('email') ?></p>
<p><a href="/passwordChange">Поменять пароль</a></p>