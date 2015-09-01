<?php

use yii\helpers\Url;
use yii\helpers\Html;

/** @var  $this \yii\web\View */

?>
<!-- Navigation -->
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Capitalov.com</a>
            <ul class="nav navbar-nav navbar-left">
                <li>
                    <a href="javascript:void(0)">+7-(499)-394-27-43</a>
                </li>
                </ul>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="<?= Url::to(['site/about']) ?>">О продукте</a>
                </li>
                <li>
                    <a href="<?= Url::to(['site/price']) ?>">Тарифы</a>
                </li>
<!--                <li>-->
<!--                    <a href="/startbootstrap-modern-business-1.0.3/services.html">Services</a>-->
<!--                </li>-->
<!--                <li>-->
<!--                    <a href="/startbootstrap-modern-business-1.0.3/contact.html">Contact</a>-->
<!--                </li>-->
<!--                <li class="dropdown">-->
<!--                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Portfolio <b class="caret"></b></a>-->
<!--                    <ul class="dropdown-menu">-->
<!--                        <li>-->
<!--                            <a href="/startbootstrap-modern-business-1.0.3/portfolio-1-col.html">1 Column Portfolio</a>-->
<!--                        </li>-->
<!--                        <li>-->
<!--                            <a href="/startbootstrap-modern-business-1.0.3/portfolio-2-col.html">2 Column Portfolio</a>-->
<!--                        </li>-->
<!--                        <li>-->
<!--                            <a href="/startbootstrap-modern-business-1.0.3/portfolio-3-col.html">3 Column Portfolio</a>-->
<!--                        </li>-->
<!--                        <li>-->
<!--                            <a href="/startbootstrap-modern-business-1.0.3/portfolio-4-col.html">4 Column Portfolio</a>-->
<!--                        </li>-->
<!--                        <li>-->
<!--                            <a href="portfolio-item.html">Single Portfolio Item</a>-->
<!--                        </li>-->
<!--                    </ul>-->
<!--                </li>-->
<!--                <li class="dropdown">-->
<!--                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Blog <b class="caret"></b></a>-->
<!--                    <ul class="dropdown-menu">-->
<!--                        <li>-->
<!--                            <a href="blog-home-1.html">Blog Home 1</a>-->
<!--                        </li>-->
<!--                        <li>-->
<!--                            <a href="blog-home-2.html">Blog Home 2</a>-->
<!--                        </li>-->
<!--                        <li>-->
<!--                            <a href="blog-post.html">Blog Post</a>-->
<!--                        </li>-->
<!--                    </ul>-->
<!--                </li>-->
<!--                <li class="dropdown">-->
<!--                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Other Pages <b class="caret"></b></a>-->
<!--                    <ul class="dropdown-menu">-->
<!--                        <li>-->
<!--                            <a href="/startbootstrap-modern-business-1.0.3/full-width.html">Full Width Page</a>-->
<!--                        </li>-->
<!--                        <li>-->
<!--                            <a href="/startbootstrap-modern-business-1.0.3/sidebar.html">Sidebar Page</a>-->
<!--                        </li>-->
<!--                        <li>-->
<!--                            <a href="/startbootstrap-modern-business-1.0.3/faq.html">FAQ</a>-->
<!--                        </li>-->
<!--                        <li>-->
<!--                            <a href="/startbootstrap-modern-business-1.0.3/404.html">404</a>-->
<!--                        </li>-->
<!--                        <li>-->
<!--                            <a href="/startbootstrap-modern-business-1.0.3/pricing.html">Pricing Table</a>-->
<!--                        </li>-->
<!--                    </ul>-->
<!--                </li>-->
                <?php if (Yii::$app->user->isGuest) { ?>
                    <li>
                        <a href="<?= Url::to(['auth/login']) ?>">Вход</a>
                    </li>
                    <li>
                        <a href="<?= Url::to(['auth/registration']) ?>">Регистрация</a>
                    </li>
                    <li>
                        <a href="<?= Url::to(['auth/password_recover']) ?>">Восстановить пароль</a>
                    </li>
                <?php } else {?>
                    <li>
                        <!-- Split button -->
                        <div class="btn-group" style="margin-top: 9px;" id="loginBarButton">
                            <a href="<?= Url::to(['cabinet/index']) ?>" class="btn btn-default" id="modalLogin"><i class="glyphicon glyphicon-user" style="padding-right: 5px;"></i>Кабинет</a>
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
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
                        </div>
                    </li>
                <?php }?>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>