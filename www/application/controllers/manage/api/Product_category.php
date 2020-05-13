<?php
require_once (APPPATH . 'controllers/manage/api/BaseApiController.php');
/**
 * 分类管理
 */
class Product_category extends BaseApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('attachment_model');
        $this->load->model('product_category_model');

    }
    
    /**
     *  edit() 分类编辑
     **/
    public function save()
    {
        $categoryParams = json_decode($this->input->post('product_category_params'), true);
        if (!isArray($categoryParams)) {
            $this->outputFail('参数有误');
        }
        $cnParams = isset($categoryParams['zh_cn']) ? $categoryParams['zh_cn'] : array();
        $enParams = isset($categoryParams['en']) ? $categoryParams['en'] : array();
        if(isset($cnParams['id']) && $cnParams['id']){
          $cnId = $cnParams['id'];
          $cnInfo = $this->product_category_model->load($cnId);
          if(!isArray($cnInfo)){
            $this->outputFail('参数有误');
          }
          $enId = $cnInfo['relation_id'];
          $enInfo = $this->product_category_model->load($enId);
        }

        if (isArray($cnParams)) {
            if(!$cnParams['cate_name']){
                $this->outputFail('类别名称不能为空');
            }
            $cndata['lang']          = 'zh_cn';
            $cndata['parent_id']     = $cnParams['parent_id'];
            $cndata['cate_name']      = $cnParams['cate_name'];
            $cndata['keyWords']      = $cnParams['keywords'];
            $cndata['remark']        = $cnParams['remark'];
            $cndata['attr_ids']      = $cnParams['attr_ids'];
            $cndata['description']   = $cnParams['description'];
            if(isset($cnInfo) && isArray($cnInfo)){
                $cndata['relation_id'] = $cnInfo['relation_id'];
                if (!$this->product_category_model->update($cnId, $cndata)) {
                  $this->outputFail('操作失败');
                }
            } else{
                $cnId = $this->product_category_model->create($cndata);
            }
        }
        if (isArray($enParams)) {
            $endata['lang']          = 'en';
            $endata['parent_id']     = $enParams['parent_id'];
            $endata['cate_name']      = $enParams['cate_name'];
            $endata['keyWords']      = $enParams['keywords'];
            $endata['remark']        = $enParams['remark'];
            $endata['attr_ids']      = $enParams['attr_ids'];
            $endata['description']   = $enParams['description'];
            if(isset($enInfo) && isArray($enInfo)){
                if (!$this->product_category_model->update($enId, $endata)) {
                  $this->outputFail('操作失败');
                }
            } else{
                $endata['relation_id'] = $cnId;
                $enId = $this->product_category_model->create($endata);
                $this->product_category_model->update($cnId,array('relation_id'=>$enId));
            }
        }
        $this->outputSuccess('操作成功');
    }

    /**
     * del 分类删除
     */
    public function del()
    {
        $id = $this->input->post('id');
        if (!$id) {
            $this->outputFail('该分类不存在');
        }
        $cnInfo = $this->product_category_model->load($id);
        if (!isArray($cnInfo)) {
            $this->outputFail('类别不存在');
        }
        if ($cnInfo['is_delete'] == EnumRes::DELETE_YES) {
            $this->outputFail('该分类已删除');
        }
        $data = array();

        $data['is_delete']   = EnumRes::DELETE_YES;
        $childCategoryInfocn = $this->product_category_model->getByParentId($id);
        if (!empty($childCategoryInfocn)) {
            $this->outputFail('该分类下还有子分类,请先删除子分类');
        }
        // foreach ($childCategoryInfocn as $val) {
        //     if (!$this->product_category_model->update($val['id'], $data)) {
        //         $this->outputFail('操作失败');
        //     }
        //     $lastCategoryInfocn = $this->product_category_model->getByParentId($val['id']);
        //     if (isArray($lastCategoryInfocn)) {
        //         foreach ($lastCategoryInfocn as $item) {
        //             if (!$this->product_category_model->update($item['id'], $data)) {
        //                 $this->outputFail('操作失败');
        //             }
        //         }
        //     }
        // }

        $enInfo              = $this->product_category_model->load($cnInfo['relation_id']);
        $eid                 = $enInfo['id'];
        // $childCategoryInfoen = $this->product_category_model->getByParentId($id);
        // foreach ($childCategoryInfoen as $val) {
        //     if (!$this->product_category_model->update($val['id'], $data)) {
        //         $this->outputFail('操作失败');
        //     }
        //     $lastCategoryInfoen = $this->product_category_model->getByParentId($val['id']);
        //     if (isArray($lastCategoryInfoen)) {
        //         foreach ($lastCategoryInfoen as $item) {
        //             if (!$this->product_category_model->update($item['id'], $data)) {
        //                 $this->outputFail('操作失败');
        //             }
        //         }
        //     }
        // }
        if (!$this->product_category_model->update($id, $data) || !$this->product_category_model->update($eid, $data)) {
            $this->outputFail('操作失败');
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
        $categoryInfocn = $this->product_category_model->load($id);
        if (!isArray($categoryInfocn)) {
            $this->outputFail('该分类不存在');
        }
        $this->load->model('attribute_model');
        $attrCnIds = $categoryInfocn['attr_ids'] ? explode(';',$categoryInfocn['attr_ids']) : array();
        $attrCnList = $this->attribute_model->getByIds($attrCnIds);
        $attrCnArr = array();
        foreach($attrCnList as $val){
            $tmp = array();
            $tmp['id'] = $val['id'];
            $tmp['attr_type'] = $val['attr_type'];
            $tmp['attr_name'] = $val['attr_name'];
            $tmp['attr_value'] = $val['attr_value'];
            $attrCnArr[] = $tmp;
        }

        $item                  = array();
        $item['id']            = $categoryInfocn['id'];
        $item['lang']            = $categoryInfocn['lang'];
        $item['remark']        = $categoryInfocn['remark'];
        $item['keywords']      = $categoryInfocn['keywords'];
        $item['description']   = $categoryInfocn['description'];
        $item['cate_name']     = $categoryInfocn['cate_name'];
        $item['attr_ids']     = $categoryInfocn['attr_ids'];
        $item['attr_params']   = $attrCnArr;
        $categoryInfoen = array();
        if($categoryInfocn['relation_id']){
            $categoryInfoen = $this->product_category_model->load($categoryInfocn['relation_id']);
        }
        if(isArray($categoryInfoen)){
            $attrEnIds = $categoryInfoen['attr_ids'] ? explode(';',$categoryInfoen['attr_ids']) : array();
            $attrEnList = $this->attribute_model->getByIds($attrEnIds);
            $attrEnArr = array();
            foreach($attrEnList as $val){
                $tmp = array();
                $tmp['id'] = $val['id'];
                $tmp['attr_type'] = $val['attr_type'];
                $tmp['attr_name'] = $val['attr_name'];
                $tmp['attr_value'] = $val['attr_value'];
                $attrEnArr[] = $tmp;
            }
            $itemen                  = array();
            $itemen['id']            = isArray($categoryInfoen) ? $categoryInfoen['id'] : '';
            $itemen['lang']            = isArray($categoryInfoen) ? $categoryInfoen['lang'] : '';
            $itemen['remark']        = isArray($categoryInfoen) ? $categoryInfoen['remark'] : '';
            $itemen['keywords']      = isArray($categoryInfoen) ? $categoryInfoen['keywords'] : '';
            $itemen['description']   = isArray($categoryInfoen) ? $categoryInfoen['description'] : '';
            $itemen['cate_name']         = isArray($categoryInfoen) ? $categoryInfoen['cate_name'] : '';
            $itemen['attr_ids']     = $categoryInfoen['attr_ids'];
            $itemen['attr_params']    = $attrEnArr;

        }

        $data   = $data['detail']['zh_cn'] = $data['detail']['en'] = array();
        $item && $item['lang'] && $data['detail'][$item['lang']] = $item;
        isset($itemen) && isArray($itemen) && $itemen['lang']  && $data['detail'][$itemen['lang']] = $itemen;
        // $data['detail']['cate_id'] = $categoryInfocn['cate_id'];
        $this->outputSuccess('ok', $data);
    }

    /**
     * index 分类列表
     */
    public function index()
    {
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
        $totalcn = $this->product_category_model->count($optioncn);
        $optionen = array('conditions' => array(), 'order' => 'created_time desc');

        if ($isDelete != '') {
            $optionen['conditions']['is_delete'] = $isDelete;
        }
        if (isset($langen) && $langen) {
            $optionen['conditions']['lang'] = $langen;
        }
        $totalen = $this->product_category_model->count($optionen);

        $listcn = $this->product_category_model->find($optioncn, 0);
        $listen = $this->product_category_model->find($optionen, 0);

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
        $id = $this->input->post('id');
        if (!$id) {
            $this->outputFail('分类不存在');
        }
        $categoryInfo = $this->product_category_model->load($id);
        if (!isArray($categoryInfo)) {
            $this->outputFail('类别不存在');
        }
        $data              = array();
        $data['is_delete'] = EnumRes::DELETE_NO;

        if (!$this->product_category_model->update($id, $data)) {
            $this->outputFail('操作失败');
        }
        $this->outputSuccess('恢复成功');
    }

}
