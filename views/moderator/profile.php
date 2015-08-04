<?php

use app\models\Translator as T;
use app\assets\ModeratorProfileAsset;
use app\models\ModerationManager;
use yii\bootstrap\Progress;

ModeratorProfileAsset::register($this);

$rating = (new ModerationManager)->getModeratorRating($this->context->user->identity->id);
?>

<div class="col-md-5" >
    <div class="row">
        <?=
        $this->render('blocks/btn_dropdown', [
            'class' => ['btn-info'],
            'actions' => [
                ['link' => '#', 'name' => 'Pick month', 'id' => 'dp-m'],
                ['link' => '#', 'name' => 'Pick day', 'id' => 'dp-d']
            ]
        ])
        ?> 
        &nbsp;
        <input name="date" id="js-dp" class="dp" type="date">
        <p id="stat-label" class="pull-right text-info"><?= T::t('Showing stats by')?> {}</p>
    </div>
    <canvas id="moderations_chart" data-type="Line"></canvas>
</div>

<div class="col-md-6 pull-right profile-block">
    <h4><?= T::t('You have {%} balls', ['%' => $balls]) ?></h4>
    <table class='table table-borderless table-condensed'>
        <tr>
            <td>
                <span class='text-primary'><?= T::t('Name') ?></span>:
            </td>
            <td>
                <span><?= $this->context->user->identity->name_first . ' ' . $this->context->user->identity->name_last ?></span>
            </td>
        </tr>
        <tr>
            <td>
                <span class='text-primary'><?= T::t('Email') ?></span>:
            </td>
            <td>
                <span><?= $this->context->user->identity->username ?></span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="text-info"><?= T::t('Rating') ?>:</span>
            </td>
            <td>
                <?=
                Progress::widget([
                    'percent' => $rating['percentage'],
                    'label' => $rating['current'],
                    'barOptions' => [
                        'class' => $rating['is_positive'] ? 'progress-bar-success' : 'progress-bar-danger',
                    ],
                    'options' => [
                        'class' => $rating['is_positive'] ? 'progress-success' : 'progress-danger',
                        'title' => ($rating['is_positive'] ? T::t('positive') : T::t('negative')) . ' ' . (T::t('{0} from {1}', [$rating['current'], $rating['max']])),
                    ]
                ])
                ?>
            </td>
        </tr>
    </table>
</div>