<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class GraphEmailLibrary
{
    protected $CI;

    public function __construct()
    {
        // Load CodeIgniter's instance
        $this->CI = &get_instance();
        // Load necessary models here if needed
        $this->CI->load->model('Contact_config_model'); // Adjust the model name as needed
        
        // $variableName = $this->CI->model_name->function_name() //use like this anywhere in the library!;
    }

    public function send_emails($email_subject, $email_body, $email_list, $cc_email_list, $sender_email, $replyto_email)
    {
        // Get the authorization token
        // $token = $this->getAuthorizationToken();
        $token_response = $this->getAuthorizationToken();
        $decoded_response = json_decode($token_response, true);

        if ($decoded_response['status'] === 'success') {
            $token = $decoded_response['token'];

            // Create an array of recipient email addresses
            $recipient_emails = array();
            foreach ($email_list as $email_entry) {
                if (isset($email_entry['email'])) {
                    $recipient_emails[] = array(
                        'emailAddress' => array(
                            'address' => $email_entry['email'],
                        ),
                    );
                }
            }

            // Create an array of CC recipient email addresses
            $cc_recipient_emails = array();
            if(!empty($cc_email_list)){
                foreach ($cc_email_list as $cc_email_entry) {
                    if (isset($cc_email_entry['email'])) {
                        $cc_recipient_emails[] = array(
                            'emailAddress' => array(
                                'address' => $cc_email_entry['email'],
                            ),
                        );
                    }
                }
            }


            // $this->CI->load->library('Firephp_lib');
		    // $this->CI->firephp_lib->log($recipient_emails, 'Recipient Emails');

            // Send the email to the recipients
            $result = $this->send_email($email_subject, $email_body, $recipient_emails, $cc_recipient_emails, $sender_email, $replyto_email, $token);
            return $result; //this is the JSON object returned by the send_email() function

        } else { //authentication failed!
            return json_encode(array(
                'status' => 'error',
                'message' => $decoded_response['message']
            ));
        }
    }

    private function getAuthorizationToken()
    {
        // Define your Azure AD application credentials
        $appdata = $this->CI->Contact_config_model->get_settings();

        // $this->CI->load->library('Firephp_lib');
		// $this->CI->firephp_lib->dump('data', $appdata);
        
        if ($appdata){
            //$appdata[0] as we only have one tenant! So one line in the db table!
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
            return json_encode(array(
                'status' => 'success',
                'token' => $decoded_token_response['access_token'],
                'message' => 'Token obtained successfully'
            ));
        } else {
            $error_message = isset($decoded_token_response['error_description']) ? $decoded_token_response['error_description'] : $token_response['response'];
            return json_encode(array(
                'status' => 'error',
                // 'token' => null,
                'message' => "Error obtaining access token: " . $error_message
            ));
        }
    }

    private function send_email($email_subject, $email_body, $recipient_emails, $cc_emails, $sender_email, $replyto_email, $token)
    {
        // Define the Microsoft Graph API endpoint for sending emails using app-only authentication
        $graph_api_url = "https://graph.microsoft.com/v1.0/users/{$sender_email}/sendMail";

        // Create the email message
        $email_message = array(
            'message' => array(
                'subject' => $email_subject,
                'body' => array(
                    'contentType' => 'HTML', 
                    'content' => $email_body,
                ),
                'toRecipients' => $recipient_emails,
                'ccRecipients' => $cc_emails, // Add CC recipients here
                'replyTo' => array(
                    array(
                        'emailAddress' => array(
                            'address' => $replyto_email,
                        ),
                    ),
                ),
            ),
            'saveToSentItems' => true,
        );

        // Send the email using Microsoft Graph API
        $response = $this->curl_post($graph_api_url, json_encode($email_message), array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ));

        // Check if the email was sent successfully
        if ($response['status_code'] == 202) {
            return json_encode(array(
                'status' => 'success',
                'message' => 'Email sent successfully to the recipient list!'
            ));
        } else {
            return json_encode(array(
                'status' => 'error',
                'message' => "Error sending email. Status code: {$response['status_code']}. Response: " . $response['response']
            ));
        }
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
}
