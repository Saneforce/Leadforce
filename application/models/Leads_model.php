<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Leads_model extends App_Model {

    public function __construct() {
        parent::__construct();
        $this->load->model('products_model');
    }

    /**
     * Get lead
     * @param  string $id Optional - leadid
     * @return mixed
     */
    public function get($id = '', $where = []) {
        $this->db->select('*,' . db_prefix() . 'leads.name, ' . db_prefix() . 'leads.id,' . db_prefix() . 'leads_status.name as status_name,' . db_prefix() . 'pipeline.name as pipeline_name,' . db_prefix() . 'leads.status as status,' . db_prefix() . 'leads.teamleader as teamleader,' . db_prefix() . 'leads.assigned as assigned');

        $this->db->join(db_prefix() . 'leads_status', db_prefix() . 'leads_status.id=' . db_prefix() . 'leads.status', 'left');
        $this->db->join(db_prefix() . 'pipeline', db_prefix() . 'pipeline.id=' . db_prefix() . 'leads.pipeline_id', 'left');

        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'leads.id', $id);
            $lead = $this->db->get(db_prefix() . 'leads')->row();
            if ($lead) {
                if ($lead->from_form_id != 0) {
                    $lead->form_data = $this->get_form([
                        'id' => $lead->from_form_id,
                    ]);
                }
                $lead->attachments = $this->get_lead_attachments($id);
                $lead->public_url = leads_public_url($id);
            }

            return $lead;
        }

        return $this->db->get(db_prefix() . 'leads')->result_array();
    }

    public function do_kanban_query($status, $search = '', $page = 1, $sort = [], $count = false) {
        $limit = get_option('leads_kanban_limit');
        $default_leads_kanban_sort = get_option('default_leads_kanban_sort');
        $default_leads_kanban_sort_type = get_option('default_leads_kanban_sort_type');
        $has_permission_view = has_permission('leads', '', 'view');

        $this->db->select(db_prefix() . 'leads.title, ' . db_prefix() . 'leads.website, ' . db_prefix() . 'leads.address, ' . db_prefix() . 'leads.city, ' . db_prefix() . 'leads.state, ' . db_prefix() . 'leads.country, ' . db_prefix() . 'leads.zip, ' . db_prefix() . 'leads.name as lead_name,' . db_prefix() . 'pipeline.name as pipeline_name,' . db_prefix() . 'leads.id as id,' . db_prefix() . 'leads.teamleader as teamleader,' . db_prefix() . 'leads.assigned,' . db_prefix() . 'leads.email,' . db_prefix() . 'leads.phonenumber,' . db_prefix() . 'leads.company,' . db_prefix() . 'leads.dateadded,' . db_prefix() . 'leads.status,' . db_prefix() . 'leads.lastcontact,(SELECT COUNT(*) FROM ' . db_prefix() . 'clients WHERE leadid=' . db_prefix() . 'leads.id) as is_lead_client, (SELECT COUNT(id) FROM ' . db_prefix() . 'files WHERE rel_id=' . db_prefix() . 'leads.id AND rel_type="lead") as total_files, (SELECT COUNT(id) FROM ' . db_prefix() . 'notes WHERE rel_id=' . db_prefix() . 'leads.id AND rel_type="lead") as total_notes,(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'leads.id and rel_type="lead" ORDER by tag_order ASC) as tags');
        $this->db->from(db_prefix() . 'leads');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid=' . db_prefix() . 'leads.assigned', 'left');
        $this->db->join(db_prefix() . 'pipeline', db_prefix() . 'pipeline.id=' . db_prefix() . 'leads.pipeline_id', 'left');
        $this->db->where(db_prefix() . 'leads.status', $status);
        $pipeline = $this->session->userdata('pipeline');
        if (empty($pipeline)) {
            $pipeline = 0;
        }
        $this->db->where(db_prefix() . 'leads.pipeline_id', $pipeline);
        if (!$has_permission_view) {
            $this->db->where('(assigned = ' . get_staff_user_id() . ' OR addedfrom=' . get_staff_user_id() . ' OR is_public=1)');
        }
        if ($search != '') {
            if (!startsWith($search, '#')) {
                $this->db->where('(' . db_prefix() . 'leads.name LIKE "%' . $search . '%" OR ' . db_prefix() . 'leads.email LIKE "%' . $search . '%" OR ' . db_prefix() . 'leads.phonenumber LIKE "%' . $search . '%" OR ' . db_prefix() . 'leads.company LIKE "%' . $search . '%" OR CONCAT(' . db_prefix() . 'staff.firstname, \' \', ' . db_prefix() . 'staff.lastname) LIKE "%' . $search . '%")');
            } else {
                $this->db->where(db_prefix() . 'leads.id IN
                (SELECT rel_id FROM ' . db_prefix() . 'taggables WHERE tag_id IN
                (SELECT id FROM ' . db_prefix() . 'tags WHERE name="' . strafter($search, '#') . '")
                AND ' . db_prefix() . 'taggables.rel_type=\'lead\' GROUP BY rel_id HAVING COUNT(tag_id) = 1)
                ');
            }
        }

        if (isset($sort['sort_by']) && $sort['sort_by'] && isset($sort['sort']) && $sort['sort']) {
            $this->db->order_by($sort['sort_by'], $sort['sort']);
        } else {
            $this->db->order_by($default_leads_kanban_sort, $default_leads_kanban_sort_type);
        }

        if ($count == false) {
            if ($page > 1) {
                $page--;
                $position = ($page * $limit);
                $this->db->limit($limit, $position);
            } else {
                $this->db->limit($limit);
            }
        }

        if ($count == false) {
            return $this->db->get()->result_array();
        }

        return $this->db->count_all_results();
    }

    /**
     * Add new lead to database
     * @param mixed $data lead data
     * @return mixed false || leadid
     */
    public function add($data) {
        if(isset($data['emailuid'])){
            $emailuid =$data['emailuid'];
            unset($data['emailuid']);
        }
        $this->load->model('clients_model');
        if (isset($data['custom_contact_date']) || isset($data['custom_contact_date'])) {
            if (isset($data['contacted_today'])) {
                $data['lastcontact'] = date('Y-m-d H:i:s');
                unset($data['contacted_today']);
            } else {
                $data['lastcontact'] = to_sql_date($data['custom_contact_date'], true);
            }
        }

        if (isset($data['is_public']) && ($data['is_public'] == 1 || $data['is_public'] === 'on')) {
            $data['is_public'] = 1;
        } else {
            $data['is_public'] = 0;
        }

        if (!isset($data['country']) || isset($data['country']) && $data['country'] == '') {
            $data['country'] = 0;
        }

        if (isset($data['custom_contact_date'])) {
            unset($data['custom_contact_date']);
        }

        $data['description'] = nl2br($data['description']);
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $data = hooks()->apply_filters('before_lead_added', $data);

        $tags = '';
        if (isset($data['tags'])) {
            $tags = $data['tags'];
            unset($data['tags']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $data['address'] = trim($data['address']);
        $data['address'] = nl2br($data['address']);

        $data['email'] = trim($data['email']);

        if(isset($data['view_source'])) {
            $data['source'] = $data['view_source'];
            unset($data['view_source']);
        }
        
        if(isset($data['client_id']) && $data['client_id']=='' && strlen(trim($data['company'])) >0){
            $companyData =array(
                'company'=>$data['company'],
                'phonenumber'=>$data['clientphonenumber'],
                'phone_country_code'=>$data['clientphone_country_code'],
                'country'=>$data['country'],
                'city'=>$data['city'],
                'state'=>$data['state'],
                'address'=>$data['address'],
                'website'=>$data['website'],
                'zip'=>$data['zip'],
            );
            
            $data['client_id']=$this->clients_model->add($companyData);
        }
        unset($data['country']);
        unset($data['clientphonenumber']);
        unset($data['clientphone_country_code']);

        $contactid =$data['contactid'];
        if(isset($data['contactid']) && $data['contactid'] ==''  && strlen(trim($data['personname'])) >0){
            $contact_data =array(
                'is_primary'=>0,
                'userid'=>isset($data['client_id']) && $data['client_id']?$data['client_id']:0,
                'firstname'=>$data['personname'],
                'lastname'=>'',
                'email'=>$data['email'],
                'phonenumber'=>$data['phonenumber'],
                'title'=>$data['title'],
                'phone_country_code'=>$data['phone_country_code'],
                'alternative_emails'=>'',
                'alternative_phonenumber'=>'',
                'addedfrom'=>$data['addedfrom'],

            );
            $contactid =$this->clients_model->add_contact($contact_data,0);
            if($contactid && isset($data['client_id']) && $data['client_id']){
                $this->db->insert(db_prefix() . 'contacts_clients',array('userid'=>$data['client_id'],'contactid'=>$contactid,'is_primary'=>1));
            }
        }
        unset($data['personname']);
        unset($data['phone_country_code']);
        unset($data['contactid']);
        
        $products = array();
        if(isset($data['product']) && !empty($data['product'])) {
            $products['product'] = $data['product'];
            $products['price'] = $data['price'];
            $products['qty'] = $data['qty'];
            $products['total'] = $data['total'];
            
            unset($data['product']);
            unset($data['price']);
            unset($data['qty']);
            unset($data['total']);
        }
        $products['grandtotal'] = $data['grandtotal'];
        $products['method'] = $data['method'];
        $products['tax'] = $data['tax'];
        $products['discount'] = $data['discount'];
        $products['status'] = $data['status'];
        $products['variation'] = $data['variation'];
        
        unset($data['method']);
        unset($data['tax']);
        unset($data['discount']);
        unset($data['status']);
        unset($data['variation']);
        unset($data['grandtotal']);
        $data['lead_cost'] =$data['project_cost'];
        unset($data['project_cost']);

        $currency = $data['currency'];
        $data['lead_currency'] =$currency;
        unset($data['currency']);

        foreach($data['no'] as $val) {
            if($data['status_'.$val]) {
                unset($data['status_'.$val]);
            }
        }

        unset($data['no']);
        $this->db->insert(db_prefix() . 'leads', $data);
        $insert_id = $this->db->insert_id();
        if($contactid>0){
            $this->db->insert(db_prefix().'lead_contacts',array('lead_id'=>$insert_id,'contacts_id'=>$contactid,'is_primary'=>1));
        }
        if ($insert_id) {
            
            if($products) {
                $this->products_model->save_lead_products($products, $insert_id, $currency);   
            }

            

            
            if(isset($emailuid)){
                $this->leads_model->log_activity($insert_id,'lead','addedfromemail',$insert_id);
                $this->load->library('mails/imap_mailer');
                $this->imap_mailer->set_rel_type('lead');
                $this->imap_mailer->set_rel_id($insert_id);
                $this->imap_mailer->connectEmail($emailuid);
            }else{
                $this->leads_model->log_activity($insert_id,'lead','added',$insert_id);
            }
            

            handle_tags_save($tags, $insert_id, 'lead');

            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }

            // $this->lead_assigned_member_notification($insert_id, $data['assigned']);
            hooks()->do_action('lead_created', $insert_id);

            return $insert_id;
        }

        return false;
    }

    public function lead_assigned_member_notification($lead_id, $assigned, $integration = false) {
        if ((!empty($assigned) && $assigned != 0)) {
            if ($integration == false) {
                if ($assigned == get_staff_user_id()) {
                    return false;
                }
            }

            $name = $this->db->select('name')->from(db_prefix() . 'leads')->where('id', $lead_id)->get()->row()->name;

            $notification_data = [
                'description' => ($integration == false) ? 'not_assigned_lead_to_you' : 'not_lead_assigned_from_form',
                'touserid' => $assigned,
                'link' => '#leadid=' . $lead_id,
                'additional_data' => ($integration == false ? serialize([
                    $name,
                ]) : serialize([])),
            ];

            if ($integration != false) {
                $notification_data['fromcompany'] = 1;
            }

            if (add_notification($notification_data)) {
                pusher_trigger_notification([$assigned]);
            }

            $this->db->select('email');
            $this->db->where('staffid', $assigned);
            $email = $this->db->get(db_prefix() . 'staff')->row()->email;

            send_mail_template('lead_assigned', $lead_id, $email);

            $this->db->where('id', $lead_id);
            $this->db->update(db_prefix() . 'leads', [
                'dateassigned' => date('Y-m-d'),
            ]);

            $not_additional_data = [
                get_staff_full_name(),
                '<a href="' . admin_url('profile/' . $assigned) . '" target="_blank">' . get_staff_full_name($assigned) . '</a>',
            ];

            if ($integration == true) {
                unset($not_additional_data[0]);
                array_values(($not_additional_data));
            }

            $not_additional_data = serialize($not_additional_data);

            $not_desc = ($integration == false ? 'not_lead_activity_assigned_to' : 'not_lead_activity_assigned_from_form');
            $this->log_lead_activity($lead_id, $not_desc, $integration, $not_additional_data);
        }
    }

    /**
     * Update lead
     * @param  array $data lead data
     * @param  mixed $id   leadid
     * @return boolean
     */
    public function update($data, $id) {
        $current_lead_data = $this->get($id);
        $current_status = $this->get_status($current_lead_data->status);
        if ($current_status) {
            $current_status_id = $current_status->id;
            $current_status = $current_status->name;
        } else {
            if ($current_lead_data->junk == 1) {
                $current_status = _l('lead_junk');
            } elseif ($current_lead_data->lost == 1) {
                $current_status = _l('lead_lost');
            } else {
                $current_status = '';
            }
            $current_status_id = 0;
        }

        $affectedRows = 0;
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }
        if (!defined('API')) {
            if (isset($data['is_public'])) {
                $data['is_public'] = 1;
            } else {
                $data['is_public'] = 0;
            }

            if (!isset($data['country']) || isset($data['country']) && $data['country'] == '') {
                $data['country'] = 0;
            }

            if (isset($data['description'])) {
                $data['description'] = nl2br($data['description']);
            }
        }

        if (isset($data['lastcontact']) && $data['lastcontact'] == '' || isset($data['lastcontact']) && $data['lastcontact'] == null) {
            $data['lastcontact'] = null;
        } elseif (isset($data['lastcontact'])) {
            $data['lastcontact'] = to_sql_date($data['lastcontact'], true);
        }

        if (isset($data['tags'])) {
            if (handle_tags_save($data['tags'], $id, 'lead')) {
                $affectedRows++;
            }
            unset($data['tags']);
        }

        if (isset($data['remove_attachments'])) {
            foreach ($data['remove_attachments'] as $key => $val) {
                $attachment = $this->get_lead_attachments($id, $key);
                if ($attachment) {
                    $this->delete_lead_attachment($attachment->id);
                }
            }
            unset($data['remove_attachments']);
        }

        $data['address'] = trim($data['address']);
        $data['address'] = nl2br($data['address']);

        $data['email'] = trim($data['email']);
        
        if(isset($data['view_source'])) {
            $data['source'] = $data['view_source'];
            unset($data['view_source']);
        }
       
        if(isset($data['client_id']) && $data['client_id']=='' && strlen(trim($data['company'])) >0){
            $companyData =array(
                'company'=>$data['company'],
                'phonenumber'=>$data['clientphonenumber'],
                'phone_country_code'=>$data['clientphone_country_code'],
                'country'=>$data['country'],
                'city'=>$data['city'],
                'state'=>$data['state'],
                'address'=>$data['address'],
                'website'=>$data['website'],
                'zip'=>$data['zip']
            );
            
            $data['client_id']=$this->clients_model->add($companyData);
        }
        unset($data['country']);
        unset($data['clientphonenumber']);
        unset($data['clientphone_country_code']);

        $contactid =$data['contactid'];
        if(isset($data['contactid']) && $data['contactid'] ==''  && strlen(trim($data['personname'])) >0){
            $contact_data =array(
                'is_primary'=>0,
                'userid'=>isset($data['client_id']) && $data['client_id']?$data['client_id']:0,
                'firstname'=>$data['personname'],
                'lastname'=>'',
                'email'=>$data['email'],
                'phonenumber'=>$data['phonenumber'],
                'title'=>$data['title'],
                'phone_country_code'=>$data['phone_country_code'],
                'alternative_emails'=>'',
                'alternative_phonenumber'=>'',
                'addedfrom'=>get_staff_user_id(),

            );
            $contactid =$this->clients_model->add_contact($contact_data,0);
            if($contactid && isset($data['client_id']) && $data['client_id']){
                $this->db->insert(db_prefix() . 'contacts_clients',array('userid'=>$data['client_id'],'contactid'=>$contactid,'is_primary'=>1));
            }
        }
        unset($data['personname']);
        unset($data['phone_country_code']);
        unset($data['contactid']);
        

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'leads', $data);

        if($contactid>0){
            $this->db->where('lead_id',$id);
            $this->db->delete(db_prefix().'lead_contacts');
            $this->db->insert(db_prefix().'lead_contacts',array('lead_id'=>$id,'contacts_id'=>$contactid,'is_primary'=>1));
        }

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            if (isset($data['status']) && $current_status_id != $data['status']) {
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'leads', [
                    'last_status_change' => date('Y-m-d H:i:s'),
                ]);
                $new_status_name = $this->get_status($data['status'])->name;
                $this->log_lead_activity($id, 'not_lead_activity_status_updated', false, serialize([
                    get_staff_full_name(),
                    $current_status,
                    $new_status_name,
                ]));

                hooks()->do_action('lead_status_changed', [
                    'lead_id' => $id,
                    'old_status' => $current_status_id,
                    'new_status' => $data['status'],
                ]);
            }

            if (($current_lead_data->junk == 1 || $current_lead_data->lost == 1) && $data['status'] != 0) {
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'leads', [
                    'junk' => 0,
                    'lost' => 0,
                ]);
            }

            if (isset($data['assigned'])) {
                if ($current_lead_data->assigned != $data['assigned'] && (!empty($data['assigned']) && $data['assigned'] != 0)) {
                    $this->lead_assigned_member_notification($id, $data['assigned']);
                }
            }
            log_activity('Lead Updated [ID: ' . $id . ']');

            return true;
        }
        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    /**
     * Delete lead from database and all connections
     * @param  mixed $id leadid
     * @return boolean
     */
    public function delete($id) {
        $affectedRows = 0;

        hooks()->do_action('before_lead_deleted', $id);

        $lead = $this->get($id);

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'leads');
        if ($this->db->affected_rows() > 0) {
            log_activity('Lead Deleted [Deleted by: ' . get_staff_full_name() . ', ID: ' . $id . ']');

            $attachments = $this->get_lead_attachments($id);
            foreach ($attachments as $attachment) {
                $this->delete_lead_attachment($attachment['id']);
            }

            // Delete the custom field values
            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'leads');
            $this->db->delete(db_prefix() . 'customfieldsvalues');

            $this->db->where('leadid', $id);
            $this->db->delete(db_prefix() . 'lead_activity_log');

            $this->db->where('leadid', $id);
            $this->db->delete(db_prefix() . 'lead_integration_emails');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'lead');
            $this->db->delete(db_prefix() . 'notes');

            $this->db->where('rel_type', 'lead');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'reminders');

            $this->db->where('rel_type', 'lead');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'taggables');

            $this->load->model('proposals_model');
            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'lead');
            $proposals = $this->db->get(db_prefix() . 'proposals')->result_array();

            foreach ($proposals as $proposal) {
                $this->proposals_model->delete($proposal['id']);
            }

            // Get related tasks
            $this->db->where('rel_type', 'lead');
            $this->db->where('rel_id', $id);
            $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();
            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id']);
            }

            if (is_gdpr()) {
                $this->db->where('(description LIKE "%' . $lead->email . '%" OR description LIKE "%' . $lead->name . '%" OR description LIKE "%' . $lead->phonenumber . '%")');
                $this->db->delete(db_prefix() . 'activity_log');
            }

            $affectedRows++;
        }
        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    /**
     * Mark lead as lost
     * @param  mixed $id lead id
     * @return boolean
     */
    public function mark_as_lost($id) {
        $this->db->select('status');
        $this->db->from(db_prefix() . 'leads');
        $this->db->where('id', $id);
        $last_lead_status = $this->db->get()->row()->status;

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'leads', [
            'lost' => 1,
            'status' => 0,
            'last_status_change' => date('Y-m-d H:i:s'),
            'last_lead_status' => $last_lead_status,
        ]);

        if ($this->db->affected_rows() > 0) {
            $this->log_lead_activity($id, 'not_lead_activity_marked_lost');

            log_activity('Lead Marked as Lost [ID: ' . $id . ']');

            hooks()->do_action('lead_marked_as_lost', $id);

            return true;
        }

        return false;
    }

    /**
     * Unmark lead as lost
     * @param  mixed $id leadid
     * @return boolean
     */
    public function unmark_as_lost($id) {
        $this->db->select('last_lead_status');
        $this->db->from(db_prefix() . 'leads');
        $this->db->where('id', $id);
        $last_lead_status = $this->db->get()->row()->last_lead_status;

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'leads', [
            'lost' => 0,
            'status' => $last_lead_status,
        ]);
        if ($this->db->affected_rows() > 0) {
            $this->log_lead_activity($id, 'not_lead_activity_unmarked_lost');

            log_activity('Lead Unmarked as Lost [ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Mark lead as junk
     * @param  mixed $id lead id
     * @return boolean
     */
    public function mark_as_junk($id) {
        $this->db->select('status');
        $this->db->from(db_prefix() . 'leads');
        $this->db->where('id', $id);
        $last_lead_status = $this->db->get()->row()->status;

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'leads', [
            'junk' => 1,
            'status' => 0,
            'last_status_change' => date('Y-m-d H:i:s'),
            'last_lead_status' => $last_lead_status,
        ]);

        if ($this->db->affected_rows() > 0) {
            $this->log_lead_activity($id, 'not_lead_activity_marked_junk');

            log_activity('Lead Marked as Junk [ID: ' . $id . ']');

            hooks()->do_action('lead_marked_as_junk', $id);

            return true;
        }

        return false;
    }

    /**
     * Unmark lead as junk
     * @param  mixed $id leadid
     * @return boolean
     */
    public function unmark_as_junk($id) {
        $this->db->select('last_lead_status');
        $this->db->from(db_prefix() . 'leads');
        $this->db->where('id', $id);
        $last_lead_status = $this->db->get()->row()->last_lead_status;

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'leads', [
            'junk' => 0,
            'status' => $last_lead_status,
        ]);
        if ($this->db->affected_rows() > 0) {
            $this->log_lead_activity($id, 'not_lead_activity_unmarked_junk');
            log_activity('Lead Unmarked as Junk [ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Get lead attachments
     * @since Version 1.0.4
     * @param  mixed $id lead id
     * @return array
     */
    public function get_lead_attachments($id = '', $attachment_id = '', $where = []) {
        $this->db->where($where);
        $idIsHash = !is_numeric($attachment_id) && strlen($attachment_id) == 32;
        if (is_numeric($attachment_id) || $idIsHash) {
            $this->db->where($idIsHash ? 'attachment_key' : 'id', $attachment_id);

            return $this->db->get(db_prefix() . 'files')->row();
        }
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'lead');
        $this->db->order_by('dateadded', 'DESC');

        return $this->db->get(db_prefix() . 'files')->result_array();
    }

    public function add_attachment_to_database($lead_id, $attachment, $external = false, $form_activity = false) {
        $attachment_id =$this->misc_model->add_attachment_to_database($lead_id, 'lead', $attachment, $external);
        
        $this->leads_model->log_activity($lead_id,'attachment','added',$attachment_id);

        if ($form_activity == false) {
            $this->leads_model->log_lead_activity($lead_id, 'not_lead_activity_added_attachment');
        } else {
            $this->leads_model->log_lead_activity($lead_id, 'not_lead_activity_log_attachment', true, serialize([
                $form_activity,
            ]));
        }

        // No notification when attachment is imported from web to lead form
        if ($form_activity == false) {
            $lead = $this->get($lead_id);
            $not_user_ids = [];
            if ($lead->addedfrom != get_staff_user_id()) {
                array_push($not_user_ids, $lead->addedfrom);
            }
            if ($lead->assigned != get_staff_user_id() && $lead->assigned != 0) {
                array_push($not_user_ids, $lead->assigned);
            }
            $notifiedUsers = [];
            foreach ($not_user_ids as $uid) {
                $notified = add_notification([
                    'description' => 'not_lead_added_attachment',
                    'touserid' => $uid,
                    'link' => '#leadid=' . $lead_id,
                    'additional_data' => serialize([
                        $lead->name,
                    ]),
                ]);
                if ($notified) {
                    array_push($notifiedUsers, $uid);
                }
            }
            pusher_trigger_notification($notifiedUsers);
        }
    }

    /**
     * Delete lead attachment
     * @param  mixed $id attachment id
     * @return boolean
     */
    public function delete_lead_attachment($id) {
        $attachment = $this->get_lead_attachments('', $id);
        $deleted = false;

        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(get_upload_path_by_type('lead') . $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete(db_prefix() . 'files');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                log_activity('Lead Attachment Deleted [ID: ' . $attachment->rel_id . ']');
            }

            if (is_dir(get_upload_path_by_type('lead') . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('lead') . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(get_upload_path_by_type('lead') . $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    // Sources

    /**
     * Get leads sources
     * @param  mixed $id Optional - Source ID
     * @return mixed object if id passed else array
     */
    public function get_source($id = false) {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'leads_sources')->row();
        }

        $this->db->order_by('name', 'asc');

        return $this->db->get(db_prefix() . 'leads_sources')->result_array();
    }

    public function get_source_admin($id = false) {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'leads_sources')->row();
        }

        $this->db->order_by('name', 'asc');
        $this->db->where('slug !=', 'manual');
        $this->db->where('slug !=', 'webtolead');
        return $this->db->get(db_prefix() . 'leads_sources')->result_array();
    }

    /**
     * Add new lead source
     * @param mixed $data source data
     */
    public function add_source($data) {
        $this->db->insert(db_prefix() . 'leads_sources', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Leads Source Added [SourceID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
        }

        return $insert_id;
    }

    /**
     * Update lead source
     * @param  mixed $data source data
     * @param  mixed $id   source id
     * @return boolean
     */
    public function update_source($data, $id) {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'leads_sources', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Leads Source Updated [SourceID: ' . $id . ', Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    public function update_source_fields($data, $id) {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'leads_sources', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Delete lead source from database
     * @param  mixed $id source id
     * @return mixed
     */
    public function delete_source($id) {
        $current = $this->get_source($id);
        // Check if is already using in table
        if (is_reference_in_table('source', db_prefix() . 'leads', $id) || is_reference_in_table('lead_source', db_prefix() . 'leads_email_integration', $id)) {
            return [
                'referenced' => true,
            ];
        }
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'leads_sources');
        if ($this->db->affected_rows() > 0) {
            if (get_option('leads_default_source') == $id) {
                update_option('leads_default_source', '');
            }
            log_activity('Leads Source Deleted [SourceID: ' . $id . ']');

            return true;
        }

        return false;
    }

    // Statuses

    /**
     * Get lead statuses
     * @param  mixed $id status id
     * @return mixed      object if id passed else array
     */
    public function get_status($id = '', $where = []) {
        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'projects_status')->row();
        }

        $statuses = $this->app_object_cache->get('projects-all-statuses');

        if (!$statuses) {
            $this->db->order_by('statusorder', 'asc');

            $statuses = $this->db->get(db_prefix() . 'projects_status')->result_array();
            $this->app_object_cache->add('projects-all-statuses', $statuses);
        }

        return $statuses;
    }

    /**
     * Add new lead status
     * @param array $data lead status data
     */
    public function add_status($data) {
        if (isset($data['color']) && $data['color'] == '') {
            $data['color'] = hooks()->apply_filters('default_lead_status_color', '#757575');
        }

        if (!isset($data['statusorder'])) {
            $data['statusorder'] = total_rows(db_prefix() . 'projects_status') + 1;
        }

        $this->db->insert(db_prefix() . 'projects_status', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Leads Status Added [StatusID: ' . $insert_id . ', Name: ' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }

    public function update_status($data, $id) {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'projects_status', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Leads Status Updated [StatusID: ' . $id . ', Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete lead status from database
     * @param  mixed $id status id
     * @return boolean
     */
    public function delete_status($id) {
        $current = $this->get_status($id);
        // Check if is already using in table
        if (is_reference_in_table('status', db_prefix() . 'leads', $id) || is_reference_in_table('projects_status', db_prefix() . 'leads_email_integration', $id)) {
            return [
                'referenced' => true,
            ];
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'projects_status');
        if ($this->db->affected_rows() > 0) {
            if (get_option('leads_default_status') == $id) {
                update_option('leads_default_status', '');
            }
            log_activity('Leads Status Deleted [StatusID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Update canban lead status when drag and drop
     * @param  array $data lead data
     * @return boolean
     */
    public function update_lead_status($data) {
        $this->db->select('status');
        $this->db->where('id', $data['leadid']);
        $_old = $this->db->get(db_prefix() . 'leads')->row();

        $old_status = '';

        if ($_old) {
            $old_status = $this->get_status($_old->status);
            if ($old_status) {
                $old_status = $old_status->name;
            }
        }

        $affectedRows = 0;
        $current_status = $this->get_status($data['status'])->name;

        $this->db->where('id', $data['leadid']);
        $this->db->update(db_prefix() . 'leads', [
            'status' => $data['status'],
        ]);

        $_log_message = '';

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            if ($current_status != $old_status && $old_status != '') {
                $_log_message = 'not_lead_activity_status_updated';
                $additional_data = serialize([
                    get_staff_full_name(),
                    $old_status,
                    $current_status,
                ]);

                hooks()->do_action('lead_status_changed', [
                    'lead_id' => $data['leadid'],
                    'old_status' => $old_status,
                    'new_status' => $current_status,
                ]);
            }
            $this->db->where('id', $data['leadid']);
            $this->db->update(db_prefix() . 'leads', [
                'last_status_change' => date('Y-m-d H:i:s'),
            ]);
        }
        if (isset($data['order'])) {
            foreach ($data['order'] as $order_data) {
                $this->db->where('id', $order_data[0]);
                $this->db->update(db_prefix() . 'leads', [
                    'leadorder' => $order_data[1],
                ]);
            }
        }
        if ($affectedRows > 0) {
            if ($_log_message == '') {
                return true;
            }
            $this->log_lead_activity($data['leadid'], $_log_message, false, $additional_data);

            return true;
        }

        return false;
    }

    /* Ajax */

    /**
     * All lead activity by staff
     * @param  mixed $id lead id
     * @return array
     */
    public function get_lead_activity_log($id) {
        $sorting = hooks()->apply_filters('lead_activity_log_default_sort', 'ASC');

        $this->db->where('leadid', $id);
        $this->db->order_by('date', $sorting);

        return $this->db->get(db_prefix() . 'lead_activity_log')->result_array();
    }

    public function staff_can_access_lead($id, $staff_id = '') {
        $staff_id = $staff_id == '' ? get_staff_user_id() : $staff_id;

        if (has_permission('leads', $staff_id, 'view')) {
            return true;
        }

        if (total_rows(db_prefix() . 'leads', 'id="' . $id . '" AND (assigned=' . $staff_id . ' OR is_public=1 OR addedfrom=' . $staff_id . ')') > 0) {
            return true;
        }

        return false;
    }

    /**
     * Add lead activity from staff
     * @param  mixed  $id          lead id
     * @param  string  $description activity description
     */
    public function log_lead_activity($id, $description, $integration = false, $additional_data = '') {
        $log = [
            'date' => date('Y-m-d H:i:s'),
            'description' => $description,
            'leadid' => $id,
            'staffid' => get_staff_user_id(),
            'additional_data' => $additional_data,
            'full_name' => get_staff_full_name(get_staff_user_id()),
        ];
        if ($integration == true) {
            $log['staffid'] = 0;
            $log['full_name'] = '[CRON]';
        }

        $this->db->insert(db_prefix() . 'lead_activity_log', $log);

        return $this->db->insert_id();
    }

    /**
     * Get email integration config
     * @return object
     */
    public function get_email_integration() {
        $this->db->where('id', 1);

        return $this->db->get(db_prefix() . 'leads_email_integration')->row();
    }

    /**
     * Get lead imported email activity
     * @param  mixed $id leadid
     * @return array
     */
    public function get_mail_activity($id) {
        $this->db->where('leadid', $id);
        $this->db->order_by('dateadded', 'asc');

        return $this->db->get(db_prefix() . 'lead_integration_emails')->result_array();
    }

    /**
     * Update email integration config
     * @param  mixed $data All $_POST data
     * @return boolean
     */
    public function update_email_integration($data) {
        $this->db->where('id', 1);
        $original_settings = $this->db->get(db_prefix() . 'leads_email_integration')->row();

        $data['create_task_if_customer'] = isset($data['create_task_if_customer']) ? 1 : 0;
        $data['active'] = isset($data['active']) ? 1 : 0;
        $data['delete_after_import'] = isset($data['delete_after_import']) ? 1 : 0;
        $data['notify_lead_imported'] = isset($data['notify_lead_imported']) ? 1 : 0;
        $data['only_loop_on_unseen_emails'] = isset($data['only_loop_on_unseen_emails']) ? 1 : 0;
        $data['notify_lead_contact_more_times'] = isset($data['notify_lead_contact_more_times']) ? 1 : 0;
        $data['mark_public'] = isset($data['mark_public']) ? 1 : 0;
        $data['responsible'] = !isset($data['responsible']) ? 0 : $data['responsible'];

        if ($data['notify_lead_contact_more_times'] != 0 || $data['notify_lead_imported'] != 0) {
            if (isset($data['notify_type']) && $data['notify_type'] == 'specific_staff') {
                if (isset($data['notify_ids_staff'])) {
                    $data['notify_ids'] = serialize($data['notify_ids_staff']);
                    unset($data['notify_ids_staff']);
                } else {
                    $data['notify_ids'] = serialize([]);
                    unset($data['notify_ids_staff']);
                }
                if (isset($data['notify_ids_roles'])) {
                    unset($data['notify_ids_roles']);
                }
            } else {
                if (isset($data['notify_ids_roles'])) {
                    $data['notify_ids'] = serialize($data['notify_ids_roles']);
                    unset($data['notify_ids_roles']);
                } else {
                    $data['notify_ids'] = serialize([]);
                    unset($data['notify_ids_roles']);
                }
                if (isset($data['notify_ids_staff'])) {
                    unset($data['notify_ids_staff']);
                }
            }
        } else {
            $data['notify_ids'] = serialize([]);
            $data['notify_type'] = null;
            if (isset($data['notify_ids_staff'])) {
                unset($data['notify_ids_staff']);
            }
            if (isset($data['notify_ids_roles'])) {
                unset($data['notify_ids_roles']);
            }
        }

        // Check if not empty $data['password']
        // Get original
        // Decrypt original
        // Compare with $data['password']
        // If equal unset
        // If not encrypt and save
        if (!empty($data['password'])) {
            $or_decrypted = $this->encryption->decrypt($original_settings->password);
            if ($or_decrypted == $data['password']) {
                unset($data['password']);
            } else {
                $data['password'] = $this->encryption->encrypt($data['password']);
            }
        }

        $this->db->where('id', 1);
        $this->db->update(db_prefix() . 'leads_email_integration', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function change_status_color($data) {
        $this->db->where('id', $data['status_id']);
        $this->db->update(db_prefix() . 'leads_status', [
            'color' => $data['color'],
        ]);
    }

    public function update_status_order($data) {
        foreach ($data['order'] as $status) {
            $this->db->where('id', $status[0]);
            $this->db->update(db_prefix() . 'leads_status', [
                'statusorder' => $status[1],
            ]);
        }
    }

    public function get_form($where) {
        $this->db->where($where);

        return $this->db->get(db_prefix() . 'web_to_lead')->row();
    }

    public function add_form($data) {
        $data = $this->_do_lead_web_to_form_responsibles($data);
        $data['success_submit_msg'] = nl2br($data['success_submit_msg']);
        $data['form_key'] = app_generate_hash();

        $data['create_task_on_duplicate'] = (int) isset($data['create_task_on_duplicate']);
        $data['mark_public'] = (int) isset($data['mark_public']);

        if (isset($data['allow_duplicate'])) {
            $data['allow_duplicate'] = 1;
            $data['track_duplicate_field'] = '';
            $data['track_duplicate_field_and'] = '';
            $data['create_task_on_duplicate'] = 0;
        } else {
            $data['allow_duplicate'] = 0;
        }

        $data['dateadded'] = date('Y-m-d H:i:s');

        $this->db->insert(db_prefix() . 'web_to_lead', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Web to Lead Form Added [' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }

    public function update_form($id, $data) {
        $data = $this->_do_lead_web_to_form_responsibles($data);
        $data['success_submit_msg'] = nl2br($data['success_submit_msg']);

        $data['create_task_on_duplicate'] = (int) isset($data['create_task_on_duplicate']);
        $data['mark_public'] = (int) isset($data['mark_public']);

        if (isset($data['allow_duplicate'])) {
            $data['allow_duplicate'] = 1;
            $data['track_duplicate_field'] = '';
            $data['track_duplicate_field_and'] = '';
            $data['create_task_on_duplicate'] = 0;
        } else {
            $data['allow_duplicate'] = 0;
        }


        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'web_to_lead', $data);

        return ($this->db->affected_rows() > 0 ? true : false);
    }

    public function delete_form($id) {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'web_to_lead');

        $this->db->where('from_form_id', $id);
        $this->db->update(db_prefix() . 'leads', [
            'from_form_id' => 0,
        ]);

        if ($this->db->affected_rows() > 0) {
            log_activity('Lead Form Deleted [' . $id . ']');

            return true;
        }

        return false;
    }

    private function _do_lead_web_to_form_responsibles($data) {
        if (isset($data['notify_lead_imported'])) {
            $data['notify_lead_imported'] = 1;
        } else {
            $data['notify_lead_imported'] = 0;
        }

        if ($data['responsible'] == '') {
            $data['responsible'] = 0;
        }
        if ($data['notify_lead_imported'] != 0) {
            if ($data['notify_type'] == 'specific_staff') {
                if (isset($data['notify_ids_staff'])) {
                    $data['notify_ids'] = serialize($data['notify_ids_staff']);
                    unset($data['notify_ids_staff']);
                } else {
                    $data['notify_ids'] = serialize([]);
                    unset($data['notify_ids_staff']);
                }
                if (isset($data['notify_ids_roles'])) {
                    unset($data['notify_ids_roles']);
                }
            } else {
                if (isset($data['notify_ids_roles'])) {
                    $data['notify_ids'] = serialize($data['notify_ids_roles']);
                    unset($data['notify_ids_roles']);
                } else {
                    $data['notify_ids'] = serialize([]);
                    unset($data['notify_ids_roles']);
                }
                if (isset($data['notify_ids_staff'])) {
                    unset($data['notify_ids_staff']);
                }
            }
        } else {
            $data['notify_ids'] = serialize([]);
            $data['notify_type'] = null;
            if (isset($data['notify_ids_staff'])) {
                unset($data['notify_ids_staff']);
            }
            if (isset($data['notify_ids_roles'])) {
                unset($data['notify_ids_roles']);
            }
        }

        return $data;
    }

    /**
     * Get  forecast data
     * @param  mixed $id Optional - Source ID
     * @return mixed object if id passed else array
     */
    public function get_forecast_data($id = false) {
        $return = array();
        $return['list'] = array();
        $return['date'] = date('Y-m-d');
        $this->db->select('*,' . db_prefix() . 'leads.name, ' . db_prefix() . 'leads.id,' . db_prefix() . 'leads_status.name as status_name,' . db_prefix() . 'leads_sources.name as source_name,' . db_prefix() . 'pipeline.name as pipeline_name,' . db_prefix() . 'leads.status as status');

        $this->db->join(db_prefix() . 'leads_status', db_prefix() . 'leads_status.id=' . db_prefix() . 'leads.status', 'left');
        $this->db->join(db_prefix() . 'leads_sources', db_prefix() . 'leads_sources.id=' . db_prefix() . 'leads.source', 'left');
        $this->db->join(db_prefix() . 'pipeline', db_prefix() . 'pipeline.id=' . db_prefix() . 'leads.pipeline_id', 'left');
        $this->db->where('pipeline_id', $this->session->userdata('pipeline'));
        $tdata = $this->db->get(db_prefix() . 'leads')->result_array();
//        pr($tdata);
        foreach ($tdata as $lkey => $lvalue) {
            $this->db->where('rel_type', 'lead');
            $this->db->where('rel_id', $lvalue['id']);
            $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();
            $push = 0;
//            pr($tasks);
            foreach ($tasks as $ltkey => $ltvalue) {
                $tdata = array();
                if ($push == 0) {
                    $tdata['name'] = '<a href="' . base_url('admin/leads/index/' . $lvalue['id']) . '" onclick="init_lead(' . $lvalue['id'] . ');return false;" class="pull-left">' . $lvalue['name'] . '</a>';
                    $push = $lvalue['id'];
                }
                $tdata['desc'] = $ltvalue['name'];
                $description = $ltvalue['name'];
                if (!empty($ltvalue['name'])) {
                    $description = 'Name : <b>' . $description . '</b>';
                    $description .= '<br />';
                }
               
                $customClass = 'ganttGreen';
                switch ($ltvalue['status']) {
                    case 1:
                        $customClass = 'ganttRed';
                        $description .= 'Status : <b>Not Started</b>';
                        break;
                    case 2:
                        $customClass = 'ganttGray';
                        $description .= 'Status : <b>Awaiting Feedback</b>';
                        break;
                    case 3:
                        $customClass = 'ganttOrange';
                        $description .= 'Status : <b>Testing</b>';
                        break;
                    case 4:
                        $customClass = 'ganttBlue';
                        $description .= 'Status : <b>In Progress</b>';
                        break;
                    case 5:
                        $customClass = 'ganttGreen';
                        $description .= 'Status : <b>Complete</b>';
                        break;
                }
                if (!empty($ltvalue['description'])) {
                    $description = 'Description : <b>' . $description . '</b>';
                }
                $tdata['values'] = [array('from' => $ltvalue['startdate'], 'to' => $ltvalue['duedate'],
                'desc' => $description,
                'label' => $ltvalue['name'],
                'customClass' => $customClass, 'dataObj' => $ltvalue)];
                $tdata['desc'] = '<a href="' . base_url('admin/tasks/view/' . $ltvalue['id']) . '" onclick="init_task_modal(' . $ltvalue['id'] . ');return false;" class="pull-left">' . $ltvalue['name'] . '</a>';
                $return['list'][] = $tdata;
            }

            if ($push == 0) {
                $tdata = array();
                $tdata['name'] = '<a href="' . base_url('admin/leads/index/' . $lvalue['id']) . '" onclick="init_lead(' . $lvalue['id'] . ');return false;" class="pull-left">' . $lvalue['name'] . '</a>';
                $tdata['desc'] = ' ';
                $tdata['values'] = [];
                $return['list'][] = $tdata;
            }
        }
        $return['listjson'] = json_encode($return['list']);
//        pre($return);
        return $return;
    }

    public function get_lead_contact($leadid)
    {
        $this->db->where('lead_id',$leadid);
        return $this->db->get(db_prefix().'lead_contacts')->row();
    }

    function convert_to_deal($lead_id,$deal_id,$primary_contact_id=false){
        $this->db->where('id',$lead_id);

        $lead =$this->db->get(db_prefix().'leads')->row();

        //add lead item to deals
        $lead_items = $this->products_model->getleads_products($lead_id);
        if($lead_items){
            foreach($lead_items as $item){
                unset($item['id']);
                $item['projectid'] =$deal_id;
                unset($item['leadid']);
                unset($item['created_date']);
                $this->db->insert(db_prefix().'project_products',$item);
            }

            $this->db->where('id', $deal_id);
            $this->db->update(db_prefix() . 'projects', ['project_cost' => $lead->lead_cost]);
        }
        
        $notes = $this->misc_model->get_notes($lead_id, 'lead');
        if ($notes) {
            foreach ($notes as $note) {
                $this->db->insert(db_prefix() . 'project_notes', [
                    'project_id'         => $deal_id,
                    'content'         => $note['description'],
                    'staff_id'       => $note['staffid'],
                    'dateadded'      => $note['dateadded']
                ]);
            }
        }

        $files = $this->misc_model->get_files($lead_id, 'lead');
        if ($files) {
            foreach ($files as $file) {
                $this->db->insert(db_prefix() . 'project_files', [
                    'project_id'         => $deal_id,
                    'file_name'         => $file['file_name'],
                    'subject'       => $file['file_name'],
                    'filetype'      => $file['filetype'],
                    'dateadded'      => $file['dateadded'],
                    'staffid'      => $file['staffid']
                ]);
            }
        }

        $this->db->where('id', $deal_id);
        $this->db->update(db_prefix() . 'projects', ['lead_id' => $lead_id, 'project_currency' => $lead->lead_currency]);

        if($primary_contact_id) {
            $this->db->where('rel_id', $lead_id);
            $this->db->where('rel_type', 'lead');
            $this->db->update(db_prefix() . 'tasks', ['rel_type' => 'project', 'rel_id' => $deal_id, 'contacts_id' => $primary_contact_id]);
        } else {
            $this->db->where('rel_id', $lead_id);
            $this->db->where('rel_type', 'lead');
            $this->db->update(db_prefix() . 'tasks', ['rel_type' => 'project', 'rel_id' => $deal_id]);
        }

        

        $this->db->where('rel_id', $lead_id);
        $this->db->where('rel_type', 'lead');
        $this->db->update(db_prefix() . 'proposals', ['rel_type' => 'project', 'rel_id' => $deal_id]);

        $this->db->where('id', $lead_id);
        $this->db->update(db_prefix() . 'leads', ['project_id' => $deal_id, 'deleted_status' => 1]);
    }

    public function get_emails($lead_id)
    {

        $this->db->where('lead_id', $lead_id);

		$this->db->where('staff_id !=', 0);
        $this->db->order_by('udate', 'desc');
		//$this->db->group_by('uid'); 
        $emails = $this->db->get(db_prefix() . 'localmailstorage')->result_array();
        return $emails;
    }
    public function get_emails_count($lead_id)
    {

        $this->db->where('lead_id', $lead_id);

		$this->db->where('staff_id !=', 0);
        $this->db->select('count(id) AS count');
		//$this->db->group_by('uid'); 
        $emails = $this->db->get(db_prefix() . 'localmailstorage')->row();
        if($emails){
            return $emails->count;
        }
        return 0;
    }

    /**
     * @type : activity
     * @action: added,updated,deleted
     */
    public function log_activity($lead_id, $type, $action,$type_id) {
        $log = [
            'lead_id' => $lead_id,
            'type'=>$type,
            'action'=>$action,
            'type_id'=>$type_id,
            'staff_id' => get_staff_user_id()
        ];
        $this->db->insert(db_prefix() . 'lead_log', $log);
        return $this->db->insert_id();
    }

    public function get_log_activities($lead_id,$page=0)
    {
        $this->db->where('lead_id',$lead_id);
        $this->db->order_by('id', 'DESC');
        $limit =10;
        $this->db->limit($limit, $limit*$page);
        return $this->db->get(db_prefix().'lead_log')->result_object();
    }

    public function get_tabs_count($lead_id,$table)
    {
        $this->db->where('rel_type','lead');
        $this->db->where('rel_id',$lead_id);
        $this->db->select('COUNT(id) as count');
        $count =$this->db->get(db_prefix().$table)->row();
        return $count->count;
    }
    public function get_activities_count($lead_id)
    {
        return $this->get_tabs_count($lead_id,'tasks');
    }

    public function get_proposal_count($lead_id)
    {
        return $this->get_tabs_count($lead_id,'proposals');
    }
    
    public function get_files_count($lead_id)
    {
        return $this->get_tabs_count($lead_id,'files');
    }
    public function get_notes_count($lead_id)
    {
        return $this->get_tabs_count($lead_id,'notes');
    }

    public function get_calls_count($lead_id){
        $this->db->where('rel_type','lead');
        $this->db->where('rel_id',$lead_id);
        $this->db->group_start();
        $this->db->where('call_request_id !=',"");
        $this->db->or_where('call_code !=',0);
        $this->db->group_end();
        $this->db->select('COUNT(id) as count');
        $count =$this->db->get(db_prefix().'tasks')->row();
        return $count->count;
    }
    public function get_logs_count($lead_id)
    {
        $this->db->where('lead_id',$lead_id);
        $this->db->select('COUNT(id) as count');
        $count =$this->db->get(db_prefix().'lead_log')->row();
        return $count->count;
    }

    public function get_leads_by_contact_email($email, $staff_id=0)
    {

        $this->db->join(db_prefix() . 'lead_contacts', db_prefix().'lead_contacts.lead_id='.db_prefix().'leads.id', 'left');
        $this->db->join(db_prefix() . 'contacts', db_prefix().'contacts.id='.db_prefix().'lead_contacts.contacts_id', 'left');
        if($staff_id)
            $this->db->where(db_prefix().'leads.assigned',$staff_id);
        $this->db->where(db_prefix().'leads.deleted_status',0);
        $this->db->where(db_prefix().'leads.lost',0);
        $this->db->where(db_prefix().'leads.junk',0);
        $this->db->where(db_prefix().'leads.project_id',0);
        $this->db->where(db_prefix().'contacts.email',$email);
        $this->db->select(db_prefix().'leads.*');
        // $this->db->select(db_prefix().'leads.firstname');
        // $this->db->select(db_prefix().'leads.lastname');
        // pre($this->db->get_compiled_select());
        return $this->db->get(db_prefix().'leads')->result_object();
    }
}
