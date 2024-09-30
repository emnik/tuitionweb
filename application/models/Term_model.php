<?php

class Term_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


    public function get_term_data($id=null){
            $this->db->select('*');
            if(!is_null($id)){
                $this->db->where('term.id', $id);
            };
            $this->db->order_by('term.id', 'desc');
            $query=$this->db->get('term');
    
            if($query->num_rows()>0) {
                return $query->result_array();
            } 
            else 
            {
                return false;
            }
    }

    public function update_term_data($data, $id){
        foreach($data as $key=>$value){
            if(empty($value)){
                $data[$key]=null;
            }
        }
        // $this->load->library('firephp');
        // $this->firephp->info($data);
        $this->db->where('id', $id);
        $this->db->update('term', $data);
    }


    public function newterm()
    {
       //insert new record in term table
       $data = array('id' => 'null');
       $this->db->insert('term', $data);
       $sectionid = $this->db->insert_id();
       return $sectionid;
    }
 
 
    public function delterm($id)
    {      
       $this->db->delete('term', array('id' => $id)); 
    }
 
 
    public function cancelreg($id)
    {      
       $query = $this->db->select('name')
                ->where('id',$id)
                ->get('term');
       
       if ($query->num_rows() > 0)
       {
          $row = $query->row();
 
          if (is_null($row->name))
          {
             $this->db->delete('term', array('id'=>$id));
             return true;
          }
          else
          {
             return false;
          };
 
       };
    }

}