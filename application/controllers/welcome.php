<?php if (!defined('BASEPATH')) die();

Class Welcome extends CI_Controller {
	
public function __construct() {
		
		parent::__construct();
	}

public function index() {
	//$this->output->enable_profiler(TRUE);
	
	$this->load->model('welcome_model');

	$regs = $this->welcome_model->get_student_names_ids();
	$regsdata= array();
	if($regs) 
		{
			foreach ($regs as $key => $value) {
				$regsdata[$value['id']]=$value['stdname'];
			};
			$this->session->set_userdata(array('fastgo_data' => $regsdata));
		};
	//$this->load->library('firephp');
	//$this->firephp->error($this->session->userdata('fastgo_data'));
	//$this->firephp->error($regsdata);



	$schoolyears=$this->welcome_model->get_schoolyears();
	if ($schoolyears) {
		$data['schoolyears'] = $schoolyears;
		}
	else {
		$data['schoolyears']=array();
	}
	
	$justyears=array();
	foreach ($schoolyears as $tmpdata) {
		array_push($justyears, $tmpdata['schoolyear']);
	};

	$selected_schstart = $this->welcome_model->get_selected_startschyear(); 
	$data['selected_schstart']= $selected_schstart; 

	$startsch = $this->input->post('startschoolyear');
	if (!empty($startsch) and $startsch!="addnextschoolyear") {
		switch ($this->input->post('submit')) {
			case 'submit1': //Μαθητολόγιο
				if ($selected_schstart!=$startsch){
					$this->welcome_model->set_schoolyear($startsch);	
				};
			
				if (in_array($startsch.'-'.($startsch+1), $justyears)==false){
					$this->welcome_model->insert_schoolyear($startsch);
				}
//				redirect('registrations');
				redirect('student');
				break;
			
			case 'submit2': // Προσωπικό
				redirect('staff');
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
	$footer_data['regs']=$this->session->userdata('fastgo_data');
	$this->load->view('include/footer', $footer_data);

	}

}