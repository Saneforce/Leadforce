<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'libraries/import/App_import.php');

class Import_deals extends App_import
{
    protected $notImportableFields = [];

    private $countryFields = ['country', 'billing_country', 'shipping_country'];

    protected $requiredFields = ['firstname', 'lastname', 'email'];

    public function __construct()
    {
        //$this->notImportableFields = hooks()->apply_filters('not_importable_clients_fields', ['userid', 'id', 'is_primary', 'password', 'datecreated', 'last_ip', 'last_login', 'last_password_change', 'active', 'new_pass_key', 'new_pass_key_requested', 'leadid', 'default_currency', 'profile_image', 'default_language', 'direction', 'show_primary_contact', 'invoice_emails', 'estimate_emails', 'project_emails', 'task_emails', 'contract_emails', 'credit_note_emails', 'ticket_emails', 'addedfrom', 'registration_confirmed', 'last_active_time', 'email_verified_at', 'email_verification_key', 'email_verification_sent_at']);
        //$this->notImportableFields = hooks()->apply_filters('not_importable_clients_fields', ['contact_fullname', 'contact_position', 'contact_email', 'contact_phonenumber', 'deal_name', 'deal_description', 'deal_pipeline', 'deal_pipeline_stage', 'deal_owner', 'deal_start_date', 'deal_deadline', 'deal_project_cost', 'deal_project_currency', 'client_company', 'client_phonenumber', 'client_city', 'client_state', 'client_zip', 'client_country', 'client_address', 'client_website', 'activity_tasktype', 'activity_name', 'activity_description', 'activity_priority', 'activity_startdate', 'activity_assignedto']);
        $this->notImportableFields = hooks()->apply_filters('not_importable_clients_fields', ['person_id','person_userid','person_userids','person_is_primary','person_lastname','person_alternative_emails','person_alternative_phonenumber','person_datecreated','person_password','person_new_pass_key','person_new_pass_key_requested','person_email_verified_at','person_email_verification_key','person_email_verification_sent_at','person_last_ip','person_last_login','person_last_password_change','person_active','person_profile_image','person_direction','person_invoice_emails','person_estimate_emails','person_credit_note_emails','person_contract_emails','person_task_emails','person_project_emails','person_ticket_emails','person_deleted_status','person_addedfrom','deal_id','deal_clientid','deal_billing_type','deal_project_created','deal_date_finished','deal_progress','deal_progress_from_tasks','deal_project_rate_per_hour','deal_estimated_hours','deal_addedfrom','deal_stage_on','deal_loss_reason','deal_loss_remark','deal_deleted_status','organization_userid','organization_vat','organization_datecreated','organization_active','organization_leadid','organization_billing_street','organization_billing_city','organization_billing_state','organization_billing_zip','organization_billing_country','organization_shipping_street','organization_shipping_city','organization_shipping_state','organization_shipping_zip','organization_shipping_country','organization_longitude','organization_latitude','organization_default_language','organization_default_currency','organization_show_primary_contact','organization_stripe_id','organization_registration_confirmed','organization_addedfrom','organization_deleted_status','activity_id','activity_dateadded','activity_duedate','activity_datefinished','activity_addedfrom','activity_is_added_from_contact','activity_status','activity_recurring_type','activity_repeat_every','activity_recurring','activity_is_recurring_from','activity_cycles','activity_total_cycles','activity_custom_recurring','activity_last_recurring_date','activity_rel_id','activity_rel_type','activity_is_public','activity_billable','activity_billed','activity_invoice_id','activity_hourly_rate','activity_milestone','activity_kanban_order','activity_milestone_order','activity_visible_to_client','activity_deadline_notified','activity_source_from','activity_contacts_id','activity_imported_id','deal_imported_id','deal_reassigned_from','deal_test']);
        if (get_option('company_is_required') == 1) {
            $this->requiredFields[] = 'company';
        }

        $this->addImportGuidelinesInfo('Duplicate email rows won\'t be imported.', true);

        $this->addImportGuidelinesInfo('Make sure you configure the default contact permission in <a href="' . admin_url('settings?group=clients') . '" target="_blank">Setup->Settings->Import Data</a> to get the best results like auto assigning contact permissions and email notification settings based on the permission.');

        parent::__construct();
    }

    public function perform()
    {
	
        //$this->initialize();
		
        $databaseFields      = $this->getImportableDatabaseFields();
        $custFields = array();
        foreach ($this->getCustomFields() as $field) {
            $custFields[] = $field['slug'];
        }
        $databaseFields = array_merge($databaseFields,$custFields);
        $totalDatabaseFields = count($databaseFields);
        $skippedData = [];
        $skipCnt = 0;
        $importCnt = 0;
		$row = 1;
		if (($handle = fopen($_FILES['file']['tmp_name'], "r")) !== FALSE) {
			
		  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$num = count($data);
			$row++;
			if($row > 2) {
				$insert    = [];
				for ($c=0; $c < $num; $c++) {
					$insert[$databaseFields[$c]] = $data[$c];
				}
                $insert = $this->trimInsertValues($insert);
				if (count($insert) > 0) {
					if($_POST['deals'] == 'deals') {
						$skipRecord = $this->checkDataToSkip($insert); 
						if($skipRecord) {
							array_unshift($data , $skipRecord);
							$skippedData[] = $data;
							$skipCnt++;
						} else {
							$importCnt++;
							$fields = get_option('deal_fields');
							$needed_fields = array();
							if(!empty($fields) && $fields != 'null'){
								$needed_fields = json_decode($fields);
							}	
							//$needed_fields = json_decode($fields);
							//Organization
							$org = array();
							if(!empty($needed_fields) && in_array("clientid", $needed_fields)) {
								if($insert['organization_name']) {
									$org['company'] = $insert['organization_name'];
								}
								if($insert['organization_phonenumber']) {
									$org['phonenumber'] = $insert['organization_phonenumber'];
								}
								if($insert['organization_country']) {
									$this->ci->db->where('short_name', $insert['organization_country']);
									$country = $this->ci->db->get(db_prefix() . 'countries')->row();
									if(!empty($country->country_id)){
										$org['country'] = $country->country_id;
									}
								}
								if($insert['organization_city']) {
									$org['city'] = $insert['organization_city'];
								}
								if($insert['organization_state']) {
									$org['state'] = $insert['organization_state'];
								}
								if($insert['organization_zip']) {
									$org['zip'] = $insert['organization_zip'];
								}
								if($insert['organization_address']) {
									$org['address'] = $insert['organization_address'];
								}
								if($insert['organization_website']) {
									$org['website'] = $insert['organization_website'];
								}
								$orgData = $this->checkOrg($org);
								if($orgData) {
									$orgId = $this->updateOrg($org, $orgData);
								} else {
									$org['datecreated'] = date('Y-m-d H:i:s');
									$org['addedfrom'] = get_staff_user_id();
									$orgId = $this->insertOrg($org);
								}
								
							}
							//Person
							$person = array();
							if(!empty($needed_fields) && (in_array("primary_contact", $needed_fields) || in_array("project_contacts[]", $needed_fields) )) {
								if($insert['person_fullname']) {
									$person['firstname'] = $insert['person_fullname'];
								}
								if($insert['person_email']) {
									$person['email'] = $insert['person_email'];
								}
								if($insert['person_phonenumber']) {
									$person['phonenumber'] = $insert['person_phonenumber'];
								}
								if($insert['person_position']) {
									$person['title'] = $insert['person_position'];
								}
								$personData = $this->checkPerson($person);
								
								if($personData) {
									$personId = $this->updatePerson($person, $personData, $orgId);
								} else {
									$personId = $this->insertPerson($person, $orgId);
								}
							}
							//Deal
							$deal = array();
							if($insert['deal_name']) {
								$deal['name'] = $insert['deal_name'];
							}
							if($insert['deal_description']) {
								$deal['description'] = $insert['deal_description'];
							}
							if($insert['deal_pipeline']) {
								$deal['pipeline_id'] = $insert['deal_pipeline'];
							}
							else{
								$deal['pipeline_id'] = get_option('default_pipeline');
							}
							if($insert['deal_pipeline_stage']) {
								$deal['status'] = $insert['deal_pipeline_stage'];
								$this->ci->db->select('progress');
								$this->ci->db->where('name', $deal['status']);
								$progress =  $this->ci->db->get(db_prefix() . 'projects_status')->row();//->progress;
								if(!empty($progress)){
									$deal['progress'] = $progress->progress;
								}
								//$this->ci->db->select('progress');
							}
							else{
								$this->ci->db->where('id', $deal['pipeline_id']);
								$deals =  $this->ci->db->get(db_prefix() . 'pipeline')->row();
								if(!empty($deals->default_status)){
									$deal['status'] = $deals->default_status;
									
								}
								if($deal['status']){
									$this->ci->db->select('progress');
									$this->ci->db->where('id', $deal['status']);
									$deal['progress'] = $this->ci->db->get(db_prefix() . 'projects_status')->row()->progress;
									$this->ci->db->select('name');
									$this->ci->db->where('id', $deal['status']);
									$deal['status'] = $this->ci->db->get(db_prefix() . 'projects_status')->row()->name;
								}
							}
							if($insert['deal_owner']) {
								$deal['teamleader'] = $insert['deal_owner'];
							}
							if($insert['deal_start_date']) {
								$deal['start_date'] = date('Y-m-d',strtotime($insert['deal_start_date']));
							}
							if($insert['deal_deadline']) {
								$deal['deadline'] = date('Y-m-d',strtotime($insert['deal_deadline']));
							}
							$deal['project_created'] = date('Y-m-d');
							if($insert['deal_project_cost']) {
								$deal['project_cost'] = $insert['deal_project_cost'];
                            }
                            if($insert['deal_stage']) {
                                $dealStage = strtolower($insert['deal_stage']);
                                if($dealStage == 'won') {
                                    $deal['stage_of'] = 1;
                                } elseif ($dealStage == 'loss') {
                                    $deal['stage_of'] = 2;
                                } else {
                                    $deal['stage_of'] = 0;
                                }
							}
							if($insert['deal_project_currency']) {
								$deal['project_currency'] = $insert['deal_project_currency'];
							}
							if($insert['deal_followers']) {
								$deal['deal_followers'] = $insert['deal_followers'];
							}
							$deal['imported_id'] = $this->uniqueId;
							$dealId = $this->insertDeal($deal, $orgId, $personId);
							//Activity
							$activity = array();
							if($insert['activity_name']) {
								$activity['name'] = $insert['activity_name'];
							}
							if($insert['activity_tasktype']) {
								$activity['tasktype'] = $insert['activity_tasktype'];
							}
							if($insert['activity_description']) {
								$activity['description'] = $insert['activity_description'];
							}
							if($insert['activity_send_reminder']) {
								$activity['send_reminder'] = $insert['activity_send_reminder'];
							}
							if($insert['activity_priority']) {
								$priority = strtolower($insert['activity_priority']);
								if($priority == 'low') {
									$activity['priority'] = 1;
								} else if($priority == 'medium') {
									$activity['priority'] = 2;
								} else if($priority == 'high') {
									$activity['priority'] = 3;
								} else {
									$activity['priority'] = 4;
								}
							}
							if($insert['activity_startdate']) {
								$startdate = date('Y-m-d H:i:s',strtotime($insert['activity_startdate']));
								$activity['startdate'] = $startdate;
								$activity['dateadded'] = $startdate;
								if($startdate == date('Y-m-d')){
									$activity['status']  = 3;
								}
								if($startdate < date('Y-m-d')){
									$activity['status']  = 2;
								}
								if (date('Y-m-d') < $startdate) {
									$activity['status'] = 1;
								}
							}
							$activity['rel_id'] = $dealId;
							$activity['rel_type'] = 'project'; 
							$activity['contacts_id'] = $personId;
							$activity['addedfrom'] = get_staff_user_id();
							$activity['imported_id'] = $this->uniqueId;
							$activityId = $this->insertActivity($activity, $insert['activity_assignedto']);
						
							foreach ($this->getCustomFields() as $field) {
								$insertCusField = [];
								if($insert[$field['slug']] && $insert[$field['slug']] != '') {
									if($field['fieldto'] == 'customers') {
										$relid = $orgId;
									} elseif ($field['fieldto'] == 'contacts') {
										$relid = $personId;
									} else {
										$relid = $dealId;
									}
									$insertCusField['relid'] = $relid;
									$insertCusField['fieldid'] = $field['id'];
									$insertCusField['fieldto'] = $field['fieldto'];
									$insertCusField['value'] = $insert[$field['slug']];
									$activityId = $this->insertCustomValue($insertCusField);
								}
							}
						}
					} else {
						//pre($insert);
						$skipRecord = $this->contactDataToSkip($insert);
						if($skipRecord) {
							//array_unshift($row , $skipRecord);
							//$skippedData[] = $row;
							$skipCnt++;
						} else {
							$importCnt++;
							//Person
							$person = array();
							if($insert['person_fullname']) {
								$person['firstname'] = $insert['person_fullname'];
							}
							if($insert['person_email']) {
								$person['email'] = $insert['person_email'];
							}
							if($insert['person_phonenumber']) {
								$person['phonenumber'] = $insert['person_phonenumber'];
							}
							if($insert['person_position']) {
								$person['title'] = $insert['person_position'];
							}
							$personData = $this->checkPerson($person);
							if($personData) {
								$personId = $this->updatePersonFromDealId($person, $personData, $insert['deals_id']);
							} else {
								$personId = $this->insertPersonFromDealId($person, $insert['deals_id']);
							}

							foreach ($this->getCustomFields() as $field) {
								$insertCusField = [];
								if($insert[$field['slug']] && $insert[$field['slug']] != '') {
									if ($field['fieldto'] == 'contacts') {
										$relid = $personId;
									}

									$insertCusField['relid'] = $relid;
									$insertCusField['fieldid'] = $field['id'];
									$insertCusField['fieldto'] = $field['fieldto'];
									$insertCusField['value'] = $insert[$field['slug']];
									$cfId = $this->insertCustomValue($insertCusField);
								}
							}
							$personContact = $this->insertProjectContacts($personId, $insert['deals_id']);
						}
					}
				}
				
			}
		  }
		  fclose($handle);
		}
		

        $insertArray['skipped'] = $skipCnt;
        $insertArray['imported'] = $importCnt;
        $insertArray['import_id'] = $this->uniqueId;
        $insertArray['filename'] = str_replace(' ','_',$_FILES['file']['name']);
        $insertArray['status'] = 'Finished';
        $insertArray['import_by'] = get_staff_user_id();
        $importFile = $this->insertImportFile($insertArray);
        $result = [];
        $result['skipped'] = $skippedData;
        $result['skippedCnt'] = $skipCnt;
        $result['importedCnt'] = $importCnt;
        return $result;
    }

    public function contactDataToSkip($data) {
        //pre($data);
        $reason = '';
        if(!$data['deals_id']) {
            $reason .= 'Deal id is empty';
        }
        if(!$data['person_fullname']) {
            if($reason)
                $reason .= ', ';
            $reason = 'Person Name is empty';
        }
        
        if($data['deals_id']) {
            $this->ci->db->where('id', $data['deals_id']);
            $orgId = $this->ci->db->get(db_prefix().'projects')->row();
            if(!$orgId) {
                if($reason)
                    $reason .= ', ';
                $reason = 'Deal not exist';
            }
        }
        return $reason;
    }

    public function checkDataToSkip($data) {
        $reason = '';
		$fields1 = get_option('deal_mandatory');
		$mandatory_fields = array();
		if(!empty($fields1) && $fields1 != 'null'){
			$mandatory_fields = json_decode($fields1);
		}		
		array_unshift($mandatory_fields,"name");
        if(!$data['person_fullname'] && (in_array("project_contacts[]", $mandatory_fields) || in_array("primary_contact", $mandatory_fields) )) {
            $reason = 'Person Name is empty';
        }
        if(!$data['organization_name'] && in_array("clientid", $mandatory_fields)) {
            if($reason)
                $reason .= ', ';
            $reason .= 'Organization Name is empty';
        }
        if(!$data['deal_name'] && in_array("name", $mandatory_fields)){
            if($reason)
                $reason .= ', ';
            $reason .= 'Deal Name is empty';
        }
        if(!$data['deal_start_date'] && in_array("start_date", $mandatory_fields)) {
            if($reason)
                $reason .= ', ';
            $reason .= 'Deal Start Date is empty';
        }
        if(!$data['deal_project_currency'] ) {
            if($reason)
                $reason .= ', ';
            $reason .= 'Deal Currency is empty';
        }
		if( in_array("pipeline_id", $mandatory_fields)){
			if(!$data['deal_pipeline']) {
				if($reason)
					$reason .= ', ';
				$reason .= 'Deal Pipeline is empty';
			} else {
				//Get Pipeline Id
				$this->ci->db->where('name', $data['deal_pipeline']);
				$pipeline = $this->ci->db->get(db_prefix().'pipeline')->row();
				if(!$pipeline) {
					if($reason)
						$reason .= ', ';
					$reason .= 'Pipeline not exist'; 
				}
			}
		}
		if( in_array("status", $mandatory_fields)){
			if(!$data['deal_pipeline_stage']) {
				if($reason)
					$reason .= ', ';
				$reason .= 'Deal Pipeline Stage is empty';
			} else {
				//Get Pipeline stage Id
				$this->ci->db->where('name', $data['deal_pipeline_stage']);
				$status = $this->ci->db->get(db_prefix().'projects_status')->row();
				if(!$status) {
					if($reason)
						$reason .= ', ';
					$reason .= 'Pipeline Stage not exist'; 
				}
			}
		}
		if( in_array("teamleader", $mandatory_fields)){
			if(!$data['deal_owner']) {
				if($reason)
					$reason .= ', ';
				$reason .= 'Deal Owner is empty';
			} else {
				//Get teamleader Id
				$this->ci->db->where('email', $data['deal_owner']);
				$staffid = $this->ci->db->get(db_prefix().'staff')->row();
				if(!$staffid) {
					if($reason)
						$reason .= ', ';
					$reason .= 'Deal Owner not exist'; 
				}
			}
		}
        if(!$data['activity_name']) {
            if($reason)
                $reason .= ', ';
            $reason .= 'Activity Name is empty';
        }
        if(!$data['activity_tasktype']) {
            if($reason)
                $reason .= ', ';
            $reason .= 'Activity Type is empty';
        }
        if(!$data['activity_priority']) {
            if($reason)
                $reason .= ', ';
            $reason .= 'Activity Priority is empty';
        }
        if(!$data['activity_startdate']) {
            if($reason)
                $reason .= ', ';
            $reason .= 'Activity Start Date is empty';
        }
		if(in_array("project_contacts[]", $mandatory_fields) || in_array("primary_contact", $mandatory_fields)){
			if(!$data['activity_assignedto'] ) {
				if($reason)
					$reason .= ', ';
				$reason .= 'Activity Assigned to is empty';
			} else {
				//Get teamleader Id
				$this->ci->db->where('email', $data['activity_assignedto']);
				$staffid = $this->ci->db->get(db_prefix().'staff')->row();
				if(!$staffid) {
					if($reason)
						$reason .= ', ';
					$reason .= 'Activity Assigned Staff not exist.'; 
				}
			}
		}
        return $reason;
    }

    public function insertCustomValue($data) {
        $this->ci->db->where('fieldid', $data['fieldid']);
        $this->ci->db->where('relid', $data['relid']);
        $customResult = $this->ci->db->get(db_prefix().'customfieldsvalues')->row();
        
        if($customResult && ($data['fieldto'] == 'customers' || $data['fieldto'] == 'contacts')) {
            $this->ci->db->where('fieldid', $data['fieldid']);
            $this->ci->db->where('relid', $data['relid']);
            $this->ci->db->update(db_prefix() . 'customfieldsvalues', $data);
        } else {
            $this->ci->db->insert(db_prefix() . 'customfieldsvalues', $data);
        }
        return true;
    }

    public function insertDeal($deal, $orgid, $personid) {
        $dealFollowers = $deal['deal_followers'];
        unset($deal['deal_followers']);
        //Get Pipeline Id
		$this->ci->db->select('*');
		$this->ci->db->from(db_prefix().'pipeline');
        $this->ci->db->where('name', $deal['pipeline_id']);
        $pipeline = $this->ci->db->get()->row();//->row();
        //Get Pipeline stage Id
        $this->ci->db->where('name', $deal['status']);
        $status = $this->ci->db->get(db_prefix().'projects_status')->row();

        //Get teamleader Id
        $this->ci->db->where('email', $deal['teamleader']);
        $staffid = $this->ci->db->get(db_prefix().'staff')->row();
		$deal['pipeline_id'] = $deal['status'] = 0;
		$fields = get_option('deal_fields');
		$need_fields12 = array();
		if(!empty($fields) && $fields != 'null'){
			$need_fields12 = json_decode($fields);
		}
		
		if(!empty($need_fields12) && in_array("pipeline_id", $need_fields12)){
			if(!empty($pipeline->id)){
				$deal['pipeline_id'] = $pipeline->id;
			}
		}
		else{
			$deal['pipeline_id']  = get_option('default_pipeline');
		}
		
		//if(!empty($need_fields12) && in_array("status", $need_fields12)){
			if(!empty($status->id)){
				$deal['status'] = $status->id;
			}
		/*}
		else{
			$default_pipeline = get_option('default_pipeline');
			$this->ci->db->where('id', $id);
			$deals = $this->ci->db->get(db_prefix() . 'pipeline')->row();
			$deal['status']  = $deals->default_status;
		}*/
		if(!empty( $staffid->staffid)){
			$deal['teamleader'] = $staffid->staffid;
		}
		if(!empty($orgid)){
			$deal['clientid'] = $orgid;
		}

        $this->ci->db->insert(db_prefix() . 'projects', $deal);
        $dealid = $this->ci->db->insert_id();
        $data = [];
        $data['contacts_id'] = $personid;
        $data['project_id'] = $dealid;
        $data['is_primary'] = 1;
        $this->ci->db->insert(db_prefix() . 'project_contacts', $data);

        //Insert Deal Followers
        $followerExp = explode(',',$dealFollowers);
        foreach($followerExp as $follower) {
            $this->ci->db->where('email', $follower);
            $staffid = $this->ci->db->get(db_prefix().'staff')->row();
            if($staffid) {
                $pmData = [];
                $pmData['project_id'] = $dealid;
                $pmData['staff_id'] = $staffid->staffid;
                $this->ci->db->insert(db_prefix() . 'project_members', $pmData);
            }
        }

        return $dealid;
    }

    public function insertImportFile($data) {
        $this->ci->db->insert(db_prefix() . 'importfiles', $data);
        return true;
    }

    public function insertActivity($data,$assignedto) {
        //Activity Type
        $this->ci->db->where('name', $data['tasktype']);
        $atype = $this->ci->db->get(db_prefix().'tasktype')->row();
        $data['tasktype'] = $atype->id;

        $this->ci->db->insert(db_prefix() . 'tasks', $data);
        $activityId = $this->ci->db->insert_id();

        //Get teamleader Id
        $this->ci->db->where('email', $assignedto);
        $staffid = $this->ci->db->get(db_prefix().'staff')->row();
        $taskdata = [];
        $taskdata['taskid'] = $activityId;
		if(!empty($staffid->staffid)){
			$taskdata['staffid'] = $staffid->staffid;
		}
        $taskdata['assigned_from'] = get_staff_user_id();
        $this->ci->db->insert(db_prefix() . 'task_assigned', $taskdata);
        return $activityId;
    }

    public function updatePerson($data, $personResult, $orgId) {
        $personData = $personResult[0];
        if($data['email']) {
            if($personData['email'] == $data['email']) {
                $update['email']  = $data['email'];
            } else {
                $explodeEmail = explode(',',$personData['alternative_emails']);
                //$existmail = array_search($data['email'],$explodeEmail);
                if(!in_array($data['email'],$explodeEmail)) {
                    if($personData['alternative_emails']) {
                        $update['alternative_emails']  = implode(',',$explodeEmail).','.$data['email'];
                    } else {
                        $update['alternative_emails']  = $data['email'];
                    }
                }
            }
        }

        if($data['phonenumber']) {
            if($personData['phonenumber'] == $data['phonenumber']) {
                $update['phonenumber']  = $data['phonenumber'];
            } else {
                $emplodPhone = explode(',',$personData['alternative_phonenumber']);
                
                if(!in_array($data['phonenumber'],$emplodPhone)) {
                    if($personData['alternative_phonenumber']) {
                        $update['alternative_phonenumber']  = implode(',',$emplodPhone).','.$data['phonenumber'];
                    } else {
                        $update['alternative_phonenumber']  = $data['phonenumber'];
                    }
                   
                }
                
            }
        }
        $update['firstname'] = $data['firstname'];
        $update['title']  = $data['title'];
        $update['userid'] = $update['userids'] = $orgId;
        $this->ci->db->where('id', $personData['id']);
        $this->ci->db->update(db_prefix() . 'contacts', $update);
        return $personData['id'];
    }

    public function insertPerson($data,$orgId) {
        $data['userid'] = $data['userids'] = $orgId;
        $this->ci->db->insert(db_prefix() . 'contacts', $data);
        return $this->ci->db->insert_id();
    }
    
    public function insertProjectContacts($personId, $dealId) {
        $this->ci->db->where('project_id', $dealId);
        $this->ci->db->where('contacts_id', $personId);
        $exist = $this->ci->db->get(db_prefix().'project_contacts')->row();
        if(!$exist) {
            $data = [];
            $data['project_id'] = $dealId;
            $data['contacts_id'] = $personId;
            $data['is_primary'] = 0;
            $this->ci->db->insert(db_prefix() . 'project_contacts', $data);
            return $this->ci->db->insert_id();
        }
    }

    public function insertPersonFromDealId($data,$dealId) {
        $this->ci->db->where('id', $dealId);
        $orgId = $this->ci->db->get(db_prefix().'projects')->row();
        $data['userid'] = $data['userids'] = $orgId->clientid;
        $data['is_primary'] = 0;
        $this->ci->db->insert(db_prefix() . 'contacts', $data);
        return $this->ci->db->insert_id();
    }

    public function updatePersonFromDealId($data, $personResult, $dealId) {
        $personData = $personResult[0];
        if($data['email']) {
            if($personData['email'] == $data['email']) {
                $update['email']  = $data['email'];
            } else {
                $explodeEmail = explode(',',$personData['alternative_emails']);
                $exist = array_search($data['email'],$explodeEmail);
                if(!$exist) {
                    if($personData['alternative_emails']) {
                        $update['alternative_emails']  = implode(',',$explodeEmail).','.$data['email'];
                    } else {
                        $update['alternative_emails']  = $data['email'];
                    }
                }
            }
        }
        if($data['phonenumber']) {
            if($personData['phonenumber'] == $data['phonenumber']) {
                $update['phonenumber']  = $data['phonenumber'];
            } else {
                $emplodPhone = explode(',',$personData['alternative_phonenumber']);
                $exist = array_search($data['phonenumber'],$emplodPhone);
                if(!$exist) {
                    if($personData['alternative_phonenumber']) {
                        $update['alternative_phonenumber']  = implode(',',$emplodPhone).','.$data['phonenumber'];
                    } else {
                        $update['alternative_phonenumber']  = $data['phonenumber'];
                    }
                }
            }
        }
        $this->ci->db->where('id', $dealId);
        $orgId = $this->ci->db->get(db_prefix().'projects')->row();
        $update['firstname'] = $data['firstname'];
        $update['title']  = $data['title'];
        $update['userid'] = $update['userids'] = $orgId->clientid;
        $this->ci->db->where('id', $personData['id']);
        $this->ci->db->update(db_prefix() . 'contacts', $update);
        return $personData['id'];
    }

    public function insertOrg($data) {
        $this->ci->db->insert(db_prefix() . 'clients', $data);
        return $this->ci->db->insert_id();
    }

    public function updateOrg($data, $orgResult) {
        $orgData = $orgResult[0];
        $this->ci->db->where('userid', $orgData['userid']);
        $this->ci->db->update(db_prefix() . 'clients', $data);
        return $orgData['userid'];
    }

    public function checkPerson($data) {
        $this->ci->db->limit(1);
        $this->ci->db->where('firstname', $data['firstname']);
        $contacts = $this->ci->db->get(db_prefix().'contacts')->result_array();
        return $contacts;
    }

    public function checkOrg($data) {
        $this->ci->db->limit(1);
        $this->ci->db->where('company', $data['company']);
        $orgs = $this->ci->db->get(db_prefix().'clients')->result_array();
        return $orgs;
    }

    public function formatFieldNameForHeading($field)
    {
        if (strtolower($field) == 'title') {
            return 'Position';
        }

        return parent::formatFieldNameForHeading($field);
    }

    protected function email_formatSampleData()
    {
        return uniqid() . '@example.com';
    }

    protected function failureRedirectURL()
    {
        return admin_url('clients/import');
    }

    protected function afterSampleTableHeadingText($field)
    {
        $contactFields = [
            'firstname', 'lastname', 'email', 'contact_phonenumber', 'title',
        ];

        if (in_array($field, $contactFields)) {
            return '<br /><span class="text-info">' . _l('import_contact_field') . '</span>';
        }
    }

    private function insertCustomerGroups($groups, $customer_id)
    {
        foreach ($groups as $group) {
            $this->ci->db->insert(db_prefix().'customer_groups', [
                                                    'customer_id' => $customer_id,
                                                    'groupid'     => $group,
                                                ]);
        }
    }

    private function shouldAddContactUnderCustomer($data)
    {
        return (isset($data['company']) && $data['company'] != '' && $data['company'] != '/')
        && (total_rows(db_prefix().'clients', ['company' => $data['company']]) === 1);
    }

    private function addContactUnderCustomer($data)
    {
        $contactFields = $this->getContactFields();
        $this->ci->db->where('company', $data['company']);

        $existingCompany = $this->ci->db->get(db_prefix().'clients')->row();
        $tmpInsert       = [];

        foreach ($data as $key => $val) {
            foreach ($contactFields as $tmpContactField) {
                if (isset($data[$tmpContactField])) {
                    $tmpInsert[$tmpContactField] = $data[$tmpContactField];
                }
            }
        }
        $tmpInsert['donotsendwelcomeemail'] = true;

        if (isset($data['contact_phonenumber'])) {
            $tmpInsert['phonenumber'] = $data['contact_phonenumber'];
        }

        $this->ci->clients_model->add_contact($tmpInsert, $existingCompany->userid, true);
    }

    private function getContactFields()
    {
        return $this->ci->db->list_fields(db_prefix().'contacts');
    }

    private function isDuplicateContact($email)
    {
        return total_rows(db_prefix().'contacts', ['email' => $email]);
    }

    private function formatValuesForSimulation($values)
    {
        // ATM only country fields
        foreach ($this->countryFields as $country_field) {
            if (array_key_exists($country_field, $values)) {
                if (!empty($values[$country_field]) && is_numeric($values[$country_field])) {
                    $country = $this->getCountry(null, $values[$country_field]);
                    if ($country) {
                        $values[$country_field] = $country->short_name;
                    }
                }
            }
        }

        return $values;
    }

    private function getCountry($search = null, $id = null)
    {
        if ($search) {
            $this->ci->db->where('iso2', $search);
            $this->ci->db->or_where('short_name', $search);
            $this->ci->db->or_where('long_name', $search);
        } else {
            $this->ci->db->where('country_id', $id);
        }

        return  $this->ci->db->get(db_prefix().'countries')->row();
    }

    private function countryValue($value)
    {
        if ($value != '') {
            if (!is_numeric($value)) {
                $country = $this->getCountry($value);
                $value   = $country ? $country->country_id : 0;
            }
        } else {
            $value = 0;
        }

        return $value;
    }
}
