<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\Form\Registration */

$this->title = 'Регистрация';

?>

    <h1 class="page-header text-center"><?= Html::encode($this->title) ?></h1>

<?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

    <div class="alert alert-success">
        <p>Благодрим вас за регистрацию.</p>
    </div>
<?php else: ?>

    <div class="row">
        <div class="col-lg-4 col-lg-offset-4">
            <?php $form = ActiveForm::begin([
                'id'                   => 'contact-form',
                'enableAjaxValidation' => true,
            ]); ?>
            <?php $field = $form->field($model, 'email',['inputOptions' => ['placeholder' => 'email']])->label('Почта', ['class' => 'hide']);
            $field->validateOnBlur = true;
            $field->validateOnChange = true;
            echo $field;
            ?>
            <?= $form->field($model, 'password1', ['inputOptions' => ['placeholder' => 'Пароль']])->passwordInput()->label('Пароль', ['class' => 'hide']) ?>
            <?= $form->field($model, 'password2', ['inputOptions' => ['placeholder' => 'Повторите пароль еще раз']])->passwordInput()->label('Пароль повтор', ['class' => 'hide']) ?>


            <hr>
            <div class="form-group">
                <?= Html::submitButton('Зарегистрироваться', [
                    'class' => 'btn btn-primary btn-lg',
                    'name' => 'contact-button',
                    'style' => 'width: 100%;background-color: #aa719f;border: none;border-radius: 24px;',
                ]) ?>
            </div>
            <hr>
            <div class="form-group">
                <a class="btn btn-primary btn-lg" href="<?= \yii\helpers\Url::to(['auth/login']) ?>" style="width: 100%;background-color: #aa719f;border: none;border-radius: 24px;">Войти в систему</a>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

<?php endif; ?>