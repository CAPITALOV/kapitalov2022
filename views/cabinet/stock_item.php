<?php

use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $item  \app\models\Stock */

$this->title = $item->getField('name');

?>

    <div class="col-lg-12">
        <h1 class="page-header"><?= $this->title ?></h1>
    </div>


<?= \cs\Widget\ChartJs\Line::widget([
    'width'     => 800,
    'lineArray' => $lineArray,
]) ?>