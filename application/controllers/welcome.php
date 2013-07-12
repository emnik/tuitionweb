<?php if (!defined('BASEPATH')) die();

Class Welcome extends CI_Controller {
	
public function __construct() {
		
		parent::__construct();
	}

public function index() {
	//$this->output->enable_profiler(TRUE);
	
	$this->load->model('welcome_model');
	$schoolyears=$this->welcome_model->get_schoolyears();
	if ($schoolyears) {
		$data['schoolyears'] = $schoolyears;
		}
	else {
		$data['schoolyears']=array();
	}
	
	$selected_schstart = $this->welcome_model->get_selected_startschyear(); 
	$data['selected_schstart']= $selected_schstart; 

	$startsch = $this->input->post('startschoolyear');
	if (!empty($startsch)) {
		switch ($this->input->post('submit')) {
			case 'submit1': //Μαθητολόγιο
				if ($selected_schstart!=$startsch){
					$this->welcome_model->set_schoolyear($startsch);	
				}
				redirect('registrations');
				break;
			
			case 'submit2': // Προσωπικό
				#code...
				break;

			case 'submit3': // Τμήματα
				#code...
				break;

			case 'submit4': // Οικονομικά
				#code...
				break;

			case 'submit5': // Αναφορές
				#code...
				break;

			case 'submit6': // Διαχείριση
				#code...
				break;
		}
		
	}
	
	$this->load->view('include/header');
	$this->load->view('welcome', $data);
	$this->load->view('include/footer');

	}

}