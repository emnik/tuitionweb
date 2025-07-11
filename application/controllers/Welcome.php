<?php if (!defined('BASEPATH')) die();

Class Welcome extends CI_Controller {
	
public function __construct() {
		
		parent::__construct();
		// $this->load->library('firephp');
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

public function user_list() {
    $this->load->model('welcome_model');

    $termid = $this->welcome_model->get_termid();
    $list = $this->welcome_model->get_student_names_ids($this->input->get('q'));

    if ($list) {
        $currentStds = [];
        $prevStds = [];
        $teachers = [];
        $data = [];

        foreach ($list as $stud) {
            $studentData = [
                "id" => $stud['id'],
                "text" => $stud['stdname']
            ];

            if ($stud['role'] === 'student') {
                // Group students based on the term
				$studentData['group'] = 'Μαθητές';
                if ($stud['termid'] == $termid) {
                    $currentStds[] = $studentData;
                } else {
                    $studentData['text'] .= ' - ' . $stud['termname']; // Add term name for previous students
					$prevStds[] = $studentData;
                }
            } else {
				$studentData['group'] = 'Καθηγητές';
				$teachers[] = $studentData;
            }
        }

        // Organize data into Select2 nested structure
		if (!empty($currentStds)) {
            $data[] = ["text" => "Μαθητές - Επιλεγμένη διαχ. περίοδος", "children" => $currentStds];
        }
        if (!empty($prevStds)) {
            $data[] = ["text" => "Μαθητές - Υπόλοιπες διαχ. περίοδοι", "children" => $prevStds];
        }
        if (!empty($teachers)) {
            $data[] = ["text" => "Καθηγητές", "children" => $teachers];
        }
	} else {
        $data = [[
            "id" => "0",
            "text" => "Κανένα αποτέλεσμα..."
        ]];
    }

    // Send the response as JSON with UTF-8 encoding
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}


public function social_media(){
	//needed for the footer!
	header('Content-Type: application/x-json; charset=utf-8');
	$this->load->model('welcome_model');
	$data = $this->welcome_model->get_school_data();
	echo(json_encode($data));
}


public function index() {
	//$this->output->enable_profiler(TRUE);
	
	$this->load->model('login_model');
	$user_id = $this->session->userdata('user_id');
	$data['user']=$this->login_model->get_user_name($user_id);

	$this->load->model('Theme_model');
    $theme = $this->Theme_model->get_user_theme($user_id);
	if (isset($theme['id'])) {
		$this->session->set_userdata(array('current_theme_id' => $theme['id'])); // Store the current theme ID in session
	}
	        

	$this->load->model('welcome_model');

	$schoolyears=$this->welcome_model->get_schoolyears();
	if ($schoolyears) {
		$data['schoolyears'] = $schoolyears;
		foreach($schoolyears as $termdata){
			if($termdata['active']==1){
				$termname= $termdata['name'];
				$this->session->set_userdata(array('startsch' => $termname)); // όνομα διαχειριστικής περιόδου
			}
		}
		}

	$schooldata = $this->welcome_model->get_school_data();
	$data['school']=$schooldata;

	$startsch = $this->input->post('startschoolyear');

	if (!empty($startsch)) {
					$this->welcome_model->set_schoolyear($startsch);	
					$schoolyears=$this->welcome_model->get_schoolyears();
					if ($schoolyears) {
						$data['schoolyears'] = $schoolyears;
						foreach($schoolyears as $termdata){
							if($termdata['active']==1){
								$termname= $termdata['name'];
								$this->session->set_userdata(array('startsch' => $termname)); // όνομα διαχειριστικής περιόδου
							}
						}
						}

		switch ($this->input->post('submitbtn')) {
			case 'submit0': //Διαχειριστικές Περίοδοι
				redirect('term');
				break;
				
			case 'submit1': //Μαθητολόγιο
				redirect('student');
				break;
			
			case 'submit2': // Προσωπικό
				redirect('staff');
				break;

			case 'submit3': // Τμήματα
				redirect('section');
				break;

			case 'submit4': // Οικονομικά
				redirect('finance');
				break;

			case 'submit5': // Πρόγραμμα Σπουδών
				redirect('curriculum');
				break;

			case 'submit6': // Διαγωνίσματα
				redirect('exam');
				break;

			case 'submit7': // Τηλεφωνικοί Κατάλογοι
				redirect('telephones');
				break;

			case 'submit8': // Ιστορικό
				redirect('history');
				break;		

			case 'submit9': // Αναφορές
				redirect('reports');
				break;							
			
			case 'submit10': // Ημερήσιο Πρόγραμμα
				redirect('schedule/index/'.date('w'));//date('w') returns 0..6 with Sunday=0
				break;			

			case 'submit11': // Στοιχεία φροντιστηρίου
				redirect('school');
				break;	

			case 'submit12': // Διαγωνίσματα
				redirect('exam');
				break;	

			case 'submit13': // Λογαριασμοί Χρηστών
				redirect('user');
				break;					

			case 'submit14': // Ρυθμίσεις επικοινωνίας
				redirect('contact_config');
				break;	

			case 'submit15': // Eπικοινωνία
				redirect('communication');
			break;						

			case 'submit16': // Microsoft Teams
				redirect('teams');
			break;

		}

	}

	$this->load->view('include/header');
	$this->load->view('welcome', $data);
	$footer_data['regs']=true;
	$this->load->view('include/footer', $footer_data);

	}

	public function logout()
	{
		$this->session->sess_destroy();
		$data['login_failed'] = false;
		$this->load->view('include/header');		
		$this->load->view('login', $data);
		$this->load->view('include/footer');
	}


}