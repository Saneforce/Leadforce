<?php
function approve_deal($deal_id)
{
    $CI = &get_instance();
    $CI->deal_approval_workflow->approveDeal($deal_id);
    return true;
}