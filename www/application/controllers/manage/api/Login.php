<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Headers:x-requested-with,content-type,x-hzdamei');
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH.'controllers/manage/api/BaseApiController.php');

class Login extends BaseApiController{
    public function __construct(){
        parent::__construct();
        $this->load->model('manage_model');
    }

    //检测登录
    public  function login_check(){
        $username  = $this->input->post('username');
        $password  = $this->input->post('password');

        $userInfo = $this->manage_model->getByUserName($username);
        //var_dump($userInfo);exit;
        if(isArray($userInfo) && $userInfo['is_delete'] == EnumRes::DELETE_YES || $userInfo['status'] == EnumRes::USER_STATUS_FORBID){
            $this->outputFail('账户已被禁用');
        }
        if(isArray($userInfo) && $userInfo['password'] && $userInfo['password'] === md5Password(md5($password),$userInfo['salt'])){
            $adata = array();
            $adata['last_login_time'] = time();
            $adata['first_login_ip'] = $this->input->ip_address();
            $adata['final_login_ip'] = $this->input->ip_address();
            $adata['login_count'] = $userInfo['login_count']+1;
            $adata['modified_time'] = time();
            $this->manage_model->update($userInfo['id'], $adata);
            $userInfo = $this->manage_model->getByUserName($username);
            $verifycode = $this->manage_model->createloginCode($userInfo);
            $userToken = array('id' => $userInfo['id'] ,'verifycode' => $verifycode, 'last_login_time' => $adata['last_login_time']); 
            $userToken = base64_encode(implode('t/',$userToken));
            set_cookie('manager_user_token', $userToken, 0);
            $data = array();
            $data['token'] = $userToken;
             //var_dump($data['token']);exit;
            $data['id'] = $userInfo['id'];
            $this->outputSuccess('登录成功',$data);
        }else{
            $this->outputFail('用户密码错误');
        }

    }

    /**
     * logout 注销
     *
     */
    public function logout(){
        set_cookie('manager_user_token', '', -1);
        $this->outputSuccess('ok');
    }
    

}