<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This Admin_template library was created specifically to minimize code redundancy
 * in determining modules that can be accessed by the logged in user
 *
 * @author Administrator
 */
class Template {

    protected $_ci;
    private $user_role;

    /**
     * User role is passed to the constructor since this class determines 
     * what is displayed/not displayed based on current role.
     * @param type $params an array containing logged_user role
     */
    public function __construct($params) {
        $this->_ci = &get_instance();
        $this->user_role = isset($params['role_id']) ? $params['role_id'] : null;
    }

    function display($template, $data = null) {
        $data['_content'] = $this->_ci->load->view('front/' . $template, $data, true);

        $this->_ci->load->view('front/template.php', $data);
    }

}
