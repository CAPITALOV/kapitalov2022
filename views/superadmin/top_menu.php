<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
$this->title = 'Верхрее меню';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('/js/pages/superadmin/top_menu.js', ['depends' => ['yii\grid\GridViewAsset']]);
$this->registerCss('.myDragClass { background-color: #cccccc!important; }');

?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <a href="/topMenu/add" class="btn btn-primary" style="margin: 20px 0px 20px 0px; ">Добавить</a>

    <div id="topMenuTable">
    <?php
    echo GridView::widget($gridViewOptions);
    ?>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Конфигурация модуля</h4>
            </div>
            <div class="modal-body">
                Конфигурация:<br>
                <textarea id="configText" rows="20" cols="60"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>


