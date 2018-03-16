<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\Json;

/**
 * api module下controller基类
 *
 */
class ApiController extends \yii\web\Controller
{
    protected $uid;
    protected $userData = [];

    protected $code = 0;
    protected $message = 'Success';
    protected $data = [];

    public function init()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        return true;
    }

    /**
     * 验签方法
     * @param array $param 参数数组
     * @return boolean true成功/false失败
     */
    public function verifyToken($key,$token)
    {
        if (!$token) {
            return false;
        }
        
        list($header,$payload,$signature) = explode('.', $token);
        $payload = json_decode(base64_decode($payload),true);
        $signature = base64_decode($signature);
        
        $c_token = Yii::$app->redis->hget($key,$payload['uid']);
        $secret = Yii::$app->params['token']['secret'];

        if (!empty($payload)) {
            $this->userData = $payload;
        }
                
        return $token === $c_token && $signature === $secret && $payload['exp'] > time() ? $payload['uid'] : null;
    }

    /**
    * 数据格式返回统一格式化
    */
    public function renderFormat($code = null, $data = [], $message = [])
    {
        // Default data info
        $dataInfo = ['code'=>$code??$this->code,'data'=>$data??$this->data,'message'=>$message??$this->message];

        // Default code info
        $codeInfo = Yii::$app->params['codeinfo'];
        
        // Validate code
        if (array_key_exists($code,$codeInfo)) {
            $dataInfo['message'] = $codeInfo[$code];
        }else{
            $dataInfo['message'] = 'Server Error!';
        }

        // The way for format data display.
        if (is_array($data) && !empty($data)) {
            foreach ($data as $key => $value) {
                $dataInfo['data'][$key] = $value;   // It's rewrite datainfo `error` and `message` info.
            }
        }

        // Custom message
        if (!empty($message)) {
            if (is_array($message) && isset($message['message'])) {
                if (isset($message['rewrite']) && !$message['rewrite']) {
                    $dataInfo['message'] .= $message['message'];
                }else{
                    $dataInfo['message'] = $message['message'];
                }
            }else{
                $dataInfo['message'] = $message;
            }
        }
        
        return $dataInfo;
    }
}
