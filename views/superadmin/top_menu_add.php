<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use app\models\User;

/** @var $this yii\web\View */
/** @var $form yii\bootstrap\ActiveForm */
/** @var $model app\models\Form\TopMenu */

$this->title = 'Добавление элемента верхнего меню';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

        <div class="alert alert-success">
            Элемент меню успешно добавлен
        </div>


    <?php else: ?>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin([
                    'id' => 'contact-form',
                    'options' => ['class' => 'form-horizontal'],
                    'fieldConfig' => [
                        'template' => "{label}\n<div class=\"col-lg-7\">{input}</div>\n<div class=\"col-lg-5\">{error}</div>",
                        'labelOptions' => ['class' => 'col-lg-4 control-label'],
                    ],
                ]); ?>
                <?= $form->field($model, 'menu') ?>
                <?= $form->field($model, 'title')->label('Заголовок') ?>
                <?= $form->field($model, 'link')->label('Ссылка') ?>
                <?= $form->field($model, 'linktype') ?>
                <?= $form->field($model, 'linkid') ?>
                <?= $form->field($model, 'target') ?>
                <?= $form->field($model, 'published')->checkbox() ?>                
                <?= $form->field($model, 'access_list') ?>                
				<?= $form->field($model, 'html_id')->hint('Селектор на элемент')->label('Селектор на элемент (html_id)') ?>
                <div class="form-group">
                    <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>

    <?php endif; ?>
</div>
