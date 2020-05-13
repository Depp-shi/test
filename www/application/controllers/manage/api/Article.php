<?php 
	require_once (APPPATH . 'controllers/manage/api/BaseApiController.php');


class Article extends BaseApiController{


	public function __construct(){
		parent::__construct();
       	// $this->checkApiLogin();
		$this->load->model('Article_model');
		$this->load->model('Attachment_model');
	}

	/**
	 * add 添加
	 */
	public function add(){
		$articleParams = json_decode($this->input->post('article_params'),true);
		if(!isArray($articleParams)){
			$this->outputFail('传参错误');
		}
		$articleInfocn = $articleParams['zh_cn'];
		$articleInfoen = $articleParams['en'];
		$this->load->model('category_model');
		$relationCategory = $this->category_model->load($articleInfocn['category']);
		if(isArray($articleInfocn)){
			$cdata = array();
			$cdata['lang']            = 'zh_cn';
			$cdata['category']        = $articleInfocn['category'];
			$cdata['title']           = $articleInfocn['title'];
			$cdata['subhead']         = $articleInfocn['subhead'];
			$cdata['title_style']     = $articleInfocn['title_style'];
			$cdata['title_style']     = implode(';',$articleInfocn['title_style']);
			$cdata['external_link']   = $articleInfocn['external_link'];
			$cdata['author']          = $articleInfocn['author'];
			$cdata['seo_keywords']    = $articleInfocn['seo_keywords'];
			$cdata['seo_description'] = $articleInfocn['seo_description'];
			$cdata['content']         = $articleInfocn['content'];
			$cdata['description']     = $articleInfocn['description'];
			$cdata['sort']            = $articleInfocn['sort'];
			$cdata['cover']           = isArray($articleInfocn['cover']) && isset($articleInfocn['cover']['fileid']) ? $articleInfocn['cover']['fileid'] :'';
			$photoId                  = isArray($articleInfocn['photos']) ? array_column($articleInfocn['photos'], 'fileid') :'';
			$cdata['photos']          = isArray($photoId) ? implode(';',$photoId) :'';
			$attachmentId             = isArray($articleInfocn['attachment']) ? array_column($articleInfocn['attachment'], 'fileid') :'';
			$cdata['attachment']      = isArray($attachmentId) ? implode(';',$attachmentId) :'';
			$cdata['from_link']       = $articleInfocn['from_link'];
			$cdata['status']          = $articleInfocn['status'];//显示
			$cdata['relation_id']     = 0;
			if(!$articleInfocn['category']){
				$this->outputFail('请选择分类');
			}
			if(!$articleInfocn['title']){
				$this->outputFail('请输入标题');
			}
			if(!$articleInfocn['cover']){
				$this->outputFail('请上传图片');
			}
			$articleCnId = $this->Article_model->create($cdata);
			if(!$articleCnId){
				$this->outputFail('操作失败');
			}else{
				if($cdata['cover'] !=''){
					$att = array();
					$att['title'] = $cdata['title'];
					$att['origin_id'] = $articleCnId;
					$att['origin_data_type'] = 2;
					$this->Attachment_model->update($cdata['cover'],$att);
				}
				if(isArray($photoId)){
					foreach ($photoId as $v) {
						$attP = array();
						$attP['title'] = $cdata['title'];
						$attP['origin_id'] = $articleCnId;
						$attP['origin_data_type'] = 2;						
						$this->Attachment_model->update($v,$attP);
					}
				}
				if(isArray($attachmentId)){
					foreach ($attachmentId as $v) {
						$attach = array();
						$attach['title'] = $cdata['title'];
						$attach['origin_id'] = $articleCnId;
						$attach['origin_data_type'] = 2;						
						$this->Attachment_model->update($v,$attach);
					}
				}
			}
 		}
 		if($articleInfoen !=''){
 			$edata = array();
			$edata['lang']            = 'en';
			$edata['category']        = $relationCategory['relation_id'];
			$edata['title']           = $articleInfoen['title'];
			$edata['subhead']         = $articleInfoen['subhead'];
			$edata['title_style']     = $articleInfoen['title_style'];
			$edata['title_style']     = $edata['title_style'] ? implode(';',$edata['title_style']) :'';
			$edata['external_link']   = $articleInfoen['external_link'];
			$edata['author']          = $articleInfoen['author'];
			$edata['seo_keywords']    = $articleInfoen['seo_keywords'];
			$edata['seo_description'] = $articleInfoen['seo_description'];
			$edata['content']         = $articleInfoen['content'];
			$edata['description']     = $articleInfoen['description'];
			$edata['sort']            = $articleInfoen['sort'];
			$edata['cover'] = isArray($articleInfoen['cover']) && isset($articleInfoen['cover']['fileid']) ? $articleInfoen['cover']['fileid'] :'';

			$photoId = isArray($articleInfoen['photos']) ? array_column($articleInfoen['photos'], 'fileid') :'';
			$edata['photos'] = isArray($photoId) ? implode(';',$photoId) :'';
			$attachmentId    = isArray($articleInfoen['attachment']) ? array_column($articleInfoen['attachment'], 'fileid') :'';
			$edata['attachment']  = isArray($attachmentId) ? implode(';',$attachmentId) :'';

			$edata['from_link']   = $articleInfoen['from_link'];
			$edata['status']      = $articleInfocn['status'];//显示 
			$edata['relation_id'] = $articleCnId;
			$articleEnId          = $this->Article_model->create($edata);
			if(!$articleInfocn['category']){
				$this->outputFail('请选择分类');
			}
			if(!$articleInfocn['title']){
				$this->outputFail('请输入标题');
			}
			if(!$articleInfocn['cover']){
				$this->outputFail('请上传图片');
			}
			if(!$articleEnId){
				$this->outputFail('操作失败');
			}else{
				$this->Article_model->update($articleCnId,array('relation_id'=>$articleEnId));
			}
 		}
			$this->outputSuccess('ok');

	}


	/**
	 * edit 编辑
	 */
	public function edit(){
		$articleParams = json_decode($this->input->post('article_params'),true);
		if(!isArray($articleParams)){
			$this->outputFail('传参错误');
		}
		$articleCn = $articleParams['zh_cn'];
		$articleEn = $articleParams['en'];
		
		$cdata = array();
		if(isArray($articleCn) && $articleParams['id']!=''){
			$cdata['id'] = $articleParams['id'];
			$articleInfocn = $this->Article_model->load($cdata['id']);
		}
		$edata = array();
		if(isArray($articleEn) && $articleParams['id']!=''){
			$edata['id'] = $articleInfocn['relation_id'];
			$articleInfoen = $this->Article_model->load($edata['id']);
		}

		$this->load->model('category_model');
		$relationCategory = $this->category_model->load($articleCn['category']);
		if(isArray($articleInfocn)){
			$cdata['lang']            = $articleInfocn['lang'];
			$cdata['category']        = $articleCn['category'];
			$cdata['title']           = $articleCn['title'];
			$cdata['subhead']         = $articleCn['subhead'];
			$cdata['title_style']     = implode(';',$articleCn['title_style']);
			$cdata['external_link']   = $articleCn['external_link'];
			$cdata['author']          = $articleCn['author'];
			$cdata['seo_keywords']    = $articleCn['seo_keywords'];
			$cdata['seo_description'] = $articleCn['seo_description'];
			$cdata['content']         = $articleCn['content'];
			$cdata['description']     = $articleCn['description'];
			$cdata['sort']            = $articleCn['sort'];
			$cdata['cover']           = isArray($articleCn['cover']) && isset($articleCn['cover']['fileid']) ? $articleCn['cover']['fileid'] :'';
			$photoId                  = isArray($articleCn['photos']) ? array_column($articleCn['photos'], 'fileid') :'';
			$cdata['photos']          = isArray($photoId) ? implode(';',$photoId) :'';
			$attachmentId             = isArray($articleCn['attachment']) ? array_column($articleCn['attachment'], 'fileid') :'';
			$cdata['attachment']      = isArray($attachmentId) ? implode(';',$attachmentId) :'';	
			$cdata['from_link']   = $articleCn['from_link'];
			$cdata['status']      = $articleCn['status'];//显示
			$cdata['relation_id'] = $articleInfocn['relation_id'];
			if(!$this->Article_model->update($cdata['id'],$cdata)){
				$this->outputFail('操作失败');
			}
			if($cdata['cover'] !=''){
				$att = array();
				$att['title']            = $cdata['title'];
				$att['origin_id']        = $articleParams['id'];
				$att['origin_data_type'] = 2;
				$this->Attachment_model->update($articleCn['cover']['fileid'],$att);
			}
			if(isArray($photoId)){
				foreach ($photoId as $v) {
					$attP = array();
					$attP['title'] = $cdata['title'];
					$attP['origin_id'] = $cdata['id'];
					$attP['origin_data_type'] = 2;						
					$this->Attachment_model->update($v,$attP);
				}
			}
			if(isArray($attachmentId)){
				foreach ($attachmentId as $v) {
					$attach = array();
					$attach['title'] = $cdata['title'];
					$attach['origin_id'] = $cdata['id'];
					$attach['origin_data_type'] = 2;						
					$this->Attachment_model->update($v,$attach);
				}
			}
		}

		if(isArray($articleInfoen)){
			$edata['lang']            = $articleInfoen['lang'];
			$edata['category']        = $relationCategory['relation_id'];
			$edata['title']           = $articleEn['title'];
			$edata['subhead']         = $articleEn['subhead'];
			$edata['title_style']     = implode(';',$articleEn['title_style']);
			$edata['external_link']   = $articleEn['external_link'];
			$edata['author']          =	$articleEn['author'];
			$edata['seo_keywords']    = $articleEn['seo_keywords'];
			$edata['seo_description'] = $articleEn['seo_description'];
			$edata['content']         = $articleEn['content'];
			$edata['description']     = $articleEn['description'];
			$edata['sort']            = $articleEn['sort'];
			
			$edata['cover']           = isArray($articleEn['cover']) && isset($articleEn['cover']['fileid']) ? $articleEn['cover']['fileid'] :'';
			
			$photoId                  = isArray($articleEn['photos']) ? array_column($articleEn['photos'], 'fileid') :'';
			$edata['photos']          = isArray($photoId) ? implode(';',$photoId) :'';
			$attachmentId             = isArray($articleEn['attachment']) ? array_column($articleEn['attachment'], 'fileid') :'';
			$edata['attachment']      = isArray($attachmentId) ? implode(';',$attachmentId) :'';
			$edata['from_link']       = $articleEn['from_link'];
			$edata['status']          = $articleEn['status'];//显示
			$edata['relation_id']     = $articleInfoen['relation_id'];
			if(!$this->Article_model->update($edata['id'],$edata)){
				$this->outputFail('操作失败');
			}
 			
		}
		if(isArray($articleEn) && $articleEn['title'] =='' && $articleInfocn['relation_id']==''){
			$data['lang']            = $articleInfoen['lang'];
			$data['category']        = $relationCategory['relation_id'];
			$data['title']           = $articleInfoen['title'];
			$data['subhead']         = $articleInfoen['subhead'];
			$data['title_style']     = $articleInfoen['title_style'];
			$data['external_link']   = $articleInfoen['external_link'];
			$data['author']          = $articleInfoen['author'];
			$data['seo_keywords']    = $articleInfoen['seo_keywords'];
			$data['seo_description'] = $articleInfoen['seo_description'];
			$data['content']         = $articleInfoen['content'];
			$data['description']     = $articleInfoen['description'];
			$data['sort']            = $articleInfoen['sort'];
			$data['cover']           = $articleInfoen['cover'];
			$cdata['photos']         = $articleInfocn['photos'];
			$data['photos']          = implode(';',$photoId);
			$data['from_link']       = $articleInfoen['from_link'];
			$data['status']          = 1;//显示
			$data['relation_id']     = $articleCn['relation_id'];
			$newId                   = $this->Article_model->create($data);
			if($newId){
				$this->outputFail('添加失败');
			}else{
				$this->Article_model->update($articleCn['id'],array('relation_id'=>$newId));
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
			$this->outputFail('传参错误');
		}
		$articleInfo   = $this->Article_model->getByIds($ids);
		$articleInfoEn = $this->Article_model->getByCnIds($ids);

		$data = array();
		$data['is_delete'] = EnumRes::DELETE_YES;
		if(isArray($articleInfo)){
			foreach($articleInfo as $value){
				if(!$this->Article_model->update($value['id'],$data)){
					$this->outputFail('删除中文文章失败');
				}
			}
		}
		if(isArray($articleInfoEn)){
			foreach($articleInfoEn as $value){
				if(!$this->Article_model->update($value['id'],$data)){
					$this->outputFail('删除英文文章失败');
				}
			}
		}
       	$this->outputSuccess('删除成功');

	}



	/**
	 * index 文章列表
	 */
	public function index(){
		$page                   = $this->input->get('page');
		$pageSize               = $this->input->get('page_size');
		$IsDelete               = $this->input->get('is_delete');
		$categoryId             = $this->input->get('category');
		$status                 = $this->input->get('status');
		$title                  = $this->input->get('title');
		$sort                   = $this->input->get('sort');
		$sortRule               = $this->input->get('sort_rule');
		!$page && $page         = 1;
		!$pageSize && $pageSize = 20;
		
		$item                   = array();
		$item['is_delete']      = $IsDelete;
		$item['status']         = $status;
		$item['category']       = $categoryId;
		$item['title']          = $title;
		$item['sort']           = $sort;
		$item['sort_rule']      = $sortRule;

        if (isset($item['sort']) && $item['sort_rule'] == 1) {
        	$options = array('conditions'=>array('lang'=>'zh_cn'),'order' => $item['sort']. ' asc');
        }elseif (isset($item['sort']) && $item['sort_rule'] == 2) {
        	$options = array('conditions'=>array('lang'=>'zh_cn'),'order' => $item['sort']. ' desc');
        }else{
			$options = array('conditions'=>array('lang'=>'zh_cn'),'order' => 'sort desc');
        }
		if(isset($item['is_delete'])){
            $options['conditions']['is_delete'] = $item['is_delete'];
        }
        if(isset($item['category']) && $item['category']){
            $options['conditions']['category'] = $item['category'];
        }
        if(isset($item['status']) && $item['status']){
        	$options['conditions']['status'] = $item['status'];
        }
        if(isset($item['title']) && $item['title']){
        	$options['conditions']['title'] = $item['title'];
        }

        $total = $this->Article_model->count($options);
        if($total){
        	$list = $this->Article_model->find($options,$pageSize, ($page - 1) * $pageSize);
        }else{
        	$list=array();
        }

        $data = array();
        $data['list']['zh_cn']['total'] = $total;
        $data['list']['zh_cn']['list'] =$list;
        $this->outputSuccess('ok',$data);
	}


	/**
	 * recover 文章恢复
	 */
    public function recover(){
       $ids = json_decode($this->input->post('ids'),true);
       if(!isArray($ids)){
         $this->outputFail('传参错误');
       }
       $articleInfo = $this->Article_model->getByIds($ids);
       $articleInfoEn = $this->Article_model->getByCnIds($ids);

       $data = array();
       $data['is_delete'] = EnumRes::DELETE_NO;
       if(isArray($articleInfo)){
           foreach($articleInfo as $val){
               if(!$this->Article_model->update($val['id'],$data)){
                  $this->outputFail('操作失败');
               }
           }
       }
       if(isArray($articleInfoEn)){
          foreach($articleInfoEn as $item){
             if(!$this->Article_model->update($item['id'],$data)){
                $this->outputFail('操作失败');
             }
          }
       }
       $this->outputSuccess('恢复成功');
    }


    /**
     * status 文章显示
     */


    public function status(){
    	$ids  = json_decode($this->input->post('ids'),true);
    	$status = $this->input->post('status');
    	if(!isArray($ids)){
    		$this->outputFail('文章不存在');
    	}
		// $articleInfo = $this->Article_model->getByIds($ids);
		$articleInfoEn = $this->Article_model->getByCnIds($ids);
    	foreach ($ids as $value) {
    		$info = $this->Article_model->update($value,array('status'=>$status));
    	}
    	if(!$info){
    		$this->outputFail('操作失败');
    	}else{
	    	foreach ($articleInfoEn as $value) {
	    		$this->Article_model->update($value['id'],array('status'=>$status));
	    	}
    	}
    	$this->outputSuccess('ok');
    }


    /**
	 * thoroughlyDel 彻底删除
	 */
	public function thoroughlyDel(){
		$ids = json_decode($this->input->post('ids'),true);

		if(!isArray($ids)){
			$this->outputFail('传参错误');
		}
		$articleInfo = $this->Article_model->getByIds($ids);
		$articleInfoEn = $this->Article_model->getByCnIds($ids);
		if(isArray($articleInfo)){
			foreach($articleInfo as $value){
				if(!$this->Article_model->delete($value['id'])){
					$this->outputFail('删除中文文章失败');
				}
			}
		}
		if(isArray($articleInfoEn)){
			foreach($articleInfoEn as $value){
				if(!$this->Article_model->delete($value['id'])){
					$this->outputFail('删除英文文章失败');
				}
			}
		}
       	$this->outputSuccess('删除成功');

	}

	/**
     * sort 文章排序
     */
    public function sort(){
    	$id = $this->input->post('id');
    	$sort = trim($this->input->post('sort'));
    	if(!$id){
    		$this->outputFail('该文章不存在');
    	}
    	if(!$sort){
    		$this->outputFail('操作错误');
    	}
    	$infocn = $this->Article_model->load($id);

    	if(isArray($infocn) && $infocn['relation_id'] !=''){
    		$this->Article_model->update($id,array('sort'=>$sort));
    		$this->Article_model->update($infocn['relation_id'],array('sort'=>$sort));
    	}elseif(isArray($infocn) && $infocn['relation_id'] ==''){
    		$this->Article_model->update($id,array('sort'=>$sort));
    	}else{
    		$this->outputFail('操作失败');
    	}
    	$this->outputSuccess('ok');

    }
    /**
     * 文章详情 
     * 2019.02.18
     */

    public function detail(){
    	$id = intval($this->input->get('id'));
    	if (!$id) {
    		$this->outputFail('参数错误');
    	}
    	$articleDetail = $this->Article_model->load($id);
    	if (!isArray($articleDetail)) {
    		$this->outputFail('数据不存在');
    	}
    	if ($articleDetail['is_delete'] == EnumRes::DELETE_YES) {
    		$this->outputFail('数据已被删除');
    	}

    	$dataCn = $articleDetail;
    	$dataCn['title_style'] = isset($articleDetail['title_style']) ? explode(';', $articleDetail['title_style']) : array();
    	$dataCn['cover'] = isset($articleDetail['cover']) ? $this->Attachment_model->load($articleDetail['cover']) : array();
    	$dataCn['cover'] = isArray($dataCn['cover']) ? array('fileid'=>$dataCn['cover']['id'],'fullurl'=>base_url().$dataCn['cover']['url']) :array();
    	$photoListIds = isset($articleDetail['photos']) ? explode(';', $articleDetail['photos']) :array();//将id转成数组
    	$photosList = $this->Attachment_model->find(array('conditions'=>array('ids'=>$photoListIds)));//根据id数组从附件表查询照片列表信息
    	if (isArray($photosList)) {
    		$photoArr = array();
    		foreach ($photosList as $val) {
    			$tmp = array();
    			$tmp['fileid'] = $val['id'];
    			$tmp['fullurl'] = base_url().$val['url'];
    			$photoArr[] = $tmp;
    		}
    	}
    	$dataCn['photos'] = isset($photoArr) ? $photoArr : array();
    	$AttachmentListIds = isset($articleDetail['attachment']) ? explode(';', $articleDetail['attachment']) :array();
    	$AttachmentList = $this->Attachment_model->find(array('conditions'=>array('ids'=>$AttachmentListIds)));
    	if (isArray($AttachmentList)) {
    		$AttachmentArr = array();
    		foreach ($AttachmentList as $val) {
    			$tmp = array();
    			$tmp['fileid'] = $val['id'];
    			$tmp['fullurl'] = base_url().$val['url'];
    			$AttachmentArr[] = $tmp;
    		}
    	}
    	$dataCn['attachment'] = isset($AttachmentArr) ? $AttachmentArr : array() ;
    	if (isset($articleDetail['relation_id']) && $articleDetail['relation_id']) {
			$articleRelationDetail = $this->Article_model->load($articleDetail['relation_id']);
	    	$dataEn = $articleRelationDetail;
	    	$dataEn['title_style'] = isset($articleRelationDetail['title_style']) ? explode(';', $articleRelationDetail['title_style']) : array();
	    	$dataEn['cover'] = isset($articleRelationDetail['cover']) ? $this->Attachment_model->load($articleRelationDetail['cover']) : array();
	    	$dataEn['cover'] = isArray($dataEn['cover']) ? array('fileid'=>$dataEn['cover']['id'],'fullurl'=>base_url().$dataEn['cover']['url']) :array();
	    	$photoListIds = isset($articleRelationDetail['photos']) ? explode(';', $articleRelationDetail['photos']) :array();//将id转成数组
	    	$photosList = $this->Attachment_model->find(array('conditions'=>array('ids'=>$photoListIds)));//根据id数组从附件表查询照片列表信息
	    	if (isArray($photosList)) {
	    		$photoArr = array();
	    		foreach ($photosList as $val) {
	    			$tmp = array();
	    			$tmp['fileid'] = $val['id'];
	    			$tmp['fullurl'] = base_url().$val['url'];
	    			$photoArr[] = $tmp;
	    		}
	    	}
	    	$dataEn['photos'] = isset($photoArr) ? $photoArr : array();
	    	$AttachmentListIds = isset($articleRelationDetail['attachment']) ? explode(';', $articleRelationDetail['attachment']) :array();
	    	$AttachmentList = $this->Attachment_model->find(array('conditions'=>array('ids'=>$AttachmentListIds)));
	    	if (isArray($AttachmentList)) {
	    		$AttachmentArr = array();
	    		foreach ($AttachmentList as $val) {
	    			$tmp = array();
	    			$tmp['fileid'] = $val['id'];
	    			$tmp['fullurl'] = base_url().$val['url'];
	    			$AttachmentArr[] = $tmp;
	    		}
	    	}
	    	$dataEn['attachment'] = isset($AttachmentArr) ? $AttachmentArr : array() ;			    		
    	}
    	$dataEn = isArray($dataEn) ? $dataEn : array();
    	$this->outputSuccess('ok',array('info'=>array('zh_cn'=>$dataCn,'en'=>$dataEn)));
    }










}