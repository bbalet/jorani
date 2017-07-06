<?php
/**
 * This controller contains the actions managing copmments of a leave
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.7.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

//We can define custom triggers before saving the leave request into the database
require_once FCPATH . "local/triggers/leave.php";

/**
 * This class allows an employee to list and manage its leave requests
 * Since 0.4.3 a trigger is called at the creation, if the function triggerCreateLeaveRequest is defined
 * see content of /local/triggers/leave.php
 */
class Comments extends CI_Controller {

    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        setUserContext($this);
        $this->load->model('comments_model');
        $this->load->model('users_model');
        $this->load->model('status_model');
        $this->lang->load('global', $this->language);
    }

    /**
     * Display the list of comments for a specific leave
     * @param int $id Id of the leave request
     * @author Emilien NICOLAS <milihhard1996@gmail.com>
     */
    public function index($id) {
        $this->auth->checkIfOperationIsAllowed('list_leaves');
        $data = getUserContext($this);
        $this->lang->load('datatable', $this->language);
        $data["comments"] = $this->comments_model->getCommentsLeave($id);
        $last_comment = new stdClass();;
        foreach ($data["comments"]->comments as $comments_item) {
          if($comments_item->type == "comment"){
            $comments_item->author = $this->users_model->getName($comments_item->author);
            $comments_item->in = "in";
            $last_comment->in="";
            $last_comment=$comments_item;
          } else if($comments_item->type == "change"){
            $comments_item->status = $this->status_model->getName($comments_item->status_number);
          }
        }
        $data['title'] = "Comments";
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('comments/index', $data);
        $this->load->view('templates/footer');
    }
}
