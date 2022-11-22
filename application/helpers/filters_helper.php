<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Format task priority based on passed priority id
 * @param  mixed $id
 * @return string
 */
 function get_filter_cond($filter_name,$filter_type,$filter = 'deal'){
	 $CI	= & get_instance();
	 $filter_cond = '';
	 switch($filter_name){
		case 'won_date':
			if($filter_type == 'is' && $filter == 'deal'){
				$filter_cond = " AND p.stage_on >= !!date1 AND p.stage_on <= !!date2 and p.stage_of = '1' ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND (p.stage_on IS NULL and p.stage_of = '1' )  ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND (p.stage_on IS NOT NULL and p.stage_of = '1')  ";
			}
			break;
		case 'lost_date':
			if($filter_type == 'is' && $filter == 'deal'){
				$filter_cond = " AND p.stage_on >= !!date1 AND p.stage_on <= !!date2 and p.stage_of = '2'  ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND (p.stage_on IS NULL and p.stage_of = '2')  ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND (p.stage_on IS NOT NULL and p.stage_of = '2')  ";
			}
			break;
		case 'project_created':
			if($filter_type == 'is' && $filter == 'deal'){
				$filter_cond = " AND p.project_created >= !!date1 AND p.project_created <= !!dat2 ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND (p.project_created IS NULL ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND (p.project_created IS NOT NULL ) ";
			}
			break;
		case 'project_modified':
			if($filter_type == 'is' && $filter == 'deal'){
				$filter_cond = " AND p.project_modified >= !!date1 AND p.project_modified <= !!date2 ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND (p.project_modified IS NULL) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND (p.project_modified IS NOT NULL ) ";
			}
			break;
		case 'project_start_date':
			if($filter_type == 'is' && $filter == 'deal'){
				$filter_cond = " AND p.start_date >= !!date1 AND p.start_date <= !!date2 ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND (p.start_date IS NULL ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND (p.start_date IS NOT NULL ) ";
			}
			break;
		case 'project_deadline':
			if($filter_type == 'is' && $filter == 'deal'){
				$filter_cond = " AND p.deadline>= !!date1 AND p.deadline <= !!date2 ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND (p.deadline IS NULL ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND (p.deadline IS NOT NULL ) ";
			}
			break;
		case 'name':
			if($filter_type == 'is' && $filter == 'deal'){
				$filter_cond = " AND (p.id like !!cond1) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.name ='') ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.name !='') ";
			}
			else if($filter_type == 'is_not' && $filter == 'deal'){
				$filter_cond = " AND ( p.id != !!cond1) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(!!in_cond)) ";
			}
		case 'project_cost':
			if($filter_type == 'is_more_than' && $filter == 'deal'){
				$filter_cond = " AND ( p.project_cost > !!cond1) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.project_cost ='') ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.project_cost !='') ";
			}
			else if($filter_type == 'is_less_than' && $filter == 'deal'){
				$filter_cond = " AND ( p.project_cost < !!cond1) ";
			}
			break;
		case 'teamleader_name':
			if($filter_type == 'is' && $filter == 'deal'){
				$filter_cond = " AND ( p.teamleader like !!cond1) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.teamleader = '') ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.teamleader != '') ";
			}
			else if($filter_type == 'is_not' && $filter == 'deal'){
				$filter_cond = " AND ( p.teamleader != !!cond1) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'deal'){
				$filter_cond = " AND ( p.teamleader in(!!in_cond) ) ";
			}
			break;
		case 'product_qty':
			if($filter_type == 'is_more_than' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT projectid FROM db_prefix()project_products group by projectid having sum(quantity) > !!cond1)) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT projectid FROM db_prefix()project_products group by projectid having sum(quantity) = '0') or p.id not in (SELECT projectid FROM db_prefix()project_products group by projectid having sum(quantity) > '0')) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT projectid FROM db_prefix()project_products group by projectid having  sum(quantity) >'0')) ";
			}
			else if($filter_type == 'is_less_than' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT projectid FROM db_prefix()project_products group by projectid having  sum(quantity) < !!cond1)) ";
			}
			break;
		case 'product_amt':
			if($filter_type == 'is_more_than' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT projectid FROM db_prefix()project_products group by projectid having sum(price) > !!cond1)) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT projectid FROM db_prefix()project_products group by projectid having sum(price) = '0' or sum(price) = '') or p.id not in (SELECT projectid FROM db_prefix()project_products group by projectid having sum(price) > '0') or p.id in (SELECT projectid FROM db_prefix()project_products group by projectid having projectid = '') ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT projectid FROM db_prefix()project_products group by projectid having sum(price) > '0') ) ";
			}
			else if($filter_type == 'is_less_than' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT projectid FROM db_prefix()project_products group by projectid having sum(price) < !!cond1) ) ";
			}
			break;
		case 'company':
			if($filter == 'deal'){
				if($filter_type == 'is' ){
					$filter_cond = " AND ( p.clientid in(SELECT userid FROM db_prefix()clients where userid = !!cond1)) ";
				}
				else if($filter_type == 'is_empty' ){
					$filter_cond = " AND ( p.clientid = '0' or p.clientid = ''  ) ";
				}
				else if($filter_type == 'is_not_empty' ){
					$filter_cond = " AND ( p.clientid != '0' or p.clientid != ''  ) ";
				}
				else if($filter_type == 'is_not' ){
					$filter_cond = " AND ( p.clientid in (SELECT userid FROM db_prefix()clients where userid != !!cond1) ) ";
				}
				else if($filter_type == 'is_any_of' ){
					$filter_cond = "  AND ( p.clientid in(SELECT userid FROM db_prefix()clients where userid in(!!in_cond) ) ) ";
				}
			}
			else if($filter == 'activity'){
				if($filter_type == 'is' ){
					$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where clientid in(SELECT userid FROM db_prefix()clients where userid = !!cond1) AND deleted_status = '0') AND db_prefix()tasks.rel_type = 'project' ) ";
				}
				else if($filter_type == 'is_empty' ){
					$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where clientid = 0 AND deleted_status = '0' )  AND db_prefix()tasks.rel_type = 'project' ) ";
				}
				else if($filter_type == 'is_not_empty' ){
					$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where clientid != 0  AND deleted_status = '0') AND db_prefix()tasks.rel_type = 'project' ) ";
				}
				else if($filter_type == 'is_not' ){
					$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where clientid in(SELECT userid FROM db_prefix()clients where userid != !!cond1) AND deleted_status = '0') AND db_prefix()tasks.rel_type = 'project' ) ";
				}
				else if($filter_type == 'is_any_of' ){
					$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where clientid in(SELECT userid FROM db_prefix()clients where userid in(!!in_cond)) AND deleted_status = '0' ) AND db_prefix()tasks.rel_type = 'project' ) ";
				}
			}
			break;
		case 'tags':
			if($filter_type == 'is' && $filter == 'deal'){
				$filter_cond = "  AND ( p.id in (SELECT rel_id FROM db_prefix()taggables where tag_id = !!cond1 and rel_type = 'project' ) ) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.id not in (SELECT rel_id FROM db_prefix()taggables where tag_id != '' and rel_type = 'project' ) ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in (SELECT rel_id FROM db_prefix()taggables where tag_id !='' and rel_type = 'project' ) ) ";
			}
			else if($filter_type == 'is_not' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in (SELECT rel_id FROM db_prefix()taggables where tag_id != !!cond1 and rel_type = 'project' ) ) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in (SELECT rel_id FROM db_prefix()taggables where tag_id in(!!in_cond) and rel_type = 'project'  ) ) ";
			}
			break;
		case 'members':
			if($filter_type == 'is' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in (SELECT project_id FROM db_prefix()project_members where staff_id = !!cond1 ) ) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.id not in (SELECT project_id FROM db_prefix()project_members) ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in (SELECT project_id FROM db_prefix()project_members ) ) ";
			}
			else if($filter_type == 'is_not' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in (SELECT project_id FROM db_prefix()project_members where staff_id != !!cond1 ) ) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in (SELECT project_id FROM db_prefix()project_members where staff_id in(!!in_cond)) ) ";
			}
			break;
		case 'loss_reason_name':
			if($filter_type == 'is' && $filter == 'deal'){
				$filter_cond = " AND ( p.loss_reason in (SELECT id FROM db_prefix()deallossreasons where id = !!cond1 ) ) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.loss_reason = 0 or p.loss_reason = '') ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND (  p.loss_reason != 0 or p.loss_reason != '' ) ";
			}
			else if($filter_type == 'is_not' && $filter == 'deal'){
				$filter_cond = " AND ( p.loss_reason in (SELECT id FROM db_prefix()deallossreasons where id != !!cond1 )) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'deal'){
				$filter_cond = " AND ( p.loss_reason in (SELECT id FROM db_prefix()deallossreasons where id in(!!in_cond))) ";
			}
			break;
		case 'project_currency':
			if($filter_type == 'is' && $filter == 'deal'){
				$filter_cond = "  AND ( p.project_currency in (SELECT name FROM db_prefix()currencies where id = !!cond1 ) ) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.project_currency = '' ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.project_currency != '' ) ";
			}
			else if($filter_type == 'is_not' && $filter == 'deal'){
				$filter_cond = " AND ( p.project_currency in (SELECT name FROM db_prefix()currencies where id != !!cond1)) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'deal'){
				$filter_cond = " AND ( p.project_currency in (SELECT name FROM db_prefix()currencies where id in(!!in_cond)) ) ";
			}
			break;
		case 'created_by':
			if($filter_type == 'is' && $filter == 'deal'){
				$filter_cond = " AND ( p.created_by  = !!cond1) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.created_by ='' or p.created_by = '0') ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.created_by != '0' and p.created_by != '') ";
			}
			else if($filter_type == 'is_not' && $filter == 'deal'){
				$filter_cond = " AND ( p.created_by != !!cond1) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'deal'){
				$filter_cond = " AND ( p.created_by in(!!in_cond) ) ";
			}
			break;
		case 'modified_by':
			if($filter_type == 'is' && $filter == 'deal'){
				$filter_cond = " AND ( p.modified_by  = !!cond1) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.modified_by ='' or p.modified_by = '0') ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.modified_by != '0' and p.modified_by != '') ";
			}
			else if($filter_type == 'is_not' && $filter == 'deal'){
				$filter_cond = " AND ( p.modified_by != !!cond1) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'deal'){
				$filter_cond = " AND ( p.modified_by in(!!in_cond) ) ";
			}
			break;
		case 'status':
			if($filter_type == 'is' && $filter == 'deal'){
				$filter_cond = " AND ( p.status in (SELECT id FROM db_prefix()projects_status where id = !!cond1) ) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.status not in (SELECT id FROM db_prefix()projects_status) ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.status in (SELECT id FROM db_prefix()projects_status) ) ";
			}
			else if($filter_type == 'is_not' && $filter == 'deal'){
				$filter_cond = " AND ( p.status in (SELECT id FROM db_prefix()projects_status where id != !!cond1) ) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'deal'){
				$filter_cond = " AND ( p.status in (SELECT id FROM db_prefix()projects_status where id in(!!in_cond)) ) ";
			}
			break;
		case 'pipeline_id':
			if($filter_type == 'is' && $filter == 'deal'){
				$filter_cond = " AND ( p.pipeline_id = !!cond1 ) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.pipeline_id = '' ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.pipeline_id != '' ) ";
			}
			else if($filter_type == 'is_not' && $filter == 'deal'){
				$filter_cond = " AND ( p.pipeline_id != !!cond1 ) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'deal'){
				$filter_cond = " AND ( p.pipeline_id in(!!in_cond) ) ";
			}
			break;
		case 'contact_email1':
			if($filter_type == 'is' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT p.project_id FROM db_prefix()project_contacts p,db_prefix()contacts c where p.contacts_id = c.id and c.email = !!cond1 and p.is_primary=1 and c.deleted_status ='0' and c.active = '1') ) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT p.project_id FROM db_prefix()project_contacts p,db_prefix()contacts c where p.contacts_id = c.id and c.email = '' and p.is_primary=1  and c.deleted_status ='0' and c.active = '1') ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT project_id FROM db_prefix()project_contacts pc,db_prefix()contacts c  where pc.contacts_id != '' and pc.is_primary=1 and c.id = pc.contacts_id and c.email!='' and c.deleted_status ='0' and c.active = '1') ) ";
			}
			else if($filter_type == 'is_not' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT project_id FROM db_prefix()project_contacts p,db_prefix()contacts c where p.contacts_id = c.id and c.email != !!cond1 and p.is_primary=1 and c.deleted_status ='0' and c.active = '1') ) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT p.project_id FROM db_prefix()project_contacts p,db_prefix()contacts c where c.email in(!!in_cond) and p.contacts_id = c.id  and p.is_primary=1 and c.deleted_status ='0' and c.active = '1') ) ";
			}
			break;
		case 'contact_name':
			if($filter_type == 'is' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT project_id FROM db_prefix()project_contacts pc, db_prefix()contacts c where pc.contacts_id = !!cond1 and pc.is_primary =1 and c.id = pc.contacts_id and c.deleted_status=0) ) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.id not in(SELECT project_id FROM db_prefix()project_contacts) ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT project_id FROM db_prefix()project_contacts pc,db_prefix()contacts c where pc.contacts_id!=''  and pc.is_primary = 1 and c.id = pc.contacts_id and c.deleted_status=0) ) ";
			}
			else if($filter_type == 'is_not' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT project_id FROM db_prefix()project_contacts  where contacts_id != !!cond1 and is_primary=1) ) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT project_id FROM db_prefix()project_contacts pc,db_prefix()contacts c where pc.contacts_id in(!!in_cond) and pc.is_primary=1 and c.id = pc.contacts_id and c.deleted_status=0) ) ";
			}
			break;
		case 'contact_phone1':
			if($filter_type == 'is' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT project_id FROM db_prefix()project_contacts pc,db_prefix()contacts c where pc.contacts_id in(!!in_cond) and pc.is_primary=1 and c.id = pc.contacts_id and c.deleted_status=0) ) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT p.project_id FROM db_prefix()project_contacts p,db_prefix()contacts c where p.contacts_id = c.id and c.phonenumber = '' and p.is_primary=1 and c.deleted_status ='0' and c.active = '1') ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT project_id FROM db_prefix()project_contacts pc,db_prefix()contacts c  where pc.contacts_id != '' and pc.is_primary=1 and c.id = pc.contacts_id and c.phonenumber!='' and c.deleted_status ='0' and c.active = '1')  ";
			}
			else if($filter_type == 'is_not' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT project_id FROM db_prefix()project_contacts p,db_prefix()contacts c where p.contacts_id = c.id and c.phonenumber != !!cond1 and p.is_primary=1 and c.deleted_status ='0' and c.active = '1') ) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'deal'){
				$filter_cond = " AND ( p.id in(SELECT p.project_id FROM db_prefix()project_contacts p,db_prefix()contacts c where c.phonenumber in(!!in_cond) and p.contacts_id = c.id  and p.is_primary=1 and c.deleted_status ='0' and c.active = '1') ) ";
			}
			break;
		case 'assignees':
			if($filter_type == 'is' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.id in (SELECT taskid FROM db_prefix()task_assigned where staffid = !!cond1 ) ) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.id in (SELECT taskid FROM db_prefix()task_assigned where staffid = 0 ) ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.id in (SELECT taskid FROM db_prefix()task_assigned where staffid != 0 ) ) ";
			}
			else if($filter_type == 'is_not' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.id in (SELECT taskid FROM db_prefix()task_assigned where staffid != !!cond1 ) ) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.id in (SELECT taskid FROM db_prefix()task_assigned where staffid in(!!in_cond) ) ) ";
			}
			break;
		case 'dateadded':
			if($filter_type == 'is' && $filter == 'activity'){
				$filter_cond = " AND db_prefix()tasks.dateadded >= !!date1 AND db_prefix()tasks.dateadded <= !!date2 ";
			}
			else if($filter_type == 'is_empty' && $filter == 'activity'){
				$filter_cond = " AND db_prefix()tasks.dateadded IS NULL ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'activity'){
				$filter_cond = " AND db_prefix()tasks.dateadded IS NOT NULL ";
			}
			break;
		case 'datefinished':
			if($filter_type == 'is' && $filter == 'activity'){
				$filter_cond = " AND db_prefix()tasks.datefinished >= !!date1 AND db_prefix()tasks.datefinished <= !!date2 ";
			}
			else if($filter_type == 'is_empty' && $filter == 'activity'){
				$filter_cond = " AND db_prefix()tasks.datefinished IS NULL ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'activity'){
				$filter_cond = " AND db_prefix()tasks.datefinished IS NOT NULL ";
			}
			break;
		case 'datemodified':
			if($filter_type == 'is' && $filter == 'activity'){
				$filter_cond = " AND db_prefix()tasks.datemodified >= !!date1 AND db_prefix()tasks.datemodified <= !!date2 ";
			}
			else if($filter_type == 'is_empty' && $filter == 'activity'){
				$filter_cond = " AND db_prefix()tasks.datemodified IS NULL ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'activity'){
				$filter_cond = " AND db_prefix()tasks.datemodified IS NOT NULL ";
			}
			break;
		case 'priority':
			if($filter_type == 'is' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.priority = !!cond1 AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.priority = 0  AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.priority != 0 AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			else if($filter_type == 'is_not' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.priority != !!cond1 AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.priority in (!!in_cond) AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			break;
		case 'project_contacts':
			if($filter_type == 'is' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT project_id FROM db_prefix()project_contacts pc, db_prefix()contacts c where pc.contacts_id = !!cond1 and pc.is_primary =1 and c.id = pc.contacts_id and c.deleted_status=0) AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT pc.project_id FROM db_prefix()project_contacts pc, db_prefix()contacts c  where pc.contacts_id = 0 and pc.is_primary=1 and c.id = pc.contacts_id and c.deleted_status=0)  AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT pc.project_id FROM db_prefix()project_contacts pc, db_prefix()contacts c  where pc.contacts_id != 0 and pc.is_primary=1 and c.id = pc.contacts_id and c.deleted_status=0) AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			else if($filter_type == 'is_not' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT pc.project_id FROM db_prefix()project_contacts pc, db_prefix()contacts c  where pc.contacts_id != !!cond1 and pc.is_primary=1 and c.id = pc.contacts_id and c.deleted_status=0) AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT project_id FROM db_prefix()project_contacts pc,db_prefix()contacts c where pc.contacts_id in(!!in_cond) and pc.is_primary=1 and c.id = pc.contacts_id and c.deleted_status=0) AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			break;
		case 'project_name':
			if($filter_type == 'is' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where id = !!cond1 AND deleted_status = '0') AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'activity'){
				$filter_cond = " AND (( db_prefix()tasks.rel_id = '') OR ( db_prefix()tasks.rel_id not in (SELECT id FROM db_prefix()projects ) AND db_prefix()tasks.rel_type = 'project' ) OR ( db_prefix()tasks.rel_id not in (SELECT id FROM db_prefix()leads ) AND db_prefix()tasks.rel_type = 'lead' ) OR ( db_prefix()tasks.rel_id not in (SELECT id FROM db_prefix()proposals ) AND db_prefix()tasks.rel_type = 'proposal' ) OR ( db_prefix()tasks.rel_id not in (SELECT userid FROM db_prefix()clients ) AND( db_prefix()tasks.rel_type = 'customer' OR db_prefix()tasks.rel_type = 'contact') ) ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where id != 0  AND deleted_status = '0') AND db_prefix()tasks.rel_type = 'project' )  ";
			}
			else if($filter_type == 'is_not' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where id != !!cond1  AND deleted_status = '0')  AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where id in(!!in_cond) AND deleted_status = '0' ) AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			break;
		case 'project_pipeline':
			if($filter_type == 'is' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where pipeline_id = !!cond1 AND deleted_status = '0') AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where pipeline_id = 0 AND deleted_status = '0' )  AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where pipeline_id != 0  AND deleted_status = '0') AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			else if($filter_type == 'is_not' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where pipeline_id != !!cond1  AND deleted_status = '0')  AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where pipeline_id in(!!in_cond) AND deleted_status = '0' ) AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			break;
		case 'project_status':
			if($filter_type == 'is' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where status = !!cond1 ) AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where status = 0 )  AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where status != 0 ) AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			else if($filter_type == 'is_not' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where status != !!cond1 )  AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where status in(!!in_cond) ) AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			break;
		case 'rel_type':
			if($filter_type == 'is' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_type = !!cond1) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_type = '') ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_type != '') ";
			}
			else if($filter_type == 'is_not' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_type != !!cond1) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_type in (!!in_cond) ) ";
			}
			break;
		case 'status':
			if($filter_type == 'is' && $filter == 'activity'){
				$filter_cond = " AND db_prefix()tasks.status = !!cond1 ";
			}
			else if($filter_type == 'is_empty' && $filter == 'activity'){
				$filter_cond = " AND (db_prefix()tasks.status = 0 or  db_prefix()tasks.status = '') ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'activity'){
				$filter_cond = " AND (db_prefix()tasks.status != 0 or  db_prefix()tasks.status != '') ";
			}
			else if($filter_type == 'is_not' && $filter == 'activity'){
				$filter_cond = " AND db_prefix()tasks.status != !!cond1 ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'activity'){
				$filter_cond = " AND db_prefix()tasks.status in(!!in_cond) ";
			}
			break;
		case 'tags':
			if($filter_type == 'is' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.id in (SELECT rel_id FROM db_prefix()taggables where tag_id in(!!cond1) and rel_type = 'task'  ) ) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.id not in (SELECT rel_id FROM db_prefix()taggables where tag_id != '' and rel_type = 'task' ) ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.id in (SELECT rel_id FROM db_prefix()taggables where tag_id != '' and rel_type = 'task' ) ) ";
			}
			else if($filter_type == 'is_not' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.id in (SELECT rel_id FROM db_prefix()taggables where tag_id != !!cond1 and rel_type = 'task' ) ) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.id in (SELECT rel_id FROM db_prefix()taggables where tag_id in(!!in_cond) and rel_type = 'task'  ) ) ";
			}
			break;
		case 'tasktype':
			if($filter_type == 'is' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.tasktype = !!cond1) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.tasktype = '') ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.tasktype != '') ";
			}
			else if($filter_type == 'is_not' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.tasktype != !!cond1) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.tasktype in (!!in_cond) ) ";
			}
			break;
		case 'teamleader':
			if($filter_type == 'is' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where teamleader = !!cond1 AND deleted_status = '0') AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			else if($filter_type == 'is_empty' && $filter == 'activity'){
				$filter_cond = " AND (( db_prefix()tasks.rel_id = '') OR ( db_prefix()tasks.rel_id not in (SELECT id FROM db_prefix()projects ) AND db_prefix()tasks.rel_type = 'project' ) OR db_prefix()tasks.rel_type != 'project' ) ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where teamleader != 0  AND deleted_status = '0') AND db_prefix()tasks.rel_type = 'project' )  ";
			}
			else if($filter_type == 'is_not' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where teamleader != !!cond1 AND deleted_status = '0') AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			else if($filter_type == 'is_any_of' && $filter == 'activity'){
				$filter_cond = " AND ( db_prefix()tasks.rel_id in (SELECT id FROM db_prefix()projects where teamleader  in(!!in_cond) AND deleted_status = '0' ) AND db_prefix()tasks.rel_type = 'project' ) ";
			}
			break;
		case 'startdate':
			if($filter_type == 'is' && $filter == 'activity'){
				$filter_cond = " AND db_prefix()tasks.startdate >= !!date1 AND db_prefix()tasks.startdate <= !!date2 ";
			}
			else if($filter_type == 'is_empty' && $filter == 'activity'){
				$filter_cond = " AND db_prefix()tasks.startdate IS NULL ";
			}
			else if($filter_type == 'is_not_empty' && $filter == 'activity'){
				$filter_cond = " AND db_prefix()tasks.startdate IS NOT NULL ";
			}
			break;
	 }
	 return $filter_cond;
 }