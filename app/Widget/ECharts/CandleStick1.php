<?php

namespace cs\Widget\ECharts;

use cs\services\Security;
use Yii;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\VarDumper;

/*
Рисует финансовые свечки
http://echarts.baidu.com/doc/doc-en.html

*/


class CandleStick1 extends Object
{
    public $id;
    public $width  = 400;
    public $height = 400;

    /** @var  string название переменной JS */
    public $varName;
    public $name;

    /**
     * @var array
     * [
     *     'x' => ["January", "February", "March", "April", "May", "June", "July"],
     *     'y' => [
     *               [
     *                 [
     *                    'open' => float,
     *                    'low' => float,
     *                    'high' => float,
     *                    'close' => float,
     *                    'volume' => float,
     *                 ],
     *                 ...],
     *               [
     *                 [
     *                    'open' => float,
     *                    'low' => float,
     *                    'high' => float,
     *                    'close' => float,
     *                    'volume' => float,
     *                 ],...
     *               ],
     *            ]
     * ]
     */
    public $lineArray;

    /**
     * Отсортированные данные
     * @var array
     * [
     *                 [
     *                    'data' => 'yyyy-mm-dd',
     *                    'open' => float,
     *                    'low' => float,
     *                    'high' => float,
     *                    'close' => float,
     *                    'volume' => float,
     *                 ],...
     *
     * ]
     */
    public $data;

    public $options = [];

    /**
     * Initializes the widget.
     */
    public function init()
    {
        if (!$this->id) {
            $this->id = 'w_' . Security::generateRandomString();
        }
        $this->varName = 'graph_' . Security::generateRandomString();
        $x = [];
        $y = [];
        if ($this->data) {
            foreach($this->data as $item) {
                if (!(is_null($item['open']) && is_null($item['close']) && is_null($item['low']) && is_null($item['high']))) {
                    $x[] = (new \DateTime($item['date']))->format('d.m.y');
                    unset($item['date']);
                    unset($item['volume']);
                    $y[] = [
                        $item['open'],
                        $item['high'],
                        $item['low'],
                        $item['close'],
                    ];
                }
            }
            $this->lineArray = [
                'x' => $x,
                'y' => [
                    $y
                ],
            ];
        }

    }

    public function run()
    {
        $this->registerClientScript();

        return Html::tag('div', null, [
            'id'    => $this->id,
            'style' => "height:{$this->height}px; width: {$this->width}px;"
        ]);
    }

    public static function widget($options = [])
    {
        $item = new static($options);

        return $item->run();
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        \app\assets\ECharts\Asset::register(Yii::$app->view);

        $x = Json::encode($this->lineArray['x']);
        $y = [];
        foreach($this->lineArray['y'] as $i) {
            $y[] = [
                'name' => 'Дата',
                'type' => 'k',
                'data' => $i,
            ];
        }
        $y = Json::encode($y);
        Yii::$app->view->registerJs(<<<JS
            var myChart = echarts.init(document.getElementById('{$this->id}'));
            var options = {
    title : {
        text: ''
    },
    tooltip : {
        trigger: 'axis',
        formatter: function (params) {
            var res = [];
            res.push(params[0].seriesName + ': ' + params[0].name);
            res.push('<table style="margin-top:10px;">');
            res.push('<tr>');
                res.push('<td style="padding-right:10px;">');
                    res.push('открытие:');
                res.push('</td>');
                res.push('<td>');
                    res.push(params[0].value[0]);
                res.push('</td>');
            res.push('</tr>');
            res.push('<tr>');
                res.push('<td style="padding-right:10px;">');
                    res.push('закрытие:');
                res.push('</td>');
                res.push('<td>');
                    res.push(params[0].value[3]);
                res.push('</td>');
            res.push('</tr>');
            res.push('<tr>');
                res.push('<td style="padding-right:10px;">');
                    res.push('max:');
                res.push('</td>');
                res.push('<td>');
                    res.push(params[0].value[1]);
                res.push('</td>');
            res.push('</tr>');
            res.push('<tr>');
                res.push('<td style="padding-right:10px;">');
                    res.push('min:');
                res.push('</td>');
                res.push('<td>');
                    res.push(params[0].value[2]);
                res.push('</td>');
            res.push('</tr>');
            res.push('</table>');

            return res.join('');
        }
    },
    legend: {
        data:['{$this->name}']
    },
    toolbox: {
        show : true,
        feature : {
            mark : {show: false},
            dataZoom : {show: true,
            title : {
            dataZoom : 'Увеличить',
            dataZoomReset : 'Сбросить'
        }
        },
            dataView : {show: false, readOnly: false},
            magicType: {show: false, type: ['line', 'bar']},
            restore : {show: true,
                title: 'обновить'
                },
            saveAsImage : {
                show: true,
                title: 'Сохранить'
            }
        }
    },
    dataZoom : {
        show : true,
        realtime: true,
        start : 50,
        end : 100
    },
    xAxis : [
        {
            type : 'category',
            boundaryGap : true,
            axisTick: {onGap:false},
            splitLine: {show:false},
            data : {$x}
        }
    ],
    yAxis : [
        {
            type : 'value',
            scale:true,
            boundaryGap: [0.01, 0.01]
        }
    ],
    series : {$y}
};

            myChart.setOption(options);
JS
        );

    }

    /**
     * @return array the options
     */
    protected function getClientOptions()
    {
        return [];
    }

}
