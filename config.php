<?php
use humhub\modules\user\authclient\Collection;
use humhub\modules\user\models\User;
use yii\mail\BaseMailer;
use yii\base\Controller;

return [
    'id' => 'entrasso',
    'class' => 'humhub\modules\entrasso\Module',
    'namespace' => 'humhub\modules\entrasso',
    'events' => [
        ['class' => Collection::class, 'event' => Collection::EVENT_AFTER_INIT, 'callback' => ['humhub\modules\entrasso\Events', 'onAuthClientInit']],
        ['class' => User::class, 'event' => User::EVENT_AFTER_LOGIN, 'callback' => ['humhub\modules\entrasso\Events', 'onAfterLogin']],
        ['class' => BaseMailer::class, 'event' => BaseMailer::EVENT_BEFORE_SEND, 'callback' => ['humhub\modules\entrasso\Events', 'onBeforeMailSend']],
        ['class' => Controller::class, 'event' => Controller::EVENT_BEFORE_ACTION, 'callback' => ['humhub\modules\entrasso\Events', 'onBeforeAction']],
    ],
];
