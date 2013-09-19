<?php if (!defined('BASEPATH')) die();

class Attendance_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


public function get_program_data($id){

	$this->db->select(array('section_program.day',
							'weekday.priority', 'classroom.classroom',
							'section_program.start_tm', 'section_program.end_tm',
							'employee.nickname', 'catalog_lesson.title', 
							'std_lesson.section_id', 'section.section'));
	$this->db->from('registration, section_program');
	$this->db->join('classroom','section_program.classroom_id = classroom.id','left');
	$this->db->join('weekday','section_program.day = weekday.name','left');
	$this->db->join('std_lesson', 'std_lesson.reg_id = registration.id AND section_program.section_id = std_lesson.section_id'); 
	$this->db->join('section','std_lesson.section_id = section.id');
	$this->db->join('lesson_tutor','section.tutor_id = lesson_tutor.id');
	$this->db->join('employee','lesson_tutor.employee_id = employee.id');
	$this->db->join('catalog_lesson','lesson_tutor.cataloglesson_id = catalog_lesson.id');
	$this->db->where('registration.id',$id);
	$this->db->order_by('weekday.priority');
	$this->db->order_by('start_tm', 'ASC');

	$query = $this->db->get();
	if ($query->num_rows() > 0) 
		{
			foreach($query->result_array() as $row) 
				{
					$students_program[] = $row;
				}
			return $students_program;
		}
	else 
		{
			return false;
		}

	}


public function get_attendance_general_data($id){

	$this->db->select(array('std_lesson.id','employee.nickname', 'catalog_lesson.title', 'section.section'));
	$this->db->from('registration');
	$this->db->join('std_lesson', 'registration.id = std_lesson.reg_id'); 
	$this->db->join('section','std_lesson.section_id = section.id');
	$this->db->join('lesson_tutor','section.tutor_id = lesson_tutor.id');
	$this->db->join('employee','lesson_tutor.employee_id = employee.id');
	$this->db->join('catalog_lesson','lesson_tutor.cataloglesson_id = catalog_lesson.id');
	$this->db->where('registration.id',$id);
	$this->db->order_by('employee.nickname');
	$this->db->order_by('section.section');

	$query = $this->db->get();
	
	if ($query->num_rows() > 0) 
		{
			foreach($query->result_array() as $row) 
				{
					$students_program[] = $row;
				}
			return $students_program;
		}
	else 
		{
			return false;
		}

	}


	public function delstdlesson($id, $lessonid){
		$this->db->where('std_lesson.reg_id',$id)
				->where('std_lesson.id',$lessonid)
				->delete('std_lesson');
	}


	public function getpossiblesections($id){
		//1. get the classid of the student
		$query = $this->db->select('class_id')
							->where('id',$id)
							->limit(1)
							->get('registration');

		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			$classid = $row->class_id;

			//2. pairing class_ids when possible
			switch ($classid) {
				case '6':
				case '8':
				case '10': //Γ Λυκείου, Γ ΕΠΑΛ, Απόφοιτοι
					$possible_classes = array('6','8','10'); 
					break;
				
				case '5':
				case '7': // Β Λυκείου, Β ΕΠΑΛ
					$possible_classes = array('5','7'); 
					break;
						
				default:
					$possible_classes = array($classid);
					break;
			}
			//3.get selected schoolyear start
			$this->load->model('welcome_model');
			$startyear = $this->welcome_model->get_selected_startschyear();

			//4.get all possible sections
			$subquery1 = $this->db->distinct()
								->select(array('CONCAT_WS(" ",`section`.`section`,`catalog_lesson`.`title`) as section_title', 'section.id'))
								->from('section')
								->join('lesson_tutor','section.tutor_id = lesson_tutor.id')
								->join('catalog_lesson','lesson_tutor.cataloglesson_id = catalog_lesson.id')
								->where('section.schoolyear', $startyear)
								->where_in('section.class_id',$possible_classes)
								->order_by('section.section', 'ASC')
								->get();

			//5.get groups of possible sections by name
			$subquery2 = $this->db->distinct()
								->select('section.section')
								->from('section')
								->join('lesson_tutor','section.tutor_id = lesson_tutor.id')
								->join('catalog_lesson','lesson_tutor.cataloglesson_id = catalog_lesson.id')
								->where('section.schoolyear', $startyear)
								->where_in('section.class_id',$possible_classes)
								->order_by('section.section', 'ASC')
								->get();

			if ($subquery1->num_rows() > 0 and $subquery1->num_rows() > 0) 
			{
				foreach($subquery1->result_array() as $row) 
					{
						$sections['all'][] = $row;
					}
				foreach($subquery2->result_array() as $row) 
					{
						$sections['groups'][] = $row;
					}
				return $sections;
			}
			else 
			{
				return false;
			}
		}

	}

	public function insertmultiple($id, $sectionids){

		$query=$this->db->select(array('section.lesson_id','section.tutor_id', 'section.id'))
						->where_in('section.id', $sectionids)
						->get('section');
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row) {
				$data[]=array(
					'reg_id'=> $id,
					'lesson_id' => $row['lesson_id'],
					'tutor_id' => $row['tutor_id'],
					'section_id' => $row['id']
					);
			};
			$this->db->insert_batch('std_lesson', $data);
			return true;
		}
		else
		{
			return false;
		}
	}

	public function insertallbyname($id, $sectionName){

		//1.get selected schoolyear start
		$this->load->model('welcome_model');
		$startyear = $this->welcome_model->get_selected_startschyear();

		//2.get all possible sections with the selected section name
		$query = $this->db->distinct()
							->select(array('section.lesson_id','section.tutor_id','section.id'))
							->from('section')
							->where('section.schoolyear', $startyear)
							->where('section.section',$sectionName)
							->get();

		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row) {
				$data[]=array(
					'reg_id'=> $id,
					'lesson_id' => $row['lesson_id'],
					'tutor_id' => $row['tutor_id'],
					'section_id' => $row['id']
					);
			};
			$this->db->insert_batch('std_lesson', $data);
			return true;
		}
		else
		{
			return false;
		}
	}

	public function get_daylessonshours($id, $stdlessonsids)
	{
		$this->db->select(array('std_lesson.id', 'catalog_lesson.title', "CONCAT_WS('-', DATE_FORMAT(`section_program`.`start_tm`, '%H:%i'), DATE_FORMAT(`section_program`.`end_tm`, '%H:%i')) AS 'hours'"))
						->from('std_lesson')
						->join('lesson', 'lesson.id=std_lesson.lesson_id')
						->join('catalog_lesson', 'catalog_lesson.id=lesson.cataloglesson_id')
						->join('section', 'std_lesson.section_id=section.id')
						->join('section_program', 'section.id=section_program.section_id')
						->join('weekday', 'section_program.day=weekday.name')
						->where('std_lesson.reg_id', $id);
						if(!empty($stdlessonsids)){
							$this->db->where_not_in('std_lesson.id', $stdlessonsids);
						};
						$this->db->where('weekday.priority', date('N'))
						//$this->db->where('weekday.priority', date('N')+5)
						->order_by('weekday.priority', 'asc');
						$query=$this->db->get();

		if ($query -> num_rows() > 0)
		{
			foreach($query->result_array() as $row) 
				{
					$daylessonshours[] = $row;
				}
			return $daylessonshours;
		}
	else 
		{
			return false;
		}

	}


	public function count_absences($id)
	{
		$allabsences = $this->db
				->where('reg_id', $id)
				->count_all_results('absences');

		if ($allabsences>0){
			$excused = $this->db
					->where('reg_id', $id)
					->where('excused','1')
					->count_all_results('absences');

			$unexcused = $allabsences - $excused;	
			
			$absences = array();
			$absences['excused']=$excused;
			$absences['unexcused']=$unexcused;

		}
		else
		{
			$absences = $allabsences; //this will be zero
		}
			return $absences;
	}


	public function get_allabsences($id)
	{
		$query=$this->db->select(array('absences.id','absences.stdlesson_id','absences.date', 'absences.excused', 'catalog_lesson.title', "CONCAT_WS('-', DATE_FORMAT(`section_program`.`start_tm`, '%H:%i'), DATE_FORMAT(`section_program`.`end_tm`, '%H:%i')) AS 'hours'"))
						->from('absences')
						->join('std_lesson', 'absences.stdlesson_id=std_lesson.id')
						->join('section', 'std_lesson.section_id=section.id')
						->join('section_program', 'section.id=section_program.section_id')	
						->join('weekday', 'section_program.day=weekday.name')
						->join('lesson_tutor', 'section.tutor_id=lesson_tutor.id')
						->join('catalog_lesson', 'lesson_tutor.cataloglesson_id=catalog_lesson.id')
						->where('weekday.priority', date('N', strtotime('absences.date')))
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


	public function get_dayabsences($id)
	{
		$query=$this->db->select(array('absences.id','absences.stdlesson_id', 'absences.excused', 'catalog_lesson.title', "CONCAT_WS('-', DATE_FORMAT(`section_program`.`start_tm`, '%H:%i'), DATE_FORMAT(`section_program`.`end_tm`, '%H:%i')) AS 'hours'"))
						->from('absences')
						->join('std_lesson', 'absences.stdlesson_id=std_lesson.id', 'left')
						//->join('lesson', 'lesson.id=std_lesson.lesson_id')
						//->join('catalog_lesson', 'catalog_lesson.id=lesson.cataloglesson_id')
						//->join('lesson_tutor', 'std_lesson.tutor_id=lesson_tutor.id')
						//->join('catalog_lesson', 'lesson_tutor.cataloglesson_id=catalog_lesson.id')
						->join('section', 'std_lesson.section_id=section.id')
						->join('lesson_tutor', 'section.tutor_id=lesson_tutor.id')
						->join('catalog_lesson', 'lesson_tutor.cataloglesson_id=catalog_lesson.id')
						->join('section_program', 'section.id=section_program.section_id')
						->where('absences.reg_id', $id)
						->where('absences.date', date('Y-m-d'))
						->join('weekday', 'section_program.day=weekday.name')
						->where('weekday.priority', date('N'))
						->order_by('hours')
						->get();

		if ($query -> num_rows() > 0)
		{
			foreach($query->result_array() as $row) 
				{
					$dayabsences[] = $row;
				}
			return $dayabsences;
		}
		else 
		{
			return false;
		}
	}


	public function ins_del_upd_absences($insertdata=null, $updatedata=null, $deletedata=null)
	{

		if (!empty($insertdata)) {
			$this->db->insert_batch('absences', $insertdata);
		};


		if (!empty($updatedata))
		{
			$this->db->update_batch('absences', $updatedata, 'id');
		};


		if (!empty($deletedata)) {
			foreach ($deletedata as $data) {
					$this->db->delete('absences',$data);
			};
		};
				
	}



	public function get_progress_data($id){
		$query = $this -> db 
						-> select('*')
						-> from('progress')
						-> where('progress.reg_id', $id)
						-> get();

		if ($query -> num_rows() > 0)
		{
			foreach($query->result_array() as $row) 
				{
					$progress[] = $row;
				}
			return $progress;
		}
		else 
		{
			return false;
		}
	}


}

