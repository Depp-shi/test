<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BaseController extends CI_Controller{

    protected $_logger;

    public function __construct(){
        parent::__construct();
        $this->load->library('encodeUtil');
    }

    public function checkAdminLogin(){
    }

    public function getCommonData() {
        return array('user'=>'hello world');
    }
    
    public function outputForbid($msg = '', $result = array(), $callback = ''){
        return $this->formatOutput(EnumRes::RET_CODE_FORBID, $msg, $result, $callback);
    }

    public function outputFail($msg = '', $result = array(), $callback = ''){
        return $this->formatOutput(EnumRes::RET_CODE_FAIL, $msg, $result, $callback);
    }

    public function outputSuccess($msg = '', $result = array(), $callback = ''){
        return $this->formatOutput(EnumRes::RET_CODE_OK, $msg, $result, $callback);
    }

    public function formatOutput($code = GeneralRes::RET_CODE_OK, $msg = '', $result = array(), $callback = ''){
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

}