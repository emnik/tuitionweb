<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class SMSto_lib
{
    protected $CI;

    public function __construct()
    {
        // Load CodeIgniter's instance
        $this->CI = &get_instance();
        // Load necessary models here if needed
        $this->CI->load->model('Contact_config_model'); // Adjust the model name as needed
    }

    public function get_balance(){
        $curl = curl_init();
        $api_key = $this->CI->Contact_config_model->get_sms_settings()[0]['apikey'];

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://auth.sms.to/api/balance',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        return $response;
    }

    public function create_list($name){
        $curl = curl_init();
        $api_key = $this->CI->Contact_config_model->get_sms_settings()[0]['apikey'];

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://sms.to/v1/people/lists/create',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "name": "'.$name.'",
            "description": ""
        }',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }


    public function create_contact($contact){
        $curl = curl_init();
        $api_key = $this->CI->Contact_config_model->get_sms_settings()[0]['apikey'];

        $data = json_encode($contact);

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://sms.to/v1/people/contacts/create',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function get_contacts(){
        $curl = curl_init();
        $api_key = $this->CI->Contact_config_model->get_sms_settings()[0]['apikey'];
        
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://sms.to/v1/people/contacts',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function get_number_of_contacts_inlist($id){
        $curl = curl_init();
        $api_key = $this->CI->Contact_config_model->get_sms_settings()[0]['apikey'];

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://sms.to/v1/people/lists',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);

        $response = json_decode($response, true);
        if($response['success'] === true){
            $lists = $response['data'];
            foreach($lists as $list){
                if($list['id'] == $id){
                    return $list['list_contacts_count'];
                }
            }
        } else {
            return 0;
        }
    }

    public function get_list_contacts($id){
        $curl = curl_init();
        $api_key = $this->CI->Contact_config_model->get_sms_settings()[0]['apikey'];

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://sms.to/v1/people/contacts?list_ids='.$id,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        return $response;
    }

    public function get_estimate(){
        $curl = curl_init();
        $api_key = $this->CI->Contact_config_model->get_sms_settings()[0]['apikey'];

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.sms.to/sms/estimate',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "message": "This is test",
            "to": "+306944123456",
            "sender_id": "SPOUDH"
        }',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

public function estimate_list_message($list_id, $message){
    $curl = curl_init();
    $api_key = $this->CI->Contact_config_model->get_sms_settings()[0]['apikey'];
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.sms.to/sms/estimate',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
        "message": "'.$message.'",
        "list_id": "'.$list_id.'",
        "sender_id": "SPOUDH"
    }',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $api_key,
        'Content-Type: application/json'
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    return $response;
    }

    public function send_sms($list_id, $message){
        $curl = curl_init();
        $api_key = $this->CI->Contact_config_model->get_sms_settings()[0]['apikey'];

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.sms.to/sms/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "message": "'.$message.'",
            "list_id": "'.$list_id.'"
        }',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function delete_list($list_id){
        $curl = curl_init();
        $api_key = $this->CI->Contact_config_model->get_sms_settings()[0]['apikey'];

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://sms.to/v1/people/lists/'.$list_id.'/delete',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'DELETE',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}