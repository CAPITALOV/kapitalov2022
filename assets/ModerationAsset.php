<?php

namespace app\assets;

use yii\web\AssetBundle;
use Yii;

class ModerationAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/moderation/work.css'
    ];
    public $js;
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\grid\GridViewAsset',
        'app\assets\AppAsset'
    ];

    public function init() {
        $assetManager = Yii::$app->assetManager;
        parent::init();
        $this->js = [
            ltrim($assetManager->publish('js/pages/moderator/work.js')[1], '/'),
        ];
    }

}
