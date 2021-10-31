<?php if (!defined('BASEPATH')) die();

class Finance_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }


   function get_payments($id) {
   		$query = $this-> db
   				 -> select('payment.*')
                -> from('payment')
                -> join('registration', 'registration.id = payment.reg_id')
               //  -> join('term', 'registration.term_id=term.id')
               //  -> where('term.active',1)
                -> where('payment.reg_id',$id)
   				 -> order_by('payment.apy_no')
   				 -> get();

   		if ($query->num_rows() > 0) {
   			foreach ($query->result_array() as $row) {
   				$payments[] = $row;
   			}
   			return $payments;
   		}
   		else
   		{
   			return false;
   		}
   }



   function update_payments($sortedformdata, $id){
      foreach ($sortedformdata as $key => $value) {
         if ($key > 0) // positive values represent the old records and negative the new ones
         {
            $this->db->where('payment.id',$key)
                     ->update('payment', $value); 
         }
         else
         {
            $value['reg_id'] = $id;
            $this->db->insert('payment', $value);
         };
      };
   }

  function get_last_apy_no(){
      $query = $this
         ->db
         ->select('apy_no')
         ->order_by('id', 'desc')
         ->limit(1)
         ->get('payment');


     if ($query->num_rows() > 0) 
      {
         return $query->row_array();  
      }
      else 
      {
         return false;
      };
   }

   function get_firstpay_data($id) {
         $query = $this-> db
                -> select(array('registration.month_price','registration.start_lessons_dt'))
                -> from('registration')
                -> where('registration.id',$id)
                ->limit(1)
                -> get();

     if ($query->num_rows() > 0) 
      {
         return $query->row_array();  
      }
      else 
      {
         return false;
      };
   }


   function del_payment($pay_id)
   {
    $this->db->delete('payment', array('id'=>$pay_id));
   }
   

   function cancel_payment($pay_id)
   {
    $data = array(
      'amount' => 0,
      'reg_id' => -1,
      'is_credit' => 0,
      'month_range' => 0,
      'notes' => 'ΑΚΥΡΗ',
      );

    $this->db->where('payment.id',$pay_id)
             ->update('payment', $data);
   }


   function move_payment($pay_id, $reg_id)
   {
   
      $data = array('reg_id' => $reg_id);
      $this->db->where('payment.id',$pay_id)
                ->update('payment', $data);
   }
   

//----------------------------CHANGES-----------------------

   function get_changes($id) {
      $query = $this-> db
           -> select('change.*')
           -> from('change')
           -> join('registration', 'registration.id = change.reg_id')
         //   -> join('term', 'registration.term_id=term.id')
         //   -> where('term.active',1)
           -> where('change.reg_id',$id)
           -> order_by('change.change_dt')
           -> get();

      if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row) {
          $changes[] = $row;
        }
        return $changes;
      }
      else
      {
        return false;
      }
   }


   function update_changes($sortedformdata, $id){
      foreach ($sortedformdata as $key => $value) {
         if ($key > 0) // positive values represent the old records and negative the new ones
         {
            $this->db->where('change.id',$key)
                     ->update('change', $value); 
         }
         else
         {
            $value['reg_id'] = $id;

            //I should remove the std_book_no from the change table...
            $query = $this-> db-> select('std_book_no')
                      -> from('registration')
                      -> where('registration.id', $id)
                      -> get();
            $value['std_book_no'] = $query->row()->std_book_no;

            $this->db->insert('change', $value);
         };
      };
   }




   function get_firstchange_data($id) {
         $query = $this-> db
                -> select(array('registration.month_price'))
                -> from('registration')
                -> where('registration.id',$id)
                ->limit(1)
                -> get();

     if ($query->num_rows() > 0) 
      {
         return $query->row_array();  
      }
      else 
      {
         return false;
      };
   }



   function del_change($change_id)
   {
    $this->db->delete('change', array('id'=>$change_id));
   }

}