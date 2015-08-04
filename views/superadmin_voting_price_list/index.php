<?php
/**
 * @var array $answersCounterList
 * @var array $priceActionList
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
$this->title = 'Категории';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('/js/pages/superadmin_voting_price_list/index.js', ['depends' => [
    'yii\web\JqueryAsset',
]]);
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <h2>Количество ответов</h2>
    <a href="<?= Url::to(['superadmin_voting_price_list/add', 'type' => 1])?>" class="btn btn-primary">Добавить</a>
    <?php
    echo GridView::widget($answersCounterList);
    ?>

    <h2>Цена участия</h2>
    <a href="<?= Url::to(['superadmin_voting_price_list/add', 'type' => 2])?>" class="btn btn-primary">Добавить</a>
    <?php
    echo GridView::widget($priceActionList);
    ?>
</div>

