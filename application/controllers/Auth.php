<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model');
    }

    public function login()
    {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('identity', 'Email / Username', 'required|trim');
            $this->form_validation->set_rules('password', 'Password', 'required');

            if ($this->form_validation->run()) {
                $user = $this->Auth_model->login($this->input->post('identity', TRUE), $this->input->post('password', TRUE));
                if ($user) {
                    $this->session->set_userdata(array(
                        'logged_in' => TRUE,
                        'user' => $user,
                    ));
                    redirect('dashboard');
                }

                $this->session->set_flashdata('error', 'Login gagal. Periksa email/username dan password.');
            }
        }

        $this->load->view('auth/login', $this->data);
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('login');
    }
}
