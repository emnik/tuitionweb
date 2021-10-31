<?php

class Exam_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


   public function get_exams_data() {

    $query=$this->db
         ->select(array('exam.id', 'exam.name', 'exam.date', 'exam.start', 'exam.end', 'exam.term_id'))
         ->from('exam')
         ->join('term', 'exam.term_id = term.id')
         ->where('term.active', 1)
         ->order_by('date', 'asc')
         ->order_by('start', 'asc')
         ->get();

        if ($query->num_rows() > 0) 
        {
            foreach($query->result_array() as $row) 
            {
                $exam[] = $row;
            }
            return $exam;
        }
        else 
        {
            return false;
        }
    }

    public function newexam()
    {
        //get active term
        $termid = $this->db->select('term.id')->where('term.active',1)->get('term')->row()->id;
                   
        //insert new record in exam table
        $data = array('id' => 'null', 'term_id'=> $termid );
        $this->db->insert('exam', $data);
        $examid = $this->db->insert_id();
        return $examid;
    }
    
    
    public function delexam($id)
    {      
        $this->db->delete('exam', array('id' => $id)); 
        //deleting an exam will delete the corresponding entries in exam_lesson, exam_participant and exam_supervisor tables!
    }
    
    
    public function cancelexam($id)
    {      
        $query = $this->db->select('name')
                ->where('id',$id)
                ->get('exam');
        
        if ($query->num_rows() > 0)
        {
            $row = $query->row();
    
            if (is_null($row->name))
            {
                $this->db->delete('exam', array('id'=>$id));
                return true;
            }
            else
            {
                return false;
            };
    
        };
    }

   }


