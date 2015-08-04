<?php

use app\models\Translator as T;
use yii\web\View;
use yii\grid\GridView;
use app\assets\ModerationAsset;

ModerationAsset::register($this);
/* @var $this View */
$this->title = T::t('Moderation');
?>
<div class="alert alert-warning alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <?php if ($hasExistingObjects): ?>
        <strong><?= T::t('You have existing objects') ?></strong>
    <?php endif ?>
    <?php if (0 < $totalCount && !$hasExistingObjects): ?>
        <strong><?= T::t('Please take objects for work') ?></strong>
    <?php elseif (!$hasExistingObjects): ?>
        <strong><?= T::t('There no objects in queue') ?></strong>
    <?php endif ?>
</div>
<?php if (0 < $totalCount && !$hasExistingObjects): ?>
    <div class="btn-group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            <?= T::t('Take for processing') ?> (<?= T::t('Total') ?> <strong><?= $totalCount ?></strong>)&nbsp;<span class="caret"></span>
        </button>
        <ul class="dropdown-menu js-mod-obj-to-work" role="menu">
            <?php foreach ([5, 10, 15, 20, 30, 50] as $row): ?>
                <li><a href="<?= \yii\helpers\Url::to(['moderator/work']) ?>?l=<?= $row ?>"><?= $row ?> <?= T::t('Objects') ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif ?>
<?php if (0 < $objectsCount): ?>
    <?= $this->render('blocks/reset_sorting_btn') ?>
    <?= GridView::widget($gridParams) ?>
<?php endif ?>

