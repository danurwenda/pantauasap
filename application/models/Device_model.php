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
class Device_model extends CI_Model {

    //put your code here
    public function __construct() {
        parent::__construct();
    }
    
    public function get_all(){
        return $this->db->get('sensor')->result();
    }

    public function get_by_mac($mac) {
        $q = $this->db->get_where('sensor', array('mac' => $mac));
        if ($q->num_rows() > 0) {
            return $q->row();
        } else {
            return null;
        }
    }

    public function exist($sensor) {
        return $this->db->get_where('sensor', array('sensor_id' => $sensor))->num_rows() > 0;
    }

    public function insert(
    $brand, $type, $mac
    ) {
        $this->db->insert(
                'sensor', array(
            'brand' => $brand,
            'type' => $co2,
            'mac' => $mac
                )
        );
    }

}
