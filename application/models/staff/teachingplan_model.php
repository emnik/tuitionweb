<?php if (!defined('BASEPATH')) die();

class Teachingplan_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }

		function get_tutor_program($id) {

		$this->db->select(array('employee.id', 'employee.nickname', 'section_program.day', 'weekday.priority',
							    'section_program.start_tm', 'section_program.end_tm',
							    'section.section', 'catalog_lesson.title', 'classroom.classroom',
							    'section_program.section_id', 'section_program.duration'));
		$this->db->from('section_program' );
		$this->db->join('classroom', 'section_program.classroom_id = classroom.id', 'left');
		$this->db->join('weekday', 'section_program.day=weekday.name','left');
		$this->db->join('section', 'section_program.section_id = section.id');
		$this->db->join('lesson_tutor', 'section.tutor_id = lesson_tutor.id');
		$this->db->join('employee', 'lesson_tutor.employee_id = employee.id');
		$this->db->join('catalog_lesson', 'lesson_tutor.cataloglesson_id = catalog_lesson.id'); 
		$this->db->join('lookup', 'lookup.value_1 = section.schoolyear');
		$this->db->where('lookup.id','2');
		$this->db->where('employee.active','1');
		$this->db->where('employee.id', $id);
		$this->db->order_by('weekday.priority','ASC');
		$this->db->order_by('section_program.start_tm','ASC');
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) 
		{
			foreach($query->result_array() as $row) 
			{
				$tutor_program_data[] = $row;
			}
			return $tutor_program_data;
		}
		else {
			return false;
		}
	} 



	function get_tutor_section_summary($id) {

		$this->db
			->select(array('section.id', 'section.section', 'catalog_lesson.title', 'COUNT(registration.id) as studentsnum', 'lesson.hours'))
			->from('std_lesson' )
			->join('registration', 'std_lesson.reg_id = registration.id')
			->join('section', 'std_lesson.section_id=section.id')
			->join('lesson', 'std_lesson.lesson_id = lesson.id')
			->join('lookup', 'lookup.value_1 = section.schoolyear')
			->join('lesson_tutor', 'section.tutor_id=lesson_tutor.id')
			->join('catalog_lesson', 'lesson_tutor.cataloglesson_id = catalog_lesson.id')
			->join('employee', 'lesson_tutor.employee_id=employee.id')
			->where('lookup.id','2')
			->where('employee.id', $id)
			->group_by('section.id')
			->order_by('section.section','ASC');
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) 
		{
			foreach($query->result_array() as $row) 
			{
				$tutor_section_students[] = $row;
			}
			return $tutor_section_students;
		}
		else {
			return false;
		}
	} 



function get_section_students($section_id){
	$this->db
			->select(array('section.id', 'section.section','CONCAT_WS(" ", registration.surname, registration.name) as stdname', 'registration.fathers_name', 'registration.mothers_name', 'contact.std_mobile','contact.mothers_mobile','contact.fathers_mobile','contact.work_tel','contact.home_tel','catalog_lesson.title'))
			->from('std_lesson')
			->join('registration', 'std_lesson.reg_id = registration.id')
			->join('section', 'std_lesson.section_id = section.id')
			->join('lookup', 'lookup.value_1 = section.schoolyear')
			->join('contact', 'contact.reg_id = registration.id')
			->join('lesson_tutor', 'section.tutor_id=lesson_tutor.id')
			->join('catalog_lesson', 'lesson_tutor.cataloglesson_id = catalog_lesson.id')
			->where('std_lesson.section_id', $section_id);
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) 
		{
			foreach($query->result_array() as $row) 
			{
				$tutor_section_students[] = $row;
			}
			return $tutor_section_students;
		}
		else {
			return false;
		}
}


	public function get_exams_by_employeeid($id, $startsch)
	{

		$query = $this->db->distinct()
						  ->select(array('exam_schedule.id','exam_schedule.date','catalog_lesson.title', 'class.class_name' ,'course.course'))
						  ->from('section')
						  ->join('lesson_tutor', 'section.tutor_id = lesson_tutor.id')
						  ->join('employee', 'lesson_tutor.employee_id = employee.id')
						  ->join('catalog_lesson', 'lesson_tutor.cataloglesson_id = catalog_lesson.id')
						  ->join('lesson', 'section.lesson_id = lesson.id')
						  ->join('course', 'lesson.course_id = course.id')
						  ->join('class', 'course.class_id = class.id')
						  ->join('exam_schedule', 'exam_schedule.lesson_id=section.lesson_id')
						  ->where('employee.id', $id)
						  ->where('section.schoolyear', $startsch)
						  ->get();

		if ($query->num_rows()>0)
		{
			foreach ($query->result_array() as $row) {
				$data[]=$row;
			}
			return $data;
		}
		return false;

	}

	public function get_participants($examids, $id)
	{
		$query = $this->db->select(array('exam_id', 'section.section'))
						  ->from('exam_participant')
						  ->join('section', 'exam_participant.section_id = section.id')
						  ->join('lesson_tutor', 'section.tutor_id = lesson_tutor.id')
						  ->join('employee', 'lesson_tutor.employee_id = employee.id')
						  ->where_in('exam_participant.exam_id', $examids)
						  ->where('employee.id', $id)
						  ->get();
		if($query->num_rows()>0)
		{
			foreach ($query->result_array() as $row) {
				$data[$row['exam_id']][]=$row['section'];
			}
			return $data;
		}
		return false;
	}

	public function get_supervisor_dates($startsch, $id)
	{

		$dates = $this->db->distinct()
						  ->select('exam_schedule.date')
						  ->from('exam_schedule')
						  ->where_in('startschyear', $startsch)
						  ->get();
		
		if($dates->num_rows()>0)
		{
			foreach ($dates->result_array() as $row) {
				$datesarr[]=$row['date'];
			}

			$supervisor_dates = $this->db->select('exam_supervisor.date')
										 ->where_in('date', $datesarr)
										 ->where('employee_id', $id)
										 ->get('exam_supervisor');
			
			if ($supervisor_dates->num_rows()>0)
			{
				foreach ($supervisor_dates->result_array() as $row) {
					$data[]=$row['date'];
				}
				return $data;
			}
		}
		return false;
	}

}