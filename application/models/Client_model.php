<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Client_model extends CI_Model
{
    public function all()
    {
        return $this->db->order_by('name', 'ASC')->get('mp_clients')->result_array();
    }

    public function find($id)
    {
        return $this->db->get_where('mp_clients', array('id' => $id))->row_array();
    }

    public function save($data, $id = NULL)
    {
        if ($id) {
            $this->db->where('id', $id)->update('mp_clients', $data);
            return $id;
        }

        $this->db->insert('mp_clients', $data);
        return $this->db->insert_id();
    }

    public function delete($id)
    {
        return $this->db->delete('mp_clients', array('id' => $id));
    }
}
