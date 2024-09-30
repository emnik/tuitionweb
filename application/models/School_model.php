<?php

class School_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


public function get_school_data(){

    $query = $this->db->select('*')->get('school');

    if ($query->num_rows() > 0) 
		{
			foreach($query->result_array() as $row) 
			{
				$school[] = $row;
            }


            return $school;
		}
		else 
		{
			return false;
		}
    }

    public function update_school_data($schooldata){
        foreach($schooldata as $key => $value){
            if ($value === "") $schooldata[$key] = null;
        }

        if ($schooldata['id']===null){ //new record so insert!
            $this->db->insert('school', $schooldata);
        }
        else {
            $this->db->where('school.id', $schooldata['id']);
            $this->db->update('school', $schooldata);
        }
    }

}
