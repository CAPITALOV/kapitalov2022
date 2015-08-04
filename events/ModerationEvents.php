<?php

namespace app\events;

class ModerationEvents {

    /**
     * Запускается в момент одобрения модераторром
     */
    const MODERATION_APPROVED = 'suffra.moderation.approved';

    /**
     * Запускается в момент отклонения модераторром
     */
    const MODERATION_DISSAPROVED = 'suffra.moderation.dissaproved';

}
