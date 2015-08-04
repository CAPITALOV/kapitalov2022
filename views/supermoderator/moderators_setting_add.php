<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Translator as T;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = T::t('Moderator new setting');
?>
<div class="row">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'msetting-form', 'action' => Url::to(['supermoderator/moderators_setting_add']), 'method' => 'post']); ?>
            <?= $form->field($model, 'key') ?>
            <?= $form->field($model, 'value') ?>
            <div class="form-group">
                <?= Html::submitButton(T::t('Create'), ['class' => 'btn btn-primary', 'name' => 'setting-create-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
