<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model
{
    public function login($identity, $password)
    {
        $user = $this->db
            ->select('u.*, r.name AS role_name')
            ->from('mp_users u')
            ->join('mp_roles r', 'r.id = u.role_id', 'left')
            ->group_start()
            ->where('u.email', $identity)
            ->or_where('u.username', $identity)
            ->group_end()
            ->where('u.is_active', 1)
            ->get()
            ->row_array();

        if (!$user || !password_verify($password, $user['password'])) {
            return FALSE;
        }

        unset($user['password']);
        return $user;
    }
}
