<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reminder_merge_fields extends App_mail_template
{
    protected $for = 'staff';

    protected $activity;

    protected $staff_email;

    public $slug = 'reminder';

    public $rel_type = 'activity';

    public function __construct($activity, $staff_email)
    {
        parent::__construct();

        $this->activity    = $activity;
        $this->staff_email = $staff_email;
    }

    public function build()
    {
        $this->to($this->staff_email)
        ->set_rel_id($this->activity->id)
        ->set_merge_fields('proposals_merge_fields', $this->activity->id);
    }
}
