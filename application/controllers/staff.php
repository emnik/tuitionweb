<?php if (!defined('BASEPATH')) die();

Class Staff extends CI_Controller {
	
public function __construct() {
		
		parent::__construct();
	}

public function index() {
	//$this->output->enable_profiler(TRUE);
	
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
	 		$this->teachingplan($id, $innersubsection, $employee);
	 		return 0;
	 		break;

 	
	 	default:
	 		# code...
	 		break;
	 }

	$this->load->model('staff/card_model');
	$data['emplcard']=array();
	if (!empty($_POST)) {
	 	foreach ($_POST as $key => $value) 
	 	{
	 		$employee_data[$key]=$value;
	 	};
	 	$this->card_model->update_employee_data($employee_data, $id);
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


public function teachingplan($id, $innersubsection=null, $employee){
	
	$data['employee']=$employee;

	$this->load->model('staff/teachingplan_model');
	$program = $this->teachingplan_model->get_tutor_program($id);

	$sections_summary = $this->teachingplan_model->get_tutor_section_summary($id);
	if ($sections_summary){
		$data['section'] = $sections_summary;
	};

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



// public function program ($id, $employee){
// 	$this->load->model('staff/teachingplan_model');
// 	$program = $this->teachingplan_model->get_tutor_program($id);
// 	$data['employee'] = $employee;
// 	if($program){
// 		$data['program'] = $program;
// 	}
// 	else
// 	{
// 		$data['program'] = false;
// 	};

// 	$this->load->view('include/header');
// 	$this->load->view('employee/program', $data);
// 	$this->load->view('include/footer');
// }


}