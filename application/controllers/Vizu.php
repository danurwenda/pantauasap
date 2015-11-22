<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Vizu extends CI_Controller {

    public function showAllSensor() {
        $this->load->model('data_model');
        $hasil = $this->data_model->getAllSensor();
        echo json_encode($hasil);
    }

    public function showAllSensorID() {
        $this->load->model('data_model');
        $hasil = $this->data_model->getAllSensorID();
        echo json_encode($hasil);
    }

    public function showLatestID($sid) {
        $this->load->model('data_model');
        $hasil = $this->data_model->getLatestByID($sid);
        echo json_encode($hasil);
    }

    public function showLatestAll() {
        $this->load->model('data_model');
        $hasil = $this->data_model->getLatestAll();
        echo json_encode($hasil);
    }

    public function showLatestGeoJson() {
        $this->load->model('data_model');
        $hasil = $this->data_model->getLatestGeoJson();
        $textA = 'eqfeed_callback({"type":"FeatureCollection","features":[';
        $textB = ']});';
        $hasilF = $textA . $hasil . $textB;

        print_r($hasilF);
    }

    public function showHistoryID($sid) {
        $this->load->model('data_model');
        $hasil = $this->data_model->getHistoryID($sid);
        $textA = 'eqfeed_callback({"type":"FeatureCollection","features":[';
        $textB = ']});';
        $hasilF = $textA . $hasil . $textB;

        print_r($hasilF);
    }

//    public function showLatestA() {
//        $this->load->model('data_model');
//        $hasil = $this->data_model->getLatestA();
//        echo json_encode($hasil);
//    }
}
