<?php

/** @var $this \yii\web\View */

use app\models\Translator as T;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->registerJs(<<<JS
    $('#layoutCabinetLinkBack').tooltip({
        placement: 'right',
        animation: true,
        delay: 300
    });
JS
);
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
                <li><a class="navbar-brand" href="<?= Url::to(['landing/index']) ?>" id="layoutCabinetLinkBack" title="Назад на главный сайт"><span class="glyphicon glyphicon-menu-left"></span></a></li>
                <li><a href="<?= Url::to(['cabinet/index']) ?>">Личный кабинет</a></li>
                <li<?php if (Url::to(['cabinet_chat/index']) == Url::current()) { echo(' class="active"');} ?>><a href="<?= Url::to(['cabinet_chat/index']) ?>">Обратная связь</a></li>
                <?php if (Yii::$app->user->identity->isAdmin()) { ?>
                    <li class="dropdown<?php if (Yii::$app->controller->id == 'superadmin_stock') { echo(' active');} ?>">
                        <a
                            href="#"
                            class="dropdown-toggle"
                            data-toggle="dropdown"
                            aria-expanded="false"
                            role="button"
                            >
                            Админ
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?= Url::to(['superadmin_requests/index']) ?>">Заявки на оплату</a></li>
                            <li><a href="<?= Url::to(['superadmin/referal']) ?>">Пирамида изобилия</a></li>
                            <li><a href="<?= Url::to(['superadmin_stock/index']) ?>">Котировки</a></li>
                        </ul>
                    </li>
                <?php } ?>
                <?php if (Yii::$app->user->identity->isRole(\app\models\UserRole::ROLE_DESIGNER)) { ?>
                    <li class="dropdown<?php if (Yii::$app->controller->id == 'designer') { echo(' active');} ?>">
                        <a
                            href="#"
                            class="dropdown-toggle"
                            data-toggle="dropdown"
                            aria-expanded="false"
                            role="button"
                            >
                            Дизайн
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?= Url::to(['designer/landing']) ?>">Главная страница</a></li>

                        </ul>
                    </li>
                <?php } ?>
                <li class="dropdown<?php if (Yii::$app->controller->id == 'cabinet') { echo(' active');} ?>">
                    <a
                        href="#"
                        class="dropdown-toggle"
                        data-toggle="dropdown"
                        aria-expanded="false"
                        role="button"
                        >
                        Котировки
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?= Url::to(['cabinet/index']) ?>">Все</a></li>
                        <li class="divider"></li>
                        <li role="presentation" class="dropdown-header">Оплаченные</li>
                        <?php foreach(\app\models\Stock::getPaid()->all() as $item) { ?>
                            <li><a href="<?= Url::to(['cabinet/stock_item3', 'id' => $item['id']]) ?>"><?= $item['name'] ?></a></li>
                        <?php } ?>
                        <li class="divider"></li>
                        <li role="presentation" class="dropdown-header">Не оплаченные</li>
                        <?php

                        foreach(\app\models\Stock::getNotPaid()->all() as $item) { ?>
                            <li><a href="<?= Url::to(['cabinet/stock_item3', 'id' => $item['id']]) ?>"><?= $item['name'] ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            </ul>


            <ul class="nav navbar-nav navbar-right" style="margin-right: 20px;">
<!--                --><?php
//                $this->registerCss('.ui-autocomplete {z-index: 9999;}');
//                // форма поиска
//                $form = ActiveForm::begin([
//                    'enableClientValidation' => false,
//                    'options'                => [
//                        'style' => 'margin-bottom: 0px;',
//                        'class' => "navbar-form navbar-left",
//                        'role'  => "search",
//                    ],
//                ]);
//                $url = Url::to(['cabinet/search_stock_autocomplete']);
//                echo $form
//                    ->field(new \app\models\Form\Search(), 'searchText', ['inputOptions' => ['placeholder' => 'Поиск']])
//                    ->label('Поиск', ['class' => 'hide'])
//                    ->widget(\yii\jui\AutoComplete::classname(), [
//                        'clientOptions' => [
//                            'source' => new  \yii\web\JsExpression(<<<JS
//function (request, response) {
//    ajaxJson({
//        url: '{$url}',
//        data: {
//        term: request.term
//        },
//        success: function(ret) {
//            response(ret);
//        }
//    });
//  }
//JS
//                            ),
//                            'select' => new \yii\web\JsExpression(<<<JS
//function(event, ui) {
//    var stockId = ui.item.id;
//    window.location = '/stockList3/' + stockId;
//}
//JS
//                            ),
//                        ],
//                        'options'       => [
//                            'class'       => 'form-control',
//                            'placeholder' => 'Поиск',
//                        ]
//                    ]);
//                ActiveForm::end();
//                ?>
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
                        <li><a href="<?= Url::to(['cabinet/change_email']) ?>"><i
                                    class="glyphicon glyphicon-asterisk" style="padding-right: 5px;"></i>Сменить
                                логин/email</a></li>
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
