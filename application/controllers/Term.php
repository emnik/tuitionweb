<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Term extends CI_Controller {
	
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
    

    public function index(){
        $this->load->model('login_model');
		$user = $this->login_model->get_user_name($this->session->userdata('user_id'));
		$data['user'] = $user;

        $this->load->model('term_model');
        $termdata = $this->term_model->get_term_data();
        $data['term'] = $termdata;

        // $this->load->library('firephp');
        // $this->firephp->info($termdata);

		$this->load->view('include/header');
		$this->load->view('term', $data);
		$footer_data['regs']=true;
		$this->load->view('include/footer', $footer_data);
    }


    public function card($id) {

        if(is_null($id)) redirect('term');
    
        $this->load->model('login_model');
        $user=$this->login_model->get_user_name($this->session->userdata('user_id'));
        $data['user']=$user;

        //get term's data in an array
        $this->load->model('term_model');

        if (!empty($_POST)) {
            $date_fields = array('start','end');
			foreach ($_POST as $key => $value) 
			{
                if (in_array($key,$date_fields))
			    {
				    $value = implode('-', array_reverse(explode('-', $value)));	
				    if ($value=='0000-00-00') $value = null;
			    };
				$formdata[$key]=$value;
			};
            $this->term_model->update_term_data($formdata, $id);
            $formdata['id']=$id;
            $data['termdata'] = $formdata;
		}
		else 
		{
            $term = $this->term_model->get_term_data($id);
            $data['termdata']=$term[0];
		}


        
        // $this->load->library('firephp');
        // $this->firephp->info($term);

        $this->load->view('include/header');
        $this->load->view('term/card', $data);
        $footer_data['regs']=true;
        $this->load->view('include/footer', $footer_data);
    }

	public function delterm($id){
		$this->load->model('term_model');
		$this->term_model->delterm($id);
		redirect('term');
	}


	public function newterm(){
		$this->load->model('term_model');
		$id = $this->term_model->newterm();
		$this-> card($id);
	}

	public function cancel($form=null, $id=null){
	if (is_null($form) || is_null($id)) show_404();
		if ($form=='card'){
			$this->load->model('term_model');
			if($this->term_model->cancelreg($id))
			{
				redirect('term');
			}
			else
			{
				redirect('term/card/'.$id);
			}
		}
	}

}