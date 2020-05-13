<?php 
header("Access-Control-Allow-Origin: *");
require_once( APPPATH . 'controllers/manage/api/BaseApiController.php');

class Attribute extends BaseApiController {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('attribute_model');
        // $this->checkApiLogin();
    }

    public function index(){
        $attrName = $this->input->get('attr_name');
        $attrType = $this->input->get('attr_type');
        $sort = $this->input->get('sort');
        $sortRule = $this->input->get('sort_rule');
        $page = $this->input->get('page');
        $pageSize = $this->input->get('page_size');
        !$page && $page = 1;
        !$pageSize && $pageSize = 20;
        $optionsCn = array('conditions'=>array('is_delete'=>0,'lang'=>'zh_cn'),'order_chinese'=>'attr_name','order'=>'id desc');
        $optionsEn = array('conditions'=>array('is_delete'=>0,'lang'=>'en'),'order'=>'id desc');
        
        if(isset($attrName) && $attrName){
            $optionsEn['conditions']['attr_name'] = $optionsCn['conditions']['attr_name'] = $attrName;
        }
        if(isset($attrType) && $attrType !=''){
           $optionsEn['conditions']['attr_type'] =  $optionsCn['conditions']['attr_type'] = $attrType;
        }
        if(isset($sort) && $sort && $sortRule){
            $sortRule = $sortRule == 1 ? 'ASC' : 'DESC';
            $optionsEn['conditions']['order'] = $optionsCn['order'] = $sort. ' ' .$sortRule;
        }
        $totalCn = $this->attribute_model->count($optionsCn);
        $totalEn = $this->attribute_model->count($optionsEn);
        $listCn = $listEn = array();
        if($totalCn){
            $listCn = $this->attribute_model->find($optionsCn ,$pageSize,($page -1) * $pageSize);
        }
        if($totalEn){
            $listEn = $this->attribute_model->find($optionsEn ,$pageSize,($page -1) * $pageSize);
        }
        $listEnMap = list2Map2($listEn,'id');
        foreach($listCn as &$val){
             $val['attr_name_en'] = isset($listEnMap[$val['relation_id']]) && isArray($listEnMap[$val['relation_id']]) ? $listEnMap[$val['relation_id']]['attr_name'] : '';
        }
        $data = array();
        $data['list']['zh_cn']['total'] = $totalCn;
        $data['list']['zh_cn']['list']  = $listCn;
        $data['list']['en']['total']    = $totalEn;
        $data['list']['en']['list']     = $listEn;
        $this->outputSuccess('ok',$data);
    }


    public function detail(){
        $id = $this->input->get('id');
        if (!$id) {
            $this->outputFail('参数为空');
        }
        $attributeInfoCn = $this->attribute_model->load($id);
        if (!isArray($attributeInfoCn)) {
            $this->outputFail('属性不存在');
        }
        $itemCn                  = array();
        $itemCn['id']            = $attributeInfoCn['id'];
        $itemCn['attr_name']       = $attributeInfoCn['attr_name'];
        $itemCn['attr_value']        = $attributeInfoCn['attr_value'];
        $itemCn['attr_type']        = $attributeInfoCn['attr_type'];
        $itemCn['sort']      = $attributeInfoCn['sort'];

        $attributeInfoEn = array();
        if($attributeInfoCn['relation_id']){
            $attributeInfoEn = $this->attribute_model->load($attributeInfoCn['relation_id']);
        }
        if(isArray($attributeInfoEn)){
            $itemen                  = array();
            $itemen['id']            = isArray($attributeInfoEn) ? $attributeInfoEn['id'] : '';
            $itemen['attr_name']       = isArray($attributeInfoEn) ? $attributeInfoEn['attr_name'] : '';
            $itemen['attr_value']        = isArray($attributeInfoEn) ? $attributeInfoEn['attr_value'] : '';
            $itemen['attr_type']        = isArray($attributeInfoEn) ? $attributeInfoEn['attr_type'] : '';
            $itemen['sort']      = isArray($attributeInfoEn) ? $attributeInfoEn['sort'] : '';
        }
        $data                    = array();
        $data['info']['zh_cn'] = isArray($itemCn) ? $itemCn : array();
        $data['info']['en']    = isArray($itemen) ? $itemen : array();
        $this->outputSuccess('ok', $data);
    }

    public function save()
    {
        $attribueParams = json_decode($this->input->post('attr_params'), true);
        if (!isArray($attribueParams)) {
            $this->outputFail('参数有误');
        }
        $cnParams = isset($attribueParams['zh_cn']) ? $attribueParams['zh_cn'] : array();
        $enParams = isset($attribueParams['en']) ? $attribueParams['en'] : array();
        if(isset($cnParams['id']) && $cnParams['id']){
          $cnId = $cnParams['id'];
          $cnInfo = $this->attribute_model->load($cnId);
          if(!isArray($cnInfo)){
            $this->outputFail('参数有误');
          }
          $enId = $cnInfo['relation_id'];
          $enInfo = $this->attribute_model->load($enId);
        }

        if (isArray($cnParams)) {
            $cndata['lang']          = 'zh_cn';
            $cndata['attr_name']     = $cnParams['attr_name'];
            $cndata['attr_value']      = $cnParams['attr_value'];
            $cndata['attr_type']      = $cnParams['attr_type'];
            $cndata['sort']        = $cnParams['sort'];
            if(isset($cnInfo) && isArray($cnInfo)){
                $cndata['relation_id'] = $cnInfo['relation_id'];
                if (!$this->attribute_model->update($cnId, $cndata)) {
                  $this->outputFail('操作失败');
                }
            } else{
                $cnId = $this->attribute_model->create($cndata);
            }
        }
        if (isArray($enParams)) {
            $endata['lang']          = 'en';
            $endata['attr_name']     = $enParams['attr_name'];
            $endata['attr_value']      = $enParams['attr_value'];
            $endata['attr_type']      = $enParams['attr_type'];
            $endata['sort']        = $enParams['sort'];
            if(isset($enInfo) && isArray($enInfo)){
                if (!$this->attribute_model->update($enId, $endata)) {
                  $this->outputFail('操作失败');
                }
            } else{
                $endata['relation_id'] = $cnId;
                $enId = $this->attribute_model->create($endata);
                $this->attribute_model->update($cnId,array('relation_id'=>$enId));
            }
        }
        $this->outputSuccess('操作成功');
    }

    /**
     * del 删除
     */
    public function del(){
        $ids = json_decode($this->input->post('ids'));

        if(!isArray($ids)){
            $this->outputFail('参数错误');
        }
        $attrInfo = $this->attribute_model->getByIds($ids);
        $enIds = array();
        if(isArray($attrInfo)){
            $enIds = array_column($attrInfo, 'relation_id');
            $attrEnInfo = $this->attribute_model->getByIds($enIds);
        }
        
        $data = array();
        $data['is_delete'] = EnumRes::DELETE_YES;
        if(isArray($attrInfo)){
            foreach($attrInfo as $attr){
                if(!$this->attribute_model->update($attr['id'],$data)){
                    $this->outputFail('删除中文失败');
                }
            }
        }
        if(isset($attrEnInfo) && isArray($attrEnInfo)){
            foreach($attrEnInfo as $attrEn){
                if(!$this->attribute_model->update($attrEn['id'],$data)){
                    $this->outputFail('删除英文失败');
                }
            }
        }
        $this->outputSuccess('删除成功');
    }

}



?>

