<?php

namespace cs\Widget\ChartJs;

use cs\services\Security;
use Yii;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

/*

*/


class Line extends Object
{
    public $id;
    public $width  = 400;
    public $height = 400;
    /**
     * @var array
     * [
     *     'name' => string,
     *     'data' => []
     * ]
     */
    public $lineArray;

    public $chartOptions = [];
    public $globalOptions = [];

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
        echo Html::tag('canvas', null, [
            'id'     => $this->id,
            'width'  => $this->width,
            'height' => $this->height,
        ]);
        $this->getClientOptions();
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
        $options = ArrayHelper::merge($this->globalOptions, $this->chartOptions);
        $optionsJson = Json::encode($options);
        if (count($options) > 0){
            $js[] = "Chart.defaults.global = {$optionsJson};";
        }

        $js[] = <<<JS
        // Get the context of the canvas element we want to select
var ctx = document.getElementById('{$this->id}').getContext("2d");
var myNewChart = new Chart(ctx).PolarArea(data);
var data = {
    labels: ["January", "February", "March", "April", "May", "June", "July"],
    datasets: [
        {
            label: "My First dataset",
            fillColor: "rgba(220,220,220,0.2)",
            strokeColor: "rgba(220,220,220,1)",
            pointColor: "rgba(220,220,220,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data: [65, 59, 80, 81, 56, 55, 40]
        },
        {
            label: "My Second dataset",
            fillColor: "rgba(151,187,205,0.2)",
            strokeColor: "rgba(151,187,205,1)",
            pointColor: "rgba(151,187,205,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
            data: [28, 48, 40, 19, 86, 27, 90]
        }
    ]
};
var myLineChart = new Chart(ctx).Line(data, options);
JS
;
        Yii::$app->view->registerJs(join("\n", $js));
    }
}
