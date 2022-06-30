<?php

defined('BASEPATH') or exit('No direct script access allowed');
class ApiController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        load_admin_language();
        $this->load->model('Authenticationapi_model');
        $this->load->model('projects_model');
        $this->load->model('tasktype_model');
        $this->load->model('staff_model');
        $this->load->model('pipeline_model');
        $this->load->model('gdpr_model');
        $postdata = file_get_contents("php://input");
        $_POST = (array) json_decode($postdata);
    }

    public function getlead()
    {
		echo $this->endAccessToken; exit;
        
    }

    public function getactivity(){
        $singledate = '';
        $daterange = '';
        $taskassigned = '';
        $tasktype = '';
        if(is_admin($this->staffid)) {
            $notadminqry = '';
        } else {
            $notadminqry = 'AND tbltasks.rel_id IN (
                SELECT
                  tblprojects.id
                FROM
                  tblprojects
                  join tblproject_members on tblproject_members.project_id = tblprojects.id
                WHERE
                  tblproject_members.staff_id in ('.$this->staffid.')
                  OR tblprojects.teamleader in ('.$this->staffid.')
              )';
        }
        if(!empty($_POST['singledate'])) {
            $singledate = ' AND date(startdate) = "'.date('Y-m-d',strtotime($_POST['singledate'])).'" ';
        }
        if(!empty($_POST['startdate']) && !empty($_POST['enddate'])) {
            $daterange = 'AND (
                date(startdate) >= "'.date('Y-m-d',strtotime($_POST['startdate'])).'"
                AND date(startdate) <= "'.date('Y-m-d',strtotime($_POST['enddate'])).'"
              )';
        }
        if(!empty($_POST['taskassigned'])) {
            $taskassigned = 'AND (
                tbltasks.id IN (
                  SELECT
                    taskid
                  FROM
                    tbltask_assigned
                  WHERE
                    staffid IN ('.$_POST['taskassigned'].')
                )
              )';
        }
        $taskfollower = '';
        if(!empty($_POST['taskfollower'])) {
            $taskfollower = 'AND (
                tbltasks.id IN (
                  SELECT
                    taskid
                  FROM
                    tbltask_followers
                  WHERE
                    staffid IN ('.$_POST['taskfollower'].')
                )
              )';
        }
        if(!empty($_POST['tasktype'])) {
            $tasktype = ' AND (tbltasks.tasktype IN ('.$_POST['tasktype'].')) ';
        }
        $searchby = '';
        if(!empty($_POST['q'])) {
            $searchby = '
                convert(tbltasks.name USING utf8) LIKE "%'.$_POST['q'].'%" 
                OR convert(tblprojects.name USING utf8) LIKE "%'.$_POST['q'].'%"  
                OR convert(tblprojects_status.name USING utf8) LIKE "%'.$_POST['q'].'%"  
                OR convert(tblclients.company USING utf8) LIKE "%'.$_POST['q'].'%"  
                OR convert(tblprojects.teamleader USING utf8) LIKE "%'.$_POST['q'].'%"  
                OR convert(tblcontacts.firstname USING utf8) LIKE "%'.$_POST['q'].'%"  
                OR convert(tbltasks.status USING utf8) LIKE "%'.$_POST['q'].'%"  
                OR convert(tbltasktype.name USING utf8) LIKE "%'.$_POST['q'].'%"  
                OR convert(startdate USING utf8) LIKE "%'.$_POST['q'].'%"  
                OR convert((
                SELECT
                   GROUP_CONCAT(CONCAT(firstname, " ", lastname) SEPARATOR ", ") 
                FROM
                   tbltask_assigned 
                   JOIN
                      tblstaff 
                      ON tblstaff.staffid = tbltask_assigned.staffid 
                WHERE
                   taskid = tbltasks.id 
                ORDER BY
                   tbltask_assigned.staffid) USING utf8) LIKE "%'.$_POST['q'].'%"  
                   OR convert(tbltasks.description USING utf8) LIKE "%'.$_POST['q'].'%"  
                   OR convert((
                   SELECT
                      GROUP_CONCAT(name SEPARATOR ", ") 
                   FROM
                      tbltaggables 
                      JOIN
                         tbltags 
                         ON tbltaggables.tag_id = tbltags.id 
                   WHERE
                      rel_id = tbltasks.id 
                      and rel_type = "task" 
                   ORDER by
                      tag_order ASC) USING utf8) LIKE "%'.$_POST['q'].'%"  
                      OR convert(priority USING utf8) LIKE "%'.$_POST['q'].'%"  
                      OR convert(rel_type USING utf8) LIKE "%'.$_POST['q'].'%"  
                      OR convert(rel_id USING utf8) LIKE "%'.$_POST['q'].'%"  
                      OR convert(recurring USING utf8) LIKE "%'.$_POST['q'].'%"  
                      OR convert((
                      CASE
                         rel_type 
                         WHEN
                            "contract" 
                         THEN
          (
                            SELECT
                               subject 
                            FROM
                               tblcontracts 
                            WHERE
                               tblcontracts.id = tbltasks.rel_id) 
                            WHEN
                               "estimate" 
                            THEN
          (
                               SELECT
                                  id 
                               FROM
                                  tblestimates 
                               WHERE
                                  tblestimates.id = tbltasks.rel_id) 
                               WHEN
                                  "proposal" 
                               THEN
          (
                                  SELECT
                                     id 
                                  FROM
                                     tblproposals 
                                  WHERE
                                     tblproposals.id = tbltasks.rel_id) 
                                  WHEN
                                     "invoice" 
                                  THEN
          (
                                     SELECT
                                        id 
                                     FROM
                                        tblinvoices 
                                     WHERE
                                        tblinvoices.id = tbltasks.rel_id) 
                                     WHEN
                                        "ticket" 
                                     THEN
          (
                                        SELECT
                                           CONCAT(CONCAT(" # ", tbltickets.ticketid), " - ", tbltickets.subject) 
                                        FROM
                                           tbltickets 
                                        WHERE
                                           tbltickets.ticketid = tbltasks.rel_id) 
                                        WHEN
                                           "lead" 
                                        THEN
          (
                                           SELECT
                                              CASE
                                                 tblleads.email 
                                                 WHEN
                                                    "" 
                                                 THEN
                                                    tblleads.name 
                                                 ELSE
                                                    CONCAT(tblleads.name, " - ", tblleads.email) 
                                              END
                                           FROM
                                              tblleads 
                                           WHERE
                                              tblleads.id = tbltasks.rel_id) 
                                              WHEN
                                                 "customer" 
                                              THEN
          (
                                                 SELECT
                                                    CASE
                                                       company 
                                                       WHEN
                                                          "" 
                                                       THEN
          (
                                                          SELECT
                                                             CONCAT(firstname, " ", lastname) 
                                                          FROM
                                                             tblcontacts 
                                                          WHERE
                                                             userid = tblclients.userid 
                                                             and is_primary = 1) 
                                                          ELSE
                                                             company 
                                                    END
                                                          FROM
                                                             tblclients 
                                                          WHERE
                                                             tblclients.userid = tbltasks.rel_id) 
                                                             WHEN
                                                                "project" 
                                                             THEN
          (
                                                                SELECT
                                                                   CONCAT(CONCAT(CONCAT(" # ", tblprojects.id), " - ", tblprojects.name), " - ", 
                                                                   (
                                                                      SELECT
                                                                         CASE
                                                                            company 
                                                                            WHEN
                                                                               "" 
                                                                            THEN
          (
                                                                               SELECT
                                                                                  CONCAT(firstname, " ", lastname) 
                                                                               FROM
                                                                                  tblcontacts 
                                                                               WHERE
                                                                                  userid = tblclients.userid 
                                                                                  and is_primary = 1) 
                                                                               ELSE
                                                                                  company 
                                                                         END
                                                                               FROM
                                                                                  tblclients 
                                                                               WHERE
                                                                                  userid = tblprojects.clientid
                                                                   )
          ) 
                                                                FROM
                                                                   tblprojects 
                                                                WHERE
                                                                   tblprojects.id = tbltasks.rel_id) 
                                                                   WHEN
                                                                      "expense" 
                                                                   THEN
          (
                                                                      SELECT
                                                                         CASE
                                                                            expense_name 
                                                                            WHEN
                                                                               "" 
                                                                            THEN
                                                                               tblexpenses_categories.name 
                                                                            ELSE
                                                                               CONCAT(tblexpenses_categories.name, " (", tblexpenses.expense_name, ")") 
                                                                         END
                                                                      FROM
                                                                         tblexpenses 
                                                                         JOIN
                                                                            tblexpenses_categories 
                                                                            ON tblexpenses_categories.id = tblexpenses.category 
                                                                      WHERE
                                                                         tblexpenses.id = tbltasks.rel_id) 
                                                                         ELSE
                                                                            NULL 
                      END
          ) USING utf8) LIKE "%'.$_POST['q'].'%"  
                      OR convert(billed USING utf8) LIKE "%'.$_POST['q'].'%"  
                      OR convert((
                      SELECT
                         staffid 
                      FROM
                         tbltask_assigned 
                      WHERE
                         taskid = tbltasks.id 
                         AND staffid = '.$this->staffid.') USING utf8) LIKE "%'.$_POST['q'].'%"  
                         OR convert((
                         SELECT
                            GROUP_CONCAT(staffid SEPARATOR ", ") 
                         FROM
                            tbltask_assigned 
                         WHERE
                            taskid = tbltasks.id 
                         ORDER BY
                            tbltask_assigned.staffid) USING utf8) LIKE "%'.$_POST['q'].'%"  
                            OR convert((
                            SELECT
                               MAX(id) 
                            FROM
                               tbltaskstimers 
                            WHERE
                               task_id = tbltasks.id 
                               and staff_id = '.$this->staffid.' 
                               and end_time IS NULL) USING utf8) LIKE "%'.$_POST['q'].'%"  
                               OR convert((
                               SELECT
                                  staffid 
                               FROM
                                  tbltask_assigned 
                               WHERE
                                  taskid = tbltasks.id 
                                  AND staffid = '.$this->staffid.') USING utf8) LIKE "%'.$_POST['q'].'%"  
                                  OR convert((
                                  SELECT
                                     CASE
                                        WHEN
                                           tbltasks.addedfrom = '.$this->staffid.' 
                                           AND is_added_from_contact = 0 
                                        THEN
                                           1 
                                        ELSE
                                           0 
                                     END
          ) USING utf8) LIKE "%'.$_POST['q'].'%"  
             ';
        }
        
        if(!empty($searchby)) {
            $searchby = '('.$searchby.') AND ';
        }
        $bystatus = '';
        if(!empty($_POST['status'])) {
            $exp = explode(',',$_POST['status']);
            foreach($exp as $val) {
                if($val == 1) {
                    $bystatus .= '(date(startdate) > "'.date('Y-m-d').'" 
                    AND tbltasks.status != 5) ';
                }
                if($val == 3) {
                    if($bystatus) {
                        $bystatus .= ' OR ';
                    }
                    $bystatus .= '(date(startdate) = "'.date('Y-m-d').'" 
                    AND tbltasks.status != 5) ';
                }
                if($val == 2 || $val == 5) {
                    if($bystatus) {
                        $bystatus .= ' OR ';
                    }
                    $bystatus .= 'tbltasks.status = "'.$val.'" ';
                }
                
            }
            if($bystatus) {
                $bystatus = ' ( '.$bystatus.' ) ';
            }
           
        } else {
            $bystatus = ' ( tbltasks.status IN (1, 3, 2, 5) ) ';
        }
        $mysql_qry = 'SELECT
        SQL_CALC_FOUND_ROWS tbltasks.id as id,
        tblclients.userid as userid,
        tbltasks.id as id,
        tbltasks.name as task_name,
        tblprojects.name as project_name,
        tblprojects_status.name as project_status,
        tblclients.company as company,
        tblcontacts.firstname as project_contacts,
        tbltasks.status as status,
        tbltasktype.name as tasktype,
        startdate,
        deadline,
        (
          SELECT
            GROUP_CONCAT(CONCAT(firstname, " ", lastname) SEPARATOR ",")
          FROM
            tbltask_assigned
            JOIN tblstaff ON tblstaff.staffid = tbltask_assigned.staffid
          WHERE
            taskid = tbltasks.id
          ORDER BY
            tbltask_assigned.staffid
        ) as assignees,
        priority,
        (
          SELECT
            GROUP_CONCAT(name SEPARATOR ",")
          FROM
            tbltaggables
            JOIN tbltags ON tbltaggables.tag_id = tbltags.id
          WHERE
            rel_id = tbltasks.id
            and rel_type = "task"
          ORDER by
            tag_order ASC
        ) as tags,
        tbltasks.description as description,
        ctable_0.value as cvalue_contacts_person_cf_2,
        ctable_1.value as cvalue_contacts_person_cf,
        ctable_2.value as cvalue_customers_type,
        tblclients.userid as userid,
        tblprojects.id as projectid,
        (
          SELECT
            GROUP_CONCAT(CONCAT(firstname, " ", lastname) SEPARATOR ",")
          FROM
            tblstaff 
          WHERE
            tblstaff.staffid = tblprojects.teamleader
        ) as p_teamleader,
        tblprojects.teamleader as p_teamleader_id,
        tblcontacts.id as contactsid,
        rel_type,
        rel_id,
        recurring,(
          CASE rel_type WHEN "contract" THEN (
            SELECT
              subject
            FROM
              tblcontracts
            WHERE
              tblcontracts.id = tbltasks.rel_id
          ) WHEN "estimate" THEN (
            SELECT
              id
            FROM
              tblestimates
            WHERE
              tblestimates.id = tbltasks.rel_id
          ) WHEN "proposal" THEN (
            SELECT
              id
            FROM
              tblproposals
            WHERE
              tblproposals.id = tbltasks.rel_id
          ) WHEN "invoice" THEN (
            SELECT
              id
            FROM
              tblinvoices
            WHERE
              tblinvoices.id = tbltasks.rel_id
          ) WHEN "ticket" THEN (
            SELECT
              CONCAT(
                CONCAT("#", tbltickets.ticketid),
                " - ",
                tbltickets.subject
              )
            FROM
              tbltickets
            WHERE
              tbltickets.ticketid = tbltasks.rel_id
          ) WHEN "lead" THEN (
            SELECT
              CASE tblleads.email WHEN "" THEN tblleads.name ELSE CONCAT(tblleads.name, " - ", tblleads.email) END
            FROM
              tblleads
            WHERE
              tblleads.id = tbltasks.rel_id
          ) WHEN "customer" THEN (
            SELECT
              CASE company WHEN "" THEN (
                SELECT
                  CONCAT(firstname, " ", lastname)
                FROM
                  tblcontacts
                WHERE
                  userid = tblclients.userid
                  and is_primary = 1
              ) ELSE company END
            FROM
              tblclients
            WHERE
              tblclients.userid = tbltasks.rel_id
          ) WHEN "project" THEN (
            SELECT
              CONCAT(
                CONCAT(
                  CONCAT("#", tblprojects.id),
                  " - ",
                  tblprojects.name
                ),
                " - ",
                (
                  SELECT
                    CASE company WHEN "" THEN (
                      SELECT
                        CONCAT(firstname, " ", lastname)
                      FROM
                        tblcontacts
                      WHERE
                        userid = tblclients.userid
                        and is_primary = 1
                    ) ELSE company END
                  FROM
                    tblclients
                  WHERE
                    userid = tblprojects.clientid
                )
              )
            FROM
              tblprojects
            WHERE
              tblprojects.id = tbltasks.rel_id
          ) WHEN "expense" THEN (
            SELECT
              CASE expense_name WHEN "" THEN tblexpenses_categories.name ELSE CONCAT(
                tblexpenses_categories.name,
                " (",
                tblexpenses.expense_name,
                ")"
              ) END
            FROM
              tblexpenses
              JOIN tblexpenses_categories ON tblexpenses_categories.id = tblexpenses.category
            WHERE
              tblexpenses.id = tbltasks.rel_id
          ) ELSE NULL END
        ) as rel_name,
        billed,(
          SELECT
            staffid
          FROM
            tbltask_assigned
          WHERE
            taskid = tbltasks.id
            AND staffid = '.$this->staffid.'
        ) as is_assigned,(
          SELECT
            GROUP_CONCAT(staffid SEPARATOR ",")
          FROM
            tbltask_assigned
          WHERE
            taskid = tbltasks.id
          ORDER BY
            tbltask_assigned.staffid
        ) as assignees_ids,(
          SELECT
            MAX(id)
          FROM
            tbltaskstimers
          WHERE
            task_id = tbltasks.id
            and staff_id = '.$this->staffid.'
            and end_time IS NULL
        ) as not_finished_timer_by_current_staff,(
          SELECT
            staffid
          FROM
            tbltask_assigned
          WHERE
            taskid = tbltasks.id
            AND staffid = '.$this->staffid.'
        ) as current_user_is_assigned,(
          SELECT
            CASE WHEN tbltasks.addedfrom = '.$this->staffid.'
            AND is_added_from_contact = 0 THEN 1 ELSE 0 END
        ) as current_user_is_creator
      FROM
        tbltasks
        LEFT JOIN tbltasktype as tbltasktype ON tbltasktype.id = tbltasks.tasktype
        LEFT JOIN tblprojects as tblprojects ON tblprojects.id = tbltasks.rel_id
        AND tbltasks.rel_type = "project"
        LEFT JOIN tblprojects_status as tblprojects_status ON tblprojects_status.id = tblprojects.status
        LEFT JOIN tblclients as tblclients ON tblclients.userid = tblprojects.clientid
        LEFT JOIN tblcontacts as tblcontacts ON tblcontacts.id = tbltasks.contacts_id
        LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid
        AND ctable_0.fieldto = "contacts"
        AND ctable_0.fieldid = 7
        LEFT JOIN tblcustomfieldsvalues as ctable_1 ON tblclients.userid = ctable_1.relid
        AND ctable_1.fieldto = "customers"
        AND ctable_1.fieldid = 6
        LEFT JOIN tblcustomfieldsvalues as ctable_2 ON tblclients.userid = ctable_2.relid
        AND ctable_2.fieldto = "customers"
        AND ctable_2.fieldid = 8
      WHERE
        ('.$searchby.$bystatus.$singledate.$daterange.$taskfollower.$taskassigned.$tasktype.')
        AND CASE WHEN rel_type = "project"
        AND rel_id IN (
          SELECT
            project_id
          FROM
            tblproject_settings
          WHERE
            project_id = rel_id
            AND name = "hide_tasks_on_main_tasks_table"
            AND value = 1
        ) THEN rel_type != "project" ELSE 1 = 1 END '.$notadminqry.'
      ORDER BY
        (
          SELECT
            GROUP_CONCAT(CONCAT(firstname, " ", lastname) SEPARATOR ",")
          FROM
            tbltask_assigned
            JOIN tblstaff ON tblstaff.staffid = tbltask_assigned.staffid
          WHERE
            taskid = tbltasks.id
          ORDER BY
            tbltask_assigned.staffid
        ) ASC ';
        $totalcnt = $this->db->query($mysql_qry)->num_rows();
        if($_POST['page'] > 1)
            $limit = ' LIMIT '.(($_POST['page']-1) * 25).', 25 ';
        else
            $limit = ' LIMIT 0, 25 ';
        //$mysql_qry =  $mysql_qry.$limit;
        //exit;
        $query = $this->db->query($mysql_qry);
        $result = $query->result_array();
        if($result) {
            $result['totalcnt'] = $totalcnt;
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $result;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Records Found.';
        }
        echo $out =json_encode($outputArr);
    }
    public function checkinsert()
    {
        $data1 = array(
            'invoice_id' => 1,
            'credit_id' => 1,
            'staff_id' => 1,
            'date' => date("Y-m-d"),
            'date_applied' => date("Y-m-d H:i:s"),
            'amount' => '0.00'
        );
        $this->load->database('perfexcrm');

        $this->db->insert(db_prefix() . 'credits', $data1);
        echo $insert_id = $this->db->insert_id();
        exit;
    }
    public function createactivity()
    {
        $data = [];
		if(isset($_POST['rel_type']) && !empty($_POST['rel_type']) && $_POST['rel_type'] == 'project_task'){
			$_POST['rel_type'] = 'project';
			$project_task = $this->tasks_model->get($_POST['rel_id']);
			$_POST['rel_id'] = $project_task->rel_id;
		}
		
		
        if ($_POST['startdate']) {
            $data['start_date'] = $_POST['startdate'];
            $data['duedate'] = $_POST['startdate'];
        }
        if ($_POST) {
            
            unset($_POST['/api/createactivity']);
            $data                = $_POST;
            $data['description'] = $_POST['description'];
			
			if(isset($data['task_mark_complete_id']) && !empty($data['task_mark_complete_id'])){
				$this->tasks_model->mark_as(5, $data['task_mark_complete_id']);
			}
			if(isset($data['task_mark_complete_id'])){
				unset($data['task_mark_complete_id']);
            }
            
           
            $data_assignee = $data['assignees'];
            unset($data['assignees']);
            if($data['id']) {
                //unset($data['id']);
                $id = $_POST['id'];
                
                $success = $this->tasks_model->editactivityfromapi($data, $id);
                $data['taskid'] =  $id;
                $task_assignees_already     = $this->tasks_model->get_task_assignees($id);
                $task_assignees_ids = [];
                foreach ($task_assignees_already as $aa) {
                    array_push($task_assignees_ids, $aa['assigneeid']);
                }
                foreach($data_assignee as $taskey => $tasvalue ){
                    if(!in_array($tasvalue,$task_assignees_ids)){
                        $data['assignee'] = $tasvalue;
                        $this->tasks_model->add_task_assignees_api($data);
                    }
                }
                $message = '';
                if ($id) {
                    $message       = _l('updated_successfully', _l('task'));
                }
            } else {
                $id   = $data['taskid']  = $this->tasks_model->addactivityfromapi($data);
                foreach($data_assignee as $taskey => $tasvalue ){
                    $data['assignee'] = $tasvalue;
                    
                    $this->tasks_model->add_task_assignees_api($data);
                }
                $message = '';
                if ($id) {
                    $message       = _l('added_successfully', _l('task'));
                }
            }
            if($id) {
                $outputArr["status_code"] = 200;
                $outputArr["status"] = true;
                $outputArr["response"] = $id;
                $outputArr["response"] = $message;
            } else {
                $outputArr["status_code"] = 400;
                $outputArr["status"] = false;
                $outputArr["error_message"] = 'Cannot Create Activity.';
            }
            echo $out =json_encode($outputArr);
            die;
        }
    }

    public function getdeals() {
        $loginstaffdeal = '';
        $stage = '';
        $and = ''; 
        $searchby = '';
        if(!empty($_POST['dealname'])) {
            $searchby = 'convert(name USING utf8) LIKE "%'.$_POST['dealname'].'%"';
        }
        if(!empty($_POST['organization'])) {
            if(!empty($searchby)) {
                $searchby = $searchby.' OR convert( CASE company WHEN "" THEN (SELECT	 CONCAT(firstname, " ", lastname) FROM	tblcontacts	
                                            WHERE	
                                            userid = tblclients.userid	
                                            and is_primary = 1	
                                        ) ELSE company END USING utf8	
                                        ) LIKE "%'.$_POST['organization'].'%"	';
            } else {
                $searchby = ' convert( CASE company WHEN "" THEN (SELECT	 CONCAT(firstname, " ", lastname) FROM	tblcontacts	
                                WHERE	
                                userid = tblclients.userid	
                                and is_primary = 1	
                            ) ELSE company END USING utf8	
                            ) LIKE "%'.$_POST['organization'].'%"	';
            }
        }
        
        if(!empty($searchby)) {
            $searchby = ' ('.$searchby.') ';
        }
        

        if($_SESSION['staff_user_id'] > 1 || (isset($_POST['mydeal']) && $_POST['mydeal'] != '')) {
            $staffid = $_SESSION['staff_user_id'];
            if(!empty($searchby)) {
                $and = ' AND ';
            }
            $loginstaffdeal = $and.' 
            tblprojects.id IN (
              SELECT
                tblprojects.id
              FROM
                tblprojects
                join tblproject_members on tblproject_members.project_id = tblprojects.id
              WHERE
                tblproject_members.staff_id in ('.$staffid.')
                OR tblprojects.teamleader in ('.$staffid.')
            ) ';
        }
        if(!empty($_POST['stage'])) {
            if(!empty($loginstaffdeal)) {
                $stage = ' AND status IN ('.$_POST['stage'].') ';
            } else {
                $stage = ' status IN ('.$_POST['stage'].') ';
            }
        }
        if(!empty($searchby) || !empty($loginstaffdeal) || !empty($stage)) {
            $searchby = ' WHERE '.$searchby.$loginstaffdeal.$stage;
        }
        $sqlquery = 'SELECT
        tblprojects.id as id,
        name,
        CASE company WHEN "" THEN (
          SELECT
            CONCAT(firstname, " ", lastname)
          FROM
            tblcontacts
          WHERE
            userid = tblclients.userid
            and is_primary = 1
        ) ELSE company END as company,
        start_date,
        deadline,
        project_cost,
        status,
        (select tblprojects_status.name from tblprojects_status where tblprojects_status.id=tblprojects.status) as status_name,
        pipeline_id,
        (select tblpipeline.name from tblpipeline where tblpipeline.id=tblprojects.pipeline_id) as pipeline_name,
        (
          SELECT
            GROUP_CONCAT(CONCAT(firstname, " ", lastname) SEPARATOR ",")
          FROM
            tblproject_members
            JOIN tblstaff on tblstaff.staffid = tblproject_members.staff_id
          WHERE
            project_id = tblprojects.id
          ORDER BY
            staff_id
        ) as members,
        (
          SELECT
            GROUP_CONCAT(staff_id SEPARATOR ",")
          FROM
            tblproject_members
          WHERE
            project_id = tblprojects.id
          ORDER BY
            staff_id
        ) as members_ids
      FROM
        tblprojects
        LEFT JOIN tblclients ON tblclients.userid = tblprojects.clientid '.$searchby.'
      ORDER BY
        deadline IS NULL ASC,
        deadline ASC ';
        $totalcnt = $this->db->query($sqlquery)->num_rows();
        if($_POST['page'] > 1)
            $limit = ' LIMIT '.(($_POST['page']-1) * 25).', 25 ';
        else
            $limit = ' LIMIT 0, 25 ';
        $sqlquery =  $sqlquery.$limit;
        $query = $this->db->query($sqlquery);
        $result = $query->result_array();
        if($result) {
            $result['totalcnt'] = $totalcnt;
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $result;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Records Found.';
        }
        echo $out =json_encode($outputArr);
    }

    public function list_activities_by_dealid() {
        //echo "<pre>"; print_r($_POST); exit;
        $singledate = '';
        $daterange = '';
        $taskassigned = '';
        $tasktype = '';
        if(is_admin($this->staffid)) {
            $notadminqry = '';
        } else {
            $notadminqry = 'AND tbltasks.rel_id IN (
                SELECT
                  tblprojects.id
                FROM
                  tblprojects
                  join tblproject_members on tblproject_members.project_id = tblprojects.id
                WHERE
                  tblproject_members.staff_id in ('.$this->staffid.')
                  OR tblprojects.teamleader in ('.$this->staffid.')
              )';
        }
        if(!empty($_POST['singledate'])) {
            $singledate = ' AND date(startdate) = "'.date('Y-m-d',strtotime($_POST['singledate'])).'" ';
        }
        if(!empty($_POST['startdate']) && !empty($_POST['enddate'])) {
            $daterange = 'AND (
                date(startdate) >= "'.date('Y-m-d',strtotime($_POST['startdate'])).'"
                AND date(startdate) <= "'.date('Y-m-d',strtotime($_POST['enddate'])).'"
              )';
        }
        if(!empty($_POST['taskassigned'])) {
            $taskassigned = 'AND (
                tbltasks.id IN (
                  SELECT
                    taskid
                  FROM
                    tbltask_assigned
                  WHERE
                    staffid IN ('.$_POST['taskassigned'].')
                )
              )';
        }
        if(!empty($_POST['taskfollower'])) {
            $taskfollower = 'AND (
                tbltasks.id IN (
                  SELECT
                    taskid
                  FROM
                    tbltask_followers
                  WHERE
                    staffid IN ('.$_POST['taskfollower'].')
                )
              )';
        }
        if(!empty($_POST['tasktype'])) {
            $tasktype = ' AND (tbltasks.tasktype IN ('.$_POST['tasktype'].')) ';
        }
        $searchby = '';
        if(!empty($_POST['deal'])) {
            $searchby = 'convert(tblprojects.name USING utf8) LIKE "%'.$_POST['deal'].'%" ';
        }
        if(!empty($_POST['organization'])) {
            if(!empty($searchby)) {
                $searchby = $searchby.' OR convert(tblclients.company USING utf8) LIKE "%'.$_POST['organization'].'%" ';
            } else {
                $searchby = 'convert(tblclients.company USING utf8) LIKE "%'.$_POST['organization'].'%"';
            }
            
        }
        if(!empty($_POST['activity'])) {
            if(!empty($searchby)) {
                $searchby = $searchby.' OR convert(tbltasks.name USING utf8) LIKE "%'.$_POST['activity'].'%" ';
            } else {
                $searchby = 'convert(tbltasks.name USING utf8) LIKE "%'.$_POST['activity'].'%"';
            }
        }
        if(!empty($searchby)) {
            $searchby = '('.$searchby.') AND ';
        }

        $dealid = $_POST['dealid'];
        $staffid = $_SESSION['staff_user_id'];
        $sqlQry = 'SELECT
        SQL_CALC_FOUND_ROWS tbltasks.id as id,
        tblclients.userid as userid,
        tbltasks.id as id,
        tbltasks.name as task_name,
        tbltasks.description as description,
        (
          SELECT
            GROUP_CONCAT(CONCAT(firstname, " ", lastname) SEPARATOR ",")
          FROM
            tbltask_assigned
            JOIN tblstaff ON tblstaff.staffid = tbltask_assigned.staffid
          WHERE
            taskid = tbltasks.id
          ORDER BY
            tbltask_assigned.staffid
        ) as assignees,
        startdate,
        tbltasks.status as status,
        tbltasktype.name as tasktype,
        tblcontacts.firstname as project_contacts,
        tblprojects.id as projectid,
        tblprojects.name as projectname,
        tblprojects.teamleader as p_teamleader,
        tblprojects_status.name as project_status,
        tblclients.company as orgname,
        tblcontacts.id as contactsid,
        billed,
        priority,
        recurring,(
          SELECT
            staffid
          FROM
            tbltask_assigned
          WHERE
            taskid = tbltasks.id
            AND staffid = '.$staffid.'
        ) as is_assigned,(
          SELECT
            GROUP_CONCAT(staffid SEPARATOR ",")
          FROM
            tbltask_assigned
          WHERE
            taskid = tbltasks.id
          ORDER BY
            tbltask_assigned.staffid
        ) as assignees_ids,(
          SELECT
            MAX(id)
          FROM
            tbltaskstimers
          WHERE
            task_id = tbltasks.id
            and staff_id = '.$staffid.'
            and end_time IS NULL
        ) as not_finished_timer_by_current_staff,(
          SELECT
            staffid
          FROM
            tbltask_assigned
          WHERE
            taskid = tbltasks.id
            AND staffid = '.$staffid.'
        ) as current_user_is_assigned,(
          SELECT
            CASE WHEN tbltasks.addedfrom = '.$staffid.'
            AND is_added_from_contact = 0 THEN 1 ELSE 0 END
        ) as current_user_is_creator
      FROM
        tbltasks
        LEFT JOIN tbltasktype as tbltasktype ON tbltasktype.id = tbltasks.tasktype
        LEFT JOIN tblprojects as tblprojects ON tblprojects.id = tbltasks.rel_id
        AND tbltasks.rel_type = "project"
        LEFT JOIN tblprojects_status as tblprojects_status ON tblprojects_status.id = tblprojects.status
        LEFT JOIN tblclients as tblclients ON tblclients.userid = tblprojects.clientid
        LEFT JOIN tblcontacts as tblcontacts ON tblcontacts.id = tbltasks.contacts_id
      WHERE
        ('.$searchby.'tbltasks.status IN (1, 3, 2, 5)'.$singledate.$daterange.$taskfollower.$taskassigned.$tasktype.')
        AND rel_id = '.$dealid.'
        AND rel_type = "project" '.$notadminqry.'
      ORDER BY
        tbltasks.status ASC ';
    $totalcnt = $this->db->query($sqlQry)->num_rows();
    if($_POST['page'] > 1)
        $limit = ' LIMIT '.(($_POST['page']-1) * 25).', 25 ';
    else
        $limit = ' LIMIT 0, 25 ';
    $sqlQry =  $sqlQry.$limit;
    $query = $this->db->query($sqlQry);
    $result = $query->result_array();
    if($result) {
        $result['totalcnt'] = $totalcnt;
         $outputArr["status_code"] = 200;
         $outputArr["status"] = true;
         $outputArr["response"] = $result;
     } else {
         $outputArr["status_code"] = 400;
         $outputArr["status"] = false;
         $outputArr["error_message"] = 'No Records Found.';
     }
     echo $out =json_encode($outputArr);
    }

    public function viewactivity() {
        $data = array();
        $id = $_POST['id'];
        if(isset($_POST['id']) && !empty($_POST['id'])){
            $task_result = $this->tasks_model->gettasks($id);
            if($task_result) {
                $data['task'] = $task_result;
                if ($data['task']->rel_type == 'project') {
                    $data['milestones'] = $this->projects_model->get_milestones($data['task']->rel_id);
                }
                $title = _l('edit', _l('task_lowercase')) . ' ' . $data['task']->name;
                
                //$data['project_end_date_attrs'] = [];
                
                //$data['staff']              = $this->staff_model->get('', ['active' => 1]);
                //pre($data);
                if ($_POST['rel_type'] == 'project' && $_POST['rel_id'] || ($id !== '' && $data['task']->rel_type == 'project')) {
                
                    $project = $this->projects_model->getproject($id === '' ? $_POST['rel_id'] : $data['task']->rel_id);
                
                    $data['project_details_company'] = $project->client_data->company;

                    $project_contacts = $this->projects_model->get_project_contacts($project->id);
                    //echo "<pre>"; print_r($project_contacts); exit;
                    $data['project_contacts_text'] = $project_contacts;
                    if ($project->deadline) {
                        $data['project_end_date_attrs'] = [
                            'data-date-end-date' => $project->deadline,
                        ];
                    }
                    $dealname = $this->projects_model->getproject($project->id);
                    $data['dealname']              = $dealname->name;
                }
            }
        } 
        
        if($data) {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $data;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Record Found.';
        }
        echo $out =json_encode($outputArr);
        

    }

    public function getcontacts() {
        $data = array();
        $data = $this->staff_model->get('', ['active' => 1]);
        if($data) {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $data;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Record Found.';
        }
        echo $out =json_encode($outputArr);
    }

    public function getactivitytypes() {
        $data = array();
        $data = $this->tasktype_model->getTasktypes();
        if($data) {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $data;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Record Found.';
        }
        echo $out =json_encode($outputArr);
    }

    public function getsearchdeal()
    {
        
        if ($_POST) {
            $type = $_POST['type'];
            $CI = & get_instance();
            $q  = '';
        // echo $_POST['q']; exit;
            if ($_POST['q']) {
                $q = $_POST['q'];
                $q = trim($q);
            }
            if ($_POST['rel_id']) {
                $rel_id = $_POST['rel_id'];
            } else {
                $rel_id = '';
            }
            $data = [];
            if ($type == 'customer' || $type == 'customers') {
                $where_clients = '';

                if ($q) {
                    $where_clients .= '(company LIKE "%' . $q . '%" OR CONCAT(firstname, " ", lastname) LIKE "%' . $q . '%" OR email LIKE "%' . $q . '%") AND '.db_prefix().'clients.active = 1';
                }

                $data = $CI->clients_model->get($rel_id, $where_clients);
            } elseif ($type == 'contact' || $type == 'contacts') {
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
            } elseif ($type == 'project') {
                if ($rel_id != '') {
                    $CI->load->model('projects_model');
                    $data = $CI->projects_model->get($rel_id);
                } else {
                    $where_projects = '';
                    if ($CI->input->post('customer_id')) {
                        $where_projects .= 'clientid=' . $CI->input->post('customer_id');
                    }
                    $search = $CI->misc_model->_search_projects($q, 0, $where_projects);
                    $data   = $search['result'];
                }
            } elseif ($type == 'staff') {
                if ($rel_id != '') {
                    $CI->load->model('staff_model');
                    $data = $CI->staff_model->get($rel_id);
                } else {
                    $search = $CI->misc_model->_search_staff($q);
                    $data   = $search['result'];
                }
            } elseif ($type == 'tasks' || $type == 'task') {
                // Tasks only have relation with custom fields when searching on top
                if ($rel_id != '') {
                    $data = $CI->tasks_model->get($rel_id);
                }
            }


            

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


            if($_data) {
                $outputArr["status_code"] = 200;
                $outputArr["status"] = true;
                $outputArr["response"] = $_data;
            } else {
                $outputArr["status_code"] = 400;
                $outputArr["status"] = false;
                $outputArr["error_message"] = 'No Record Found.';
            }
            echo $out =json_encode($outputArr);
            die;
        }
    }

    public function get_org_person_bydeal()
    {
        
        if ($_POST) {
            
            $id = $_POST['id'];
            $task_id = $_POST['taskid'];
            $selected_milestone = '';
            if ($task_id != '' && $task_id != 'undefined') {
                $task               = $this->tasks_model->get($task_id);
                $selected_milestone = $task->milestone;
            }
            
            $data['allow_to_view_tasks'] = 0;
            $this->db->where('project_id', $id);
            $this->db->where('name', 'view_tasks');
            $project_settings = $this->db->get(db_prefix() . 'project_settings')->row();
            if ($project_settings) {
                $data['allow_to_view_tasks'] = $project_settings->value;
            }
            
            $data['deadline'] = get_project_deadline($id);
            $data['deadline_formatted']  = $data['deadline'] ? _d($data['deadline']) : null;
            $data['project_contacts'] = $this->projects_model->get_project_contacts($id);
            $project_details = $this->projects_model->getproject($id);
            $data['project_contacts_text'] = $project_details;
            $data['project_company_text'] = $project_details->client_data->company;
            $data['billing_type'] = get_project_billing_type($id);
            
/*
            echo json_encode([
                'deadline'            => $deadline,
                'deadline_formatted'  => $deadline ? _d($deadline) : null,
                'allow_to_view_tasks' => $allow_to_view_tasks,
                'project_details'     => $project_details,
                'project_contacts'     => $project_contacts,
                'project_contacts_text'     => $project_contacts_text,
                'project_company_text'     => $project_company_text,
                'billing_type'        => get_project_billing_type($id),
                'milestones'          => render_select('milestone', $this->projects_model->get_milestones($id), [
                    'id',
                    'name',
                ], 'task_milestone', $selected_milestone),
            ]);
*/
            if($data) {
                $outputArr["status_code"] = 200;
                $outputArr["status"] = true;
                $outputArr["response"] = $data;
            } else {
                $outputArr["status_code"] = 400;
                $outputArr["status"] = false;
                $outputArr["error_message"] = 'No Record Found.';
            }
            echo $out =json_encode($outputArr);
            die;
        }
    
    }

    public function createdeal()
    {
        // [name] => Deal 
        // [clientid] => 103
        // [project_contacts] => Array
        //     (
        //         [0] => 2
        //     )
    
        // [progress_from_tasks] => on
        // [pipeline_id] => 6
        // [status] => 5
        // [progress] => 0
        // [teamleader] => 3
        // [project_members] => Array
        //     (
        //         [0] => 4
        //     )
    
        // [project_cost] => 100
        // [start_date] => 08-10-2020
        // [deadline] => 15-10-2020
        // [tags] => sales,marketing,Software Engineer
        // [description] => Test deal description
        // [settings] => Array
        //     (
        //         [available_features] => Array
        //             (
        //                 [0] => project_overview
        //             )
    
        //     )
        if ($_POST) {
            //pre($_POST);
            $data = $project_contacts = $_POST;
            $id = $_POST['id'];
            if($id) {
                if(isset($data['project_contacts'])){
                    unset($data['project_contacts']);
                }
                if(isset($data['description'])){
                    $data['description'] = $_POST['description'];
                }

                if (!has_permission('projects', '', 'create')) {
                    access_denied('Projects');
                }
                if (!array_key_exists("project_members",$data))
                {
                    $data['project_members'] = $data['teamleader'];
                }
                unset($data['/api/createdeal']);
                
                $success = $this->projects_model->updatedeal_byapi($data, $id);
                if ($success) {
                    $this->projects_model->add_edit_contactsapi($project_contacts, $id);
                    $message = _l('updated_successfully', _l('project'));
                } else {
                    $message = _l('updated_successfully', _l('project'));
                }
            } else {
                
                if(isset($data['project_contacts'])){
                    unset($data['project_contacts']);
                }
        
                $data['description'] = $_POST['description'];

                if (!has_permission('projects', '', 'create')) {
                    access_denied('Projects');
                }
                if (!array_key_exists("project_members",$data))
                {
                    $data['project_members'] = $data['teamleader'];
                }
                unset($data['/api/createdeal']);
                //pr($data); exit;
                $id = $this->projects_model->add_dealfromapi($data);

                if ($id) {
                    $this->projects_model->add_edit_contactsapi($project_contacts, $id);
                    $message = _l('added_successfully', _l('project'));
                }
            }
           
        }
        if($message) {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["id"] = $id;
            $outputArr["response"] = $message;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'Cannot Create Deal.';
        }
        echo $out =json_encode($outputArr);
        die;
        
    }

    public function check_deal_exist() {
        $data = $_POST;
        $exist = $this->projects_model->check_deal_exist($data);
        if($exist) {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'Deal name already exist!, if you still want to create the deal you can ignore this message.';
        } else {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["id"] = $id;
            $outputArr["response"] = 'Deal name available.';
        }
        echo $out =json_encode($outputArr);
        die;
    }


    public function get_search_organisation()
    {
        if ($_POST) {
            $type = $_POST['type'];
            $data = get_relation_data($type);
            if ($_POST['rel_id']) {
                $rel_id = $_POST['rel_id'];
            } else {
                $rel_id = '';
            }

            $relOptions = init_relation_options($data, $type, $rel_id);
            if($relOptions) {
                $outputArr["status_code"] = 200;
                $outputArr["status"] = true;
                $outputArr["response"] = $relOptions;
            } else {
                $outputArr["status_code"] = 400;
                $outputArr["status"] = false;
                $outputArr["error_message"] = 'No Record Found.';
            }
            echo $out =json_encode($outputArr);
            die;
        }
    }

    public function changepipeline()
	{
        $data = array();
        if ($_POST) {
            $pipeline = $_POST['pipeline'];
            $leaderlist = $this->pipeline_model->getPipelineTeamleaders($pipeline);
            $statuseslist = $this->pipeline_model->getPipelineleadstatus($pipeline);
            $data['statuses'] = $statuseslist;
            $data['teamleaders'] = $leaderlist;
        }
        if($data) {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $data;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Record Found.';
        }
        echo $out =json_encode($outputArr);
        die;
    }

	public function changepipelineteammembers()
	{
        $data = array();
        if ($_POST) {
            $teamleader = $_POST['leaderid'];
            $pipeline = $_POST['pipeline'];
            $memberslist = $this->pipeline_model->getTeammembersexceptowner($teamleader, $pipeline);
            $data['teammembers'] = $memberslist;
        }
        if($data) {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $data;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Record Found.';
        }
        echo $out =json_encode($outputArr);
        die;
    }

    public function getpipeline() {
        $data = array();
        $data['pipelines'] = $this->pipeline_model->getPipeline();
        if($data) {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $data;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Record Found.';
        }
        echo $out =json_encode($outputArr);
        die;
    }
    public function all_contacts()
    {
        $data = array();
        $where = '';
        $staffqry = '';
        if(@$_POST['q']) {
            $q = $_POST['q'];
            $where = 'WHERE 
            (
              convert(firstname USING utf8) LIKE "%'.$q.'%"
              OR convert(lastname USING utf8) LIKE "%'.$q.'%" 
              OR convert(email USING utf8) LIKE "%'.$q.'%" 
              OR convert(company USING utf8) LIKE "%'.$q.'%" 
              OR convert(tblcontacts.userids USING utf8) LIKE "%'.$q.'%" 
              OR convert(
                tblcontacts.phonenumber USING utf8
              ) LIKE "%'.$q.'%" 
              OR convert(title USING utf8) LIKE "%'.$q.'%" 
              OR convert(last_login USING utf8) LIKE "%'.$q.'%" 
              OR convert(tblcontacts.active USING utf8) LIKE "%'.$q.'%" 
              OR convert(tblcontacts.id USING utf8) LIKE "%'.$q.'%" 
              OR convert(tblcontacts.userid USING utf8) LIKE "%'.$q.'%" 
              OR convert(is_primary USING utf8) LIKE "%'.$q.'%" 
              OR convert(
                (
                  SELECT 
                    count(*) 
                  FROM 
                    tblcontacts c 
                  WHERE 
                    c.userid = tblcontacts.userid
                ) USING utf8
              ) LIKE "%'.$q.'%" 
              OR convert(
                tblclients.registration_confirmed USING utf8
              ) LIKE "%'.$q.'%"
            ) ';
        }
        if(!is_admin($this->staffid)) {
            $staffqry = '  
            tblcontacts.userid IN (
              select 
                userid 
              from 
                tblcontacts 
              where 
                id IN (
                  select 
                    contacts_id 
                  from 
                    tbltasks 
                  where 
                    id IN (
                      SELECT 
                        taskid 
                      FROM 
                        `tbltask_assigned` 
                      WHERE 
                        staffid = '.$this->staffid.'
                    ) 
                    and contacts_id > 0
                )
            )  ';
        }
        
        if($where != '' && $staffqry != '') {
            $where = $where.' AND '.$staffqry;
        } else {
            if($staffqry)
                $where = ' where '.$staffqry;
        }
        //echo $where; exit;
        $qry = 'SELECT SQL_CALC_FOUND_ROWS firstname, lastname, email, company, tblcontacts.userids as userids, tblcontacts.phonenumber as phonenumber, title, last_login, tblcontacts.active as active, ctable_0.value as cvalue_0 ,tblcontacts.id as id,tblcontacts.userid as userid,is_primary,(SELECT count(*) FROM tblcontacts c WHERE c.userid=tblcontacts.userid) as total_contacts,tblclients.registration_confirmed as registration_confirmed
        FROM tblcontacts
        LEFT JOIN tblclients ON tblclients.userid=tblcontacts.userid LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid AND ctable_0.fieldto="contacts" AND ctable_0.fieldid=7
        '.$where.' ORDER BY firstname ASC ';
        $totalcnt = $this->db->query($qry)->num_rows();
        if($_POST['page'] > 1)
            $limit = ' LIMIT '.(($_POST['page']-1) * 25).', 25 ';
        else
            $limit = ' LIMIT 0, 25 ';
        $qry =  $qry.$limit;
        $query = $this->db->query($qry);
        $result = $query->result_array();
        if($result) {
            $result['totalcnt'] = $totalcnt;
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $result;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Records Found.';
        }
        echo $out =json_encode($outputArr);
    }

    public function organisationlist()
    {
        $data = array();
        if($_POST) {
            $q = $_POST['q'];
            if($q) {
                $where = ' convert(tblclients.userid USING utf8) LIKE "%'.$q.'%" 
                OR convert(company USING utf8) LIKE "%'.$q.'%" 
                OR convert(firstname USING utf8) LIKE "%'.$q.'%" 
                OR convert(email USING utf8) LIKE "%'.$q.'%" 
                OR convert(
                tblclients.phonenumber USING utf8
                ) LIKE "%'.$q.'%" 
                OR convert(tblclients.active USING utf8) LIKE "%'.$q.'%" 
                OR convert(
                (
                    SELECT 
                    GROUP_CONCAT(name SEPARATOR ",") 
                    FROM 
                    tblcustomer_groups 
                    JOIN tblcustomers_groups ON tblcustomer_groups.groupid = tblcustomers_groups.id 
                    WHERE 
                    customer_id = tblclients.userid 
                    ORDER by 
                    name ASC
                ) USING utf8
                ) LIKE "%'.$q.'%" 
                OR convert(
                tblclients.datecreated USING utf8
                ) LIKE "%'.$q.'%" 
                OR convert(ctable_0.value USING utf8) LIKE "%'.$q.'%" 
                OR convert(ctable_1.value USING utf8) LIKE "%'.$q.'%" 
                OR convert(tblcontacts.id USING utf8) LIKE "%'.$q.'%" 
                OR convert(lastname USING utf8) LIKE "%'.$q.'%" 
                OR convert(tblclients.zip USING utf8) LIKE "%'.$q.'%" 
                OR convert(
                registration_confirmed USING utf8
                ) LIKE "%'.$q.'%"
            ) 
            AND ( ';
            }
        }
        if(!is_admin($this->staffid)) {
            $staffqry = 'AND tblclients.userid IN (
                SELECT 
                  userid 
                FROM 
                  tblcontacts 
                WHERE 
                  email =(
                    SELECT 
                      email 
                    FROM 
                      tblstaff 
                    WHERE 
                      staffid = '.$this->staffid.'
                  )
              ) ';
        }
        $qry = 'SELECT 
        SQL_CALC_FOUND_ROWS 1, 
        tblclients.userid as userid, 
        company, 
        firstname, 
        email, 
        tblclients.phonenumber as phonenumber, 
        `tblclients`.`active` AS `active`, 
        (
          SELECT 
            GROUP_CONCAT(name SEPARATOR ",") 
          FROM 
            tblcustomer_groups 
            JOIN tblcustomers_groups ON tblcustomer_groups.groupid = tblcustomers_groups.id 
          WHERE 
            customer_id = tblclients.userid 
          ORDER by 
            name ASC
        ) as customerGroups, 
        tblclients.datecreated as datecreated, 
        ctable_0.value as cvalue_0, 
        ctable_1.value as cvalue_1, 
        tblcontacts.id as contact_id, 
        lastname, 
        tblclients.zip as zip, 
        registration_confirmed 
      FROM 
        tblclients 
        LEFT JOIN tblcontacts ON tblcontacts.userid = tblclients.userid 
        AND tblcontacts.is_primary = 1 
        LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblclients.userid = ctable_0.relid 
        AND ctable_0.fieldto = "customers" 
        AND ctable_0.fieldid = 6 
        LEFT JOIN tblcustomfieldsvalues as ctable_1 ON tblclients.userid = ctable_1.relid 
        AND ctable_1.fieldto = "customers" 
        AND ctable_1.fieldid = 8 
      WHERE 
        ('.$where.'
          (tblclients.active = 1 
          OR tblclients.active = 0) 
        ) '.$staffqry.' 
      ORDER BY 
        company ASC ';
        
      $totalcnt = $this->db->query($qry)->num_rows();
    //   if($_POST['page'] > 1)
    //       $limit = ' LIMIT '.(($_POST['page']-1) * 25).', 25 ';
    //   else
    //       $limit = ' LIMIT 0, 25 ';
      //$qry =  $qry.$limit;
      $query = $this->db->query($qry);
      $result = $query->result_array();
      if($result) {
          $result['totalcnt'] = $totalcnt;
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $result;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Records Found.';
        }
        echo $out =json_encode($outputArr);
    }

    public function add_contact()
    {
        //     [/admin/clients/form_contact/undefined/] => 
    // [contactid] => 
    // [firstname] => Sathya
    // [title] => Manager
    // [email] => sathya1986@gmail.com
    // [alternative_emails] => Array
    //     (
    //         [0] => sathiyanarayanan@techmango.net
    //     )

    // [phonenumber] => 9876543210
    // [alternative_phonenumber] => Array
    //     (
    //         [0] => 6738921033
    //     )

    // [clientid] => 103
    // [custom_fields] => Array
    //     (
    //         [contacts] => Array
    //             (
    //                 [7] => CF
    //             )

    //     )
        $customer_id = '';
        
        
        $result = array();
        if ($_POST) {
            $contact_id             = $_POST['id'];
            $customer_id = $_POST['clientid'];
            
            //$data['customer_id'] = $customer_id;
            //$data['contactid']   = $contact_id;
            $data['password'] = $_POST['password'];
            $data['project_id'] = $_POST['project_id'];
            $data['alternative_emails'] = $_POST['alternative_emails'];
            $data['alternative_phonenumber'] = $_POST['alternative_phonenumber'];
            $data['email'] = $_POST['email'];
            $data['phonenumber'] = $_POST['phonenumber'];
            $data['title'] = $_POST['title'];
            $data['firstname'] = $_POST['firstname'];
            
            
            if ($contact_id == '') {
               
                $data['userid'] = $data['userids'] = $_POST['clientid'];
                // if(isset($data['clientid'])) {
                //     if(is_array($data['clientid']) && count($data['clientid']) > 0){
                //         $data['userids'] = implode(',', $data['clientid']);
                //          $data['userid'] =  isset($data['clientid'][0])?$data['clientid'][0]:0;
                //     }
                //     unset($data['clientid']);
                // }
                
                $id      = $this->clients_model->add_contact_fromapi($data, $customer_id);
                
                $message = $card = '';
                $success = false;
                
                if ($id) {
                    handle_contact_profile_image_upload($id);
                    $success = true;
                    $result['message'] = _l('added_successfully', _l('contact'));

                    //Assign Deals
                    if (isset($data['project_id'])) {
                        foreach($data['project_id'] as $val) {
                            $this->db->insert(db_prefix() . 'project_contacts', [
                                'project_id' => $val,
                                'contacts_id'   => $id,
                            ]);
                        }
                    }

                    
                } else {
                    $err = 'Cannot Update Contact Person.';
                }
                
            } else {
                if($contact_id) {
                    // assign Deals
                    $data['project_id'] = $_POST['project_id'];
                    if (isset($data['project_id'])) {
                        $deals = $data['project_id'];
                        unset($data['project_id']);
                    }
                    // pr($deals);
                    // pr($_POST);
                    // pre($data);
                    if (isset($deals)) {
                        $this->db->where('contacts_id', $contact_id);
                        $this->db->delete(db_prefix() . 'project_contacts');
                        foreach($deals as $val) {
                            $this->db->insert(db_prefix() . 'project_contacts', [
                                'project_id' => $val,
                                'contacts_id'   => $contact_id,
                            ]);
                        }
                    }
                    
                }
                $data['userid'] = $data['userids'] = $_POST['clientid'];
                $original_contact = $this->clients_model->get_contact($contact_id);
               
                unset($data['clientid']);
                //  if(isset($data['clientid'])) {
                //         if(is_array($data['clientid']) && count($data['clientid']) > 0){
                //             $data['userids'] = implode(',', $data['clientid']);
                //             $data['userid'] =  isset($data['clientid'][0])?$data['clientid'][0]:0;
                //         }
                //         unset($data['clientid']);
                //     }
                $success          = $this->clients_model->update_contactfromapi($data, $contact_id);
                $message          = '';
                if ($success == true) {
                    $updated = true;
                    $message = _l('updated_successfully', _l('contact'));
                } else {
                    $err = 'Cannot Update Contact Person.';
                }
               
                if (handle_contact_profile_image_upload($contact_id) && !$updated) {
                    $message = _l('updated_successfully', _l('contact'));
                    $success = true;
                }
                
            }
            
        } 
        if($message) {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["id"] = $id;
            $outputArr["response"] = $message;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = $err;
        }
        echo $out =json_encode($outputArr);
        
    }

    public function getcontactsdeals() {
        $qry = '';
        if($_POST['id']) {
            $qry = 'SELECT
            SQL_CALC_FOUND_ROWS name,
            CASE
               company 
               WHEN
                  "" 
               THEN
         (
                  SELECT
                     CONCAT(firstname, " ", lastname) 
                  FROM
                     tblcontacts 
                  WHERE
                     userid = tblclients.userid 
                     and is_primary = 1) 
                  ELSE
                     company 
            END
            as company,
            (
               SELECT
                  GROUP_CONCAT(name SEPARATOR ", ") 
               FROM
                  tbltaggables 
                  JOIN
                     tbltags 
                     ON tbltaggables.tag_id = tbltags.id 
               WHERE
                  rel_id = tblprojects.id 
                  and rel_type = "project" 
               ORDER by
                  tag_order ASC
            )
            as tags,
            start_date,
            deadline,
            status,
            (
               SELECT
                  GROUP_CONCAT(CONCAT(firstname, " ", lastname) SEPARATOR ", ") 
               FROM
                  tblproject_members 
                  JOIN
                     tblstaff 
                     on tblstaff.staffid = tblproject_members.staff_id 
               WHERE
                  project_id = tblprojects.id 
               ORDER BY
                  staff_id
            )
            as members,
            (
               SELECT
                  name 
               FROM
                  tblpipeline 
               WHERE
                  id = tblprojects.pipeline_id
            )
            as pipeline_name,
            ctable_0.value as cvalue_contacts_person_cf,
            ctable_1.value as cvalue_customers_type,
            clientid,
            (
               SELECT
                  GROUP_CONCAT(staff_id SEPARATOR ", ") 
               FROM
                  tblproject_members 
               WHERE
                  project_id = tblprojects.id 
               ORDER BY
                  staff_id
            )
            as members_ids,
            tblprojects.id as id 
                  FROM
                     tblprojects 
                     LEFT JOIN
                        tblclients 
                        ON tblclients.userid = tblprojects.clientid 
                     LEFT JOIN
                        tblcustomfieldsvalues as ctable_0 
                        ON tblclients.userid = ctable_0.relid 
                        AND ctable_0.fieldto = "customers" 
                        AND ctable_0.fieldid = 6 
                     LEFT JOIN
                        tblcustomfieldsvalues as ctable_1 
                        ON tblclients.userid = ctable_1.relid 
                        AND ctable_1.fieldto = "customers" 
                        AND ctable_1.fieldid = 8 
                  WHERE
                     tblprojects.id IN 
                     (
                        SELECT
                           project_id 
                        FROM
                           tblproject_contacts 
                        WHERE
                           contacts_id = "'.$_POST['id'].'"
                     )
                     AND 
                     (
                        status IN 
                        (
                           6,
                           3,
                           1,
                           7,
                           8,
                           5
                        )
                     )
                     AND 
                     (
                        status IN 
                        (
                           6,
                           3,
                           1,
                           7,
                           8,
                           5
                        )
                     )
                     AND tblprojects.deleted_status = 0 
                  ORDER BY
         (
                     SELECT
                        GROUP_CONCAT(CONCAT(firstname, " ", lastname) SEPARATOR ", ") 
                     FROM
                        tblproject_members 
                        JOIN
                           tblstaff 
                           on tblstaff.staffid = tblproject_members.staff_id 
                     WHERE
                        project_id = tblprojects.id 
                     ORDER BY
                        staff_id) ASC ';
        }
        // if($_POST['page'] > 1)
        //     $limit = ' LIMIT '.(($_POST['page']-1) * 25).', 25 ';
        // else
        //     $limit = ' LIMIT 0, 25 ';
        // $qry =  $qry.$limit;
        $query = $this->db->query($qry);
        $result = $query->result_array();
        if($result) {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $result;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Records Found.';
        }
        echo $out =json_encode($outputArr);
    }

    public function getcontactsorg() {
        $qry = '';
        if($_POST['id']) {
            $qry = 'SELECT
            tblclients.*
                  FROM
                     tblprojects 
                     LEFT JOIN
                        tblclients 
                        ON tblclients.userid = tblprojects.clientid 
                     LEFT JOIN
                        tblcustomfieldsvalues as ctable_0 
                        ON tblclients.userid = ctable_0.relid 
                        AND ctable_0.fieldto = "customers" 
                        AND ctable_0.fieldid = 6 
                     LEFT JOIN
                        tblcustomfieldsvalues as ctable_1 
                        ON tblclients.userid = ctable_1.relid 
                        AND ctable_1.fieldto = "customers" 
                        AND ctable_1.fieldid = 8 
                  WHERE
                     tblprojects.id IN 
                     (
                        SELECT
                           project_id 
                        FROM
                           tblproject_contacts 
                        WHERE
                           contacts_id = "'.$_POST['id'].'"
                     )
                     AND 
                     (
                        status IN 
                        (
                           6,
                           3,
                           1,
                           7,
                           8,
                           5
                        )
                     )
                     AND 
                     (
                        status IN 
                        (
                           6,
                           3,
                           1,
                           7,
                           8,
                           5
                        )
                     )
                     AND tblprojects.deleted_status = 0 
                     GROUP BY tblclients.userid 
                  ORDER BY
         (
                     SELECT
                        GROUP_CONCAT(CONCAT(firstname, " ", lastname) SEPARATOR ", ") 
                     FROM
                        tblproject_members 
                        JOIN
                           tblstaff 
                           on tblstaff.staffid = tblproject_members.staff_id 
                     WHERE
                        project_id = tblprojects.id 
                     ORDER BY
                        staff_id) ASC ';
        }
        if($_POST['page'] > 1)
            $limit = ' LIMIT '.(($_POST['page']-1) * 25).', 25 ';
        else
            $limit = ' LIMIT 0, 25 ';
        $qry =  $qry.$limit;
        $query = $this->db->query($qry);
        $result = $query->result_array();
        if($result) {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $result;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Records Found.';
        }
        echo $out =json_encode($outputArr);
    }

    public function getorgcontacts() {
        $qry = '';
        if($_POST['id']) {
            $qry = 'SELECT
            SQL_CALC_FOUND_ROWS CONCAT(firstname, " ", lastname) as full_name,
            email,
            title,
            phonenumber,
            active,
            last_login,
            ctable_0.value as cvalue_0,
            tblcontacts.id as id,
            userid,
            is_primary 
         FROM
            tblcontacts 
            LEFT JOIN
               tblcustomfieldsvalues as ctable_0 
               ON tblcontacts.id = ctable_0.relid 
               AND ctable_0.fieldto = "contacts" 
               AND ctable_0.fieldid = 7 
         WHERE
            (
               tblcontacts.userid = "'.$_POST['id'].'" 
               or FIND_IN_SET("'.$_POST['id'].'", tblcontacts.userids) 
            )
            AND tblcontacts.deleted_status = 0 
         ORDER BY
            CONCAT(firstname, " ", lastname) ASC';
        }
        if($_POST['page'] > 1)
            $limit = ' LIMIT '.(($_POST['page']-1) * 25).', 25 ';
        else
            $limit = ' LIMIT 0, 25 ';
        $qry =  $qry.$limit;
        $query = $this->db->query($qry);
        $result = $query->result_array();
        if($result) {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $result;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Records Found.';
        }
        echo $out =json_encode($outputArr);
    }

    public function getcontactsactivity() {
        $qry = '';
        if(!is_admin($this->staffid)) {
            $staffqry = 'AND tbltasks.rel_id IN 
            (
               SELECT
                  tblprojects.id 
               FROM
                  tblprojects 
                  join
                     tblproject_members 
                     on tblproject_members.project_id = tblprojects.id 
               WHERE
                  tblproject_members.staff_id in 
                  (
                     '.$this->staffid.'
                  )
                  OR tblprojects.teamleader in 
                  (
                     '.$this->staffid.'
                  )
            )';
        }
        if($_POST['id']) {
            $qry = 'SELECT
            SQL_CALC_FOUND_ROWS tbltasks.id as id,
            tblclients.userid as userid,
            tbltasks.id as id,
            tbltasks.name as task_name,
            tblprojects.name as project_name,
            (
               SELECT
                  GROUP_CONCAT(CONCAT(firstname, " ", lastname) SEPARATOR ", ") 
               FROM
                  tbltask_assigned 
                  JOIN
                     tblstaff 
                     ON tblstaff.staffid = tbltask_assigned.staffid 
               WHERE
                  taskid = tbltasks.id 
               ORDER BY
                  tbltask_assigned.staffid
            )
            as assignees,
            startdate,
            tbltasks.status as status,
            tbltasktype.name as tasktype,
            tblprojects.id as projectid,
            tblprojects.teamleader as p_teamleader,
            tblcontacts.id as contactsid,
            billed,
            recurring,
            (
               SELECT
                  staffid 
               FROM
                  tbltask_assigned 
               WHERE
                  taskid = tbltasks.id 
                  AND staffid = '.$this->staffid.'
            )
            as is_assigned,
            (
               SELECT
                  GROUP_CONCAT(staffid SEPARATOR ", ") 
               FROM
                  tbltask_assigned 
               WHERE
                  taskid = tbltasks.id 
               ORDER BY
                  tbltask_assigned.staffid
            )
            as assignees_ids,
            (
               SELECT
                  MAX(id) 
               FROM
                  tbltaskstimers 
               WHERE
                  task_id = tbltasks.id 
                  and staff_id = '.$this->staffid.' 
                  and end_time IS NULL
            )
            as not_finished_timer_by_current_staff,
            (
               SELECT
                  staffid 
               FROM
                  tbltask_assigned 
               WHERE
                  taskid = tbltasks.id 
                  AND staffid = '.$this->staffid.'
            )
            as current_user_is_assigned,
            (
               SELECT
                  CASE
                     WHEN
                        tbltasks.addedfrom = '.$this->staffid.' 
                        AND is_added_from_contact = 0 
                     THEN
                        1 
                     ELSE
                        0 
                  END
            )
            as current_user_is_creator 
         FROM
            tbltasks 
            LEFT JOIN
               tbltasktype as tbltasktype 
               ON tbltasktype.id = tbltasks.tasktype 
            LEFT JOIN
               tblprojects as tblprojects 
               ON tblprojects.id = tbltasks.rel_id 
               AND tbltasks.rel_type = "project" 
            LEFT JOIN
               tblprojects_status as tblprojects_status 
               ON tblprojects_status.id = tblprojects.status 
            LEFT JOIN
               tblclients as tblclients 
               ON tblclients.userid = tblprojects.clientid 
            LEFT JOIN
               tblcontacts as tblcontacts 
               ON tblcontacts.id = tbltasks.contacts_id 
         WHERE
            rel_type = "project" 
            AND 
            (
               contacts_id IN 
               (
                "'.$_POST['id'].'"
               )
            )'.$staffqry;

            $totalcnt = $this->db->query($qry)->num_rows();
            if($_POST['page'] > 1)
                $limit = ' LIMIT '.(($_POST['page']-1) * 25).', 25 ';
            else
                $limit = ' LIMIT 0, 25 ';
            $qry =  $qry.$limit;
            $query = $this->db->query($qry);
            $result = $query->result_array();
            if($result) {
                $result['totalcnt'] = $totalcnt;
                $outputArr["status_code"] = 200;
                $outputArr["status"] = true;
                $outputArr["response"] = $result;
            } else {
                $outputArr["status_code"] = 400;
                $outputArr["status"] = false;
                $outputArr["error_message"] = 'No Records Found.';
            }
            echo $out =json_encode($outputArr);
        }
    }

    public function getcustomfields() {
        $customfields = array();
        if($_POST) {
            $field = $_POST['custom_fields'];
            $customfields = get_custom_fields($field,array('show_on_table'=>1));
        }
        if($customfields) {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $customfields;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Records Found.';
        }
        echo $out =json_encode($outputArr);
    }

    public function getcountries() {
        $mysql_qry = 'select country_id, short_name from tblcountries';
        $query = $this->db->query($mysql_qry);
        $data = $query->result_array();
        if($data) {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $data;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Records Found.';
        }
        echo $out =json_encode($outputArr);
    }

    public function getpriorities() {
        $mysql_qry = 'select * from tblprojects_status';
        $query = $this->db->query($mysql_qry);
        $data = $query->result_array();
        if($data) {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $data;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Records Found.';
        }
        echo $out =json_encode($outputArr);
    }

    public function addclient_byapi()
    {

        // [company] => test
        // [vat] => test12345
        // [phonenumber] => 9485739480
        // [website] => www.test.com
        // [groups_in] => Array
        //     (
        //         [0] => 2
        //     )

        // [address] => test address
        // [city] => madurai
        // [state] => tamilnadu
        // [zip] => 625011
        // [country] => 102
        // [custom_fields] => Array
        //     (
        //         [customers] => Array
        //             (
        //                 [6] => test
        //                 [8] => Array
        //                     (
        //                         [0] => cfk_hidden
        //                         [1] => cfk_hidden
        //                         [2] => cfk_hidden
        //                     )

        //                 [9] => testadde
        //             )

        //     )

        // [billing_street] => 
        // [billing_city] => 
        // [billing_state] => 
        // [billing_zip] => 
        // [billing_country] => 
        // [shipping_street] => 
        // [shipping_city] => 
        // [shipping_state] => 
        // [shipping_zip] => 
        // [shipping_country] =>
        
        $result = array();
        //pre($_POST); 
        if ($_POST) {
            $data = $_POST;
            unset($data['/api/addclient_byapi']);
            $data['billing_street'] = ''; 
            $data['billing_city'] = '';
            $data['billing_state'] = '';
            $data['billing_zip'] = '';
            $data['billing_country'] = ''; 
            $data['shipping_street'] = ''; 
            $data['shipping_city'] = ''; 
            $data['shipping_state'] = '';
            $data['shipping_zip'] = ''; 
            $data['shipping_country'] = '';
            
            $save_and_add_contact = false;
            if (isset($data['save_and_add_contact'])) {
                unset($data['save_and_add_contact']);
                $save_and_add_contact = true;
            }
            
            if($data['id']) {
                $id = $data['id'];
                $success = $this->clients_model->update_orgbyapi($data, $id);
                if ($success == true) {
                    $result['success'] = _l('updated_successfully', _l('client'));
                }
            } else {
               $id = $this->clients_model->addorganisation_byapi($data);
                if (!has_permission('customers', '', 'view')) {
                    $assign['customer_admins']   = [];
                    $assign['customer_admins'][] = get_staff_user_id();
                    $this->clients_model->assign_admins($assign, $id);
                }
                if ($id) {
                    $result['success'] = _l('added_successfully', _l('client'));
                }
            }
        }
        if($result) {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["id"] = $id;
            $outputArr["response"] = $result;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'Cannot add Organization.';
        }
        echo $out =json_encode($outputArr);

    }

    public function getcontactDetails() {
        $data = $this->clients_model->get_contact($_POST['id']);
        
        if($data) {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $data;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Records Found.';
        }
        echo $out =json_encode($outputArr);
    }

    public function getstaff_details()
    {
        $this->load->model('departments_model');
        $this->load->model('staff_model');
        $id = $_POST['id'];
        $member = $this->staff_model->getstaffdetails($id);
        
        
        $data['member']            = $member;
        $title                     = $member->firstname . ' ' . $member->lastname;
        $data['staff_departments'] = $this->departments_model->get_staff_departments($member->staffid);

        $ts_filter_data = [];
        if ($this->input->get('filter')) {
            if ($this->input->get('range') != 'period') {
                $ts_filter_data[$this->input->get('range')] = true;
            } else {
                $ts_filter_data['period-from'] = $this->input->get('period-from');
                $ts_filter_data['period-to']   = $this->input->get('period-to');
            }
        } else {
            $ts_filter_data['this_month'] = true;
        }
        
        $data['logged_time'] = $this->staff_model->get_logged_time_data($id, $ts_filter_data);
        $data['timesheets']  = $data['logged_time']['timesheets'];
        $data['roles']         = $this->roles_model->get();
        $data['designations']         = $this->designation_model->get();
        $data['member_reporting_to']     = $this->staff_model->get('', ['active' => 1,'role !=' => 3,'staffid !=' => $id]);
        $data['member_action_for']         = array(array('text'=>'Active'),array('text'=>'Blocked'),array('text'=>'Deactivate'),array('text'=>'Vacant'));
        $data['user_notes']    = $this->misc_model->get_notes($id, 'staff');
        $data['departments']   = $this->db->get(db_prefix() . 'departments')->result_array();
        $data['title']         = $title;
        if($data) {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $data;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Records Found.';
        }
        echo $out =json_encode($outputArr);
    }

    public function getorgdeals() {
        $qry = '';
        if(!is_admin($this->staffid)) { 
            $staffqry = 'AND 
            (
               tblprojects.id IN 
               (
                  SELECT
                     tblprojects.id 
                  FROM
                     tblprojects 
                     join
                        tblproject_members 
                        on tblproject_members.project_id = tblprojects.id 
                  WHERE
                     tblproject_members.staff_id in 
                     (
                        '.$this->staffid.'
                     )
               )
               OR tblprojects.teamleader in 
               (
                '.$this->staffid.'
               )
            )';
        }
        if($_POST['id']) {
            $qry = 'SELECT
            SQL_CALC_FOUND_ROWS name,
            CASE
               company 
               WHEN
                  "" 
               THEN
         (
                  SELECT
                     CONCAT(firstname, " ", lastname) 
                  FROM
                     tblcontacts 
                  WHERE
                     userid = tblclients.userid 
                     and is_primary = 1) 
                  ELSE
                     company 
            END
            as company,
            (
               SELECT
                  GROUP_CONCAT(name SEPARATOR ", ") 
               FROM
                  tbltaggables 
                  JOIN
                     tbltags 
                     ON tbltaggables.tag_id = tbltags.id 
               WHERE
                  rel_id = tblprojects.id 
                  and rel_type = "project" 
               ORDER by
                  tag_order ASC
            )
            as tags,
            start_date,
            deadline,
            status,
            (
               SELECT
                  GROUP_CONCAT(CONCAT(firstname, " ", lastname) SEPARATOR ", ") 
               FROM
                  tblproject_members 
                  JOIN
                     tblstaff 
                     on tblstaff.staffid = tblproject_members.staff_id 
               WHERE
                  project_id = tblprojects.id 
               ORDER BY
                  staff_id
            )
            as members,
            (
               SELECT
                  name 
               FROM
                  tblpipeline 
               WHERE
                  id = tblprojects.pipeline_id
            )
            as pipeline_name,
            ctable_0.value as cvalue_contacts_person_cf,
            ctable_1.value as cvalue_customers_type,
            status,
            clientid,
            (
               SELECT
                  GROUP_CONCAT(staff_id SEPARATOR ", ") 
               FROM
                  tblproject_members 
               WHERE
                  project_id = tblprojects.id 
               ORDER BY
                  staff_id
            )
            as members_ids,
            tblprojects.id as id 
                  FROM
                     tblprojects 
                     LEFT JOIN
                        tblclients 
                        ON tblclients.userid = tblprojects.clientid 
                     LEFT JOIN
                        tblcustomfieldsvalues as ctable_0 
                        ON tblclients.userid = ctable_0.relid 
                        AND ctable_0.fieldto = "customers" 
                        AND ctable_0.fieldid = 6 
                     LEFT JOIN
                        tblcustomfieldsvalues as ctable_1 
                        ON tblclients.userid = ctable_1.relid 
                        AND ctable_1.fieldto = "customers" 
                        AND ctable_1.fieldid = 8 
                  WHERE
                     clientid = "'.$_POST['id'].'" 
                     '.$staffqry.'
                     AND tblprojects.deleted_status = 0 
                  ORDER BY
         (
                     SELECT
                        GROUP_CONCAT(CONCAT(firstname, " ", lastname) SEPARATOR ", ") 
                     FROM
                        tblproject_members 
                        JOIN
                           tblstaff 
                           on tblstaff.staffid = tblproject_members.staff_id 
                     WHERE
                        project_id = tblprojects.id 
                     ORDER BY
                        staff_id) ASC '.$staffqry;
            
            $totalcnt = $this->db->query($qry)->num_rows();
            // if($_POST['page'] > 1)
            //     $limit = ' LIMIT '.(($_POST['page']-1) * 25).', 25 ';
            // else
            //     $limit = ' LIMIT 0, 25 ';
            // $qry =  $qry.$limit;
            $query = $this->db->query($qry);
            $result = $query->result_array();
            if($result) {
                $result['totalcnt'] = $totalcnt;
                $outputArr["status_code"] = 200;
                $outputArr["status"] = true;
                $outputArr["response"] = $result;
            } else {
                $outputArr["status_code"] = 400;
                $outputArr["status"] = false;
                $outputArr["error_message"] = 'No Records Found.';
            }
            echo $out =json_encode($outputArr);
        }
    }

    public function getorgactivity() {
        $qry = '';
        if(!is_admin($this->staffid)) {
            $staffqry = 'AND tbltasks.rel_id IN 
            (
               SELECT
                  tblprojects.id 
               FROM
                  tblprojects 
                  join
                     tblproject_members 
                     on tblproject_members.project_id = tblprojects.id 
               WHERE
                  tblproject_members.staff_id in 
                  (
                     '.$this->staffid.'
                  )
                  OR tblprojects.teamleader in 
                  (
                     '.$this->staffid.'
                  )
            )';
        }
        if($_POST['id']) {
            $qry = 'SELECT
            SQL_CALC_FOUND_ROWS tbltasks.id as id,
            tblclients.userid as userid,
            tbltasks.id as id,
            tbltasks.name as task_name,
            tblprojects.name as project_name,
            (
               SELECT
                  GROUP_CONCAT(CONCAT(firstname, " ", lastname) SEPARATOR ", ") 
               FROM
                  tbltask_assigned 
                  JOIN
                     tblstaff 
                     ON tblstaff.staffid = tbltask_assigned.staffid 
               WHERE
                  taskid = tbltasks.id 
               ORDER BY
                  tbltask_assigned.staffid
            )
            as assignees,
            startdate,
            tbltasks.status as status,
            tbltasktype.name as tasktype,
            tblprojects.id as projectid,
            tblprojects.teamleader as p_teamleader,
            tblcontacts.id as contactsid,
            billed,
            recurring,
            (
               SELECT
                  staffid 
               FROM
                  tbltask_assigned 
               WHERE
                  taskid = tbltasks.id 
                  AND staffid = '.$this->staffid.'
            )
            as is_assigned,
            (
               SELECT
                  GROUP_CONCAT(staffid SEPARATOR ", ") 
               FROM
                  tbltask_assigned 
               WHERE
                  taskid = tbltasks.id 
               ORDER BY
                  tbltask_assigned.staffid
            )
            as assignees_ids,
            (
               SELECT
                  MAX(id) 
               FROM
                  tbltaskstimers 
               WHERE
                  task_id = tbltasks.id 
                  and staff_id = '.$this->staffid.' 
                  and end_time IS NULL
            )
            as not_finished_timer_by_current_staff,
            (
               SELECT
                  staffid 
               FROM
                  tbltask_assigned 
               WHERE
                  taskid = tbltasks.id 
                  AND staffid = '.$this->staffid.'
            )
            as current_user_is_assigned,
            (
               SELECT
                  CASE
                     WHEN
                        tbltasks.addedfrom = '.$this->staffid.' 
                        AND is_added_from_contact = 0 
                     THEN
                        1 
                     ELSE
                        0 
                  END
            )
            as current_user_is_creator 
         FROM
            tbltasks 
            LEFT JOIN
               tbltasktype as tbltasktype 
               ON tbltasktype.id = tbltasks.tasktype 
            LEFT JOIN
               tblprojects as tblprojects 
               ON tblprojects.id = tbltasks.rel_id 
               AND tbltasks.rel_type = "project" 
            LEFT JOIN
               tblprojects_status as tblprojects_status 
               ON tblprojects_status.id = tblprojects.status 
            LEFT JOIN
               tblclients as tblclients 
               ON tblclients.userid = tblprojects.clientid 
            LEFT JOIN
               tblcontacts as tblcontacts 
               ON tblcontacts.id = tbltasks.contacts_id 
         WHERE
            rel_type = "project" 
            AND 
            (
                rel_id IN 
                (
                    SELECT
                        id 
                    FROM
                        tblprojects 
                    WHERE
                        clientid = "'.$_POST['id'].'"
                )
            )'.$staffqry;

            $totalcnt = $this->db->query($qry)->num_rows();
            if($_POST['page'] > 1)
                $limit = ' LIMIT '.(($_POST['page']-1) * 25).', 25 ';
            else
                $limit = ' LIMIT 0, 25 ';
            $qry =  $qry.$limit;
            $query = $this->db->query($qry);
            $result = $query->result_array();
            if($result) {
                $result['totalcnt'] = $totalcnt;
                $outputArr["status_code"] = 200;
                $outputArr["status"] = true;
                $outputArr["response"] = $result;
            } else {
                $outputArr["status_code"] = 400;
                $outputArr["status"] = false;
                $outputArr["error_message"] = 'No Records Found.';
            }
            echo $out =json_encode($outputArr);
        }
    }

    public function getroles()
    {
        $data['roles'] = $this->db->get(db_prefix() . 'roles')->result_array();
        
        if($data) {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $data;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Records Found.';
        }
        echo $out =json_encode($outputArr);
    }

    public function getdesignations()
    {
        $data['designations'] = $this->db->get(db_prefix() . 'designations')->result_array();
        
        if($data) {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $data;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Records Found.';
        }
        echo $out =json_encode($outputArr);
    }

    public function getorgbyid() {
        $data = array();
        if($_POST['id']) {
            $data['client'] = $this->clients_model->get($_POST['id']);
            $sql = 'SELECT id, (select name from tblcustomfields where id=fieldid) as fieldname, value, fieldto FROM tblcustomfieldsvalues where fieldto = "customers" AND relid = "'.$_POST['id'].'"';
            $query = $this->db->query($sql);
            $data['customfields'] = $query->result_array();
            if($data) {
                $outputArr["status_code"] = 200;
                $outputArr["status"] = true;
                $outputArr["response"] = $data;
            } else {
                $outputArr["status_code"] = 400;
                $outputArr["status"] = false;
                $outputArr["error_message"] = 'No Records Found.';
            }
            echo $out =json_encode($outputArr);
        }
    }

    public function getdealbyid()
    {
        $id = $_POST['id'];
        $data['project']                               = $this->projects_model->getproject($id);
        
        $data['project']->settings->available_features = unserialize($data['project']->settings->available_features);
        $data['contact_persons'] = $this->projects_model->get_project_contacts($id);
        $data['followers'] = $this->projects_model->get_project_members($id,(array)(isset($data['project'])?$data['project']:array()));
        $data['stages'] = $this->pipeline_model->getPipelineleadstatus((isset($data['project'])?$data['project']->pipeline_id:0));
        
        //echo "<pre>"; print_r($data); exit;
        if($data) {
            $outputArr["status_code"] = 200;
            $outputArr["status"] = true;
            $outputArr["response"] = $data;
        } else {
            $outputArr["status_code"] = 400;
            $outputArr["status"] = false;
            $outputArr["error_message"] = 'No Records Found.';
        }
        echo $out =json_encode($outputArr);
    }

}
