<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Finance_model extends CI_Model
{
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


    public function get_dept_update_date($startsch){

		$existingdata = $this->db->select(array('value_1','value_2'))->from('lookup')->where('id',3)->get();
    	$row = $existingdata->row();

	    if ($row->value_2 == $startsch) 
	    {
	       return $row->value_1; 
	    }
	    else 
	    {
	       return false;
	    }
    }


    function get_schoolyear_finance($startsch){
    	$existingdata = $this->db->select(array('value_1','value_2'))->from('lookup')->where('id',3)->get();
    	$row = $existingdata->row();
    	if ($row->value_2 == $startsch ){
	    	$query=$this->db->select(array('Μήνας', 'Οφειλές', 'Εισπράξεις', 'Τζίρος'))
	    			->from('vw_finance_schoolyear')
	    			->get();

	    	if ($query->num_rows() > 0) 
			{
				foreach($query->result_array() as $row) 
				{
					$schfinance[] = $row;
				}
				return $schfinance;
			}
			else 
			{
				return false;
			}
    	}
    	else
    	{
    		return false;
    	}
    }

    public function get_economicyear_update_date($startsch){

		$existingdata = $this->db->select(array('value_1','value_2'))->from('lookup')->where('id',4)->get();
    	$row = $existingdata->row();

	    if ($row->value_2 == $startsch) 
	    {
	       return $row->value_1; 
	    }
	    else 
	    {
	       return false;
	    }
    }


    function get_economicyear_finance($startsch){
    	$existingdata = $this->db->select(array('value_1','value_2'))->from('lookup')->where('id',4)->get();
    	$row = $existingdata->row();
    	if ($row->value_2 == $startsch ){
	    	$query=$this->db->select(array('Μήνες', 'Ποσό', 'Κατηγορία'))
	    			->from('vw_finance_year')
	    			->order_by('Μήνες')
	    			->order_by('Κατηγορία', 'desc')
	    			->get();

	    	if ($query->num_rows() > 0) 
			{
				foreach($query->result_array() as $row) 
				{
					$ecofinance[] = $row;
				}
				return $ecofinance;
			}
			else 
			{
				return false;
			}
		}
		else
		{
			return false;
		}
    }

    //---------------------------------------------------------------------//
    //------------FUNCTIONS FOR SCHOOLYEAR FINANCE RECALCULATION-----------//
    //---------------------------------------------------------------------//


    public function resetTables(){
    	$this->db->truncate('debt');
    	$this->db->truncate('post_payment');
    }


    public function insertSinglePays($startsch){
    	$this->db->query("INSERT INTO `post_payment` (`reg_id`, `month_num`, `amount`, `is_credit`)
						   SELECT `payment`.`reg_id`,`payment`.`month_range`, `payment`.`amount`, `payment`.`is_credit`
						   FROM `payment`, `registration` 
						   WHERE `payment`.`reg_id` = `registration`.`id` AND 
						   LENGTH(`payment`.`month_range`)<=2 AND 
						   ((YEAR(`registration`.`start_lessons_dt`)=".$this->db->escape($startsch)." AND MONTH(`registration`.`start_lessons_dt`)>=8) 
						   OR (YEAR(`registration`.`start_lessons_dt`)=".$this->db->escape($startsch+1)." AND MONTH(`registration`.`start_lessons_dt`)<=7))");
    }


    public function getMultiplePays($startsch){
    	$query = $this->db->query("SELECT `payment`.`reg_id`,`payment`.`month_range`, `payment`.`amount`, `payment`.`is_credit`, `registration`.`month_price`
									FROM `payment`, `registration`
									WHERE `payment`.`reg_id` = `registration`.`id` AND
									LENGTH(`payment`.`month_range`)>2 AND
									((YEAR(`registration`.`start_lessons_dt`)=".$this->db->escape($startsch)." AND MONTH(`registration`.`start_lessons_dt`)>=8)
									OR (YEAR(`registration`.`start_lessons_dt`)=".$this->db->escape($startsch+1)." AND MONTH(`registration`.`start_lessons_dt`)<=7))");
    	
    	if ($query->num_rows() > 0) 
		{
			foreach($query->result_array() as $row) 
			{
				$multiplePays[] = $row;
			}
			return $multiplePays;
		}
		else 
		{
			return false;
		}
    }


    public function insertMultiplePays($multipleData)
    {
    	$this->db->insert_batch('post_payment', $multipleData);

    }


    public function populateDebt($monthset, $endmonth, $startsch)
    {
    	foreach ($monthset as $monthnum) {
    		if ($monthnum<>0)
    		{
    			//Εισάγουμε στο πίνακα debt όλους εκείνους τους μαθητές του τρέχοντος σχολικού έτους, που για τον εκάστοτε μήνα από το σετ δεν έχουν αντίστοιχη εγγραφή στο πίνακα πληρωμών.
				//Σε αυτό το σημείο δε μας ενδιαφέρει πιο μήνα γράφτηκε ο μαθητής ή αν και πότε διαγράφηκε κλπ. Θα γίνουν αργότερα οι απαραίτητες διαγραφές!
    			$this->db->query("INSERT INTO `debt`(`reg_id`, `amount`)
						SELECT `id`, `month_price` FROM `registration`
						WHERE ((YEAR(`registration`.`start_lessons_dt`)=".$this->db->escape($startsch). " AND MONTH(`registration`.`start_lessons_dt`)>=8) 
						OR (YEAR(`registration`.`start_lessons_dt`)=".$this->db->escape($startsch+1)." AND MONTH(`registration`.`start_lessons_dt`)<=7))
						AND NOT EXISTS (SELECT * FROM `post_payment` WHERE `post_payment`.`reg_id` = `registration`.`id` AND `post_payment`.`is_credit` = '0' AND `post_payment`.`month_num` = ".$this->db->escape($monthnum).")");

				//Ενημερώνουμε το πεδίο <Μήνας> του πίνακα tbldebt για το μήνα που αναφερόμαστε...     		
    			$this->db->query("UPDATE `debt` SET `month_num` = ".$this->db->escape($monthnum)." WHERE `month_num` IS NULL AND NOT EXISTS (SELECT * FROM `post_payment` WHERE `post_payment`.`reg_id` = `debt`.`reg_id` AND `post_payment`.`is_credit` = '0' AND `post_payment`.`month_num` = ".$this->db->escape($monthnum).")");
    		}
    		else
    		{
    			break;
    		}

    	}

    	//Διαγραφή όσων έχουν εγγραφές στο πίνακα οφειλών για μήνες προ εγγραφής και για μήνες μετά τη διαγραφή!

		if ($endmonth >=1 and $endmonth <=7) { //'Αν βρισκόμαστε στο νέο έτος...
			//[*] Η ακόλουθη διαγραφή αφορά όσους <γράφτηκαν> το προηγούμενο έτος. Όταν βρισκόμαστε σε μήνα του τρέχοντος έτους π.χ 2ο οι εγγραφές στο πίνακα οφειλών περιέχουν τους μήνες 8,9,10,11,12,1,2
			//Για κάποιον λοιπόν που γράφτηκε το 10ο πρέπει να διαγραφούν οι εγγραφές για μήνες 8 & 9. Άρα η συνθήκη είναι > τρέχων μήνα ΚΑΙ < μήνα έναρξης (>2 ΚΑΙ <10)
			//(The next is a trick to resolve the above issue. Instead of < FROM `tbldebt` > I use < FROM (SELECT * FROM `tbldebt`) AS `temp` >)
			$this->db->query("DELETE FROM `debt` WHERE `id` IN (SELECT `debt`.`id` FROM (SELECT * FROM `debt`) AS `temp`, `registration` WHERE `debt`.`reg_id` = `registration`.`id` AND ((`debt`.`month_num` < MONTH(`registration`.`start_lessons_dt`) AND `debt`.`month_num` > ".$this->db->escape($endmonth).")))");

			
			//[*] Διαγραφή όσων έχουν εγγραφές για μήνες μετά τη διαγραφή τους από το μαθητολόγιο
			//[-] Αν η διαγραφή από το μαθητολόγιο έγινε τους μήνες 1-7 (νέο έτος) τότε σβήνω τις οφειλές για τους μήνες μετά τη διαγραφή του μαθητολογίου και μέχρι τον τρέχων
			$this->db->query("DELETE FROM `debt` WHERE `debt`.`id` IN (SELECT `temp`.`id` FROM (SELECT * FROM `debt`) AS `temp` JOIN `registration` ON (`temp`.`reg_id` = `registration`.`id`) WHERE (MONTH(`del_lessons_dt`) >=1 AND MONTH(`del_lessons_dt`) <=7) AND (`temp`.`month_num` > MONTH(`del_lessons_dt`) AND `temp`.`month_num` <= ".$this->db->escape($endmonth)."))");
			
			//[*] Διαγραφή όσων γράφτηκαν και διέκοψαν την ίδια μέρα.
			$this->db->query("DELETE FROM `debt` WHERE `debt`.`id` IN (SELECT `temp`.`id` FROM (SELECT * FROM `debt`) AS `temp` JOIN `registration` ON (`temp`.`reg_id` = `registration`.`id`) WHERE `del_lessons_dt` = `start_lessons_dt`)");


			//--> Αν η διαγραφή από το μαθητολόγιο έγινε σε μήνα >=8 (προηγούμενο έτος) τότε σβήνω τις οφειλές για τους μήνες μετά τη διαγραφή του μαθητολογίου και μέχρι και το Δεκέμβριο
			$this->db->query("DELETE FROM `debt` WHERE `debt`.`id` IN (SELECT `temp`.`id` FROM (SELECT * FROM `debt`) AS `temp` JOIN `registration` ON (`temp`.`reg_id` = `registration`.`id`) WHERE (MONTH(`del_lessons_dt`) >=8) AND (`temp`.`month_num` > MONTH(`del_lessons_dt`) AND `temp`.`month_num`<= 12 ))");
			
			//--> --> όπως επίσης και από το Δεκέμβριο μέχρι τον τρέχων μήνα.
			$this->db->query("DELETE FROM `debt` WHERE `debt`.`id` IN (SELECT `temp`.`id` FROM (SELECT * FROM `debt`) AS `temp` JOIN `registration` ON (`temp`.`reg_id` = `registration`.`id`) WHERE (MONTH(`del_lessons_dt`) >=8) AND (`temp`.`month_num` >=1 AND `temp`.`month_num`<= ".$this->db->escape($endmonth)." ))");
			
			
			//[*] Διαγραφή όσων γράφτηκαν το νέο έτος (μήνας εγγραφής < 7) και έχουν στο πίνακα οφειλών εγγραφή για μήνα < από το μήνα έναρξης !
	    	$this->db->query("DELETE FROM `debt` WHERE `id` IN (SELECT `debt`.`id` FROM (SELECT * FROM `debt`) AS `temp`, `registration` WHERE `debt`.`reg_id` = `registration`.`id` AND (MONTH(`registration`.`start_lessons_dt`)<=7 AND `debt`.`month_num` < MONTH(`registration`.`start_lessons_dt`)))");
			
			//[*] Διαγραφή όσων γράφτηκαν το νέο έτος (μήνας εγγραφής < 7) και έχουν στο πίνακα οφειλών εγγραφή για μήνα > από τον τρέχων !
			$this->db->query("DELETE FROM `debt` WHERE `id` IN (SELECT `debt`.`id` FROM (SELECT * FROM `debt`) AS `temp`, `registration` WHERE `debt`.`reg_id` = `registration`.`id` AND (`debt`.`month_num` > ".$this->db->escape($endmonth)." and MONTH(`registration`.`start_lessons_dt`)<=7))");

		}
		else //Αν βρισκόμαστε στο έτος έναρξης της σχολικής χρονιάς...
		{
			$this->db->query("DELETE FROM `debt` WHERE `id` IN (SELECT `debt`.`id` FROM (SELECT * FROM `debt`) AS `temp`, `registration` WHERE `debt`.`reg_id` = `registration`.`id` AND ((`debt`.`month_num` < MONTH(`registration`.`start_lessons_dt`) ) OR  `debt`.`month_num` >= MONTH(`registration`.`del_lessons_dt`)))");
			$this->db->query("DELETE FROM `debt` WHERE `id` IN (SELECT `debt`.`id` FROM (SELECT * FROM `debt`) AS `temp`, `registration` WHERE `debt`.`reg_id` = `registration`.`id` AND (MONTH(`registration`.`start_lessons_dt`) <= 7))");
		}


		//Διαγραφή όσων διαγράφηκαν το προηγούμενο έτος (μήνες 8-12) και έχουν εγγραφές στο πίνακα οφειλών για το νέο έτος (μήνες >=1)
		$this->db->query("DELETE FROM `debt` WHERE `id` IN (SELECT `debt`.`id` FROM (SELECT * FROM `debt`) AS `temp`, `registration` WHERE `debt`.`reg_id` = `registration`.`id` AND (MONTH(`registration`.`del_lessons_dt`)>=8 AND `debt`.`month_num`<=7))");


		// if ($chk0PayState==1) {
		// 	//ΓΙΑ ΔΙΑΓΡΑΦΗ ΜΗΔΕΝΙΚΩΝ ΟΦΕΙΛΩΝ (ΔΩΡΕΑΝ ΠΟΥ ΔΕΝ ΕΧΟΥΜΕ ΚΟΨΕΙ ΑΠΟΔΕΙΞΗ...)	
		// 	$this->db->query("DELETE FROM `debt` WHERE `amount` = 0");
		// }

    }


    public function UpdateDebtChanges($monthset)
    {
    	foreach ($monthset as $monthnum) {
	
			if ($monthnum <> 0) {
				if ($monthnum <> 1) {	
					$myquery1 = $this->db->query("SELECT DISTINCT `change`.* FROM `debt`, `change` WHERE  `debt`.`reg_id` = `change`.`reg_id` AND MONTH(`change`.`change_dt`)-1 = ".$this->db->escape($monthnum)." ");
				}
				else
				{ 
					$myquery1 = $this->db->query("SELECT DISTINCT `change`.* FROM `debt`, `change` WHERE  `debt`.`reg_id` = `change`.`reg_id` AND MONTH(`change`.`change_dt`)-1 = 0 ");
				}
				
				foreach($myquery1->result_array() as $row) 
				{
					$Apo_Poso = $row['prev_month_price'];
					$std_ID = $row['reg_id'];
					if ($monthnum > 7) {
						$this->db->query("UPDATE `debt` SET `debt`.`amount` = ".$this->db->escape($Apo_Poso)." WHERE  `debt`.`reg_id` = ".$this->db->escape($std_ID)." AND (`debt`.`month_num` >= ".$this->db->escape($monthnum)." OR `debt`.`month_num` <= 7) " );
					}
					elseif ($monthnum > 1) {
						$this->db->query("UPDATE `debt` SET `debt`.`amount` = ".$this->db->escape($Apo_Poso)." WHERE  `debt`.`reg_id` = ".$this->db->escape($std_ID)." AND (`debt`.`month_num` >= ".$this->db->escape($monthnum)." AND `debt`.`month_num` <= 7) " );
					}
					elseif ($monthnum == 1) {
						$this->db->query("UPDATE `debt` SET `debt`.`amount` = ".$this->db->escape($Apo_Poso)." WHERE  `debt`.`reg_id` = ".$this->db->escape($std_ID)." AND (`debt`.`month_num` =  12  OR ( `debt`.`month_num` >=1 AND `debt`.`month_num` <= 7)) " );
					}
				}
				

				$myquery2 = $this->db->query("SELECT DISTINCT `change`.* FROM `debt`, `change` WHERE  `debt`.`reg_id` = `change`.`reg_id` AND MONTH(`change`.`change_dt`) = ".$this->db->escape($monthnum)." ");

				foreach ($myquery2->result_array() as $row) 
				{
					$Se_Poso = $row['new_month_price'];
					$std_ID = $row['reg_id'];
					if ($monthnum > 7) {
						$this->db->query("UPDATE `debt` SET `debt`.`amount` = ".$this->db->escape($Se_Poso)." WHERE  `debt`.`reg_id` = ".$this->db->escape($std_ID)." AND (`debt`.`month_num` >= ".$this->db->escape($monthnum)." OR `debt`.`month_num` <= 7) " );
					}
					elseif ($monthnum >= 1) {
						$this->db->query("UPDATE `debt` SET `debt`.`amount` = ".$this->db->escape($Se_Poso)." WHERE  `debt`.`reg_id` = ".$this->db->escape($std_ID)." AND (`debt`.`month_num` >= ".$this->db->escape($monthnum)." AND `debt`.`month_num` <= 7) " );
					}
				}
			}
			else 
			{
				break;
			}
		
		} //end foreach

    }


    public function schFinanceUpdateDate($startsch)
    {
    	$data=array('value_1'=>date('d-m-Y'), 'value_2'=>$startsch);
    	$this->db->where('id', 3);
		$this->db->update('lookup', $data);

    }

}