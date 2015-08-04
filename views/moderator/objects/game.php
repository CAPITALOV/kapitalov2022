<?php

use app\models\Translator as T;
$this->beginContent('@app/views/moderator/obj.php',['pobject' => $pobject]);
?>
<div class="col-md-12">
    <h4 class="text-info text-justify"><?= T::t('Name') ?>: <span class="text-right"><?= $object['name'] ?></span></h4>
    <hr>
    <h4 class="text-info text-justify"><?= T::t('Resolution') ?>: <span class="text-right"><?= $object['width'] ?>x<?= $object['height'] ?></span></h4>
    <hr>
</div>
<?php $this->endContent() ?>