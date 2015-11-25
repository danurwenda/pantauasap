<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Device extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('device_model');
        $this->load->model('record_model');
    }

    public function get_points($device, $from, $to) {
        $from = $from / 1000;
        $to = $to / 1000;
//        $records = $this->record_model->getRecords($device, 1447214580, 1447261380);
        $records = $this->record_model->getRecords($device, $from, $to);
        foreach($records as $r){
            $r->temperature = ($r->temperature-4000)/100;
        }
        echo json_encode($records);
    }

    /**
     *
     * @param type $from    Epoch millisecond
     * @param type $to      Epoch millisecond
     */
    public function get_average($from, $to) {
        $from = $from / 1000;
        $to = $to / 1000;
        //hitung dulu
        $this->db
                ->from('record')
                ->where("recorded_timestamp>to_timestamp($from)", null, false)
                ->where("recorded_timestamp<to_timestamp($to)", null, false);
        $num = $this->db->count_all_results();
        $this->db->select_avg('iaq')
                ->select_avg('co2')
                ->select_avg('pm10')
                ->select_avg('pm25')
                ->select_avg('humidity')
                ->select_avg('temperature')
                ->select_avg('temperature')
                ->select_avg('tvoc')
                ->where("recorded_timestamp>to_timestamp($from)", null, false)
                ->where("recorded_timestamp<to_timestamp($to)", null, false)
        ;
        $q = $this->db->get('record')->row();
        echo json_encode(array(
            'num' => $num,
            'iaq' => round($q->iaq, 2),
            'tvoc' => round($q->tvoc, 2),
            'co2' => round($q->co2, 2),
            'hum' => round($q->humidity, 2),
            'temp' => round(0.01 * ($q->temperature - 4000), 2),
            'pm25' => round($q->pm25, 2),
            'pm10' => round($q->pm10, 2)
        ));
    }

    /**
     * Return json in format below (last 24 data)
     * {
      "xData": [timestamp1, timestamp2, ...],
      "datasets": [{
      "name": "PM10",
      "data": [...],
      "unit": "μg/m3",
      "type": "line",
      "valueDecimals": 0
      }, {
      "name": "PM25",
      "data": [...],
      "unit": "μg/m3",
      "type": "line",
      "valueDecimals": 0
      }, {
      "name": "CO2",
      "data": [...],
      "unit": "ppm",
      "type": "line",
      "valueDecimals": 0
      }, {
      "name": "Temperature",
      "data": [...],
      "unit": "℃",
      "type": "line",
      "valueDecimals": 2
      }, {
      "name": "IAQ",
      "data": [...],
      "unit": "index",
      "type": "line",
      "valueDecimals": 0
      }, {
      "name": "Humidity",
      "data": [...],
      "unit": "%",
      "type": "line",
      "valueDecimals": 2
      }]
      }
     * @param type $sensor_id
     */
    public function get_json($sensor_id) {
        //preparing array for each dataset
        $tvoc = array(
            'name' => 'TVOC',
            "unit" => "ppb",
            "valueDecimals" => 0,
            "data" => array());
        $iaq = array(
            "data" => array(),
            'name' => 'IAQ',
            "unit" => "index",
            "valueDecimals" => 0);
        $pm10 = array(
            "data" => array(),
            'name' => 'PM10',
            "unit" => "μg/m3",
            "valueDecimals" => 0);
        $pm25 = array(
            "data" => array(),
            'name' => 'PM2.5',
            "unit" => "μg/m3",
            "valueDecimals" => 0);
        $co2 = array(
            "data" => array(),
            'name' => 'CO2',
            "unit" => "ppm",
            "valueDecimals" => 0);
        $temp = array(
            "data" => array(),
            'name' => 'Temperature',
            "unit" => "℃",
            "valueDecimals" => 2);
        $hum = array(
            "data" => array(),
            'name' => 'Humidity',
            "unit" => "%",
            "valueDecimals" => 0);
        $xData = array();
        $last_data = $this->record_model->get_recent_data($sensor_id);
        foreach ($last_data as $row) {
            //treat all data as number
            $xData[] = 1000 * ($row->ts + 7 * 3600); //convert GMT to Jakarta
            $temp['data'][] = ( $row->temperature - 4000) * 0.01;
            $co2['data'][] = 0 + $row->co2;
            $pm10['data'][] = 0 + $row->pm10;
            $pm25['data'][] = 0 + $row->pm25;
            $hum['data'][] = 0 + $row->humidity;
            $iaq['data'][] = 0 + $row->iaq;
            $tvoc['data'][] = 0 + $row->tvoc;
        }
        $ret = array('datasets' => array($iaq, $co2, $pm10, $pm25, $tvoc, $temp, $hum), 'xData' => $xData);
        echo json_encode($ret);
    }

    public function get_data($sensor_id) {
        // The x value is the current JavaScript time, which is the Unix time multiplied
// by 1000.
        $x = time() * 1000;
// The y value is a random number
        $y = rand(0, 100);

// Create a PHP array and echo it as JSON
        $ret = array($x, $y);
        echo json_encode($ret);
    }

    public function get_chart($sensor) {
        $this->load->model('record_model');
        $sensor_data = $this->record_model->get_all($sensor);
        //data dipakai untuk chart
//        $this->load->library('chart');
        $data['events'] = array();
//        $data['charts']['TL'] = $this->chart->generate($c, array('render-to' => 'TL', 'expand-button' => true));
//        $data['charts']['TR'] = $this->chart->generate($c, array('render-to' => 'TR', 'expand-button' => true));
//        $data['charts']['BL'] = $this->chart->generate($c, array('render-to' => 'BL', 'expand-button' => true));
//        $data['charts']['BR'] = $this->chart->generate($c, array('render-to' => 'BR', 'expand-button' => true));
        $this->load->view('4panel_view', $data);
    }

    public function get_sensor_id_post() {
        $mac = $this->input->post('mac');
        $sensor = $this->device_model->get_by_mac($mac);
        if ($sensor)
            echo $sensor->sensor_id;
        else
            echo '-1';
    }

    public function get_sensor_id($mac) {
        $sensor = $this->device_model->get_by_mac($mac);
        if ($sensor)
            echo $sensor->sensor_id;
        else
            echo '-1';
    }

    /**
     * POST
     */
    public function register() {
        $mac = $this->input->post('mac');
        $sensor = $this->device_model->get_by_mac($mac);
        if ($sensor)
            echo '-1';
        else {
            $this->device_model->register(
                    $mac, $this->input->post('brand'), $this->input->post('type')
            );
            echo $this->get_sensor_id($mac);
        }
    }

}
