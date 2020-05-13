<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once FCPATH . '/application/controllers/BaseController.php';

class BaseApiController extends BaseController{

    protected $_app;
    protected $_upyun = array();
    protected $_userInfo = array();
    protected $_id = 0;

    public function __construct(){
        parent::__construct();
        $this->load->model('manage_model');
        header("Content-type:application/json");
        header('Access-Control-Allow-Headers:x-requested-with,content-type,x-hzdamei');
    }

    public function checkUserLogin(){
    }

    public function checkApiLogin(){
        $token = isset($_SERVER['HTTP_X_HZDAMEI']) ? $_SERVER['HTTP_X_HZDAMEI'] : '';
        $userToken = explode('t/',base64_decode($token));
        if(!isArray($userToken) || count($userToken) != 3){
            return $this->outputForbidden();
        }

        if(!isArray($this->manage_model->getInfoById($userToken[0]))){
           return $this->outputFail('网络异常，请刷新重试');
        }
        // if(time() - $userToken[2] > 72000){
        //    return $this->outputForbidden('登录超时,请重新登录');
        // }

        $Id = $userToken[0];
        $verifyCode = $userToken[1];
        if($Id && $verifyCode){
            $userInfo = $this->manage_model->getInfoById($Id);
            if(isArray($userInfo) && $this->manage_model->createloginCode($userInfo) == $verifyCode){
                $this->_id = $userInfo['id'];
                $this->_userInfo = $this->build_UserInfo($userInfo);
            }else{
                return $this->outputForbidden('登录超时,请重新登录');
            }
        }
    }

    private function build_UserInfo($_userInfo){
        if(!isArray($_userInfo)){
            return array();
        }
        return $_userInfo;
    }

    public function checkAjaxRequest(){
        return $this->input->is_ajax_request() && $this->input->post_get('_from_ajax_request_');
    }

    public function outputFail($msg = '', $result = array(), $callback = ''){
        return $this->formatOutput(EnumRes::RET_CODE_FAIL, $msg, $result, $callback);
    }

    public function outputForbidden($msg = ''){
        return $this->formatOutput(EnumRes::RET_CODE_FORBIDDEN, $msg);
    }

    public function outputSuccess($msg = '', $result = array(), $callback = ''){
        return $this->formatOutput(EnumRes::RET_CODE_OK, $msg, $result, $callback);
    }

    public function formatOutput($code = EnumRes::RET_CODE_OK, $msg = '', $result = array(), $callback = ''){
        $data = array();
        $data['code'] = $code;
        $data['msg'] = $msg;
        $data['data'] = $result;
        return $this->jsonOutput($callback, $data);
    }

    public function jsonOutput($callback, $data = array()) {
        if ($callback) {
            $out = sprintf('%s(%s)', $callback, json_encode($data));
        } else {
            $out = json_encode($data);
        }
        print_r($out);
        exit ();
    }
    
    /**
     * 初始化又拍云配置
     * @return [type] [description]
     */
    public function initUpyun($app){
        $upyunEnum = UpyunEnum::enum();
        $config = array();
        $config['bucket_name'] = $upyunEnum[$app]['bucket_name'];
        $config['form_api_secret'] = $upyunEnum[$app]['api_secret'];
        $config['operator'] = $upyunEnum[$app]['operator'];
        $config['operator_password'] = $upyunEnum[$app]['operator_password'];
        $this->_upyun = $config;
        $this->_app = $app;
        $this->load->library('upyun',$config); 
    }

    public function createActionLog($content = ''){
        $data = array();
        $data['content'] = $content;
        $data['operate'] = $this->_userInfo['truename'];
        $data['operate_id'] = $this->_id;
        $data['ip'] = getRealIp();
        $this->load->model('user_log_model');
        $this->user_log_model->create($data);
    }
}