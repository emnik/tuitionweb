<?php if (!defined('BASEPATH')) die();

class Program_model extends CI_Model {

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
}