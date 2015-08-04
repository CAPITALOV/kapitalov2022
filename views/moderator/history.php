<?php

use yii\grid\GridView;
use app\models\Translator as T;
use app\assets\ModerationAsset;
ModerationAsset::register($this);
?>

<h3><?= T::t('History of moderator')?> <span class="text-info"><?= $moderator->full_name?></span></h3>
<?= $this->render('blocks/reset_sorting_btn')?>
<?= GridView::widget($gridOpts); ?>