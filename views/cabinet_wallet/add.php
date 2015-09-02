<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\authclient\widgets\AuthChoice;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model cs\base\BaseForm */

$this->title = 'Пополнение счета';


?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>

<?php if (Yii::$app->session->hasFlash('contactFormSubmitted')):
    $monthCounter = Yii::$app->session->getFlash('monthAdd');
    ?>

    <div class="alert alert-success">
        Ваша заявка успешно отправлена.
    </div>


    <p>Информация по заявке</p>
    <table class="table table-striped table-hover" style="width: auto;">
        <tr>
            <td>Акция</td>
            <td>
                <?= Yii::$app->session->getFlash('stock')->getField('name') ?>
            </td>
        </tr>
        <tr>
            <td>Количество месяцев</td>
            <td>
                <?= $monthCounter ?>
            </td>
        </tr>
        <tr>
            <td>Сумма к оплате</td>
            <td>
                <div class="alert alert-info">
                    <?= Yii::$app->formatter->asCurrency($monthCounter * 100 * 65)  ?>
                </div>
            </td>
        </tr>

    </table>

    <p>Для того чтобы получить данную услугу вам необходимо отправить соответствующую сумму на следующие реквизиты:</p>

    <table class="table table-striped table-hover" style="width: auto;">
        <tr>
            <td>Номер счета</td>
            <td>40802810970010001446</td>
        </tr>
        <tr>
            <td>Кор. счет</td>
            <td>30101810000000000340</td>
        </tr>
        <tr>
            <td>ИНН</td>
            <td>771805200350</td>
        </tr>
        <tr>
            <td>Банк</td>
            <td>МОСКОВСКИЙ ФИЛИАЛ ОАО КБ"РЕГИОНАЛЬНЫЙ КРЕДИТ"
                Г.МОСКВА
            </td>
        </tr>
        <tr>
            <td>БИК</td>
            <td>044583340</td>
        </tr>
        <tr>
            <td>Юр. лицо</td>
            <td>Индивидуальный предприниматель Касьянов Дмитрий
                Валериевич
            </td>
        </tr>
    </table>


<?php else: ?>


    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin([
                'id' => 'contact-form',
            ]); ?>
            <?= $model->field($form, 'monthCounter') ?>

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
