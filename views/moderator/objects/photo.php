<?php

use Suffra\Config;
$this->beginContent('@app/views/moderator/obj.php', ['pobject' => $pobject]);
?>
<div class='row'>
    <div class="col-md-6">
        <?php if($object['isFolder'] != 1):?>
        <img onerror="this.src='<?= Yii::$app->params['suffra_url']?>/images/nophoto.png';"; src="<?= Yii::$app->params['suffra_url'] . Config::userDirectory($object['user_id'])?>/photos/small/<?=$object['imageurl']?>">
        <?php endif ?>
    </div>
</div>
<?php $this->endContent() ?>