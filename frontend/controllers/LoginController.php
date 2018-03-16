<?php

namespace frontend\controllers;

use Yii;
use frontend\models\UserModel;
use frontend\models\EmployeeModel;
use yii\filters\auth\HttpBasicAuth;
use \Yunpian\Sdk\YunpianClient;
use common\models\Token;

/**
 * 账户类
 *
 */
class LoginController extends ApiController
{
    public $enableCsrfValidation = false;


    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        return true;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::className(),
        ];
        return $behaviors;
    }

    public function actionIndex($value='')
    {
        $phone = Yii::$app->request->post('phone');

        echo Yii::$app->cache->get($phone);

    }

    public function actionSendCode($value='')
    {
        $phone = Yii::$app->request->post('phone');

        $code = rand(1000,9999);
        Yii::$app->cache->set($phone,$code,60);
        echo $code;
        exit();

        $apikey = '5604c7311950aea5a1d83c0e87c41b45';
        //初始化client,apikey作为所有请求的默认值
        $clnt = YunpianClient::create($apikey);

        $param = [YunpianClient::MOBILE => '15652929681',YunpianClient::TEXT => '【秉象资管】您的验证码是1234'];
        $r = $clnt->sms()->single_send($param);
        var_dump($r);
    }

    public function actionRegister($value='')
    {
        $phone = Yii::$app->request->post('phone');
        $code = Yii::$app->request->post('code');
        $district_id = Yii::$app->request->post('district_id');
        $params = ['phone'=>$phone,'code'=>$code];

        try {
            $model = new UserModel();
            $model->load($params);
            $model->registerUser($district_id);
        } catch (\Exception $e) {
            $this->code = $e->getCode();
            $this->message = $e->getMessage();
        }
        
        return $this->renderFormat($this->code,$this->data,$this->message);
    }

    public function actionULogin($value='')
    {
        $params = Yii::$app->request->post();

        try {
            $model = new UserModel();
            $model->load($params);
            $this->data = $model->loginValidate('u_token');
        } catch (\Exception $e) {
            $this->code = $e->getCode();
            $this->message = $e->getMessage();
            // Yii::Error($e);
        }
        
        return $this->renderFormat($this->code,$this->data,$this->message);
    }

    public function actionELogin($value='')
    {
        $params = Yii::$app->request->post();
        
        try {
            $model = new EmployeeModel();
            $model->load($params);
            $this->data = $model->loginValidate('e_token');
        } catch (\Exception $e) {
            $this->code = $e->getCode();
            $this->message = $e->getMessage();
            // Yii::Error($e);
        }
        
        return $this->renderFormat($this->code,$this->data,$this->message);
    }

    public function actionRefreshToken($value='')
    {
        $u_token = Yii::$app->request->post('u_token');
        $e_token = Yii::$app->request->post('e_token');

        $token = $u_token ?? $e_token;
        $key = $u_token ? 'u_token' : 'e_token';
        if (!$this->uid = $this->verifyToken($token)) {
            $this->code = 401;
        }else{
            $this->data = ['token' => Token::createToken($key,['uid'=>$this->uid])];
        }

        return $this->renderFormat($this->code,$this->data,$this->message);
    }
}
