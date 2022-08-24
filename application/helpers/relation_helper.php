<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Function used to get related data based on rel_id and rel_type
 * Eq in the tasks section there is field where this task is related eq invoice with number INV-0005
 * @param  string $type
 * @param  string $rel_id
 * @return mixed
 */
function get_relation_data($type, $rel_id = '')
{
    $CI = & get_instance();
    $q  = '';
    if ($CI->input->post('q')) {
        $q = $CI->input->post('q');
        $q = trim($q);
    }
    $data = [];
    if ($type == 'customer' || $type == 'customers') {
        $where_clients = '';

        if ($q) {
            if(is_admin(get_staff_user_id())) {
                $where_clients .= '(company LIKE "%' . $q . '%" OR CONCAT(firstname, " ", lastname) LIKE "%' . $q . '%" OR email LIKE "%' . $q . '%") AND '.db_prefix().'clients.active = 1 AND '.db_prefix().'clients.deleted_status = 0';
            } else {
                $where_clients .= '((tblclients.addedfrom = "'.get_staff_user_id().'") OR tblclients.userid IN (SELECT userid FROM tblcontacts WHERE email=(SELECT email FROM tblstaff WHERE staffid="'.get_staff_user_id().'")) OR (tblclients.userid IN (select clientid from tblprojects where id IN (select project_id from tblproject_members where staff_id="'.get_staff_user_id().'") OR tblclients.userid IN ( select clientid from tblprojects where teamleader = "'.get_staff_user_id().'")) )) AND (company LIKE "%' . $q . '%" OR CONCAT(firstname, " ", lastname) LIKE "%' . $q . '%" OR email LIKE "%' . $q . '%") AND '.db_prefix().'clients.active = 1 AND '.db_prefix().'clients.deleted_status = 0';
            }
        }else{
			if(is_admin(get_staff_user_id())) {
                $where_clients .= db_prefix().'clients.active = 1 AND '.db_prefix().'clients.deleted_status = 0';
            } else {
                $where_clients .= '((tblclients.addedfrom = "'.get_staff_user_id().'") OR tblclients.userid IN (SELECT userid FROM tblcontacts WHERE email=(SELECT email FROM tblstaff WHERE staffid="'.get_staff_user_id().'")) OR (tblclients.userid IN (select clientid from tblprojects where id IN (select project_id from tblproject_members where staff_id="'.get_staff_user_id().'") OR tblclients.userid IN ( select clientid from tblprojects where teamleader = "'.get_staff_user_id().'")) )) AND  '.db_prefix().'clients.active = 1 AND '.db_prefix().'clients.deleted_status = 0';
            }
		}

        $data = $CI->clients_model->get($rel_id, $where_clients);
    } elseif ($type == 'contact' || $type == 'contacts'  ) {
        if ($rel_id != '') {
            $data = $CI->clients_model->get_contact($rel_id);
        } else {
            $where_contacts = db_prefix().'contacts.active=1';
            if ($CI->input->post('tickets_contacts')) {
                if (!has_permission('customers', '', 'view') && get_option('staff_members_open_tickets_to_all_contacts') == 0) {
                    $where_contacts .= ' AND '.db_prefix().'contacts.userid IN (SELECT customer_id FROM '.db_prefix().'customer_admins WHERE staff_id=' . get_staff_user_id() . ')';
                }
            }
            if ($CI->input->post('contact_userid')) {
                $where_contacts .= ' AND '.db_prefix().'contacts.userid=' . $CI->input->post('contact_userid');
            }
            $search = $CI->misc_model->_search_contacts($q, 0, $where_contacts);
            $data   = $search['result'];
        }
    } elseif ( $type == 'staff_phone' || $type == 'staff_email' ) {
        if ($rel_id != '') {
            $data = $CI->clients_model->get_contact($rel_id);
        } else {
            $where_contacts = db_prefix()."contacts.active=1 and ".db_prefix()."contacts.id IN(select  contacts_id from ".db_prefix()."project_contacts where is_primary=1)";
            if ($CI->input->post('tickets_contacts')) {
                if (!has_permission('customers', '', 'view') && get_option('staff_members_open_tickets_to_all_contacts') == 0) {
                    $where_contacts .= ' AND '.db_prefix().'contacts.userid IN (SELECT customer_id FROM '.db_prefix().'customer_admins WHERE staff_id=' . get_staff_user_id() . ')';
                }
            }
            if ($CI->input->post('contact_userid')) {
                $where_contacts .= ' AND '.db_prefix().'contacts.userid=' . $CI->input->post('contact_userid');
            }
            $search = $CI->misc_model->_search_contacts($q, 0, $where_contacts);
            $data   = $search['result'];
        }
    } elseif ($type == 'invoice') {
        if ($rel_id != '') {
            $CI->load->model('invoices_model');
            $data = $CI->invoices_model->get($rel_id);
        } else {
            $search = $CI->misc_model->_search_invoices($q);
            $data   = $search['result'];
        }
    } elseif ($type == 'credit_note') {
        if ($rel_id != '') {
            $CI->load->model('credit_notes_model');
            $data = $CI->credit_notes_model->get($rel_id);
        } else {
            $search = $CI->misc_model->_search_credit_notes($q);
            $data   = $search['result'];
        }
    } elseif ($type == 'estimate') {
        if ($rel_id != '') {
            $CI->load->model('estimates_model');
            $data = $CI->estimates_model->get($rel_id);
        } else {
            $search = $CI->misc_model->_search_estimates($q);
            $data   = $search['result'];
        }
    } elseif ($type == 'contract' || $type == 'contracts') {
        $CI->load->model('contracts_model');

        if ($rel_id != '') {
            $CI->load->model('contracts_model');
            $data = $CI->contracts_model->get($rel_id);
        } else {
            $search = $CI->misc_model->_search_contracts($q);
            $data   = $search['result'];
        }
    } elseif ($type == 'ticket') {
        if ($rel_id != '') {
            $CI->load->model('tickets_model');
            $data = $CI->tickets_model->get($rel_id);
        } else {
            $search = $CI->misc_model->_search_tickets($q);
            $data   = $search['result'];
        }
    } elseif ($type == 'expense' || $type == 'expenses') {
        if ($rel_id != '') {
            $CI->load->model('expenses_model');
            $data = $CI->expenses_model->get($rel_id);
        } else {
            $search = $CI->misc_model->_search_expenses($q);
            $data   = $search['result'];
        }
    } elseif ($type == 'lead' || $type == 'leads') {
        if ($rel_id != '') {
            $CI->load->model('leads_model');
            $data = $CI->leads_model->get($rel_id);
        } else {
            $search = $CI->misc_model->_search_leads($q, 0, [
                'junk' => 0,
                ]);
            $data = $search['result'];
        }
    } elseif ($type == 'proposal') {
        if ($rel_id != '') {
            $CI->load->model('proposals_model');
            $data = $CI->proposals_model->get($rel_id);
        } else {
            $search = $CI->misc_model->_search_proposals($q);
            $data   = $search['result'];
        }
    } 
	elseif ($type == 'project') {
		$fields = get_option('deal_fields');
		$fields1 = get_option('deal_mandatory');
		$need_fields = $mandatory_fields = array("name");
		if(!empty($fields) && $fields != 'null'){
			$need_fields = json_decode($fields);
		}
		if(!empty($fields1) && $fields1 != 'null'){
			$mandatory_fields = json_decode($fields1);
		}
        if ($rel_id != '') {
            $CI->load->model('projects_model');
            $data = $CI->projects_model->get($rel_id);
        } else {
            $where_projects = ' tblprojects.deleted_status = 0 ';
			if(in_array('clientid',$need_fields) && in_array('clientid',$mandatory_fields)){
				if ($CI->input->post('customer_id')) {
				    $where_projects .= 'AND clientid=' . $CI->input->post('customer_id');
				}
			}
			else if(in_array('clientid',$need_fields)){
				if ($CI->input->post('customer_id')) {
				    $where_projects .= 'AND clientid=' . $CI->input->post('customer_id')." or (  clientid  = '')";
				}
			}
            $search = $CI->misc_model->_search_projects($q, 0, $where_projects);
            $data   = $search['result'];
        }
    }
	elseif ($type == 'staff' ) {
        if ($rel_id != '') {
            $CI->load->model('staff_model');
            $data = $CI->staff_model->get($rel_id);
        } else {
            $search = $CI->misc_model->_search_staff($q);
            $data   = $search['result'];
        }
    }elseif ($type == 'tags') {
            $search = $CI->misc_model->_search_tags($q);
            $data   = $search['result'];
	}elseif ($type == 'manager') {
            $search = $CI->misc_model->_search_manager($q);
            $data   = $search['result'];
	}elseif ($type == 'tasks' || $type == 'task') {
        // Tasks only have relation with custom fields when searching on top
        if ($rel_id != '') {
            $data = $CI->tasks_model->get($rel_id);
        }
    } elseif ($type == 'product_category') {
        $where_clients = '';
        if ($q) {
                $where_clients .= ' cat_name LIKE "%' . $q . '%" ';
        }

        $data = $CI->products_model->getCategories($rel_id, $where_clients);
    }

    return $data;
}
/**
 * Ger relation values eq invoice number or project name etc based on passed relation parsed results
 * from function get_relation_data
 * $relation can be object or array
 * @param  mixed $relation
 * @param  string $type
 * @return mixed
 */
function get_relation_values($relation, $type)
{
    if ($relation == '') {
        return [
            'name'      => '',
            'id'        => '',
            'link'      => '',
            'addedfrom' => 0,
            'subtext'   => '',
            ];
    }

    $addedfrom = 0;
    $name      = '';
    $id        = '';
    $link      = '';
    $subtext   = '';
    if ($type == 'customer' || $type == 'customers') {
        if (is_array($relation)) {
            $id   = $relation['userid'];
            $name = $relation['company'];
        } else {
            $id   = $relation->userid;
            $name = $relation->company;
        }
        $link = admin_url('clients/client/' . $id);
    } elseif ($type == 'contact' || $type == 'contacts') {
        if (is_array($relation)) {
            $userid = isset($relation['userid']) ? $relation['userid'] : $relation['relid'];
            $id     = $relation['id'];
            $name   = $relation['firstname'] . ' ' . $relation['lastname'];
        } else {
            $userid = $relation->userid;
            $id     = $relation->id;
            $name   = $relation->firstname . ' ' . $relation->lastname;
        }
        $subtext = get_company_name($userid);
        $link    = admin_url('clients/client/' . $userid . '?contactid=' . $id);
    } elseif ($type == 'invoice') {
        if (is_array($relation)) {
            $id        = $relation['id'];
            $addedfrom = $relation['addedfrom'];
        } else {
            $id        = $relation->id;
            $addedfrom = $relation->addedfrom;
        }
        $name = format_invoice_number($id);
        $link = admin_url('invoices/list_invoices/' . $id);
    } elseif ($type == 'credit_note') {
        if (is_array($relation)) {
            $id        = $relation['id'];
            $addedfrom = $relation['addedfrom'];
        } else {
            $id        = $relation->id;
            $addedfrom = $relation->addedfrom;
        }
        $name = format_credit_note_number($id);
        $link = admin_url('credit_notes/list_credit_notes/' . $id);
    } elseif ($type == 'estimate') {
        if (is_array($relation)) {
            $id        = $relation['estimateid'];
            $addedfrom = $relation['addedfrom'];
        } else {
            $id        = $relation->id;
            $addedfrom = $relation->addedfrom;
        }
        $name = format_estimate_number($id);
        $link = admin_url('estimates/list_estimates/' . $id);
    } elseif ($type == 'contract' || $type == 'contracts') {
        if (is_array($relation)) {
            $id        = $relation['id'];
            $name      = $relation['subject'];
            $addedfrom = $relation['addedfrom'];
        } else {
            $id        = $relation->id;
            $name      = $relation->subject;
            $addedfrom = $relation->addedfrom;
        }
        $link = admin_url('contracts/contract/' . $id);
    } elseif ($type == 'ticket') {
        if (is_array($relation)) {
            $id   = $relation['ticketid'];
            $name = '#' . $relation['ticketid'];
            $name .= ' - ' . $relation['subject'];
        } else {
            $id   = $relation->ticketid;
            $name = '#' . $relation->ticketid;
            $name .= ' - ' . $relation->subject;
        }
        $link = admin_url('tickets/ticket/' . $id);
    } elseif ($type == 'expense' || $type == 'expenses') {
        if (is_array($relation)) {
            $id        = $relation['expenseid'];
            $name      = $relation['category_name'];
            $addedfrom = $relation['addedfrom'];

            if (!empty($relation['expense_name'])) {
                $name .= ' (' . $relation['expense_name'] . ')';
            }
        } else {
            $id        = $relation->expenseid;
            $name      = $relation->category_name;
            $addedfrom = $relation->addedfrom;
            if (!empty($relation->expense_name)) {
                $name .= ' (' . $relation->expense_name . ')';
            }
        }
        $link = admin_url('expenses/list_expenses/' . $id);
    } elseif ($type == 'lead' || $type == 'leads') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = $relation['name'];
            if ($relation['email'] != '') {
                $name .= ' - ' . $relation['email'];
            }
        } else {
            $id   = $relation->id;
            $name = $relation->name;
            if ($relation->email != '') {
                $name .= ' - ' . $relation->email;
            }
        }
        $link = admin_url('leads/index/' . $id);
    } elseif ($type == 'proposal') {
        if (is_array($relation)) {
            $id        = $relation['id'];
            $addedfrom = $relation['addedfrom'];
            if (!empty($relation['subject'])) {
                $name .= ' - ' . $relation['subject'];
            }
        } else {
            $id        = $relation->id;
            $addedfrom = $relation->addedfrom;
            if (!empty($relation->subject)) {
                $name .= ' - ' . $relation->subject;
            }
        }
        $name = format_proposal_number($id);
        $link = admin_url('proposals/list_proposals/' . $id);
    } elseif ($type == 'tasks' || $type == 'task') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = $relation['name'];
        } else {
            $id   = $relation->id;
            $name = $relation->name;
        }
        $link = admin_url('tasks/view/' . $id);
    } elseif ($type == 'staff') {
        if (is_array($relation)) {
            $id   = $relation['staffid'];
            $name = $relation['firstname'] . ' ' . $relation['lastname'];
        } else {
            $id   = $relation->staffid;
            $name = $relation->firstname . ' ' . $relation->lastname;
        }
        $link = admin_url('profile/' . $id);
    }elseif ($type == 'staff_email') {
       if (is_array($relation)) {
            $id   = $relation['email'];
			$userid = isset($relation['userid']) ? $relation['userid'] : $relation['relid'];
            $name = $relation['email'];
        } else {
            $id   = $relation->email;
			$userid = $relation->userid;
            $name = $relation->email;
        }
		//$subtext = get_company_name($userid);
        $link    = admin_url('clients/client/' . $userid . '?contactid=' . $id);
    }elseif ($type == 'staff_phone') {
        if (is_array($relation)) {
            $id   = $relation['phonenumber'];
			$userid = isset($relation['userid']) ? $relation['userid'] : $relation['relid'];
            $name = $relation['phonenumber'];
        } else {
            $id   = $relation->phonenumber;
			$userid = $relation->userid;
            $name = $relation->phonenumber;
        }
		//$subtext = get_company_name($userid);
        $link    = admin_url('clients/client/' . $userid . '?contactid=' . $id);
    }elseif ($type == 'manager') {
        if (is_array($relation)) {
            $id   = $relation['staffid'];
            $name = $relation['firstname'] . ' ' . $relation['lastname'];
        } else {
            $id   = $relation->staffid;
            $name = $relation->firstname . ' ' . $relation->lastname;
        }
        $link = admin_url('profile/' . $id);
    }elseif ($type == 'tags') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = $relation['name'];
        } else {
            $id   = $relation->id;
            $name = $relation->name;
        }
        $link = '#';
    } elseif ($type == 'project') {
        if (is_array($relation)) {
            $id       = $relation['id'];
            $name     = $relation['name'];
            $clientId = $relation['clientid'];
        } else {
            $id       = $relation->id;
            $name     = $relation->name;
            $clientId = $relation->clientid;
        }

        //$name = 'ID ' . $id . ' - ' . $name . ' - ' . get_company_name($clientId);
        $name = $name . ' - ' . get_company_name($clientId);

        $link = admin_url('projects/view/' . $id);
    } elseif ($type == 'product_category' || $type == 'product_category') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = $relation['cat_name'];
        } else {
            $id   = $relation->id;
            $name = $relation->cat_name;
        }
        $link = '#';
    }

    return hooks()->apply_filters('relation_values', [
        'id'       => $id,
        'name'      => $name,
        'link'      => $link,
        'addedfrom' => $addedfrom,
        'subtext'   => $subtext,
        'type'      => $type,
        ]);
}

/**
 * Function used to render <option> for relation
 * This function will do all the necessary checking and return the options
 * @param  mixed $data
 * @param  string $type   rel_type
 * @param  string $rel_id rel_id
 * @return string
 */
function init_relation_options($data, $type, $rel_id = '')
{
    $_data = [];
    $has_permission_projects_view  = has_permission('projects', '', 'view');
    $has_permission_customers_view = has_permission('customers', '', 'view');
    $has_permission_contracts_view = has_permission('contracts', '', 'view');
    $has_permission_invoices_view  = has_permission('invoices', '', 'view');
    $has_permission_estimates_view = has_permission('estimates', '', 'view');
    $has_permission_expenses_view  = has_permission('expenses', '', 'view');
    $has_permission_proposals_view = has_permission('proposals', '', 'view');
    $is_admin                      = is_admin();
    $CI                            = & get_instance();
    $CI->load->model('projects_model');
    foreach ($data as $relation) {
        $relation_values = get_relation_values($relation, $type);
        if ($type == 'project') {
            if (!$has_permission_projects_view) {
                if (!$CI->projects_model->is_member($relation_values['id']) && $rel_id != $relation_values['id']) {
                    continue;
                }
            }
        } elseif ($type == 'lead') {
            if (!has_permission('leads', '', 'view')) {
                if ($relation['assigned'] != get_staff_user_id() && $relation['addedfrom'] != get_staff_user_id() && $relation['is_public'] != 1 && $rel_id != $relation_values['id']) {
                    continue;
                }
            }
        } elseif ($type == 'customer') {
            if (!$has_permission_customers_view && !have_assigned_customers() && $rel_id != $relation_values['id']) {
                continue;
            } elseif (have_assigned_customers() && $rel_id != $relation_values['id'] && !$has_permission_customers_view) {
                if (!is_customer_admin($relation_values['id'])) {
                    continue;
                }
            }
        } elseif ($type == 'contract') {
            if (!$has_permission_contracts_view && $rel_id != $relation_values['id'] && $relation_values['addedfrom'] != get_staff_user_id()) {
                continue;
            }
        } elseif ($type == 'invoice') {
            if (!$has_permission_invoices_view && $rel_id != $relation_values['id'] && $relation_values['addedfrom'] != get_staff_user_id()) {
                continue;
            }
        } elseif ($type == 'estimate') {
            if (!$has_permission_estimates_view && $rel_id != $relation_values['id'] && $relation_values['addedfrom'] != get_staff_user_id()) {
                continue;
            }
        } elseif ($type == 'expense') {
            if (!$has_permission_expenses_view && $rel_id != $relation_values['id'] && $relation_values['addedfrom'] != get_staff_user_id()) {
                continue;
            }
        } elseif ($type == 'proposal') {
            if (!$has_permission_proposals_view && $rel_id != $relation_values['id'] && $relation_values['addedfrom'] != get_staff_user_id()) {
                continue;
            }
        } 

        $_data[] = $relation_values;
        //  echo '<option value="' . $relation_values['id'] . '"' . $selected . '>' . $relation_values['name'] . '</option>';
    }
    return $_data;
}
