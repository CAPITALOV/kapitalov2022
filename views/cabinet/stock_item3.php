<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this \yii\web\View */
/* @var $item  \app\models\Stock */
/* @var $lineArrayPast  array */
/* @var $lineArrayFuture  array */
/* @var $lineArrayCandels  array */
/* @var $isPaid  bool опачена ли эта котировка? */

$this->title = $item->getField('name');

?>


<div class="container">
    <div class="col-md-auto" style="float:left; ">
        <img src="/images/icon-index.png" style="height:35px;padding-right:10px;">

        <div class="text-nowrap"
             style="vertical-align:middle; font-size:18px; font-weight: bold; display:inline-block;">Просмотр индексов
            капиталов
        </div>
    </div>
    <div
        style="height: 40px; margin: 0 20px; border-left: 1px solid #f2f2f2; border-right: 1px solid #ffffff; float:left;"></div>
    <div style="float:left;">
        <h5 style="float:left;">Инфо</h5>
        <img src="/images/icon-info.png" style="height:40px;padding-left:10px;">
    </div>
</div>

<br/>

<div class="center-block" style="width:800px">
    <?php if ($isPaid) { ?>
        <div class="center-block" style="width:860px">
            <?php if (!is_null($lineArrayFuture)) { ?>
                <?= \cs\Widget\AmCharts\CandleStick::widget([
                    'height'       => 200,
                    'lineArray'    => $lineArrayFuture,
                    'chartOptions' => [
                        "type"           => "serial",
                        "theme"          => "light",
                        "legend"         => [
                            "useGraphSettings" => true
                        ],
                        "valueAxes"      => [
                            [
                                "id"            => "v1",
                                "axisColor"     => "#FF0000",
                                "axisThickness" => 2,
                                "gridAlpha"     => 0,
                                "axisAlpha"     => 1,
                                "position"      => "left"
                            ],
                            [
                                "id"            => "v2",
                                "axisColor"     => "#0000ff",
                                "axisThickness" => 2,
                                "gridAlpha"     => 0,
                                "axisAlpha"     => 1,
                                "position"      => "right"
                            ],
                        ],
                        "graphs"         => [
                            [
                                "valueAxis"             => "v1",
                                "lineColor"             => "#FF0000",
                                "bullet"                => "round",
                                "bulletBorderThickness" => 1,
                                "hideBulletsCount"      => 30,
                                "title"                 => "красный",
                                "valueField"            => "red",
                                "fillAlphas"            => 0
                            ],
                            [
                                "valueAxis"             => "v2",
                                "lineColor"             => "#0000ff",
                                "bullet"                => "round",
                                "bulletBorderThickness" => 1,
                                "hideBulletsCount"      => 30,
                                "title"                 => "синий",
                                "valueField"            => "blue",
                                "fillAlphas"            => 0
                            ],
                        ],
                        "chartScrollbar" => [],
                        "chartCursor"    => [
                            "cursorPosition" => "mouse"
                        ],
                        "categoryField"  => "date",
                        "categoryAxis"   => [
                            "parseDates"       => true,
                            "axisColor"        => "#DADADA",
                            "minorGridEnabled" => true
                        ],
                        "export"         => [
                            "enabled"  => true,
                            "position" => "bottom-right"
                        ]
                    ],
                ]) ?>
            <?php } else { ?>
                <div class="alert alert-danger">
                    Нет данных
                </div>
            <?php } ?>

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

<br>
<hr><br>

<div class="container">
    <div style="float:left; ">
        <img src="/images/History_icon-280x280.png" style="height:35px;padding-right:10px;">

        <div style="vertical-align:middle; font-size:18px; font-weight: bold; display:inline-block;">Просмотр истории
            индексов
        </div>
    </div>
    <div
        style="height: 40px; margin: 0 20px; border-left: 1px solid #f2f2f2; border-right: 1px solid #ffffff; float:left;"></div>

    <div style="float:left;">
        <h5 style="float:left;">Инфо</h5>
        <img src="/images/icon-info.png" style="height:40px;padding-left:10px;">
    </div>
</div>

<br/>

<div class="center-block" style="width:800px">
    <div class="center-block" style="width:860px">
        <?php if (!is_null($lineArrayPast)) { ?>
            <?= \cs\Widget\AmCharts\CandleStick::widget([
                'height'       => 200,
                'lineArray'    => $lineArrayPast,
                'chartOptions' => [
                    "type"           => "serial",
                    "theme"          => "light",
                    "legend"         => [
                        "useGraphSettings" => true
                    ],
                    "valueAxes"      => [
                        [
                            "id"            => "v1",
                            "axisColor"     => "#FF0000",
                            "axisThickness" => 2,
                            "gridAlpha"     => 0,
                            "axisAlpha"     => 1,
                            "position"      => "left"
                        ],
                        [
                            "id"            => "v2",
                            "axisColor"     => "#0000ff",
                            "axisThickness" => 2,
                            "gridAlpha"     => 0,
                            "axisAlpha"     => 1,
                            "position"      => "right"
                        ],
                        [
                            "id"            => "v3",
                            "axisColor"     => "#00ff00",
                            "axisThickness" => 2,
                            "gridAlpha"     => 0,
                            "offset"        => 50,
                            "axisAlpha"     => 1,
                            "position"      => "left"
                        ]
                    ],
                    "graphs"         => [
                        [
                            "valueAxis"             => "v1",
                            "lineColor"             => "#FF0000",
                            "bullet"                => "round",
                            "bulletBorderThickness" => 1,
                            "hideBulletsCount"      => 30,
                            "title"                 => "красный",
                            "valueField"            => "red",
                            "fillAlphas"            => 0
                        ],
                        [
                            "valueAxis"             => "v2",
                            "lineColor"             => "#0000ff",
                            "bullet"                => "round",
                            "bulletBorderThickness" => 1,
                            "hideBulletsCount"      => 30,
                            "title"                 => "синий",
                            "valueField"            => "blue",
                            "fillAlphas"            => 0
                        ],
                        [
                            "valueAxis"             => "v3",
                            "lineColor"             => "#00ff00",
                            "bullet"                => "round",
                            "bulletBorderThickness" => 1,
                            "hideBulletsCount"      => 30,
                            "title"                 => "зеленый",
                            "valueField"            => "kurs",
                            "fillAlphas"            => 0
                        ]
                    ],
                    "chartScrollbar" => [],
                    "chartCursor"    => [
                        "cursorPosition" => "mouse"
                    ],
                    "categoryField"  => "date",
                    "categoryAxis"   => [
                        "parseDates"       => true,
                        "axisColor"        => "#DADADA",
                        "minorGridEnabled" => true
                    ],
                    "export"         => [
                        "enabled"  => true,
                        "position" => "bottom-right"
                    ]
                ],
            ]) ?>
        <?php } else { ?>
            <div class="alert alert-danger">
                Нет данных
            </div>
        <?php } ?>
    </div>


</div>

<br>
<hr><br>


<h2 class="page-header">Свечи</h2>
<div class="center-block" style="width:860px">
    <?= \cs\Widget\AmCharts\CandleStick::widget([
        'height'       => 200,
        'lineArray'    => $lineArrayCandels,
        'chartOptions' => [
            "type"           => "serial",
            "theme"          => "light",
            "dataDateFormat" => 'YYYY-MM-DD',
            "valueAxes"      => [[
                "position" => "left"
            ]],
            "graphs"         => [
                [
                    "id"                 => "g1",
                    "balloonText"        => "Открытие:<b>[[open]]</b><br>min:<b>[[low]]</b><br>max:<b>[[high]]</b><br>Закрытие:<b>[[close]]</b><br>",
                    "closeField"         => "close",
                    "fillColors"         => "#7f8da9",
                    "highField"          => "high",
                    "lineColor"          => "#7f8da9",
                    "lineAlpha"          => 1,
                    "lowField"           => "low",
                    "fillAlphas"         => 0.9,
                    "negativeFillColors" => "#db4c3c",
                    "negativeLineColor"  => "#db4c3c",
                    "openField"          => "open",
                    "title"              => "Price:",
                    "type"               => "candlestick",
                    "valueField"         => "close"
                ]
            ],
            "chartScrollbar" => [
                "graph"           => "g1",
                "graphType"       => "line",
                "scrollbarHeight" => 30
            ],
            "chartCursor"    => [
                "valueLineEnabled"        => true,
                "valueLineBalloonEnabled" => true
            ],
            "categoryField"  => "date",
            "categoryAxis"   => [
                "parseDates" => true
            ],
            "export"         => [
                "enabled"  => true,
                "position" => "bottom-right"
            ]
        ]
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