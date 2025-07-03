<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Theme extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Theme_model'); // Replace with your model name
    }

    public function load_css() {
        $user_id = $this->session->userdata('user_id'); // Assume user ID is stored in session
        $theme = $this->Theme_model->get_user_theme($user_id);
 
        if (!$theme) {
            // Define default theme values if none found
            $theme = [
                'primary_color' => '#141e26',
                'secondary_color' => '#1e5067',
                'accent_color' => '#159ab7',
                'background_color' => '#cbd8df',
                'light_color' => '#ffffff',
                'light_secondary_color' => '#eae8fa',
                'dark_color' => '#1a535c',
            ];
        }         

        header("Content-Type: text/css; charset=UTF-8");
        echo ":root {
            --primary-color: {$theme['primary_color']};
            --secondary-color: {$theme['secondary_color']};
            --accent-color: {$theme['accent_color']};
            --background-color: {$theme['background_color']};
            --light-color: {$theme['light_color']};
            --light-secondary-color: {$theme['light_secondary_color']};
            --dark-color: {$theme['dark_color']};
        }";
    }

    public function set_theme() {
        $user_id = $this->session->userdata('user_id'); // Assuming you're using session for user authentication
    
        $theme_id = $this->input->post('theme_id');

        if (!$theme_id) {
            echo json_encode(['success' => false, 'message' => 'Theme ID is required']);
            return;
        }
    
        $this->load->model('Theme_model');
        $updated = $this->Theme_model->update_user_theme($user_id, $theme_id);
    
        if ($updated) {
            $this->session->set_userdata(array('current_theme_id' => $theme_id)); // Store the current theme ID in session
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update theme']);
        }
    }


    public function get_themes() {
        // Load the model
        $this->load->model('Theme_model');
        
        // Get the list of themes from the model
        $themes = $this->Theme_model->get_all_themes();
        
        // Return the themes as JSON
        echo json_encode($themes);
    }



}
