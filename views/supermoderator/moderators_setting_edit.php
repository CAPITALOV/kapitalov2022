<?php

use app\models\ContactForm;
use app\models\Translator as T;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model ContactForm */

$this->title = T::t('Update moderator settings');
?>
<div class="row">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('setting_updated')): ?>

        <div class="alert alert-success">
            <?= T::t('Setting was successfully updated') ?>
        </div>

    <?php else: ?>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'msetting-form', 'action' => Url::to(['supermoderator/moderators_setting_edit', 'id' => Yii::$app->request->get('id')]), 'method' => 'post']); ?>
                <?= $form->field($model, 'key')->label(T::t('Key')) ?>
                <?= $form->field($model, 'value')->label(T::t('Value')) ?>
                <div class="form-group">
                    <?= Html::submitButton(T::t('Update'), ['class' => 'btn btn-primary', 'name' => 'setting-create-button']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>

    <?php endif; ?>
</div>
