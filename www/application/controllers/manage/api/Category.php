<?php 
	require_once (APPPATH . 'controllers/manage/api/BaseApiController.php');
	/**
	* 分类管理
	*/
class Category extends BaseApiController{
		
	public function __construct(){
    	parent::__construct();
		$this->load->model('attachment_model');				       	
		$this->load->model('category_model');				
	}
	/**
	 * index 分类列表
	 */
	public function index(){
		$page                   = $this->input->get('page');
		$pageSize               = $this->input->get('page_size');

		!$page && $page         = 1;
		!$pageSize && $pageSize = 20;
		$option               = array('conditions' => array('is_delete'=>EnumRes::DELETE_NO), 'order' => 'id asc,sort desc');

		$total  = $this->category_model->count($option);
		if (!$total) {
			$this->outputSuccess('ok',array('total'=>0,'list'=>array()));
		}
		$list = $this->category_model->find($option,0);
		$this->outputSuccess('ok',array('total'=>$total,'list'=>$list));
	}

	/**
	 * detail 分类详情
	 *
	 */
	public function detail(){
        $id = $this->input->get('id');
        if (!$id) {
            $this->outputFail('该分类不存在');
        }
        $categoryInfo = $this->category_model->load($id);
        if (!isArray($categoryInfo)) {
            $this->outputFail('该分类不存在');
        }

		$data                     = array();
		$data['id']               = $categoryInfo['id'];
		$data['parent_id']        = $categoryInfo['parent_id'];
		$data['title']            = $categoryInfo['title'];
		$data['title_en']         = $categoryInfo['title_en'];
		$data['cover']            = $categoryInfo['cover'];
		$data['external_link']    = $categoryInfo['external_link'];
		$data['external_link_en'] = $categoryInfo['external_link_en'];
		$data['remark']           = $categoryInfo['remark'];
		$data['remark_en']        = $categoryInfo['remark_en'];
		$data['keywords']         = $categoryInfo['keywords'];
		$data['keywords_en']      = $categoryInfo['keywords_en'];
		$data['description']      = $categoryInfo['description'];
		$data['description_en']   = $categoryInfo['description_en'];
		$data['is_show_front']    = $categoryInfo['is_show_front'];
		$data['sort']             = $categoryInfo['sort'];
		$data['cover']            = getAttachMent($categoryInfo['cover']);

        $this->outputSuccess('ok', $data);
	}
	/**
	 * 保存数据
	 */
	public function save(){
        $categoryParams = json_decode($this->input->post('category_params'), true);
        if (!isArray($categoryParams)) {
            $this->outputFail('参数有误');
        }
        if(isset($categoryParams['id']) && $categoryParams['id']){
        	$categoryId = $categoryParams['id'];
        	$categoryInfo = $this->category_model->load($categoryId);
        	if(!isArray($categoryInfo)){
        		$this->outputFail('参数有误');
        	}
        }
        if(!isset($categoryParams['title']) || !$categoryParams['title']){
            $this->outputFail('请填写分类标题');
        }
		$data                     = array();
		$data['parent_id']        = $categoryParams['parent_id'];
		$data['title']            = $categoryParams['title'];
		$data['title_en']         = $categoryParams['title_en'];
		$data['cover']            = getAttachmentIdsToStr($categoryParams['cover']);
		$data['external_link']    = $categoryParams['external_link'];
		$data['external_link_en'] = $categoryParams['external_link_en'];
		$data['remark']           = $categoryParams['remark'];
		$data['remark_en']        = $categoryParams['remark_en'];
		$data['keywords']         = $categoryParams['keywords'];
		$data['keywords_en']      = $categoryParams['keywords_en'];
		$data['description']      = $categoryParams['description'];
		$data['description_en']   = $categoryParams['description_en'];
		$data['is_show_front']    = $categoryParams['is_show_front'];
		$data['sort']             = $categoryParams['sort'];

        $this->db->trans_begin();
        $insertId = '';
        if(isset($categoryInfo) && isArray($categoryInfo)){
            $this->category_model->update($categoryId, $data);
        } else{
            $insertId = $this->category_model->create($data);
        }
		if ($this->db->trans_status() === true) {
	        $this->db->trans_commit();
        	$this->outputSuccess('操作成功');
    	}else{
	        $this->db->trans_rollback();
        	$this->outputFail('操作失败');
    	}
	}

	/**
	 * del 分类删除
	 */
	public function del(){
        $id = $this->input->post('id');
        if (!$id) {
            $this->outputFail('该分类不存在');
        }
        $cnInfo = $this->category_model->load($id);
        if (!isArray($cnInfo)) {
            $this->outputFail('类别不存在');
        }
        if ($cnInfo['is_delete'] == EnumRes::DELETE_YES) {
            $this->outputFail('该分类已删除');
        }
		$data                = array();
		$data['is_delete']   = EnumRes::DELETE_YES;
		$childCategoryInfocn = $this->category_model->getByParentId($id);
        if (!empty($childCategoryInfocn)) {
            $this->outputFail('该分类下还有子分类,请先删除子分类');
        }
        $this->db->trans_begin();
        $result = $this->category_model->update($id, $data);
        if ($result) {
        	$this->category_model->update($cnInfo['relation_id'], $data);
    		if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $this->outputFail('操作失败');
	        }
	        $this->db->trans_commit();
        }
        $this->outputSuccess('删除成功');
	}


	/**
	 * recover 分类恢复
	 */
	public function recover(){
		$id = $this->input->post('id');
		if(!$id){
		 $this->outputFail('分类不存在');
		}
		$categoryInfo = $this->category_model->load($id);
		if(!isArray($categoryInfo)){
		 $this->outputFail('类别不存在');
		}
		$data = array();
		$data['is_delete'] = EnumRes::DELETE_NO;


		if(!$this->category_model->update($id,$data)){
		  $this->outputFail('操作失败');
		}
		$this->outputSuccess('恢复成功');
    }


}