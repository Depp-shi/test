<?php
require_once (APPPATH . 'controllers/manage/api/BaseApiController.php');
/**
 * 分类管理
 */
class Product extends BaseApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('attachment_model');
        $this->load->model('product_model');
        $this->load->model('product_category_model');
        $this->load->model('product_attribute_relation_model');

    }
    
    /**
     *  edit() 分类编辑
     **/
    public function save()
    {
        $productParams = json_decode($this->input->post('product_params'), true);
        if (!isArray($productParams)) {
            $this->outputFail('参数有误');
        }
        $cnParams = isset($productParams['zh_cn']) ? $productParams['zh_cn'] : array();
        $enParams = isset($productParams['en']) ? $productParams['en'] : array();
        if(isset($cnParams['id']) && $cnParams['id']){
          $cnId = $cnParams['id'];
          $cnInfo = $this->product_model->load($cnId);
          if(!isArray($cnInfo)){
              $this->outputFail('参数有误');
          }
          $enId = $cnInfo['relation_id'];
          $enInfo = $this->product_model->load($enId);
        }
        if (isArray($cnParams)) {
            if(!$cnParams['product_name']){
               $this->outputFail('产品名称不能为空');
            }
            $cndata['lang']          = 'zh_cn';
            $cndata['cate_id']     = $cnParams['cate_id'];
            $cndata['product_name']  = $cnParams['product_name'];
            $cndata['keyWords']      = $cnParams['keywords'];
            // $cndata['remark']        = $cnParams['remark'];
            $cndata['sort']          = $cnParams['sort'];
            $cndata['cover']          = $cnParams['cover'];
            $cndata['photos']         = $cnParams['photos'];
            $cndata['attachment']         = $cnParams['attachment'] ? implode(';',array_column($cnParams['attachment'],'fileid')) : '';
            $cndata['status']         = $cnParams['status'];
            $cndata['description']   = $cnParams['description'];

            $attributeCnParams = $cnParams['attribute_params'];
            
            if(isset($cnInfo) && isArray($cnInfo)){
                $cndata['relation_id'] = $cnInfo['relation_id'];
                if (!$this->product_model->update($cnId, $cndata)) {
                  $this->outputFail('操作失败');
                }
                //分类改变，清空该产品与属性的关联
                if($cnInfo['cate_id'] != $cnParams['cate_id']){
                    $productAttributeRelationCn = $this->product_attribute_relation_model->getByProductId($cnInfo['id']);
                    if(isArray($productAttributeRelationCn)){
                        foreach ($productAttributeRelationCn as $value) {
                            $this->product_attribute_relation_model->delete($value['id']);
                        }
                    }
                }else{
                    $oldAttribuateRelationCnIds = $cnInfo['attribute_relation_ids'] ? explode(';',$cnInfo['attribute_relation_ids']) : array();
                    $newAttributeRelationsCnIds = array_column($attributeCnParams,'attrbute_relation_id');
                    $attributeRelationsCnIds = isArray($newAttributeRelationsCnIds) ? array_diff($oldAttribuateRelationCnIds,$newAttributeRelationsCnIds) : array();
                    $diffCnRelations = isArray($attributeRelationsCnIds) ? $this->product_attribute_relation_model->getByIds($attributeRelationsCnIds) : array();
                    if(isArray($diffCnRelations)){
                        foreach($diffCnRelations as $relation){
                            $this->product_attribute_relation_model->delete($realtion['id']);
                        }
                    }
                }
            } else{
                $cnId = $this->product_model->create($cndata);
                log_message('DEBUG',sprintf('product create sql %s',$this->db->last_query()));
            }
            // $attributeCnParams = json_decode($cnParams['attribute_params'],true);
            
            $attributeCnRelationIds = array();
            if(isArray($attributeCnParams)){
                foreach($attributeCnParams as $attribute){
                    $cnAttr = array();
                    $cnAttr['attr_id'] = $attribute['attr_id'];
                    $cnAttr['product_id'] = $cnId;
                    $cnAttr['attr_value'] = $attribute['attr_value'];
                    if(!$attribute['attrbute_relation_id']){
                        $attribute_relation_id = $this->product_attribute_relation_model->create($cnAttr);
                        $attributeCnRelationIds[] = $attribute_relation_id;
                    }else{
                        $attributeCnRelationIds[] = $attribute['attrbute_relation_id'];
                        $this->product_attribute_relation_model->update($attribute['attrbute_relation_id'],$cnAttr);
                    }
                }
                $this->product_model->update($cnId, array('attribute_relation_ids'=>implode(';',$attributeCnRelationIds)));
            }
            //更新附件表
            if ($cndata['cover']) {
                $attCn                     = array();
                $attCn['title']            = '';
                $attCn['origin_id']        = $cnId;
                $attCn['origin_data_type'] = 4;
                $this->attachment_model->update($cnParams['cover'], $attCn);
            }
            if($cndata['photos']){
                $photosCnArr = explode(';',$cnParams['photos']);
                foreach ($photosCnArr as $key=>$val) {
                    $attCnP = array();
                    $attCnP['title'] = '';
                    $attCnP['sort'] = $key+1;
                    $attCnP['origin_id'] = $cnId;
                    $attCnP['origin_data_type'] = 4;                      
                    $this->attachment_model->update($val,$attCnP);
                }
            }
            if($cnParams['attachment']){
                foreach ($cnParams['attachment'] as $val) {
                    $attCnA = array();
                    $attCnA['title'] = $val['name'];
                    $attCnA['origin_id'] = $cnId;
                    $attCnA['origin_data_type'] = 4;                      
                    $this->attachment_model->update($val['fileid'],$attCnA);
                }
            }
            
        }
        if ((isArray($enParams) && $enParams['product_name'])) {
            $endata['lang']          = 'en';
            $endata['cate_id']     = $enParams['cate_id'];
            $endata['product_name']      = $enParams['product_name'];
            $endata['keyWords']      = $enParams['keywords'];
            // $endata['remark']        = $enParams['remark'];
            $endata['sort']          = $enParams['sort'];
            $endata['cover']          = $enParams['cover'];
            $endata['photos']         = $enParams['photos'];
            $endata['attachment']    = $enParams['attachment'] ? implode(';',array_column($enParams['attachment'],'fileid')) : '';
            $endata['status']         = $enParams['status'];
            $endata['description']   = $enParams['description'];
            $attributeEnParams = $enParams['attribute_params'];
            if(isset($enInfo) && isArray($enInfo)){
                if (!$this->product_model->update($enId, $endata)) {
                  $this->outputFail('操作失败');
                }
                //分类改变，清空该产品与属性的关联
                if($enInfo['cate_id'] != $enParams['cate_id']){
                    $productAttributeRelationEn = $this->product_attribute_relation_model->getByProductId($enInfo['id']);
                    if(isArray($productAttributeRelationEn)){
                        foreach ($productAttributeRelationEn as $value) {
                            $this->product_attribute_relation_model->delete($value['id']);
                        }
                    }
                }else{
                    $oldAttribuateRelationEnIds = $enInfo['attribute_relation_ids'] ? explode(';',$enInfo['attribute_relation_ids']) : array();
                    
                    $newAttributeRelationsEnIds = isArray($attributeEnParams) ? array_column($attributeEnParams,'attrbute_relation_id') : array();
                    $attributeRelationsEnIds = array_diff($oldAttribuateRelationEnIds,$newAttributeRelationsEnIds);
                    $diffEnRelations = isArray($attributeRelationsEnIds) ? $this->product_attribute_relation_model->getByIds($attributeRelationsEnIds) : array();
                    if(isArray($diffEnRelations)){
                        foreach($diffEnRelations as $relation){
                            $this->product_attribute_relation_model->delete($relation['id']);
                        }
                    }
                }
            } else{
                $endata['relation_id'] = $cnId;
                $enId = $this->product_model->create($endata);
                $this->product_model->update($cnId,array('relation_id'=>$enId));
            }
            
            $attributeEnRelationIds = array();
            if($enParams['attribute_params'] && isArray($attributeEnParams)){
                foreach($attributeEnParams as $attribute){
                    $enAttr = array();
                    $enAttr['attr_id'] = $attribute['attr_id'];
                    $enAttr['product_id'] = $enId;
                    $enAttr['attr_value'] = $attribute['attr_value'];
                    if(!$attribute['attrbute_relation_id']){
                        $attribute_relation_id = $this->product_attribute_relation_model->create($enAttr);
                        $attributeEnRelationIds[] = $attribute_relation_id;
                    }else{
                        $this->product_attribute_relation_model->update($attribute['attrbute_relation_id'],$enAttr);
                    }
                }
                $this->product_model->update($enId, array('attribute_relation_ids'=>implode(';',$attributeEnRelationIds)));
            }
            //更新附件表
            if ($endata['cover']) {
                $attEn                     = array();
                $attEn['title']            = '';
                $attEn['origin_id']        = $enId;
                $attEn['origin_data_type'] = 4;
                $this->attachment_model->update($enParams['cover'], $attEn);
            }
            if($endata['photos']){
                $photosEnArr = explode(';',$enParams['photos']);
                foreach ($photosEnArr as $key=>$val) {
                    $attEnP = array();
                    $attEnP['title'] = '';
                    $attEnP['sort'] = $key+1;
                    $attEnP['origin_id'] = $enId;
                    $attEnP['origin_data_type'] = 4;                      
                    $this->attachment_model->update($val,$attEnP);
                }
            }
            if($enParams['attachment']){
                foreach ($enParams['attachment'] as $val) {
                    $attEnA = array();
                    $attEnA['title'] = $val['name'];
                    $attEnA['origin_id'] = $enId;
                    $attEnA['origin_data_type'] = 4;                      
                    $this->attachment_model->update($val['fileid'],$attEnA);
                }
            }
        }
        $this->outputSuccess('操作成功');
    }

    /**
     * del 分类删除
     */
    public function del()
    {
        $ids = json_decode($this->input->post('ids'),true);
        $thorough = $this->input->post('thorough');
        if (!isArray($ids)) {
            $this->outputFail('参数错误');
        }
        $ProductCnInfo = $this->product_model->getByIds($ids);
        $ProductCnInfoEn = $this->product_model->getByCnIds($ids);

        $data = array();
        $data['is_delete'] = EnumRes::DELETE_YES;
        if(isArray($ProductCnInfo)){
            foreach($ProductCnInfo as $value){
                if($thorough){
                    $this->product_model->delete($value['id']);
                }else{
                    if(!$this->product_model->update($value['id'],$data)){
                        $this->outputFail('删除中文产品失败');
                    }
                }
            }
        }

        if(isArray($ProductCnInfoEn)){
            foreach($ProductCnInfoEn as $val){
                if($thorough){
                    $this->product_model->delete($val['id']);
                }else{
                    if(!$this->product_model->update($val['id'],$data)){
                        $this->outputFail('删除英文产品失败');
                    }
                }
            }
        }
        $this->outputSuccess('删除成功');

    }

    /**
     * detail 分类详情
     *
     */
    public function detail()
    {
        $id = $this->input->get('id');
        if (!$id) {
            $this->outputFail('该分类不存在');
        }
        $productInfoCn = $this->product_model->load($id);
        if (!isArray($productInfoCn)) {
            $this->outputFail('产品不存在');
        }
        $attachmentCnInfo = $this->attachment_model->load($productInfoCn['cover']);
        $photoCnIds =  $productInfoCn['photos'] ? explode(';',$productInfoCn['photos']) : array();
        $photosCnlist = $this->attachment_model->getByIds($photoCnIds);
        $photosCnArr = array();
        if(isArray($photosCnlist)){
            foreach ($photosCnlist as $value) {
                $tmp = array();
                $tmp['fileid'] = $value['id'];
                $tmp['sort'] = $value['sort'];
                $tmp['url'] = base_url().$value['url'];
                $photosCnArr[] = $tmp;
            }
        }

        $attachmentCnIds =  $productInfoCn['attachment'] ? explode(';',$productInfoCn['attachment']) : array();
        $attachmentCnlist = $this->attachment_model->getByIds($attachmentCnIds);
        $attachmentCnArr = array();
        if(isArray($attachmentCnlist)){
            foreach ($attachmentCnlist as $value) {
                $tmp = array();
                $tmp['fileid'] = $value['id'];
                $tmp['name'] = $value['title'];
                $tmp['url'] = base_url().$value['url'];
                $attachmentCnArr[] = $tmp;
            }
        }

        $productCnAttribute = $this->product_attribute_relation_model->getByProductId($productInfoCn['id']);
        $attributeRelationCnArr = array();
        foreach($productCnAttribute as $val){
            $tmp = array();
            $tmp['attrbute_relation_id'] = $val['id'];
            $tmp['attr_id'] = $val['attr_id'];
            $tmp['attr_value'] = $val['attr_value'];
            $attributeRelationCnArr[] = $tmp;
        }

        $item                  = array();
        $item['id']            = $productInfoCn['id'];
        $item['cate_id']       = $productInfoCn['cate_id'];
        $item['status']        = $productInfoCn['status'];
        $item['remark']        = $productInfoCn['remark'];
        $item['keywords']      = $productInfoCn['keywords'];
        $item['description']   = $productInfoCn['description'];
        $item['product_name']  = $productInfoCn['product_name'];
        $item['cover'] = isArray($attachmentCnInfo) ? array('url'=>base_url().$attachmentCnInfo['url'],'fileid'=>$attachmentCnInfo['id']) : '';
        $item['photos'] = isArray($photosCnArr) ? $photosCnArr : array();
        $item['attachment'] = isArray($attachmentCnArr) ? $attachmentCnArr : array();
        $item['attribute_params']  = $attributeRelationCnArr;

        $productInfoEn = array();
        if($productInfoCn['relation_id']){
            $productInfoEn = $this->product_model->load($productInfoCn['relation_id']);
        }
        if(isArray($productInfoEn)){
            $attachmentEnInfo = $this->attachment_model->load($productInfoEn['cover']);

            $photoEnIds =  $productInfoEn['photos'] ? explode(';',$productInfoEn['photos']) : array();
            $photosEnlist = $this->attachment_model->getByIds($photoEnIds);
            $photosEnArr = array();
            if(isArray($photosEnlist)){
                foreach ($photosEnlist as $value) {
                    $tmp = array();
                    $tmp['fileid'] = $value['id'];
                    $tmp['sort'] = $value['sort'];
                    $tmp['url'] = base_url().$value['url'];
                    $photosEnArr[] = $tmp;
                }
            }

            $attachmentEnIds =  $productInfoEn['attachment'] ? explode(';',$productInfoEn['attachment']) : array();
            $attachmentEnlist = $this->attachment_model->getByIds($attachmentEnIds);
            $attachmentEnArr = array();
            if(isArray($attachmentEnlist)){
                foreach ($attachmentEnlist as $value) {
                    $tmp = array();
                    $tmp['sort'] = $value['sort'];
                    $tmp['url'] = base_url().$value['url'];
                    $tmp['name'] = $value['title'];
                    $attachmentEnArr[] = $tmp;
                }
            }
            $productEnAttribute = $this->product_attribute_relation_model->getByProductId($productInfoEn['id']);
            $attributeRelationEnArr = array();
            foreach($productEnAttribute as $val){
                $tmp = array();
                $tmp['attrbute_relation_id'] = $val['id'];
                $tmp['attr_id'] = $val['attr_id'];
                $tmp['attr_value'] = $val['attr_value'];
                $attributeRelationEnArr[] = $tmp;
            }

            $itemen                  = array();
            $itemen['id']            = isArray($productInfoEn) ? $productInfoEn['id'] : '';
            $itemen['cate_id']       = isArray($productInfoEn) ? $productInfoEn['cate_id'] : '';
            $itemen['status']        = isArray($productInfoEn) ? $productInfoEn['status'] : '';
            $itemen['remark']        = isArray($productInfoEn) ? $productInfoEn['remark'] : '';
            $itemen['keywords']      = isArray($productInfoEn) ? $productInfoEn['keywords'] : '';
            $itemen['description']   = isArray($productInfoEn) ? $productInfoEn['description'] : '';
            $itemen['product_name']  = isArray($productInfoEn) ? $productInfoEn['product_name'] : '';
            $itemen['cover'] = isArray($attachmentEnInfo) ? array('url'=>base_url().$attachmentEnInfo['url'],'fileid'=>$attachmentEnInfo['id']) : '';
            $itemen['photos'] = isArray($photosEnArr) ? $photosEnArr : array();
            $itemen['attachment'] = isArray($attachmentEnArr) ? $attachmentEnArr : array();
            $itemen['attribute_params']  = $attributeRelationEnArr;
        }

        $data                    = array();
        $data['detail']['zh_cn'] = isArray($item) ? $item : array();
        $data['detail']['en']    = isArray($itemen) ? $itemen : array();
        $this->outputSuccess('ok', $data);
    }

    /**
     * index 分类列表
     */
    public function index()
    {
        $prodoctName            = $this->input->get('product_name');
        $cateId                 = $this->input->get('cate_id');
        $status                 = $this->input->get('status');
        $isDelete               = $this->input->get('is_delete');
        $sort                   = $this->input->get('sort');
        $sortRule               = $this->input->get('sort_rule');
        $isDelete               = $this->input->get('is_delete');
        $page                   = $this->input->get('page');
        $pageSize               = $this->input->get('page_size');
        $langcn                 = 'zh_cn';
        $langen                 = 'en';
        !$page && $page         = 1;
        !$pageSize && $pageSize = 20;
        $optioncn               = array('conditions' => array(), 'order' => 'created_time desc');
        if ($isDelete != '') {
            $optioncn['conditions']['is_delete'] = $isDelete;
        }
        if (isset($langcn) && $langcn) {
            $optioncn['conditions']['lang'] = $langcn;
        }
        if (isset($prodoctName) && $prodoctName) {
            $optioncn['conditions']['product_name'] = $prodoctName;
        }

        $categoryidInfo = $this->product_category_model->load($cateId);
        if (isArray($categoryidInfo)) {
            $info = $this->product_category_model->getChildrendById($categoryidInfo['id'],true);
            if (isArray($info)) {
                $cateIds = array_column($info,'id');
                array_push($cateIds, $cateId);
            }else{
                $cateIds = $cateId;
            }
        }

        if (isset($cateIds) && $cateIds) {
            $optioncn['conditions']['cate_id_in'] = $cateIds;
        }
        if (isset($status) && $status!='') {
            $optioncn['conditions']['status'] = $status;
        }
        if(isset($sort) && $sort && $sortRule){
            $sortRule = $sortRule == 1 ? 'ASC' : 'DESC';
            $optioncn['order'] = $sort. ' ' .$sortRule;
        }
        $totalcn = $this->product_model->count($optioncn);
        $optionen = array('conditions' => array(), 'order' => 'created_time desc');

        if ($isDelete != '') {
            $optionen['conditions']['is_delete'] = $isDelete;
        }
        if (isset($langen) && $langen) {
            $optionen['conditions']['lang'] = $langen;
        }
        $totalen = $this->product_model->count($optionen);

        $listcn = $this->product_model->find($optioncn, 0);
        $listen = $this->product_model->find($optionen, 0);

        $data                           = array();
        $data['list']['zh_cn']['total'] = $totalcn;
        $data['list']['zh_cn']['list']  = $listcn;
        $data['list']['en']['total']    = $totalen;
        $data['list']['en']['list']     = $listen;

        $this->outputSuccess('ok', $data);
    }

    /**
     * recover 分类恢复
     */
    public function recover()
    {
        $ids  = json_decode($this->input->post('ids'),true);
        if (!isArray($ids)) {
            $this->outputFail('参数错误');
        }
        $productCnInfo = $this->product_model->getByIds($ids);
        $productEnInfo = $this->product_model->getByCnIds($ids);
        $data              = array();
        $data['is_delete'] = EnumRes::DELETE_NO;
        if(isArray($productCnInfo)){
            foreach ($productCnInfo as $value) {
                $this->product_model->update($value['id'],$data);
            }
        }
        if(isArray($productEnInfo)){
            foreach ($productEnInfo as $val) {
                $this->product_model->update($val['id'],$data);
            }
        }

        $this->outputSuccess('恢复成功');
    }


    /**
     * 产品上线与下线
     * @return [type] [description]
     */
    public function status(){
        $ids  = json_decode($this->input->post('ids'),true);
        $status = $this->input->post('status');
        if(!isArray($ids)){
            $this->outputFail('参数错误');
        }
        $productCnInfo = $this->product_model->getByIds($ids);
        $productEnInfo = $this->product_model->getByCnIds($ids);
        if(isArray($productCnInfo)){
            foreach ($productCnInfo as $value) {
                $this->product_model->update($value['id'],array('status'=>$status));
            }
        }
        if(isArray($productEnInfo)){
            foreach ($productEnInfo as $val) {
                $this->product_model->update($val['id'],array('status'=>$status));
            }
        }
        $this->outputSuccess('ok');
    }
    

}
