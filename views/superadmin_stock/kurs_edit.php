<?php

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = $item->getField('name');
?>

<div class="container">
    <div class="col-lg-12">
        <h1 class="page-header"><?= $this->title ?></h1>
    </div>


    <table class="table" style="width:100%;">
        <?php
        foreach ($items as $i) {
            ?>
            <tr>
                <td>
                    <div class="form-group">
                        <input class="form-control inputKurs" type="text" value="<?= $i['kurs'] ?>" data-id="<?= $i['id'] ?>" style="width: auto;" data-type="kurs"/>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input class="form-control inputDate" type="text" value="<?= $i['date'] ?>" data-id="<?= $i['id'] ?>" style="width: auto;" data-type="date"/>
                    </div>
                </td>
            </tr>
        <?php
        }?>
    </table>
</div>