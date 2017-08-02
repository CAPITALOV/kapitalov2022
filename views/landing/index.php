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
        <li data-target="#myCarousel" data-slide-to="3"></li>
    </ol>

    <!-- Wrapper for slides -->
    <div class="carousel-inner">
        <div class="item active">
            <div class="fill" style="background-image:url('/images/index/promo0.jpg');"></div>
            <div class="carousel-caption">
            </div>
        </div>
        <div class="item">
            <div class="fill" style="background-image:url('/images/index/promo1.png');"></div>
            <div class="carousel-caption">
            </div>
        </div>
        <div class="item">
            <div class="fill" style="background-image:url('/images/index/promo2.png');"></div>
            <div class="carousel-caption">
            </div>
        </div>
        <div class="item">
            <div class="fill" style="background-image:url('/images/index/promo3.png');"></div>
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

<hr>

<div class="container">

<!-- Footer -->
<footer>
    <div class="row">
        <div class="row">
            <div class="col-lg-4 col-lg-offset-4">
                <p class="text-center"><a class="btn btn-primary btn-lg" href="/login" style="width: 100%;background-color: #aa719f;border: none;border-radius: 24px;">Войти в систему</a></p>
            </div>
        </div>
        <div class="col-lg-12">
            <p>© 2002 — <?= date('Y') ?> | www.kapitalov.com | Национальное Агентство Капиталов | <?= $this->render('../blocks/telephone') ?></p>
        </div>
    </div>
</footer>
</div>