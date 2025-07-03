<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Telephones extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$session_user = $this->session->userdata('is_logged_in');
		if (!empty($session_user)) {
			// get the group and redirect to appropriate controller
			$this->load->model('login_model');
			$grp = $this
				->login_model
				->get_user_group($this->session->userdata('user_id'));

			switch ($grp->name) {
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
		} else {
			redirect('login');
		}
	}


	public function index()
	{
		redirect('telephones/catalog');
	}

	// ------------------------------------telephones catalog (students / teachers)--------------------------
	public function catalog()
	{
		$this->load->model('login_model');
		$user = $this->login_model->get_user_name($this->session->userdata('user_id'));
		$data['user'] = $user;

		$startsch = $this->session->userdata('startsch');

		$this->load->view('include/header');
		$this->load->view('reports/telephones', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);
	}

	public function getstudentphones()
	{
		header('Content-Type: application/x-json; charset=utf-8');

		$this->load->model('reports/telephones_model');
		$res = $this->telephones_model->get_phonecatalog();
		echo json_encode($res);
	}

	public function getemployeephones()
	{
		header('Content-Type: application/x-json; charset=utf-8');

		$this->load->model('reports/telephones_model');
		$res = $this->telephones_model->get_employeephones();

		//return results
		echo json_encode($res);
	}

	// ------------------------------------telephones exports (bulkSMS / Google contacts)--------------------------	


	public function exports()
	{
		$this->load->model('login_model');
		$user = $this->login_model->get_user_name($this->session->userdata('user_id'));
		$data['user'] = $user;

		$startsch = $this->session->userdata('startsch');

		$this->load->view('include/header');
		$this->load->view('reports/telephone_exports', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);
	}


	public function getGoogleData()
	{
		$filename = "gcontacts_export.csv";
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=$filename");

		$this->load->model('reports/telephones_model');
		$res = $this->telephones_model->google_export_data();

		// use ob_clean() to clean (erase) the output buffer. If not, I get blank lines at the start!!!
		ob_clean();
		$file = fopen('php://output', 'w');

		$header = array(
			"Name",
			"Given Name",
			"Family Name",
			"Notes",
			"Group Membership",
			"Address 1 - Type",
			"Address 1 - Formatted",
			"Address 1 - Street",
			"Address 1 - City",
			"Phone 1 - Type", "Phone 1 - Value",
			"Phone 2 - Type", "Phone 2 - Value",
			"Phone 3 - Type", "Phone 3 - Value"
		);
		fputcsv($file, $header);

		foreach ($res['google'] as $row) {
			$valuesArray = array();
			foreach ($row as $name => $value) {
				$valuesArray[] = $value;
			}
			fputcsv($file, $valuesArray);
		}

		fclose($file);
		exit;
	}

}
