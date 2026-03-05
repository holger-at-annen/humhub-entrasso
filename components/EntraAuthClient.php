<?php
namespace humhub\modules\entrasso\components;

use Yii;
use yii\authclient\OpenIdConnect;
use humhub\modules\user\authclient\interfaces\SyncAttributes;

class EntraAuthClient extends OpenIdConnect implements SyncAttributes
{
    public $name = 'entra';
    public $title = 'Login with Microsoft';

    public function init()
    {
        $tenantId = getenv('ENTRASSO_TENANT_ID') ?: 'common';
        
        $this->issuerUrl = "https://login.microsoftonline.com/{$tenantId}/v2.0";
        $this->clientId = getenv('ENTRASSO_CLIENT_ID');
        $this->clientSecret = getenv('ENTRASSO_CLIENT_SECRET');
        
        parent::init();
    }

    protected function defaultNormalizeUserAttributes($attributes)
    {
        $session = Yii::$app->session;
        $session->set('entra_groups', $attributes['groups'] ?? []);
        
        $token = $this->getAccessToken();
        if ($token) {
            $session->set('entra_token', $token->getToken());
        }

        return [
            'id' => $attributes['oid'] ?? $attributes['sub'],
            'email' => $attributes['email'] ?? $attributes['preferred_username'] ?? '',
            'firstname' => $attributes['given_name'] ?? '',
            'lastname' => $attributes['family_name'] ?? '',
        ];
    }

    public function getSyncAttributes()
    {
        return ['email', 'firstname', 'lastname'];
    }
}
