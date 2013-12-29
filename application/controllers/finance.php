<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Finance extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		
	}
	

	public function index()
	{
		redirect('finance/schoolyear');
		// $this->load->model('login_model');
		// $user=$this->login_model->get_user_name($this->session->userdata('user_id'));
		// $data['user']=$user;

				
		// $this->load->model('welcome_model');
		// $startsch = $this->welcome_model->get_selected_startschyear(); 
		// $this->load->model('finance_model');
		// $schoolyear_update=$this->finance_model->get_dept_update_date($startsch);
		// $economicyear_update=$this->finance_model->get_economicyear_update_date($startsch);
		// if ($schoolyear_update) $data['schoolyear_update'] = $schoolyear_update;
		// if ($economicyear_update) $data['economicyear_update'] = $economicyear_update;

		// $this->load->view("include/header");
		// $this->load->view("finance", $data);
		// $this->load->view("include/footer");
	}


	public function getschfinancedata(){
		//To populate the schoolyear finance data table in the finance sumary view
		//via ajax with databables
		header('Content-Type: application/x-json; charset=utf-8');
		
		$this->load->model('welcome_model');
		$startsch = $this->welcome_model->get_selected_startschyear(); 

		$this->load->model('finance_model','', TRUE);
		$schyearfinance = $this->finance_model->get_schoolyear_finance($startsch);

		//return results
		$tableData=array('aaData'=>$schyearfinance);
		echo json_encode($tableData);
	}


	public function getecofinancedata(){
		//To populate the economic year finance data table in the finance sumary view
		//via ajax with databables
		header('Content-Type: application/x-json; charset=utf-8');

		$this->load->model('welcome_model');
		$startsch = $this->welcome_model->get_selected_startschyear(); 
		
		$this->load->model('finance_model','', TRUE);
		$ecoyearfinance = $this->finance_model->get_economicyear_finance($startsch);

		//return results
		$tableData=array('aaData'=>$ecoyearfinance);
		echo json_encode($tableData);
	}



	public function update_schfinance_data(){
		//To recalculate the schoolyear's financial data
		header('Content-Type: application/x-json; charset=utf-8');
		
		//DEBUGGING
		// $this->load->library('firephp');
		// $this->firephp->info($_POST);


		//options
		$chkCurMonthState=0;
		$chk0PayState=0;
		if (!empty($_POST['chkCurMonthState'])) $chkCurMonthState=1;
		if (!empty($_POST['chk0PayState'])) $chk0PayState=1;
		// ----------------------------------------------------------//
		

		//1. get Start School Year from table lookup stored value
		// ----------------------------------------------------------//
		$this->load->model('welcome_model');
		$startsch = $this->welcome_model->get_selected_startschyear(); 

		
		//2. check if we are in the selected year or a next one
		$PrevSchoolYearSelected = 0; //false
		if ((date('Y')> $startsch and date('m')>=8) or (date('Y')>$startsch+1)){
			$PrevSchoolYearSelected = 1; //true
		}

		//3. generate the monthset for finance data to be calculated
		// ----------------------------------------------------------//

		//Να λαμβάνεται ο τρέχων μήνας;
		//$chkCurMonthState = 0; //false

		if ($PrevSchoolYearSelected == 0) {
			if ($chkCurMonthState==1) 
			{
				$endmonth = date('m');
			}
			else
				{
					if (date('m') <> 1) 
					{
						$endmonth = date('m') - 1;
					}
					else
					{
						$endmonth = 12;
					}
				}
	
		//Φτιάχνω το πίνακα με τους μήνες από τον 8ο μέχρι τον τρέχων (ή τον προηγούμενο) 
		$c = 0;
			if (date('Y') == $startsch)
			{
				for ($m = 8; $m<=$endmonth; $m++)
				{ 
					$monthset[$c] = $m;
					$c = $c + 1;
				}
			}
			else
			{
				for ($m = 8; $m<=12; $m++)
				{
					$monthset[$c] = $m;
					$c = $c + 1;
				}
				if ($endmonth <> 12) { 
					for ($m =1; $m<=$endmonth;$m++)
					{ 
						$monthset[$c] = $m;
						$c = $c + 1;
					}
				}
			}
		}
		else //previous year selected so monthset is 8, 9, 10 , 11, 12, 1, 2, 3, 4, 5, 6, 7
		{
			$c=0;
			for ($m=8; $m<=12; $m++)
			{
				$monthset[$c] = $m;
				$c=$c+1;
			}
			for ($m=1;$m<=7;$m++)
			{
				$monthset[$c] = $m;
				$c=$c+1;
			}
			$endmonth = 7;
		}

		//4. Empty (reset) existing tables dept & post_payment
		// ----------------------------------------------------------//
		$this->load->model('finance_model');
		$this->finance_model->resetSchFinanceTables();

		//5. Insert single months' payments in the post_payment table
		$this->finance_model->insertSinglePays($startsch);

		//6. Get multiple payments, seperate them and insert them in the post_payment table
		$multipleData = array();
		$c=0;
		$multiplePays = $this->finance_model->getMultiplePays($startsch);
		if ($multiplePays){
			foreach ($multiplePays as $data) {
				$months = explode(',', $data['month_range']);
				for ($m=0; $m<=count($months)-1; $m++){
					$multipleData[$c]=$data;
					$multipleData[$c]['month_num']=$months[$m];
					$multipleData[$c]['amount']=$data['amount']/count($months); //the total amount for multiple months is divided equally to each month...
					unset($multipleData[$c]['month_range']);
					unset($multipleData[$c]['month_price']);
					$c++;
				}
			}
			$this->finance_model->insertMultiplePays($multipleData);
		}

		//7. Get data in table debt
		$this->finance_model->populateDebt($monthset, $endmonth, $startsch);

	 	//ΓΙΑ ΔΙΑΓΡΑΦΗ ΜΗΔΕΝΙΚΩΝ ΟΦΕΙΛΩΝ (ΔΩΡΕΑΝ ΠΟΥ ΔΕΝ ΕΧΟΥΜΕ ΚΟΨΕΙ ΑΠΟΔΕΙΞΗ...)
		if ($chk0PayState==1){
			$this->finance_model->delzerodebts();		
		}

		//8. Update table debt with month payment changes
		$r = $this->finance_model->UpdateDebtChanges($monthset);
		
		//9. Set the update date
		$this->finance_model->schFinanceUpdateDate($startsch);

		//return results
		echo json_encode($r);
	}



	public function update_ecofinance_data(){
		//To recalculate the economic year's financial data
		header('Content-Type: application/x-json; charset=utf-8');

		$this->load->model('welcome_model');
		$startsch = $this->welcome_model->get_selected_startschyear(); 

		//1. reset finance_year_debt and finance_year_pays
		$this->load->model('finance_model');
		$this->finance_model->resetEcoFinanceTables();

		//2. Insert single payments
		$this->finance_model->insertEcoSinglePays($startsch);
		
		//3. Get multiple payments, seperate them and insert them in the finance_year_pay table
		$multipleDataPays = array();
		$c=0;
		$multiplePays = $this->finance_model->multiEcoPaysSelect($startsch);
		if ($multiplePays){
			foreach ($multiplePays as $data) {
				$months = explode(',', $data['month_range']);
				for ($m=0; $m<=count($months)-1; $m++){
					$multipleDataPays[$c]=$data;
					$multipleDataPays[$c]['paid_month']=$months[$m];
					$multipleDataPays[$c]['amount']=$data['amount']/count($months); //the total amount for multiple months is divided equally to each month...
					unset($multipleDataPays[$c]['month_range']);
					unset($multipleDataPays[$c]['month_price']);
					$c++;
				}
			}
			$this->finance_model->insertMultipleEcoPays($multipleDataPays);
		}

		//4. Insert Single debts
		$this->finance_model->insertEcoSingleDebt($startsch);


		//5. Get multiple payments, seperate them and insert them in the finance_year_debt table
		$multipleDataDebts = array();
		$c=0;
		$multiplePays = $this->finance_model->multiEcoPaysSelect($startsch);
		if ($multiplePays){
			foreach ($multiplePays as $data) {
				$months = explode(',', $data['month_range']);
				for ($m=0; $m<=count($months)-1; $m++){
					$multipleDataDebts[$c]=$data;
					$multipleDataDebts[$c]['credit_month']=$months[$m];
					$multipleDataDebts[$c]['amount']=$data['amount']/count($months); //the total amount for multiple months is divided equally to each month...
					unset($multipleDataDebts[$c]['month_range']);
					unset($multipleDataDebts[$c]['month_price']);
					$c++;
				}
			}
			$this->finance_model->insertMultipleEcoDebts($multipleDataDebts);
		}

		//9. Set the update date
		$this->finance_model->schEcoFinanceUpdateDate($startsch);

		//return results
		echo json_encode(true);
	}

	//----------------------schoolyear view----------------------------------//
	public function schoolyear()
	{
		$this->load->model('login_model');
		$user=$this->login_model->get_user_name($this->session->userdata('user_id'));
		$data['user']=$user;

		//the following are from summary...
		$this->load->model('welcome_model');
		$startsch = $this->welcome_model->get_selected_startschyear(); 
		$this->load->model('finance_model');
		$schoolyear_update=$this->finance_model->get_dept_update_date($startsch);
		$economicyear_update=$this->finance_model->get_economicyear_update_date($startsch);
		if ($schoolyear_update) $data['schoolyear_update'] = $schoolyear_update;
		if ($economicyear_update) $data['economicyear_update'] = $economicyear_update;
		//until here

		$this->load->view('include/header');
		$this->load->view('finance/schoolyear', $data);
		$this->load->view('include/footer');
	}


	public function getreport1data()
	{
		header('Content-Type: application/x-json; charset=utf-8');

		$this->load->model('welcome_model');
		$startsch = $this->welcome_model->get_selected_startschyear(); 

		$this->load->model('finance_model');
		$res = $this->finance_model->getreport1data($startsch);
		
		//return results
		echo json_encode($res);
	}


	public function getreport2data()
	{
		header('Content-Type: application/x-json; charset=utf-8');

		$this->load->model('welcome_model');
		$startsch = $this->welcome_model->get_selected_startschyear();

		$this->load->model('finance_model');
		$res = $this->finance_model->getreport2data($startsch);
		
		//return results
		echo json_encode($res);
	}

}