<?php

use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $paid array */
/* @var $notPaid array */

$this->title = 'Котировки';
$this->registerJs("$('.labelPaid').tooltip()");

//\cs\services\VarDumper::dump($notPaid);
?>

<h1 class="page-header"><?= Html::encode($this->title) ?></h1>


<h2 class="page-header">Оплаченные котировки</h2>
<div class="row col-sm-12" style="margin-bottom: 40px;">
    <?php
    foreach ($paid as $item) {

        $class = new \app\models\Stock($item);
        ?>
        <div class="col-sm-4" style="margin-bottom: 30px;">
            <?php
            if (!is_null($item['logo'])) {
                echo Html::a(Html::img($item['logo'], [
                    'class' => 'thumbnail',
                    'style' => 'opacity: 1;',
                ]), [
                    'cabinet/stock_item3',
                    'id' => $item['id']
                ]);
            }
            ?>
            <p><?= $item['name'] ?></p>
        <span
            href="<?= Url::to(['cabinet_wallet/add', 'id' => $item['id']]) ?>"
            class="label label-success labelPaid"
            style="width: 100%"
            title="<?= 'до ' . \Yii::$app->formatter->asDate($item['date_finish']) ?>, осталось <?= \cs\services\DatePeriod::diff($item['date_finish']) ?>"

            >Оплачено</span>
        </div>
    <?php
    }?>
</div>


<h2 class="page-header">Заказать</h2>
<div class="row col-sm-12">
    <?php
    foreach ($notPaid as $item) {
        $class = new \app\models\Stock($item);
        ?>
        <div class="col-sm-4" style="margin-bottom: 30px;">
            <?php
            if (!is_null($item['logo'])) {
                echo Html::a(Html::img($item['logo'], [
                    'class' => 'thumbnail',
                    'style' => 'opacity: 0.5;',
                ]), [
                    'cabinet_wallet/add',
                    'id' => $item['id']
                ]);
            }
            ?>
            <p><?= $item['name'] ?></p>
        <a
            href="<?= Url::to(['cabinet_wallet/add', 'id' => $item['id']]) ?>"
            class="btn btn-primary"
            style="width: 100%"

            >Оплатить</a>
        </div>
    <?php
    }?>
<!--    <div class="col-sm-4" style="margin-bottom: 30px;">-->
<!--        <center>-->
<!--            --><?php
//            echo Html::a(Html::img('/images/cabinet/index/all-stok.png', [
//                'class' => 'thumbnail',
//                'width' => 200,
//            ]), ['cabinet_wallet/add1']);
//            ?>
<!--            <p>Национальный рынок</p>-->
<!--        </center>-->
<!--        <a-->
<!--            href="--><?//= Url::to(['cabinet_wallet/add1']) ?><!--"-->
<!--            class="btn btn-primary"-->
<!--            style="width: 100%"-->
<!---->
<!--            >Выбрать</a>-->
<!--    </div>-->
<!--    <div class="col-sm-4" style="margin-bottom: 30px;">-->
<!--        <center>-->
<!--            --><?php
//            echo Html::a(Html::img('/images/cabinet/index/all-stok.png', [
//                'class' => 'thumbnail',
//                'width' => 200,
//            ]), ['cabinet_wallet/add2']);
//            ?>
<!--            <p>Зарубежный рынок</p>-->
<!--        </center>-->
<!--        <a-->
<!--            href="--><?//= Url::to(['cabinet_wallet/add2']) ?><!--"-->
<!--            class="btn btn-primary"-->
<!--            style="width: 100%"-->
<!---->
<!--            >Выбрать</a>-->
<!--    </div>-->

</div>