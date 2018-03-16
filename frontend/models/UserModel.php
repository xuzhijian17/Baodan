<?php
namespace frontend\models;

use Yii;
use yii\db\Query;
use common\models\Token;
use common\models\ChinesePinyin;

/**
 * user model
 * @package frontend\models
 */
class UserModel extends BaseModel
{

	public $phone;
    public $code;
    public $district_id;

    protected $table = 'dk_user';

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Returns a list of scenarios and the corresponding active attributes.
     * An active attribute is one that is subject to validation in the current scenario.
     * @return array a list of scenarios and the corresponding active attributes.
     */
    public function scenarios($value='')
    {
        return [
            'default' => ['phone', 'code'],
        ];
    }
    
    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     * @return array validation rules
     * @see scenarios()
     */
    public function rules()
    {
        return [
            [['phone', 'code'],'required'],
        ];
    }

    public function getFirstCharter($string='')
    {
        $f_str = '';

        $len = mb_strlen($string);
        while ($len) {
            $stringList[] = mb_substr($string, 0, 1, 'utf8');
            $string = mb_substr($string, 1, $len, 'utf8');
            $len = mb_strlen($string);
        }

        if (!is_array($stringList)) {
            return $pinyinList;
        }

        $pinyinList = [];
        foreach ($stringList as $string) {
            if ((strlen($string) === 3) && isset(ChinesePinyin::$chinesePinyin[$string])) {
            // 大部分汉字strlen长度为3，在拼音库里。所有读音都取出。
                $pinyinList[] = ChinesePinyin::$chinesePinyin[$string];
            } else {
                $pinyinList[] = array($string);
            }
        }

        foreach ($pinyinList as $key => $value) {
            $f_str .= substr($value[0], 0, 1);
        }

        return $f_str;
    }

    /**
     * Register the data validation.
     *
     */
    public function registerUser($district_id='')
    {
        // validate phone
        $rs1 = (new Query)
            ->select(['*'])
            ->from($this->table)
            ->where(['phone'=>$this->phone, 'status'=>0])
            ->one()
        ;

        if ($rs1) {
            throw new \Exception(Yii::$app->params['codeinfo']['4'], 4);
        }

        // validate code
        $rs2 = Yii::$app->cache->get($this->phone) == $this->code ? true : false;

        if (!$rs2) {
            throw new \Exception(Yii::$app->params['codeinfo']['3'], 3);
        }

        // validate district
        $rs3 = (new Query)
            ->select(['name'])
            ->from('dk_district')
            ->where(['id'=>$district_id])
            ->one()
        ;
        
        Yii::$app->db->createCommand()->insert($this->table, [
            'username' => $this->getFirstCharter($rs3['name']).'_'.$this->phone,
            'phone' => $this->phone, 
            'created_at' => date("Y-m-d H:i:s"),
        ])->execute();

        return;
    }


    /**
     * Login the data validation.
     *
     */
    public function loginValidate($key)
    {
        // validate phone
        $rs1 = (new Query)
            ->select(['*'])
            ->from($this->table)
            ->where(['phone'=>$this->phone, 'status'=>0])
            ->one()
        ;

        if (!$rs1) {
            throw new \Exception(Yii::$app->params['codeinfo']['2'], 2);
        }

        // validate code
        $rs2 = Yii::$app->cache->get($this->phone) == $this->code ? true : false;

        if (!$rs2) {
            throw new \Exception(Yii::$app->params['codeinfo']['3'], 3);
        }
        
        $data = ['uid'=>$rs1['id']];
        $token = Token::createToken($key,$data);

        return ['token'=>$token];
    }

}