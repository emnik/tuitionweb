<?php

class User_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


    public function get_user_data($id=null){
        $this->db->select('*');
        if(!is_null($id)){
            $this->db->where('user.id', $id);
        };
        //$this->db->order_by('user.id', 'desc');
        $query=$this->db->get('user');

        if($query->num_rows()>0) {
            return $query->result_array();
        } 
        else 
        {
            return false;
        }
    }

    public function get_usernames(){
        $this->db->select('username');
        $query=$this->db->get('user');

        if($query->num_rows()>0) {
            foreach($query->result_array() as $row){
                $result[] = $row['username'];
            }
            return $result;
        } 
        else 
        {
            return false;
        }
    }

    public function get_group_data(){
        $this->db->select('*');
        $query=$this->db->get('group');
        $group = array();

        if($query->num_rows()>0) {
            // return $query->result_array();
            foreach($query->result_array() as $row) 
            {
               $group[$row['id']] = $row['name'];
            }
            return $group;
        } 
        else 
        {
            return false;
        }

    }    
    public function update_user_data($data, $id){
        foreach($data as $key=>$value){
            if(empty($value)){
                $data[$key]=null;
            }
        }
        $this->db->where('id', $id);
        $this->db->update('user', $data);
    }


    public function newuser()
    {
       //insert new record in user table
       $data = array('id' => 'null', 'group_id' => 'null', 'created' => date("Y-m-d H:i:s"), 'expires' => '0000-00-00');
       $this->db->insert('user', $data);
       $uid = $this->db->insert_id();
       return $uid;
    }
 
 
    public function deluser($id)
    {  
        if ($id != $this->session->userdata('user_id')) {
            $this->db->delete('user', array('id' => $id)); 
        }   
        else {
            return false;
        } 
       
    }
 
 
    public function canceluser($id)
    {      
       $query = $this->db->select('*')
                ->where('id', $id)
                ->get('user');
       
       if ($query->num_rows() > 0)
       {
          $row = $query->row();
 
          if (empty($row->username))
          {
             $this->db->delete('user', array('id'=>$id));
             return true;
          }
          else
          {
             return false;
          };
 
       };
    }


    public function get_termid(){
        //get active term
        $termid = $this->db->select('term.id')->where('term.active',1)->get('term')->row()->id;
        return $termid;
     }

     
     public function get_student_names_ids($filter=null){

        if (!is_null($filter)){
           $this->db
           // ->select(array('registration.id', 'CONCAT_WS(" - ", CONCAT_WS(" ", registration.surname, registration.name), term.name) as stdname'))
           ->select(array('registration.id', 'CONCAT_WS(" ", registration.surname, registration.name)  as stdname', 'term.name as termname', 'term.id as termid'))
           ->from('registration')
           ->join('term', 'registration.term_id=term.id')
           ->where("((`registration`.`surname` LIKE '%".$filter."%' OR `registration`.`name` LIKE '%".$filter."%'))")
           // ->where('term.active',1)
           ->order_by('stdname', 'ASC')
           ->order_by('termid', 'ASC');
        };
        
        $query=$this->db->get();
  
        if ($query->num_rows() > 0) 
        {
           foreach($query->result_array() as $row) 
           {
              $students[] = $row;
           }
           return $students;
        }
        else 
        {
           return false;
        }
     }     

}