<?php

use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $item  \app\models\Stock */
/* @var $lineArrayKurs  array */
/* @var $lineArrayRed  array */
/* @var $lineArrayBlue  array */
/* @var $isPaid  bool опачена ли эта акция? */

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

<h2>Прогноз (красный)</h2>
<?= \cs\Widget\ChartJs\Line::widget([
    'width'     => 800,
    'lineArray' => $lineArrayRed,
    'colors' => [
        [
            'label'                => "Прогноз",
            'fillColor'            => "rgba(220,220,220,0)",
            'strokeColor'          => "rgba(255,229,229,1)",
            'pointColor'           => "rgba(255,204,204,1)",
            'pointStrokeColor'     => "#fff",
            'pointHighlightFill'   => "#fff",
            'pointHighlightStroke' => "rgba(220,220,220,1)",
        ]
    ],
]) ?>

<h2>Прогноз (синий)</h2>
<?= \cs\Widget\ChartJs\Line::widget([
    'width'     => 800,
    'lineArray' => $lineArrayBlue,
    'colors' => [
        [
            'label'                => "Прогноз",
            'fillColor'            => "rgba(220,220,220,0)",
            'strokeColor'          => "rgba(229,229,255,1)",
            'pointColor'           => "rgba(204,204,255,1)",
            'pointStrokeColor'     => "#fff",
            'pointHighlightFill'   => "#fff",
            'pointHighlightStroke' => "rgba(220,220,220,1)",
        ]
    ],
]) ?>

<h2>Курс</h2>
<?= \cs\Widget\ChartJs\Line::widget([
    'width'     => 800,
    'lineArray' => $lineArrayKurs,
]) ?>

<?php if ($isPaid) { ?>
    <a href="<?= Url::to(['cabinet_wallet/add', 'id' => $item->getId()]) ?>" class="btn btn-default">Купить</a>
<?php } ?>

<h2 class="page-header">Экспорт</h2>

<div class="col-lg-6">
    <div style="margin: 10px 0px 20px 0px;">
        <div id="slider"></div>
    </div>
    <button class="btn btn-default" style="width: 100%;">Экспортировать</button>
</div>
