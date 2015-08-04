<?php

use app\models\Translator as T;
use yii\i18n\Formatter;
$this->beginContent('@app/views/moderator/obj.php',['pobject' => $pobject]);
?>
<div class="col-md-12">
    <h4 class="text-info text-justify"><?= T::t('Name') ?>: <span class="text-right"><?= $object['filename'] ?></span></h4>
    <hr>
    <h4 class="text-info text-justify"><?= T::t('Published') ?>: <span class="text-right"><?=$object['pubdate'] ?></span></h4>
    <hr>
    <h4 class="text-info text-justify"><?= T::t('Is in market') ?>: <span class="text-right"><?=  (new Formatter)->asBoolean($object['in_market']) ?></span></h4>
</div>
<?php $this->endContent() ?>