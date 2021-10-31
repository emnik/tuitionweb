<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Finance extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
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
	

	// public function index()
	// {
	// 	//maybe I'll think something good for finance summary... but for now:
	// 	redirect('finance/schoolyear');
	// }



	public function update_schfinance_data(){
		//To recalculate the schoolyear's financial data
		header('Content-Type: application/x-json; charset=utf-8');
		
		//DEBUGGING
		// $this->load->library('firephp');
		// $this->firephp->info($_POST);


		//options
		$chkCurMonthState = 0;
		$chk0PayState = 0;
		// if (array_key_exists('chkCurMonthState', $_POST)) $chkCurMonthState=1;
		if (!empty($_POST['chkCurMonthState'])) $chkCurMonthState=1;
		if (!empty($_POST['chk0PayState'])) $chk0PayState=1;
		// ----------------------------------------------------------//
		

		//1. get Start School Year from table lookup stored value
		// ----------------------------------------------------------//
		// $startsch=$this->session->userdata('startsch');
		$this->load->model('finance_model');
		$termdata = $this->finance_model->get_term_data();
		if($termdata['startyear'] == $termdata['endyear']){
			$yearspan=1;
		}
		else {
			$yearspan=2;
		}
		
		// 2. check if we are in the selected year or a next one
		// $PrevSchoolYearSelected = 0; //false
		// if ((date('Y')> $startsch and date('m')>=8) or (date('Y')>$startsch+1)){
		// 	$PrevSchoolYearSelected = 1; //true
		// }


		//3. generate the monthset for finance data to be calculated
		// ----------------------------------------------------------//

		//Φτιάχνω το πίνακα με τους μήνες
		$c=0;
		if ($yearspan==1){
			if(date('m')> $termdata['endmonth']) {
				$endmonth = $termdata['endmonth'];
			} 
			else 
			{
				if ($chkCurMonthState==1) {
					$endmonth= date('m');
				}
				else	
				{
					$endmonth = date('m') - 1;
				}
			}
		
			for ($m=$termdata['startmonth']; $m<=$endmonth; $m++){
				$monthset[$c] = $m;
				$c++;
			}
		}
		else { //yearspan=2
			if(date('Y')==$termdata['startyear']){
				
				if ($chkCurMonthState==1) {
					$endmonth= date('m');
				}
				else	
				{
					$endmonth = date('m') - 1;
				}
				for ($m = $termdata['startmonth']; $m<=$endmonth; $m++)
				{
					$monthset[$c] = $m;
					$c = $c + 1;
				}
			} else //we are in the endyear 
			{
				for ($m = $termdata['startmonth']; $m<=12; $m++)
				{
					$monthset[$c] = $m;
					$c = $c + 1;
				}
				if(date('m')> $termdata['endmonth']) {
					$endmonth = $termdata['endmonth'];
				} 
				else 
				{
					if ($chkCurMonthState==1) {
						$endmonth= date('m');
					}
					else	
					{
						$endmonth = date('m') - 1;
					}
				}
				for ($m =1; $m<=$endmonth;$m++)
				{ 
					$monthset[$c] = $m;
					$c = $c + 1;
				}
			}

		}
		// $this->load->library('firephp');
		// $this->firephp->info($monthset);

		//4. Empty (reset) existing tables dept & post_payment
		// ----------------------------------------------------------//
		
		$this->finance_model->resetSchFinanceTables();

		//5. Insert single months' payments in the post_payment table
		$this->finance_model->insertSinglePays();

		//6. Get multiple payments, seperate them and insert them in the post_payment table
		$multipleData = array();
		$c=0;
		$multiplePays = $this->finance_model->getMultiplePays();
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
		$r=$this->finance_model->populateDebt($monthset, $endmonth, $yearspan, $termdata);

	 	//ΓΙΑ ΔΙΑΓΡΑΦΗ ΜΗΔΕΝΙΚΩΝ ΟΦΕΙΛΩΝ (ΔΩΡΕΑΝ ΠΟΥ ΔΕΝ ΕΧΟΥΜΕ ΚΟΨΕΙ ΑΠΟΔΕΙΞΗ...)
		if ($chk0PayState==1){
			$this->finance_model->delzerodebts();		
		}

		//8. Update table debt with month payment changes
		// $r = $this->finance_model->UpdateDebtChanges($monthset); //NEEDES REVIEW!!!!!!!!!!!!!
		
		//9. Set the update date
		$this->finance_model->schFinanceUpdateDate();

		//return results
		echo json_encode($r);
	}



	public function update_ecofinance_data(){
		//To recalculate the economic year's financial data
		header('Content-Type: application/x-json; charset=utf-8');


		$startsch=$this->session->userdata('startsch');

		//options
		$chk0PayState=0;
		if (!empty($_POST['chk0PayState'])) $chk0PayState=1;

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

	 	//ΓΙΑ ΔΙΑΓΡΑΦΗ ΜΗΔΕΝΙΚΩΝ ΟΦΕΙΛΩΝ (ΔΩΡΕΑΝ ΠΟΥ ΔΕΝ ΕΧΟΥΜΕ ΚΟΨΕΙ ΑΠΟΔΕΙΞΗ...)
		if ($chk0PayState==1){
			$this->finance_model->delzeroyeardebts();		
		}

		//9. Set the update date
		$this->finance_model->schEcoFinanceUpdateDate($startsch);

		//return results
		echo json_encode(true);
	}

	//----------------------schoolyear view----------------------------------//
	public function index() //schoolyear()
	{
		$this->load->model('login_model');
		$user=$this->login_model->get_user_name($this->session->userdata('user_id'));
		$data['user']=$user;


		// $startsch=$this->session->userdata('startsch');

		$this->load->model('finance_model');
		$schoolyear_update=$this->finance_model->get_dept_update_date();
		if ($schoolyear_update) $data['schoolyear_update'] = $schoolyear_update;
		
		$this->load->view('include/header');
		$this->load->view('finance/schoolyear', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);
	}


	public function getschoolreport1data()
	{
		header('Content-Type: application/x-json; charset=utf-8');

		// $startsch=$this->session->userdata('startsch');

		$this->load->model('finance_model');
		$res = $this->finance_model->getschoolreport1data();
		
		//return results
		echo json_encode($res);
	}


	public function getschoolreport2data()
	{
		header('Content-Type: application/x-json; charset=utf-8');

		// $startsch=$this->session->userdata('startsch');

		$this->load->model('finance_model');
		$res = $this->finance_model->getschoolreport2data();
		
		//return results
		echo json_encode($res);
	}



	public function getschfinancedata(){
	//report #3
		header('Content-Type: application/x-json; charset=utf-8');
		
		// $startsch=$this->session->userdata('startsch');

		$this->load->model('finance_model','', TRUE);
		$schyearfinance = $this->finance_model->get_schoolyear_finance();

		// $this->load->library('firephp');
		// $this->firephp->info($schyearfinance);

		//return results
		$tableData=array('aaData'=>$schyearfinance);
		echo json_encode($tableData);
	}


	//----------------------economicyear view----------------------------------//

	public function economicyear()
	{
		$this->load->model('login_model');
		$user=$this->login_model->get_user_name($this->session->userdata('user_id'));
		$data['user']=$user;

		$startsch=$this->session->userdata('startsch');

		$this->load->model('finance_model');
		$economicyear_update=$this->finance_model->get_economicyear_update_date($startsch);
		if ($economicyear_update) $data['economicyear_update'] = $economicyear_update;

		$this->load->view('include/header');
		$this->load->view('finance/economicyear', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);
	}

	public function getecofinancedata(){
		//report #1
		header('Content-Type: application/x-json; charset=utf-8');

		$startsch=$this->session->userdata('startsch');
		
		$this->load->model('finance_model','', TRUE);
		$ecoyearfinance = $this->finance_model->get_economicyear_finance($startsch);

		//return results
		$tableData=array('aaData'=>$ecoyearfinance);
		echo json_encode($tableData);
	}

	public function getecoreport2data()
	{
		header('Content-Type: application/x-json; charset=utf-8');

		$startsch=$this->session->userdata('startsch');

		$this->load->model('finance_model');
		$res = $this->finance_model->getecoreport2data($startsch);
		
		//return results
		echo json_encode($res);
	}

	public function getecoreport3data()
	{
		header('Content-Type: application/x-json; charset=utf-8');

		$startsch=$this->session->userdata('startsch');

		$this->load->model('finance_model');
		$res = $this->finance_model->getecoreport3data($startsch);
		
		//return results
		echo json_encode($res);
	}

}