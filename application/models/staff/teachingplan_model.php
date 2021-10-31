<?php if (!defined('BASEPATH')) die();

class Teachingplan_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	function get_tutor_program($id)
	{

		$this->db->select(array(
			'employee.id', 'employee.nickname', 'section_program.day', 'weekday.priority',
			'section_program.start_tm', 'section_program.end_tm',
			'section.section', 'catalog_lesson.title', 'classroom.classroom',
			'section_program.section_id', 'section_program.duration','section.id'
		));
		$this->db->from('section_program');
		$this->db->join('classroom', 'section_program.classroom_id = classroom.id', 'left');
		$this->db->join('weekday', 'section_program.day=weekday.name', 'left');
		$this->db->join('section', 'section_program.section_id = section.id');
		$this->db->join('term', 'section.term_id=term.id');
		$this->db->join('lesson_tutor', 'section.tutor_id = lesson_tutor.id');
		$this->db->join('employee', 'lesson_tutor.employee_id = employee.id');
		$this->db->join('catalog_lesson', 'lesson_tutor.cataloglesson_id = catalog_lesson.id');
		$this->db->where('term.active',1);
		$this->db->where('employee.active', '1');
		$this->db->where('employee.id', $id);
		$this->db->order_by('weekday.priority', 'ASC');
		$this->db->order_by('section_program.start_tm', 'ASC');

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$tutor_program_data[] = $row;
			}
			return $tutor_program_data;
		} else {
			return false;
		}
	}



	function get_tutor_section_summary($id)
	{

		$this->db
			->select(array('section.id', 'section.section', 'catalog_lesson.title', 'COUNT(registration.id) as studentsnum', 'section_program.duration as hours', 'class.class_name'))
			->from('std_lesson')
			->join('registration', 'std_lesson.reg_id = registration.id')
			->join('class', 'registration.class_id=class.id')
			->join('section', 'std_lesson.section_id=section.id')
			->join('term', 'section.term_id=term.id')
			->join('section_program', 'section.id=section_program.section_id')
			->join('lesson', 'std_lesson.lesson_id = lesson.id')
			->join('lesson_tutor', 'section.tutor_id=lesson_tutor.id')
			->join('catalog_lesson', 'lesson_tutor.cataloglesson_id = catalog_lesson.id')
			->join('employee', 'lesson_tutor.employee_id=employee.id')
			->where('term.active',1)
			->where('employee.id', $id)
			->group_by('section.id')
			->group_by('section_program.id')
			->order_by('class.class_name', 'ASC')
			->order_by('section.section', 'ASC');


		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$tutor_section_students[] = $row;
			}
			return $tutor_section_students;
		} else {
			return false;
		}
	}



	function get_section_students($section_id)
	{
		$this->db
			->select(array('section.id', 'section.section', 'CONCAT_WS(" ", registration.surname, registration.name) as stdname', 'registration.fathers_name', 'registration.mothers_name', 'contact.std_mobile', 'contact.mothers_mobile', 'contact.fathers_mobile', 'contact.work_tel', 'contact.home_tel', 'catalog_lesson.title'))
			->from('std_lesson')
			->join('registration', 'std_lesson.reg_id = registration.id')
			->join('section', 'std_lesson.section_id = section.id')
			->join('term', 'section.term_id=term.id')
			// ->join('lookup', 'lookup.value_1 = section.schoolyear')
			->join('contact', 'contact.reg_id = registration.id')
			->join('lesson_tutor', 'section.tutor_id=lesson_tutor.id')
			->join('catalog_lesson', 'lesson_tutor.cataloglesson_id = catalog_lesson.id')
			->where('term.active',1)
			->where('std_lesson.section_id', $section_id);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$tutor_section_students[] = $row;
			}
			return $tutor_section_students;
		} else {
			return false;
		}
	}

	function get_exams_data($id) {
		$query=$this->db->distinct()
						->select(array('exam.id', 'exam.date', 'exam.start', 'exam.end', 'catalog_lesson.title', 'class.class_name'))
						->from('exam')
						->join('term', 'term.id = exam.term_id')
						->join('exam_lesson', 'exam_lesson.exam_id = exam.id')
						->join('section', 'section.lesson_id = exam_lesson.lesson_id')
						->join('class', 'section.class_id = class.id')
						->join('lesson', 'lesson.id = section.lesson_id')
						->join('catalog_lesson', 'catalog_lesson.id = lesson.cataloglesson_id')
						->join('lesson_tutor', 'lesson_tutor.id = section.tutor_id')
						->where('lesson_tutor.employee_id', $id)
						->where('term.active', 1)
						->where('section.term_id = exam.term_id')
						->where('exam.date >=', date('Y-m-d'))
						->order_by('exam.date')
						->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$exam_data[] = $row;
			}
			return $exam_data;
		} else {
			return false;
		}			
	}


	function get_supervisor_data($id){
		$query=$this->db->select(array('exam.id', 'exam.date','exam.start', 'exam.end'))
						->from('exam')
						->join('exam_supervisor', 'exam.id = exam_supervisor.exam_id')
						->where('exam_supervisor.employee_id', $id)
						->where('exam.date >=', date('Y-m-d'))
						->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			return $data;
		} else {
			return false;
		}							
	}

}
