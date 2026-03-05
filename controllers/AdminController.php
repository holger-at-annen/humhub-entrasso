<?php
namespace humhub\modules\entrasso\controllers;

use Yii;
use humhub\modules\admin\components\Controller;
use humhub\modules\entrasso\models\SettingsForm;

class AdminController extends Controller
{
    public function actionIndex()
    {
        $model = new SettingsForm();
        $model->loadSettings();

        if ($model->load(Yii::$app->request->post()) && $model->saveSettings()) {
            $this->view->saved();
        }

        return $this->render('index', ['model' => $model]);
    }
}
