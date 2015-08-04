<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */

$this->title = 'Объекты пользователя';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('/js/pages/admin/objects.js', ['depends' => ['yii\grid\GridViewAsset']]);
?>
<div class="site-about">
    <h1>Объекты пользователя</h1>
    <h2>Файлы</h2>
    <?php
    echo GridView::widget($fileList);
    ?>
    <h2>Видео</h2>
    <?php
    echo GridView::widget($videoList);
    ?>
    <h2>Опросы</h2>
    <?php
    echo GridView::widget($votingList);
    ?>

</div>
