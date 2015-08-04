<?php

use app\models\Translator as T;
$this->beginContent('@app/views/moderator/obj.php', ['pobject' => $pobject]);

?>
<div class="col-md-12">
    <table class="table table-borderless table-condensed">
        <tr>
            <td>
                <p class="text-info"><?= T::t('Name') ?>:</p>
            </td>
            <td>
                <p class="text-muted"><?= $object['name'] ?></p>
            </td>
        </tr>
        <tr>
            <td>
                <p class="text-info"><?= T::t('Link') ?>:</p>
            </td>
            <td>
                <p class="text-muted"><?= $object['url'] ?></p>
            </td>
        </tr>
        <tr>
            <td>
                <p class="text-info"><?= T::t('Views') ?>:</p>
            </td>
            <td>
                <p class="text-muted"><?= $object['hits'] ?></p>
            </td>
        </tr>
        <?php if ($object['desc_tags']): ?>
            <tr>
                <td>
                    <span class='text-primary'><?= T::t('Tags') ?></span>:
                </td>
                <td>
                    <span class="text-info"><?= str_replace('#', ' ', $object['desc_tags']) ?></span>
                </td>
            </tr>
        <?php endif ?>
    </table>
</div>
<?php $this->endContent() ?>