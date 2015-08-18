<?php

use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $item  \app\models\Stock */

$this->title = $item->getField('name');

?>

<div class="container">
    <div class="col-lg-12">
        <h1 class="page-header"><?= $this->title ?></h1>
    </div>


    <?= \cs\Widget\ChartJs\Line::widget([
        'width'     => 800,
        'lineArray' => \app\service\GraphExporter::convert([
            'rows'  => [
                \app\models\StockKurs::query(['stock_id' => $item->getId()])->all(),
                \app\models\StockPrognosis::query(['stock_id' => $item->getId()])->all(),
            ]
        ]),
    ]) ?>
</div>