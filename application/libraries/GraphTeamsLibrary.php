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
            if ($action === 'reset'){
                // Reset the users data
                $result = $this->get_users($token, $active=true);
            }
            else if ($action === 'get_single_user') {
                // Get a single user data
                $result = $this->get_single_user($token, $userId);
            } 
            else if ($action === 'delete') {
                // Delete the users
                $result = $this->batch_delete_users($token, $data);
            }
            else if ($action === 'get_deleted_users') {
                // Get the deleted users data
                $result = $this->get_users($token, $active=false);  
            }
            else if ($action === 'get_deleted_user') {
                // Get a single deleted user data
                $result = $this->get_deleted_user($token, $userId);
            }
            else if ($action === 'restore') {
                // Restore the deleted user
                $result = $this->restore_user($token, $userId);
            }
            else if ($action === 'update') {
                // Update the user data
                $result = $this->update($token, $data, $userId);
            }
            else if ($action === 'get_domain') {
                // Get the domain data
                $result = $this->get_domain($token);
            } 
            else if ($action === 'create') {
                // Create a new user
                $result = $this->create_user($token, $data);
            }
            else if ($action === 'assign_licence'){
                // Assign a licence to a user
                $result = $this->assign_licence($token, $data, $userId);
            }
            else if ($action === 'get_organization_metadata'){
                // Get the organization metadata
                $result = $this->get_organization_metadata($token);
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


    private function update($token, $data, $userId)
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

    private function get_domain($token)
    {
        // Define the Microsoft Graph API endpoint for retrieving the domain data
        $graph_api_url = 'https://graph.microsoft.com/v1.0/domains';
        $headers = array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        );

        // Send the request to Microsoft Graph API
        $response = $this->curl_get($graph_api_url, $headers);

        // Check if the request was successful
        if ($response['status_code'] == 200) {
            $response_data = json_decode($response['response'], true);
            $default_domain = null;
            foreach ($response_data['value'] as $domain) {
                if (isset($domain['isDefault']) && $domain['isDefault']) {
                    $default_domain = $domain['id'];
                    break;
                }
            }
            return json_encode(array(
                'status' => 'success',
                'message' => 'Domain data retrieved successfully!',
                'domain' => $default_domain
            ));
        } else {
            return json_encode(array(
                'status' => 'error',
                'message' => "Error retrieving the domain data. Status code: {$response['status_code']}. Response: " . $response['response']
            ));
        }
    }


    private function get_organization_metadata($token)
    {
        // Define the Microsoft Graph API endpoint for retrieving the organization metadata
        $graph_api_url = 'https://graph.microsoft.com/v1.0/organization';
        $headers = array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        );

        // Send the request to Microsoft Graph API
        $response = $this->curl_get($graph_api_url, $headers);

        // Check if the request was successful
        if ($response['status_code'] == 200) {
            $response_data = json_decode($response['response'], true);
            $default_domain = null;
            $countryLetterCode = null;
            foreach ($response_data['value'] as $organization) {
                if(isset($organization['countryLetterCode'])){
                    $countryLetterCode = $organization['countryLetterCode'];
                }
                if(isset($organization['verifiedDomains'])){
                    foreach ($organization['verifiedDomains'] as $domain) {
                        if (isset($domain['isDefault']) && $domain['isDefault']) {
                            $default_domain = $domain['name'];
                            break;
                        }
                    }
                }
            }
            $data = array(
                'default_domain' => $default_domain,
                'countryLetterCode' => $countryLetterCode
            );
            return json_encode(array(
                'status' => 'success',
                'message' => 'Organization metadata retrieved successfully!',
                'data' => $data
            ));
        } else {
            return json_encode(array(
                'status' => 'error',
                'message' => "Error retrieving the organization metadata. Status code: {$response['status_code']}. Response: " . $response['response']
            ));
        }
    }


    private function create_user($token, $data)
    {
        // Define the Microsoft Graph API endpoint for creating a user
        $graph_api_url = 'https://graph.microsoft.com/v1.0/users';
        $headers = array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        );

        // Send the request to Microsoft Graph API
        $createResult = $this->curl_post($graph_api_url, $data, $headers);

        // Check if the request was successful
        if ($createResult['status_code'] == 201) {
            $createResultData = json_decode($createResult['response'], true);
            return json_encode(array(
                'status' => 'success',
                'message' => 'User created successfully!',
                'id' => $createResultData['id']
            ));
            // Retrieve the user data to use them in the local database

            // This is not working because the mail property is not returned in the response
            // unless the user has assigned a licence!

            // $created_user_data = $this->get_single_user($token, json_decode($createResult['response'], true)['id']);
            // $created_user_data = json_decode($created_user_data, true);
            // if ($created_user_data['status'] == 'error') {
            //     return json_encode(array(
            //         'status' => 'error',
            //         'message' => $created_user_data['message']
            //     ));
            // } else {
            //     $insert_data = $created_user_data['data'];
            //     $res = $this->CI->Teams_model->add_single_user_in_teams_table($insert_data);

            //     if ($res) {
            //         return json_encode(array(
            //             'status' => 'success',
            //             'message' => 'User created successfully!',
            //             'id' => $insert_data['id']
            //         ));
            //     } else {
            //         return json_encode(array(
            //             'status' => 'error',
            //             'message' => 'User created in Microsoft Graph but failed to insert into local database. Please Reset!'
            //         ));
            //     }
            // }

            // Previously I used the response from the create user request to insert the user data into the local database
            // but that did not work as expected because the response does not contain all the user data
            // for example the otherMails property is not returned in the response!!!

            // $insert_data = json_decode($createResult['response'], true);
            // if(!isset($insert_data['mail'])){
            //     $insert_data['mail'] = $insert_data['userPrincipalName'];
            // }
            // if(!isset($insert_data['otherMails'])){
            //     $insert_data['otherMails'] = [];
            // }
            // if(!isset($insert_data['mobilePhone'])){
            //     $insert_data['mobilePhone'] = '';
            // }
            // $res = $this->CI->Teams_model->add_single_user_in_teams_table($insert_data);

            // if ($res) {
            //     return json_encode(array(
            //         'status' => 'success',
            //         'message' => 'User created successfully!',
            //         'id' => $insert_data['id']
            //     ));
            // } else {
            //     return json_encode(array(
            //         'status' => 'error',
            //         'message' => 'User created in Microsoft Graph but failed to insert into local database. Please Reset!'
            //     ));
            // }
        } else {
        // Decode the JSON response
        $response_data = json_decode($createResult['response'], true);

        // Extract the error message if it exists
        $error_message = isset($response_data['error']['message']) ? $response_data['error']['message'] : $createResult['response'];

        return json_encode(array(
            'status' => 'error',
            'message' => "Error creating the user. Status code: {$createResult['status_code']}. Response: " . $error_message
        ));
        }
    }

    private function assign_licence($token, $data, $userId)
    {
        // Define the Microsoft Graph API endpoint for assigning a licence to a user
        $graph_api_url = 'https://graph.microsoft.com/v1.0/users/' . $userId . '/assignLicense';
        $headers = array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        );

        // Send the request to Microsoft Graph API
        $response = $this->curl_post($graph_api_url, $data, $headers);

        // Check if the request was successful
        if ($response['status_code'] == 200) {
            return json_encode(array(
                'status' => 'success',
                'message' => 'Licence assigned successfully!'
            ));
        } else {
            return json_encode(array(
                'status' => 'error',
                'message' => "Error assigning the licence. Status code: {$response['status_code']}. Response: " . $response['response']
            ));
        }
    }


    private function get_single_user($token, $userId)
    {
        // Define the Microsoft Graph API endpoint for retrieving a user
        $graph_api_url = 'https://graph.microsoft.com/v1.0/users/' . $userId . '?%24select=id%2CdisplayName%2CgivenName%2Csurname%2Cmail%2CmobilePhone%2CotherMails';
        $headers = array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'ConsistencyLevel: eventual'
        );

        // Send the request to Microsoft Graph API
        $response = $this->curl_get($graph_api_url, $headers);

        // Check if the request was successful
        if ($response['status_code'] == 200) {
            $response_data = json_decode($response['response'], true);
            return json_encode(array(
                'status' => 'success',
                'message' => 'User retrieved successfully!',
                'data' => $response_data
            ));
        } else {
            return json_encode(array(
                'status' => 'error',
                'message' => "Error retrieving the user. Status code: {$response['status_code']}. Response: " . $response['response']
            ));
        }
    }

    private function restore_user($token, $userId)
    {
        // Define the Microsoft Graph API endpoint for restoring a deleted user
        $graph_api_url = 'https://graph.microsoft.com/v1.0/directory/deletedItems/' . $userId . '/restore';
        $headers = array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'ConsistencyLevel: eventual'
        );

        // Send the request to Microsoft Graph API
        $response = $this->curl_post($graph_api_url, null, $headers);
        $response_data = json_decode($response['response'], true);

        // Check if the request was successful
        if ($response['status_code'] == 200) {
            return json_encode(array(
                'status' => 'success',
                'message' => 'User restored successfully!'
            ));
        } else {
            return json_encode(array(
                'status' => 'error',
                'message' => "Error restoring the user. Status code: {$response['status_code']}. Response: " . $response['response']
            ));
        }
    }

    private function get_deleted_user($token, $userId)
    {
        // Define the Microsoft Graph API endpoint for retrieving a deleted user
        $graph_api_url = 'https://graph.microsoft.com/v1.0/directory/deletedItems/' . $userId .'?$select=id,displayName,givenName,surname,mail,mobilePhone,otherMails';
        $headers = array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'ConsistencyLevel: eventual'
        );

        // Send the request to Microsoft Graph API
        $response = $this->curl_get($graph_api_url, $headers);

        // Check if the request was successful
        if ($response['status_code'] == 200) {
            $response_data = json_decode($response['response'], true);
            return json_encode(array(
                'status' => 'success',
                'message' => 'Deleted user retrieved successfully!',
                'data' => $response_data
            ));
        } else {
            return json_encode(array(
                'status' => 'error',
                'message' => "Error retrieving the deleted user. Status code: {$response['status_code']}. Response: " . $response['response']
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
