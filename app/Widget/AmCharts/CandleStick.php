<?php
/**
 * Created by PhpStorm.
 * User: Дмитрий
 * Date: 16.09.2015
 * Time: 21:59
 */

namespace cs\Widget\AmCharts;

use cs\services\Security;
use yii\base\Object;
use yii\helpers\Html;
use yii\helpers\Json;

class CandleStick extends Object
{
    public $id;
    public $width  = 400;
    public $height = 200;

    public $enableExport;

    /** @var  string название переменной JS */
    public $varName;
    /**
     * @var array
     * [[
     *     'date' => 'yyyy-mm-dd'
     *     'open' => float
     *     'high' => float
     *     'low' => float
     *     'close' => float
     * ],...]
     */
    public $lineArray;

    /**
     * @var \yii\web\JsExpression дополнительный js
     */
    public $js;

    public $chartOptions  = [];

    /**
     * Initializes the widget.
     */
    public function init()
    {
        if (!$this->id) {
            $this->id = 'w_' . Security::generateRandomString();
        }
        $this->varName = 'graph_' . Security::generateRandomString();
    }

    public function run()
    {
        $this->registerClientScript();


        return Html::tag('div', null, [
            'id' => $this->id,
        ]);
    }

    public static function widget($config = [])
    {
        $class = new self($config);
        return $class->run();
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        $am = Asset::register(\Yii::$app->view);
        if ($this->js) {
            \Yii::$app->view->registerJs($this->js);
        }
        if ($this->enableExport) {
            \Yii::$app->view->registerJsFile($am->baseUrl . '/plugins/export/export.js', [
                'depends' => ['cs\Widget\AmCharts\Asset'],
            ]);
            \Yii::$app->view->registerCssFile($am->baseUrl . '/plugins/export/export.css');
        }

        $optionsJson = $this->getClientOptions($am);
        \Yii::$app->view->registerJs(<<<JS
var {$this->varName} = AmCharts.makeChart( "{$this->id}", {$optionsJson} );

{$this->varName}.addListener( "rendered", zoomChart_{$this->varName} );
zoomChart_{$this->varName}();

// this method is called when chart is first inited as we listen for "dataUpdated" event
function zoomChart_{$this->varName}() {
    // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
    {$this->varName}.zoomToIndexes( {$this->varName}.dataProvider.length - 10, {$this->varName}.dataProvider.length - 1 );
}
JS
);
//, ($this->enableExport)? \yii\web\View::POS_HEAD : \yii\web\View::POS_READY);
        \Yii::$app->view->registerCss(<<<CSS
#{$this->id} {
	width	: 100%;
	height	: {$this->height}px;
}
CSS
);
    }

    /**
     * @param \yii\web\AssetBundle $am
     *
     * @return string json
     */
    protected function getClientOptions($am)
    {
        $options = $this->chartOptions;
        if ($this->enableExport) {
            $options['export'] = [
                'enabled' => true,
                'libs' => [
                    'path' => $am->baseUrl . '/plugins/export/libs/',
                ]
            ];
        }
        if (!isset($options['dataProvider'])) {
            $options['dataProvider'] = $this->lineArray;
        }

        return Json::encode($options);
    }
}