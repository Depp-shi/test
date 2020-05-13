<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Headers:x-requested-with,content-type,x-hzdamei');
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH.'controllers/manage/api/BaseApiController.php');

class Setting extends BaseApiController{
    public function __construct(){
        parent::__construct();
        $this->load->model('config_model');
    }

    
    /**
     * 查询基本配置
     * @return [type] [description]
     */
    public function index(){
        $type = $this->input->get('type');
        $configArr = $this->config_model->find(array(),0,0);
        $cnArr = $enArr = array();
        if(isArray($configArr)){
            foreach($configArr as $config){
                if($config['lang'] == 'zh_cn'){
                    $cnArr[$config['keyname']] = $config['value'];
                }
                if($config['lang'] == 'en'){
                    $enArr[$config['keyname']] = $config['value'];
                }
            }
        }
        $data = array();
        if($type == 'basic'){
            $data['info']['zh_cn'] = $this->buildBasicFeilds($cnArr);
            $data['info']['en'] = $this->buildBasicFeilds($enArr);
        }
        if($type == 'smtp'){
            $data['info']['zh_cn'] = $this->buildSmtpFeilds($cnArr);
            $data['info']['en'] = $this->buildSmtpFeilds($enArr);
        }
        if($type == 'attachment'){
            $data['info']['zh_cn'] = $this->buildAttachmentFeilds($cnArr);
            $data['info']['en'] = $this->buildAttachmentFeilds($enArr);
        }
        
        $this->outputSuccess('ok',$data);
    }

    protected function buildBasicFeilds($result){
        $data = array(); 
        if(isArray($result)){
            $data['site_name'] = isset($result['site_name']) ? $result['site_name'] : ''; 
            $data['seo_keywords'] = isset($result['seo_keywords']) ? $result['seo_keywords'] : ''; 
            $data['seo_description'] = isset($result['seo_description']) ? $result['seo_description'] : ''; 
            $data['statistic_code'] = isset($result['statistic_code']) ? $result['statistic_code'] : ''; 
            $data['record'] = isset($result['record']) ? $result['record'] : ''; 
            $data['site_switch'] = isset($result['site_switch']) ? $result['site_switch'] : ''; 
            $data['close_reason'] = isset($result['close_reason']) ? $result['close_reason'] : ''; 
        }
        return $data;
    }

    protected function buildSmtpFeilds($result){
        $data = array(); 
        if(isArray($result)){
            $data['smtp_host'] = isset($result['smtp_host']) ? $result['smtp_host'] : ''; 
            $data['smtp_user'] = isset($result['smtp_user']) ? $result['smtp_user'] : ''; 
            $data['smtp_pass'] = isset($result['smtp_pass']) ? $result['smtp_pass'] : ''; 
            $data['smtp_port'] = isset($result['smtp_port']) ? $result['smtp_port'] : ''; 
            $data['smtp_sendmail'] = isset($result['smtp_sendmail']) ? $result['smtp_sendmail'] : ''; 
        }
        return $data;
    }

    protected function buildAttachmentFeilds($result){
        $data = array(); 
        if(isArray($result)){
            $data['attr_maxsize'] = isset($result['attr_maxsize']) ? $result['attr_maxsize'] : ''; 
            $data['attr_type'] = isset($result['attr_type']) ? $result['attr_type'] : ''; 
            $data['is_upload_upyun'] = isset($result['is_upload_upyun']) ? $result['is_upload_upyun'] : ''; 
        }
        return $data;
    }

    /**
     * 修改添加基本配置
     * @return [type] [description]
     */
    public function edit(){
        $basicParams = json_decode($this->input->post('basic_params'),true);
        if(!isArray($basicParams)){
            $this->outputFail('参数有误，请检查重试');
        }
        $basicCnArr = isset($basicParams['zh_cn']) ? $basicParams['zh_cn'] : array();
        $basicEnArr = isset($basicParams['en']) ? $basicParams['en'] : array();

        if(isset($basicCnArr) && isArray($basicCnArr)){
            $cnData = array();
            foreach($basicCnArr as $key => $val){
                $configArr = $this->config_model->getByKeyAndLang($key,'zh_cn');
                $cnData = array('keyname'=>$key,'value'=>$val,'lang'=>'zh_cn');
                if(isArray($configArr)){
                    $this->config_model->updateByKeynameAndLang($key,'zh_cn',$cnData);
                }else{
                    if($val){
                       if(!$this->config_model->create($cnData)){
                           $this->outputFail('添加中文配置失败');
                       }
                    }
                }
            }
        }
        if(isset($basicEnArr) && isArray($basicEnArr)){
            $enData = array();
            foreach($basicEnArr as $key => $val){
                $configArr = $this->config_model->getByKeyAndLang($key,'en');
                $enData = array('keyname'=>$key,'value'=>$val,'lang'=>'en');
                if(isArray($configArr)){
                    $this->config_model->updateByKeynameAndLang($key,'en',$enData);
                }else{
                    if($val){
                       if(!$this->config_model->create($enData)){
                           $this->outputFail('添加英文配置失败');
                       }
                    }
                }
            }
        }
        $this->outputSuccess('操作成功');
    }

    
}