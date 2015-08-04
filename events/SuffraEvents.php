<?php

namespace app\events;

class SuffraEvents {

    /**
     * Срабатывает в модели при добавлении нового опроса
     */
    const GOOD_ADDED = 'suffra.good_added';

    /**
     * Срабатывает в разделе файлы при смене видимости файла
     */
    const FILE_PERMISSION_CHANGE = 'suffra.file_permission_change';

    /**
     * Срабатывает в разделе файлы при атрибутов файлов
     */
    const FILE_ATTRIBUTE_CHANGE = 'suffra.file_attribute_change';

    /**
     * Срабатывает в момент когда пользователь нажимает на кнопку пожаловаться
     */
    const ACTION_VIOLATION = 'suffra.violation';

    /**
     * Срабатывает в момент когда пользователь нажимает на кнопку пожаловаться на модератора
     */
    const ACTION_VIOLATION_TO_MODERATOR = 'suffra.violation_to_moderator';

    /**
     * Срабатывает в момент когда пользователь хочет вывести баллы
     */
    const ACTION_PAY_OUT_REQUEST = 'suffra.pay_out_request';

}
