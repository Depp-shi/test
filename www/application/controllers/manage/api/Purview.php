<?php 
	require_once(APPPATH . 'controllers/manage/api/BaseApiController.php');

	class Purview extends BaseApiController{
		public function __construct(){
			parent::__construct();
		}


		/**
		 * add 添加权限
		 */
		public function add(){
			$parentId = $this->input->post('parent_id');
			$class = $this->input->post('class');
			$status = $this->input->post('status');

			$data = array();
			$data['parent_id'] = $parentId;
			$data['class'] = $class;
			$data['status'] = $status;
			$this->load->model('purview_model');
			if(!$this->purview_model->create($data)){
				$this->outputFail('操作失败');
			}
			$this->outputSuccess('ok');
		}

		/**
		 * edit 编辑权限
		 */

		public function edit(){
			$id = $this->input->post('id');
			$parentId = $this->input->post('parent_id');
			$class = $this->input->post('class');
			$status = $this->input->post('status');
			if(!$id){
				$this->outputFail('该权限不存在');
			}
			$data = array();
			$data['parent_id'] = $parentId;
			$data['class'] = $class;
			$data['status'] = $status;
			$this->load->model('purview_model');
			if(!$this->purview_model->update($id,$data)){
				$this->outputFail('操作失败');
			}			
			$this->outputSuccess('ok');
		}


		/**
		 * del 删除权限
		 */
		public function del(){
			$id = $this->input->post('id');
			if(!$id){
				$this->outputFail('该权限不存在');
			}
			$isDelete = EnumRes::DELETE_YES;
			$this->load->model('purview_model');
			$purviewInfo = $this->purview_model->load($id);
			if($purviewInfo){
				$data['is_delete'] = $isDelete;
			}
			if(!$this->purview_model->update($id,$data)){
				$this->outputFail('操作失败');
			}
			$this->outputSuccess('ok');
		}


		/**
		 * recover 恢复权限
		 */
		public function recover(){
			$id = $this->input->post('id');
			if(!$id){
				$this->outputFail('该权限不存在');
			}
			$isDelete = EnumRes::DELETE_NO;
			$this->load->model('purview_model');
			$purviewInfo = $this->purview_model->load($id);
			if($purviewInfo){
				$data['is_delete'] = $isDelete;
			}
			if(!$this->purview_model->update($id,$data)){
				$this->outputFail('操作失败');
			}
			$this->outputSuccess('ok');
		}

		/**
		 * detail 权限详情
		 */

		public function detail(){
			$id = $this->input->get('id');
			if(!$id){
				$this->outputFail('该用户组不存在');
			}
			$this->load->model('purview_model');
			$purview = $this->purview_model->load($id);
			$data = array();
			if($purview){
				$data['info']['id'] = $purview['id'];
				$data['info']['parent_id'] = $purview['parent_id'];
				$data['info']['status'] = $purview['status'];
				$data['info']['class'] = $purview['class'];
			}
			$this->outputSuccess('ok',$data);			
		}

		/**
		 * index 权限列表
		 */
		public function index(){
			$status = $this->input->get('status');
			if(!$status){
				$this->outputFail('状态错误');
			}
			$item = array();
			$item['status'] = $status;
			$this->load->model('purview_model');
			$option = array('conditions'=>array());
			if(isset($item['status']) && $item['status']){
            $options['conditions']['status'] = $item['status'];
        }
        	$list = $this->purview_model->find($options);
        	if(!isArray($list)){
        		$this->outputFail('error');
        	}
        	// $info = array();
        	// foreach ($list as  &$value) {
        	// $value['id'] = $value['id'];
        	// $value['parent_id'] = $value['parent_id'];
        	// $value['class'] = $value['class'];
        	// $value['status'] = $value['status'];
        	// }

        	$data =array();
        	$data['list'] = $list;
        	$this->outputSuccess('ok',$data);

		}
	}
 ?>