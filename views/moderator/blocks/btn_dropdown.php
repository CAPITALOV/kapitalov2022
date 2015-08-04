<?php

use app\models\Translator as T;
?>
<div class="btn-group">
    <button type="button" class="btn <?= ' ' . (isset($class) ? implode(' ', $class) : 'btn-default') ?> dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        <?= isset($name) ? T::t($name) : T::t('Action') ?> <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <?php foreach ($actions as $a): ?>
            <?php if (isset($a['divider'])): ?>
                <li class="divider"></li>
                <?php endif ?>
            <li><a <?=isset($a['id']) ? sprintf('id="%s" ', $a['id']) : '' ?><?=isset($a['class']) ? sprintf('class="%s" ', $a['class']) : '' ?>href="<?= $a['link'] ?>"><?= T::t($a['name']) ?></a></li>
        <?php endforeach ?>
    </ul>
</div>
