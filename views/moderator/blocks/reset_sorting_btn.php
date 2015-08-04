<?php

use yii\helpers\Html;
use app\models\Translator as T;
?>

<?php if(strpos(\Yii::$app->request->getUrl(),'sort=')): ?>
<div>
<?= Html::a(T::t('Reset sorting'), \Yii::$app->request->getHostInfo()  .'/'. \Yii::$app->request->getPathInfo(), ['class' => 'btn btn-primary btn-xs pull-right'])?>
</div>
<?php endif ?>