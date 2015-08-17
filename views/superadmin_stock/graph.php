<?php

use yii\helpers\Url;
use yii\helpers\Html;
/* @var $this \yii\web\View */

$this->title = 'Курсы';

?>

<div class="container">
    <div class="col-lg-12">
        <h1 class="page-header"><?= $this->title ?></h1>
    </div>


    <?= \cs\Widget\ChartJs\Line::widget([
        'width' => 800,
        'lineArray' => [
            'x' => ["January", "February", "March", "April", "May", "June", "July"],
            'y' => [
                [65, 59, 80, 81, 56, 55, 40],
                [28, null, 48, 40, 19, 86, 27],
            ]
        ],
    ]) ?>
</div>