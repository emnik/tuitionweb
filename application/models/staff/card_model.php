<?php if (!defined('BASEPATH')) die();

class Card_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }

   public function get_employee_data($id) {
   	$query=$this
   		->db
         ->select('*')
         ->from('employee')
         ->where('employee.id',$id)
         ->limit(1)
   		->get();

   	if ($query->num_rows() > 0) 
		{
   		 
         return $query->row_array(); 
		}
		else 
		{
			return false;
		}

   }

   public function get_lessons()
   {
      $query = $this->db->select('*')
               ->from('catalog_lesson')
               ->get();
      if($query->num_rows()>0)
      {
         foreach ($query->result() as $row) {
            $data[$row->id] = $row->title;
         }
         return $data;
      }
      return false;
   }

   function get_tutor_lessons($id)
   {
      $query = $this->db->select('id, cataloglesson_id')
               ->from('lesson_tutor')
               ->where('employee_id', $id)
               ->get();
      
      if($query->num_rows()>0)
      {
         foreach ($query->result() as $row) {
            $data[$row->id] = $row->cataloglesson_id;
         }
         return $data;
      }
      return false;
   }      
   
   public function update_lessons_data($lessons_data, $selectedlessons, $id)
   {
      if(!empty($selectedlessons))
      {
         foreach ($selectedlessons as $key => $value) {
                  if(!in_array($value, $lessons_data))
                  {
                     $this->db->where('id', $key)->delete('lesson_tutor');
                  }
         }         
      }
      if(!empty($lessons_data))
      {
         foreach ($lessons_data as $key => $value) {
            if(!empty($selectedlessons))
            {
               if(!in_array($value, $selectedlessons))
               {
                  $newlesson[] = array('cataloglesson_id'=>$value, 'employee_id'=>$id);
               }   
            }
            else
            {
               $newlesson[] = array('cataloglesson_id'=>$value, 'employee_id'=>$id);
               
            }   
         }
      }
      if(!empty($newlesson))
      {
         $this->db->insert_batch('lesson_tutor', $newlesson);   
      }
      
   }

   public function update_employee_data($employee_data, $id){

      //replacing empty array values with NULL
      foreach ($employee_data as $i => $value) {
         if ($value === "") $employee_data[$i] = null;
      };

      $this->db->where('employee.id',$id);
      $this->db->update('employee', $employee_data);
   }


}