<?php 
	require_once(APPPATH .'controllers/manage/api/BaseApiController.php');



	class User extends BaseApiController{
		public function __construct(){
			parent::__construct();
        	$this->checkApiLogin();
			$this->load->model('User_model');
		}

		/**
		 * add 用户添加
		 */
		public function add(){
			$userGroup = $this->input->post('usergroup');
			$trueName = $this->input->post('truename');
			$userName = $this->input->post('username');
			$passWord = $this->input->post('password');
			$status = $this->input->post('status');
			//var_dump($status);exit;
			if(!$userGroup){
				$this->outputFail('用户组不存在');
			}
			if(!$trueName){
				$this->outputFail('请填写真实姓名');
			}
			if(!$userName){
				$this->outputFail('请填写昵称');
			}
			if(!$passWord){
				$this->outputFail('请输入密码');
			}
			if($status ==''){
				$this->outputFail('请选择状态');
			}
			$salt = mt_rand(100000,999999);
			$data = array();
			$data['username'] = $userName;
			$data['truename'] = $trueName;
			$data['password'] = md5Password(md5($passWord),$salt);
			$data['status'] = $status;
			$data['usergroup'] = $userGroup;
			$data['salt'] = $salt;
			$this->load->model('User_model');
			if(!$this->User_model->create($data)){
				$this->outputFail('操作失败');
			}
			$this->outputSuccess('ok');
		}


		/**
		 * edit 编辑用户
		 */
		public function edit(){
			$id = $this->input->post('id');
			$userGroup = $this->input->post('usergroup');
			$trueName = $this->input->post('truename');
			$userName = $this->input->post('username');
			$password = $this->input->post('password');
			$status = $this->input->post('status');	

			if(!$id){
				$this->outputFail('该用户不存在');
			}
			if(!$userGroup){
				$this->outputFail('用户组不存在');
			}
			if(!$trueName){
				$this->outputFail('请填写真实姓名');
			}
			if(!$userName){
				$this->outputFail('请填写昵称');
			}
			if($status ==''){
				$this->outputFail('请选择状态');
			}

			$info = $this->User_model->load($id);

			$data = array();

			if(!$password){
				$data['password'] = $info['password'];
			}else{
				$salt = mt_rand(100000,999999);
				$data['password'] = md5Password(md5($password),$salt);
				$data['salt'] = $salt;
			}
			$data['username'] = $userName;
			$data['truename'] = $trueName;
			$data['status'] = $status;
			$data['usergroup'] = $userGroup;
			//var_dump($data);exit;
			$this->load->model('User_model');
			if(!$this->User_model->update($id,$data)){
				$this->outputFail('操作失败');
			}
			$this->outputSuccess('ok');
		}

		/**
		 * del_user 删除用户
		 */

		public function del_user(){
	       $ids = json_decode($this->input->post('id'));
	       if(!isArray($ids)){
	          $this->outputFail('传参错误');
	       }
	       $userInfo = $this->User_model->getByIds($ids);
	       // var_dump($ids)exit;
	       $data = array();
	       $data['is_delete'] = EnumRes::DELETE_YES;
	       if(isArray($userInfo)){
	           foreach($userInfo as $value){
	              if(!$this->User_model->update($value['id'],$data)){
	                  $this->outputFail('操作失败');
	               }
	           }  
	       }
	       $this->outputSuccess('删除成功');
		}


		/**
		 * recover 恢复用户
		 */

		public function recover(){
	       $ids = json_decode($this->input->post('id'));
	       if(!isArray($ids)){
	          $this->outputFail('参数错误');
	       }
	       $userInfo = $this->User_model->getByIds($ids);
	       // var_dump($ids)exit;
	       $data = array();
	       $data['is_delete'] = EnumRes::DELETE_NO;
	       if(isArray($userInfo)){
	           foreach($userInfo as $value){
	              if(!$this->User_model->update($value['id'],$data)){
	                  $this->outputFail('操作失败');
	               }
	           }  
	       }
	       $this->outputSuccess('恢复成功');
		}


		/**
		 * detail 用户详情
		 */
		public function detail(){
			$id = $this->input->get('id');
			if(!$id){
				$this->outputFail('该用户不存在');
			}
			$this->load->model('User_model');
			$userInfo = $this->User_model->load($id);
			$data = array();
			if($userInfo){
				$data['info']['id'] = $userInfo['id'];
				$data['info']['username'] = $userInfo['username'];
				$data['info']['truename'] = $userInfo['truename'];
				$data['info']['usergroup'] = $userInfo['usergroup'];
				$data['info']['status'] = $userInfo['status'];
				$data['info']['is_delete'] = $userInfo['is_delete'];
			}
			$this->outputSuccess('ok',$data);			
		}


		/**
		 * index 用户列表
		 */
		public function index(){
			$page = $this->input->get('page');
			$pageSize = $this->input->get('page_size');
			$isDelete = $this->input->get('is_delete');
			$status = $this->input->get('status');
			$groupId = $this->input->get('usergroup');
			$username = $this->input->get('username');

			!$page && $page = 1;
			!$pageSize && $pageSize = 20;

			$item = array();
			$item['is_delete'] = $isDelete;
			$item['status'] = $status;
			$item['usergroup'] = $groupId; 
			$item['username'] = $username; 

			$options = array('conditions'=>array());
			if($item['is_delete'] != ''){
            	$options['conditions']['is_delete'] = $item['is_delete'];
            	$options['conditions']['status'] = $item['status'];
            	$options['conditions']['usergroup'] = $item['usergroup'];
            	$options['conditions']['username'] = $item['username'];

        	}
        	$this->load->model('User_model');
        	$total = $this->User_model->count($options);
        	if($total){
        		$list = $this->User_model->find($options,$pageSize, ($page - 1) * $pageSize);
        		$tmp = array();
        		foreach ($list as $value) {
        			$arr = array();
        			if($value['created_time'] !='0'){
        				$arr['created_time'] = date('Y-m-d H:i:s',$value['created_time']);

        			}else{
        				$arr['created_time'] = '';
        			}

        			if($value['last_login_time'] !='0'){
        				$arr['last_login_time'] = date('Y-m-d H:i:s',$value['last_login_time']);

        			}else{
        				$arr['last_login_time'] = '';
        			}        				
			        	$arr['id'] = $value['id'];
			        	//$arr['created_time'] =$list['created_time'];
			        	$arr['username'] = $value['username'];
			        	$arr['usergroup'] = $value['usergroup'];
			        	$arr['truename'] = $value['truename'];
			        	$arr['login_count'] = $value['login_count'];
			        	$arr['status'] = $value['status'];
			        	$tmp[] = $arr;
        		}
        		
        	}else{
        		$arr=array();
        	}

         	$data = array();
        	$data['total'] =$total;
        	$data['info'] = $tmp;
        	$this->outputSuccess('ok',$data);
		}


		/**
		 * pwdmodify 密码修改
		 */
		public function pwdmodify(){
			$id = $this->input->post('id');
			$oldPassword = $this->input->post('old_password');
			$password = $this->input->post('password');

			if (!$id) {
				$this->outputFail('该用户不存在');
			}
			if(!$oldPassword){
				$this->outputFail('请填写原密码');
			}
			if(!$password){
				$this->outputFail('请填写新的密码');
			}
			$info = $this->User_model->load($id);
			// $salt = $info['salt'];
			if($info['salt'] ==''){
				$salt = mt_rand(100000,999999);
			}else{
				$salt = $info['salt'];
			}
			$pwd = md5Password(md5($oldPassword),$salt);
			if($pwd != $info['password']){
				$this->outputFail('原密码错误');
			}
			$newSalt = mt_rand(100000,999999);
			$newpwd = md5Password(md5($password),$newSalt);
			$data = array();
			$data['password'] = $newpwd;
			$data['salt'] = $newSalt;
			if(!$this->User_model->update($id,$data)){
				$this->outputFail('操作失败');
			}
			$this->outputSuccess('ok');
		}


		/**
		 * userinfo 用户权限信息
		 */

		public function userinfo(){
			$userInfo = $this->_userInfo;
			if(!isArray($userInfo)){
				$this->outputFail('该用户不存在');
			}
			$this->load->model('Usergroup_model');
			$userGroupInfo = $this->Usergroup_model->load($userInfo['usergroup']);
			$data = array();
			$data['purview'] = isset($userGroupInfo['purview']) ? $userGroupInfo['purview'] : array();
			$data['username'] = $userInfo['username'];
			$data['truename'] = $userInfo['truename'];
			$data['first_login_ip'] = $userInfo['first_login_ip'];
			$data['final_login_ip'] = $userInfo['final_login_ip'];
			$data['login_count'] = $userInfo['login_count'];
			$data['last_login_time'] = $userInfo['last_login_time'] ? date('Y-m-d H:i:s', $userInfo['last_login_time']) : '';
			$data['id'] = $userInfo['id'];
			//$enumAtions = EnumRes::EnumAtions();
			$this->outputSuccess('ok',$data);
		}
	}
 ?>