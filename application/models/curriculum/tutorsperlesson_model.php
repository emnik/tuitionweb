<?php

class Tutorsperlesson_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


   public function get_cataloglessons() {

      $query = $this->db->select(array('id', 'title'))
                        ->from('catalog_lesson')
                        ->get();

      if ($query->num_rows() > 0)
      {
         foreach ($query->result() as $row)
         {
               $data[$row->id] = $row->title;
         };
         return $data;   
      }
      
      else
      {
         return false;
      };
   }


   public function get_tutors() {

      $query = $this->db->select(array('lesson_tutor.id', 'lesson_tutor.cataloglesson_id', 'lesson_tutor.employee_id', 'employee.active'))
      					->from('lesson_tutor')
      					->join('employee', 'lesson_tutor.employee_id = employee.id')
                        ->where('employee.is_tutor', 1)
                        ->get();

      if ($query->num_rows() > 0)
      {
         foreach ($query->result() as $row)
         {
         	if($row->active==1)
         	{
               	$data_active[$row->cataloglesson_id][] = $row->employee_id;
         	}
         	else
         	{
         		$data_inactive[$row->cataloglesson_id][] = $row->employee_id;	
         	}
         };
         return array('active'=>$data_active, 'inactive'=>$data_inactive);   
      }
      
      else
      {
         return false;
      };
   }

   public function get_employees() {

      $query = $this->db->select(array('id', 'CONCAT_WS(" ",surname,name) as tutorname','active'))
                        ->from('employee')
                        ->where('employee.is_tutor', 1)
                        ->get();

      if ($query->num_rows() > 0)
      {
         foreach ($query->result() as $row)
         {
         	if($row->active==1)
         	{
               $data['active'][] = array('id'=>$row->id, 'text'=>$row->tutorname);
         	}
         	else
         	{
				$data['inactive'][] = array('id'=>$row->id, 'text'=>$row->tutorname);
         	}
         };
         return $data;   
      }
      
      else
      {
         return false;
      };
   }


   public function delcataloglesson($id)
   {
   if($this->db->where('id', $id)->delete('catalog_lesson'))
      {
         return array('success'=>'true');
      }
   }
      

   public function update_lessontutors($lessonsdata, $tutorsdata, $tutors)
   {

   		//merge lesson tutors regardless if they are active or not...
   		foreach ($tutors['active'] as $key => $value) {
   			$existingtutors[$key]=$value;
   		}
   		foreach ($tutors['inactive'] as $skey => $svalue) {
			if(empty($existingtutors[$skey]))
			{
				$existingtutors[$skey]=$svalue;
			}
			else
			{
				foreach ($svalue as $iskey => $isvalue) {
					array_push($existingtutors[$skey],$isvalue);		
				}
				
			}
   		}//end of merge...

   		$insertedlessontutorids = array();
		   $lessonupdate = array();

   		foreach ($lessonsdata as $key=>$value) {
   			if($key>0){
   				//catalog_lesson data update
   				$lessonupdate[] = array('id'=>$key, 'title'=>$value);

   				//delete from lesson_tutor the records that have been removed by the user...
                  if(!empty($existingtutors[$key])){
         				foreach ($existingtutors[$key] as $existkey => $existvalue) {
                     if(!empty($tutorsdata[$key])){ 
         					if (!in_array($existvalue, $tutorsdata[$key]))
         					{
         						$this->db->where('employee_id', $existvalue)->where('cataloglesson_id', $key)->delete('lesson_tutor');
         					}
         				}
                     else //in case of an existing lesson when removing all tutors
                     {
                           $this->db->where('employee_id', $existvalue)->where('cataloglesson_id', $key)->delete('lesson_tutor');                        
                     }
                  }
               }
   				//for every user check if he is in the existing users. If not insert a new record
   				if(!empty($tutorsdata[$key])){
                  foreach ($tutorsdata[$key] as $tkey=>$tvalue) {
   	   				if (!empty($existingtutors[$key])){
                        if(!in_array($tvalue, $existingtutors[$key]))
      	   				{
      	   					$insdata1 = array('cataloglesson_id'=>$key, 'employee_id'=>$tvalue);
      	   					$this->db->insert('lesson_tutor', $insdata1);
      	   				}
                     }
                     else //in case of an existing lesson when adding the first tutor
                     {
                           $insdata1 = array('cataloglesson_id'=>$key, 'employee_id'=>$tvalue);
                           $this->db->insert('lesson_tutor', $insdata1);                        
                     }
      				}
               }
               if(!empty($lessonupdate)){
                  $this->db->update_batch('catalog_lesson', $lessonupdate, 'id');                     
               }
            }
			else //new lesson
   			{
   	   		$insdata = array('title'=>$value);
   				$this->db->insert('catalog_lesson', $insdata); 					
   				$insertedlessonid=$this->db->insert_id();
               if(!empty($tutorsdata[$key])){
                  foreach ($tutorsdata[$key] as $ntkey => $ntvalue) {
                     $insdata2 = array('cataloglesson_id'=>$insertedlessonid, 'employee_id'=>$ntvalue);
                     $this->db->insert('lesson_tutor', $insdata2);
                  }
               }
   			}   			
   		}
   }





}