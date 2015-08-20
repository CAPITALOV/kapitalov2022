<?php

use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $item  \app\models\Stock */

$this->title = $item->getField('name');

\app\assets\Slider\Asset::register($this);

$timeEnd = time();
$timeStart = $timeEnd - 60 * 60 * 24 * 30 * 3;
$defaultEnd = $timeEnd;
$defaultStart = $defaultEnd - 60*60*24*30;
$this->registerJs(<<<JS
$('#slider').rangeSlider({
    bounds: {min: {$timeStart}, max: {$timeEnd}},
    formatter: function(val) {
        var d = new Date();
        d.setTime(parseInt(val) + '000');
        var out = d.getDate() + '.' + (d.getMonth() + 1) + '.' + d.getFullYear();

        return out;
    },
    defaultValues:{min: {$defaultStart}, max: {$defaultEnd}}
});
JS
);
?>
<h1 class="page-header"><?= $this->title ?></h1>


<?= \cs\Widget\ChartJs\Line::widget([
    'width'     => 800,
    'lineArray' => $lineArray,
]) ?>

<h2 class="page-header">Экспорт</h2>

<div class="col-lg-6">
    <div style="margin: 10px 0px 20px 0px;">
        <div id="slider"></div>
    </div>
    <button class="btn btn-default" style="width: 100%;">Экспортировать</button>
</div>
