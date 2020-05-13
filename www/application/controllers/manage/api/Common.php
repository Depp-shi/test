<?php 
    header("Access-Control-Allow-Origin: *");
	require_once( APPPATH . 'controllers/manage/api/BaseApiController.php');

class Common extends BaseApiController {

    public function __construct()
    {
        parent::__construct();
        // $this->checkApiLogin();

    }

    public function upload()
    {
        $filetype = $this->input->post('type');
        $config = array();
        $uploadSavePath = 'data/img/';
    	if($filetype == 'img'){
	    	$config['upload_path']      = 'data/img/';
			$config['allowed_types']    = 'gif|jpg|png';
			$config['max_size']     = 10000;
			$config['file_name'] = date('Ymdhis',time()).mt_rand(0,9999);
    	}elseif($filetype == 'video'){
            $uploadSavePath = 'data/video/';
	    	$config['upload_path']      = 'data/video/';
			$config['allowed_types']    = 'mp4';
			$config['max_size']     = 1000000;
			//$config['file_name'] = date('Ymd',time()).mt_rand(0,9999);    		
    	}elseif($filetype == 'file'){
            $uploadSavePath = '/data/file/';
            $config['upload_path']      = '.'. $uploadSavePath;
			$config['allowed_types']    = '*';
			$config['max_size']     = 100000;
			//$config['file_name'] = date('Ymd',time()).mt_rand(0,9999);      		
    	}

        $this->load->library('upload', $config);
        $file = $this->input->post('file');
        if (!$this->upload->do_upload($file)){
            print_r($this->upload->display_errors());exit;
            $error = $this->upload->display_errors();
        	$this->outputFail('上传失败');
        }else{
            $data = $this->upload->data();
            $info = array();
            $info['origin_id'] = 0;
            $info['origin_data_type'] = 0;
            $info['url'] =  sprintf('%s%s', $uploadSavePath, $data['file_name']);
            $info['title'] = '';
            if($filetype == 'img'){
            	$info['type'] = EnumRes::ATTACHMENT_TYPE_IMG;
            }elseif($filetype == 'video'){
            	$info['type'] = EnumRes::ATTACHMENT_TYPE_FILE;
            }else{
            	$info['type'] = EnumRes::ATTACHMENT_TYPE_FILE;
            }
            $info['size'] = $data['file_size'];
            $info['upload_time'] = time();
            $this->load->model('Attachment_model');
            $attachmentId = $this->Attachment_model->create($info);
            if($attachmentId){
            	$item =array();
            	$item['file']['filename'] = $data['file_name'];
            	$item['file']['filepath'] = sprintf('%s%s', $uploadSavePath, $data['file_name']);
            	$item['file']['fullpath'] = base_url($item['file']['filepath']);
            	$item['file']['fileid'] = $attachmentId;
            	$this->outputSuccess('ok',$item);
            }

        }
    }
}



?>

