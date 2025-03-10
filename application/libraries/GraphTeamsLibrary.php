<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class GraphTeamsLibrary
{
    protected $CI;
    private $token;
    private $token_expiry;

    public function __construct()
    {
        // Load CodeIgniter's instance
        $this->CI = &get_instance();
        // Load necessary models here if needed
        $this->CI->load->model('Contact_config_model'); // Adjust the model name as needed
        $this->CI->load->model('Teams_model'); // Adjust the model name as needed
    }

    public function do($action, $data=null, $userId=null)
    {
        // Get the authorization token
        $token = $this->getAuthorizationToken();
        if ($token) {
            if ($action == 'reset'){
                $result = $this->get_users($token, $active=true);
            } 
            else if ($action === 'delete') {
                $result = $this->batch_delete_users($token, $data);
            }
            else if ($action === 'get_deleted_users') {
                $result = $this->get_users($token, $active=false);  
            }
            else if ($action === 'update') {
                $result = $this->update($token, $data, $userId);
            }
            return $result; //this is the JSON object returned by the calling function
        } else {
            return json_encode(array(
                'status' => 'error',
                'message' => 'Error obtaining access token'
            ));
        }
    }

    private function getAuthorizationToken()
    {
        // Check if the token is already set and not expired
        if ($this->token && $this->token_expiry > time()) {
            return $this->token;
        }

        // Define your Azure AD application credentials
        $appdata = $this->CI->Contact_config_model->get_settings();

        if ($appdata) {
            $client_id = $appdata[0]['mailclientid'];
            $client_secret = $appdata[0]['mailclientsecret'];
            $tenant_id = $appdata[0]['tenantid'];
        }

        // Obtain an OAuth2 token using client credentials flow
        $token_url = "https://login.microsoftonline.com/{$tenant_id}/oauth2/v2.0/token";
        $token_data = array(
            'grant_type' => 'client_credentials',
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'scope' => 'https://graph.microsoft.com/.default',
        );

        // Get the OAuth2 token
        $token_response = $this->curl_post($token_url, http_build_query($token_data));
        $decoded_token_response = json_decode($token_response['response'], true);

        if (isset($decoded_token_response['access_token'])) {
            $this->token = $decoded_token_response['access_token'];
            $this->token_expiry = time() + $decoded_token_response['expires_in'];
            return $this->token;
        } else {
            $error_message = isset($decoded_token_response['error_description']) ? $decoded_token_response['error_description'] : $token_response['response'];
            return null;
        }
    }

    private function get_users($token, $active)
    {
        // Define the Microsoft Graph API endpoint for retrieving the users list data
        if ($active) {
            $graph_api_url = 'https://graph.microsoft.com/v1.0/users?%24select=id%2CdisplayName%2CgivenName%2Csurname%2Cmail%2CmobilePhone%2CotherMails';
        } else {
            $graph_api_url = 'https://graph.microsoft.com/beta/directory/deletedItems/microsoft.graph.user?%24count=true&%24select=id%2CdisplayName%2Csurname%2CgivenName%2Cmail%2CotherMails%2CmobilePhone%2CdeletedDateTime%2CsignInSessionsValidFromDateTime&%24orderby=deletedDateTime%20desc';
        }
        
        $headers = array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'ConsistencyLevel: eventual'
        );

        $data = [];
        $next_link = $graph_api_url;
        $all_data_retrieved = true;

        // Loop to handle pagination
        do {
            // Send the request using Microsoft Graph API
            $response = $this->curl_get($next_link, $headers);

            // Check if the request was successful
            if ($response['status_code'] == 200) {
                $response_data = json_decode($response['response'], true);
                if (isset($response_data['value'])) {
                    $data = array_merge($data, $response_data['value']);
                }
                $next_link = isset($response_data['@odata.nextLink']) ? $response_data['@odata.nextLink'] : null;
            } else {
                $all_data_retrieved = false;
                return json_encode(array(
                    'status' => 'error',
                    'message' => "Error retrieving the users list. Status code: {$response['status_code']}. Response: " . $response['response']
                ));
            }
        } while ($next_link);

        // store the data of active (not deleted) users in the database
        if ($active) {
            // Insert each user into the teams table only if all data were successfully retrieved
            if ($all_data_retrieved) {
                $res = $this->CI->Teams_model->insert_into_teams_table($data);
            }

            if (!$res) {
                return json_encode(array(
                    'status' => 'error',
                    'message' => 'Error inserting users into the database'
                ));
            }
        }

        return json_encode(array(
            'status' => 'success',
            'message' => 'Users list retrieved successfully!',
            'data' => $data
        ));
    }

    private function batch_delete_users($token, $data)
    {
        // Define the Microsoft Graph API endpoint for deleting users
        $graph_api_url = 'https://graph.microsoft.com/v1.0/users/';
        $headers = array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'ConsistencyLevel: eventual'
        );

        $deleted_users = [];
        $batch_request = array('requests' => array());

        foreach ($data as $index => $uid) {
            $batch_request['requests'][] = array(
            'id' => (string)($index + 1),
            'method' => 'DELETE',
            'url' => "/users/{$uid}"
            );
        }

        // Send the batch request to Microsoft Graph API
        $batch_url = 'https://graph.microsoft.com/v1.0/$batch';
        $response = $this->curl_post($batch_url, json_encode($batch_request), $headers);

        // Check if the request was successful
        if ($response['status_code'] == 200) {
            $response_data = json_decode($response['response'], true);
            foreach ($response_data['responses'] as $res) {
                if ($res['status'] == 204) {
                    $deleted_users[] = $data[$res['id'] - 1]; // Add the user ID to the deleted users array ($res['id'] is the corresponding 'id' property of the batch request array)
                } 
            }
        } else {
            return json_encode(array(
            'status' => 'error',
            'message' => "Error deleting users. Status code: {$response['status_code']}. Response: " . $response['response']
            ));
        }

        if (!empty($deleted_users)){
            $res = $this->CI->Teams_model->delete_from_teams_table($deleted_users);

            if (!$res) {
                return json_encode(array(
                    'status' => 'error',
                    'message' => 'Error deleted users from local database. Though succesully deleted from Microsoft Server. Please Reset!'
                ));
            }

        }

        return json_encode(array(
            'status' => 'success',
            'message' => 'Users deleted successfully!',
            'data' => $deleted_users
        ));
    }


    public function update($token, $data, $userId)
    {
        // Define the Microsoft Graph API endpoint for updating a user
        $graph_api_url = 'https://graph.microsoft.com/v1.0/users/' . $userId;
        $headers = array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        );

        // Send the request to Microsoft Graph API
        $response = $this->curl_patch($graph_api_url, $data, $headers);

        // Check if the request was successful
        if ($response['status_code'] == 204) {
            // Update the local database
            
            $update_data = json_decode($data, true);

            // Check if $data only contains the passwordProfile property
            if (isset($update_data['passwordProfile']) && count($update_data) === 1) {
                return json_encode(array(
                    'status' => 'success',
                    'message' => 'User password updated successfully!'
                ));
            }
            $res = $this->CI->Teams_model->update_user_in_teams_table($userId, $update_data);

            if ($res) {
                return json_encode(array(
                    'status' => 'success',
                    'message' => 'User updated successfully!'
                ));
            } else {
                return json_encode(array(
                    'status' => 'error',
                    'message' => 'User updated in Microsoft Graph but failed to update in local database. Please Reset!'
                ));
            }
        } else {
            return json_encode(array(
                'status' => 'error',
                'message' => "Error updating the user. Status code: {$response['status_code']}. Response: " . $response['response']
            ));
        }
    }

    // -----------------CURL HELPER FUNCTIONS-----------------

    // Curl post helper function to return both the response and status code
    private function curl_post($url, $data, $headers = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Return both the response and the status code as an associative array
        return array('response' => $response, 'status_code' => $status_code);
    }

    private function curl_get($url, $headers = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Return both the response and the status code as an associative array
        return array('response' => $response, 'status_code' => $status_code);
    }

    private function curl_patch($url, $data, $headers = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Return both the response and the status code as an associative array
        return array('response' => $response, 'status_code' => $status_code);
    }
}
