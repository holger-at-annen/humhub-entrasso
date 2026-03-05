<?php
namespace humhub\modules\entrasso;

use Yii;
use humhub\modules\user\models\User;
use humhub\modules\user\models\Group;
use humhub\modules\space\models\Space;
use humhub\modules\file\handler\ProfileImage;
use humhub\modules\entrasso\components\EntraAuthClient;

class Events
{
    public static function onAuthClientInit($event)
    {
        // Only inject the Microsoft login button if the Environment Variables are set in Coolify
        if (getenv('ENTRASSO_CLIENT_ID') && getenv('ENTRASSO_CLIENT_SECRET')) {
            $event->sender->setClient('entra', ['class' => EntraAuthClient::class]);
        }
    }

    public static function onAfterLogin($event)
    {
        $user = $event->identity;
        $session = Yii::$app->session;
        $settings = Yii::$app->getModule('entrasso')->settings;

        // 1 & 2: SYNC GROUPS AND SPACES
        $entraGroups = $session->get('entra_groups');
        if (is_array($entraGroups)) {
            
            // Sync HumHub Groups
            foreach (explode("\n", $settings->get('groupMapping', '')) as $line) {
                $parts = explode("=", trim($line));
                if (count($parts) === 2) {
                    $group = Group::findOne(['name' => trim($parts[1])]);
                    if ($group) {
                        $shouldBeMember = in_array(trim($parts[0]), $entraGroups);
                        if ($shouldBeMember && !$group->isMember($user)) $group->addUser($user);
                        elseif (!$shouldBeMember && $group->isMember($user)) $group->removeUser($user);
                    }
                }
            }

            // Sync HumHub Spaces
            foreach (explode("\n", $settings->get('spaceMapping', '')) as $line) {
                $parts = explode("=", trim($line));
                if (count($parts) === 2) {
                    $space = Space::findOne(['name' => trim($parts[1])]);
                    if ($space) {
                        $shouldBeMember = in_array(trim($parts[0]), $entraGroups);
                        if ($shouldBeMember && !$space->isMember($user->id)) $space->addMember($user->id);
                        elseif (!$shouldBeMember && $space->isMember($user->id)) $space->removeMember($user->id);
                    }
                }
            }
            $session->remove('entra_groups');
        }

        // 3: SYNC PROFILE PICTURE
        $token = $session->get('entra_token');
        if ($token) {
            try {
                $opts = ['http' => ['header' => "Authorization: Bearer {$token}\r\n"]];
                $context = stream_context_create($opts);
                $imageContent = @file_get_contents('https://graph.microsoft.com/v1.0/me/photo/$value', false, $context);

                if ($imageContent) {
                    $profileImage = new ProfileImage($user->id);
                    $profileImage->setNew($imageContent);
                }
            } catch (\Exception $e) {}
            $session->remove('entra_token');
        }
    }

    public static function onBeforeMailSend($event)
    {
        $message = $event->message;
        $toAddresses = $message->getTo();
        
        if (!empty($toAddresses)) {
            $newToAddresses = [];
            $addressSwapped = false;

            foreach ($toAddresses as $email => $name) {
                $user = User::findOne(['email' => $email]);
                if ($user !== null && $user->profile->hasAttribute('private_email')) {
                    $privateEmail = $user->profile->private_email;
                    if (!empty($privateEmail) && filter_var($privateEmail, FILTER_VALIDATE_EMAIL)) {
                        $newToAddresses[$privateEmail] = $name;
                        $addressSwapped = true;
                        continue;
                    }
                }
                $newToAddresses[$email] = $name;
            }

            if ($addressSwapped) {
                $message->setTo($newToAddresses);
            }
        }
    }

    public static function onBeforeAction($event)
    {
        if (Yii::$app->user->isGuest) return;
        
        $settings = Yii::$app->getModule('entrasso')->settings;
        if (!$settings->get('requirePrivateEmail')) return;

        $user = Yii::$app->user->getIdentity();
        $route = $event->action->uniqueId;

        // Allow access to account settings and logout
        $isAllowedRoute = (strpos($route, 'user/account') === 0) || ($route === 'user/auth/logout');

        if (!$isAllowedRoute && $user->profile->hasAttribute('private_email')) {
            if (empty($user->profile->private_email)) {
                Yii::$app->getSession()->setFlash('error', 'You must provide a private notification email before accessing the intranet.');
                $event->isValid = false; // Stop the requested action
                Yii::$app->response->redirect(['/user/account/edit']);
                Yii::$app->end();
            }
        }
    }
}
