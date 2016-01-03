<?php
/**
 * This controller serves the administration pages
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.2
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This class serves the administration pages (readonly settings page for the moment).
 * In Jorani the settings are set into a configuration file and not into DB.
 */
class Admin extends CI_Controller {
    
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        setUserContext($this);
        $this->lang->load('global', $this->language);
    }
    
    /**
     * Display the settings of the system (extract of config.php)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function settings() {
        $this->auth->checkIfOperationIsAllowed('list_settings');
        $data = getUserContext($this);
        $data['title'] = 'application/config/config.php';
        $data['help'] = $this->help->create_help_link('global_link_doc_page_settings');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('admin/settings', $data);
        $this->load->view('templates/footer');
    }
}
