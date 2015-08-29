<?php

$this->title = 'Шедевр изобилия';
?>
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
            <div class="fill" style="background-image:url('/images/index/1.jpg');"></div>
            <div class="carousel-caption">
            </div>
        </div>
        <div class="item">
            <div class="fill" style="background-image:url('/images/index/2.jpg');"></div>
            <div class="carousel-caption">
            </div>
        </div>
        <div class="item">
            <div class="fill" style="background-image:url('/images/index/3.jpg');"></div>
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
        <div class="col-lg-12">
            <h1 class="page-header">
                Добро пожаловать в изобилие
            </h1>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4><i class="fa fa-fw fa-check"></i> Полный контроль</h4>
                </div>
                <div class="panel-body">
                    <p>CAPITALOV.com является одним из ведущих поставщиков инсайдерской информации для участников финансового рынка Европы, России и США и продолжает развивать новые бизнес-направления в интересах исключительно своих клиентов</p>
                    <a href="<?= \yii\helpers\Url::to(['site/price'])?>" class="btn btn-default">Подробнее</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4><i class="fa fa-fw fa-gift"></i> Гарантировано</h4>
                </div>
                <div class="panel-body">
                    <p>Вероятность сбывания прогноза превышает 90%. Мы хранители информации. Мы - будущее.</p>
                    <a href="<?= \yii\helpers\Url::to(['site/price'])?>" class="btn btn-default">Подробнее</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4><i class="fa fa-fw fa-compass"></i> Очень дешево</h4>
                </div>
                <div class="panel-body">
                    <p>Регистрируясь в кабинете вы получаете просмотр прогнозов до начала следующего календарного месяца.

                        Если вы оплатили сколько-то месяцев, вы получаете в открытом доступе прогнозы на этот месяц бесплатно до его окончания и полных n оплаченных месяцев.</p>
                    <a href="<?= \yii\helpers\Url::to(['site/price'])?>" class="btn btn-default">Подробнее</a>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->

    <!-- Portfolio Section -->
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Дополнительные преимущества</h2>
        </div>
        <div class="col-md-4 col-sm-6">
            <a href="portfolio-item.html">
                <img class="img-responsive img-portfolio img-hover" src="http://placehold.it/700x450" alt="">
            </a>
        </div>
        <div class="col-md-4 col-sm-6">
            <a href="portfolio-item.html">
                <img class="img-responsive img-portfolio img-hover" src="http://placehold.it/700x450" alt="">
            </a>
        </div>
        <div class="col-md-4 col-sm-6">
            <a href="portfolio-item.html">
                <img class="img-responsive img-portfolio img-hover" src="http://placehold.it/700x450" alt="">
            </a>
        </div>
        <div class="col-md-4 col-sm-6">
            <a href="portfolio-item.html">
                <img class="img-responsive img-portfolio img-hover" src="http://placehold.it/700x450" alt="">
            </a>
        </div>
        <div class="col-md-4 col-sm-6">
            <a href="portfolio-item.html">
                <img class="img-responsive img-portfolio img-hover" src="http://placehold.it/700x450" alt="">
            </a>
        </div>
        <div class="col-md-4 col-sm-6">
            <a href="portfolio-item.html">
                <img class="img-responsive img-portfolio img-hover" src="http://placehold.it/700x450" alt="">
            </a>
        </div>
    </div>
    <!-- /.row -->

    <!-- Features Section -->
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Личный кабинет</h2>
        </div>
        <div class="col-md-6">
            <p>Регистрируясь в кабинете вы получаете просмотр прогнозов до начала следующего календарного месяца.</p>
            <p>Если вы оплатили сколько-то месяцев, вы получаете в открытом доступе прогнозы на этот месяц бесплатно до его окончания и полных n оплаченных месяцев.</p>
            <p>В тариф включено:</p>
            <ul>
                <li>сопровождение</li>
                <li>консультация по стратегии клиента</li>
                <li>просмотр прогноза на один месяц вперед конкретной оплаченной акции</li>
            </ul>
        </div>
        <div class="col-md-6">
            <img class="img-responsive img-thumbnail" src="/images/index/cabinet.png" alt="">
        </div>
    </div>
    <!-- /.row -->

    <hr>

    <!-- Call to Action Section -->
    <div class="well">
        <div class="row">
            <div class="col-md-8">
                <p>До конца месяца вы получаете для просмотра прогнозов всех акции <span style="color: #008000; font-weight: bold;">бесплатно</span>. Убедитесь лично в действенности нашего инструмента. Проверьте прогнозы на основе прошедших дней.</p>
            </div>
            <div class="col-md-4">
                <a class="btn btn-lg btn-default btn-block" href="<?= \yii\helpers\Url::to(['auth/registration']) ?>">Подключиться</a>
            </div>
        </div>
    </div>

    <hr>

    <!-- Footer -->
    <footer>
        <div class="row">
            <div class="col-lg-12">
                <p>&copy; www.capitalov.com 2015</p>
            </div>
        </div>
    </footer>

</div>
<!-- /.container -->