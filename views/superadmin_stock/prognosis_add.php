<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use app\models\UnionCategory;
use yii\db\Query;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model cs\base\BaseForm */

$this->title = 'Добавить прогноз';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

        <div class="alert alert-success">
            Успешно добавлено.
        </div>

    <?php else: ?>


        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin([
                    'id'      => 'contact-form',
                    'options' => ['enctype' => 'multipart/form-data']
                ]); ?>
                <?= $model->field($form, 'date1') ?>
                <?= $model->field($form, 'kurs1') ?>
                <?= $model->field($form, 'date2') ?>
                <?= $model->field($form, 'kurs2') ?>
                <?= $model->field($form, 'date3') ?>
                <?= $model->field($form, 'kurs3') ?>
                <?= $model->field($form, 'date4') ?>
                <?= $model->field($form, 'kurs4') ?>
                <?= $model->field($form, 'date5') ?>
                <?= $model->field($form, 'kurs5') ?>
                <?= $model->field($form, 'date6') ?>
                <?= $model->field($form, 'kurs6') ?>
                <?= $model->field($form, 'date7') ?>
                <?= $model->field($form, 'kurs7') ?>
                <?= $model->field($form, 'date8') ?>
                <?= $model->field($form, 'kurs8') ?>
                <?= $model->field($form, 'date9') ?>
                <?= $model->field($form, 'kurs9') ?>
                <?= $model->field($form, 'date10') ?>
                <?= $model->field($form, 'kurs10') ?>

                <input type="hidden" value="1" name="stock_id"/>

                <div class="form-group">
                    <hr>
                    <?= Html::submitButton('Добавить', [
                        'class' => 'btn btn-default',
                        'name'  => 'contact-button',
                        'style' => 'width:100%',
                    ]) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>

    <?php endif; ?>
</div>
