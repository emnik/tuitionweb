<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// require_once APPPATH . 'vendor/firephp/firephp-core/lib/FirePHPCore/FirePHP.class.php';

class Firephp_lib {

    private $firephp;

    public function __construct() {
        // Initialize FirePHP
        $this->firephp = FirePHP::getInstance(true);
    }

    // Log information
    public function info($message) {
        $this->firephp->info($message);
    }

    // Log errors
    public function error($message) {
        $this->firephp->error($message);
    }

    // Log warnings
    public function warn($message) {
        $this->firephp->warn($message);
    }

    // Log regular messages
    public function log($message) {
        $this->firephp->log($message);
    }

    // Log variable dumps
    public function dump($key, $value) {
        $this->firephp->fb($value, $key, FirePHP::LOG);
    }
}
