<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use app\models\User;

/** @var $this yii\web\View */
/** @var $form yii\bootstrap\ActiveForm */
/** @var $model app\models\Form\Component */

$this->title = 'Инсталлировать компонент';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

    <div class="alert alert-success">
        Компонет успешно добавлен
    </div>



    <?php else: ?>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
            <?= $form->field($model, 'title') ?>
            <?= $form->field($model, 'link') ?>
            <?= $form->field($model, 'config')->textarea() ?>
            <?= $form->field($model, 'internal')->checkbox() ?>
            <?= $form->field($model, 'author') ?>
            <?= $form->field($model, 'version') ?>
            <?= $form->field($model, 'system')->checkbox() ?>
            <div class="form-group">
                    <?= Html::submitButton('Инсталлировать', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?php endif; ?>
</div>
