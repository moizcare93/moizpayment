<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends Authenticated_Controller
{
    public function index()
    {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('company_name', 'Nama Perusahaan', 'required|trim');
            $this->form_validation->set_rules('company_email', 'Email', 'trim|valid_email');

            if ($this->form_validation->run()) {
                $settings = $this->data['app_settings'];
                $logo = $settings['company_logo'] ?? '';

                if (!empty($_FILES['company_logo']['name'])) {
                    $config = array(
                        'upload_path' => FCPATH . 'assets/img/uploads/',
                        'allowed_types' => 'jpg|jpeg|png',
                        'max_size' => 2048,
                        'file_name' => 'company-logo-' . time(),
                    );
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload('company_logo')) {
                        $logo = 'assets/img/uploads/' . $this->upload->data('file_name');
                    }
                }

                $payload = array(
                    'company_name' => $this->input->post('company_name', TRUE),
                    'company_tagline' => $this->input->post('company_tagline', TRUE),
                    'company_address' => $this->input->post('company_address', TRUE),
                    'company_city' => $this->input->post('company_city', TRUE),
                    'company_province' => $this->input->post('company_province', TRUE),
                    'company_postal_code' => $this->input->post('company_postal_code', TRUE),
                    'company_phone' => $this->input->post('company_phone', TRUE),
                    'company_email' => $this->input->post('company_email', TRUE),
                    'company_website' => $this->input->post('company_website', TRUE),
                    'company_npwp' => $this->input->post('company_npwp', TRUE),
                    'company_logo' => $logo,
                    'invoice_prefix' => $this->input->post('invoice_prefix', TRUE),
                    'quotation_prefix' => $this->input->post('quotation_prefix', TRUE),
                    'default_payment_terms' => (int) $this->input->post('default_payment_terms'),
                    'bank_name' => $this->input->post('bank_name', TRUE),
                    'bank_account_number' => $this->input->post('bank_account_number', TRUE),
                    'bank_account_name' => $this->input->post('bank_account_name', TRUE),
                    'smtp_host' => $this->input->post('smtp_host', TRUE),
                    'smtp_port' => $this->input->post('smtp_port', TRUE),
                    'smtp_username' => $this->input->post('smtp_username', TRUE),
                    'smtp_password' => $this->input->post('smtp_password', TRUE),
                    'smtp_encryption' => $this->input->post('smtp_encryption', TRUE),
                    'wa_api_url' => $this->input->post('wa_api_url', TRUE),
                    'wa_api_key' => $this->input->post('wa_api_key', TRUE),
                    'wa_sender_number' => $this->input->post('wa_sender_number', TRUE),
                    'currency_symbol' => $this->input->post('currency_symbol', TRUE),
                    'currency_code' => $this->input->post('currency_code', TRUE),
                );

                $this->Settings_model->save($payload);
                $this->session->set_flashdata('success', 'Pengaturan berhasil diperbarui.');
                redirect('settings');
            }
        }

        $this->render('settings/index', array('page_title' => 'Pengaturan'));
    }
}
