<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <?php if(isset($consent_purposes)) { ?>
            <div class="row mbot15">
              <div class="col-md-3 contacts-filter-column">
               <div class="select-placeholder">
                <select name="custom_view" title="<?php echo _l('gdpr_consent'); ?>" id="custom_view" class="selectpicker" data-width="100%">
                 <option value=""></option>
                 <?php foreach($consent_purposes as $purpose) { ?>
                 <option value="consent_<?php echo $purpose['id']; ?>">
                  <?php echo $purpose['name']; ?>
                </option>
                <?php } ?>
              </select>
            </div>
          </div>
        </div>
        <?php } ?>
        <div class="clearfix"></div>
          <?php 
              if(isset($_SESSION['alpha'])) {
                  if (($key = array_search($_REQUEST['a'], $_SESSION['alpha'])) !== FALSE) {
                    unset($_SESSION['alpha'][$key]);
                  } else {
					   if(isset($_REQUEST['a'])){
						$_SESSION['alpha'][] = $_REQUEST['a'];
					   }
                    //array_values($_SESSION['alpha']);
                  }
              } else {
				  if(isset($_REQUEST['a'])){
					$_SESSION['alpha'][] = $_REQUEST['a'];
				  }
              }
			   $alphabets =  array();
			   if(isset($_SESSION['alpha'])){
				$alphabets = array_filter($_SESSION['alpha']);
			   }

              //pre($alphabets);
              $likeqry = '';
              $alphaCnt = count($alphabets);
              $all = '';
              if($alphaCnt > 0) {
                  $i = 1;
                  foreach ($alphabets as $val) {
                    if (($key = array_search('All', $_SESSION['alpha'])) == FALSE) {
                          if($i < $alphaCnt)
                              $likeqry .= db_prefix()."contacts.firstname LIKE '".$val."%' OR ";
                          else
                              $likeqry .= db_prefix()."contacts.firstname LIKE '".$val."%'";
                          $i++;
                      } else {
                          $all = 1;
                      }
                  }
              }
              //echo $likeqry; exit;
              if($likeqry) {
                  $likeqry = ' AND ( '.$likeqry.' ) ';
              }
              //echo $likeqry; exit;

              if(!is_admin(get_staff_user_id())) {
                $CI          = & get_instance();
                $my_staffids = $CI->staff_model->get_my_staffids();
                if($my_staffids){
                    $where = ' WHERE ('.db_prefix().'contacts.addedfrom IN (' . implode(',',$my_staffids) . ') OR (' . db_prefix() . 'contacts.userid IN (SELECT ' . db_prefix() . 'projects.clientid FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id in (' . implode(',',$my_staffids) . ')  AND tblprojects.clientid != "")) OR  (' . db_prefix() . 'contacts.userid IN (SELECT ' . db_prefix() . 'projects.clientid FROM ' . db_prefix() . 'projects where ' . db_prefix() . 'projects.teamleader in (' . implode(',',$my_staffids) . ') AND tblprojects.clientid != "" )))   AND (tblcontacts.active=1 or tblcontacts.active=0)  AND tblcontacts.deleted_status=0 '.$likeqry;
                    $where_person_qry = 'SELECT  tblcontacts.firstname, tblcontacts.id, tblcontacts.email
                    FROM tblcontacts
                    LEFT JOIN tblclients ON tblclients.userid=tblcontacts.userid LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid AND ctable_0.fieldto="contacts" AND ctable_0.fieldid=7'.$where;
                    $list_person     = $CI->db->query($where_person_qry)->result_array();
                    
                } else {
                    $where_person_qry = 'SELECT  tblcontacts.firstname, tblcontacts.id, tblcontacts.email
                    FROM tblcontacts
                    LEFT JOIN tblclients ON tblclients.userid=tblcontacts.userid LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid AND ctable_0.fieldto="contacts" AND ctable_0.fieldid=7
                    
                    WHERE  ('.db_prefix().'contacts.addedfrom="'.get_staff_user_id().'" OR (tblcontacts.userid IN (select userid from tblclients where '.db_prefix().'clients.addedfrom="'.get_staff_user_id().'")) OR tblcontacts.id IN (select contacts_id from tblproject_contacts where project_id IN (select project_id from tblproject_members where staff_id = "'.get_staff_user_id().'") ) OR tblcontacts.id IN (select contacts_id from tblproject_contacts where project_id IN (select tblprojects.id from tblprojects where tblprojects.teamleader = "'.get_staff_user_id().'") ) )  AND (tblcontacts.active=1 or tblcontacts.active=0)  AND tblcontacts.deleted_status=0 '.$likeqry;
                    $CI          = & get_instance();
                    $list_person     = $CI->db->query($where_person_qry)->result_array();

                }
                   
              }
//pre($client);
          ?>
        <div class="inline-block new-contact-wrapper" style="width:100%" data-title="<?php echo _l('customer_contact_person_only_one_allowed'); ?>"<?php if(isset($disable_new_contacts)){ ?> data-toggle="tooltip"<?php } ?>>
   <!-- <a href="#" <?php if(isset($client->userid)){?> onclick="contact(<?php echo $client->userid; ?>); return false;"<?php }?> class="btn btn-info new-contact mbot25<?php if(isset($disable_new_contacts)){echo ' disabled';} ?>"><?php echo _l('new_contact'); ?></a> -->
            <?php if (has_permission('contacts','','create')) { ?><a href="#" onclick="contact(<?php echo $client->userid; ?>); return false;" class="btn btn-info new-contact mbot25<?php if(isset($disable_new_contacts)){echo ' disabled';} ?>" id="check_new_contact"><?php echo _l('new_contact'); ?></a> <?php } ?>
   <a href="#" ic data-toggle="modal" data-target="#contactid_add_modal" return false;" class="btn btn-info new-contact mbot25<?php if(isset($disable_new_contacts)){echo ' disabled';} ?>"><?php echo _l('merge_contact'); ?></a>
   <div class="btn-group pull-right mleft4 mbot25 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('contact_list_column'); ?>">
       <!-- Button trigger modal -->
<button type="button" class="btn btn-default" data-toggle="modal" data-target="#projects_list_column_orderModal">
  <i class="fa fa-list" aria-hidden="true"></i>
</button>

<!-- Modal -->
<div class="modal fade" id="projects_list_column_orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<?php echo form_open_multipart(admin_url('settings/contacts_list_column'),array('id'=>'projects_list_column')); ?>
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo _l('projects_list_column_order'); ?></h5>
      </div>
      <div class="modal-body">
        <div class="form-group">

            <?php $colarr = array(
            "firstname"=>array("ins"=>"firstname","ll"=>"client_firstname"),
            "email"=>array("ins"=>"email","ll"=>"client_email"),
            "company"=>array("ins"=>"company","ll"=>"client_company"),
            "phonenumber"=>array("ins"=>"phonenumber","ll"=>"clients_phone"),
            "title"=>array("ins"=>"title","ll"=>"contact_position"),
            "active"=>array("ins"=>"active","ll"=>"project_status")
            ); 
            $custom_fields = get_table_custom_fields('contacts');
			$cus_1 = array();
            foreach($custom_fields as $cfkey=>$cfval){
                $colarr[$cfval['slug']] = array("ins"=>$cfval['name'],"ll"=>$cfval['name']);
				$cus_1[$cfval['slug']] = $colarr[$cfval['slug']] = array("ins"=>$cfval['name'],"ll"=>$cfval['name']);
            }
            
            ?>  
            <ul id="sortable">
            <?php 
			

			$projects_list_column_order = (array)json_decode(get_option('contacts_list_column_order'));  ?>
            <?php foreach($projects_list_column_order as $ckey=>$cval){ 
				 if((!empty($need_fields) && in_array($ckey, $need_fields)) || !empty($cus_1[$ckey])){
			?>
                <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                <input type="checkbox" name="settings[contacts_list_column][<?php echo $ckey; ?>]" value="1" checked="checked" /> <?php echo _l($colarr[$ckey]['ll']); ?>
                </li>
            <?php }

			}			?>
            <?php foreach($colarr as $ckey=>$cval){ 
			  if((!empty($need_fields) && in_array($ckey, $need_fields)) || !empty($cus_1[$ckey])){
			if(!isset($projects_list_column_order[$ckey])){?>
                <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                <input type="checkbox" name="settings[contacts_list_column][<?php echo $ckey; ?>]" value="1"/> <?php echo _l($cval['ll']); ?>
                </li>
            <?php }}} ?>
            
            </ul>
            
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
	  </form>
    </div>
  </div>
</div>

</div>


   <?php if(!is_admin(get_staff_user_id())) { ?>
    <form class="pull-right" action="" method="GET" id="getcontact">
        <select class="form-control" name="contacts" id="contacts">
            <option value="all">All</option>
            <?php foreach($list_person as $val) {
                $selected = '';
                if($val['id'] == $_GET['contacts']) {
                    $selected = 'selected';
                }
                echo '<option value="'.$val['id'].'" '.$selected.'>'.$val['firstname'].' - '.$val['email'].'</option>';
            } ?>
        </select>
    </form>
    <?php } ?>
</div>
<div class="alpha-filter _filter_data">
                      <ul>
                      <li><a class="btn btn-<?php if(isset($_SESSION['alpha'])){echo array_search('All', $_SESSION['alpha'])?'info':'default';}else{echo 'default';} ?>" style="padding:10px,15px; margin:0 2px;" href="<?php echo admin_url('all_contacts'); ?>?a=All">All</a></li>
                  <?php foreach(range('A', 'Z') as $letter) {
                    $active = 'default';
                    if (isset($_SESSION['alpha']) && ($key = array_search($letter, $_SESSION['alpha'])) !== FALSE) {
                      $active = 'info';
                    }
                      ?>
                        <li><a class="btn btn-<?php echo $active; ?>" style="padding:10px,15px; margin:0 2px;" href="<?php echo admin_url('all_contacts'); ?>?a=<?php echo $letter; ?>"><?php echo $letter; ?></a></li>
                  <?php  }
                ?>
                </ul>
                    </div>
        <div class="clearfix"></div>
                  <?php if(has_permission('customers','','view') || have_assigned_customers()) {
                     $where_summary = '';
                     if(!has_permission('customers','','view')){
                         $where_summary = ' AND userid IN (SELECT customer_id FROM '.db_prefix().'customer_admins WHERE staff_id='.get_staff_user_id().')';
                     }
                     $CI          = & get_instance();
                     if(is_admin(get_staff_user_id())) {
                        //unset($_SESSION['alpha']);
                      //pre($_SESSION);
                        // $where_summary_activeperson_qry = 'SELECT  COUNT(*) AS `numrows`
                        // FROM tblcontacts
                        // LEFT JOIN tblclients ON tblclients.userid=tblcontacts.userid LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid AND ctable_0.fieldto="contacts" AND ctable_0.fieldid=7  where tblcontacts.active=1  AND tblcontacts.deleted_status=0 AND tblclients.deleted_status=0 '.$likeqry;

                        $where_summary_activeperson_qry = 'SELECT  COUNT(*) AS `numrows`
                        FROM tblcontacts
                        where tblcontacts.active=1  AND tblcontacts.deleted_status=0 '.$likeqry;

                        $CI          = & get_instance();
                        $where_summary_activeperson     = $CI->db->query($where_summary_activeperson_qry)->result_array();

                        // $where_summary_inactiveperson_qry = 'SELECT  COUNT(*) AS `numrows`
                        // FROM tblcontacts
                        // LEFT JOIN tblclients ON tblclients.userid=tblcontacts.userid LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid AND ctable_0.fieldto="contacts" AND ctable_0.fieldid=7  where tblcontacts.active=0  AND tblcontacts.deleted_status=0 AND tblclients.deleted_status=0 '.$likeqry;

                        $where_summary_inactiveperson_qry = 'SELECT  COUNT(*) AS `numrows`
                        FROM tblcontacts
                        where tblcontacts.active=0  AND tblcontacts.deleted_status=0 '.$likeqry;

                        $CI          = & get_instance();
                        $where_summary_inactiveperson     = $CI->db->query($where_summary_inactiveperson_qry)->result_array();

                        $where_summary_totorg_qry = 'SELECT company
                        FROM tblclients
                        LEFT JOIN tblcontacts ON tblcontacts.userid=tblclients.userid AND tblcontacts.is_primary=1 LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblclients.userid = ctable_0.relid AND ctable_0.fieldto="customers" AND ctable_0.fieldid=6 LEFT JOIN tblcustomfieldsvalues as ctable_1 ON tblclients.userid = ctable_1.relid AND ctable_1.fieldto="customers" AND ctable_1.fieldid=8
                        WHERE (tblclients.active = 1 OR tblclients.active = 0)  AND tblcontacts.deleted_status=0  group by tblclients.company';
                        $CI          = & get_instance();
                        $where_summary_totorg     = $CI->db->query($where_summary_totorg_qry)->result_array();
                        
                        $where_summary_active_qry = 'SELECT company
                         FROM tblclients
                         LEFT JOIN tblcontacts ON tblcontacts.userid=tblclients.userid AND tblcontacts.is_primary=1 LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblclients.userid = ctable_0.relid AND ctable_0.fieldto="customers" AND ctable_0.fieldid=6 LEFT JOIN tblcustomfieldsvalues as ctable_1 ON tblclients.userid = ctable_1.relid AND ctable_1.fieldto="customers" AND ctable_1.fieldid=8
                         WHERE tblclients.active = 1  AND tblcontacts.deleted_status=0  group by tblclients.company';
                        $CI          = & get_instance();
                        $where_summary_active     = $CI->db->query($where_summary_active_qry)->result_array();
                        
                        $where_summary_inactive_qry = 'SELECT company
                        FROM tblclients
                        LEFT JOIN tblcontacts ON tblcontacts.userid=tblclients.userid AND tblcontacts.is_primary=1 LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblclients.userid = ctable_0.relid AND ctable_0.fieldto="customers" AND ctable_0.fieldid=6 LEFT JOIN tblcustomfieldsvalues as ctable_1 ON tblclients.userid = ctable_1.relid AND ctable_1.fieldto="customers" AND ctable_1.fieldid=8
                        WHERE tblclients.active = 0  AND tblcontacts.deleted_status=0  group by tblclients.company';
                        $CI          = & get_instance();
                       $where_summary_inactive     = $CI->db->query($where_summary_inactive_qry)->result_array();
                    
                    } else {
//Person
                        if($_GET['contacts'] && $_GET['contacts'] != 'all') {
                            $where_summary_activeperson_qry = 'SELECT  COUNT(*) AS `numrows`
                            FROM tblcontacts
                            LEFT JOIN tblclients ON tblclients.userid=tblcontacts.userid LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid AND ctable_0.fieldto="contacts" AND ctable_0.fieldid=7
                            
                            WHERE ('.db_prefix().'contacts.addedfrom="'.get_staff_user_id().'" OR tblcontacts.id IN (select contacts_id from tblproject_contacts where project_id IN (select project_id from tblproject_contacts where project_id IN (select project_id from tblproject_members where staff_id = "'.get_staff_user_id().'") AND contacts_id = "'.$_GET['contacts'].'" ) ) OR tblcontacts.id IN (select contacts_id from tblproject_contacts where project_id IN (select tblprojects.id from tblprojects where tblprojects.teamleader = "'.get_staff_user_id().'") AND contacts_id = "'.$_GET['contacts'].'" ) )  AND tblcontacts.active=1  AND tblcontacts.deleted_status=0  AND tblclients.deleted_status=0 '.$likeqry;
                            
                            $where_summary_activeperson     = $CI->db->query($where_summary_activeperson_qry)->result_array();

                            $where_summary_inactiveperson_qry = 'SELECT  COUNT(*) AS `numrows`
                            FROM tblcontacts
                            LEFT JOIN tblclients ON tblclients.userid=tblcontacts.userid LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid AND ctable_0.fieldto="contacts" AND ctable_0.fieldid=7
                            
                            WHERE  ('.db_prefix().'contacts.addedfrom="'.get_staff_user_id().'" OR tblcontacts.id IN (select contacts_id from tblproject_contacts where project_id IN (select project_id from tblproject_contacts where project_id IN (select project_id from tblproject_members where staff_id = "'.get_staff_user_id().'") AND contacts_id = "'.$_GET['contacts'].'" ) ) OR tblcontacts.id IN (select contacts_id from tblproject_contacts where project_id IN (select tblprojects.id from tblprojects where tblprojects.teamleader = "'.get_staff_user_id().'") AND contacts_id = "'.$_GET['contacts'].'" ) )  AND tblcontacts.active=0  AND tblcontacts.deleted_status=0  AND tblclients.deleted_status=0 '.$likeqry;
                            
                            $where_summary_inactiveperson     = $CI->db->query($where_summary_inactiveperson_qry)->result_array();
                        } else {
                            $my_staffids = $CI->staff_model->get_my_staffids();
                            if($my_staffids){
                                $where = ' WHERE ('.db_prefix().'contacts.addedfrom IN (' . implode(',',$my_staffids) . ') OR (' . db_prefix() . 'contacts.userid IN (SELECT ' . db_prefix() . 'projects.clientid FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id in (' . implode(',',$my_staffids) . ')  AND tblprojects.clientid != "")) OR  (' . db_prefix() . 'contacts.userid IN (SELECT ' . db_prefix() . 'projects.clientid FROM ' . db_prefix() . 'projects where ' . db_prefix() . 'projects.teamleader in (' . implode(',',$my_staffids) . ') AND tblprojects.clientid != "" )))   AND tblcontacts.active=1  AND tblcontacts.deleted_status=0 AND tblclients.deleted_status=0 '.$likeqry;
                                $where_summary_activeperson_qry = 'SELECT  COUNT(*) AS `numrows`
                                FROM tblcontacts
                                LEFT JOIN tblclients ON tblclients.userid=tblcontacts.userid LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid AND ctable_0.fieldto="contacts" AND ctable_0.fieldid=7'.$where;
                                $where_summary_activeperson     = $CI->db->query($where_summary_activeperson_qry)->result_array();

                                $where = ' WHERE ('.db_prefix().'contacts.addedfrom IN (' . implode(',',$my_staffids) . ') OR (' . db_prefix() . 'contacts.userid IN (SELECT ' . db_prefix() . 'projects.clientid FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id in (' . implode(',',$my_staffids) . ')  AND tblprojects.clientid != "")) OR  (' . db_prefix() . 'contacts.userid IN (SELECT ' . db_prefix() . 'projects.clientid FROM ' . db_prefix() . 'projects where ' . db_prefix() . 'projects.teamleader in (' . implode(',',$my_staffids) . ') AND tblprojects.clientid != "" )))   AND tblcontacts.active=0  AND tblcontacts.deleted_status=0 AND tblclients.deleted_status=0 '.$likeqry;
                                $where_summary_inactiveperson_qry = 'SELECT  COUNT(*) AS `numrows`
                                FROM tblcontacts
                                LEFT JOIN tblclients ON tblclients.userid=tblcontacts.userid LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid AND ctable_0.fieldto="contacts" AND ctable_0.fieldid=7'.$where;
                                $where_summary_inactiveperson     = $CI->db->query($where_summary_inactiveperson_qry)->result_array();
                            } else {
                                $where_summary_activeperson_qry = 'SELECT  COUNT(*) AS `numrows`
                                FROM tblcontacts
                                LEFT JOIN tblclients ON tblclients.userid=tblcontacts.userid LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid AND ctable_0.fieldto="contacts" AND ctable_0.fieldid=7
                                
                                WHERE  ('.db_prefix().'contacts.addedfrom="'.get_staff_user_id().'" OR (tblcontacts.userid IN (select userid from tblclients where '.db_prefix().'clients.addedfrom="'.get_staff_user_id().'")) OR tblcontacts.id IN (select contacts_id from tblproject_contacts where project_id IN (select project_id from tblproject_members where staff_id = "'.get_staff_user_id().'") ) OR tblcontacts.id IN (select contacts_id from tblproject_contacts where project_id IN (select tblprojects.id from tblprojects where tblprojects.teamleader = "'.get_staff_user_id().'") ) )  AND tblcontacts.active=1  AND tblcontacts.deleted_status=0 AND tblclients.deleted_status=0 '.$likeqry;
                                $CI          = & get_instance();
                                $where_summary_activeperson     = $CI->db->query($where_summary_activeperson_qry)->result_array();

                                $where_summary_inactiveperson_qry = 'SELECT  COUNT(*) AS `numrows`
                                FROM tblcontacts
                                LEFT JOIN tblclients ON tblclients.userid=tblcontacts.userid LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid AND ctable_0.fieldto="contacts" AND ctable_0.fieldid=7
                                
                                WHERE  ('.db_prefix().'contacts.addedfrom="'.get_staff_user_id().'" OR (tblcontacts.userid IN (select userid from tblclients where '.db_prefix().'clients.addedfrom="'.get_staff_user_id().'")) OR tblcontacts.id IN (select contacts_id from tblproject_contacts where project_id IN (select project_id from tblproject_members where staff_id = "'.get_staff_user_id().'") ) OR tblcontacts.id IN (select contacts_id from tblproject_contacts where project_id IN (select tblprojects.id from tblprojects where tblprojects.teamleader = "'.get_staff_user_id().'") ) )  AND tblcontacts.active=0  AND tblcontacts.deleted_status=0 AND tblclients.deleted_status=0 '.$likeqry;
                                $CI          = & get_instance();
                                $where_summary_inactiveperson     = $CI->db->query($where_summary_inactiveperson_qry)->result_array();
                            }
                            
    // //TOTAL Organisation
    //                         $where_summary_totorg_qry = 'SELECT company
    //                         FROM tblclients
    //                         LEFT JOIN tblcontacts ON tblcontacts.userid=tblclients.userid AND tblcontacts.is_primary=1 LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblclients.userid = ctable_0.relid AND ctable_0.fieldto="customers" AND ctable_0.fieldid=6 LEFT JOIN tblcustomfieldsvalues as ctable_1 ON tblclients.userid = ctable_1.relid AND ctable_1.fieldto="customers" AND ctable_1.fieldid=8
    //                         WHERE  (tblclients.active = 1 OR tblclients.active = 0)  AND tblcontacts.deleted_status=0  AND tblclients.userid IN (SELECT userid FROM tblcontacts WHERE email=(SELECT email FROM tblstaff WHERE staffid="'.get_staff_user_id().'"))  group by tblclients.company';
    //                         $CI          = & get_instance();
    //                         $where_summary_totorg     = $CI->db->query($where_summary_totorg_qry)->result_array();
    //     //Active Organisation
    //                             $where_summary_active_qry = 'SELECT company
    //                             FROM tblclients
    //                             LEFT JOIN tblcontacts ON tblcontacts.userid=tblclients.userid AND tblcontacts.is_primary=1 LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblclients.userid = ctable_0.relid AND ctable_0.fieldto="customers" AND ctable_0.fieldid=6 LEFT JOIN tblcustomfieldsvalues as ctable_1 ON tblclients.userid = ctable_1.relid AND ctable_1.fieldto="customers" AND ctable_1.fieldid=8
    //                             WHERE  (tblclients.active = 1) AND tblcontacts.deleted_status=0  AND tblclients.userid IN (SELECT userid FROM tblcontacts WHERE email=(SELECT email FROM tblstaff WHERE staffid="'.get_staff_user_id().'")) group by tblclients.company';
    //                         $CI          = & get_instance();
    //                         $where_summary_active     = $CI->db->query($where_summary_active_qry)->result_array();
    //     //Inactive Organisation
    //                         $where_summary_inactive_qry = 'SELECT company
    //                         FROM tblclients
    //                         LEFT JOIN tblcontacts ON tblcontacts.userid=tblclients.userid AND tblcontacts.is_primary=1 LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblclients.userid = ctable_0.relid AND ctable_0.fieldto="customers" AND ctable_0.fieldid=6 LEFT JOIN tblcustomfieldsvalues as ctable_1 ON tblclients.userid = ctable_1.relid AND ctable_1.fieldto="customers" AND ctable_1.fieldid=8
    //                         WHERE  (tblclients.active = 0)  AND tblcontacts.deleted_status=0 AND tblclients.userid IN (SELECT userid FROM tblcontacts WHERE email=(SELECT email FROM tblstaff WHERE staffid="'.get_staff_user_id().'")) group by tblclients.company';
    //                         $CI          = & get_instance();
    //                         $where_summary_inactive     = $CI->db->query($where_summary_inactive_qry)->result_array();
                        }
                    }
                     ?>
                  <hr class="hr-panel-heading" />
                  <div class="row mbot15">
                     <!-- <div class="col-md-12">
                        <h4 class="no-margin"><?php echo _l('customers_summary'); ?></h4>
                     </div>
                     <div class="col-md-2 col-xs-6 border-right">
                        <h3 class="bold"><?php echo $totcnt = count($where_summary_active) + count($where_summary_inactive); ?></h3>
                        <span class="text-dark"><?php echo _l('customers_summary_total'); ?></span>
                     </div>
                     <div class="col-md-2 col-xs-6 border-right">
                        <h3 class="bold"><?php echo count($where_summary_active); ?></h3>
                        <span class="text-success"><?php echo _l('active_customers'); ?></span>
                     </div>
                     <div class="col-md-2 col-xs-6 border-right">
                        <h3 class="bold"><?php echo count($where_summary_inactive); ?></h3>
                        <span class="text-danger"><?php echo _l('inactive_active_customers'); ?></span>
                     </div> -->
                     <div class="col-md-2 col-xs-6 border-right">
                        <h3 class="bold"><?php echo $where_summary_activeperson[0]['numrows']; ?></h3>
                        <span class="text-info"><?php echo _l('customers_summary_active'); ?></span>
                     </div>
                     <div class="col-md-2  col-xs-6 border-right">
                        <h3 class="bold"><?php echo $where_summary_inactiveperson[0]['numrows']; ?></h3>
                        <span class="text-danger"><?php echo _l('customers_summary_inactive'); ?></span>
                     </div>
                     <div class="col-md-2  col-xs-6 border-right">
                        <h3 class="bold"><?php echo ($where_summary_activeperson[0]['numrows'] + $where_summary_inactiveperson[0]['numrows']); ?></h3>
                        <span class="text-dark"><?php echo _l('customers_summary_totalperson'); ?></span>
                     </div>
                     
                     </div>
                  <?php } ?>
                  <hr class="hr-panel-heading" />                  <div class="clearfix"></div>
       
        <?php
        $table_data_temp = [
            'id'=>_l('the_number_sign'),
            'firstname'=>_l('client_firstname'),
            'email'=>_l('client_email'),
            'company'=>_l('client_company'),
            'phonenumber'=>_l('clients_phone'),
            'title'=>_l('contact_position'),
            'active'=>_l('project_status')
         ];
         
         $custom_fields = get_custom_fields('contacts', ['show_on_table' => 1]);
         foreach ($custom_fields  as $cfkey=>$cfval) {
             $table_data_temp[$cfval['slug']] = $cfval['name'];
         }
         
         $contacts_list_column_order = (array)json_decode(get_option('contacts_list_column_order')); //pr($projects_list_column_order);
         $table_data = array();
          foreach($contacts_list_column_order as $ckey=>$cval){
              if(isset($table_data_temp[$ckey])){
                  $table_data[] =$table_data_temp[$ckey];
              }
          }
         $table_data = hooks()->apply_filters('contacts_table_columns', $table_data);
//pre($table_data);
         
    //     $table_data = array(_l('client_firstname'));
    //     if(is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1'){
    //      array_push($table_data, array(
    //       'name'=>_l('gdpr_consent') .' ('._l('gdpr_short').')',
    //       'th_attrs'=>array('id'=>'th-consent', 'class'=>'not-export')
    //     ));
    //    }
    //    $table_data = array_merge($table_data, array(
    //     _l('client_email'),
    //     _l('clients_list_company'),
    //     _l('client_phonenumber'),
    //     _l('contact_position'),
    //     _l('clients_list_last_login'),
    //     _l('contact_active'),
    //   ));
    //    $custom_fields = get_custom_fields('contacts',array('show_on_table'=>1));
    //    foreach($custom_fields as $field){
    //     array_push($table_data,$field['name']);
    //   }
      render_datatable($table_data, isset($class) ?  $class : 'all-contacts', [], [
        'data-last-order-identifier' => 'all-contacts',
        'data-default-order'  => get_table_last_order('all-contacts'),
    ]);
      ?>
    </div>
  </div>
</div>
</div>
</div>
</div>
<div class="modal fade" id="contactid_add_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('merge_contact'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/ajax_mergecontact',array('id'=>'contactid_add_group_modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                    <div class="form-group select-placeholder contactid input-group-select">
                      <label for="contactid" class="control-label"><?php echo _l('contact'); ?></label>
                      <div class="dropdown bootstrap-select input-group-select show-tick bs3 bs3-has-addon" style="width: 100%;">
                          <select id="contact_name" name="contact_name" class="selectpicker contact_name" data-actions-box="1" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true" tabindex="-98">
                          <?php
                              if(isset($contacts)){
                                  foreach($contacts as $contact){
                                      echo '<option value="'.$contact['firstname'].'" >'.$contact['firstname'].'</option>';
                                  }
                              }
                          ?>
                          </select>
                      </div>
                    </div>
                    <div class="col-md-12 contactdetails"></div>
                  </div>
              </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<div class="modal fade" id="call_person_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php //echo _l('merge_contact'); ?>Deals</span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                    <div class="form-group select-placeholder contactid input-group-select">
                      <label for="contactid" class="control-label"><?php echo _l('contact'); ?></label>
                      <div class="dropdown bootstrap-select input-group-select show-tick bs3 bs3-has-addon" style="width: 100%;">
                          <select id="deals_list" name="deals_list" class="selectpicker deals_list" data-actions-box="1" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true" tabindex="-98">
                          
                          </select>
                      </div>
                    </div>
                  </div>
                  <input type="hidden" id="con_id" value="">
                  <input type="hidden" id="contact_no" value="">
              </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" onclick="clicktocall_create();" class="btn btn-info">Call</button>
            </div>
        </div>
    </div>
</div>


<?php init_tail(); ?>
<?php $this->load->view('admin/clients/client_js'); ?>
<div id="contact_data"></div>
<div id="consent_data"></div>
<script>
 $(function(){
  var optionsHeading = [];
  var allContactsServerParams = {
   "custom_view": "[name='custom_view']",
 }
 <?php if(is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1'){ ?>
  optionsHeading.push($('#th-consent').index());
  <?php } ?>
  _table_api = initDataTable('.table-all-contacts', window.location.href, optionsHeading, optionsHeading, allContactsServerParams);
  if(_table_api) {
   <?php if(is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1'){ ?>
    _table_api.on('draw', function () {
      var tableData = $('.table-all-contacts').find('tbody tr');
      $.each(tableData, function() {
        $(this).find('td:eq(2)').addClass('bg-light-gray');
      });
    });
    $('select[name="custom_view"]').on('change', function(){
      _table_api.ajax.reload()
      .columns.adjust()
      .responsive.recalc();
    });
    <?php } ?>
  }
});

//Merge Contact
appValidateForm($('#contactid_add_group_modal'), {
      // selectedcontact: 'required',
      // contact_name: 'required'
    });


    $('#contactid_add_group_modal').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var group_id = $(invoker).data('id');
        $('#contactid_add_group_modal input[name="project"]').val('');
        // is from the edit button
        if (typeof(group_id) !== 'undefined') {
            $('#contactid_add_group_modal input[name="project"]').val($(invoker).parents('tr').find('td').eq(0).text());
        }
    });
      
  

   $('#contactid_add_group_modal').submit(function(e) {
          e.preventDefault();
            var contactid = $('input[name="selectedcontact"]:checked').val();
            var name = $('.contact_name div.filter-option-inner-inner').html();
            if(contactid) {
                $.ajax({
                    url: admin_url + 'clients/ajax_mergecontact',
                    type: 'POST',
                    data: {
                        'contactid': contactid,
                        'cont_name' : name
                    },
                    dataType: 'json',
                    success: function success(result) {
                      alert_float('success', result.message);
                      $('#contact_name').selectpicker('refresh'); 
                      $('#contactid_add_group_modal input[name="contact_name"]').val('');
                      $('.contact_name div.filter-option-inner-inner').html('');
                      $('.contactdetails').html('');
                      $('#contactid_add_modal').modal('hide');
                      setTimeout(function(){// wait for 5 secs(2)
                          location.reload(); // then reload the page.(3)
                      }, 100);
                    }
                });
            } else {
              alert('Please Select Contact.');
              return false;
            }
    });

    $('select#contact_name').on('change', function() {
        var contactid = this.value;
        $.ajax({
            url: admin_url + 'clients/getContactById',
            type: 'POST',
            data: {
                'contactid': contactid
            },
            dataType: 'json',
            success: function success(result) {
                $('.contactdetails').html(result.result);
            }
        });
    });

    $('select#contacts').on('change', function() {
        var contactid = this.value;
        $("#getcontact").submit();
    });

function getFormData($form){
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}
</script>
<script>
  $( function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
	init_ajax_search('customer', '#clientid.ajax-search');
		//$('#clientid').selectpicker();
        init_selectpicker();
        init_datepicker();
  } );
  </script>
</body>
</html>
