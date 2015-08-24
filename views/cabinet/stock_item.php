<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this \yii\web\View */
/* @var $item  \app\models\Stock */
/* @var $lineArrayKurs  array */
/* @var $lineArrayRed  array */
/* @var $lineArrayBlue  array */
/* @var $isPaid  bool опачена ли эта акция? */

$this->title = $item->getField('name');

$model = new \app\models\Form\StockItemGraph();

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
<?php

$graph3 = new \cs\Widget\ChartJs\Line([
    'width'     => 800,
    'lineArray' => $lineArrayKurs,
]);
echo $graph3->run();
$url = Url::to(['cabinet/graph_ajax']);
$this->registerJs(<<<JS
    $('#buttonRecalculate').click(function() {
        if ($('#stockitemgraph-datemin').val() == '') {
            showInfo('Нужно заполнить дату начала');
            return;
        }
        if ($('#stockitemgraph-datemax').val() == '') {
            showInfo('Нужно заполнить дату начала');
            return;
        }
        {$graph3->varName}.destroy();
        var start = $('#stockitemgraph-datemin').val();
        var end = $('#stockitemgraph-datemax').val();
        start = start.substring(6,10) + '-' + start.substring(3,5) + '-' + start.substring(0,2);
        end = end.substring(6,10) + '-' + end.substring(3,5) + '-' + end.substring(0,2);
        ajaxJson({
            url: '$url',
            data: {
                'min': start,
                'max': end,
                'id': {$item->getId()}
            },
            success: function(ret) {
                {$graph3->varName} = new Chart(document.getElementById('$graph3->id').getContext('2d')).Line(ret.kurs, []);
            }
        })
    })
JS
);
?>

<?php if (!$isPaid) { ?>
    <hr>

    <a
        href="<?= Url::to(['cabinet_wallet/add', 'id' => $item->getId()]) ?>"
        class="btn btn-default"
        style="width: 100%;"
        >Купить</a>
<?php } ?>

<div class="row">
    <div class="col-lg-6">
        <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
        <?= $model->field($form, 'dateMin') ?>
        <?= $model->field($form, 'dateMax') ?>
        <hr>
        <div class="form-group">
            <?= Html::button('Показать', [
                'class' => 'btn btn-default',
                'name'  => 'contact-button',
                'style' => 'width:100%',
                'id'    => 'buttonRecalculate',
            ]) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>



<h2 class="page-header">Экспорт</h2>

<div class="col-lg-6">
    <div style="margin: 10px 0px 20px 0px;">
        <div id="slider"></div>
    </div>
    <button class="btn btn-default" style="width: 100%;">Экспортировать</button>
</div>
