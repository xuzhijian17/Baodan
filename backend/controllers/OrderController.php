<?php
namespace backend\controllers;

use Yii;
use backend\models\Order;

/**
 * Order controller
 */
class OrderController extends BaseController
{

    /**
     * @return string
     */
    public function actionIndex()
    {
        $params = Yii::$app->request->get();

        $model = new Order();
        $data = $model->getOrderList();

        var_dump($data);
    }
}
