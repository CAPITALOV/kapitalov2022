<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use app\models\User;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = 'Добавление задачи крон';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

    <div class="alert alert-success">
        Задача добавлена успешно.
    </div>

    <?php else: ?>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
<?php

$fields = [
    ['comment', 'Комментарий', 'Максимум 200 символов'],
    ['job_name', 'Название','Только латинские буквы, цифры и знак подчеркивания'],
    ['job_interval', 'Интервал', 'Периодичность запуска задачи в часах'],
    ['component', 'Компонент'],
    ['model_method', 'Метод модели'],
    ['custom_file', 'Файл', 'Пример: includes/myphp/test.php'],
    ['class_name', 'Класс', 'файл|класс, пример: actions|cmsActions или класс, пример: cmsDatabase'],
    ['class_method', 'Метод класса'],
];

foreach ($fields as $f) {
    $o = $form->field($model, $f[0])->label($f[1]);
    if (isset($f[2])) {
        $o->hint($f[2]);
    }
    echo $o;
}
?>
            <?= $form->field($model, 'is_enabled')->label('Включено')->checkbox()->hint('Неактивные задачи не выполняются') ?>

            <div class="form-group">
                <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?php endif; ?>
</div>
