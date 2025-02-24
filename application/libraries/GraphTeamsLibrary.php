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

    public function do($action, $data=null)
    {
        // Get the authorization token
        $token = $this->getAuthorizationToken();
        if ($token) {
            if ($action == 'reset'){
                $result = $this->get_users($token);
            }
            return $result; //this is the JSON object returned by the get_users() function
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

    private function get_users($token)
    {
        // Define the Microsoft Graph API endpoint for retrieving the users list data
        $graph_api_url = 'https://graph.microsoft.com/v1.0/users?%24select=id%2CgivenName%2Csurname%2C%20mail%2C%20jobTitle';
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

        // Insert each user into the teams table only if all data were successfully retrieved
        if ($all_data_retrieved) {
            $this->CI->Teams_model->insert_into_teams_table($data);
        }

        return json_encode(array(
            'status' => 'success',
            'message' => 'Users list retrieved successfully!',
            'data' => $data
        ));
    }

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
}
