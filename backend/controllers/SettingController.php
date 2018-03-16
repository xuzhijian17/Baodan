<?php
namespace backend\controllers;

use Yii;

/**
 * Setting controller
 */
class SettingController extends BaseController
{

    /**
     * @return string
     */
    public function actionIndex()
    {
        echo "string";
    }

    // 部门列表
    public function actionDepartmentList($value='')
    {
        $params = Yii::$app->request->get();

        $model = new \backend\models\Department();
        $data = $model->getList();

        var_dump($data);
    }

    // 添加部门
    /*public function actionAddDepartment($value='')
    {
        $name = Yii::$app->request->post('name');

        $model = new \backend\models\Department();
        $data = $model->add($name);

        var_dump($data);
    }*/

    // 产品列表
    public function actionProductList($value='')
    {
        $params = Yii::$app->request->get();

        $model = new \backend\models\Product();
        $data = $model->getList();

        var_dump($data);
    }

    // 添加产品
    public function actionAddDepartment($value='')
    {
        $name = Yii::$app->request->post('name');
        $department_id = Yii::$app->request->post('department_id');

        $model = new \backend\models\Product();
        $data = $model->add($name,$department_id);

        var_dump($data);
    }

    // 角色列表
    public function actionRoleList($value='')
    {
        $params = Yii::$app->request->get();

        $model = new \backend\models\Role();
        $data = $model->getList();

        var_dump($data);
    }

    // 添加角色
    public function actionAddRole($value='')
    {
        $name = Yii::$app->request->post('name');

        $model = new \backend\models\Role();
        $data = $model->add($name);

        var_dump($data);
    }

    // 后台账号列表
    public function actionAdminList($value='')
    {
        $params = Yii::$app->request->get();

        $model = new \backend\models\Admin();
        $data = $model->getList();

        var_dump($data);
    }

    // 添加后台账号
    public function actionAddAccount($value='')
    {
        $username = Yii::$app->request->post('username');
        $password = Yii::$app->request->post('password');
        $role_id = Yii::$app->request->post('role_id');

        $model = new \backend\models\Admin();
        $data = $model->add($username,$password,$role_id);

        var_dump($data);
    }
}
