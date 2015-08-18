<?php

namespace cs\Widget\ChartJs;

use cs\services\Security;
use Yii;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\VarDumper;

/*
Рисует линейный график
http://www.chartjs.org/docs/#line-chart

*/


class Line extends Object
{
    public $id;
    public $width  = 400;
    public $height = 400;
    /**
     * @var array
     * [
     * 'x' => ["January", "February", "March", "April", "May", "June", "July"],
     * 'y' => [
     * [65, 59, 80, 81, 56, 55, 40],
     * [null, null, 48, 40, 19, 86, 27],
     * ]
     * ]
     */
    public $lineArray;

    public $chartOptions = [];
    public $globalOptions = [];

    public $colors = [
        [
            'label' => "My First dataset",
            'fillColor' => "rgba(220,220,220,0.2)",
            'strokeColor' => "rgba(220,220,220,1)",
            'pointColor' => "rgba(220,220,220,1)",
            'pointStrokeColor' => "#fff",
            'pointHighlightFill' => "#fff",
            'pointHighlightStroke' => "rgba(220,220,220,1)",
        ],
        [
            'label' => "My Second dataset",
            'fillColor' => "rgba(151,187,205,0.2)",
            'strokeColor' => "rgba(151,187,205,1)",
            'pointColor' => "rgba(151,187,205,1)",
            'pointStrokeColor' => "#fff",
            'pointHighlightFill' => "#fff",
            'pointHighlightStroke' => "rgba(151,187,205,1)",
        ],
    ];
    /**
     * Initializes the widget.
     */
    public function init()
    {
        if (!$this->id) {
            $this->id = 'w_' . Security::generateRandomString();
        }
    }

    public function run()
    {
        $this->registerClientScript();
        $this->getClientOptions();
        return Html::tag('canvas', null, [
            'id'     => $this->id,
            'width'  => $this->width,
            'height' => $this->height,
        ]);
    }

    public static function widget($options)
    {
        $item = new static($options);
        $item->run();
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        Asset::register(Yii::$app->view);
    }

    /**
     * @return array the options
     */
    protected function getClientOptions()
    {
        $optionsJson = Json::encode($this->globalOptions);
        if (count($this->globalOptions) > 0){
            $js[] = "Chart.defaults.global = {$optionsJson};";
        }
        $optionsJson = Json::encode($this->chartOptions);
        $c = 0;
        $datasets = [];
        foreach($this->lineArray['y'] as $line) {
            $data = $this->colors[$c];
            $data['data'] = $line;
            $datasets[] = $data;
            $c++;
        }
        $data = [
            'labels' => $this->lineArray['x'],
            'datasets' => $datasets,
        ];
        $dataJson = Json::encode($data);
        $js[] = <<<JS
var options = {$optionsJson};
var ctx = document.getElementById('{$this->id}').getContext('2d');
var data = {$dataJson};
var myLineChart = new Chart(ctx).Line(data, options);
JS
;
        $js[] = "";
        Yii::$app->view->registerJs(join("\n", $js));
    }
}
