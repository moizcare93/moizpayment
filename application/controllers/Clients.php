<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clients extends Authenticated_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Client_model');
    }

    public function index()
    {
        $this->render('clients/index', array(
            'page_title' => 'Klien',
            'clients' => $this->Client_model->all(),
        ));
    }

    public function create()
    {
        $this->save();
    }

    public function edit($id)
    {
        $this->save($id);
    }

    private function save($id = NULL)
    {
        $client = $id ? $this->Client_model->find($id) : NULL;
        if ($id && !$client) {
            show_404();
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('name', 'Nama Klien', 'required|trim');
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');

            if ($this->form_validation->run()) {
                $payload = array(
                    'name' => $this->input->post('name', TRUE),
                    'company_name' => $this->input->post('company_name', TRUE),
                    'email' => $this->input->post('email', TRUE),
                    'phone' => $this->input->post('phone', TRUE),
                    'address' => $this->input->post('address', TRUE),
                    'city' => $this->input->post('city', TRUE),
                    'notes' => $this->input->post('notes', TRUE),
                );
                $this->Client_model->save($payload, $id);
                $this->session->set_flashdata('success', 'Data klien berhasil disimpan.');
                redirect('clients');
            }
        }

        $this->render('clients/form', array(
            'page_title' => $id ? 'Edit Klien' : 'Tambah Klien',
            'client' => $client,
        ));
    }

    public function delete($id)
    {
        $this->Client_model->delete($id);
        $this->session->set_flashdata('success', 'Data klien dihapus.');
        redirect('clients');
    }
}
