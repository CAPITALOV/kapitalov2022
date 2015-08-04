<?php

use app\models\Translator as T;
use app\models\ModerationObject;
use app\models\ModeratorAction;
?>
<div class="modal fade" id="obj">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <?php
                $ref = \Yii::$app->request->getReferrer();
                if ($ref && preg_match('/\/moderator$/', $ref)):
                    ?>
                    <div class="progress" title="<?= T::t('Priority') ?>">
                        <div class="progress-bar progress-bar-<?php if ($pobject['priority'] <= 40): ?>success<?php elseif ($pobject['priority'] > 40 && $pobject['priority'] < 70): ?>warning<?php elseif ($pobject['priority'] >= 70): ?>danger<?php endif ?>" role="progressbar" aria-valuenow="<?= $pobject['priority'] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $pobject['priority'] ?>%;">
                        </div>
                    </div>
                <?php endif ?>
                <button type="button" class="close" data-dismiss="modal" title="<?= T::t('Close') ?>"><span aria-hidden="true">&times;</span><span class="sr-only"><?= T::t('Close') ?></span></button>
                <h3 class="modal-title">
                    <span class='text-info'><?= $pobject['mod_category'] == ModerationObject::MODERATION_CATEGORY_VIOLATION ? T::t('category_' . $pobject['mod_category']) . ' ' . T::t('on') . ' ' : '' ?></span><?= T::t('obj_' . (new ModerationObject)->getHumanReadableObjectType($pobject['type'])) ?>
                </h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= $content; ?>
                        <?php if ('' != $pobject['comment']): ?>
                            <hr>
                            <h4><?= T::t('Comment') ?>:</h4>
                            <blockquote class="blockquote text-info">
                                <?= $pobject['comment'] ?>
                            </blockquote>
                        <?php endif ?>
                        <?= T::t('Sent') ?>: <a target="_blank" href="/moderator/profile/<?= $pobject['user_id'] ?>"><?= $pobject['nickname'] ?></a>
                    </div>
                </div>
            </div>
            <div class="modal-footer" data-oid="<?= $pobject['id'] ?>">
                <button type="button" class="js-mod-action btn btn-xs btn-default pull-left" data-dismiss="modal" data-action="<?= ModeratorAction::ACTION_PUT_TO_QUEUE ?>"> <span class="glyphicon glyphicon-share-alt"></span>&nbsp;<?= T::t('Put to queue') ?></button>
                <button type="button" class="js-mod-action btn btn-xs btn-info" data-dismiss="modal" data-action="<?= ModeratorAction::ACTION_DISAPPROVE ?>"><span class=" glyphicon glyphicon-ok"></span>&nbsp;<?= $pobject['mod_category'] == ModerationObject::MODERATION_CATEGORY_VIOLATION ? T::t('Decline violation') : T::t('Decline') ?></button>
                <button type="button" class="js-mod-action btn btn-xs btn-danger" data-dismiss="modal" data-action="<?= ModeratorAction::ACTION_APPROVE ?>"><span class="glyphicon glyphicon-remove"></span>&nbsp;<?= $pobject['mod_category'] == ModerationObject::MODERATION_CATEGORY_VIOLATION ? T::t('Approve violation') : T::t('Approve') ?></button>
            </div>
        </div>
    </div>
</div>