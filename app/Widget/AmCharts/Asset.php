<?php

/**
 * @link      http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license   http://www.yiiframework.com/license/
 */

namespace cs\Widget\AmCharts;

use yii\web\AssetBundle;
use Yii;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since  2.0
 */
class Asset extends AssetBundle
{
    public $sourcePath = '@vendor/amcharts/amstock3/amcharts';
    public $css      = [
        'default.css',
    ];
    public $js       = [
        'amcharts.js',
        'serial.js',
        'lang/ru.js',
        'themes/light.js',
        'amstock.js',
    ];
    public $depends  = [
    ];

    public function init()
    {
    }
}
