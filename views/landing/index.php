<?php

$this->title = 'Capitalov.com Эксклюзивные финансовые услуги';

$design = \app\models\Design::find(1);
?>

<div class="container">
    <div class="col-lg-12">
        <center>
            <h1 class="page-header">Эксклюзивные услуги анализа движения рынка капиталов</h1>
        </center>
    </div>
</div>


<!-- Header Carousel -->
<header id="myCarousel" class="carousel slide">
    <!-- Indicators -->
    <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="1"></li>
        <li data-target="#myCarousel" data-slide-to="2"></li>
    </ol>

    <!-- Wrapper for slides -->
    <div class="carousel-inner">
        <div class="item active">
            <div class="fill" style="background-image:url('<?= $design->getImg(1) ?>');"></div>
            <div class="carousel-caption">
            </div>
        </div>
        <div class="item">
            <div class="fill" style="background-image:url('<?= $design->getImg(2) ?>');"></div>
            <div class="carousel-caption">
            </div>
        </div>
        <div class="item">
            <div class="fill" style="background-image:url('<?= $design->getImg(3) ?>');"></div>
            <div class="carousel-caption">
            </div>
        </div>
    </div>

    <!-- Controls -->
    <a class="left carousel-control" href="#myCarousel" data-slide="prev">
        <span class="icon-prev"></span>
    </a>
    <a class="right carousel-control" href="#myCarousel" data-slide="next">
        <span class="icon-next"></span>
    </a>
</header>

<!-- Page Content -->
<div class="container">

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
                        <!--                        <p>Уникальность заключается в грфическом представлении анализа измения цены котировки.</p>-->
                        <p>Прогноз будущего движения рынка показывается графически с указанием точных дат изменения
                            курса.</p>
                        <!--                        <p>Уникальный графический прогноз будущего движения рынка.</p>-->
                        <!--                        <p>Анализ и прогноз движения трендов, акций и биржевых инструментов, строится в виде графиков по данным математического и нейрокомпьютерного моделирования, которые работают на идеях искусственного интеллекта компании CAPITALOV.</p>-->
                    </div>
                </div>
            </div>
            <!--            <div class="col-md-3">-->
            <!--                <div class="panel panel-default">-->
            <!--                    <div class="panel-heading">-->
            <!--                        <h4>Открытость</h4>-->
            <!--                    </div>-->
            <!--                    <div class="panel-body">-->
            <!--                        <p>Мы открыты к общению и диалогу.</p>-->
            <!--                    </div>-->
            <!--                </div>-->
            <!--            </div>-->
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
        <div class="col-lg-4">
            <img src="/images/index/snapshot.png" width="100%" style="margin-top: 50px;border-color: black;" class="thumbnail">
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

        <div class="col-lg-6">
            <img class="img-responsive img-thumbnail" src="/images/index/cabinet.gif" alt="">
        </div>
    </div>


    <!-- /.row -->

    <hr>

    <!-- Footer -->
    <footer>
        <div class="row">
            <div class="col-lg-12">
                <p>© 2007 — 2015 | www.capitalov.com | Национальное Агентство Капиталов | <?= $this->render('../blocks/telephone') ?></p>
            </div>
        </div>
    </footer>

</div>
<!-- /.container -->