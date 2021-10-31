<?php if (!defined('BASEPATH')) die();

Class Staff extends CI_Controller {
	
public function __construct() {
		
		parent::__construct();

		$session_user = $this->session->userdata('is_logged_in');
		if(!empty($session_user))
		{
			// get the group and redirect to appropriate controller
				$this->load->model('login_model');
				$grp = $this
					->login_model
					->get_user_group($this->session->userdata('user_id'));
				
				switch ($grp->name)
				{
					case 'admin':
						// redirect('welcome');
						break;
					// case 'tutor':
					// 	redirect('tutor');
					// 	break;
					// case 'parent':
					// 	redirect('parent');
					// 	break;
				}
		}
		else
		{
			redirect('login');
		}
	}

public function index() {
	//$this->output->enable_profiler(TRUE);
	
	$this->load->model('login_model');
	$user=$this->login_model->get_user_name($this->session->userdata('user_id'));
	$data['user']=$user;

	$this->load->model('staff_model');
	$staff=$this->staff_model->get_staff_data();

	if ($staff) {
		
		$data['employees'] = $staff;
		}
	else {
		$data['employees'] = false;
	}
	
	$this->load->view('include/header');
	$this->load->view('staff', $data);
	$footer_data['regs']=true;
	$this->load->view('include/footer', $footer_data);

	}


public function card($id, $subsection=null, $innersubsection=null) {

	if(is_null($id)) redirect('staff');

	$this->load->model('login_model');
	$user=$this->login_model->get_user_name($this->session->userdata('user_id'));
	$data['user']=$user;

	// //get employee's main data (name surname id) in an array to use everywhere in employee section
	$this->load->model('staff_model');
	$employee = $this->staff_model->get_employee_common_data($id);
	if ($employee) {
		$data['employee'] = $employee;	
	}
	else {
		//the following is needed to show greek in the die() message!
		$msg="  <!DOCTYPE html>
				<html lang='en'>
  				<head>
			  		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
			  	<head>
			  	<body>
			  		<p>Δεν υπάρχει εργαζόμενος με κωδικό ".$id."</p>
			  	</body>
			  	</html>";

		die($msg);
	}
	

	switch ($subsection) {
	 	case 'teachingplan':
	 		$this->teachingplan($id, $innersubsection, $employee, $user);
	 		return 0;
	 		break;

 	    // case 'gradebook':
	 	// 	$this->gradebook($id, $employee, $user);
	 	// 	return 0;
	 	// 	break;

	 	default:
	 		# code...
	 		break;
	 }
	// $this->load->library('firephp');
	
	$this->load->model('staff/card_model');
	$lessons = $this->card_model->get_lessons();
	if($lessons){$data['lesson']=$lessons;}
	
	$selectedlessons = $this->card_model->get_tutor_lessons($id);
	if($selectedlessons){$data['selectedlessons']=$selectedlessons;}

	// $this->firephp->info(array($selectedlessons, $lessons));
	$data['emplcard']=array();
	if (!empty($_POST)) {
		// $this->load->library('firephp');
		$lessons_data=array();
		// $this->firephp->info($_POST);
	 	foreach ($_POST as $key => $value) 
	 	{
	 		if($key=='lessons') 
	 		{
	 			foreach ($value as $lkey => $lvalue) {
	 				$lessons_data[]=$lvalue;	
	 			}
	 		}
	 		else
	 		{
				$employee_data[$key]=$value;
	 		}	
	 	};
	 	// $this->firephp->info($lessons_data);
	 	$this->card_model->update_employee_data($employee_data, $id);
	 	$this->card_model->update_lessons_data($lessons_data, $selectedlessons, $id);
	 	//get the new lessons data
	 	$data['selectedlessons']=array();
	 	$selectedlessons = $this->card_model->get_tutor_lessons($id);
		if($selectedlessons){$data['selectedlessons']=$selectedlessons;}
	}
	else 
	{
		$employee_data=$this->card_model->get_employee_data($id);
	}

	$data['emplcard'] = $employee_data;
	
	$this->load->view('include/header');
	$this->load->view('employee/card', $data);
	$footer_data['regs']=true;
	$this->load->view('include/footer', $footer_data);
	}

public function newreg(){
	$this->load->model('staff_model');
	$id = $this->staff_model->newreg();
	$this-> card($id);
}


public function delreg($id){
	$this->load->model('staff_model');
	$this->staff_model->delreg($id);
	redirect('staff');
}


public function cancel($form=null, $id=null){
	if (is_null($form) || is_null($id)) show_404();
	if ($form=='card'){
		$this->load->model('staff_model');
		if($this->staff_model->cancelreg($id))
		{
			redirect('staff');
		}
		else
		{
			redirect('staff/card/'.$id);
		}
	}
}


public function teachingplan($id, $innersubsection=null, $employee, $user){
	
	$data['employee']=$employee;
	$data['user']=$user;

	$this->load->model('staff/teachingplan_model');
	$program = $this->teachingplan_model->get_tutor_program($id);

	$exams = $this->teachingplan_model->get_exams_data($id);
	if ($exams) {
		$data['exam']=$exams;
	}

	$supervisor = $this->teachingplan_model->get_supervisor_data($id);
	if ($supervisor) {
		$data['supervisor']=$supervisor;
	}

	$sections_data = $this->teachingplan_model->get_tutor_section_summary($id);
	if ($sections_data){
		//group sections by class name
		$classcount = 1;
		$section_summary=array();
		$classname = $sections_data[0]['class_name'];
		$section_summary[$classname]=array();
		foreach($sections_data as $section){
			if(!array_key_exists($section['class_name'], $section_summary)){
				$classcount ++;
				$classname=$section['class_name'];
				$section_summary[$classname]=array();
			}
		}
		//we have multiple entries for the sections that have multiple days in their program
		//so we keep one of them and calculate the summary of weekly hours
		$previd=-1;
		$k=null; //doesn't need a value as it will get one the first time it executes array_push below
		$hourscount = 0;
		$sectionscount = 0;
		foreach($sections_data as $row){
			if($row['id']!=$previd){
				$k=array_push($section_summary[$row['class_name']], $row);
				$previd = $row['id'];
				//count the sections for statistics
				$sectionscount++;
			}
			else {
				$section_summary[$row['class_name']][$k-1]['hours'] += $row['hours'];

			}
			//count the hours for statistics
			$hourscount += $row['hours'];
		}

		//statistics
		$stats=array();
		$stats['sectionscount']=$sectionscount;
		$stats['hourscount']=$hourscount;
		$stats['classcount']=$classcount;
		//number of students for stats is calculated below as we need the number of unique students
		//and not the summary of students in the sections (as one student can participate in many sections!)

		$data['section_summary']=$section_summary;

		$section_data=array();
		foreach ($sections_data as $tmpdata) {
			$students = $this->teachingplan_model->get_section_students($tmpdata['id']);
			$section_data[$tmpdata['id']]=$students;
		}
		$data['section_data'] = $section_data;

		//count unique students (for stats)!
		$stds = array();
		foreach($section_data as $sn=>$mdata){
			foreach($mdata as $key=>$value){
				array_push($stds, $value['stdname']);
			}
		};
		$unames = array_unique($stds);
		$stats['stdcount']=count($unames);
		$data['stats']=$stats;


	}
	else {
		$data['section_summary']=false;
		$data['section_data']=false;
		$data['stats']=false;
	}

	
	if($program){
		$data['program'] = $program;
		$dayprogram = array();
		$j=0;
		for ($i=0; $i < count($program); $i++) { 
			if ($program[$i]['priority']==date('N')){
				$dayprogram[$j]= $program[$i];
				$j++;
			}
		};
		$data['dayprogram'] = $dayprogram;
	}
	else {
		$data['program'] = false;
		$data['dayprogram'] = false;
	}
	// $this->load->library('firephp');
	// $this->firephp->info($exams);
	$this->load->view('include/header');

	switch ($innersubsection) {
		case 'program':
			$this->load->view('employee/program', $data);	
			break;
		case 'sections':
			$this->load->view('employee/sections', $data);	
			break;
		default:
			$this->load->view('employee/teachingplan', $data);	
			break;
	}
	
	$footer_data['regs']=true;
	$this->load->view('include/footer', $footer_data);
}


}