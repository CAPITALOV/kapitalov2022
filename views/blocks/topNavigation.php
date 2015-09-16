<?php

/** @var $this \yii\web\View */
/** @var $item \app\models\Stock */

use app\models\Translator as T;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

if (Yii::$app->requestedRoute == 'cabinet/stock_item3') {
    $item = \app\models\Stock::find($_GET['id']);
    /**
     * var $userStock
     */
    $userStock = \app\models\UserStock::find(['stock_id' => $item->getId(), 'user_id' => Yii::$app->user->id]);
    $date = Yii::$app->formatter->asDate($userStock->getField('date_finish'));
}

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
                <li>
                     <a class="navbar-brand" href="/" style="padding: 5px 10px 5px 10px;"><img
                                        src="<?= Yii::$app->getAssetManager()->getBundle('app\assets\LayoutSite\Asset')->baseUrl ?>/images/capitalovlogo2.png"
                                        class="siteLayoutLogo" style="height:40px; margin:0px"></a>
                </li>

                <li>
                <!-- // TODO: https://app.asana.com/0/49453878855328/49759457524215 -->

                </li>

                <li class="dropdown<?php if (Yii::$app->controller->id == 'cabinet') { echo(' active');} ?>">
                                    <a href="#"
                                        class="dropdown-toggle"
                                        data-toggle="dropdown"
                                        aria-expanded="false"
                                        role="button">
                                        Выбор котировки
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
                                        <li role="presentation" class="dropdown-header">Заказать</li>
                                        <li><a href="<?= Url::to(['cabinet_wallet/add1']) ?>">Национальный рынок</a></li>
                                        <li><a href="<?= Url::to(['cabinet_wallet/add2']) ?>">Зарубежный рынок</a></li>
                                    </ul>
                </li>

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
                <li><a href="" style="height:50px; margin:0;">Связаться с экспертом <img src="/images/message.png" style="height:28px; margin:0;"/></a></li>
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
