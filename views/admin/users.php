<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('/js/pages/admin/users.js', ['depends' => ['yii\grid\GridViewAsset']]);
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    echo GridView::widget($gridViewOptions);
    ?>
</div>
