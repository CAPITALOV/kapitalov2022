<?php

$this->title = 'Услуги';
?>
<h1 class="page-header">Услуги</h1>

<div class="row">
    <div class="col-lg-8">
        <p>
            Система аккумулирует в себе прогнозы и рекомендации профессиональных участников рынка по ценным бумагам в виде временного графика с датами разворота тренда эмитента компании.
        </p>
        <p>
            Система Kapitalov применяется во многих сферах информационного бизнеса. Система прошла тестовые испытания и показала высокий уровень результативности.
        </p>
        <p>
            Используется трейдерами, аналитиками, кризис менеджерами, банкирами, финансистами, инвесторами, меценатами, владельцами бизнесов, СМИ и людьми следящими за рынком.
        </p>
        <p>
            Сервис доступен круглосуточно.

        </p>


    </div>
    <div class="col-lg-4">
        <img class="img-responsive img-thumbnail" src="/images/index/cabinet.gif" alt="">
    </div>
</div>

<div class="row">

    <div class="col-lg-4 col-lg-offset-4">
        <?php if (Yii::$app->user->isGuest) { ?>
            <a class="btn btn-primary btn-lg" href="<?= \yii\helpers\Url::to(['auth/login']) ?>" style="width: 100%;background-color: #aa719f;border: none;border-radius: 24px;">Войти в систему</a>
        <?php } else { ?>
            <a class="btn btn-primary btn-lg" href="<?= \yii\helpers\Url::to(['cabinet/index']) ?>" style="width: 100%;background-color: #aa719f;border: none;border-radius: 24px;">Войти в систему</a>
        <?php } ?>
    </div>

</div>


