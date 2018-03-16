<?php
namespace frontend\models;

use Yii;
use yii\db\Query;
use common\models\Token;

/**
 * employee model
 */
class EmployeeModel extends BaseModel
{

	public $phone;
    public $code;

    protected $table = 'dk_employee';

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
        
        $data = ['uid'=>$rs1['id'],'did'=>$rs1['department_id']];
        $token = Token::createToken($key,$data);

    	return ['token'=>$token];
    }
}