<?php

use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Котировки';
$this->registerJs("$('.labelPaid').tooltip()");
?>

<h1 class="page-header"><?= Html::encode($this->title) ?></h1>

<?php
foreach ($items as $item) {

    $class = new \app\models\Stock($item);
    $isPaid = $class->isPaid();
    ?>
    <div class="col-sm-3" style="margin-bottom: 30px;">
        <?php
        if (!is_null($item['logo'])) {
            echo Html::a(Html::img($item['logo'], [
                'class' => 'thumbnail',
                'style' => 'opacity: ' . (($isPaid) ? '1' : '0.2'),
            ]), [
                'cabinet/stock_item3',
                'id' => $item['id']
            ]);
        }
        ?>
        <p><?= $item['name'] ?></p>
        <?php if ($isPaid) { ?>
            <span
                href="<?= Url::to(['cabinet_wallet/add', 'id' => $item['id']]) ?>"
                class="label label-success labelPaid"
                style="width: 100%"
                title="<?= 'до ' . \Yii::$app->formatter->asDate($item['date_finish']) ?> осталось <?= \cs\services\DatePeriod::diff($item['date_finish']) ?>"

                >Оплачено</span>
        <?php } else { ?>
            <a
                href="<?= Url::to(['cabinet_wallet/add', 'id' => $item['id']]) ?>"
                class="btn btn-default"
                style="width: 100%"

                >Оплатить</a>
        <?php } ?>

    </div>
<?php
}?>


