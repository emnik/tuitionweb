<?php

class Participants_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


    public function get_participants_data($id)
    {
		$query = $this->db
			   ->select(array('exam_participant.id','section.section','employee.nickname','COUNT(std_lesson.id) as stdcount'))
			   ->from('section')
			   ->join('exam_participant', 'exam_participant.section_id=section.id')
			   ->join('lesson_tutor', 'section.tutor_id = lesson_tutor.id')
			   ->join('employee','lesson_tutor.employee_id=employee.id')
			   ->join('std_lesson', 'section.id=std_lesson.section_id')
			   ->where('exam_participant.exam_id', $id)
			   ->group_by('std_lesson.section_id')
			   ->get();

		if ($query->num_rows()>0)
		{
			return $query->result_array();
		}
		return false;
    }

	public function get_all_sections_by_lesson($lesson_id, $startsch)
	{
		$query = $this->db
					   ->select(array('section.id','section.section'))
					   ->from('section')
					   ->where('section.lesson_id', $lesson_id)
					   ->where('section.schoolyear', $startsch)
					   ->get();

		if ($query->num_rows()>0)
		{
			foreach ($query->result_array() as $row) {
				$data[$row['id']]=$row['section'];
			}
			return $data;
		}
		else
		{
		return false;
		}
	}


	//get the tutors that tutor the lesson (#1) 
	public function get_tutors_by_lesson($lesson_id, $startsch)
	{
		$query = $this->db->distinct()
			   ->select(array('section.tutor_id', 'employee.nickname'))
			   ->from('employee')
			   ->join('lesson_tutor', 'employee.id=lesson_tutor.employee_id')
			   ->join('section', 'lesson_tutor.id=section.tutor_id')
			   ->where('section.lesson_id', $lesson_id)
			   ->where('section.schoolyear', $startsch)
			   ->get();

		if ($query->num_rows()>0)
		{
			foreach ($query->result_array() as $row) {
				$data[$row['tutor_id']]=$row['nickname'];
			}
			return $data;
		}
		return false;
	}

	//get the sections that belong to those tutors and that lesson (#2)!
	public function get_sections_by_tutors($tutorslist, $lessonid, $startsch)
	{
		$query = $this->db
			   ->select('section.id')
			   ->from('section')
			   ->where_in('section.tutor_id', $tutorslist)
			   ->where('section.lesson_id', $lessonid)
			   ->where('section.schoolyear', $startsch)
			   ->get();

		if ($query->num_rows()>0)
		{
			foreach ($query->result_array() as $row) {
				$data[]=$row['id'];
			}
			return $data;
		}
		return false;
	}


	public function insertexamsectionids($examid, $sectionids){
		foreach ($sectionids as $key => $value) {
			$data[]=array(
					'exam_id'=> $examid,
					'section_id' => $value,
					);
		}
		
		$this->db->insert_batch('exam_participant', $data);
		return true;
	}

	public function delexamparticipant($examid, $id)
	{
		$this->db->where('exam_id',$examid)->where('id',$id)->delete('exam_participant');
	}
}