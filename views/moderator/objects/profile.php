<?php
use Suffra\Config;
use app\models\Translator as T;
$this->beginContent('@app/views/moderator/obj.php', ['pobject' => $pobject]);
?>
<div class="col-md-12">
    <img onerror="this.src='<?= Yii::$app->params['suffra_url']?>/images/users/avatars/<?= $object['gender'] ?>nopic.png';" src="<?= Yii::$app->params['suffra_url']?><?= Config::userDirectory($object['id'], true) ?>/avatars/avatar.jpg">
    <hr>
    <table class='table table-borderless'>
        <tr>
            <td>
                <span class='text-primary'><?= T::t('Nickname') ?></span>:
            </td>
            <td>
                <?= $object['nickname'] ?>
            </td>
        </tr>
        <tr>
            <td>
                <span class='text-primary'><?= T::t('Email') ?></span>:
            </td>
            <td>
                <?= $object['email'] ?>
            </td>
        </tr>
        <tr>
            <td>
                <span class='text-primary'><?= T::t('Registered') ?></span>:
            </td>
            <td>
                <?= $object['regdate'] ?>
            </td>
        </tr>
        <tr>
            <td>
                <span class='text-primary'><?= T::t('Last login') ?></span>:
            </td>
            <td>
                <?= $object['logdate'] ?>
            </td>
        </tr>
    </table>
</div>
<?php $this->endContent() ?>