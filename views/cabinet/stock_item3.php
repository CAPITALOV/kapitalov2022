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
$colorViolet = [
    'label'                => "Прогноз",
    'fillColor'            => "rgba(220,220,220,0)",
    'strokeColor'          => "rgba(120,255,255,1)",
    'pointColor'           => "rgba(70,255,255,1)",
    'pointStrokeColor'     => "#fff",
    'pointHighlightFill'   => "#fff",
    'pointHighlightStroke' => "rgba(220,220,220,1)",
];

\app\assets\Slider\Asset::register($this);

$url = Url::to(['cabinet/graph_ajax']);

$this->registerJsFile('/js', ['depends' => ['yii\web\JqueryAsset',]]);

?>

<hr class="clearfix" style="color:#489F46; background-color:#489F46; height:3px; margin-top: 0px;margin-bottom: 0px;">

<div class="container-fluid" style="background-color:#ededed; height:90px; display: -webkit-box; display: -moz-box; display: -ms-flexbox; display: -webkit-flex; display: flex;align-items: center;">
    <div>
        <img src="/images/collapseIcon.png" style="height:35px;padding-right:35px;padding-left:10px;">
    </div>
    <div style="float:left;">
            <img src="/images/icon-info.png" style="height:30px;padding-right:35px;">
     </div>

    <div class="col-md-auto" style="float:right; ">
        <img src="/images/icon-index.png" style="height:35px;padding-right:10px;">
        <div  class="text-nowrap" style="vertical-align:middle; font-size:16px; font-weight: bold; display:inline-block;">Просмотр индексов капиталов</div>
    </div>
    <div style="height: 40px; margin: 0 20px; border-left: 1px solid #f2f2f2; border-right: 1px solid #ffffff; float:left;"></div>
    
</div>

<br/>

<div class="center-block" style="width:800px">
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
    <div>
        <div style="margin: 10px 0px 20px 0px; width: 800px;">
            <div id="sliderFuture"></div>
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
</div>

<br><hr><br>

 <?php $model = new \app\models\Form\StockItem3(); $form = ActiveForm::begin(['id' => 'contact-form2',]); ?>
<div class="container">
        <div  style="float:left; ">
            <img src="/images/History_icon-280x280.png" style="height:35px;padding-right:10px;">
            <div style="vertical-align:middle; font-size:18px; font-weight: bold; display:inline-block;">Просмотр истории индексов</div>
        </div>
        <div style="height: 40px; margin: 0 20px; border-left: 1px solid #f2f2f2; border-right: 1px solid #ffffff; float:left;"></div>

        <div style="float:left;">
            <h5 style="float:left;">Инфо</h5>
            <img src="/images/icon-info.png" style="height:40px;padding-left:10px;">
        </div>

        <div style="height: 40px; margin: 0 20px; border-left: 1px solid #f2f2f2; border-right: 1px solid #ffffff; float:left;"></div>

        <div class="col-md-auto" style="float:left; vertical-align:middle;">
    <div style="float:left; padding-right:15px;">
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
            <div>Красная линия закрытия торгов</div>
        </div>
        <div style="height: 40px; margin: 0 20px; border-left: 1px solid #f2f2f2; border-right: 1px solid #ffffff; float:left;"></div>
        <div class="col-md-auto" style="float:left; ">


    <div style="float:left; padding-right:15px;">
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
    <div>
            Синяя линия взвешенной цены
        </div>
        </div>
        <div style="height: 40px; margin: 0 20px; border-left: 1px solid #f2f2f2; border-right: 1px solid #ffffff; float:left;"></div>
        <div class="col-md-auto" style="float:left; ">

    <div style="float:left; padding-right:15px;">
        <?= $form->field($model, 'isKurs')->widget('cs\Widget\CheckBox2\CheckBox', ['options' => ['data-onstyle' => 'success']])->label('', ['class' => 'hide']) ?>
    </div>
    <div>
        Зеленая линия прошедших торгов
    </div>

        </div>
</div>

<?php ActiveForm::end() ?>

<div class="center-block" style="width:800px">
<div class="row col-lg-12">
    <?php
    $graph3 = new \cs\Widget\ChartJs\Line([
        'width'     => 800,
        'lineArray' => $lineArrayPast,
        'colors'    => [
            $colorGreen,
            $colorRed,
            $colorBlue,
            $colorViolet,
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

<div class="row">
    <center>
        <div class="row col-lg-8">
            <div style="margin: 10px 0px 20px 0px;width: 800px;" class="text-center">
                <div id="slider" style=""></div>
            </div>
        </div>
    </center>
</div>
</div>

<br><hr><br>


<h2>Свечи</h2>
<div class="center-block" style="width:860px">
<?php
$today = new DateTime();
$end = $today->format('Y-m-d');
$start = $today->sub(new DateInterval('P1Y'))->format('Y-m-d');

echo \cs\Widget\ECharts\CandleStick1::widget([
    'width' => 860,
    'name'  => $item->getField('name', ''),
    'data'  => \app\models\StockKurs::query(['stock_id' => $item->getId()])
        ->select([
            'date',
            'open',
            'close',
            'low',
            'high',
            'volume',
        ])
        ->andWhere(['between', 'date', $start, $end])
        ->orderBy(['date' => SORT_ASC])
        ->all()
]) ?>

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