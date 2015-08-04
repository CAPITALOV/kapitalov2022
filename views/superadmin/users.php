<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
$this->title = 'Пользователи админки';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('/js/pages/superadmin/users.js', ['depends' => ['yii\grid\GridViewAsset']]);
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <a href="/adminUsers/add" class="btn btn-primary" style="margin: 20px 0px 20px 0px; ">Добавить</a>

    <?php
    echo GridView::widget($gridViewOptions);
    ?>
</div>
