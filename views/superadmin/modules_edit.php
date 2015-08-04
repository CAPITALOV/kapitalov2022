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
/** @var $model app\models\Form\Module */

$this->title = 'Редактирование модуля';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

    <div class="alert alert-success">
        Успешно сохранено.
    </div>
    <?php else: ?>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
            <?php $fields = [
                'name',
                'title',
                'css_prefix',
                'cachetime',
                'cacheint',
                'template',
                'version',
            ];
                foreach($fields as $fieldName) {
                    echo $form->field($model, $fieldName);
                }
            ?>
            <?= $form->field($model, 'cache')->checkbox(); ?>
            <?= $form->field($model, 'original')->checkbox(); ?>
            <?= $form->field($model, 'showtitle')->checkbox(); ?>
            <?= $form->field($model, 'is_strict_bind')->checkbox(); ?>
            <?= $form->field($model, 'is_external')->checkbox(); ?>
            <?= $form->field($model, 'user')->checkbox(); ?>
            <?= $form->field($model, 'position')->listBox(Template::getPositionList2()); ?>
            <div class="form-group">
                <a class="btn btn-primary" href="<?= Url::to(['superadmin/modules_extra_position', 'id' => $model->id])?>">Дополонителоые положения</a>
            </div>
            <?= $form->field($model, 'content')->textarea(); ?>
            <?= $form->field($model, 'access_list')->checkboxList(Module::getSiteGroups())->label('Доступ по группам'); ?>


            <div class="form-group">
                    <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?php endif; ?>
</div>
