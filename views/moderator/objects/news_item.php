<?php

use app\models\Translator as T;
$this->beginContent('@app/views/moderator/obj.php', ['pobject' => $pobject]);
?>
<div class="col-md-12">
    <img src="<?= Yii::$app->params['suffra_url'] . $object['img']?>">
    <hr>
    <table class='table table-borderless'>
        <tr>
            <td><?= T::t('Title')?>:</td>
            <td><?= $object['title']?></td>
        </tr>
        <tr>
            <td><?= T::t('Description')?>:</td>
            <td><?= $object['description']?></td>
        </tr>
    </table>
<?php $this->endContent() ?>