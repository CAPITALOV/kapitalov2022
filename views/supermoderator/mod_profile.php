<?php 
use Yii;
?>
<script>
    $(document).ready(function () {
        $(document).on('click', '#change-rating', function () {
            if ($("#r").length == 0)
                $('<input type="number" id="r" name="rating"/><button id="rs" class="btn btn-xs">обновить</button>').insertAfter($(this));
        });
        $(document).on('click', '#rs', function () {
            var r = $(this).val();
            $.ajax({
                url: '/moderation/rest/moderator.change.rating',
                data: {rating: $('#r').val(), uid: <?= $this->context->user->identity->id ?>, mod_id: <?= Yii::$app->request->get('id')  ?>},
                type: "POST",
                beforeSend: function () {
                    $('#r,#rs').remove();
                }
            }).done(function (d) {
                if (!d.error) {
                    window.location.reload();
                } else {
                    alert(d.error.message);
                }
            });
        });
    });
</script>
<div class="row">
    <h3>Профиль модератора <span class="text-info"><?= $profile->getNickname() ?></span></h3>
    <div class="col-md-6">
        <table class='table table-hover'>
            <tr>
                <td class='col-md-3'>Рейтинг:</td>
                <td class='text-info'>
                    <?php // include file = 'blocks/progress.tpl' progress = $profile->getRating('moderators') ?>
                    <a class="btn btn-info" href="/moderation/mviolations/<?= $smarty . request . moderator_id ?>">Посмотреть жалобы</a>
                    <button class="btn btn-default" id="change-rating">Назначить рейтинг</button>
                </td>
            </tr>
            <tr>
                <td class='col-md-3'><a href="/users/<?= $profile->getId() ?>"><img style="width:100%;" src="<?= $profile->getAvatarPath('small') ?>"></a></td>
                <td class="col-md-offset-2">Логин: <span class='text-info'><?= $profile->getLogin() ?></span></td>
            </tr>
            <tr>
                <td class='col-md-3'>День рождения:</td>
                <td class='text-info'><?= $profile->getBirthDateAsString() ?></td>
            </tr>
            <tr>
                <td class='col-md-3'>Последнее посещение:</td>
                <td class='text-info'><?= $profile->getLastLogin() ?></td>
            </tr>
        </table>
    </div>
</div>

