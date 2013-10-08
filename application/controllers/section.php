<?php if (!defined('BASEPATH')) die();

Class Section extends CI_Controller {
	
public function __construct() {
		
		parent::__construct();
	}

public function index() {
	//$this->output->enable_profiler(TRUE);
	
	$this->load->model('section_model');
	$sections=$this->section_model->get_sections_data();

	if ($sections) {
		
		$data['section'] = $sections;
		}
	else {
		$data['section'] = false;
	}
	
	$this->load->view('include/header');
	$this->load->view('sections', $data);
	$this->load->view('include/footer');

	}


public function delreg($id){
	$this->load->model('section_model');
	$this->section_model->delreg($id);
	redirect('section');
}



}