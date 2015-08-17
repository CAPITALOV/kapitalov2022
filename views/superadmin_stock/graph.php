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


    <?= (new \cs\Widget\ChartJs\Line())->run() ?>
</div>