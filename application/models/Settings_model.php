<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_model extends CI_Model
{
    public function get_settings()
    {
        $row = $this->db->get('mp_settings')->row_array();
        return $row ?: array();
    }

    public function save($data)
    {
        $existing = $this->db->get('mp_settings')->row_array();
        if ($existing) {
            $this->db->where('id', $existing['id'])->update('mp_settings', $data);
            return $existing['id'];
        }

        $this->db->insert('mp_settings', $data);
        return $this->db->insert_id();
    }
}
