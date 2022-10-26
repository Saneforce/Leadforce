<?php

function approve_deal($deal_id)
{
    $CI = &get_instance();
    $CI->deal_approval_workflow->approveDeal($deal_id);
    return true;
}

function approvalFlowTree($approval_history ,$title ='Approval Status')
{ 
    $CI =&get_instance();
    
?>
    <p class="project-info bold font-size-14"><?php echo $title ?></p>
    <div class="activity-feed">
        <?php foreach ($approval_history as $history_key => $history) :
            $currentHistory = $approval_history[$history_key];
            if ($history->status == 1) {
                $approval_status = 'approved';
            } else {
                $approval_status = 'rejected';
            }
            $currentLevelStaff = $CI->staff_model->get($history->approved_by);
        ?>

            <div class="feed-item <?php echo ($approval_status == 'approved') ? 'approved-status' : 'pending-status'; ?>">
                <div class="row">
                    <div class="col-md-8">

                        <?php if ($approval_status == 'approved') : ?>
                            <div class="date"><span class="text-has-action text-success" data-toggle="tooltip" data-title="<?php echo _dt($currentHistory->approved_at); ?>" data-original-title="" title="">Approved - <?php echo time_ago($currentHistory->approved_at); ?></span></div>
                        <?php elseif ($approval_status == 'rejected') : ?>
                            <div class="date"><span class="text-has-action text-danger" data-toggle="tooltip" data-title="<?php echo _dt($currentHistory->approved_at); ?>" data-original-title="" title="">Rejected - <?php echo time_ago($currentHistory->approved_at); ?></span></div>
                        <?php else : ?>
                            <div class="date">Pending</div>
                        <?php endif; ?>

                        <div class="text">
                            <?php if ($currentLevelStaff) : ?>
                                <div style="display: flex;">
                                    <div>
                                        <a href="<?php echo admin_url('profile/' . $currentLevelStaff->staffid) ?>"><?php echo staff_profile_image($currentLevelStaff->staffid, array('staff-profile-image-small', 'media-object')); ?></a>
                                    </div>
                                    <div>
                                        <p class="mbot10 no-mtop"><?php echo $currentLevelStaff->full_name; ?></p>
                                        <p class="text-muted"><?php echo $currentLevelStaff->designation_name ?></p>
                                        <?php if ($approval_status == 'approved') : ?>
                                            <?php if($currentHistory->remarks):?>
                                            <p class="mbot10 no-mtop"><?php echo _l('remarks') ?> : <?php echo $currentHistory->remarks ?></p>
                                            <?php endif; ?>
                                        <?php elseif ($approval_status == 'rejected') : ?>
                                            <?php $reason =$CI->DealRejectionReasons_model->getDealRejectionReasonsbyId($currentHistory->reason);
                                            if($reason):?>
                                            <p class="mbot10 no-mtop text-danger"><?php echo _l('reason') ?> : <?php echo $reason->name ?></p>
                                            <?php endif; ?>
                                            <p class="mbot10 no-mtop"><?php echo _l('remarks') ?> : <?php echo $currentHistory->remarks ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>


                            <?php else : ?>
                                <p class="mtop10 no-mbot">Auto approval</b></p>
                            <?php endif; ?>
                        </div>

                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <hr class="hr-10">
                    </div>
                </div>
            </div>
            <?php if($history->status==0 && isset($approval_history[$history_key+1]) ){?>
                </div>
                <div class="activity-feed">
            <?php }?>
        <?php endforeach; ?>
    </div>
<?php
}
