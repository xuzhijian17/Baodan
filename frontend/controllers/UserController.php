<?php

namespace frontend\controllers;

use Yii;
use yii\base\ErrorException;
use frontend\models\OrderModel;

/**
 * 用户端类
 *
 */
class UserController extends ApiController
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $token = Yii::$app->request->headers->get('Authorization');
        if (!$this->uid = $this->verifyToken('u_token',$token)) {
            exit(json_encode($this->renderFormat(401),JSON_UNESCAPED_UNICODE));
        }

        return true;
    }

    public function actionIndex($value='')
    {
        var_dump($this->uid);
    }

    // 报单
    public function actionCreate($value='')
    {
        $params = Yii::$app->request->post();

        try {
            $model = new OrderModel(['scenario'=>'addOrder']);
            $model->load($params);
            $model->addOrder($this->uid);
        } catch (\Exception $e) {
            $this->code = $e->getCode();
            $this->message = $e->getMessage();
        }

        return $this->renderFormat($this->code,$this->data,$this->message);
    }

    // 撤销
    public function actionCancel($value='')
    {
        $id = Yii::$app->request->post('id');

        try {
            $model = new OrderModel();
            $model->cancelOrder($id,$this->uid);
        } catch (\Exception $e) {
            $this->code = $e->getCode();
            $this->message = $e->getMessage();
        }

        return $this->renderFormat($this->code,$this->data,$this->message);
    }

    // 获取用户报单
    public function actionGetOrders($value='')
    {
        $params = Yii::$app->request->get();

        try {
            $model = new OrderModel();
            $model->load($params);
            $this->data = $model->getOrdersByUid($this->uid);
        } catch (\Exception $e) {
            $this->code = $e->getCode();
            $this->message = $e->getMessage();
        }
        
        return $this->renderFormat($this->code,$this->data,$this->message);
    }
}
