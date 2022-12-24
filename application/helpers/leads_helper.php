<?php

defined('BASEPATH') or exit('No direct script access allowed');

hooks()->add_action('app_admin_head', 'leads_app_admin_head_data');

function leads_app_admin_head_data()
{
    ?>
    <script>
        var leadUniqueValidationFields = <?php echo json_decode(json_encode(get_option('lead_unique_validation'))); ?>;
        var leadAttachmentsDropzone;
    </script>
    <link rel="stylesheet" type="text/css"href="<?php echo base_url('assets/plugins/jquery-comments/css/jquery-comments.css'); ?>">
    <link rel="stylesheet" type="text/css"href="<?php echo base_url('assets/plugins/gantt/css/style.css'); ?>">
    <?php
    $CI = &get_instance();
        $CI->app_scripts->add('jquery-comments-js', 'assets/plugins/jquery-comments/js/jquery-comments.min.js', 'admin', ['vendor-js']);
        $CI->app_scripts->add('jquery-gantt-js', 'assets/plugins/gantt/js/jquery.fn.gantt.min.js', 'admin', ['vendor-js']);

}

/**
 * Check if the user is lead creator
 * @since  Version 1.0.4
 * @param  mixed  $leadid leadid
 * @param  mixed  $staff_id staff id (Optional)
 * @return boolean
 */

function is_lead_creator($lead_id, $staff_id = '')
{
    if (!is_numeric($staff_id)) {
        $staff_id = get_staff_user_id();
    }

    return total_rows(db_prefix() . 'leads', [
        'addedfrom' => $staff_id,
        'id'        => $lead_id,
    ]) > 0;
}

/**
 * Lead consent URL
 * @param  mixed $id lead id
 * @return string
 */
function lead_consent_url($id)
{
    return site_url('consent/l/' . get_lead_hash($id));
}

/**
 * Lead public form URL
 * @param  mixed $id lead id
 * @return string
 */
function leads_public_url($id)
{
    return site_url('forms/l/' . get_lead_hash($id));
}

/**
 * Get and generate lead hash if don't exists.
 * @param  mixed $id  lead id
 * @return string
 */
function get_lead_hash($id)
{
    $CI   = &get_instance();
    $hash = '';

    $CI->db->select('hash');
    $CI->db->where('id', $id);
    $lead = $CI->db->get(db_prefix() . 'leads')->row();
    if ($lead) {
        $hash = $lead->hash;
        if (empty($hash)) {
            $hash = app_generate_hash() . '-' . app_generate_hash();
            $CI->db->where('id', $id);
            $CI->db->update(db_prefix() . 'leads', ['hash' => $hash]);
        }
    }

    return $hash;
}

/**
 * Get leads summary
 * @return array
 */
function get_leads_summary()
{
    $CI = &get_instance();
    if (!class_exists('leads_model')) {
        $CI->load->model('leads_model');
    }
    $statuses = $CI->leads_model->get_status();

    $totalStatuses         = count($statuses);
    $has_permission_view   = has_permission('leads', '', 'view');
    $sql                   = '';
    $whereNoViewPermission = '(addedfrom = ' . get_staff_user_id() . ' OR assigned=' . get_staff_user_id() . ' OR is_public = 1)';

    $statuses[] = [
        'lost'  => true,
        'name'  => _l('lost_leads'),
        'color' => '',
    ];

    $statuses[] = [
        'junk'  => true,
        'name'  => _l('junk_leads'),
        'color' => '',
    ];

    foreach ($statuses as $status) {
        $sql .= ' SELECT COUNT(*) as total';
        $sql .= ' FROM ' . db_prefix() . 'leads';

        if (isset($status['lost'])) {
            $sql .= ' WHERE lost=1';
        } elseif (isset($status['junk'])) {
            $sql .= ' WHERE junk=1';
        } else {
            $sql .= ' WHERE status=' . $status['id'];
        }
        if (!$has_permission_view) {
            $sql .= ' AND ' . $whereNoViewPermission;
        }
        $sql .= ' UNION ALL ';
        $sql = trim($sql);
    }

    $result = [];

    // Remove the last UNION ALL
    $sql    = substr($sql, 0, -10);
    $result = $CI->db->query($sql)->result();

    if (!$has_permission_view) {
        $CI->db->where($whereNoViewPermission);
    }

    $total_leads = $CI->db->count_all_results(db_prefix() . 'leads');

    foreach ($statuses as $key => $status) {
        if (isset($status['lost']) || isset($status['junk'])) {
            $statuses[$key]['percent'] = ($total_leads > 0 ? number_format(($result[$key]->total * 100) / $total_leads, 2) : 0);
        }

        $statuses[$key]['total'] = $result[$key]->total;
    }

    return $statuses;
}

/**
 * Render lead status select field with ability to create inline statuses with + sign
 * @param  array  $statuses         current statuses
 * @param  string  $selected        selected status
 * @param  string  $lang_key        the label of the select
 * @param  string  $name            the name of the select
 * @param  array   $select_attrs    additional select attributes
 * @param  boolean $exclude_default whether to exclude default Client status
 * @return string
 */
function render_leads_status_select($statuses, $selected = '', $lang_key = '', $name = 'status', $select_attrs = [], $exclude_default = false)
{
    foreach ($statuses as $key => $status) {
        if ($status['isdefault'] == 1) {
            if ($exclude_default == false) {
                $statuses[$key]['option_attributes'] = ['data-subtext' => _l('leads_converted_to_client')];
            } else {
                unset($statuses[$key]);
            }

            break;
        }
    }

    if (is_admin() || get_option('staff_members_create_inline_lead_status') == '1') {
        return render_select_with_input_group($name, $statuses, ['id', 'name'], $lang_key, $selected, '<a href="#" onclick="new_lead_status_inline();return false;" class="inline-field-new"><i class="fa fa-plus"></i></a>', $select_attrs);
    }

    return render_select($name, $statuses, ['id', 'name'], $lang_key, $selected, $select_attrs);
}

/**
 * Render lead source select field with ability to create inline source with + sign
 * @param  array   $sources         current sourcees
 * @param  string  $selected        selected source
 * @param  string  $lang_key        the label of the select
 * @param  string  $name            the name of the select
 * @param  array   $select_attrs    additional select attributes
 * @return string
 */
function render_leads_source_select($sources, $selected = '', $lang_key = '', $name = 'source', $select_attrs = [])
{
    if (is_admin() || get_option('staff_members_create_inline_lead_source') == '1') {
        echo render_select_with_input_group($name, $sources, ['id', 'name'], $lang_key, $selected, '<a href="#" onclick="new_lead_source_inline();return false;" class="inline-field-new"><i class="fa fa-plus"></i></a>', $select_attrs);
    } else {
        echo render_select($name, $sources, ['id', 'name'], $lang_key, $selected, $select_attrs);
    }
}

/**
 * Load lead language
 * Used in public GDPR form
 * @param  string $lead_id
 * @return string return loaded language
 */
function load_lead_language($lead_id)
{
    $CI = & get_instance();
    $CI->db->where('id', $lead_id);
    $lead = $CI->db->get(db_prefix() . 'leads')->row();

    // Lead not found or default language already loaded
    if (!$lead || empty($lead->default_language)) {
        return false;
    }

    $language = $lead->default_language;

    if (!file_exists(APPPATH . 'language/' . $language)) {
        return false;
    }

    $CI->lang->is_loaded = [];
    $CI->lang->language  = [];

    $CI->lang->load($language . '_lang', $language);
    if (file_exists(APPPATH . 'language/' . $language . '/custom_lang.php')) {
        $CI->lang->load('custom_lang', $language);
    }

    return true;
}


/**
 * Get project name by passed id
 * @param  mixed $id
 * @return string
 */
function get_lead_name_by_id($id)
{
    $CI      = & get_instance();
    $lead = $CI->app_object_cache->get('lead-name-data-' . $id);

    if (!$lead) {
        $CI->db->select('name');
        $CI->db->where('id', $id);
        $lead = $CI->db->get(db_prefix() . 'leads')->row();
        $CI->app_object_cache->add('lead-name-data-' . $id, $lead);
    }

    if ($lead) {
        return $lead->name;
    }

    return '';
}

function render_lead_activities($lead_id,$page=0)
{
    $CI = & get_instance();
    $logs =$CI->leads_model->get_log_activities($lead_id,$page);
    if($logs){
        ob_start(); ?>
        <?php if($page ==0): ?>
        <ol class="timeline" id="lead_activities_wrapper">
        <?php endif; ?>
        <?php foreach($logs as $log): ?>
            <?php 
                $message ='';
                if($log->type =='lead'){
                    $icon ='<i class="fa fa-tty"></i>';
                    $subject ='has created Lead';
                }elseif($log->type =='activity'){
                    $icon ='<i class="fa fa-tasks"></i>';
                    $CI->db->where('id',$log->type_id);
                    $activity =$CI->db->get(db_prefix().'tasks')->row();
                    if(!$activity){
                        continue;
                    }
                    $subject ='has created  <i class="fa fa-tasks"></i> activity';
                    $message ='<div class="comment">
                    <p class="text-muted no-mbot" style="padding:0;padding-bottom:5px">Title : <a herf="#" onclick="edit_task('.$activity->id.'); return false;" style="cursor:pointer">'.$activity->name.'</a></p>';
                    if($activity->description){
                        $message .='<p class="text-muted no-mbot" style="padding:0">Description : '.$activity->description.'</p>';
                    }
                    $message .='</div>';
                }elseif($log->type =='note'){
                    $note =$CI->misc_model->get_note($log->type_id);
                    if(!$note){
                        continue;
                    }
                    $icon ='<i class="fa fa-sticky-note"></i>';
                    $subject ='has added new  <i class="fa fa-sticky-note"></i> note';
                    $message ='<div class="comment note-bg">'.$note->description.'</div>';
                }elseif($log->type =='email'){
                    $CI->db->where('id',$log->type_id);
                    $email =$CI->db->get(db_prefix().'localmailstorage')->row();
                    if(!$email){
                        continue;
                    }
                    $mailid = json_decode($email->mail_to,true);
                    $icon ='<i class="fa fa-envelope" aria-hidden="true"></i>';
                    $subject ='has sent  <i class="fa fa-envelope" aria-hidden="true"></i> email';
                    $message ='<div class="comment">
                    <p class="text-muted no-mbot" style="padding:0;padding-bottom:5px">To : <a href="mailto:'.$mailid[0]['email'].'">'.$mailid[0]['email'].'</a></p>
                    <p class="text-muted no-mbot" style="padding:0">Subject : '.$email->subject.'</p>
                    </div>';
                }elseif($log->type =='attachment'){
                    $CI->db->where('id',$log->type_id);
                    $file = $CI->db->get('files')->row();
                    if(!$file){
                        continue;
                    }
                    $attachment_url = site_url('download/file/lead_attachment/'.$file->id);
                    if(!empty($file->external)){
                        $attachment_url = $file->external_link;
                    }

                    $icon ='<i class="fa fa-paperclip"></i>';
                    $subject ='has created new  <i class="fa fa-paperclip"></i> attachment';
                    $message ='<div class="comment">
                        <div class="row">
                            <div class="col-md-1"><div class="document-icon-wrapper">';
                            if($file->filetype =='image/jpeg' || $file->filetype =='image/gif' || $file->filetype =='image/png'){
                                $message .='<i class="fa fa-picture-o" aria-hidden="true"></i>';
                            }elseif($file->filetype =='application/pdf'){
                                $message .='<i class="fa fa-file-pdf-o" aria-hidden="true" style="color:#F40F02"></i>';
                            }elseif($file->filetype =='application/msword' || $file->filetype =='application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
                                $message .='<i class="fa fa-file-word-o" aria-hidden="true" style="color:#00a2ed"></i>';
                            }elseif($file->filetype =='application/vnd.ms-excel' || $file->filetype =='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $file->filetype =='text/csv'){
                                $message .='<i class="fa fa-file-excel-o" aria-hidden="true" style="color:#1D6F42"></i>';
                            }else{
                                $message .='<i class="fa fa-file" aria-hidden="true"></i>';
                            }
                                
                            $message .='</div></div>
                            <div class="col-md-11"><p><a href="'.$attachment_url.'">'
                                .$file->file_name.
                            '</p></a></div>
                        </div>
                    </div>';
                }else{
                    continue;
                    $icon ='<i class="fa fa-tasks"></i>';
                    $subject ='';
                }

                $profile_icon =staff_profile_image($log->staff_id);
            ?>
            <li class="timeline-item">
                <span class="timeline-item-icon | faded-icon">
                    <?php echo $icon ?>
                </span>
                <div class="timeline-item-wrapper">
                    <div class="timeline-item-description">
                        <i class="avatar | small">
                            <?php echo $profile_icon ?>
                        </i>
                        <span><a href="<?php echo admin_url('profile/'.$log->staff_id); ?>"><?php echo get_staff_full_name($log->staff_id); ?></a> <?php echo $subject ?> - <time datetime="<?php echo _dt($log->added_at); ?>"><?php echo time_ago($log->added_at); ?></time></span>
                    </div>
                    <?php echo $message ?>
                </div>
                
            </li>
        <?php endforeach; ?>
    <?php if($page ==0): ?>
    </ol>
    <?php endif; ?>
    <?php $content = ob_get_clean(); 
    }else{
        $content =false;
    }
    return $content;
}