<?php
namespace backend\controllers;

use Yii;
use backend\models\User;

/**
 * User controller
 */
class UserController extends BaseController
{

    /**
     * @return string
     */
    public function actionIndex()
    {
        $params = Yii::$app->request->get();

        $model = new User();
        $data = $model->getUserList();

        var_dump($data);
    }
}
