<?php

use yii\helpers\Url;
use yii\helpers\Html;
/* @var $this \yii\web\View */

$this->title = 'Курсы';

$this->registerJs(<<<JS
// Get the context of the canvas element we want to select
var ctx = document.getElementById("myChart").getContext("2d");
var myNewChart = new Chart(ctx).PolarArea(data);
JS
)
?>

<div class="container">
    <div class="col-lg-12">
        <h1 class="page-header"><?= $this->title ?></h1>
    </div>


    <?= (new \cs\Widget\ChartJs\Line())->run() ?>
</div>