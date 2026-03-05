<?php
namespace humhub\modules\entrasso\models;

use Yii;
use yii\base\Model;

class SettingsForm extends Model
{
    public $groupMapping;
    public $spaceMapping;
    public $requirePrivateEmail;

    public function rules()
    {
        return [
            [['groupMapping', 'spaceMapping'], 'safe'],
            [['requirePrivateEmail'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'groupMapping' => 'Group Mapping (Format: Entra-ID=HumHub-Group-Name)',
            'spaceMapping' => 'Space Mapping (Format: Entra-ID=HumHub-Space-Name)',
            'requirePrivateEmail' => 'Force users to provide a private email on first login',
        ];
    }

    public function loadSettings()
    {
        $settings = Yii::$app->getModule('entrasso')->settings;
        $this->groupMapping = $settings->get('groupMapping');
        $this->spaceMapping = $settings->get('spaceMapping');
        $this->requirePrivateEmail = (bool) $settings->get('requirePrivateEmail');
    }

    public function saveSettings()
    {
        $settings = Yii::$app->getModule('entrasso')->settings;
        $settings->set('groupMapping', $this->groupMapping);
        $settings->set('spaceMapping', $this->spaceMapping);
        $settings->set('requirePrivateEmail', $this->requirePrivateEmail);
        return true;
    }
}
