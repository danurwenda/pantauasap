<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('record_model');
        $this->load->model('device_model');
        $this->load->library('template', array());
    }

    public function index() {
        $this->template->display('viz', array('page' => 1));
    }

    public function about() {
        $this->template->display('about', array('page' => 3));
    }

    public function sensor_chart($sensor_id) {
        $this->template->display('demo', array('sensor_id' => $sensor_id, 'page' => 1));
    }

    public function history($id = 1) {
        $data['devices']=$this->device_model->get_all();
        $data['page']=2;
        $this->template->display('history', $data);
    }

}
