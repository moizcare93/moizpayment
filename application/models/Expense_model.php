<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expense_model extends CI_Model
{
    public function all()
    {
        return $this->db->order_by('expense_date', 'DESC')->get('mp_expenses')->result_array();
    }

    public function create($data)
    {
        $this->db->insert('mp_expenses', $data);
        return $this->db->insert_id();
    }

    public function delete($id)
    {
        return $this->db->delete('mp_expenses', array('id' => $id));
    }
}
