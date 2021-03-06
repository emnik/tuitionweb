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
	$this->load->view('include/footer');

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

 	    case 'gradebook':
	 		$this->gradebook($id, $employee, $user);
	 		return 0;
	 		break;

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
	$this->load->view('include/footer');
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

	$sections_summary = $this->teachingplan_model->get_tutor_section_summary($id);
	if ($sections_summary){
		$data['section'] = $sections_summary;

		$section_data=array();
		foreach ($sections_summary as $tmpdata) {
			$students = $this->teachingplan_model->get_section_students($tmpdata['id']);
			$section_data[$tmpdata['id']]=$students;
		}
	$data['section_data'] = $section_data;
	};


	$examdata = $this->teachingplan_model->get_exams_by_employeeid($id, $this->session->userdata('startsch'));
	if($examdata)
	{
		//get the paricipants and merge the results with the examdata
		foreach ($examdata as $row) {
			$examids[]=$row['id'];
			$participants = $this->teachingplan_model->get_participants($examids, $id);
			if($participants)
			{
				$participants_list=array();
				foreach ($participants as $key => $value) {
					$participants_list[$key]="";
					foreach ($value as $subkey => $subvalue) {
						if($participants_list[$key]=="")
						{
							$participants_list[$key]=$subvalue;	
						}
						else
						{
							$participants_list[$key]=$participants_list[$key].', '.$subvalue;
						}
					}
				}
				foreach ($participants_list as $key1 => $value1) {
					foreach ($examdata as $key2 => $value2) {
						if($value2['id']==$key1)
						{
							$examdata[$key2]['sections'] = $value1;
						}
					}
				}
				$data['participants']=$participants;
			}
		}

		$data['exam']=$examdata;
	}

	$supervisor = $this->teachingplan_model->get_supervisor_dates($this->session->userdata('startsch'),$id);
	if($supervisor)
	{
		$supervisor_dates="";
		foreach ($supervisor as $key => $value) {
			if($supervisor_dates=="")
			{
				$supervisor_dates='<strong>'.implode('-', array_reverse(explode('-', $value))).'</strong>';
			}
			else
			{
				$supervisor_dates = $supervisor_dates.' , <strong>'.implode('-', array_reverse(explode('-', $value))).'</strong>';
			}
		}
		$data['supervisor'] = $supervisor_dates;
	}


	// $this->load->library('firephp');
	// $this->firephp->info($supervisor);

	
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
	};

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

	$this->load->view('include/footer');
}


public function gradebook($id, $employee, $user){
	
	$data['employee']=$employee;
	$data['user']=$user;

	//get the exam data from the teachingplan model as we use exactly the same info...
	$this->load->model('staff/teachingplan_model');
	$examdata = $this->teachingplan_model->get_exams_by_employeeid($id, $this->session->userdata('startsch'));
	if($examdata)
	{
		//get the paricipants and merge the results with the examdata
		foreach ($examdata as $row) {
			$examids[]=$row['id'];
			$participants = $this->teachingplan_model->get_participants($examids, $id);
			if($participants)
			{
				$participants_list=array();
				foreach ($participants as $key => $value) {
					$participants_list[$key]="";
					foreach ($value as $subkey => $subvalue) {
						if($participants_list[$key]=="")
						{
							$participants_list[$key]=$subvalue;	
						}
						else
						{
							$participants_list[$key]=$participants_list[$key].', '.$subvalue;
						}
					}
				}
				foreach ($participants_list as $key1 => $value1) {
					foreach ($examdata as $key2 => $value2) {
						if($value2['id']==$key1)
						{
							$examdata[$key2]['sections'] = $value1;
						}
					}
				}
				$data['participants']=$participants;
			}
		}

		$data['exam']=$examdata;
	}


	$this->load->view('include/header');
	$this->load->view('employee/gradebook', $data);	
	$this->load->view('include/footer');


}


	public function logout()
	{

		$this->session->destroy();

		$this->load->view('include/header');		
		$this->load->view('login');
		$this->load->view('include/footer');
	}


}