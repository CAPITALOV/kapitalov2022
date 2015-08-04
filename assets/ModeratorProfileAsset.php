<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;
use Yii;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ModeratorProfileAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];
    public $js = [
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\YiiAsset'
    ];

    public function init() {
        $assetManager = Yii::$app->assetManager;
        parent::init();
        $this->js = [
            ltrim($assetManager->publish('@bower/bootstrap/dist/js/bootstrap.min.js')[1], '/'),
            ltrim($assetManager->publish('@vendor/nnnick/Chart.js/Chart.min.js')[1], '/'),
            ltrim($assetManager->publish('js/pages/moderator/profile.js')[1], '/'),
        ];

        $this->css = [
            ltrim($assetManager->publish('css/moderation/work.css')[1], '/'),
            ltrim($assetManager->publish('css/moderation/profile.css')[1], '/'),
        ];
    }

}
