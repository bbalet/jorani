<?php
/**
 * This controller serves the user management REST API
 * @copyright  Copyright (c) 2014-2018 Benjamin BALET
 * @license    http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link       https://github.com/bbalet/jorani
 * @since      0.6.6
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This REST controller serves the API calls related to users
 * The difference with HR Controller is that operations are technical (CRUD, etc.).
 */
class RestUsers extends MY_RestController {

    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('users_model');
    }

    /**
     * Get the the profile of the connected employee
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function profile() {
        log_message('debug', '++profile');
        $profile = new \stdClass();
        $this->load->model('positions_model');
        $this->load->model('contracts_model');
        $this->load->model('organization_model');
        $this->load->model('oauthclients_model');
        $profile->managerName = $this->users_model->getName($this->user->manager);
        $profile->contractName = $this->contracts_model->getName($this->user->contract);
        $profile->positionName = $this->positions_model->getName($this->user->position);
        $profile->organizationName = $this->organization_model->getName($this->user->organization);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($profile));
        log_message('debug', '--profile');
    }

    /**
     * Display the list of all users or one user
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function users($userID = 0) {
        log_message('debug', '++users / userID = ' . $userID);
        //Is the connected user an admin or HR?
        if ($this->user->isAdmin || $this->user->isHr) {
            if ($userID != 0) {
                $users = $this->users_model->getUsers($userID);
                unset($users['password']);
                unset($users['random_hash']);
            } else {
                $users = $this->users_model->getUsersAndRoles();
            }
        } else {
            $this->forbidden();
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($users));
        log_message('debug', '--users');
    }

    /**
     * Enable a user
     * @param int $userID User identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function enable($userID) {
        log_message('debug', '++enable');
        //Is the connected user an admin or HR?
        if ($this->user->isAdmin || $this->user->isHr) {
            $this->users_model->setActive($userID, TRUE);
        } else {
            $this->forbidden();
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode('OK'));
        log_message('debug', '--enable');
    }

    /**
     * Disable a user
     * @param int $userID User identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function disable($userID) {
        log_message('debug', '++disable');
        //Is the connected user an admin or HR?
        if ($this->user->isAdmin || $this->user->isHr) {
            $this->users_model->setActive($userID, FALSE);
        } else {
            $this->forbidden();
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode('OK'));
        log_message('debug', '--disable');
    }
}
