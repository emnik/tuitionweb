<?php

class Supervisors_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


   public function get_employees() {
      $query=$this
         ->db
         ->select(array('id', 'CONCAT_WS(" ",`surname`,`name`) as employee'))
         ->where('active','1')
         ->get('employee');

      if ($query->num_rows() > 0) 
      {
        foreach ($query->result_array() as $row) {
           	$data[$row['id']]=$row['employee'];
        }
         return $data;
      }
      else 
      {
         return false;
      }

   }


   public function get_exam_dates()
   {
      $query = $this->db->distinct()
				->select('date')
				->from('exam_schedule')
				->where('exam_schedule.startschyear', $this->session->userdata('startsch'))
				->order_by('date')
				->get();
      
      if ($query->num_rows()>0)
      {
         foreach ($query->result_array() as $row) {
            $data[]=$row['date'];
         }
         return $data;
      }
      return false;
   }

   public function get_supervisors($dates)
   {
      $query = $this->db->select('*')
      					->from('exam_supervisor')
      					->where_in('date', $dates)
      					->get();
      
      if ($query->num_rows()>0)
      {
         foreach ($query->result_array() as $row) {
            $data[$row['date']][]=$row['employee_id'];
         }
         return $data;
      }
      return false;
   }
  

  public function insert_supervisors($insertdata, $dates)
  {
  	$query = $this->db->where_in('date',$dates)->delete('exam_supervisor');
	$query = $this->db->insert_batch('exam_supervisor', $insertdata);
  }


}
