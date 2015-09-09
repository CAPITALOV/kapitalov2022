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
    'strokeColor'          => "rgba(120,255,120,1)",
    'pointColor'           => "rgba(70,255,70,1)",
    'pointStrokeColor'     => "#fff",
    'pointHighlightFill'   => "#fff",
    'pointHighlightStroke' => "rgba(220,220,220,1)",
];
$colorRed = [
    'label'                => "Прогноз",
    'fillColor'            => "rgba(220,220,220,0)",
    'strokeColor'          => "rgba(255,120,120,1)",
    'pointColor'           => "rgba(255,70,70,1)",
    'pointStrokeColor'     => "#fff",
    'pointHighlightFill'   => "#fff",
    'pointHighlightStroke' => "rgba(220,220,220,1)",
];
$colorBlue = [
    'label'                => "Прогноз",
    'fillColor'            => "rgba(220,220,220,0)",
    'strokeColor'          => "rgba(120,120,255,1)",
    'pointColor'           => "rgba(70,70,255,1)",
    'pointStrokeColor'     => "#fff",
    'pointHighlightFill'   => "#fff",
    'pointHighlightStroke' => "rgba(220,220,220,1)",
];

\app\assets\Slider\Asset::register($this);

$url = Url::to(['cabinet/graph_ajax']);



?>

<h1 class="page-header"><?php
    $logo = $item->getField('logo', '');
    if ($logo) {
        ?>
        <img src="<?= $logo ?>" width="50">
    <?php
    }
    ?><?= $this->title ?></h1>

<?php
$d = $item->getField('description', '');
if ($d) {
    ?>
    <div class="row col-lg-12">
        <p><?= $d ?></p>
    </div>
<?php
}
?>


<h2 class="page-header">Индексы капиталов</h2>
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
    $timeEnd = $timeStart + 60 * 60 * 24 * 30;
    $defaultEnd = $timeStart;
    $defaultStart = $timeEnd;

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
    var functionOnChangeFuture = function(e, data) {
        {$graphFuture->varName}.destroy();
        var start = getDate(data.values.min);
        var end = getDate(data.values.max);
        ajaxJson({
            url: '$url',
            data: {
                'min': start,
                'max': end,
                'id': {$item->getId()},
                'isUseRed': 1,
                'isUseBlue': 1,
                'isUseKurs': 0,
                'y': 2
            },
            success: function(ret) {
                    {$graphFuture->varName} = new Chart(document.getElementById('$graphFuture->id').getContext('2d')).Line(ret, []);
            }
        });
    };
    $("#sliderFuture").bind("valuesChanged", functionOnChangeFuture);

JS
    );
    ?>

    <div class="row col-lg-12">
        <div class="row col-lg-8">
            <div style="margin: 10px 0px 20px 0px; width: 800px;">
                <div id="sliderFuture"></div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="row col-lg-12">
        <div class="form-group">
            <p><span class="label label-danger">График не оплачен</span></p>
        </div>
    </div>
    <a
        href="<?= Url::to(['cabinet_wallet/add', 'id' => $item->getId()]) ?>"
        class="btn btn-default"
        style="width: 100%"
        >Купить</a>
<?php } ?>



<h2 class="page-header">Предыстория торгов</h2>

<div class="col-lg-12 row">
    <?php
    $model = new \app\models\Form\StockItem3();
    $form = ActiveForm::begin([
        'id' => 'contact-form2',
    ]);
    ?>
    <div class="col-sm-2">
        Красная линия закрытия торгов
    </div>
    <div class="col-sm-2">
        <?php
        $this->registerJs(<<<JS
    $('#linkInfoRed').click(function(){
        $('#myModalRed').modal('show');
    });
JS
        );
        ?>
        <?= $form->field($model, 'isRed')->widget('cs\Widget\CheckBox2\CheckBox', ['options' => ['data-onstyle' => 'danger']])->label('', ['class' => 'hide']) ?>
    </div>


    <div class="col-sm-2">
        Синяя линия взвешенной цены
    </div>

    <div class="col-sm-2">
        <?php
        $this->registerJs(<<<JS
    $('#linkInfoBlue').click(function(){
        $('#myModalBlue').modal('show');
    });
JS
        );
        ?>
        <?= $form->field($model, 'isBlue')->widget('cs\Widget\CheckBox2\CheckBox', ['options' => ['data-onstyle' => 'primary']])->label('', ['class' => 'hide']) ?>
    </div>
    <div class="col-sm-2">
        Зеленая линия прошедших торгов
    </div>
    <div class="col-sm-2">
        <?= $form->field($model, 'isKurs')->widget('cs\Widget\CheckBox2\CheckBox', ['options' => ['data-onstyle' => 'success']])->label('', ['class' => 'hide']) ?>
    </div>

    <?php ActiveForm::end() ?>
</div>

<div class="row col-lg-12">
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

    $timeEnd = time() - 60 * 60 * 24;
    $timeStart = $timeEnd - 60 * 60 * 24 * 30 * 6;
    $defaultEnd = $timeEnd;
    $defaultStart = $defaultEnd - 60 * 60 * 24 * 30;

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
    var functionOnChange = function(e, data)  {
        {$graph3->varName}.destroy();
        var values = $('#slider').rangeSlider('values');
        var start = getDate(values.min);
        var end = getDate(values.max);
        console.log([start, end ]);
        ajaxJson({
            url: '$url',
            data: {
                'min': start,
                'max': end,
                'id': {$item->getId()},
                'isUseRed': $('#stockitem3-isred').is(':checked')? 1 : 0,
                'isUseBlue': $('#stockitem3-isblue').is(':checked')? 1 : 0,
                'isUseKurs': $('#stockitem3-iskurs').is(':checked')? 1 : 0,
                'y': 1
            },
            success: function(ret) {
                {$graph3->varName} = new Chart(document.getElementById('$graph3->id').getContext('2d')).Line(ret, []);
            }
        });
    };
    $("#slider").bind("valuesChanged", functionOnChange);
    $('#stockitem3-isred').change(functionOnChange);
    $('#stockitem3-isblue').change(functionOnChange);
    $('#stockitem3-iskurs').change(functionOnChange);
JS
    );
    ?>
</div>

<div class="row col-lg-12">
    <center>
        <div class="row col-lg-8">
            <div style="margin: 10px 0px 20px 0px;width: 800px;" class="text-center">
                <div id="slider" style=""></div>
            </div>
        </div>
    </center>
</div>


<div class="modal fade" id="myModalBlue" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Относительный ценовой генератор</h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModalRed" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Цена по закрытию торгов</h4>
            </div>
            <div class="modal-body">
                <p>
                    ...
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>