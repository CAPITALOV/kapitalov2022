
<?php

use app\models\Translator as T;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?= Html::a( T::t('add new'), Url::to(['supermoderator/moderators_setting_add']), ['class' => 'btn btn-primary']) ?>
<h3><?= T::t('Moderators settings') ?></h3>
<?= $this->render('../blocks/flash')?>
<?= GridView::widget($gridOpts); ?>
