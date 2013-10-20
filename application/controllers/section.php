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


public function card($id, $subsection=null) {

	if(is_null($id)) redirect('section');

	//get section's main data (name surname id) in an array to use everywhere in employee section
	$this->load->model('section_model');
	$section = $this->section_model->get_section_common_data($id);
	if ($section) {
		$data['section'] = $section;	
	}
	else {
		//the following is needed to show greek in the die() message!
		$msg="  <!DOCTYPE html>
				<html lang='en'>
  				<head>
			  		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
			  	<head>
			  	<body>
			  		<p>Δεν υπάρχει τμήμα με κωδικό ".$id."</p>
			  	</body>
			  	</html>";

		die($msg);
	}
	

	switch ($subsection) {
	 	case 'studentgroup':
	 		$this->studentgroup($id, $section);
	 		return 0;
	 		break;

 	
	 	default:
	 		# code...
	 		break;
	 }

	$this->load->model('section/card_model');
	$data['sectioncard']=array();
	if (!empty($_POST)) {
	 	foreach ($_POST as $key => $value) 
	 	{
	 		$section_data[$key]=$value;
	 	};
	 	$this->card_model->update_section_data($section_data, $id);
	}
	else 
	{
		$section_data=$this->card_model->get_section_data($id);
	}

	$data['sectioncard'] = $section_data;
	
	$this->load->view('include/header');
	$this->load->view('section/card', $data);
	$this->load->view('include/footer');

	}


public function delreg($id){
	$this->load->model('section_model');
	$this->section_model->delreg($id);
	redirect('section');
}



}