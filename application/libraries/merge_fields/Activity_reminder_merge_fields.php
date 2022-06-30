<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Activity_reminder_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
					[
						'name'      =>'Activity Type',
						'key'       => '{task_type}',
						'available' => [
							'activity_reminder',
						],
					],
				[
                    'name'      => 'Subject',
                    'key'       => '{task_subject}',
                    'available' => [
                        'activity_reminder',
                    ],
                ],
				[
                    'name'      => 'Description',
                    'key'       => '{task_description}',
                    'available' => [
                        'activity_reminder',
                    ],
                ],
				[
                    'name'      => 'Assigned to',
                    'key'       => '{task_assign}',
                    'available' => [
                        'activity_reminder',
                    ],
                ],
				[
                    'name'      => 'Assigned to',
                    'key'       => '{task_assign}',
                    'available' => [
                        'activity_reminder',
                    ],
                ],
				[
                    'name'      => 'Date',
                    'key'       => '{task_date}',
                    'available' => [
                        'activity_reminder',
                    ],
                ],
				[
                    'name'      => 'Time',
                    'key'       => '{task_time}',
                    'available' => [
                        'activity_reminder',
                    ],
                ],
				[
                    'name'      => 'Priority ',
                    'key'       => '{task_priority}',
                    'available' => [
                        'activity_reminder',
                    ],
                ],
				[
                    'name'      => 'Deal',
                    'key'       => '{task_deal}',
                    'available' => [
                        'activity_reminder',
                    ],
                ],
				[
                    'name'      => 'Organization',
                    'key'       => '{task_org}',
                    'available' => [
                        'activity_reminder',
                    ],
                ],
				[
                    'name'      => 'Contact Person',
                    'key'       => '{task_contact}',
                    'available' => [
                        'activity_reminder',
                    ],
                ],
				[
                    'name'      => 'Status',
                    'key'       => '{task_status}',
                    'available' => [
                        'activity_reminder',
                    ],
                ],
				
				[
                    'name'      => 'Proposal ID',
                    'key'       => '{proposal_id}',
                    'available' => [
                        'proposal_reminder',
                    ],
                ],
                [
                    'name'      => 'Proposal Number',
                    'key'       => '{proposal_number}',
                    'available' => [
                        'proposal_reminder',
                    ],
                ],
                [
                    'name'      => 'Subject',
                    'key'       => '{proposal_subject}',
                    'available' => [
                        'proposal_reminder',
                    ],
                ],
                [
                    'name'      => 'Proposal Total',
                    'key'       => '{proposal_total}',
                    'available' => [
                        'proposal_reminder',
                    ],
                ],
                [
                    'name'      => 'Proposal Subtotal',
                    'key'       => '{proposal_subtotal}',
                    'available' => [
                        'proposal_reminder',
                    ],
                ],
                [
                    'name'      => 'Open Till',
                    'key'       => '{proposal_open_till}',
                    'available' => [
                        'proposal_reminder',
                    ],
                ],
                [
                    'name'      => 'Proposal Assigned',
                    'key'       => '{proposal_assigned}',
                    'available' => [
                        'proposal_reminder',
                    ],
                ],
                [
                    'name'      => 'Company Name',
                    'key'       => '{proposal_proposal_to}',
                    'available' => [
                        'proposal_reminder',
                    ],
                ],
                [
                    'name'      => 'Address',
                    'key'       => '{proposal_address}',
                    'available' => [
                        'proposal_reminder',
                    ],
                ],
                [
                    'name'      => 'City',
                    'key'       => '{proposal_city}',
                    'available' => [
                        'proposal_reminder',
                    ],
                ],
                [
                    'name'      => 'State',
                    'key'       => '{proposal_state}',
                    'available' => [
                        'proposal_reminder',
                    ],
                ],
                [
                    'name'      => 'Zip Code',
                    'key'       => '{proposal_zip}',
                    'available' => [
                        'proposal_reminder',
                    ],
                ],
                [
                    'name'      => 'Country',
                    'key'       => '{proposal_country}',
                    'available' => [
                        'proposal_reminder',
                    ],
                ],
                [
                    'name'      => 'Email',
                    'key'       => '{proposal_email}',
                    'available' => [
                        'proposal_reminder',
                    ],
                ],
                [
                    'name'      => 'Phone',
                    'key'       => '{proposal_phone}',
                    'available' => [
                        'proposal_reminder',
                    ],
                ],
                [
                    'name'      => 'Assign',
                    'key'       => '{assign}',
                    'available' => [
                        'target_reminder',
                    ],
                ],
                [
                    'name'      => 'Pipeline',
                    'key'       => '{pipeline}',
                    'available' => [
                        'target_reminder',
                    ],
                ],
				[
                    'name'      => 'Tracking Metric',
                    'key'       => '{tracking_metric}',
                    'available' => [
                        'target_reminder',
                    ],
                ],
				[
                    'name'      => 'Type',
                    'key'       => '{target_type}',
                    'available' => [
                        'target_reminder',
                    ],
                ],
				[
                    'name'      => 'Interval',
                    'key'       => '{Interval}',
                    'available' => [
                        'target_reminder',
                    ],
                ],
				[
                    'name'      => 'User',
                    'key'       => '{user}',
                    'available' => [
                        'target_reminder',
                    ],
                ],
				[
                    'name'      => 'Manager',
                    'key'       => '{manager}',
                    'available' => [
                        'target_reminder',
                    ],
                ],
				[
                    'name'      => 'Count/Value',
                    'key'       => '{count_value}',
                    'available' => [
                        'target_reminder',
                    ],
                ],
				[
                    'name'      => 'Pipeline Stage',
                    'key'       => '{pipeline_stage}',
                    'available' => [
                        'target_reminder',
                    ],
                ],
				[
                    'name'      => 'Start Date',
                    'key'       => '{start_date}',
                    'available' => [
                        'target_reminder',
                    ],
                ],
				[
                    'name'      => 'End Date',
                    'key'       => '{end_date}',
                    'available' => [
                        'target_reminder',
                    ],
                ],
            ];
    }

    /**
     * Merge fields for tasks
     * @param  mixed  $task_id         task id
     * @param  boolean $client_template is client template or staff template
     * @return array
     */
    public function format($task_id, $client_template = false)
    {
        $fields = [];

        $this->ci->db->where('id', $task_id);
        $task = $this->ci->db->get(db_prefix().'tasks')->row();

        if (!$task) {
            return $fields;
        }

        // Client templateonly passed when sending to tasks related to project and sending email template to contacts
        // Passed from tasks_model  _send_task_responsible_users_notification function
        if ($client_template == false) {
            $fields['{task_link}'] = admin_url('tasks/view/' . $task_id);
        } else {
            $fields['{task_link}'] = site_url('clients/project/' . $task->rel_id . '?group=project_tasks&taskid=' . $task_id);
        }

        if (is_client_logged_in()) {
            $fields['{task_user_take_action}'] = get_contact_full_name(get_contact_user_id());
        } else {
            $fields['{task_user_take_action}'] = get_staff_full_name(get_staff_user_id());
        }

        $fields['{task_comment}'] = '';
        $fields['{task_related}'] = '';
        $fields['{project_name}'] = '';

        if ($task->rel_type == 'project') {
            $this->ci->db->select('name, clientid');
            $this->ci->db->from(db_prefix().'projects');
            $this->ci->db->where('id', $task->rel_id);
            $project = $this->ci->db->get()->row();
            if ($project) {
                $fields['{project_name}'] = $project->name;
            }
        }

        if (!empty($task->rel_id)) {
            $rel_data                 = get_relation_data($task->rel_type, $task->rel_id);
            $rel_values               = get_relation_values($rel_data, $task->rel_type);
            $fields['{task_related}'] = $rel_values['name'];
        }

        $fields['{task_name}']        = $task->name;
        $fields['{task_description}'] = $task->description;

        $languageChanged = false;

        // The tasks status may not be translated if the client language is not loaded
        if (!is_client_logged_in()
        && $task->rel_type == 'project'
        && $project
        && isset($GLOBALS['SENDING_EMAIL_TEMPLATE_CLASS'])
        && !$GLOBALS['SENDING_EMAIL_TEMPLATE_CLASS']->get_staff_id() // email to client
    ) {
            load_client_language($project->clientid);
            $languageChanged = true;
        } else {
            if (isset($GLOBALS['SENDING_EMAIL_TEMPLATE_CLASS'])) {
                $sending_to_staff_id = $GLOBALS['SENDING_EMAIL_TEMPLATE_CLASS']->get_staff_id();
                if ($sending_to_staff_id) {
                    load_admin_language($sending_to_staff_id);
                    $languageChanged = true;
                }
            }
        }

        $fields['{task_status}']   = format_task_status($task->status, false, true);
        $fields['{task_priority}'] = task_priority($task->priority);

        $custom_fields = get_custom_fields('tasks');
        foreach ($custom_fields as $field) {
            $fields['{' . $field['slug'] . '}'] = get_custom_field_value($task_id, $field['id'], 'tasks');
        }

        if (!is_client_logged_in() && $languageChanged) {
            load_admin_language();
        } elseif (is_client_logged_in() && $languageChanged) {
            load_client_language();
        }

        $fields['{task_startdate}'] = _d($task->startdate);
        $fields['{task_duedate}']   = _d($task->duedate);
        $fields['{comment_link}']   = '';

        $this->ci->db->where('taskid', $task_id);
        $this->ci->db->limit(1);
        $this->ci->db->order_by('dateadded', 'desc');
        $comment = $this->ci->db->get(db_prefix().'task_comments')->row();

        if ($comment) {
            $fields['{task_comment}'] = $comment->content;
            $fields['{comment_link}'] = $fields['{task_link}'] . '#comment_' . $comment->id;
        }

        return hooks()->apply_filters('task_merge_fields', $fields, [
        'id'              => $task_id,
        'task'            => $task,
        'client_template' => $client_template,
     ]);
    }
}
