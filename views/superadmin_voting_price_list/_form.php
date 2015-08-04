<?php



?>

<?= $form->field($model, 'cnt')->label('Значение') ?>
<?= $form->field($model, 'price')->label('Множитель') ?>
<?= $form->field($model, 'type')->hiddenInput()->label('', ['class' => 'hide']) ?>