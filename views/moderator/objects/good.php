<?php

use app\models\Translator as T;
$this->beginContent('@app/views/moderator/obj.php', ['pobject' => $pobject]);
?>
<div class="col-md-12">
    <h4 class="text-info" title="Просмотреть"><a target="_blank" href="/goods/viewActive/<?= $object['id']?>"><?=$object['name']?></a></h4>
<hr>
    <div class="col-md-4">
       <a target="_blank" href='<?= Yii::$app->params['suffra_url']?>/goods/viewActive/<?=$object['id']?>'><img onerror="this.src='<?= Yii::$app->params['suffra_url']?>/images/nophoto.png';" style="width:100%" src="<?= Yii::$app->params['suffra_url']?>/upload/goods/<?=$object['img']?>"></a>
    </div>
    <div class="col-md-8">
        <table class="table table-responsive table-borderless">
            <tr>
                <td><strong><?= T::t('Description')?>: </strong></td>
                <td><a target="_blank" href='/goods/viewActive/<?=$object['id']?>'><?=$object['descr']?></a></td>
            </tr>
            <tr>
                <td><strong><?= T::t('Vendor')?>: </strong></td>
                <td><p><?=$object['vendor']?></p></td>
            </tr>
            <tr>
                <td><strong><?=T::t('Published')?>: </strong></td>
                <td><p><?=$object['date_create']?></p></td>
            </tr>
        </table>
    </div>
</div>
<?php $this->endContent() ?>