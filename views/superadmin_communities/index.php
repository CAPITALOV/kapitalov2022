<?php
/**
 * @var array $newItems
 * @var array $gridViewOptions
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
$this->title = 'Категории';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('/js/pages/superadmin_communities/index.js', ['depends' => [
    'yii\web\JqueryAsset',
    'app\assets\TableDnd',
]]);
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>
    <a href="<?= Url::to(['superadmin_communities/add'])?>" class="btn btn-primary">Добавить</a>

    <?php
    echo GridView::widget($gridViewOptions);
    ?>
</div>

