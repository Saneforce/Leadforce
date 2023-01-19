<?php 


function render_lead_activities($lead_id,$page=0)
{
    $CI = & get_instance();
    $CI->load->model('tasktype_model');
    $logs =$CI->leads_model->get_log_activities($lead_id,$page);
    if($logs){
        ob_start(); ?>
        <?php if($page ==0): ?>
        <ol class="timeline" id="lead_activities_wrapper">
        <?php endif; ?>
        <?php foreach($logs as $log): ?>
            <?php 
                $title ='';
                $date=date_create($log->added_at);
                $logged_at =date_format($date,"M d , Y h:i a");

                $log_data ='<span class=""><i class="fa fa-calendar" aria-hidden="true"></i> '.$logged_at.'</span> | <a class="" href="'.admin_url("profile/".$log->staff_id).'"><i class="fa fa-user" aria-hidden="true"></i> '.get_staff_full_name($log->staff_id).'</a>';
                $meta_data ='';
                $detailed_content ='';
                if($log->type =='lead'){
                    $icon ='<i class="fa fa-tty"></i>';
                    if($log->action =='addedfromemail'){
                        $title ='Lead created from email';
                    }elseif($log->action =='addedfromform'){
                        $title ='Lead created from web form';
                        $CI->db->where('id',$log->type_id);
                        $web_to_lead_form =$CI->db->get(db_prefix().'web_to_lead')->row();
                        if($web_to_lead_form){
                            $meta_data .='<span><a href="'.admin_url('leads/form/'.$web_to_lead_form->id).'">'.$web_to_lead_form->name.'</a></span>';
                        }

                    }else{
                        $title ='Lead manually created';
                    }
                    
                }elseif($log->type =='activity'){
                    if($log->action =='called'){
                        $icon ='<i class="fa fa-phone" aria-hidden="true"></i>';
                    }else{
                        $icon ='<i class="fa fa-tasks"></i>';
                    }
                    
                    $CI->db->where('id',$log->type_id);
                    $activity =$CI->db->get(db_prefix().'tasks')->row();
                    $taskassinged =$CI->tasks_model->get_task_assignees($activity->id);
                    // pre($activity);
                    $taskType =$CI->tasktype_model->getTasktype($activity->tasktype);
                    if(!$activity || !$taskassinged){
                        continue;
                    }else{
                        $taskassinged =$taskassinged[0];
                    }

                    $startdate=date_create($activity->startdate);
                    $activity_start_date =date_format($startdate,"M d , Y h:i a");

                    $activitystatusclass='';
                    if($activity->datefinished || $activity->status ==5){
                        $activitystatusclass="text-success";
                    }elseif($startdate < date_create()){
                        $activitystatusclass="text-danger";
                    }
                    
                    $title ='<i class="fa fa-circle '.$activitystatusclass.'" aria-hidden="true"></i>   <a class="'.$activitystatusclass.'" herf="#" onclick="edit_task('.$activity->id.'); return false;" style="cursor:pointer">'.$activity->name.'</a>';
                    if($log->action =='called'){
                        $CI->db->where('task_id',$activity->id);
                        $CI->db->order_by("id", "desc");
                        $call_log =$CI->db->get(db_prefix().'call_history')->row();
                        if($call_log){
                            $meta_data .='<audio id="myAudio" controls><source src="'.base_url('uploads/recordings/'.$call_log->filename).'"></audio><br>';
                        }
                    }
                    $meta_data .='<span><i class="fa fa-star" aria-hidden="true"></i>  '.$taskType->name.'</span> | <span><a class="" href="'.admin_url("profile/".$taskassinged['assigneeid']).'"><i class="fa fa-user" aria-hidden="true"></i> '.$taskassinged['full_name'].'</a></span> | <span class=""><i class="fa fa-calendar" aria-hidden="true"></i> '.$activity_start_date.'</span>';
                    if($activity->description){
                        $detailed_content ='<div id="activitycontent'.$activity->id.'">
                        <div class="card card-body mtop8">
                        <div class="comment note-bg">'.$activity->description.'</div>
                        </div>
                    </div>';
                    }
                    
                }elseif($log->type =='note'){
                    $note =$CI->misc_model->get_note($log->type_id);
                    if(!$note){
                        continue;
                    }
                    $icon ='<i class="fa fa-sticky-note"></i>';
                    $subject ='has added new  <i class="fa fa-sticky-note"></i> note';
                    $detailed_content ='<div class="comment note-bg">'.$note->description.'</div>';
                }elseif($log->type =='email'){
                    $CI->db->where('id',$log->type_id);
                    $email =$CI->db->get(db_prefix().'localmailstorage')->row();
                    if(!$email){
                        continue;
                    }
                    $mailid = json_decode($email->mail_to,true);
                    $title ='<a data-toggle="collapse" href="#mailcontent'.$email->id.'" role="button" aria-expanded="false" aria-controls="collapseExample">'.$email->subject.'</a>';
                    $meta_data .='<span><a href="mailto:'.$email->from_email.'"><i class="fa fa-envelope" aria-hidden="true"></i> '.$email->from_email.'</a></span> <span><i class="fa fa-angle-right" aria-hidden="true"></i></span> <span><a href="mailto:'.$mailid[0]['email'].'"><i class="fa fa-envelope" aria-hidden="true"></i> '.$mailid[0]['email'].'</a></span>';
                    $icon ='<i class="fa fa-envelope" aria-hidden="true"></i>';
                    $detailed_content ='<div class="collapse" id="mailcontent'.$email->id.'">
                    <div class="card card-body mtop8">
                      '.$email->body_plain.'
                    </div>
                  </div>';
                }elseif($log->type =='attachment'){
                    $CI->db->where('id',$log->type_id);
                    $file = $CI->db->get('files')->row();
                    if(!$file){
                        continue;
                    }
                    $attachment_url = site_url('download/file/lead_attachment/'.$file->id);
                    $path = get_upload_path_by_type('lead') . $file->rel_id . '/' . $file->file_name;
                    $filesize =convertToReadableSize(filesize($path));
                    if(!empty($file->external)){
                        $attachment_url = $file->external_link;
                    }
                    if($file->filetype =='image/jpeg' || $file->filetype =='image/gif' || $file->filetype =='image/png'){
                        $filetypeicon ='<i class="fa fa-picture-o" aria-hidden="true"></i>';
                    }elseif($file->filetype =='application/pdf'){
                        $filetypeicon ='<i class="fa fa-file-pdf-o" aria-hidden="true" style="color:#F40F02"></i>';
                    }elseif($file->filetype =='application/msword' || $file->filetype =='application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
                        $filetypeicon ='<i class="fa fa-file-word-o" aria-hidden="true" style="color:#00a2ed"></i>';
                    }elseif($file->filetype =='application/vnd.ms-excel' || $file->filetype =='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $file->filetype =='text/csv'){
                        $filetypeicon ='<i class="fa fa-file-excel-o" aria-hidden="true" style="color:#1D6F42"></i>';
                    }else{
                        $filetypeicon ='<i class="fa fa-file" aria-hidden="true"></i>';
                    }
                    $title ='<a href="'.$attachment_url.'">'.$filetypeicon.' '.$file->file_name.'</a>';
                    $meta_data .='<span>'.$filesize.'</span>';
                    $icon ='<i class="fa fa-paperclip"></i>';

                    
                }else{
                    continue;
                    $icon ='<i class="fa fa-tasks"></i>';
                    $subject ='';
                }
            ?>
            <li class="timeline-item">
                <span class="timeline-item-icon | faded-icon">
                    <?php echo $icon ?>
                </span>
                <div class="timeline-item-wrapper">
                    <div class="timeline-item-description mtop8 font-medium-xs">
                        <?php echo $log_data ?>
                    </div>
                    <div class="timeline-item-title">
                        <h4 class="mtop0"><?php echo $title ?></h4>
                    </div>
                    <div class="timeline-item-description font-medium-xs">
                        <?php echo $meta_data ?>
                    </div>
                    <?php echo $detailed_content ?>
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

function convertToReadableSize($size){
    $base = log($size) / log(1024);
    $suffix = array("", "KB", "MB", "GB", "TB");
    $f_base = floor($base);
    return round(pow(1024, $base - floor($base)), 1) . " ".$suffix[$f_base];
  }