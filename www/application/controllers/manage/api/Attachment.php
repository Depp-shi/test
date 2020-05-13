<?php 
	require_once(APPPATH .'controllers/manage/api/BaseApiController.php');

	class Attachment extends BaseApiController{
		public function __construct(){
			parent::__construct();
	
        	$this->load->model('attachment_model');

		}


		/**
		 * index 附件管理
		 */
		public function index(){
			$orginId       = $this->input->get('origin_id');
			$orginDataType = $this->input->get('origin_data_type');
			$type          = $this->input->get('type');
			$isDelete      = $this->input->get('is_delete');
			$page          = $this->input->get('page');
			$pageSize      = $this->input->get('page_size');
			$title         = $this->input->get('title');
			$sort          = $this->input->get('sort');
			$sortRule      = $this->input->get('sort_rule');
			!$page && $page = 1;
			!$pageSize && $pageSize = 20;

			$item['is_delete'] = $isDelete;
			$item['type'] = $type;
			$item['origin_data_type'] = $orginDataType;
			$item['sort']           = $sort;
			$item['sort_rule']      = $sortRule;
	        if (isset($item['sort']) && $item['sort_rule'] == 1) {
	        	$options = array('conditions'=>array(),'order' => $item['sort']. ' asc');
	        }elseif (isset($item['sort']) && $item['sort_rule'] == 2) {
	        	$options = array('conditions'=>array(),'order' => $item['sort']. ' desc');
	        }else{
				$options = array('conditions'=>array(),'order' => 'id desc');
	        }
			if($item['is_delete']!= ''){
            	$options['conditions']['is_delete'] = $item['is_delete'];
        	}
        	if(isset($item['type']) && $item['type']){
            	$options['conditions']['type'] = $item['type'];
        	}
        	if(isset($item['origin_data_type']) && $item['origin_data_type']){
            	$options['conditions']['origin_data_type'] = $item['origin_data_type'];
        	}
        	if(isset($item['origin_data_type']) && $item['origin_data_type']){
            	$options['conditions']['origin_data_type'] = $item['origin_data_type'];
        	}
        	if(isset($item['title']) && $item['title']){
        		$options['conditions']['title'] = $item['title'];
        	}
			$data = array();
        	$total = $this->attachment_model->count($options);
        	if($total){
        		$data['total'] = $total;
        		$list = $this->attachment_model->find($options,$pageSize, ($page - 1) * $pageSize);
        		$data['list'] = $list;
        		foreach ($list as $key => $value) {
        			$data['list'][$key]['thumb'] = base_url($value['url']);
        		}
        	}else{
        		$list = array();
        	}

        	$this->outputSuccess('ok',$data);
		}



	}

 ?>