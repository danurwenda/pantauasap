<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Data_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        //$this->load->model('transx_model');
    }

    public function getAllSensor() {
        $query = $this->db->query('select * from sensor');
        $datatemp = array();
        foreach ($query->result_array() as $row) {
            $datatemp[] = array(
                'id' => (int) $row['sensor_id'],
                'brand' => $row['brand'],
                'mac' => $row['mac']
            );
        }
        return array('sensor' => $datatemp);
    }

    public function getAllSensorID() {
        $query = $this->db->query('select * from sensor');
        $datatemp = array();
        foreach ($query->result_array() as $row) {
            $datatemp[] = array(
//                'id'=>(int)$row['sensor_id']
                (int) $row['sensor_id']
            );
        }
        //return array('sensorid'=>$datatemp);
        return $datatemp;
    }

    public function getLatestByID($sensorid) {
        $query = $this->db->query('select sensor_id,iaq,co2 as co,pm25,pm10,temperature,humidity,location[0] as locy,location[1] as locx,location,recorded_timestamp as recorded from record WHERE  sensor_id=' . $sensorid . ' ORDER BY recorded_timestamp DESC limit 1');
        $datatemp = array();
        foreach ($query->result_array() as $row) {
            $datatemp = array(
                'sensorid' => (int) $row['sensor_id'],
                'iaq' => (int) $row['iaq'],
                'co2' => (int) $row['co'],
                'pm25' => (int) $row['pm25'],
                'pm10' => (int) $row['pm10'],
                'temperature' => (int) $row['temperature'],
                'humidity' => (int) $row['humidity'],
                'locX' => (double) $row['locx'],
                'locY' => (double) $row['locy'],
                'lotlan' => $row['location'],
                'recorded' => $row['recorded']
            );
        }
        //return array('latest'=>$datatemp);
        return $datatemp;
    }

    public function getLatestAll() {
        $query = $this->db->query('select * from sensor');
        $datatemp = array();
        foreach ($query->result_array() as $row) {
            $itemp = (int) $row['sensor_id'];
            $latest = $this->getLatestByID($itemp);
            $datatemp[] = array(
                'id' => $itemp,
                'co2' => (int) $latest['co2'],
                'pm25' => (int) $latest['pm25']
            );
        }
        return array('latestAll' => $datatemp);
        //return $datatemp;
    }

    public function getLatestGeoJson() {
        $xdef = 106.82197;
        $ydef = -6.17498;
        $query = $this->db->query('select * from sensor');
        $datatemp = array();
        $texttemp = '';
        foreach ($query->result_array() as $row) {
            $itemp = (int) $row['sensor_id'];
            $latest = $this->getLatestByID($itemp);
            $cooX = (double) $latest['locX'];
            $cooY = (double) $latest['locY'];
            if ($cooX < 0) {
                $cooX = $xdef;
                $cooY = $ydef;
            }

            $texttemp.='{"type":"Feature","properties":{"id":' . $itemp . ', "iaq": ' . $latest['iaq'] . ',"pm10": ' . $latest['pm10'] . ',"recorded": "' . $latest['recorded'] . '"},"geometry":{"type":"Point","coordinates":[' . $cooX . ',' . $cooY . ']}},';
        }

        substr($texttemp, 0, -1);
        return $texttemp;
    }

    public function getHistoryID($sensorid) {
        $xdef = 106.8219;
        $ydef = -6.1749;
//$locX = $xdef;
//        $locY = $ydef;
        $texttemp = '';
        $query = $this->db->query('select distinct location[1] as locx, location[0] as locy,iaq, pm10, recorded_timestamp as recorded '
                . 'from record WHERE  sensor_id=' . $sensorid . ' ORDER BY recorded DESC limit 200');
        foreach ($query->result_array() as $row) {
            $locX = (double) $row['locx'];
            $locY = (double) $row['locy'];
            if ($locX < 0) {
                $locX = $xdef;
                $locY = $ydef;
            } else {
                $xdef = $locX;
                $ydef = $locY;
            }
            $texttemp.='{"type":"Feature","properties":{"iaq": ' . $row['iaq'] . ',"pm10": ' . $row['pm10'] . ',"recorded": "' . $row['recorded'] . '"},"geometry":{"type":"Point","coordinates":[' . $locX . ',' . $locY . ']}},';
        }
        substr($texttemp, 0, -1);
        return $texttemp;
    }

    public function get_last_co() {
        $query = $this->db->query('select * from record');
        foreach ($query->result_array() as $row) {
            $last = (int) $row['iaq'];
        }
        return $last;
        //return number_format($last);
    }

}
