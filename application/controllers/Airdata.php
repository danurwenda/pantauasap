<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Airdata extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('record_model');
        $this->load->model('device_model');
    }

    public function index() {
        $this->load->view('welcome_message');
    }

    public function send_data_post() {
        $sensor_id = $this->input->post('sensor_id');
        //cek dulu sensor_id ini registered apa engga
        if ($this->device_model->exist($sensor_id)) {
            $recorded_time = $this->input->post(recorded_time);
            //check apakah sudah ada
            if (!$this->record_model->exist($sensor_id, $recorded_time)) {
                //insert
                $this->record_model->insert(
                        $sensor_id, $recorded_time, $this->input->post('location'), $this->input->post('battery'), $this->input->post('is_dc'), $this->input->post('co2'), $this->input->post('pm25'), $this->input->post('pm10'), $this->input->post('temperature'), $this->input->post('humidity'), $this->input->post('tvoc'), $this->input->post('iaq')
                );
                echo '0';
            }
        } else {
            //belum ada, suruh register dulu
            echo '-1';
        }
    }

    public function send_data(
    $sensor_id, $recorded_time, $lat, $lon, $battery, $is_dc, $co2, $pm25, $pm10, $temperature, $humidity, $tvoc, $iaq
    ) {
        //$recorded_time ini formatnya 1999-01-08-04-05-06, harus diconvert jadi
        //1999-01-08 04:05:06
        //kalau yang format baru, YYYY-MM-DD-HH-MM-SS-angka
        $recorded_time = explode("-", $recorded_time);
        $timezone = isset($recorded_time[6]) ? $recorded_time[6] - 12 : 7;
        if ($timezone > 0) {
            $timezone = '+' . $timezone;
        } else if ($timezone == 0) {
            $timezone = '';
        }

        $recorded_time = $recorded_time[0] . "-" . $recorded_time[1] . "-" . $recorded_time[2]
                . " " .
                $recorded_time[3] . ":" . $recorded_time[4] . ":" . $recorded_time[5] . $timezone;

        //cek dulu sensor_id ini registered apa engga
        if ($this->device_model->exist($sensor_id)) {
            //check apakah sudah ada
            if (!$this->record_model->exist($sensor_id, $recorded_time)) {
                //insert
                if ($this->record_model->insert(
                                $sensor_id, $recorded_time, "($lat,$lon)", $battery, $is_dc, $co2, $pm25, $pm10, $temperature, $humidity, $tvoc, $iaq
                        )) {
                    echo '1';
                } else {
                    echo '0';
                }
            } else {
                echo '1';
            }
        } else {
            //belum ada, suruh register dulu
            echo '-1';
        }
    }

}
