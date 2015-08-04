<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use app\models\User;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = 'Очищение кеша';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs(<<<JS
    $('#clear').click(function() {
        ajaxJson({
            url: '/clearCache/ajax',
            success: function(ret) {
                showInfo('Успешно очищено.')
            }
        });
    })
JS
);


?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Очищается ключ: <code><?= Html::encode('\cmsCore::getAll/items') ?></code></p>
    <button type="button" class="btn btn-primary" id="clear">Очистить</button>
</div>
