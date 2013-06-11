<?php if (!defined('BASEPATH')) die();

Class Registrations extends CI_Controller {
	
public function __construct() {
		
		parent::__construct();
	}

public function index() {
	//$this->output->enable_profiler(TRUE);
	
	$this->load->model('registrations_model');
	$registration=$this->registrations_model->get_registration_data();

	if ($registration) {
		
		$data['students'] = $registration;
		}
	else {
		$data['students'] = false;
	}
	
	$this->load->view('include/header');
	$this->load->view('registrations', $data);
	$this->load->view('include/footer');

	}

}