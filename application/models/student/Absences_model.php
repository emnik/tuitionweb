<?php if (!defined('BASEPATH')) die();

class Absences_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


	public function get_allabsences($id)
	{
		$query=$this->db->select(array('absences.id','absences.stdlesson_id','absences.date', 'absences.excused', 'catalog_lesson.title', "CONCAT_WS('-', DATE_FORMAT(`section_program`.`start_tm`, '%H:%i'), DATE_FORMAT(`section_program`.`end_tm`, '%H:%i')) AS 'hours'", 'employee.nickname'))
						->from('absences')
						->join('std_lesson', 'absences.stdlesson_id=std_lesson.id')
						->join('section', 'std_lesson.section_id=section.id')
						->join('section_program', 'section.id=section_program.section_id')	
						->join('weekday', 'section_program.day=weekday.name')
						->join('lesson_tutor', 'section.tutor_id=lesson_tutor.id')
						->join('employee', 'lesson_tutor.employee_id=employee.id')
						->join('catalog_lesson', 'lesson_tutor.cataloglesson_id=catalog_lesson.id')
						->where('weekday.priority', 'DAYOFWEEK(`absences`.`date`)-1', false)
						->where('absences.reg_id', $id)
						->order_by('date')
						->order_by('hours')
						->get();

		if ($query -> num_rows() > 0)
		{
			foreach($query->result_array() as $row) 
				{
					$allabsences[] = $row;
				}
			return $allabsences;
		}
		else 
		{
			return false;
		}
	}


	public function updateabsences($excused_data, $id)
	{
		//Get all absences ids for the student
		$query = $this->db->select('id')
							->from('absences')
							->where('reg_id', $id)
							->get();
		
		foreach($query->result_array() as $row) 
		{
			$absencesids[] = $row;
		};
		
		$data=array(); //for the batch update statement
		
		//$this->load->library('firephp');


		//make an array with all posible ids
		$allids=array();
		foreach ($absencesids as $arrayid) {
			foreach ($arrayid as $key => $value) {
				array_push($allids, $value);
			}
		};

		//make an array with ids that we have excused absences
		$idswithdata=array();
		foreach ($excused_data as $key=>$value) {
			//set the data for the update for the excused absences
			$data[]=array('id'=>$key, 'excused'=>1);
			array_push($idswithdata, $key);
		};


		//make an array with ids that have unexcused absences
		$idswithnodata=array_diff($allids, $idswithdata);

		foreach ($idswithnodata as $key => $value) {
			//set the data for the update for the unexcused absences
			$data[]=array('id'=>$value, 'excused'=>0);
		};

		//update absences with the new data
		$this->db->update_batch('absences', $data, 'id');
	}


   function del_absence($absence_id)
   {
    $this->db->delete('absences', array('id'=>$absence_id));
   }



}