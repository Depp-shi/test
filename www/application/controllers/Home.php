<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once 'BaseController.php';

class Home extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->_logger = Logger::getLogger('HomeController');
    }

    public function index()
    {
        $data            = $this->getCommonData();
        $data['version'] = GeneralRes::MANAGE_ASSETS_CDN_VERSION;
        $data['token'] = get_cookie('manager_user_token');
        $this->load->view('home', $data);
    }
}
