<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Demo extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('record_model');
        $this->load->model('device_model');
    }

    public function chart($sensor_id) {
        $this->load->view('demo',array('sensor_id'=>$sensor_id));
    }

    
}
