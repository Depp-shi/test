<?php 
	require_once (APPPATH . 'controllers/manage/api/BaseApiController.php');


	class User_group extends BaseApiController
	{
		public function __construct(){
			parent::__construct();
		}
		/**
		 * add 添加用户组
		 */
		public function add(){
			$name = $this->input->post('name');
			$purview = $this->input->post('purview');
			$status = $this->input->post('status');

			if(!$name){
				$this->outputFail('请填写姓名');
			}
			if(!$purview){
				$this->outputFail('请选择权限');
			}
			$data = array();
			$data['name'] = $name;
			$data['purview'] = $purview;
			$data['status'] = $status;
			$this->load->model('Usergroup_model');
			if(!$this->Usergroup_model->create($data)){
				$this->outputFail('操作失败');
			}
			$this->outputSuccess('ok');
		}


		/**
		 * edit 编辑用户组
		 */
		public function edit(){
			$id = $this->input->post('id');
			$purview = $this->input->post('purview');
			$status = $this->input->post('status');
			$name = $this->input->post('name');

			if(!$id){
				$this->outputFail('该用户组不存在');
			}
			$data = array();
			$data['purview'] = $purview;
			$data['status'] = $status;
			$data['name'] = $name;
			$this->load->model('Usergroup_model');
			if(!$this->Usergroup_model->update($id,$data)){
				$this->outputFail('修改失败');
			} 
			$this->outputSuccess('ok');
		}

		/**
		 * del 删除用户组
		 */
		public function del(){
			$id = json_decode($this->input->post('id'));
			if(!isArray($id)){
				$this->outputFail('传参错误');
			}
			$this->load->model('Usergroup_model');
	       	$userGroup = $this->Usergroup_model->getByIds($id);
	       	// var_dump($userGroup);exit;
	       	$data = array();
	       	$data['is_delete'] = EnumRes::DELETE_YES;
	       	if(isArray($userGroup)){
	           foreach($userGroup as $value){
	              if(!$this->Usergroup_model->update($value['id'],$data)){
	                  $this->outputFail('操作失败');
	               }
	           }  
	       	}
			$this->outputSuccess('ok');
		}



		/**
		 * recover 恢复用户组
		 */
		public function recover(){
			$id = json_decode($this->input->post('id'));
			if(!isArray($id)){
				$this->outputFail('传参错误');
			}
			$this->load->model('Usergroup_model');
	       	$userGroup = $this->Usergroup_model->getByIds($id);
	       	$data = array();
	       	$data['is_delete'] = EnumRes::DELETE_NO;
	       	if(isArray($userGroup)){
	           foreach($userGroup as $value){
	              if(!$this->Usergroup_model->update($value['id'],$data)){
	                  $this->outputFail('操作失败');
	               }
	           }  
	       	}
			$this->outputSuccess('ok');
		}


		/**
		 *  detail 用户组详情
		 */
		public function detail(){
			$id = $this->input->get('id');
			if(!$id){
				$this->outputFail('该用户组不存在');
			}
			$this->load->model('Usergroup_model');
			$userGroup = $this->Usergroup_model->load($id);
			$data = array();
			if($userGroup){
				$data['info']['id'] = $userGroup['id'];
				$data['info']['name'] = $userGroup['name'];
				$data['info']['purview'] = $userGroup['purview'];
				$data['info']['status'] = $userGroup['status'];
				$data['info']['is_delete'] = $userGroup['is_delete'];
			}
			$this->outputSuccess('ok',$data);
		}


		/**
		 * index 用户组列表
		 */
		public function index(){
			$page = $this->input->get('page');
			$pageSize = $this->input->get('page_size');
			$isDelete = $this->input->get('is_delete');
			$status = $this->input->get('status');
			!$page && $page = 1;
			!$pageSize && $pageSize = 20;

			$item = array();
			$item['is_delete'] = $isDelete;
			$item['status'] = $status;
			$options = array('conditions'=>array());
			if($item['is_delete']!= ''){
            	$options['conditions']['is_delete'] = $item['is_delete'];
            	$options['conditions']['status'] = $item['status'];
        	}
        	$this->load->model('Usergroup_model');
        	$total = $this->Usergroup_model->count($options);
        	if($total){
        		$list = $this->Usergroup_model->find($options,$pageSize, ($page - 1) * $pageSize);
        		$tmp = array();
        		foreach ($list as $value) {
        			$arr = array();
        			if($value['created_time'] !='0'){
        				$arr['created_time'] = date('Y-m-d H:i:s',$value['created_time']);

        			}else{
        				$arr['created_time'] = '';
        			}

        			// if($value['last_login_time'] !='0'){
        			// 	$arr['last_login_time'] = date('Y-m-d H:i:s',$value['last_login_time']);

        			// }else{
        			// 	$arr['last_login_time'] = '';
        			// }        				
			        	$arr['id'] = $value['id'];
			        	//$arr['created_time'] =$list['created_time'];
			        	$arr['name'] = $value['name'];
			        	//$arr['usergroup'] = $value['usergroup'];
			        	//$arr['truename'] = $value['truename'];
			        	//$arr['login_count'] = $value['login_count'];
			        	$arr['status'] = $value['status'];
			        	$tmp[] = $arr;
        		}
        	}else{
        		$list=array();
        	}

        	$data = array();
        	$data['total'] =$total;
        	$data['info'] = $tmp;
        	$this->outputSuccess('ok',$data);
		}




	}
?>