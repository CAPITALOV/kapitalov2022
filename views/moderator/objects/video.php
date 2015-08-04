<?php

use app\models\Translator as T;

parse_str(parse_url($object['video_url'], PHP_URL_QUERY), $r);
$this->beginContent('@app/views/moderator/obj.php', ['pobject' => $pobject]);
?>
<div class="col-md-12">
<?php /** @link  http://stackoverflow.com/questions/2068344/how-do-i-get-a-youtube-video-thumbnail-from-the-youtube-api  */ ?>
    <a target="_blank" href="<?= $object['video_url'] ?>"><img src="http://i.ytimg.com/vi/<?= $r['v'] ?>/mqdefault.jpg" ></a>
    <hr>
    <table class='table table-borderless'>
        <tr>
            <td>
                <span class='text-primary'><?= T::t('Description') ?></span>:
            </td>
            <td>
<?= $object['descr'] ?>
            </td>
        </tr>
        <tr>
            <td>
                <span class='text-primary'><?= T::t('Views') ?></span>:
            </td>
            <td>
<?= $object['hits'] ?>
            </td>
        </tr>
        <tr>
            <td>
                <span class='text-primary'><?= T::t('Link') ?></span>:
            </td>
            <td>
                <textarea class="col-md6 text-muted" readonly><?= $object['video_url'] ?></textarea>
            </td>
        </tr>
<?php if ($object['desc_tags']): ?>
            <tr>
                <td>
                    <span class='text-primary'><?= T::t('Tags') ?></span>:
                </td>
                <td>
                    <span class="text-info"><?= str_replace('#', ' ', $object['desc_tags']) ?></span>
            </tr>
<?php endif ?>
    </table>
</div>
<?php $this->endContent() ?>