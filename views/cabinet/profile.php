<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use app\models\UnionCategory;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model cs\base\BaseForm */

$this->title = 'Редактирование профиля';
$this->params['breadcrumbs'][] = $this->title;



?>

<h1 class="page-header"><?= Html::encode($this->title) ?></h1>

<div class="col-lg-8">
    <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

        <div class="alert alert-success">
            Успешно обновлено.
        </div>

    <?php else: ?>

        <?php $form = ActiveForm::begin([
            'id'      => 'contact-form',
            'options' => ['enctype' => 'multipart/form-data'],
            'layout'  => 'horizontal',
        ]); ?>
        <?= $model->field($form, 'name_org') ?>
        <?= $model->field($form, 'name_first') ?>
        <?= $model->field($form, 'name_last') ?>
        <?= $model->field($form, 'avatar') ?>
        <?= $model->field($form, 'birth_date') ?>
        <?= $model->field($form, 'phone') ?>



        <hr class="featurette-divider">
        <div class="form-group">
            <?= Html::submitButton('Обновить', [
                'class' => 'btn btn-default',
                'name'  => 'contact-button',
                'style' => 'width: 100%;',
            ]) ?>
        </div>
        <?php ActiveForm::end(); ?>

    <?php endif; ?>


</div>



