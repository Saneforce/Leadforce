<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="<?php
if ($openEdit == true) {
    echo 'open-edit ';
}
?>lead-wrapper" <?php
         if (isset($lead) && ($lead->junk == 1 || $lead->lost == 1)) {
             echo 'lead-is-junk-or-lost';
         }
         ?>>
           <?php if (isset($lead)) { ?>
        <div class="btn-group pull-left lead-actions-left">
            <a href="#" lead-edit class="mright10 font-medium-xs pull-left<?php
           if ($lead_locked == true) {
               echo ' hide';
           }
               ?>">
    <?php echo _l('edit'); ?>
                <i class="fa fa-pencil-square-o"></i>
            </a>
            <a href="#" class="font-medium-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="lead-more-btn">
                <?php echo _l('more'); ?>
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-left" id="lead-more-dropdown">
                        <?php
                        if ($lead->junk == 0) {
                            if ($lead->lost == 0 && (total_rows(db_prefix() . 'clients', array('leadid' => $lead->id)) == 0)) {
                                ?>
                        <li>
                            <a href="#" onclick="lead_mark_as_lost(<?php echo $lead->id; ?>); return false;">
                                <i class="fa fa-mars"></i>
                                <?php echo _l('lead_mark_as_lost'); ?>
                            </a>
                        </li>
                    <?php } else if ($lead->lost == 1) { ?>
                        <li>
                            <a href="#" onclick="lead_unmark_as_lost(<?php echo $lead->id; ?>); return false;">
                                <i class="fa fa-smile-o"></i>
                        <?php echo _l('lead_unmark_as_lost'); ?>
                            </a>
                        </li>
        <?php } ?>
                        <?php } ?>
                <!-- mark as junk -->
    <?php
    if ($lead->lost == 0) {
        if ($lead->junk == 0 && (total_rows(db_prefix() . 'clients', array('leadid' => $lead->id)) == 0)) {
            ?>
                        <li>
                            <a href="#" onclick="lead_mark_as_junk(<?php echo $lead->id; ?>); return false;">
                                <i class="fa fa fa-times"></i>
            <?php echo _l('lead_mark_as_junk'); ?>
                            </a>
                        </li>
                    <?php } else if ($lead->junk == 1) { ?>
                        <li>
                            <a href="#" onclick="lead_unmark_as_junk(<?php echo $lead->id; ?>); return false;">
                                <i class="fa fa-smile-o"></i>
                                <?php echo _l('lead_unmark_as_junk'); ?>
                            </a>
                        </li>
                    <?php } ?>
                <?php } ?>
    <?php if (((is_lead_creator($lead->id) || has_permission('leads', '', 'delete')) && $lead_locked == false) || is_admin()) { ?>
                    <li>
                        <a href="<?php echo admin_url('leads/delete/' . $lead->id); ?>" class="text-danger delete-text _delete" data-toggle="tooltip" title="">
                            <i class="fa fa-remove"></i>
            <?php echo _l('lead_edit_delete_tooltip'); ?>
                        </a>
                    </li>
        <?php } ?>
            </ul>
        </div>
        <a data-toggle="tooltip" class="btn btn-default pull-right lead-print-btn lead-top-btn lead-view mleft5" onclick="print_lead_information(); return false;" data-placement="top" title="<?php echo _l('print'); ?>" href="#">
            <i class="fa fa-print"></i>
        </a>
        <?php
        $client = false;
        $convert_to_client_tooltip_email_exists = '';
        if (total_rows(db_prefix() . 'contacts', array('email' => $lead->email)) > 0 && total_rows(db_prefix() . 'clients', array('leadid' => $lead->id)) == 0) {
            $convert_to_client_tooltip_email_exists = _l('lead_email_already_exists');
            $text = _l('lead_convert_to_client');
        } else if (total_rows(db_prefix() . 'clients', array('leadid' => $lead->id))) {
            $client = true;
        } else {
            $text = _l('lead_convert_to_client');
        }
        ?>
        <?php if ($lead_locked == false) { ?>
            <div class="lead-edit<?php
        if (isset($lead)) {
            echo ' hide';
        }
        ?>">
                <button type="button" class="btn btn-info pull-right mleft5 lead-top-btn lead-save-btn" onclick="document.getElementById('lead-form-submit').click();">
            <?php echo _l('submit'); ?>
                </button>
            </div>
        <?php } ?>
        <!-- <?php if ($client && (has_permission('customers', '', 'view') || is_customer_admin(get_client_id_by_lead_id($lead->id)))) { ?>
            <a data-toggle="tooltip" class="btn btn-success pull-right lead-top-btn lead-view" data-placement="top" title="<?php echo _l('lead_converted_edit_client_profile'); ?>" href="<?php echo admin_url('clients/client/' . get_client_id_by_lead_id($lead->id)); ?>">
                <i class="fa fa-user-o"></i>
            </a>
        <?php } ?> -->
    <?php if($lead->project_id > 0){ ?>
      <a href="<?php echo admin_url('leads/convert_lead_to_existing_deal/' . $lead->id); ?>" class="btn btn-success pull-right lead-convert-to-customer lead-top-btn lead-view" >
      <!-- <i class="fa fa-user-o"></i> -->
      <?php echo  _l('lead_convert_to_client'); ?>
      </a>
      <?php } else { ?>
        <a href="#" class="btn btn-success pull-right lead-convert-to-customer lead-top-btn lead-view" onclick="convert_lead_to_customer(<?php echo $lead->id; ?>); return false;">
        <!-- <i class="fa fa-user-o"></i> -->
        <?php echo  _l('lead_convert_to_client'); ?>
        </a>
            <?php } ?>
        <?php } ?>
    <div class="clearfix no-margin"></div>

            <?php if (isset($lead)) { ?>

        <div class="row mbot15">
            <hr class="no-margin" />
        </div>

        <div class="alert alert-warning hide mtop20" role="alert" id="lead_proposal_warning">
        <?php echo _l('proposal_warning_email_change', array(_l('lead_lowercase'), _l('lead_lowercase'), _l('lead_lowercase'))); ?>
            <hr />
            <a href="#" onclick="update_all_proposal_emails_linked_to_lead(<?php echo $lead->id; ?>); return false;">
            <?php echo _l('update_proposal_email_yes'); ?>
            </a>
            <br />
            <a href="#" onclick="init_lead_modal_data(<?php echo $lead->id; ?>); return false;">
    <?php echo _l('update_proposal_email_no'); ?>
            </a>
        </div>
<?php } ?>
<?php echo form_open((isset($lead) ? admin_url('leads/lead/' . $lead->id) : admin_url('leads/lead')), array('id' => 'lead_form')); ?>
    <div class="row">
        <div class="lead-view<?php
if (!isset($lead)) {
    echo ' hide';
}
?>" id="leadViewWrapper">
            <div class="col-md-4 col-xs-12 lead-information-col">
                <div class="lead-info-heading">
                    <h4 class="no-margin font-medium-xs bold">
<?php echo _l('lead_info'); ?>
                    </h4>
                </div>
                <p class="text-muted lead-field-heading no-mtop"><?php echo _l('lead_add_edit_name'); ?></p>
                <p class="bold font-medium-xs lead-name"><?php echo (isset($lead) && $lead->name != '' ? $lead->name : '-') ?></p>
                <p class="text-muted lead-field-heading"><?php echo _l('lead_title'); ?></p>
                <p class="bold font-medium-xs"><?php echo (isset($lead) && $lead->title != '' ? $lead->title : '-') ?></p>
                <p class="text-muted lead-field-heading"><?php echo _l('lead_add_edit_email'); ?></p>
                <p class="bold font-medium-xs"><?php echo (isset($lead) && $lead->email != '' ? '<a href="mailto:' . $lead->email . '">' . $lead->email . '</a>' : '-') ?></p>
                <p class="text-muted lead-field-heading"><?php echo _l('lead_website'); ?></p>
                <p class="bold font-medium-xs"><?php echo (isset($lead) && $lead->website != '' ? '<a href="' . maybe_add_http($lead->website) . '" target="_blank">' . $lead->website . '</a>' : '-') ?></p>
                <p class="text-muted lead-field-heading"><?php echo _l('lead_add_edit_phonenumber'); ?></p>
                <p class="bold font-medium-xs"><?php echo (isset($lead) && $lead->phonenumber != '' ? '<a href="tel:' . $lead->phonenumber . '">' . $lead->phonenumber . '</a>' : '-') ?></p>
                <p class="text-muted lead-field-heading"><?php echo _l('lead_company'); ?></p>
                <p class="bold font-medium-xs"><?php echo (isset($lead) && $lead->company != '' ? $lead->company : '-') ?></p>
                <p class="text-muted lead-field-heading"><?php echo _l('lead_address'); ?></p>
                <p class="bold font-medium-xs"><?php echo (isset($lead) && $lead->address != '' ? $lead->address : '-') ?></p>
                <p class="text-muted lead-field-heading"><?php echo _l('lead_city'); ?></p>
                <p class="bold font-medium-xs"><?php echo (isset($lead) && $lead->city != '' ? $lead->city : '-') ?></p>
                <p class="text-muted lead-field-heading"><?php echo _l('lead_state'); ?></p>
                <p class="bold font-medium-xs"><?php echo (isset($lead) && $lead->state != '' ? $lead->state : '-') ?></p>
                <p class="text-muted lead-field-heading"><?php echo _l('lead_country'); ?></p>
                <p class="bold font-medium-xs"><?php echo (isset($lead) && $lead->country != 0 ? get_country($lead->country)->short_name : '-') ?></p>
                <p class="text-muted lead-field-heading"><?php echo _l('lead_zip'); ?></p>
                <p class="bold font-medium-xs"><?php echo (isset($lead) && $lead->zip != '' ? $lead->zip : '-') ?></p>
            </div>
            <div class="col-md-4 col-xs-12 lead-information-col">
                <div class="lead-info-heading">
                    <h4 class="no-margin font-medium-xs bold">
<?php echo _l('lead_general_info'); ?>
                    </h4>
                </div>
                <p class="text-muted lead-field-heading no-mtop">Source</p>
                <p class="bold font-medium-xs mbot15"><?php echo (isset($source) && $source != '' ? $source : '-') ?></p>
                <p class="text-muted lead-field-heading">Lead Owner</p>
                <p class="bold font-medium-xs mbot15"><?php echo (isset($lead) && $lead->assigned != 0 ? get_staff_full_name($lead->assigned) : '-') ?></p>
                <!-- <p class="text-muted lead-field-heading"><?php echo _l('pipeline'); ?></p>
                <p class="bold font-medium-xs mbot15"><?php echo (isset($lead) && $lead->pipeline_name != '' ? $lead->pipeline_name : '-') ?></p> -->
                    <?php //if (get_option('disable_language') == 0) { ?>
                    <!-- <p class="text-muted lead-field-heading"><?php echo _l('localization_default_language'); ?></p>
                    <p class="bold font-medium-xs mbot15"><?php echo (isset($lead) && $lead->default_language != '' ? ucfirst($lead->default_language) : _l('system_default_string')) ?></p> -->
                    <?php //} ?>
                <p class="text-muted lead-field-heading"><?php echo _l('tags'); ?></p>
                <p class="bold font-medium-xs mbot10">
                <?php
                if (isset($lead)) {
                    $tags = get_tags_in($lead->id, 'lead');
                    if (count($tags) > 0) {
                        echo render_tags($tags);
                        echo '<div class="clearfix"></div>';
                    } else {
                        echo '-';
                    }
                }
                ?>
                </p>
                <?php /* <p class="text-muted lead-field-heading"><?php echo _l('leads_dt_datecreated'); ?></p>
                  <p class="bold font-medium-xs"><?php echo (isset($lead) && $lead->dateadded != '' ? '<span class="text-has-action" data-toggle="tooltip" data-title="'._dt($lead->dateadded).'">' . time_ago($lead->dateadded) .'</span>' : '-') ?></p>
                  <p class="text-muted lead-field-heading"><?php echo _l('leads_dt_last_contact'); ?></p>
                  <p class="bold font-medium-xs"><?php echo (isset($lead) && $lead->lastcontact != '' ? '<span class="text-has-action" data-toggle="tooltip" data-title="'._dt($lead->lastcontact).'">' . time_ago($lead->lastcontact) .'</span>' : '-') ?></p>
                  <p class="text-muted lead-field-heading"><?php echo _l('lead_public'); ?></p>
                  <p class="bold font-medium-xs mbot15">
                  <?php if(isset($lead)){
                  if($lead->is_public == 1){
                  echo _l('lead_is_public_yes');
                  } else {
                  echo _l('lead_is_public_no');
                  }
                  } else {
                  echo '-';
                  }
                  ?>
                  </p> */ ?>
                <?php if (isset($lead) && $lead->from_form_id != 0) { ?>
                    <p class="text-muted lead-field-heading"><?php echo _l('web_to_lead_form'); ?></p>
                    <p class="bold font-medium-xs mbot15"><?php echo $lead->form_data->name; ?></p>
                <?php } ?>
            </div>
            <div class="col-md-4 col-xs-12 lead-information-col">
                <?php if (total_rows(db_prefix() . 'customfields', array('fieldto' => 'leads', 'active' => 1)) > 0 && isset($lead)) { ?>
                    <div class="lead-info-heading">
                        <h4 class="no-margin font-medium-xs bold">
    <?php echo _l('custom_fields'); ?>
                        </h4>
                    </div>
    <?php
    $custom_fields = get_custom_fields('leads');
    foreach ($custom_fields as $field) {
        $value = get_custom_field_value($lead->id, $field['id'], 'leads');
        ?>
                        <p class="text-muted lead-field-heading no-mtop"><?php echo $field['name']; ?></p>
                        <?php if($value != '' && $field['type'] =='location'){ ?>
                            <iframe src = "https://maps.google.com/maps?q=<?php echo $value; ?>&hl=es;z=14&output=embed"></iframe>
                        <?php }else{ ?>
                            <p class="bold font-medium-xs"><?php echo ($value != '' ? $value : '-') ?></p>
                        <?php } ?>
    <?php } ?>
<?php } ?>
            </div>
            <!--          <div class="clearfix"></div>
                     <div class="col-md-12">
                         <div class="row">
                             <div class="col-md-3">
                                 <p class="text-muted lead-field-heading"><?php echo _l('clients_contracts_dt_start_date'); ?></p>
                        <p class="bold font-medium-xs"><?php echo (isset($lead) && $lead->startdate != '' ? $lead->startdate : '-') ?></p>
                             </div>
                             <div class="col-md-3">
                                 <p class="text-muted lead-field-heading"><?php echo _l('clients_contracts_dt_end_date'); ?></p>
                        <p class="bold font-medium-xs"><?php echo (isset($lead) && $lead->enddate != '' ? $lead->enddate : '-') ?></p>
                             </div>
                             <div class="col-md-3">
                                 <p class="text-muted lead-field-heading"><?php echo _l('currency'); ?></p>
                        <p class="bold font-medium-xs">
<?php
if (isset($lead) && $lead->currency != '') {
    foreach ($currencies as $key => $value) {
        if ($value['id'] == $lead->currency) {
            echo $value['name'];
        }
    }
} else {
    echo '-';
}
?>
                        </p>
                             </div>
                             <div class="col-md-3">
                                 <p class="text-muted lead-field-heading"><?php echo _l('invoice_item_add_edit_rate_currency'); ?></p>
                        <p class="bold font-medium-xs"><?php echo (isset($lead) && $lead->rate != '' ? $lead->rate : '-') ?></p>
                             </div>
                         </div>
                        
                     </div>-->
            <div class="clearfix"></div>
            <div class="col-md-12">
                <p class="text-muted lead-field-heading"><?php echo _l('lead_description'); ?></p>
                <p class="bold font-medium-xs"><?php echo (isset($lead) && $lead->description != '' ? $lead->description : '-') ?></p>
            </div>
        </div>
        <div class="clearfix"></div>
        
        <div class="lead-edit<?php
                        if (isset($lead)) {
                            echo ' hide';
                        }
?>">

            <div class="row">
            
                <!-- <div class="col-md-3">
                    <div class="pipelineleads">
                    <?php
                    $assigned_attrs = array();
                    $pipelineleadselected = (isset($lead) ? $lead->pipeline_id : '');
                    echo render_select('pipeline_id', $pipelines, array('id', 'name'), 'pipeline', $pipelineleadselected, $assigned_attrs);
                    ?>
                    </div>
                    <input type="hidden" name="client_id" id="client_id" value="<?php echo (isset($lead) ? $lead->client_id : '') ?>" />
                </div>
                <div class="col-md-3 form_status">
<?php
$statusselected = (isset($lead) ? $lead->status : '');
echo render_select('status', $statuses, array('id', 'name'), 'lead_add_edit_status', $statusselected, $assigned_attrs);
?>
                </div>
                <div class="col-md-3 form_teamleader">
<?php
$teamleaderselected = (isset($lead) && !empty($lead->teamleader) ? $lead->teamleader : '');
echo render_select('teamleader', $teamleaders, array('staffid', array('firstname', 'lastname')), 'teamleader', $teamleaderselected, $assigned_attrs);
?>
                </div>
                <div class="col-md-3 form_assigned">
                <?php
                $teammemberselected = (isset($lead) && !empty($lead->assigned) ? $lead->assigned : '');
                echo render_select('assigned', $teammembers, array('staffid', array('firstname', 'lastname')), 'teammembers', $teammemberselected, $assigned_attrs);
                ?>
                </div> -->
            </div>
           
            <div class="clearfix"></div>
            <button type="button" class="btn btn-info pull-right mleft5 lead-top-btn lead-save-btn <?php
                        if (isset($lead)) {
                            echo ' hide';
                        }
?>" style="margin-top:-70px; margin-right:15px;" onclick="document.getElementById('lead-form-submit').click();">
            Save                </button>

            <div class="col-md-6">
<?php $value = (isset($lead) ? $lead->name : ''); ?>
<?php echo render_input('name', 'lead_add_edit_name', $value); ?>
<?php $value = (isset($lead) ? $lead->title : ''); ?>
<?php echo render_input('title', 'lead_title', $value); ?>
<?php $value = (isset($lead) ? $lead->email : ''); ?>
<div class="form-group" app-field-wrapper="email">
    <label for="email" class="control-label">Email Address</label>
    <input type="email" id="email" name="email" class="form-control" value="<?php echo $value; ?>" autocomplete="new-text">
</div>
<?php //echo render_input('email', 'lead_add_edit_email', $value); ?>
<?php
if ((isset($lead) && empty($lead->website)) || !isset($lead)) {
    $value = (isset($lead) ? $lead->website : '');
    echo render_input('website', 'lead_website', $value);
} else {
    ?>
                    <div class="form-group">
                        <label for="website"><?php echo _l('lead_website'); ?></label>
                        <div class="input-group">
                            <input type="text" name="website" id="website" value="<?php echo $lead->website; ?>" class="form-control">
                            <div class="input-group-addon">
                                <span>
                                    <a href="<?php echo maybe_add_http($lead->website); ?>" target="_blank" tabindex="-1">
                                        <i class="fa fa-globe"></i>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php
                }
                $value = (isset($lead) ? $lead->phonenumber : '');
                ?>
                <?php echo render_input('phonenumber', 'lead_add_edit_phonenumber', $value); ?>
                <?php $value = (isset($lead) ? $lead->company : ''); ?>
<?php echo render_input('company', 'lead_company', $value); ?>
<div class="form-group" app-field-wrapper="company" id="source_addlead">
                <label for="company" class="control-label">Source</label>
                <?php
                   foreach($sources as $val) {
                        if($val['slug'] == 'manual') {
                            $selected = $val['id'];
                        }
                   }
                   $selected = (isset($lead) ? $lead->source : $selected);
                    echo render_select('view_source',$sources,array('id','name'),'',$selected,array('data-width'=>'100%','data-none-selected-text'=>'Manually'),array(),'no-mbot');
                ?>
            </div>
            </div>
            <div class="col-md-6">
                        <?php $value = (isset($lead) ? $lead->address : ''); ?>
                        <?php echo render_textarea('address', 'lead_address', $value, array('rows' => 1, 'style' => 'height:36px;font-size:100%;')); ?>
                        <?php $value = (isset($lead) ? $lead->city : ''); ?>
                        <?php echo render_input('city', 'lead_city', $value); ?>
                        <?php $value = (isset($lead) ? $lead->state : ''); ?>
                        <?php echo render_input('state', 'lead_state', $value); ?>
                        <?php
                        $countries = get_all_countries();
                        $customer_default_country = get_option('customer_default_country');
                        $selected = ( isset($lead) ? $lead->country : $customer_default_country);
                        echo render_select('country', $countries, array('country_id', array('short_name')), 'lead_country', $selected, array('data-none-selected-text' => _l('dropdown_non_selected_tex')));
                        ?>
<?php $value = (isset($lead) ? $lead->zip : ''); ?>
                <?php echo render_input('zip', 'lead_zip', $value); ?>
                    <div class="form_assigned">
                    <?php
                        $assigned_attrs = array();
                        $selected = (isset($lead) ? $lead->assigned : get_staff_user_id());
                        if(isset($lead)
                            && $lead->assigned == get_staff_user_id()
                            && $lead->addedfrom != get_staff_user_id()
                            && !is_admin($lead->assigned)
                            && !has_permission('leads','','view')
                        ){
                            $assigned_attrs['disabled'] = true;
                        }
                        echo render_select('assigned',$members,array('staffid',array('firstname','lastname')),'lead_add_edit_assigned',$selected,$assigned_attrs); 
                    ?>
                    </div>
                <?php //if (get_option('disable_language') == 0) { ?>
                    <!-- <div class="form-group">
                        <label for="default_language" class="control-label"><?php echo _l('localization_default_language'); ?></label>
                        <select name="default_language" data-live-search="true" id="default_language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            <option value=""><?php echo _l('system_default_string'); ?></option>
                <?php
                foreach ($this->app->get_available_languages() as $availableLanguage) {
                    $selected = '';
                    if (isset($lead)) {
                        if ($lead->default_language == $availableLanguage) {
                            $selected = 'selected';
                        }
                    }
                    ?>
                                <option value="<?php echo $availableLanguage; ?>" <?php echo $selected; ?>><?php echo ucfirst($availableLanguage); ?></option>
                <?php } ?>
                        </select>
                    </div> -->
            <?php //} ?>
            </div>
            <!-- <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6 form_assigned">
                        <?php
                        $teammemberselected = (isset($lead) && !empty($lead->assigned) ? $lead->assigned : '');
                        echo render_select('assigned', $teammembers, array('staffid', array('firstname', 'lastname')), 'teammembers', $teammemberselected, $assigned_attrs);
                        ?>
                    </div>
                </div>
            </div> -->
            <!--         <div class="col-md-12">
                         <div class="row">
                           <div class="col-md-3">
            <?php $value = (isset($lead) ? $lead->startdate : _d(date('Y-m-d'))); ?>
            <?php echo render_date_input('startdate', 'clients_contracts_dt_start_date', $value); ?>
                        </div>
                         <div class="col-md-3"> 
            <?php $value = (isset($lead) ? $lead->enddate : _d(date('Y-m-d'))); ?>
            <?php echo render_date_input('enddate', 'clients_contracts_dt_end_date', $value); ?>
                         </div> 
                         <div class="col-md-3"> 
                             
            <?php
            $currency_attr = array('readonly' => true, 'data-show-subtext' => true);
            $currency_attr = apply_filters_deprecated('invoice_currency_disabled', [$currency_attr], '2.3.0', 'invoice_currency_attributes');

            foreach ($currencies as $currency) {
                if ($currency['isdefault'] == 1) {
                    $currency_attr['data-base'] = $currency['id'];
                }
                if (isset($invoice)) {
                    if ($currency['id'] == $lead->currency) {
                        $selected = $currency['id'];
                    }
                } else {
                    if ($currency['isdefault'] == 1) {
                        $selected = $currency['id'];
                    }
                }
            }
            $currency_attr = hooks()->apply_filters('invoice_currency_attributes', $currency_attr);
            ?>
                <?php echo render_select('currency', $currencies, array('id', 'name', 'symbol'), 'invoice_add_edit_currency', $selected, $currency_attr); ?>
                         </div>
                             <div class="col-md-3"> 
                <?php $value = (isset($lead) ? $lead->rate : ''); ?>
                             <label for="rate" class="control-label">
                <?php echo _l('invoice_item_add_edit_rate_currency', $base_currency->name . ' <small>(' . _l('base_currency_string') . ')</small>'); ?></label>
                                 <input type="number" id="rate" name="rate" class="form-control" value="<?php echo $value; ?>">
                                    </div>
                         </div> 
                         </div>-->
            
                        
                         <hr class="mtop5 mbot10" />
            <div class="col-md-12">
                <div class="form-group no-mbot" id="inputTagsWrapper">
                    <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
                    <input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo (isset($lead) ? prep_tags_input(get_tags_in($lead->id, 'lead')) : ''); ?>" data-role="tagsinput">
                </div>
            </div>
            <div class="clearfix"></div>             
            <div class="col-md-12">
                <?php $value = (isset($lead) ? $lead->description : ''); ?>
                <?php echo render_textarea('description', 'lead_description', $value); ?>
                <?php /* <div class="row">
                  <div class="col-md-12">
                  <?php if(!isset($lead)){ ?>
                  <div class="lead-select-date-contacted hide">
                  <?php echo render_datetime_input('custom_contact_date','lead_add_edit_datecontacted','',array('data-date-end-date'=>date('Y-m-d'))); ?>
                  </div>
                  <?php } else { ?>
                  <?php echo render_datetime_input('lastcontact','leads_dt_last_contact',_dt($lead->lastcontact),array('data-date-end-date'=>date('Y-m-d'))); ?>
                  <?php } ?>
                  <div class="checkbox-inline checkbox checkbox-primary<?php if(isset($lead)){echo ' hide';} ?><?php if(isset($lead) && (is_lead_creator($lead->id) || has_permission('leads','','edit'))){echo ' lead-edit';} ?>">
                  <input type="checkbox" name="is_public" <?php if(isset($lead)){if($lead->is_public == 1){echo 'checked';}}; ?> id="lead_public">
                  <label for="lead_public"><?php echo _l('lead_public'); ?></label>
                  </div>
                  <?php if(!isset($lead)){ ?>
                  <div class="checkbox-inline checkbox checkbox-primary">
                  <input type="checkbox" name="contacted_today" id="contacted_today" checked>
                  <label for="contacted_today"><?php echo _l('lead_add_edit_contacted_today'); ?></label>
                  </div>
                  <?php } ?>
                  </div>
                  </div> */ ?>
            </div>
            <div class="col-md-12 mtop15">
<?php $rel_id = (isset($lead) ? $lead->id : false); ?>
<?php echo render_custom_fields('leads', $rel_id); ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <?php if (isset($lead)) { ?>
        <div class="lead-latest-activity lead-view">
            <div class="lead-info-heading">
                <h4 class="no-margin bold font-medium-xs"><?php echo _l('lead_latest_activity'); ?></h4>
            </div>
            <div id="lead-latest-activity" class="pleft5"></div>
        </div>
<?php } ?>
<?php if ($lead_locked == false) { ?>
        <div class="lead-edit<?php
    if (isset($lead)) {
        echo ' hide';
    }
    ?>">
            <hr />
            <button type="submit" class="btn btn-info pull-right lead-save-btn" id="lead-form-submit"><?php echo _l('submit'); ?></button>
            <button type="button" class="btn btn-default pull-right mright5" data-dismiss="modal"><?php echo _l('close'); ?></button>
        </div>
<?php } ?>
    <div class="clearfix"></div>
<?php echo form_close(); ?>
</div>
<?php if (isset($lead) && $lead_locked == true) { ?>
    <script>
        $(function () {
            // Set all fields to disabled if lead is locked
            $.each($('.lead-wrapper').find('input, select, textarea'), function () {
                $(this).attr('disabled', true);
                if ($(this).is('select')) {
                    $(this).selectpicker('refresh');
                }
            });
        });
    </script>
<?php } ?>
<script>
    function changecontactname() {
        var alname = $('#lead_form .dropdown.bootstrap-select.bs3.dropup #name');
        $('.dropdown.bootstrap-select.bs3.dropup').attr("style", "width: 100%;");
        var stext = $('#lead_form #name option:selected').attr("data-name");

        var title = $('#lead_form #name option:selected').attr("data-title");
        if (title != '' && $('#lead_form #title').length > 0) {
            $('#lead_form  #title').val(title).attr('readonly', 'readonly');
        } else {
            $('#lead_form  #title').removeAttr('readonly');
        }
        var email = $('#lead_form #name option:selected').attr("data-email");
        if (email != '' && $('#lead_form #email').length > 0) {
            $('#lead_form  #email').val(email).attr('readonly', 'readonly');
        } else {
            $('#lead_form  #email').removeAttr('readonly');
        }
        var phonenumber = $('#lead_form #name option:selected').attr("data-phonenumber");
        if (phonenumber != '' && $('#lead_form #phonenumber').length > 0) {
            $('#lead_form  #phonenumber').val(phonenumber).attr('readonly', 'readonly');
        } else {
            $('#lead_form  #phonenumber').removeAttr('readonly');
        }
    }
    $(function () {

        if ($('#status').length > 0) {
            $('.form_status .selectpicker').addClass("formstatus");
        }
        if ($('#teamleader').length > 0) {
            $('.form_teamleader .selectpicker').addClass("formteamleader");
        }
        if ($('#assigned').length > 0) {
            $('.form_assigned .selectpicker').addClass("formassigned");
        }
        $('#pipeline_id').change(function () {
            $('.formstatus').selectpicker('destroy');
            $('.formstatus').html('').selectpicker('refresh');

            $('.formteamleader').selectpicker('destroy');
            $('.formteamleader').html('').selectpicker('refresh');

            $('.formassigned').selectpicker('destroy');
            $('.formassigned').html('').selectpicker('refresh');

            var pipeline_id = $('#pipeline_id').val();
            $.ajax({
                url: admin_url + 'leads/changepipeline',
                type: 'POST',
                data: {'pipeline_id': pipeline_id},
                dataType: 'json',
                success: function success(result) {
                    $('.formstatus').selectpicker('destroy');
                    $('.formstatus').html(result.statuses).selectpicker('refresh');

                    $('.formteamleader').selectpicker('destroy');
                    $('.formteamleader').html(result.teamleaders).selectpicker('refresh');

                    $('.formassigned').selectpicker('destroy');
                    $('.formassigned').html(result.teammembers).selectpicker('refresh');


                    if (result.contacts && result.contacts.length > 0) {
                        var alname = $('#lead_form #name');
                        var select = $("<select></select>").attr("id", "name").attr("name", "name").attr("style", "width: 100%;").attr("onchange", "changecontactname()");
                        $.each(result.contacts, function (index, json) {
                            select.append($("<option>" + json.firstname + " " + json.lastname + "</option>").attr("value", json.id)
                                    .attr("data-title", json.title)
                                    .attr("data-email", json.email)
                                    .attr("data-name", json.firstname + " " + json.lastname)
                                    .attr("data-phonenumber", json.phonenumber)
                                    .text(json.text));
                        });
                        alname.parent().append(select);
                        alname.hide().val(result.firstname + ' ' + result.lastname);
                        $('#lead_form  #name[name=selValue]').val(result.country);
                        $('#lead_form  #name').selectpicker('refresh');
                        alname.attr('disabled', 'disabled');
                        changecontactname();
                    } else {
                        $('#lead_form .dropdown.bootstrap-select.bs3.dropup #name').selectpicker('refresh').attr('disabled', 'disabled').hide();
                        $('.dropdown.bootstrap-select.bs3.dropup').hide();
                        $('#lead_form  #name').removeAttr('disabled').show();

                        if (result.firstname && $('#lead_form  #name').length > 0) {
                            $('#lead_form #name').val(result.firstname + ' ' + result.lastname).attr('readonly', 'readonly');
                        } else {
                            $('#lead_form  #name').removeAttr('readonly').removeAttr('disabled');
                        }
                    }
                    
                    if (result.client_id && $('#lead_form  #client_id').length > 0) {
                        $('#lead_form  #client_id').val(result.client_id).attr('readonly', 'readonly');
                    } else {
                        $('#lead_form  #client_id').removeAttr('readonly');
                    }
                    
                    if (result.address && $('#lead_form  #address').length > 0) {
                        $('#lead_form  #address').val(result.address).attr('readonly', 'readonly');
                    } else {
                        $('#lead_form  #address').removeAttr('readonly');
                    }


                    if (result.city && $('#lead_form #city').length > 0) {
                        $('#lead_form  #city').val(result.city).attr('readonly', 'readonly');
                    } else {
                        $('#lead_form  #city').removeAttr('readonly');
                    }


                    if (result.state && $('#lead_form #state').length > 0) {
                        $('#lead_form  #state').val(result.state).attr('readonly', 'readonly');
                    } else {
                        $('#lead_form  #state').removeAttr('readonly');
                    }

                    if (result.website && $('#lead_form #website').length > 0) {
                        $('#lead_form  #website').val(result.website).attr('readonly', 'readonly');
                    } else {
                        $('#lead_form  #website').removeAttr('readonly');
                    }

                    if (result.country && $('#lead_form #country').length > 0) {
                        $('#lead_form  #country').val(result.country).attr('readonly', 'readonly');
                        $('#lead_form  #country[name=selValue]').val(result.country);
                        $('#lead_form  #country').selectpicker('refresh');
                    } else {
                        $('#lead_form  #country').removeAttr('readonly');
                    }

                    if (result.phonenumber && $('#lead_form #phonenumber').length > 0) {
                        $('#lead_form  #phonenumber').val(result.phonenumber).attr('readonly', 'readonly');
                    } else {
                        $('#lead_form  #phonenumber').removeAttr('readonly');
                    }

                    if (result.zip && $('#lead_form #zip').length > 0) {
                        $('#lead_form  #zip').val(result.zip).attr('readonly', 'readonly');
                    } else {
                        $('#lead_form  #zip').removeAttr('readonly');
                    }

                    if (result.company && $('#lead_form #company').length > 0) {
                        $('#lead_form  #company').val(result.company).attr('readonly', 'readonly');
                    } else {
                        $('#lead_form  #company').removeAttr('readonly');
                    }

                    if (result.default_language && $('#lead_form #default_language').length > 0) {
                        $('#lead_form  #default_language').val(result.default_language).attr('readonly', 'readonly');
                        $('#lead_form  #default_language[name=selValue]').val(result.default_language);
                        $('#lead_form  #default_language').selectpicker('refresh');
                    } else {
                        $('#lead_form  #default_language').removeAttr('readonly');
                    }

                    if (result.description && $('#lead_form #description').length > 0) {
                        $('#lead_form  #description').val(result.description).attr('readonly', 'readonly');
                    } else {
                        $('#lead_form  #description').removeAttr('readonly');
                    }

                }
            });
        });

    });
</script>