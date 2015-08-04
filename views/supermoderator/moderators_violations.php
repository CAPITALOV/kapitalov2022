
<?php

use yii\grid\GridView;
use app\models\Translator as T;

?>
<h3><?= T::t('Violations for')?> <span class="text-info"><?=$moderator->name_first?> <?=$moderator->name_last?></span></h3>
<?= $this->render('../moderator/blocks/reset_sorting_btn') ?>
<?= GridView::widget($gridOpts); ?>
