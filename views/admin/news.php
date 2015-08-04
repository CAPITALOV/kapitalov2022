<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
$this->title = 'Новостные ленты';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('/js/pages/admin/news.js', ['depends' => ['yii\grid\GridViewAsset']]);

?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <a href="/news/add" class="btn btn-primary" style="margin: 20px 0px 20px 0px; ">Добавить</a>

    <?php
    echo GridView::widget($gridViewOptions);
    ?>
</div>



