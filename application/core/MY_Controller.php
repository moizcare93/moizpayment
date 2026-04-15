<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    protected $data = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Settings_model');
        $this->data['app_settings'] = $this->Settings_model->get_settings();
        $this->data['current_user'] = $this->session->userdata('user');
    }

    protected function render($view, $data = array())
    {
        $payload = array_merge($this->data, $data);
        $this->load->view('templates/header', $payload);
        $this->load->view('templates/sidebar', $payload);
        $this->load->view($view, $payload);
        $this->load->view('templates/footer', $payload);
    }
}

class Authenticated_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }
    }
}
