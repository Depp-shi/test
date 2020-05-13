<?php 
	require_once (APPPATH . 'controllers/manage/api/BaseApiController.php');
/**
 * 广告管理
 */

class Advertisement extends BaseApiController{
	public function __construct(){
    	parent::__construct();
    	// $this->checkApiLogin();
		$this->load->model('Advertisement_model');
		$this->load->model('Attachment_model');
		$this->load->model('Category_model');

	}

		/**
		 * add 添加广告
		 */
	public function add(){
		$ads_params = json_decode($this->input->post('ads_params'),true);
		if (!isArray($ads_params)) {
			$this->outputFail('参数错误');
		}
		$adscn = $ads_params['zh_cn'];
		$adsen = $ads_params['en'];
		if(isArray($adscn)){
			$cndata                  = array();
			$cndata['position']      = $adscn['position'];
			$cndata['position_type'] = $adscn['position_type'];
			$cndata['position_desc'] = $adscn['position_desc'];
			$cndata['type']          = $adscn['type'];
			$cndata['link_url']      = $adscn['link_url'];
			$cndata['link_target']   = $adscn['link_target'];
			$cndata['description']   = $adscn['description'];
			$cndata['sort']          = $adscn['sort'];
			$cndata['status']        = $adscn['status'];
			$cndata['img_url']       = isArray($adscn['img_url']) ? $adscn['img_url']['fileid'] :'';
			$cndata['video_url']     = isArray($adscn['video_url'])&&isset($adscn['video_url']['fileid']) ? $adscn['video_url']['fileid'] :'';
			$cndata['title']         = $adscn['title'];
			$cndata['keywords']      = $adscn['keywords'];
			$cndata['lang']          = 'zh_cn';
			$cndata['relation_id']   = 0;
			if(!$adscn['title']){
				$this->outPutFail('请输入标题');
			}
			if(!$adscn['position']){
				$this->outputFail('请输入位置标识');
			}
			$adscnId = $this->Advertisement_model->create($cndata);
			if (!$adscnId) {
				$this->outputFail('操作失败');
			}
			if(isArray($adscn['img_url'])&&isset($adscn['img_url']['fileid'])){
				$att                     = array();
				$att['title']            = $cndata['title'];
				$att['origin_id']        = $adscnId;
				$att['origin_data_type'] = 1;
				$this->Attachment_model->update($adscn['img_url']['fileid'],$att);
			}
			if(isArray($adscn['video_url']) && isset($adscn['video_url']['fileid'])){
				$attV                     = array();
				$attV['title']            = $cndata['title'];
				$attV['origin_id']        = $adscnId;
				$attV['type']             = $type;
				$attV['origin_data_type'] = 3;
				$this->Attachment_model->update($adscn['video_url']['fileid'],$attV);
			}

		}
		if(isArray($adsen) && $adscnId){
			$endata                  = array();
			$endata['position']      = $adsen['position'];
			$endata['position_type'] = $adsen['position_type'];
			$endata['position_desc'] = $adsen['position_desc'];
			$endata['type']          = $adsen['type'];
			$endata['link_url']      = $adsen['link_url'];
			$endata['link_target']   = $adsen['link_target'];
			$endata['description']   = $adsen['description'];
			$endata['sort']          = $adsen['sort'];
			$endata['status']        = $adsen['status'];
			$endata['img_url']       = isArray($adsen['img_url']) ? $adsen['img_url']['fileid'] :'';
			$endata['video_url']     = isArray($adsen['video_url'])&&isset($adsen['video_url']['fileid']) ? $adsen['video_url']['fileid'] :'';
			$endata['title']         = $adsen['title'];
			$endata['keywords']      = $adsen['keywords'];
			$endata['lang']          = 'en';
			$endata['relation_id'] 	 = $adscnId;
			$adsenId = $this->Advertisement_model->create($endata);
			if (isArray($adsenId)) {
				$this->outputFail('操作失败');
			}
			if($adsenId){
				$this->Advertisement_model->update($adscnId,array('relation_id'=>$adsenId));
				if(isArray($adsen['img_url']) && isset($adsen['img_url']['fileid'])){
					$att                     = array();
					$att['title']            = $cndata['title'];
					$att['origin_id']        = $adsenId;
					$att['origin_data_type'] = 1;
					$this->Attachment_model->update($adsen['img_url']['fileid'],$att);
				}
				if(isArray($adsen['video_url']) && isset($adsen['video_url']['fileid'])){
					$attV                     = array();
					$attV['title']            = $cndata['title'];
					$attV['origin_id']        = $adsenId;
					$attV['type']             = $type;
					$attV['origin_data_type'] = 3;						
					$this->Attachment_model->update($adsen['video_url']['fileid'],$attV);
				}
			}
			$this->outputSuccess('操作成功');				
		}
		$this->outputSuccess('ok');
	}
		/**
		 * edit 编辑广告
		 * @return [type] [description]
		 */
	public function edit(){
		$ads_params = json_decode($this->input->post('ads_params'),true);
		if(!isArray($ads_params)){
			$this->outputFail('传参错误');
		}

		$adscn = $ads_params['zh_cn'];
		$adsen = $ads_params['en'];
		$cdata = array();
		if(isArray($adscn) && $ads_params['id']!=''){
			$cdata['id'] = $ads_params['id'];
			$adscnInfo = $this->Advertisement_model->load($cdata['id']);
		}
		$edata = array();
		if(isArray($adsen) && $ads_params['id']!=''){
			$edata['id'] = $adscnInfo['relation_id'];
			$adsenInfo = $this->Advertisement_model->load($edata['id']);
		}			
		if(isArray($adscnInfo)){
			$cdata['lang']          = 'zh_cn';
			$cdata['position_type'] = $adscn['position_type'];
			$cdata['position']      = $adscn['position'];
			$cdata['position_desc'] = $adscn['position_desc'];
			$cdata['type']          = $adscn['type'];
			$cdata['link_url']      = $adscn['link_url'];
			$cdata['link_target']   = $adscn['link_target'];
			$cdata['description']   = $adscn['description'];
			$cdata['sort']          = $adscn['sort'];
			$cdata['status']        = $adscn['status'];
			$cdata['created_time']  = $adscn['created_time'];
			$cdata['img_url']       = isArray($adscn['img_url']) && isset($adscn['img_url']['fileid']) ? $adscn['img_url']['fileid'] :'';
			$cdata['video_url']     = isArray($adscn['video_url']) && isset($adscn['video_url']['fileid']) ? $adscn['video_url']['fileid']:'';
			$cdata['title']         = $adscn['title'];
			$cdata['keywords']      = $adscn['keywords'];
			if(!$this->Advertisement_model->update($ads_params['id'],$cdata)){
				$this->outputFail('操作失败');
			}
			if(isArray($adscn['img_url']) && $adscn['img_url']['fileid'] !=''){
				$att                     = array();
				$att['title']            = $cdata['title'];
				$att['origin_id']        = $ads_params['id'];
				$att['origin_data_type'] = 1;
				$this->Attachment_model->update($adscn['img_url']['fileid'],$att);
			}
			if(isArray($adscn['video_url']) && $adscn['video_url']['fileid'] !=''){
				$attV                     = array();
				$attV['title']            = $cdata['title'];
				$attV['origin_id']        = $ads_params['id'];
				$attV['type']             = $adscn['type'];
				$attV['origin_data_type'] = 3;	
				$this->Attachment_model->update($adscn['video_url']['fileid'],$att);
			}
		}


		if(isArray($adsenInfo)){
			$edata['lang']          = 'en';
			$edata['position']      = $adsen['position'];
			$edata['position_type'] = $adsen['position_type'];
			$edata['position_desc'] = $adsen['position_desc'];
			$edata['type']          = $adsen['type'];
			$edata['link_url']      = $adsen['link_url'];
			$edata['link_target']   = $adsen['link_target'];
			$edata['description']   = $adsen['description'];
			$edata['sort']          = $adsen['sort'];
			$edata['status']        = $adsen['status'];
			$edata['created_time']  = $adsen['created_time'];
			$edata['img_url']       = isArray($adsen['img_url']) && isset($adsen['img_url']['fileid']) ? $adsen['img_url']['fileid'] :'';
			$edata['video_url']     = isArray($adsen['video_url']) && isset($adsen['video_url']['fileid']) ? $adsen['video_url']['fileid']:'';
			$edata['title']         = $adsen['title'];
			$edata['keywords']      = $adsen['keywords'];

			if(!$this->Advertisement_model->update($edata['id'],$edata)){
				$this->outputFail('操作失败');
			}
			if(isArray($adsen['img_url']) && $adsen['img_url']['fileid'] !=''){
				$att                     = array();
				$att['title']            = $cdata['title'];
				$att['origin_id']        = $adsenInfo['id'];
				$att['origin_data_type'] = 1;					
			    $this->Attachment_model->update($adsen['img_url']['fileid'],$att);
			}
			if(isArray($adsen['video_url']) && $adsen['video_url']['fileid'] !=''){
				$attV                     = array();
				$attV['title']            = $cdata['title'];
				$attV['origin_id']        = $adsenInfo['id'];
				$attV['type']             = $edata['type'];
				$attV['origin_data_type'] = 3;
				$this->Attachment_model->update($adsen['video_url']['fileid'],$attV);
			}				
		}

		$this->outputSuccess('操作成功');
	}


	/**
	 * del  删除广告
	 */
	public function del(){
		$ids = json_decode($this->input->post('id'));
		if(!isArray($ids)){
			$this->outputFail('参数错误');
		}
		$adsInfocn = $this->Advertisement_model->getByIds($ids);
		$adsInfoen = $this->Advertisement_model->getByCnIds($ids);
		$data = array();
		$data['is_delete'] = EnumRes::DELETE_YES;
		if(isArray($adsInfocn)){
			foreach ($adsInfocn as $value) {
				if(!$this->Advertisement_model->update($value['id'],$data)){
					$this->outputFail('删除中文广告失败');
				}
			}
		}
		if(isArray($adsInfoen)){
			foreach ($adsInfoen as $value) {
				if(!$this->Advertisement_model->update($value['id'],$data)){
					$this->outputFail('删除英文广告失败');
				}
			}
		}
		$this->outputSuccess('删除成功');
	}

		/** 
		 * detail 广告详情
		 */
	public function detail(){
		$id = $this->input->get('id');
		if(!$id){
			$this->outputFail('该广告不存在');
		}
		$isDelete = EnumRes::EnumIsDelete();
		$isStatus = EnumRes::EnumAdsStatus();
		$adsInfocn = $this->Advertisement_model->load($id);
		if (!isArray($adsInfocn)) {
			$this->outputFail('该广告不存在');
		}
		$data = array();
		if(isArray($adsInfocn)){
			$img = $video = array();
			if(isset($adsInfocn['img_url']) && $adsInfocn['img_url']){
				$imgInfo              = $this->Attachment_model->load($adsInfocn['img_url']);
				$imgArr               = array();
				$imgArr['fileid']     = $imgInfo['id'];
				$imgArr['filename']   = isset($imgInfo['title']) ? $imgInfo['title'] :'';
				$imgArr['fileurl']    = isset($imgInfo['url']) ? $imgInfo['url'] :'';
				$imgArr['fullpath']   = isset($imgInfo['url']) ? base_url($imgInfo['url']) :'';
				$img = isArray($imgArr) ? $imgArr :'';
			}
			if(isset($adsInfocn['video_url']) && $adsInfocn['video_url']){
				$videoInfo              = $this->Attachment_model->load($adsInfocn['video_url']);
				$videoArr               = array();
				$videoArr['fileid']     = $videoInfo['id'];
				$videoArr['filename']   = isset($videoInfo['title']) ? $videoInfo['title'] :'';
				$videoArr['fileurl']    = isset($videoInfo['url']) ? $videoInfo['url'] :'';
				$videoArr['fullpath']   = isset($videoInfo['url']) ? base_url($videoInfo['url']) :'';
				$video = isArray($videoArr) ? $videoArr :'';
			}
			$adsInfocn['img_url']       = isArray($img) ? $img :'';
			$adsInfocn['video_url']     = isArray($video) ? $video :'';
			$adsInfocn['position']      = $adsInfocn['position'];
			$adsInfocn['position_desc'] = $adsInfocn['position_desc'];
			$adsInfocn['type']          = $adsInfocn['type'];
			$adsInfocn['position_type'] = $adsInfocn['position_type'];
			$adsInfocn['title']         = $adsInfocn['title'];
			$adsInfocn['link_url']      = $adsInfocn['link_url'];
			$adsInfocn['link_target']   = $adsInfocn['link_target'];
			$adsInfocn['keywords']      = $adsInfocn['keywords'];
			$adsInfocn['description']   = $adsInfocn['description'];
			$adsInfocn['sort']          = $adsInfocn['sort'];
			$adsInfocn['is_delete']     = $adsInfocn['is_delete'];
			$data['info']['zh_cn']      = $adsInfocn;
		}
		if(isArray($adsInfocn) && $adsInfocn['relation_id']){
			$adsInfoen = $this->Advertisement_model->load($adsInfocn['relation_id']);
			if(isArray($adsInfoen)){
				$img = $video = array();
				if(isset($adsInfoen['img_url']) && $adsInfoen['img_url']){
					$imgInfo              = $this->Attachment_model->load($adsInfoen['img_url']);
					$imgArr               = array();
					$imgArr['fileid']     = $imgInfo['id'];
					$imgArr['filename']   = isset($imgInfo['title']) ? $imgInfo['title'] :'';
					$imgArr['fileurl']    = isset($imgInfo['url']) ? $imgInfo['url'] :'';
					$imgArr['fullpath']   = isset($imgInfo['url']) ? base_url($imgInfo['url']) :'';
					$img = isArray($imgArr) ? $imgArr :'';
				}
				if(isset($adsInfoen['video_url']) && $adsInfoen['video_url']){
					$videoInfo              = $this->Attachment_model->load($adsInfoen['video_url']);
					$videoArr               = array();
					$videoArr['fileid']     = $videoInfo['id'];
					$videoArr['filename']   = isset($videoInfo['title']) ? $videoInfo['title'] :'';
					$videoArr['fileurl']    = isset($videoInfo['url']) ? $videoInfo['url'] :'';
					$videoArr['fullpath']   = isset($videoInfo['url']) ? base_url($videoInfo['url']) :'';
					$video = isArray($videoArr) ? $videoArr :'';
				}
				$adsInfoen['img_url']       = isArray($img) ? $img :'';
				$adsInfoen['video_url']     = isArray($video) ? $video :'';
				$adsInfoen['position']      = $adsInfoen['position'];
				$adsInfoen['position_desc'] = $adsInfoen['position_desc'];
				$adsInfoen['type']          = $adsInfoen['type'];
				$adsInfoen['position_type'] = $adsInfoen['position_type'];
				$adsInfoen['title']         = $adsInfoen['title'];
				$adsInfoen['link_url']      = $adsInfoen['link_url'];
				$adsInfoen['link_target']   = $adsInfoen['link_target'];
				$adsInfoen['keywords']      = $adsInfoen['keywords'];
				$adsInfoen['description']   = $adsInfoen['description'];
				$adsInfoen['sort']          = $adsInfoen['sort'];
				$adsInfoen['is_delete']     = $adsInfoen['is_delete'];
				$data['info']['en']       = $adsInfoen;
			}
		}
		return $this->outputSuccess('ok',$data);
	}

		/**
		 * index 广告列表
		 */
	public function index(){
		$page          = $this->input->get('page'); 
		$pageSize      = $this->input->get('page_size');  
		$position      = $this->input->get('position'); 
		$status        = $this->input->get('status');
		$isDelete      = $this->input->get('is_delete'); 
		$position_type = $this->input->get('position_type'); 
		$title         = $this->input->get('title');
		$type          = $this->input->get('type'); 
		$sort          = $this->input->get('sort');
		$sortRule      = $this->input->get('sort_rule');
       	!$page && $page = 1; 
       	!$pageSize && $pageSize = 20;
       	$segments = array();
		$segments['position']      = $position;
		$segments['status']        = $status;
		$segments['is_delete']     = $isDelete;
		$segments['position_tpye'] = $position_type;
		$segments['title']         = $title;
		$segments['type']          = $type;
		$segments['sort']          = $sort;
		$segments['sort_rule']     = $sortRule;
        if (isset($segments['sort']) && $segments['sort_rule'] == 1) {
        	$options = array('conditions'=>array('lang'=>'zh_cn'),'order' => $segments['sort']. ' asc');
        }elseif (isset($segments['sort']) && $segments['sort_rule'] == 2) {
        	$options = array('conditions'=>array('lang'=>'zh_cn'),'order' => $segments['sort']. ' desc');
        }else{
			$options = array('conditions'=>array('lang'=>'zh_cn'),'order' => 'sort desc');
        }
   		if(isset($segments['position']) && $segments['position']!=''){
       		$options['conditions']['position'] = $segments['position'];
   		}
   		if(isset($segments['position_type']) && $segments['position_type']!=''){
       		$options['conditions']['position_type'] = $segments['position_type'];
   		}
   		if(isset($segments['type']) && $segments['type']!=''){
       		$options['conditions']['type'] = $segments['type'];
   		}
   		if(isset($segments['title']) && $segments['title']!=''){
       		$options['conditions']['title'] = $segments['title'];
   		}
   		if($segments['is_delete']!= ''){
        	$options['conditions']['is_delete'] = $segments['is_delete'];
    	}
   		if(isset($segments['status']) && $segments['status']!=''){
       		$options['conditions']['status'] = $segments['status'];
   		}
   		if(isset($segments['lang']) && $segments['lang']!=''){
       		$options['conditions']['lang'] = $segments['lang'];
   		}
   		$total = $this->Advertisement_model->count($options);
   		if($total){
      		$adslist = $this->Advertisement_model->find($options,$pageSize,($page-1)*$pageSize);
   		}else{
      		$adslist = array();
   		}
		$enumAttachmentType = EnumRes::EnumAttachmentType();
   		if(isArray($adslist)){
	   		$item = array();
			foreach($adslist as &$item){

			if($item['position'] =='首页焦点图' || $item['position'] =='首页'){
				$item['position'] = $item['position'] ? $item['position'] : '';
			}else{
				$positionId = $this->Category_model->load($item['position']);
				$item['position'] = $positionId['title'];
			}
			// $item['position'] = $item['position'] ? $item['position'] : '';
			if($item['img_url'] !=''){
				$item['img_url'] = $item['img_url'];
				$imgInfo         = $this->Attachment_model->load($item['img_url']);
				$arr             = array();
				$arr['fileid']   = $imgInfo['id'];
				$arr['filename'] = $imgInfo['title'];
				$arr['fileurl']  = $imgInfo['url'];
				$arr['fullpath'] = base_url($imgInfo['url']);
				$item['img_url'] = $arr;
			}else{
				$item['img_url'] = '';
			}
			if($item['video_url'] !='0' || $item['video_url'] !=''){
			 	$item['video_url'] = $item['video_url'];
			 	$videoInfo = $this->Attachment_model->load($item['video_url']);
			 	$arr = array();
				$arr['fileid']   = $videoInfo['id'];
				$arr['filename'] = $videoInfo['title'];
				$arr['fileurl']  = $videoInfo['url'];
				$arr['fullpath']  = base_url($videoInfo['url']);
			 	$item['video_url'] = $arr;
			 }else{
			 	$item['video_url'] ='';
			 }
				$item['title']       = $item['title'];
				$item['sort']        = $item['sort'];
				$item['link_target'] = $item['link_target'];
				$item['keywords']    = $item['keywords'];
				$item['status']   = $item['status'];
				$item['link_url'] = $item['link_url'];
				$item['lang']        = $item['lang'];
				$item['description'] = $item['description'] ? $item['description'] : '';
				$item['type']        = isset($enumAttachmentType[$item['type']]) ? $enumAttachmentType[$item['type']] : '';
			}
   		}
	   	$data =array();
	   	$data['list']['zh_cn']['total'] = $total;
	   	$data['list']['zh_cn']['list'] = $adslist;

        $this->outputSuccess('ok',$data);

	}


		/**
		 * recover 广告恢复
		 */

    public function recover(){
			$ids = json_decode($this->input->post('ids'),true);
			if(!isArray($ids)){
				$this->outputFail('参数错误');
			}
			$adsInfocn = $this->Advertisement_model->getByIds($ids);
			$adsInfoen = $this->Advertisement_model->getByCnIds($ids);
			$data = array();
			$data['is_delete'] = EnumRes::DELETE_NO;
			if(isArray($adsInfocn)){
				foreach ($adsInfocn as $value) {
					if(!$this->Advertisement_model->update($value['id'],$data)){
						$this->outputFail('恢复中文广告失败');
					}
				}
			}
			if(isArray($adsInfoen)){
				foreach ($adsInfoen as $value) {
					if(!$this->Advertisement_model->update($value['id'],$data)){
						$this->outputFail('恢复英文广告失败');
					}
				}
			}
			$this->outputSuccess('恢复成功');
    }



    /**
     * status 广告显示
     */
    public function status(){
    	$ids  = json_decode($this->input->post('ids'),true);
    	$status = $this->input->post('status');
    	if(!isArray($ids)){
    		$this->outputFail('文章不存在');
    	}
		$articleInfoEn = $this->Advertisement_model->getByCnIds($ids);
    	foreach ($ids as $value) {
    		$info = $this->Advertisement_model->update($value,array('status'=>$status));
    	}
    	if(!$info){
    		$this->outputFail('操作失败');
    	}else{
	    	foreach ($articleInfoEn as $value) {
	    		$this->Advertisement_model->update($value['id'],array('status'=>$status));
	    	}
    	}
    	$this->outputSuccess('ok');
    }


    /**
     * sort 广告排序
     */
    public function sort(){
    	$id = $this->input->post('id');
    	$sort = trim($this->input->post('sort'));
    	if(!$id){
    		$this->outputFail('该广告不存在');
    	}
    	if(!$sort){
    		$this->outputFail('操作错误');
    	}
    	$infocn = $this->Advertisement_model->load($id);

    	if(isArray($infocn) && $infocn['relation_id'] !=''){
    		$this->Advertisement_model->update($id,array('sort'=>$sort));
    		$this->Advertisement_model->update($infocn['relation_id'],array('sort'=>$sort));
    	}elseif(isArray($infocn) && $infocn['relation_id'] ==''){
    		$this->Advertisement_model->update($id,array('sort'=>$sort));
    	}else{
    		$this->outputFail('操作失败');
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
		$articleInfo = $this->Advertisement_model->getByIds($ids);
		$articleInfoEn = $this->Advertisement_model->getByCnIds($ids);
		if(isArray($articleInfo)){
			foreach($articleInfo as $value){
				if(!$this->Advertisement_model->delete($value['id'])){
					$this->outputFail('删除中文广告失败');
				}
			}
		}

		if(isArray($articleInfoEn)){
			foreach($articleInfoEn as $value){
				if(!$this->Advertisement_model->delete($value['id'])){
					$this->outputFail('删除英文广告失败');
				}
			}
		}
       	$this->outputSuccess('删除成功');

	}
}

	