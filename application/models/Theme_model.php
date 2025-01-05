<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Theme_model extends CI_Model
{
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    public function get_user_theme($user_id) {
        $this->db->select('themes.*');
        $this->db->from('user_preferences');
        $this->db->join('themes', 'themes.id = user_preferences.theme_id');
        $this->db->where('user_preferences.user_id', $user_id);
        $query = $this->db->get();

        return $query->row_array();
    }

    public function update_user_theme($user_id, $theme_id) {
        $this->db->where('user_id', $user_id);
        $this->db->update('user_preferences', ['theme_id' => $theme_id]);
    
        return $this->db->affected_rows() > 0;
    }
    
    public function get_all_themes() {
        $this->db->select('id, name');
        $query = $this->db->get('themes');
        
        return $query->result_array();  // Return as an associative array
    }


}