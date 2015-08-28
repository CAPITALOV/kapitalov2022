<?php

namespace app\controllers;


class LandingController extends \cs\base\BaseController
{
    public $layout = 'landing';

    public function actionIndex()
    {
        return $this->render();
    }
} 