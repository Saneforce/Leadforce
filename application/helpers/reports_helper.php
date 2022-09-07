<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Format task priority based on passed priority id
 * @param  mixed $id
 * @return string
 */
function get_req_val($req_val,$sel_val,$s_val,$d_val,$key,$all_val,$out_type){
	$CI		= & get_instance();
	$cur_id12 = '';
	if(!empty($_REQUEST['cur_id12'])){
		$cur_id12 = '_edit_'.$_REQUEST['cur_id12'];
	}
	if($out_type == 'deal'){
		$filters	=	$CI->session->userdata('filters'.$cur_id12);
		$filters1	=	$CI->session->userdata('filters1'.$cur_id12);
		$filters2	=	$CI->session->userdata('filters2'.$cur_id12);
		$filters3	=	$CI->session->userdata('filters3'.$cur_id12);
		$filters4	=	$CI->session->userdata('filters4'.$cur_id12);
	}
	else if($out_type == 'task'){
		$filters	=	$CI->session->userdata('activity_filters'.$cur_id12);
		$filters1	=	$CI->session->userdata('activity_filters1'.$cur_id12);
		$filters2	=	$CI->session->userdata('activity_filters2'.$cur_id12);
		$filters3	=	$CI->session->userdata('activity_filters3'.$cur_id12);
		$filters4	=	$CI->session->userdata('activity_filters4'.$cur_id12);
	}
	$is_sel		=	($filters1[$req_val-1]=='is')?'selected':'';
	$is_not		=	($filters1[$req_val-1]=='is_not')?'selected':'';
	$is_any		=	($filters1[$req_val-1]=='is_any_of')?'selected':'';
	$is_emp		=	($filters1[$req_val-1]=='is_empty')?'selected':'';
	$is_nemp	=	($filters1[$req_val-1]=='is_not_empty')?'selected':'';
	$is_more	=	($filters1[$req_val-1]=='is_more_than')?'selected':'';
	$is_less	=	($filters1[$req_val-1]=='is_less_than')?'selected':'';
	$req_out = '';
	$req_disp	=	($filters1[$req_val-1]=='is_empty' || $filters1[$req_val-1]=='is_not_empty')?'style="display:none"':'';
	$req_mul	= ($filters1[$req_val-1]=='is_any_of')?'multiple':'';
	$req_clas	= ($filters1[$req_val-1]=='is_empty' || $filters1[$req_val-1]=='is_not_empty')?'w_88':'';
	$req_marg	= ($filters1[$req_val-1]=='is_empty' || $filters1[$req_val-1]=='is_not_empty')?'-5px':'-25px;';
	if($sel_val == 'select'){ 
		if (str_contains($filters2[$req_val-1], ',')) {
			$req_out .= '<input type="hidden" value="'.$filters2[$req_val-1].'" id="year_val_'.$req_val.'">';
		}
		else{
			$req_out .= '<input type="hidden" value="" id="year_val_'.$req_val.'">';
		}
	}
	else{
		$req_out .= '<input type="hidden" value="" id="year_val_'.$req_val.'">';
	}
	if($sel_val == 'select' && $key == ''){
		$req_out .= '<div class="col-md-12"><div class="col-md-5 '.$req_clas.'" id="1_'.$req_val.'_filter">';
		$req_out .= '<select data-live-search="false" data-width="100%" class="ajax-search selectpicker" id="filter_option_'.$req_val.'" tabindex="-98" onchange="check_filter(this)" >';
		$req_out .= '<option value="is" '.$is_sel.'>Is</option>';
		$req_out .= '<option value="is_not" '.$is_not.'>Is Not</option>';
		$req_out .= '<option value="is_any_of" '.$is_any.'>Is Any Of</option>';
		$req_out .= '<option value="is_empty" '.$is_emp.'>Is Empty</option>';
		$req_out .= '<option value="is_not_empty" '.$is_nemp.'>Is Not Empty</option>';
		$req_out .= '</select></div>';
		$req_out .= '<div class="col-md-6" id="2_'.$req_val.'_filter" '.$req_disp.'><div class="col-md-12"><select data-live-search="true" data-width="100%" class="ajax-search selectpicker" data-none-selected-text="Nothing selected" tabindex="-98" data-action-box="true" id="year_'.$req_val.'" '.$req_mul.'  name="filter_'.$filters[$req_val-1].'[]" >';
		if (str_contains($d_val, ',')) {
			$d_all_vals = explode(',',$d_val);
			if(!empty($all_val)){
				foreach($all_val as $val1){
					$ch_sel = ($filters2[$req_val-1]==$val1[$s_val])?"selected":"";
					$display_val = '';
					for($i = 0;$i< count($d_all_vals);$i++){
						$display_val .= $val1[$d_all_vals[$i]].' ';
					}
					$display_val = rtrim($display_val," ");
					if (str_contains($filters2[$req_val-1], ',')) { 
						$ch_filters = explode(',',$filters2[$req_val-1]);
						$ch_sel = '';
					}
					$req_out .= '<option value="'.$val1[$s_val].'" '.$ch_sel.'>'.$display_val.'</option>';
				}
			}
		}else{
			if(!empty($all_val) && is_array($all_val) ){
				foreach($all_val as $val1){
					if(!empty($val1[$s_val]) && !empty($val1[$d_val])){
						$ch_sel = ($filters2[$req_val-1]==$val1[$s_val])?"selected":"";
						if (str_contains($filters2[$req_val-1], ',')) { 
							$ch_filters = explode(',',$filters2[$req_val-1]);
							$ch_sel = '';
						}
						$req_out .= '<option value="'.$val1[$s_val].'" '.$ch_sel.'>'.$val1[$d_val].'</option>';
					}
				}
			}
		}
		$del_val ="'".$req_val."'";
		$req_out .= '</select></div></div><div class="col-md-1" >
					<a href="javascript:void(0);" onclick="del_filter('.$del_val.')" style="margin-left:'.$req_marg.';"><i class="fa fa-trash" style="color:red;font-size: 20px;margin-top: 5px;" title="'._l('delete').'"></i></a>
				</div>';
		$req_out .= '<div>';
		
	}
	else if($sel_val == 'select' && $key == 'key'){
		$req_out .= '<div class="col-md-12"><div class="col-md-5 '.$req_clas.'" id="1_'.$req_val.'_filter">';
			$req_out .= '<select data-live-search="false" data-width="100%" class="ajax-search selectpicker" id="filter_option_'.$req_val.'" tabindex="-98" onchange="check_filter(this)">';
			$req_out .= '<option value="is" '.$is_sel.'>Is</option>';
			$req_out .= '<option value="is_not" '.$is_not.'>Is Not</option>';
			$req_out .= '<option value="is_any_of" '.$is_any.'>Is Any Of</option>';
			$req_out .= '<option value="is_empty" '.$is_emp.'>Is Empty</option>';
			$req_out .= '<option value="is_not_empty" '.$is_nemp.'>Is Not Empty</option>';
			$req_out .= '</select></div>';
			$req_out .= '<div class="col-md-6" id="2_'.$req_val.'_filter"  '.$req_disp.'><div class="col-md-12"><select data-live-search="true" data-width="100%" class="ajax-search selectpicker" data-none-selected-text="Nothing selected" tabindex="-98" id="year_'.$req_val.'"  '.$req_mul.' name="filter_'.$filters[$req_val-1].'[]">';
			if(!empty($all_val)){
				foreach($all_val as $key => $val1){
					if (str_contains($filters2[$req_val-1], ',')) {
						$ch_vals = explode(',',$filters2[$req_val-1]);
						$ch_sel = '';
					}else{
						$ch_sel = ($filters2[$req_val-1]==$key)?"selected":"";
					}
					
					$req_out .= '<option value="'.$key.'" '.$ch_sel.'>'.$val1.'</option>';
				}
			}
			$del_val ="'".$req_val."'";
			$req_out .= '</select></div></div><div class="col-md-1" >
					<a href="javascript:void(0);" onclick="del_filter('.$del_val.')" style="margin-left:'.$req_marg.';"><i class="fa fa-trash" style="color:red;font-size: 20px;margin-top: 5px;" title="'._l('delete').'"></i></a>
				</div>';
			$req_out .= '<div>';
	}
	else if($sel_val == 'text'){
		$req_out .= '<div class="col-md-12"><div class="col-md-5" id="1_'.$req_val.'_filter">';
		$req_out .= '<select data-live-search="false" data-width="100%" class="ajax-search selectpicker" id="filter_option_'.$req_val.'" tabindex="-98" onchange="check_filter(this)">';
		$req_out .= '<option value="is" '.$is_sel.'>Is</option>';
		$req_out .= '<option value="is_not" '.$is_not.'>Is Not</option>';
		$req_out .= '<option value="is_any_of" '.$is_any.'>Is Any Of</option>';
		$req_out .= '<option value="is_empty" '.$is_emp.'>Is Empty</option>';
		$req_out .= '<option value="is_not_empty" '.$is_nemp.'>Is Not Empty</option>';
		$req_out .= '</select></div>';
		$req_out .= '<div class="col-md-6" id="2_'.$req_val.'_filter"  '.$req_disp.'><div class="col-md-12"><input type="text" class="form-control" id="year_'.$req_val.'" value="'.$filters2[$req_val-1].'" name="filter_'.$filters[$req_val-1].'[]">';
		$del_val ="'".$req_val."'";
		$req_out .= '</div></div><div class="col-md-1" >
					<a href="javascript:void(0);" onclick="del_filter('.$del_val.')" style="margin-left:-26px;"><i class="fa fa-trash" style="color:red;font-size: 20px;margin-top: 5px;" title="'._l('delete').'"></i></a>
				</div>';
		$req_out .= '<div>';
	}
	else if($sel_val == 'number'){
		$req_out .= '<div class="col-md-12"><div class="col-md-5" id="1_'.$req_val.'_filter">';
		$req_out .= '<select data-live-search="false" data-width="100%" class="ajax-search selectpicker" id="filter_option_'.$req_val.'" tabindex="-98" onchange="check_filter(this)">';
		$req_out .= '<option value="is_more_than" '.$is_more.'>Is More Than</option>';
		$req_out .= '<option value="is_less_than" '.$is_less.'>Is Less Than</option>';
		$req_out .= '<option value="is_empty" '.$is_emp.'>Is Empty</option>';
		$req_out .= '<option value="is_not_empty" '.$is_nemp.'>Is Not Empty</option>';
		$req_out .= '</select></div>';
		$req_out .= '<div class="col-md-6" id="2_'.$req_val.'_filter"  '.$req_disp.'><div class="col-md-12"><input type="number" class="form-control" id="year_'.$req_val.'" value="'.$filters2[$req_val-1].'" name="filter_'.$filters[$req_val-1].'[]">';
		$del_val ="'".$req_val."'";
		$req_out .= '</div></div><div class="col-md-1" >
					<a href="javascript:void(0);" onclick="del_filter('.$del_val.')" style="margin-left:-26px;"><i class="fa fa-trash" style="color:red;font-size: 20px;margin-top: 5px;" title="'._l('delete').'"></i></a>
				</div>';
		$req_out .= '<div>';
	}
	else if($sel_val == 'date'){
		$this_yr	=	($filters2[$req_val-1]=='this_year')?'selected':'';
		$last_yr	=	($filters2[$req_val-1]=='last_year')?'selected':'';
		$next_yr	=	($filters2[$req_val-1]=='next_year')?'selected':'';
		
		$this_mn	=	($filters2[$req_val-1]=='this_month')?'selected':'';
		$last_mn	=	($filters2[$req_val-1]=='last_month')?'selected':'';
		$next_mn	=	($filters2[$req_val-1]=='next_month')?'selected':'';
		
		$this_w		=	($filters2[$req_val-1]=='this_week')?'selected':'';
		$last_w		=	($filters2[$req_val-1]=='last_week')?'selected':'';
		$next_w		=	($filters2[$req_val-1]=='next_week')?'selected':'';
		
		$today		=	($filters2[$req_val-1]=='today')?'selected':'';
		$yesterday	=	($filters2[$req_val-1]=='yesterday')?'selected':'';
		$tomorrow	=	($filters2[$req_val-1]=='tomorrow')?'selected':'';
		
		$cus_pr		=	($filters2[$req_val-1]=='custom_period')?'selected':'';
		$ch_val =  $filters1[$req_val-1];
		$req_out .= '<div class="col-md-12"><div class="col-md-2" id="1_'.$req_val.'_filter">';
		$req_out .= '<select data-live-search="false" data-width="100%" class="ajax-search selectpicker" id="filter_option_'.$req_val.'" tabindex="-98" onchange="check_filter(this)">';
		$req_out .= '<option value="is" '.$is_sel.'>Is</option>';
		$req_out .= '<option value="is_empty" '.$is_emp.'>Is Empty</option>';
		$req_out .= '<option value="is_not_empty" '.$is_nemp.'>Is Not Empty</option>';
		$req_out .= '</select></div>';
		$req_out .= '<div class="col-md-3" id="2_'.$req_val.'_filter"  '.$req_disp.'><select data-live-search="false" data-width="100%" class="ajax-search selectpicker" data-none-selected-text="Nothing selected" tabindex="-98" id="year_'.$req_val.'" onchange="change_2_filter(this)" name="filter_'.$filters[$req_val-1].'[]">';
		$req_out .= '<option value="this_year" '.$this_yr.'>This Year</option>
					<option value="last_year" '.$last_yr.' >Last Year</option>
					<option value="next_year" '.$next_yr.' >Next Year</option>
					<option value="this_month" '.$this_mn.'>This Month</option>
					<option value="last_month" '.$last_mn.' >Last Month</option>
					<option value="next_month" '.$next_mn.' >Next Month</option>
					<option value="this_week" '.$this_w.'>This Week</option>
					<option value="last_week" '.$last_w.' >Last Week</option>
					<option value="next_week" '.$next_w.' >Next Week</option>
					<option value="today" '.$today.'>Today</option>
					<option value="yesterday" '.$yesterday.' >Yesterday</option>
					<option value="tomorrow" '.$tomorrow.' >Tomorrow</option>
					<option value="custom_period" '.$cus_pr.'>Custom Period</option>';
		$req_out .= '</select></div>';
		$req_out .= '<div class="col-md-7"><div class="col-md-5" id="'.$req_val.'_3_filter"  '.$req_disp.'><input type="text" class="form-control" id="start_date_edit_'.$req_val.'" value="'.$filters3[$req_val-1].'" name="filter_4[]"></div>';
		$req_out .= '<div class="col-md-5" id="'.$req_val.'_4_filter"  '.$req_disp.'><input type="text" class="form-control" id="end_date_edit_'.$req_val.'" value="'.$filters4[$req_val-1].'" name="filter_5[]" ></div>';
		$del_val ="'".$req_val."'";
		$req_out .= '<div><div class="col-md-2" >
					<a href="javascript:void(0);" onclick="del_filter('.$del_val.')"  style="margin-left:-5px;"><i class="fa fa-trash" style="color:red;font-size: 20px;margin-top: 5px;" title="'._l('delete').'"></i></a>
				</div></div>';
	}
	return $req_out;
}