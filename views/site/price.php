<?php
/**
 * Created by PhpStorm.
 * User: Дмитрий
 * Date: 29.08.2015
 * Time: 1:41
 */

$this->title = 'Цены';

use yii\helpers\Url;

?>

<!-- Page Heading/Breadcrumbs -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Наши цены</h1>

    </div>
</div>
<!-- /.row -->

<!-- Content Row -->
<div class="row">
    <div class="col-md-offset-2 col-md-4">
        <div class="panel panel-default text-center">
            <div class="panel-heading">
                <h3 class="panel-title">Демо</h3>
            </div>
            <div class="panel-body">
                <span class="price"><sup>$</sup>0</span>
                <span class="period">до конца месяца</span>
            </div>
            <ul class="list-group">
                <li class="list-group-item"><strong>1</strong> Акция</li>
                <li class="list-group-item"><strong>Нет</strong> поддержки</li>
                <li class="list-group-item"><a href="<?= Url::to(['auth/registartion']) ?>" class="btn btn-primary"
                                               style="width:100%;">Получить!</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-primary text-center">
            <div class="panel-heading">
                <h3 class="panel-title">Базовый <span class="label label-success">Оптимальный выбор</span></h3>
            </div>
            <div class="panel-body">
                <span class="price"><sup>$</sup>100</span>
                <span class="period">за месяц</span>
            </div>
            <ul class="list-group">
                <li class="list-group-item"><strong>1</strong> Акция</li>
                <li class="list-group-item"><strong>Есть</strong> поддержка</li>
                <li class="list-group-item"><a href="<?= Url::to(['auth/registartion']) ?>" class="btn btn-primary"
                                               style="width:100%;">Получить!</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="col-md-offset-2 col-md-8">
        <p>Телефон для обратной связи: +7-499-394-27-43</p>
    </div>
</div>
<!-- /.row -->


<!-- Page Content -->
<div class="container">

    <!-- Marketing Icons Section -->
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                Эксклюзивные финансовые услуги анализа движения капиталов
            </h1>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4><i class="fa fa-fw fa-check"></i> Уникальность</h4>
                </div>
                <div class="panel-body">
                    <p>Ваш ориентир в мире капиталов. Экспертная система анализа движения доступная каждому по всему
                        миру </p>
                    <a href="/price" class="btn btn-default">Тарифы</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4><i class="fa fa-fw fa-gift"></i> Гарантировано</h4>
                </div>
                <div class="panel-body">
                    <p>Поставщик инсайдерской информации для участников финансового рынка Европы, России, США и
                        Азии.</p>
                    <a href="/price" class="btn btn-default">Тарифы</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4><i class="fa fa-fw fa-compass"></i> Доступно</h4>
                </div>
                <div class="panel-body">
                    <p>Эксклюзивная финансовая система анализа мировых рынков доступна каждому 24 часа онлайн</p>
                    <a href="/price" class="btn btn-default">Тарифы</a>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->

    <!-- Portfolio Section -->
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header"> Ваш ориентир в огромном мире капиталов</h2>
        </div>
        <hr>
    </div>
    <!-- /.row -->


    <!-- Call to Action Section -->
    <div class="well">
        <div class="row">
            <div class="col-md-8">
                <p>Убедитесь лично. Проверьте прогнозы на основе прошедших дней. До конца месяца вы получаете доступ в
                    систему эксклюзивных финансовых услуг <span
                        style="color: #008000; font-weight: bold;">бесплатно</span>. </p>
            </div>
            <div class="col-md-4">
                <a class="btn btn-lg btn-default btn-block" href="/registration">Подключиться к системе</a>
            </div>
        </div>
    </div>

    <hr>

    <!-- Features Section -->
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Торгуйте зная направления тренда</h2>
        </div>
        <div class="col-md-6">
            <p>Регистрируясь вы получаете доступ в экспертную аналитическую систему для просмотра прогнозов до начала
                следующего календарного месяца.</p>

            <p>После оплаты вы получаете доступ к будушим прогнозам включая первый месяц бесплатного пробного
                использования аналитической системы.</p>

            <p>В тариф включено:</p>
            <ul>
                <li>Абонентская подпискана 30 дней</li>
                <li>Экспертное сопровождение</li>
                <li>Консультация по индивидуальной стратегии абонента</li>
                <li>Просмотр прогноза на один месяц вперед конкретной оплаченной акции</li>
            </ul>
        </div>
        <div class="col-md-6">
            <img class="img-responsive img-thumbnail" src="/images/index/cabinet.gif" alt="">
        </div>
    </div>
    <!-- /.row -->


    <hr>
    <!-- Footer -->
    <footer>
        <div class="row">
            <div class="col-lg-12">
                <p>© 2007 &mdash; 2015 | www.capitalov.com | Эксклюзивные финансовые услуги</p>
            </div>
        </div>
    </footer>

    <!-- /.container -->
</div>