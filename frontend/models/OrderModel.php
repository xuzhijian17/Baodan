<?php
namespace frontend\models;

use Yii;
use yii\db\Query;

/**
 * order model
 * @package frontend\models
 */
class OrderModel extends BaseModel
{
    public $id;
	public $name;
    public $sex;
    public $phone;
    public $order_type;
    public $district_id;
    public $loans;
    public $points;
    public $units;
    public $note;
    public $page;
    public $pageSize;

    protected $table = 'dk_order';

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
            'default' => ['page','pageSize'],
            'addOrder' => ['name', 'sex', 'phone', 'order_type', 'note','district_id'],
            'overOrder' => ['id','loans','points','units']
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
            ['sex','default','value'=>0],
            ['note','default','value'=>''],
            ['units','default','value'=>0],
            ['page','default','value'=>1],
            ['pageSize','default','value'=>10],
            [['name', 'phone', 'order_type','district_id'],'required','on'=>'addOrder'],
            [['id','loans','points'],'required','on'=>'overOrder'],
        ];
    }

    public function addOrder($uid)
    {
        $order_type = json_decode($this->order_type);
        if (!$order_type) {
            throw new \Exception(Yii::$app->params['codeinfo'][1], 1);
        }

        foreach ($order_type as $key => $value) {
            if ((new Query)->from($this->table)->where(['phone'=>$this->phone,'order_type'=>$value])->one()) {
                continue;
            }

            Yii::$app->db->createCommand()->insert($this->table, [
                'user_id' => $uid,
                'order_no' => md5(uniqid('order_',true)),
                'name' => $this->name,
                'sex' => $this->sex,
                'phone' => $this->phone, 
                'order_type' => $value,
                'note' => $this->note,
                'district_id' => $this->district_id,
                'created_at' => date("Y-m-d H:i:s"),
            ])->execute();

            Yii::$app->redis->executeCommand('LPUSH',['orderIdList',Yii::$app->db->getLastInsertID()]);
        }

        return;
    }

    public function getOrdersByUid($uid)
    {
        $data =[];

        $rs = (new Query())
        ->from($this->table)
        ->where(['user_id'=>$uid])
        ->limit($this->pageSize)
        ->offset(($this->page-1)*$this->pageSize)
        ->orderBy(['updated_at'=>SORT_DESC,'created_at'=>SORT_DESC])
        ->all();

        if (!empty($rs)) {
            foreach ($rs as $key => $value) {
                $data[$value['phone']][] = $value;
            }
        }

        return $data;
    }

    public function getOrderList($uid,$status=0)
    {
        $data = [];

        $aOrderType = (new Query())->select('dk_product.id')->from('dk_employee')->leftJoin('dk_product', 'dk_product.department_id = dk_employee.department_id')->where(['dk_employee.id'=>$uid,'dk_employee.status'=>0])->createCommand()->queryColumn();
        
        if (!empty($aOrderType)) {
            $data = (new Query())
                ->from($this->table)
                ->where(['status'=>$status,'order_type'=>$aOrderType])
                ->limit($this->pageSize)
                ->offset(($this->page-1)*$this->pageSize)
                ->orderBy(['updated_at'=>SORT_DESC,'created_at'=>SORT_DESC])
                ->all()
            ;
        }

        return $data;
    }

    public function acceptOrder($id,$uid)
    {
        $rs = Yii::$app->db->createCommand()->update($this->table, [
            'status' => 1,
            'e_id' => $uid,
            'updated_at' => date('Y-m-d H:i:s')
        ],['id' => $id, 'status'=>0])->execute();
        
        if (empty($rs)) {
            throw new \Exception(Yii::$app->params['codeinfo']['101'], 101);
        }
    }

    public function declineOrder($id,$uid)
    {
        $rs = Yii::$app->db->createCommand()->update($this->table, [
            'status' => 4,
            'e_id' => $uid,
            'updated_at' => date('Y-m-d H:i:s')
        ],['id' => $id, 'status'=>[0,1]])->execute();
        
        if (empty($rs)) {
            throw new \Exception(Yii::$app->params['codeinfo']['102'], 102);
        }
    }

    public function getAcceptOrderByEid($eid)
    {
        return (new Query)->from($this->table)->where(['e_id'=>$eid,'status'=>1])->all();
    }

    public function overOrder($uid)
    {
        $rs = Yii::$app->db->createCommand()->update($this->table, [
            'status' => 2,
            'loans' => $this->loans,
            'points' => $this->points,
            'units' => $this->units,
            'employee_id' => $uid,
            'updated_at' => date('Y-m-d H:i:s')
        ],['id' => $this->id, 'status'=>1])->execute();
        
        if (empty($rs)) {
            throw new \Exception(Yii::$app->params['codeinfo']['103'], 103);
        }
    }

    public function cancelOrder($id,$uid)
    {
        $rs = Yii::$app->db->createCommand()->update($this->table, [
            'status' => 0,
            'e_id' => $uid,
            'updated_at' => date('Y-m-d H:i:s')
        ],['id' => $id, 'status'=>[0,1]])->execute();
        
        if (empty($rs)) {
            throw new \Exception(Yii::$app->params['codeinfo']['104'], 104);
        }
    }
}