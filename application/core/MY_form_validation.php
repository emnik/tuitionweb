 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

 class MY_Form_validation extends CI_Form_validation {

   public function __construct($rules = array())
  {
   parent::__construct($rules);
   $this->lang->load('form_validation','greek');

  }
   function alpha_greek($str)
  {
   return ( ! preg_match("/^[-αάΆΑβΒγΓδΔεέΕΈζΖηήΗΉθΘιΙΊκΚλΛμΜνΝξΞοόΟΌπΠρΡσΣτΤυύΥΎφΦχΧψΩωώΩΏ_]+$/", $str)) ? FALSE : TRUE;
  } 
 
  }


?>  