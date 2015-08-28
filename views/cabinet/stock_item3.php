<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this \yii\web\View */
/* @var $item  \app\models\Stock */
/* @var $lineArrayPast  array */
/* @var $lineArrayFuture  array */
/* @var $isPaid  bool опачена ли эта акция? */

$this->title = $item->getField('name');

$colorGreen = [
    'label'                => "Курс",
    'fillColor'            => "rgba(220,220,220,0.2)",
    'strokeColor'          => "rgba(229,255,229,1)",
    'pointColor'           => "rgba(204,255,204,1)",
    'pointStrokeColor'     => "#fff",
    'pointHighlightFill'   => "#fff",
    'pointHighlightStroke' => "rgba(220,220,220,1)",
];
$colorRed = [
    'label'                => "Прогноз",
    'fillColor'            => "rgba(220,220,220,0)",
    'strokeColor'          => "rgba(255,229,229,1)",
    'pointColor'           => "rgba(255,204,204,1)",
    'pointStrokeColor'     => "#fff",
    'pointHighlightFill'   => "#fff",
    'pointHighlightStroke' => "rgba(220,220,220,1)",
];
$colorBlue = [
    'label'                => "Прогноз",
    'fillColor'            => "rgba(220,220,220,0)",
    'strokeColor'          => "rgba(229,229,255,1)",
    'pointColor'           => "rgba(204,204,255,1)",
    'pointStrokeColor'     => "#fff",
    'pointHighlightFill'   => "#fff",
    'pointHighlightStroke' => "rgba(220,220,220,1)",
];

\app\assets\Slider\Asset::register($this);




?>

<h1 class="page-header"><?= $this->title ?></h1>

<?php
$d = $item->getField('description', '');
if ($d) {
    echo Html::tag('p', $d);
}

$logo = $item->getField('logo', '');
if ($logo) {
    ?>
    <img src="<?= $logo ?>" class="thumbnail">
<?php
}
?>
<h2 class="page-header">Прошлое</h2>
<?php
$graph3 = new \cs\Widget\ChartJs\Line([
    'width'     => 800,
    'lineArray' => $lineArrayPast,
    'colors'    => [
        $colorGreen,
        $colorRed,
        $colorBlue,
    ],
]);
echo $graph3->run();
$url = Url::to(['cabinet/graph_ajax']);

$timeEnd = time() - 60 * 60 * 24;
$timeStart = $timeEnd - 60 * 60 * 24 * 30 * 6;
$defaultEnd = $timeEnd;
$defaultStart = $defaultEnd - 60*60*24*30;

$this->registerJs(<<<JS
    /**
    *
    * @param f float
    */
    function getDate(f)
    {
        start = new Date();
        start.setTime(parseInt(f) + '000');
        var m = start.getMonth() + 1;
        if (m < 10) m = '0' + m;
        var d = start.getDate();
        if (d < 10) d = '0' + d;

        return start.getFullYear() + '-' + m + '-' + d;
    }

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
    $("#slider").bind("valuesChanged", function(e, data){
        {$graph3->varName}.destroy();
        var start = getDate(data.values.min);
        var end = getDate(data.values.max);
        console.log([start, end ]);
        ajaxJson({
            url: '$url',
            data: {
                'min': start,
                'max': end,
                'id': {$item->getId()},
                'isUseRed': 1,
                'isUseBlue': 1,
                'isUseKurs': 1,
                'y': 1
            },
            success: function(ret) {
                {$graph3->varName} = new Chart(document.getElementById('$graph3->id').getContext('2d')).Line(ret, []);
            }
        });
    });
JS
);
?>
<div class="col-lg-8">
    <div style="margin: 10px 0px 20px 0px;">
        <div id="slider"></div>
    </div>
</div>

<h2 class="page-header row col-lg-12" style="page-break-before: always;">Будущее</h2>
<?php if ($isPaid) { ?>
    <?php

    $graphFuture = new \cs\Widget\ChartJs\Line([
        'width'     => 800,
        'lineArray' => $lineArrayFuture,
        'colors'    => [
            $colorRed,
            $colorBlue,
        ],
    ]);
    echo $graphFuture->run();

    $timeStart = time();
    $timeEnd = $timeStart + 60 * 60 * 24 * 30 * 6;
    $defaultEnd = $timeStart;
    $defaultStart = $timeStart + 60*60*24*30;

    $this->registerJs(<<<JS
    $('#sliderFuture').rangeSlider({
        bounds: {min: {$timeStart}, max: {$timeEnd}},
        formatter: function(val) {
            var d = new Date();
            d.setTime(parseInt(val) + '000');
            var out = d.getDate() + '.' + (d.getMonth() + 1) + '.' + d.getFullYear();

            return out;
        },
        defaultValues:{min: {$defaultStart}, max: {$defaultEnd}}
    });
    $("#sliderFuture").bind("valuesChanged", function(e, data){
        {$graphFuture->varName}.destroy();
        var start = getDate(data.values.min);
        var end = getDate(data.values.max);
        console.log([start, end ]);
        ajaxJson({
            url: '$url',
            data: {
                'min': start,
                'max': end,
                'id': {$item->getId()},
                'isUseRed': 1,
                'isUseBlue': 1,
                'isUseKurs': 1,
                'y': 1
            },
            success: function(ret) {
                {$graphFuture->varName} = new Chart(document.getElementById('$graphFuture->id').getContext('2d')).Line(ret, []);
            }
        });
    });
JS
    );
    ?>

    <div class="col-lg-8">
        <div style="margin: 10px 0px 20px 0px;">
            <div id="sliderFuture"></div>
        </div>
    </div>
<?php } else { ?>
    <div class="form-group">
        <p><span class="label label-danger">График не оплачен</span></p>
    </div>
    <a
        href="<?= Url::to(['cabinet_wallet/add', 'id' => $item->getId()]) ?>"
        class="btn btn-default"
        style="width: 100%"
        >Купить</a>
<?php } ?>
