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
	 	case 'program':
	 		//$this->program($id, $employee);
	 		//return 0;
	 		break;

	 	case 'sections':
	 		//$this->sections($id, $innersubsection, $employee);
	 		//return 0;
	 		break;
	 	
	 	default:
	 		# code...
	 		break;
	 }

	$this->load->model('employee/card_model');
	$data['emplcard']=array();
	if (!empty($_POST)) {
	// 	$date_fields = array('start_lessons_dt','del_lessons_dt','reg_dt');
	 	foreach ($_POST as $key => $value) 
	 	{
			
	// 		if (in_array($key,$date_fields))
	// 		{
	// 			$value = implode('-', array_reverse(explode('-', $value)));	
	// 			if ($value=='0000-00-00') $value = null;
	// 		};
	 		$employee_data[$key]=$value;
	 	};
	 	$this->card_model->update_employee_data($employee_data, $id);
	}
	else 
	{
		$employee_data=$this->card_model->get_employee_data($id);
	}

	$data['emplcard'] = $employee_data;
	//echo print_r($data);
	
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
	redirect('student');
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
	// else if ($form=='contact'){
	// 	redirect('student/card/'.$id.'/contact');
	// };	
	
}


}