<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model cs\base\BaseForm */

$this->title = 'Вход в систему капиталов';
?>


    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>

    <p>Пожалуйста заполните следующие поля для входа:</p>

<?php $form = ActiveForm::begin([
    'id'     => 'login-form',
    'layout' => 'horizontal',
]); ?>

<?= $model->field($form, 'username') ?>
<?= $model->field($form, 'password', [
    'template' => '{label}{beginWrapper}{input}<div style="margin-top: 5px;"><b>Забыли пароль?</b><span style="margin-left: 20px;"><a href=' . \yii\helpers\Url::to(['auth/password_recover']) . '>Восстановить</a></span></div>{hint}{error}{endWrapper}',
])->passwordInput() ?>

<?= $model->field($form, 'rememberMe') ?>

<?= \yii\authclient\widgets\AuthChoice::widget([
    'baseAuthUrl' => ['auth/auth']
]); ?>


    <div class="form-group">
        <div class="col-lg-12">
            <?= Html::submitButton('Вход', [
                'class' => 'btn btn-primary',
                'name'  => 'login-button',
                'style' => 'width: 100%;',
            ]) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>