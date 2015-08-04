<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1>Пользователи</h1>
    <?php
    echo GridView::widget($gridViewOptions);
    ?>
</div>
