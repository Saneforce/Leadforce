<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Acres99 extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('plugins_model');
        $this->load->model('leads_model');
    }

    public function lead()
    {
        $config = $this->plugins_model->get_config_by_plugin('99acres_lead');
        $data['configured_web_forms'] = '';
        $form_id = 0;
        if ($config) {
            $form_id = $config->config['web_form_id'];
            $this->db->where('id', $form_id);
            $form = $this->db->get(db_prefix() . 'web_to_lead')->row();
            if ($form) {
                $GLOBALS['locale'] = get_locale_key($form->language);

                $data['form_fields'] = json_decode($form->form_data);
                if (!$data['form_fields']) {
                    $data['form_fields'] = [];
                }
                $post_data =json_decode(file_get_contents("php://input"),true);
                $required  = [];

                foreach ($data['form_fields'] as $field) {
                    if (isset($field->required)) {
                        $required[] = $field->name;
                    }
                }
                if (is_gdpr() && get_option('gdpr_enable_terms_and_conditions_lead_form') == 1) {
                    $required[] = 'accept_terms_and_conditions';
                }
                foreach ($required as $field) {
                    if ($field == 'file-input') {
                        continue;
                    }
                    if (!isset($post_data[$field]) || isset($post_data[$field]) && empty($post_data[$field])) {
                        
                        $this->output->set_status_header(422);
                        die;
                    }
                }
                
                if (isset($post_data['g-recaptcha-response'])) {
                    unset($post_data['g-recaptcha-response']);
                }

                unset($post_data['key']);

                $regular_fields = [];
                $custom_fields  = [];
                foreach ($post_data as $name => $val) {
                    if (strpos($name, 'form-cf-') !== false) {
                        array_push($custom_fields, [
                            'name'  => $name,
                            'value' => $val,
                        ]);
                    } else {
                        if ($this->db->field_exists($name, db_prefix() . 'leads')) {
                            if ($name == 'country') {
                                if (!is_numeric($val)) {
                                    if ($val == '') {
                                        $val = 0;
                                    } else {
                                        $this->db->where('iso2', $val);
                                        $this->db->or_where('short_name', $val);
                                        $this->db->or_where('long_name', $val);
                                        $country = $this->db->get(db_prefix() . 'countries')->row();
                                        if ($country) {
                                            $val = $country->country_id;
                                        } else {
                                            $val = 0;
                                        }
                                    }
                                }
                            } elseif ($name == 'address') {
                                $val = trim($val);
                                $val = nl2br($val);
                            }

                            $regular_fields[$name] = $val;
                        }
                    }
                }
                $success      = false;
                $insert_to_db = true;
                
                if ($form->allow_duplicate == 0) {
                    $where = [];
                    if (!empty($form->track_duplicate_field) && isset($regular_fields[$form->track_duplicate_field])) {
                        $where[$form->track_duplicate_field] = $regular_fields[$form->track_duplicate_field];
                    }
                    if (!empty($form->track_duplicate_field_and) && isset($regular_fields[$form->track_duplicate_field_and])) {
                        $where[$form->track_duplicate_field_and] = $regular_fields[$form->track_duplicate_field_and];
                    }

                    if (count($where) > 0) {
                        $total = total_rows(db_prefix() . 'leads', $where);

                        $duplicateLead = false;
                        /**
                         * Check if the lead is only 1 time duplicate
                         * Because we wont be able to know how user is tracking duplicate and to send the email template for
                         * the request
                         */
                        if ($total == 1) {
                            $this->db->where($where);
                            $duplicateLead = $this->db->get(db_prefix() . 'leads')->row();
                        }

                        if ($total > 0) {
                            // Success set to true for the response.
                            $success      = true;
                            $insert_to_db = false;
                            if ($form->create_task_on_duplicate == 1) {
                                $task_name_from_form_name = false;
                                $task_name                = '';
                                if (isset($regular_fields['name'])) {
                                    $task_name = $regular_fields['name'];
                                } elseif (isset($regular_fields['email'])) {
                                    $task_name = $regular_fields['email'];
                                } elseif (isset($regular_fields['company'])) {
                                    $task_name = $regular_fields['company'];
                                } else {
                                    $task_name_from_form_name = true;
                                    $task_name                = $form->name;
                                }
                                if ($task_name_from_form_name == false) {
                                    $task_name .= ' - ' . $form->name;
                                }

                                $description          = '';
                                $custom_fields_parsed = [];
                                foreach ($custom_fields as $key => $field) {
                                    $custom_fields_parsed[$field['name']] = $field['value'];
                                }

                                $all_fields    = array_merge($regular_fields, $custom_fields_parsed);
                                $fields_labels = [];
                                foreach ($data['form_fields'] as $f) {
                                    if ($f->type != 'header' && $f->type != 'paragraph' && $f->type != 'file') {
                                        $fields_labels[$f->name] = $f->label;
                                    }
                                }

                                $description .= $form->name . '<br /><br />';
                                foreach ($all_fields as $name => $val) {
                                    if (isset($fields_labels[$name])) {
                                        if ($name == 'country' && is_numeric($val)) {
                                            $c = get_country($val);
                                            if ($c) {
                                                $val = $c->short_name;
                                            } else {
                                                $val = 'Unknown';
                                            }
                                        }

                                        $description .= $fields_labels[$name] . ': ' . $val . '<br />';
                                    }
                                }

                                $task_data = [
                                    'name'        => $task_name,
                                    'priority'    => get_option('default_task_priority'),
                                    'dateadded'   => date('Y-m-d H:i:s'),
                                    'startdate'   => date('Y-m-d'),
                                    'addedfrom'   => $form->responsible,
                                    'status'      => 1,
                                    'description' => $description,
                                ];

                                $task_data = hooks()->apply_filters('before_add_task', $task_data);
                                $this->db->insert(db_prefix() . 'tasks', $task_data);
                                $task_id = $this->db->insert_id();
                                if ($task_id) {
                                    $attachment = handle_task_attachments_array($task_id, 'file-input');
                                    if ($attachment && count($attachment) > 0) {
                                        $this->tasks_model->add_attachment_to_database($task_id, $attachment, false, false);
                                    }

                                    $assignee_data = [
                                        'taskid'   => $task_id,
                                        'assignee' => $form->responsible,
                                    ];
                                    $this->tasks_model->add_task_assignees($assignee_data, true);

                                    hooks()->do_action('after_add_task', $task_id);
                                    if ($duplicateLead && $duplicateLead->email != '') {
                                        send_mail_template('lead_web_form_submitted', $duplicateLead);
                                    }
                                }
                            }
                        }
                    }
                }

                if ($insert_to_db == true) {
                    $regular_fields['status'] = $form->lead_status;
                    if ((isset($regular_fields['name']) && empty($regular_fields['name'])) || !isset($regular_fields['name'])) {
                        $regular_fields['name'] = 'Unknown';
                    }
                    $regular_fields['source']       = $form->lead_source;
                    $regular_fields['addedfrom']    = 0;
                    $regular_fields['lastcontact']  = null;
                    $regular_fields['assigned']     = $form->responsible;
                    $regular_fields['dateadded']    = date('Y-m-d H:i:s');
                    $regular_fields['from_form_id'] = $form->id;
                    $regular_fields['is_public']    = $form->mark_public;
                    $this->db->insert(db_prefix() . 'leads', $regular_fields);
                    $lead_id = $this->db->insert_id();

                    hooks()->do_action('lead_created', $lead_id);
                    $success = false;
                    if ($lead_id) {
                        $success = true;

                        $this->leads_model->log_activity($lead_id, 'lead', 'addedfrom99acres', $form->id);
                        // /handle_custom_fields_post
                        $custom_fields_build['leads'] = [];
                        foreach ($custom_fields as $cf) {
                            $cf_id                                = strafter($cf['name'], 'form-cf-');
                            $custom_fields_build['leads'][$cf_id] = $cf['value'];
                        }

                        handle_custom_fields_post($lead_id, $custom_fields_build);

                        $this->leads_model->lead_assigned_member_notification($lead_id, $form->responsible, true);
                        //pre($_FILES); 
                        handle_lead_attachments($lead_id, 'file-input');
                    }
                }
                return;
            }
        }

        show_404();
    }
}
