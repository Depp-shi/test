<?php
require_once APPPATH . 'controllers/manage/api/BaseApiController.php';
class Sys extends BaseApiController
{
    public function __construct()
    {
        parent::__construct();
        $action = $this->uri->segment(3);

    }

    /**
     * 全局枚举类型
     *
     */
    public function enums()
    {
        $data = array();
        //全局
        $data['site_switch_enum'] =  $this->buildEnumMap(EnumRes::EnumSiteSwitch());
        $data['attachment_type_enum'] = $this->buildEnumMap(EnumRes::EnumAttachmentType());
        $data['attachment_data_type_enum'] = $this->buildEnumMap(EnumRes::EnumAttachmentDataType());
        $data['is_delete'] = $this->buildEnumMap(EnumRes::EnumIsDelete());
        $data['ads_status'] = $this->buildEnumMap(EnumRes::EnumAdsStatus());
        $data['ads_banner'] = $this->buildEnumMap(EnumRes::EnumAdsBanner());
        $data['language'] = $this->buildEnumMap(EnumRes::EnumLanguage());
        $data['article_status'] = $this->buildEnumMap(EnumRes::EnumArticleStatus());
        $roleActions = array();
        $roleActions[] = RoleRes::EnumSiteActions();
        $roleActions[] = RoleRes::EnumAdvertisementActions();
        $roleActions[] = RoleRes::EnumContentActions();
        $roleActions[] = RoleRes::EnumManageActions();
        $roleActions[] = RoleRes::EnumAttachmentActions();
        $roleActions[] = RoleRes::EnumProductActions();

        $data['role_actions'] = $roleActions;
        return $this->outputSuccess('ok', $data);
    }
    /**
     * 首页浏览数据
     */
    public function home(){
        $data = array();
        $data['today_login_operate'] = array();//今日登录的操作者；
        $data['7_day_article_count'] = array();//7天添加文章数量统计；
    }

    /**
     * 清缓存
     * @return [type] [description]
     */
    public function clean_cache()
    {
        $this->load->driver('cache', array('adapter' => 'file'));
        if ($this->cache->clean()) {
            $this->outputSuccess('清除缓存成功');
        } else {
            $this->outputSuccess('清除缓存失败,请重试');
        }
    }

    private function buildEnumMap($data)
    {
        $result = array();
        foreach ($data as $k => $v) {
            $result[] = array("id" => $k, "title" => $v);
        }
        return $result;
    }

    /**
     * 读取ueditor配置
     * @return [type] [description]
     */
    public function ueditor()
    {
        $confStr = file_get_contents(RESPATH . "/json/ueditor_conf.json");
        //var_dump($confStr);
        $confArr = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", $confStr), true);

        $confArr['imageFormUrl']  = site_url('manage/api/common/upload');
        $confArr['imageUrlPrefix'] = base_url();
        $confArr['imageFormData'] = array('file'=>'file','type'=>'img');

        $confArr['fileFormUrl']  = site_url('manage/api/common/upload');
        $confArr['fileUrlPrefix'] = base_url();
        $confArr['fileFormData'] = array('file'=>'file','type'=>'file');

        $confArr['videoFormUrl']  = site_url('manage/api/common/upload');
        $confArr['videoUrlPrefix'] = base_url();
        $confArr['videoFormData'] = array('file'=>'file','type'=>'video');
        print_r(json_encode($confArr));
    }
}
