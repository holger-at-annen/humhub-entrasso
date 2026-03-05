<?php
namespace humhub\modules\entrasso;

use yii\helpers\Url;

class Module extends \humhub\components\Module
{
    public function getConfigUrl()
    {
        return Url::to(['/entrasso/admin/index']);
    }
}
