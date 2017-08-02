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


<!-- Marketing Icons Section -->
<div class="row">
    <div class="col-lg-8">

        <h2 class="page-header">
            Кто мы
        </h2>

        <p>Национальное Агентство Капиталов является поставщиком инсайдерской информации для участников
            финансового рынка Европы, России, Азии и США.</p>

        <h2 class="page-header">
            Что мы предлагаем
        </h2>

        <p>
            Мы предоставляем графический прогноз изменения движения цены котировок на месяц. Мы анализируем
            нестабильность на валютно-финансовых рынках и представляем это графическим способом.
        </p>
        <p>
            Например: Юкос, Золото, Нефть, Периоды кризиса. <a href="<?= \yii\helpers\Url::to(['site/about']) ?>">Подробнее</a>
        </p>


        <h2 class="page-header">
            Как мы делаем расчет
        </h2>
        <p>
            Мы применяем математический анализ поведения людей, их выбор, определяющий их торговую стратегию.
            Наш экспертный анализ построен на базе искусственного интелекта и нейро-компьютерных вычислений.
        </p>


        <h2 class="page-header">
            Для чего это нужно
        </h2>

        <p>Для того чтобы заработать, зная когда продавать и когда покупать.</p>

        <p>Например: IPO ВТБ Банк, Тинькофф, Юкос.</p>


        <h2 class="page-header">
            Для кого
        </h2>

        <p>Для
            трейдеров,
            аналитиков,
            кризис менеджеров,
            банкиров,
            финансистов,
            инвесторов,
            меценатов,
            владельцев бизнеса,
            СМИ
            и людей следящих за рынком (пифов).</p>



        <h2 class="page-header">
            Что нас отличает от других
        </h2>

        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Уникальность</h4>
                </div>
                <div class="panel-body" style="min-height: 150px;">
                    <p>Прогноз будущего движения рынка показывается графически с указанием точных дат изменения
                        курса.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Доступно</h4>
                </div>
                <div class="panel-body" style="min-height: 150px;">
                    <p>Наш сервис доступен круглосуточно.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Гарантия</h4>
                </div>
                <div class="panel-body" style="min-height: 150px;">
                    <p>Мы работаем с 2001 г. и по статистике сбываемость прогнозов более 70%.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<h2 class="page-header">
    Что вам надо сделать
</h2>

<div class="row">
    <div class="col-lg-6">
        <ul>
            <li>Зарегистрироваться по <a href="<?= \yii\helpers\Url::to(['auth/registration']) ?>">ссылке</a>.</li>
            <li>Войти в личный кабинет. Для просмотра примера вам доступна одна котировка.</li>
            <li>Посмотреть как это работает на реальном рабочем примере.</li>
            <li>Заказать и оплатить котировку, которая вас интересует на любом рынке.</li>
        </ul>

        <hr>
        <a
                href="<?= \yii\helpers\Url::to(['auth/registration']) ?>"
                class="btn btn-primary btn-lg"
                style="width: 100%"
        >Регистрация</a>

        <h2 class="page-header">Регистрация и тарифы</h2>

        <p>Регистрируясь вы получаете доступ в экспертную аналитическую систему для просмотра прогнозов до начала
            следующего календарного месяца.</p>

        <p>После оплаты вы получаете доступ к будущим прогнозам включая первый месяц бесплатного пробного
            использования аналитической системы.</p>

        <p>В тариф включено:</p>
        <ul>
            <li>Абонентская подписка на 30 дней</li>
            <li>Экспертное сопровождение</li>
            <li>Консультация по индивидуальной стратегии абонента</li>
            <li>Просмотр прогноза на один месяц вперёд конкретной оплаченной акции</li>
        </ul>
    </div>

</div>

