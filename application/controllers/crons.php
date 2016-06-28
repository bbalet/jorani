<?php

/**
 * This controller for remind manager days before his member's leave
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license    http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link       https://github.com/locnh/jorani
 * @since      0.4.7
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Crons extends CI_Controller {

    public function leaveRemind($dayBefore) {
        
        $this->load->model('users_model');
        $this->load->model('leaves_model');

        $leaves = $this->leaves_model->getComingLeave($dayBefore);
        
        foreach ($leaves as $user) {
            
            $manager['name']  = $this->users_model->getName($user['manager']);
            $manager['email'] = $this->users_model->getEmail($user['manager']);

            $message = "Hi {$manager['name']},\nYour member {$user['login']} will be on leave in next $dayBefore days, please be aware that !";

            $this->load->library('email');
            $this->email->set_encoding('quoted-printable');
            
            if ($this->config->item('from_mail') != FALSE && $this->config->item('from_name') != FALSE ) {
                $this->email->from($this->config->item('from_mail'), $this->config->item('from_name'));
            } else {
               $this->email->from('do.not@reply.me', 'LMS');
            }

            if ($this->config->item('subject_prefix') != FALSE) {
                $subject = $this->config->item('subject_prefix');
            } else {
               $subject = '[Jorani] ';
            }

            $this->email->to($manager['email']);
            
            $this->email->subject($subject . 'Your member will be on leave in soon !');
            $this->email->message($message);
            $this->email->send();
        }
    }
}