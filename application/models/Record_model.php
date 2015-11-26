<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Record_model
 *
 * @author Slurp
 */
class Record_model extends CI_Model {

    //put your code here
    public function __construct() {
        parent::__construct();
    }

    public function getRecords($sensor_id, $from, $to) {
        if ($sensor_id != 0) {
            $this->db->where('sensor_id', $sensor_id);
        }
        return $this->db->select('location[0] as lat, location[1] as lon, iaq, pm10, tvoc, co2, pm25, temperature, humidity, recorded_timestamp, sensor_id', false)
                        ->where("recorded_timestamp>to_timestamp($from)", null, false)
                        ->where("recorded_timestamp<to_timestamp($to)", null, false)
                        ->get('record')
                        ->result();
    }
    
    /**
     * 
     * @param type $sensor_id Kalau 0 berarti semua
     */
    public function getLastRecords($sensor_id){
        if ($sensor_id != 0) {
            $this->db->where('sensor_id', $sensor_id);
        }
        return $this->db->select('location[0] as lat, location[1] as lon, iaq, pm10, tvoc, co2, pm25, temperature, humidity, recorded_timestamp, sensor_id', false)
                        ->where('location[0] <>',-1,false)
                        ->where('location[1] <>',-1,false)
                        ->order_by("recorded_timestamp","desc")
                        ->limit(1)
                        ->get('record')
                        ->result();
    }

    public function exist($sensor, $rectime) {
        return $this->db->get_where('record', array('sensor_id' => $sensor, 'recorded_timestamp' => $rectime))->num_rows() > 0;
    }

    public function get_all($sensor) {
        return $this->db->get_where('record', array('sensor_id' => $sensor))->result();
    }

    /**
     * Last 20 entry
     * @param type $sensor
     */
    public function get_recent_data($sensor) {
        return array_reverse($this->db
                        ->select("extract(epoch from recorded_timestamp at time zone 'utc') as ts, co2, pm25, pm10, temperature, humidity, tvoc, iaq", false)
                        ->where('sensor_id', $sensor)
                        ->order_by('ts', 'desc')
                        ->limit(24)
                        ->get('record')
                        ->result());
    }

    public function insert(
    $sensor_id, //sensor id, unik setiap device
            $recorded_time, //terkadang banyak pencatatan di batch dulu sebelum di submit ke server
            $location, //point (lat, long)
            $battery, //persentase batery saat pencatatan
            $is_dc, //apakah dicolok saat pencatatan
            $co2, //kadar co2
            $pm25, //kadar pm2.5
            $pm10, //kadar pm10
            $temperature, //temperature
            $humidity, //kelembapan
            $tvoc, //index tvoc
            $iaq            //index indoor air quality
    ) {
        return
                $this->db->insert(
                        'record', array(
                    'recorded_timestamp' => $recorded_time,
                    'co2' => $co2,
                    'pm25' => $pm25,
                    'pm10' => $pm10,
                    'temperature' => $temperature,
                    'humidity' => $humidity,
                    'tvoc' => $tvoc,
                    'iaq' => $iaq,
                    'sensor_id' => $sensor_id,
                    'location' => $location,
                    'battery' => $battery,
                    'is_dc' => $is_dc
                        )
        );
    }

}
