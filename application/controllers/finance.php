<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Finance extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		
	}
	

	public function index()
	{

		$this->load->model('login_model');
		$user=$this->login_model->get_user_name($this->session->userdata('user_id'));
		$data['user']=$user;

		$this->load->model('finance_model');
		$schoolyear_update=$this->finance_model->get_dept_update_date();
		if ($schoolyear_update) $data['schoolyear_update'] = $schoolyear_update;

		// $this->load->library('firephp');
		// $this->firephp->info($schoolyear_update);
		// $this->firephp->info($user);

		$this->load->view("include/header");
		$this->load->view("finance", $data);
		$this->load->view("include/footer");
	}


	public function getschfinancedata(){
		header('Content-Type: application/x-json; charset=utf-8');
		$this->load->model('finance_model','', TRUE);
		$schyearfinance = $this->finance_model->get_schoolyear_finance();

		//return results
		$tableData=array('aaData'=>$schyearfinance);
		echo json_encode($tableData);
	}


	public function getecofinancedata(){
		header('Content-Type: application/x-json; charset=utf-8');
		$this->load->model('finance_model','', TRUE);
		$ecoyearfinance = $this->finance_model->get_economicyear_finance();

		//return results
		$tableData=array('aaData'=>$ecoyearfinance);
		echo json_encode($tableData);
	}



}