<?php if (!defined('BASEPATH')) die();

Class Employee extends CI_Controller {
	
public function __construct() {
		
		parent::__construct();
	}


public function index(){
	/*
	If I want to pass a parameter to index through a uri segment then I would have to use
	a url such as: http://domain/tuitionweb/student/index/id
	It must have the index method specified!!! 
	*/
	redirect('staff');
}