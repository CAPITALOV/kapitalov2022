<?php

use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $item  \app\models\Stock */

$this->title = $item->getField('name');

?>
    <h1 class="page-header"><?= $this->title ?></h1>


<?= \cs\Widget\ChartJs\Line::widget([
    'width'     => 800,
    'lineArray' => $lineArray,
]) ?>