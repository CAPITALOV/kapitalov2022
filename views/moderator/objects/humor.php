<?php

use app\models\Translator as T;
$this->beginContent('@app/views/moderator/obj.php', ['pobject' => $pobject]);
?>
<div class="col-md-12">
    <h4 class="text-info"><?= T::t('Text') ?>:</h4>
    <hr>
    <p class="text-justify text-muted"><?= $object['message'] ?></p>
    <hr>
    <p class="text-info text-right"><?= T::t('Published') ?>: <span class="text-primary"><?= $object['pubdate'] ?></span></p>
</div>
<?php $this->endContent() ?>