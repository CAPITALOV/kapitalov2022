<?php

use app\models\Translator as T;
$this->beginContent('@app/views/moderator/obj.php', ['pobject' => $pobject]);
?>
<h4><?= T::t('Text')?>: <span class="text-info"><?=$object['message']?></span></h4>
<hr>
<h4><?=T::t('Sent')?>: <span class="text-info"><?=$object['senddate']?></span></h4>
<?php $this->endContent() ?>