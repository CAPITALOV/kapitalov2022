<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
$this->title = 'Задачи крон';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('/js/pages/superadmin/cron.js', ['depends' => ['yii\grid\GridViewAsset', 'app\assets\AppAsset']]);

?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <a style="margin: 10px 0px 10px 0px;" class="btn btn-primary" href="<?= Url::to(['superadmin/cron_add'])?>">Добавить задачу</a>

    <?php
    echo GridView::widget($gridViewOptions);
    ?>
</div>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">исполнение скрипта cron</h4>
            </div>
            <div class="modal-body">
                Вывод скрипта:<br>
                <textarea id="configText" rows="20" cols="60"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>
