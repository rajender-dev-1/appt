<?php
if (!defined('BASEPATH')) die('the script cannot be run');

class Periodic_Model extends CI_Model
{
    public function index($atomic_number)
    {
        $data = $this->db->get_where('elements', ['id'=>$atomic_number])->row_array();
        print_r($data);
    }
}
