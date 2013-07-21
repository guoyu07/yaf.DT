<?php
/**
 * Class UserController
 */
class UserController extends Controller
{

    /**
     * @var models_user
     */
    public $model;

    /**
     * @var models_emailset
     */
    private $model_email_set;

    public function init()
    {
        parent::init();
        $this->model           = models_user::getInstance();
        $this->model_email_set = models_emailset::getInstance();
    }

    /**
     * 注册
     */
    public function regAction()
    {
        $this->rest->method('POST');

        $params                    = $this->getRequest()->getPost();
        $this->rest->paramsMustMap = array('email', 'pwd', 'user_name');
        $this->rest->paramsMustValid($params);

        if ($this->model->exits(array('user_email' => $params['email']))) {
            $this->rest->error(rest_Code::STATUS_SUCCESS_DO_ERROR_DB_REPEAT, 'email已存在');
        }

        if ($this->model->exits(array('user_name' => $params['user_name']))) {
            $this->rest->error(rest_Code::STATUS_SUCCESS_DO_ERROR_DB_REPEAT, '用户名已存在');
        }

        $data   = $this->model->mkdata($params);
        $result = $this->model->insert($data);

        if ($result == FALSE) $this->rest->error(rest_Code::STATUS_SUCCESS_DO_ERROR);

        $this->rest->success('', rest_Code::STATUS_SUCCESS, '注册成功，请登录');
    }

    /**
     * 登录
     */
    public function loginAction()
    {
        $this->rest->method('POST');

        $params                    = $this->getRequest()->getPost();
        $this->rest->paramsMustMap = array('pwd', 'user_name');
        $this->rest->paramsMustValid($params);

        $result = $this->model->login($params['user_name'], $params['pwd']);
        if ($result == FALSE) $this->rest->error(rest_Code::STATUS_SUCCESS_DO_ERROR_DB_NULL, '登录失败');

        $this->rest->success();
    }

    /**
     * 初始化个人设置信息
     */
    public function setinitAction()
    {
        $this->rest->method('GET');

        $data = array();
        $info = $this->model->getUserInfoAll();
        if (!is_array($info) || count($info) < 1) $this->rest->error();

        $data['email'] = $info['user_email'];
        $data['username'] = $info['user_name'];
        $data['avatar'] = $info['face_url'];
        $data['gender'] = $info['gender'];

        $data['email_set'] = $this->model_email_set->getEmailSet($this->user_id);

        $this->rest->success($data);
    }

    /**
     * 个人设置
     */
    public function setAction()
    {
        $this->rest->method('POST');
        $params                   = $this->allParams();
        $this->rest->paramsCanMap = array('email', 'pwd', 'avatar', 'gender', 'email_set');
        $this->rest->paramsMustValid($params);

        /**
         * 更新email
         */
        if (array_key_exists('email', $params)) {
            $email = $this->getRequest()->getPost('email',0);
            $result = $this->model->updateEmail($email);
            if (!$result) $this->rest->error('','email更新出现错误');
        }

        if (array_key_exists('pwd', $params)) {
            $pwd = $this->getRequest()->getPost('pwd',0);

            $this->rest->paramsMustMap = array('new','old','repeat');
            $this->rest->paramsMustValid($pwd);

            $result = $this->model->updatePwd($pwd);
            if (!$result) $this->rest->error('','密码更新出现错误');
        }

        if (array_key_exists('avatar', $params)) {
            $avatar = $this->getRequest()->getPost('avatar',0);
            $result = $this->model->updateAvatar($avatar);
            if (!$result) $this->rest->error('','头像更新出现错误');
        }

        if (array_key_exists('gender', $params)) {
            $gender = $this->getRequest()->getPost('gender',contast_user::GENDER_MAN);
            $result = $this->model->updateGender($gender);
            if (!$result) $this->rest->error('','性别更新出现错误');
        }

        if (array_key_exists('email_set', $params)) {
            $email_set = $this->getRequest()->getPost('email_set',0);
            $email_set['user_id'] = $this->user_id;
            $result = $this->model_email_set->updateEmailSet($email_set);
            if (!$result) $this->rest->error('','邮件设置更新出现错误');
        }

        $this->rest->success('', '', '修改成功');
    }

    /**
     * 登出
     */
    public function logoutAction()
    {
        $this->rest->method('GET');
        $this->session->del('userinfo');
        $this->rest->success();
    }

}
