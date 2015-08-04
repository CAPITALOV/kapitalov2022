<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use app\models\Template;
use yii\helpers\ArrayHelper;
use app\models\Form\Module;

/** @var $this yii\web\View */
/** @var $form yii\bootstrap\ActiveForm */
/** @var $model app\models\Form\ModuleBind */

$this->title = 'Редактирование модуля';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs(<<<JSSSS
$('.buttonDelete').click(function() {
    if (confirm('Подтвердите удаление')) {
        var trObj = $(this).parent().parent();
        ajaxJson({
            url: '/modules/{$model->module_id}/extraPosition/delete',
            type: 'post',
            data: {
                id: $(this).data('id')
            },
            success: function(ret) {
                trObj.remove();
            }
        });
    }
});
$('.buttonUpdate').click(function() {
    var rowId = $(this).data('id');
    var obj = $(this);
    ajaxJson({
        url: '/modules/{$model->module_id}/extraPosition/update',
        type: 'post',
        data: {
            id: rowId,
            menu_id: $('#menu_id-' + rowId).val(),
            position: $('#position-' + rowId).val()
        },
        success: function(ret) {
            obj.attr('disabled','disabled');
        }
    });
});
JSSSS
);

?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>
        <div class="alert alert-success">
            Успешно сохранено.
        </div>
    <?php else: ?>

        <h2>Все</h2>
        <div class="row">
            <table class="table table-striped table-bordered">
                <tr>
                    <th>id</th>
                    <th>menu_id</th>
                    <th>position</th>
                    <th>Обновить</th>
                    <th>Удалить</th>
                </tr>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= Html::tag('select', Html::renderSelectOptions($row['menu_id'], \app\models\Form\ModuleBind::getMenuList()), [
                                'class' => 'form-control',
                                'id' => 'menu_id-'.$row['id'],
                            ])  ?>
                        </td>
                        <td><?= Html::tag('select', Html::renderSelectOptions($row['position'], Template::getPositionList2()), [
                                'class' => 'form-control',
                                'id' => 'position-'.$row['id'],
                            ])  ?>
                        </td>
                        <td><button class="btn btn-primary buttonUpdate" data-id="<?= $row['id'] ?>">Обновить</button></td>
                        <td><button class="btn btn-primary buttonDelete" data-id="<?= $row['id'] ?>">Удалить</button></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <h2>Добавить</h2>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
                <?= $form->field($model, 'menu_id')->dropDownList(\app\models\Form\ModuleBind::getMenuList()) ?>
                <?= $form->field($model, 'position')->dropDownList(Template::getPositionList2()) ?>

                <div class="form-group">
                    <?= Html::submitButton('Добавить', [
                        'class' => 'btn btn-primary',
                        'name'  => 'contact-button'
                    ]) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>

    <?php endif; ?>
</div>
