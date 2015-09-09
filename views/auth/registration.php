<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\Form\Registration */

$this->title = 'Регистрация';

//\cs\services\VarDumper::dump(Yii::$app->session->hasFlash('contactFormSubmitted'),3,false);
?>

    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>

<?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

    <div class="alert alert-success">
        <p>Благодрим вас за регистрацию.</p>
    </div>
<?php else: ?>

    <div class="row">
        <div class="col-lg-5">
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
                    'class' => 'btn btn-primary',
                    'name' => 'contact-button',
                    'style' => 'width: 100%;',
                ]) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

<?php endif; ?>