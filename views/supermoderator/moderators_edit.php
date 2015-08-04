<?php

use app\models\Translator as T;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;


$this->title = T::t('Moderator editing');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

    <div class="alert alert-success">
            <?= T::t('Moderator successfully updated')?>
    </div>

    <?php else: ?>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
                <?= $form->field($model, 'email')->label(T::t('Email')) ?>
                <?= $form->field($model, 'name_first')->label(T::t('First name')) ?>
                <?= $form->field($model, 'name_last')->label(T::t('Last name')) ?>
                <?= $form->field($model, 'rating')->label(T::t('Rating')) ?>

                <div class="form-group">
                    <?= Html::submitButton(T::t('Update'), ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?php endif; ?>
</div>
