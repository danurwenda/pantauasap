<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Peta extends CI_Controller {

    public function index() {
        $this->load->view('map_test');
    }

    public function google() {
        $this->load->view('circle');
    }

    public function viz() {
        $this->load->view('viz');
    }

    public function history($id) {
        $data['id'] = $id;
        $this->load->view('history', $data);
    }

}
