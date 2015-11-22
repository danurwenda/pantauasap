<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Peta extends CI_Controller{
    
    public function index() {
        $this->load->view('map_test');
    }
    
    public function vari() {
        $this->load->view('map_var');
    }
    
    public function vek() {
        $this->load->view('map_vek');
    }
}

