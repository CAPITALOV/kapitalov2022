<?php

$this->title = 'Национальное Агентство Капиталов';
?>
<h1 class="page-header">О продукте</h1>

<div class="row">
    <div class="col-lg-8">
        <p>
            +7 (999) 898-0442<br>
            +7 (926) 190-4992<br>
            info@kapitalov.com
        </p>
    </div>
    <div class="col-lg-4">
        <img class="img-responsive img-thumbnail" src="/images/index/cabinet.gif" alt="">
    </div>
</div>

<div class="row">
    <div class="col-lg-4 col-lg-offset-4">
        <?php if (Yii::$app->user->isGuest) { ?>
            <a class="btn btn-primary btn-lg" href="<?= \yii\helpers\Url::to(['auth/login']) ?>" style="width: 100%;background-color: #aa719f;border: none;border-radius: 24px;">Войти</a>
        <?php } else { ?>
            <a class="btn btn-primary btn-lg" href="<?= \yii\helpers\Url::to(['cabinet/index']) ?>" style="width: 100%;background-color: #aa719f;border: none;border-radius: 24px;">Войти</a>
        <?php } ?>
    </div>
</div>