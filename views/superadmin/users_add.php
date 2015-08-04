<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use app\models\User;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = 'Добавление пользователя административной панели';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

    <div class="alert alert-success">
        Пользователь добавлен успешно.
    </div>

<!--    <p>-->
<!--        Note that if you turn on the Yii debugger, you should be able-->
<!--        to view the mail message on the mail panel of the debugger.-->
<!--        --><?php //if (Yii::$app->mailer->useFileTransport): ?>
<!--        Because the application is in development mode, the email is not sent but saved as-->
<!--        a file under <code>--><?//= Yii::getAlias(Yii::$app->mailer->fileTransportPath) ?><!--</code>.-->
<!--        Please configure the <code>useFileTransport</code> property of the <code>mail</code>-->
<!--        application component to be false to enable email sending.-->
<!--        --><?php //endif; ?>
<!--    </p>-->
<!---->
    <?php else: ?>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
            <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'name_first')->label('Имя') ?>
                <?= $form->field($model, 'name_last')->label('Фамилия') ?>
                <?= $form->field($model, 'password')->label('Пароль') ?>
                <?= $form->field($model, 'roles')->checkboxList(User::getRolesIndex())->label('Роль'); ?>
            <div class="form-group">
                    <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?php endif; ?>
</div>
