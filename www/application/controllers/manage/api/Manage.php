<?php 
require_once(APPPATH .'controllers/manage/api/BaseApiController.php');
class Manage extends BaseApiController{
	public function __construct(){
		parent::__construct();
		$this->checkApiLogin();
		$this->load->model('manage_model');
		$this->load->model('manage_group_model');
	}

	public function save(){
		$id        = $this->input->post('id');
		$manageGroup = $this->input->post('manage_group_id');
		$trueName  = trim($this->input->post('truename'));
		$manageName  = trim($this->input->post('managename'));
		$passWord  = trim($this->input->post('password'));
		$status    = $this->input->post('status');
		if(!$manageGroup){
			$this->outputFail('管理员组不存在');
		}
		if(!$trueName){
			$this->outputFail('请填写真实姓名');
		}
		if(!$manageName){
			$this->outputFail('请填写昵称');
		}

		$this->load->model('manage_model');

		$info = $id ? $this->manage_model->load($id) : array();
		$salt                = mt_rand(100000,999999);
		$data                = array();
		$data['managename']  = $manageName;
		$data['truename']    = $trueName;
		$data['password']    = $passWord ? md5Password(md5($passWord),$salt) : $info['password'];
		$data['status']      = $status;
		$data['managegroup'] = $manageGroup;
		$data['salt']        = $salt;
		$this->db->trans_begin();
		if (!$id) {
			$bool = $this->manage_model->create($data);
		}else{
			$bool = $this->manage_model->update($id,$data);
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
 * del_user 删除用户
 */

	public function del_user(){
	   $ids = json_decode($this->input->post('id'));
	   if(!isArray($ids)){
	      $this->outputFail('传参错误');
	   }
	   $userInfo = $this->manage_model->getByIds($ids);
	   $data = array();
	   $data['is_delete'] = EnumRes::DELETE_YES;
	   if(isArray($userInfo)){
	       foreach($userInfo as $value){
	          if(!$this->manage_model->update($value['id'],$data)){
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
	   $userInfo = $this->manage_model->getByIds($ids);
	   // var_dump($ids)exit;
	   $data = array();
	   $data['is_delete'] = EnumRes::DELETE_NO;
	   if(isArray($userInfo)){
	       foreach($userInfo as $value){
	          if(!$this->manage_model->update($value['id'],$data)){
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
		$this->load->model('manage_model');
		$userInfo = $this->manage_model->load($id);
		$data = array();
		if(!isArray($userInfo)){
			$this->outputSuccess('用户不存在');			
		}
		$data['info']['id'] = $userInfo['id'];
		$data['info']['username'] = $userInfo['managename'];
		$data['info']['truename'] = $userInfo['truename'];
		$data['info']['usergroup'] = $userInfo['managegroup'];
		$data['info']['status'] = $userInfo['status'];
		$data['info']['is_delete'] = $userInfo['is_delete'];
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
		$this->load->model('manage_model');
		$total = $this->manage_model->count($options);
		if($total){
			$list = $this->manage_model->find($options,$pageSize, ($page - 1) * $pageSize);
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
		        	$arr['username'] = $value['managename'];
		        	$arr['usergroup'] = $value['managegroup'];
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
		$info = $this->manage_model->load($id);
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
		if(!$this->manage_model->update($id,$data)){
			$this->outputFail('操作失败');
		}
		$this->outputSuccess('ok');
	}


/**
 * userinfo 用户权限信息
 */

	public function manageinfo(){
		$userInfo = $this->_userInfo;
		if(!isArray($userInfo)){
			$this->outputFail('该用户不存在');
		}
		$this->load->model('manage_group_model');
		$userGroupInfo = $this->manage_group_model->load($userInfo['managegroup']);
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