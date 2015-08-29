<?php

/** @var $this \yii\web\View */

use app\models\Translator as T;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>

<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only"><?= T::t('Hide navigation') ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-left">
                <li><a class="navbar-brand" href="<?= Url::to(['landing/index']) ?>">Capitalov.com</a></li>
                <li><span class="navbar-brand">/</span></li>
                <li><a class="navbar-brand" href="<?= Url::to(['cabinet/index']) ?>">Личный кабинет</a></li>
            </ul>


            <ul class="nav navbar-nav navbar-right" style="margin-right: 20px;">
                <?php
                $this->registerCss('.ui-autocomplete {z-index: 9999;}');
                // форма поиска
                $form = ActiveForm::begin([
                    'enableClientValidation' => false,
                    'options'                => [
                        'style' => 'margin-bottom: 0px;',
                        'class' => "navbar-form navbar-left",
                        'role'  => "search",
                    ],
                ]);
                $url = Url::to(['cabinet/search_stock_autocomplete']);
                echo $form
                    ->field(new \app\models\Form\Search(), 'searchText', ['inputOptions' => ['placeholder' => 'Поиск']])
                    ->label('Поиск', ['class' => 'hide'])
                    ->widget(\yii\jui\AutoComplete::classname(), [
                        'clientOptions' => [
                            'source' => new  \yii\web\JsExpression(<<<JS
function (request, response) {
    ajaxJson({
        url: '{$url}',
        data: {
        term: request.term
        },
        success: function(ret) {
            response(ret);
        }
    });
  }
JS
                            ),
                            'select' => new \yii\web\JsExpression(<<<JS
function(event, ui) {
    var stockId = ui.item.id;
    window.location = '/stockList3/' + stockId;
}
JS
                            ),
                        ],
                        'options'       => [
                            'class'       => 'form-control',
                            'placeholder' => 'Поиск',
                        ]
                    ]);
                ActiveForm::end();
                ?>
                <li class="dropdown">
                    <a
                        href="#"
                        class="dropdown-toggle"
                        data-toggle="dropdown"
                        aria-expanded="false"
                        role="button"
                        style="padding: 5px 10px 5px 10px;"
                        >
                        <?= Html::img(Yii::$app->user->identity->getAvatar(), [
                            'height' => '40px',
                            'class'  => 'img-circle'
                        ]) ?>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?= Url::to(['cabinet/profile']) ?>"><i class="glyphicon glyphicon-cog"
                                                                          style="padding-right: 5px;"></i>Мой
                                профиль</a></li>
                        <li><a href="<?= Url::to(['cabinet/password_change']) ?>"><i
                                    class="glyphicon glyphicon-asterisk" style="padding-right: 5px;"></i>Сменить
                                пароль</a></li>
                        <li class="divider"></li>
                        <li><a href="<?= Url::to(['money/history']) ?>"><i
                                    class="glyphicon glyphicon-rub" style="padding-right: 5px;"></i>История платежей</a></li>
                        <li class="divider"></li>
                        <li><a href="<?= Url::to(['auth/logout']) ?>" data-method="post"><i
                                    class="glyphicon glyphicon-off" style="padding-right: 5px;"></i>Выйти</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
