<?php if (!defined('BASEPATH')) die();

class Supervisors_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


   public function get_exams_data(){
      
      $termid = $this->db->select('*')->where('term.active',1)->get('term')->row()->id;
      
      $query=$this->db->select('*')
                  ->where('exam.term_id', $termid)
                  ->order_by('exam.date')
                  ->get('exam');

      if ($query->num_rows() > 0) 
      {
         foreach($query->result_array() as $row) 
         {
            $list[] = $row;
         }
         return $list;
      }
      else 
      {
         return false;
      }
   }

   public function update_supervisors_data($supervisor){

      $this->db->empty_table('exam_supervisor');
      // $this->load->library('firephp');
      // $this->firephp->info($supervisor);
      if (!empty($supervisor)){
         foreach ($supervisor as $key => $value) {
            foreach($value as $subkey => $subvalue){
               $data[]=array('exam_id'=>$key, 'employee_id'=>$subvalue);      
            }
         }
      }
      $this->db->insert_batch('exam_supervisor', $data);
   }


   public function get_supervisors_names_ids(){
      $query = $this->db
                     ->select(array('employee.id', 'employee.nickname'))
                     ->from('employee')
                     ->where('employee.active', 1)
                     ->where('employee.is_tutor', 1)
                     ->order_by('nickname', 'ASC')
                     ->get();

      if ($query->num_rows() > 0) 
      {
         foreach($query->result_array() as $row) 
         {
            $list[] = $row;
         }
         return $list;
      }
      else 
      {
         return false;
      }
   }


   public function get_supervisors($examid){
      $query = $this->db->select('employee_id')
                        ->where('exam_id', $examid)
                        ->get('exam_supervisor');

      if ($query->num_rows() > 0) 
      {                        
         $supervisors = $query->result_array();
         return $supervisors;
      }
      else 
      {
         return false;
      }      
   }

}