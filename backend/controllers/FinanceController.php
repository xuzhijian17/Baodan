<?php
namespace backend\controllers;

use Yii;
use backend\models\Order;

/**
 * Finance controller
 */
class FinanceController extends BaseController
{

    /**
     * @return string
     */
    public function actionIndex()
    {
        $params = Yii::$app->request->get();

        $model = new Order();
        $data = $model->getOverOrderList();

        var_dump($data);
    }

    public function actionSettle($value='')
    {
        $id = Yii::$app->request->post('id');

        $model = new Order();
        $data = $model->settleAccount($id);

        var_dump($data);
    }
}
