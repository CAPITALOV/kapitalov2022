<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $log array */

$this->title = 'Лог';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">

<div class="site-login">
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>

    <table class="table table-hover table-striped table-bordered">
        <?php foreach($log as $row) { ?>
            <tr>
                <td nowrap>
                    <?= $row['date'] ?>
                </td>
                <td nowrap>
                    <?= $row['time'] ?>
                </td>
                <td nowrap>
                    <?= $row['ip'] ?>
                </td>
                <td nowrap>
                    <?= $row['user_id'] ?>
                </td>
                <td nowrap>
                    <?= $row['code'] ?>
                </td>
                <td nowrap>
                    <?= $row['type'] ?>
                </td>
                <td nowrap>
                    <?= $row['app'] ?>
                </td>
                <td>
                    <pre><?= Html::encode($row['message']) ?></pre>
                </td>
            </tr>
        <?php }?>
    </table>



</div>
</div>