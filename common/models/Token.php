<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;

/**
 * Token model
 */
class Token
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }


    /**
     * Create token.
     */
    public static function createToken($key,$data)
    {
        $exp = Yii::$app->params['token']['exp'];
        $secret = Yii::$app->params['token']['secret'];
        
        $data['exp'] = $exp;
        $header = base64_encode(json_encode(['typ'=>'JWT','alg'=>'HS256']));
        $payload = base64_encode(json_encode($data));
        // $signature = hash_hmac('sha256', $header.$payload, $secret, true); 
        $signature = base64_encode($secret);

        $token = $header.'.'.$payload.'.'.$signature;

        Yii::$app->redis->hset($key,$data['uid'],$token);

        return $token;
    }

    /**
     * Verify token.
     */
    /*public static function verifyToken($token)
    {
        if (!$token) {
            return false;
        }
        
        list($header,$payload,$signature) = explode('.', $token);
        $payload = json_decode(base64_decode($payload),true);
        $signature = base64_decode($signature);
        
        $c_token = Yii::$app->redis->hget('token',$payload['uid']);
        $secret = Yii::$app->params['token']['secret'];

        if ($signature != $secret) {
            throw new \Exception(Yii::$app->params['codeinfo']['401'], 401);
        }

        if ($token != $c_token) {
            throw new \Exception(Yii::$app->params['codeinfo']['403'], 403);
        }

        if ($payload['exp'] < time()) {
            throw new \Exception(Yii::$app->params['codeinfo']['404'], 404);
        }
        
        return $token === $c_token && $signature === $secret && $payload['exp'] > time() ? $payload['uid'] : null;
    }*/
}
