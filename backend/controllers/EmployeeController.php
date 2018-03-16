<?php
namespace backend\controllers;

use Yii;
use backend\models\Employee;

/**
 * Employee controller
 */
class EmployeeController extends BaseController
{

    /**
     * @return string
     */
    public function actionIndex()
    {
        $params = Yii::$app->request->get();

        $model = new Employee();
        $data = $model->getEmployeeList();

        var_dump($data);
    }
}
