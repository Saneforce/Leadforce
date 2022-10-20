<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Format task priority based on passed priority id
 * @param  mixed $id
 * @return string
 */
function get_default_val($cur_val,$req_val,$type1){
	$CI		= & get_instance();
	$fields =  $CI->db->query("SELECT type,options FROM " . db_prefix() . "customfields where slug = '".$cur_val."' ")->row();
	if($fields->type == 'date_picker' || $fields->type == 'date_picker_time' || $fields->type == 'date_range'){
		$req_out = get_req_val($req_val,'date','','','','',$type1);
	}
	else if($fields->type == 'select' || $fields->type=='multiselect'){
		$req_array = array();
		if (str_contains($fields->options, ',')) { 
			$options = explode(',',$fields->options);
			foreach($options as $option1){
				$req_array[$option1] = $option1;
			}
		}
		else{
			$req_array[$fields->options] = $fields->options;
		}
		$req_out = get_req_val($req_val,'select','','','key',$req_array,$type1);
	}
	else if($fields->type == 'number'){
		$req_out = get_req_val($req_val,'number','','','','',$type1);
	}
	else{
		$req_out = get_req_val($req_val,'text','','','','',$type1);
	}
	return $req_out;
}
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
		if(!empty($this_yr) || (!empty($filters3[$req_val-1]) && !empty($filters4[$req_val-1]) )){
			$req_out .= '<div class="col-md-7"><div class="col-md-5" id="'.$req_val.'_3_filter"  '.$req_disp.'><input type="text" class="form-control" id="start_date_edit_'.$req_val.'" value="'.$filters3[$req_val-1].'" name="filter_4[]"></div>';
			$req_out .= '<div class="col-md-5" id="'.$req_val.'_4_filter"  '.$req_disp.'><input type="text" class="form-control" id="end_date_edit_'.$req_val.'" value="'.$filters4[$req_val-1].'" name="filter_5[]" ></div>';
		}
		else{
			$req_out .= '<div class="col-md-7"><div class="col-md-5" id="'.$req_val.'_3_filter"  '.$req_disp.'><input type="text" class="form-control" id="start_date_edit_'.$req_val.'" value="01-01-'.date('Y').'" name="filter_4[]"></div>';
			$req_out .= '<div class="col-md-5" id="'.$req_val.'_4_filter"  '.$req_disp.'><input type="text" class="form-control" id="end_date_edit_'.$req_val.'" value="31-12-'.date('Y').'" name="filter_5[]" ></div>';
		}
		$del_val ="'".$req_val."'";
		$req_out .= '<div><div class="col-md-2" >
					<a href="javascript:void(0);" onclick="del_filter('.$del_val.')"  style="margin-left:-5px;"><i class="fa fa-trash" style="color:red;font-size: 20px;margin-top: 5px;" title="'._l('delete').'"></i></a>
				</div></div>';
	}
	return $req_out;
}
function get_activity_filters($req_filters,$check_data=''){
	$CI			= 	& get_instance();
	$needed = get_tasks_need_fields();
	$needed['need_fields'][] = 'rel_type';
	$where 		= 	array();
	$req_cond	=	'';
	$filters	=	$req_filters['filters'];
	$filters1	=	$req_filters['filters1'];
	$filters2	=	$req_filters['filters2'];
	$filters3	=	$req_filters['filters3'];
	$filters4	=	$req_filters['filters4'];
	if(!empty($filters))
	{
		$i1 = 0;
		$s_group_by = '';
		$table = db_prefix().'filter';
		$custom_fields = get_table_custom_fields('tasks');
		$customs = array_column($custom_fields, 'slug');
		foreach($filters as $filter12){
			if ((!empty($needed['need_fields']) && in_array($filter12, $needed['need_fields'])) || in_array($filter12, $customs)){
				$check_cond = filter_cond($filters2[$i1]);
				$activity_vals 	= $CI->db->query("SELECT filter_name,filter_cond,filter_type,date_field,filter FROM ".$table." where filter_name = '".$filter12."' and filter_type= '".$filters1[$i1]."' and filter = 'activity' ")->result_array();
				if(!empty($activity_vals)){
					$cur_cond = $activity_vals[0]['filter_cond'];
					$cur_cond = str_replace('db_prefix()', db_prefix(), $cur_cond);
					if(($filters1[$i1]=='is' || $filters1[$i1]=='is_more_than' || $filters1[$i1]=='is_less_than' || $filters1[$i1]=='is_not') && $activity_vals[0]['date_field'] ==0){
						if($check_cond){
							$cur_cond = str_replace('$$cond1', "'".$filters2[$i1]."'", $cur_cond);
						}
						else{
							$cur_cond = '';
						}
					}
					if($filters1[$i1]=='is_any_of'){
						if($check_cond){
							$req_arrs = explode(',',$filters2[$i1]);
							$req_arr = '';
							if(!empty($req_arrs)){
								foreach($req_arrs as $req_arr1){
									$req_arr .= "'".$req_arr1."',";
								}
							}
							$req_arr = rtrim($req_arr,",");
							$cur_cond = str_replace('$$in_cond', $req_arr, $cur_cond);
						}
						else{
							$cur_cond = '';
						}
					}
					if (str_contains($cur_cond, '$$date1')) {
						$date1 = "'".date('Y-m-d',strtotime($filters3[$i1]))."'";
						$cur_cond = str_replace('$$date1', $date1, $cur_cond);
					}
					if (str_contains($cur_cond, '$$date2')) {
						$date2 = "'".date('Y-m-d',strtotime($filters4[$i1]))."'";
						
						$cur_cond = str_replace('$$date2', $date2, $cur_cond);
					}
					$req_cond .= $cur_cond;
					array_push($where, $cur_cond);
				}
				else if(in_array($filter12, $customs)){
					if($filters1[$i1]=='is'){
						if($check_cond ){
							$cur_cond = " AND ( ".db_prefix().'tasks'.".id in(SELECT cv.relid FROM ".db_prefix() ."customfieldsvalues cv,".db_prefix() ."customfields c where cv.value  = '".$filters2[$i1]."' and c.slug = '".$filter12."' and cv.fieldid = c.id) )";
							$req_cond .= $cur_cond;
							array_push($where, $cur_cond);
						}else{
							$cur_cond = " AND ( ".db_prefix().'tasks'.".id in(SELECT cv.relid FROM ".db_prefix() ."customfieldsvalues cv,".db_prefix() ."customfields c where value  > '".date('Y-m-d',strtotime($filters3[$i1]))."' AND value < '".date('Y-m-d',strtotime($filters4[$i1]))."' and c.slug = '".$filter12."' and cv.fieldid = c.id ) )";
							$req_cond .= $cur_cond;
							array_push($where, $cur_cond);
						}
					}
					else if($filters1[$i1]=='is_empty'){
						$cur_cond = " AND ( ".db_prefix().'tasks'.".id in(SELECT cv.relid FROM ".db_prefix() ."customfieldsvalues cv,".db_prefix() ."customfields c where (cv.value  = '' or cv.value = '0' or cv.value = '0000-00-00') and c.slug = '".$filter12."' and cv.fieldid = c.id) )";
						$req_cond .= $cur_cond;
						array_push($where, $cur_cond);
					}
					else if($filters1[$i1]=='is_not_empty'){
						$cur_cond = " AND ( ".db_prefix().'tasks'.".id in(SELECT cv.relid FROM ".db_prefix() ."customfieldsvalues cv,".db_prefix() ."customfields c where cv.value  != '' AND cv.value != '0' AND cv.value != '0000-00-00' AND cv.fieldto = 'tasks' and c.slug = '".$filter12."' and cv.fieldid = c.id) )";
						$req_cond .= $cur_cond;
						array_push($where, $cur_cond);
					}
					else if($filters1[$i1]=='is_not'){
						$cur_cond = " AND ( ".db_prefix().'tasks'.".id in(SELECT cv.relid FROM ".db_prefix() ."customfieldsvalues cv,".db_prefix() ."customfields c  where cv.value  != '".$filters2[$i1]."' and c.slug = '".$filter12."' and cv.fieldid = c.id) )";
						$req_cond .= $cur_cond;
						array_push($where, $cur_cond);
					}
					else if($filters1[$i1]=='is_any_of'  && $filters2[$i1]!=''){
						$req_arrs = explode(',',$filters2[$i1]);
						$req_arr = '';
						if(!empty($req_arrs)){
							foreach($req_arrs as $req_arr1){
								$req_arr .= "'".$req_arr1."',";
							}
						}
						$req_arr = rtrim($req_arr,",");
						$cur_cond = " AND ( ".db_prefix().'tasks'.".id in(SELECT cv.relid FROM ".db_prefix() ."customfieldsvalues cv,".db_prefix() ."customfields c where cv.value  in(".$req_arr.") and c.slug = '".$filter12."' and cv.fieldid = c.id ) )";
						$req_cond .= $cur_cond;
						array_push($where, $cur_cond);
					}
					else if($filters1[$i1]=='is_more_than' && $filters2[$i1]!=''){
						$cur_cond = " AND ( ".db_prefix().'tasks'.".id in(SELECT cv.relid FROM ".db_prefix() ."customfieldsvalues cv,".db_prefix() ."customfields c where value  > ".$filters2[$i1]." and c.slug = '".$filter12."' and cv.fieldid = c.id) )";
						$req_cond .= $cur_cond;
						array_push($where, $cur_cond);
					}
					else if($filters1[$i1]=='is_less_than'  && $filters2[$i1]!=''){
						$cur_cond = " AND ( ".db_prefix().'tasks'.".id in(SELECT cv.relid FROM ".db_prefix() ."customfieldsvalues cv,".db_prefix() ."customfields c where cv.value  < ".$filters2[$i1]." and c.slug = '".$filter12."' and cv.fieldid = c.id) )";
						$req_cond .= $cur_cond;
						array_push($where, $cur_cond);
					}
				}
				$i1++;
			}
		}
	}
	if(!empty($check_data)){
		$req_cond = str_replace('p.', db_prefix().'projects.', $req_cond);
	}
	return $req_cond;
}
function get_task_vals($fields,$fields1,$table,$qry_cond,$filters = array()){
	 $CI	= & get_instance();
	 $conds = get_activity_filters($filters);
	 $type_cond = '';
	 if(str_contains($filters['report_name'], 'Call Performance')){
		$type_cond = db_prefix().'tasks.tasktype = (select id from '.db_prefix().'tasktype where name="Call" and status ="Active" )';
	}
	if(str_contains($filters['report_name'], 'Email Performance')){
		$type_cond = db_prefix().'tasks.tasktype = (select id from '.db_prefix().'tasktype where name="E-mail" and status ="Active" )';
	}
	
	 if(!empty($qry_cond)){
		 if(!empty($type_cond)){
			 
			 $type_cond = 'AND '.$type_cond." AND " ;
		 }
		 else{
			$type_cond = " " ;
			if(!empty($filters)){
				$type_cond = " AND " ;
			}
			$qry_cond = ltrim($qry_cond,"and ");
			$qry_cond = ltrim($qry_cond,"AND ");
		 }
			if(empty($type_cond) && !empty($filters)){
				$conds = " AND " ;
			}
			$qry_cond = " where ".db_prefix()."tasks.id !='' ".$conds.$type_cond.$qry_cond;
	 }else{
			if(empty($type_cond) && !empty($filters)){
				$conds = " AND " ;
			}
		  $qry_cond = " where ".db_prefix()."tasks.id !='' ".$conds.$type_cond;
	 }
	 if(!empty($fields)){
		 $fields = $fields.",";
	 }
	  if(!empty($fields1)){
		 $fields1 = "".$fields1;
	 }
	 $my_staffids = $CI->staff_model->get_my_staffids();
	 if(!is_admin(get_staff_user_id()) && $my_staffids){
		 $table .= " ,".db_prefix()."task_assigned ta1 ";
		 $qry_cond .= " and ((ta1.taskid = ".db_prefix()."tasks.id and ta1.staffid in(" . implode(',',$my_staffids) . ") ) or ( OR ".db_prefix()."tasks.rel_id IN(SELECT ".db_prefix()."projects.id FROM ". db_prefix()."projects join ".db_prefix()."project_members  on ".db_prefix()."project_members.project_id = " .db_prefix()."projects.id WHERE ".db_prefix()."project_members.staff_id in (". implode(',',$my_staffids)."))))";
	 }
	 $CI			= & get_instance();
	
	 $task_vals 	= $CI->db->query("SELECT ".$fields."COUNT(DISTINCT IF(".db_prefix(). "tasks.status = '1',".db_prefix(). "tasks.id,NULL)) AS upcoming,COUNT(DISTINCT IF(".db_prefix(). "tasks.status = '2',".db_prefix(). "tasks.id,NULL)) AS overdue,COUNT(DISTINCT IF(".db_prefix(). "tasks.status = '3',".db_prefix(). "tasks.id,NULL)) AS today,COUNT(DISTINCT IF(".db_prefix(). "tasks.status = '4',".db_prefix(). "tasks.id,NULL)) AS in_progress,COUNT(DISTINCT IF(".db_prefix(). "tasks.status = '5',".db_prefix(). "tasks.id,NULL)) AS completed ".$fields1." FROM ".$table.$qry_cond)->result_array();
	return $task_vals;
 }
 function get_task_data($view_by,$cur_rows,$task_vals){
	$i = $upcoming = $today = $overdue  = $in_progress = $completed = $tot_cnt = $tot_val = $avg_task = $tot_avg  =0;
	$data = array();
	foreach($task_vals as $task_val1){
		$data[$i] = tasks_counts($task_val1['upcoming'],$task_val1['overdue'],$task_val1['today'],$task_val1['in_progress'],$task_val1['completed'],$task_val1['tot_val'],$view_by,$cur_rows,$task_val1);
		$upcoming		=	$upcoming + $task_val1['upcoming'];
		$overdue		=	$overdue + $task_val1['overdue'];
		$today			=	$today + $task_val1['today'];
		$in_progress	=	$in_progress + $task_val1['in_progress'];
		$completed		=	$completed + $task_val1['completed'];
		$tot_cnt=	$tot_cnt + $task_val1['upcoming'] + $task_val1['overdue'] + $task_val1['today'] + $task_val1['in_progress'] + $task_val1['completed'];
		$tot_val=	$tot_val + $task_val1['tot_val'];
		$tot_avg	=	$tot_avg + get_decimal($data[$i]['avg_task']);
		$i++;
	}
	$data[$i] = $avg_task = task_avg($upcoming,$overdue,$today,$in_progress,$completed,$tot_cnt,$tot_val,$view_by,$i,$tot_avg);
	$i++;
	$data[$i] = task_total($upcoming,$overdue,$today,$in_progress,$completed,$tot_cnt,$tot_val,$view_by,$tot_avg);
	
	return $data;
}
function task_avg($upcoming,$overdue,$today,$in_progress,$completed,$tot_cnt,$tot_val,$view_by,$num,$tot_avg)
{
	$data['upcoming']	= 	$data['overdue']	= 	$data['today'] = 	$data['in_progress']	= 	$data['completed']	= 	$data['total_cnt_task']	=  $data['total_val_task']	= $data['avg_task']		= 	$data['avg_tot'] =  0;
	if($tot_cnt>0){
		$data['upcoming']		= 	get_decimal($upcoming/$num);
		$data['overdue']		= 	get_decimal($overdue/$num);
		$data['today']			= 	get_decimal($today/$num);
		$data['in_progress']	= 	get_decimal($in_progress/$num);
		$data['completed']		= 	get_decimal($completed/$num);
		$data['total_cnt_task']	= 	get_decimal($tot_cnt/$num);
		$data['total_val_task']	= 	get_decimal($tot_val/$num);
		$data['avg_task']		=	get_decimal($tot_avg/$num);
		$data['avg_tot'] 		=	$data['avg_task'] + $data['avg_tot'];
	}
	$data[$view_by]	= 	$data['rows']	=	'Average';
	return $data;
}
function task_total($upcoming,$overdue,$today,$in_progress,$completed,$tot_cnt,$tot_val,$view_by,$tot_avg)
{
	$data['upcoming']		= 	get_decimal($upcoming);
	$data['overdue']		= 	get_decimal($overdue);
	$data['today']			= 	get_decimal($today);
	$data['in_progress']	= 	get_decimal($in_progress);
	$data['completed']		= 	get_decimal($completed);
	$data['total_cnt_task']	= 	$tot_cnt;
	$data['total_val_task']	= 	get_decimal($tot_val);
	$data['avg_task']		= 	0;
	if($tot_cnt>0){
		$data['avg_task']	=	get_decimal($tot_avg);
	}
	$data[$view_by]	= 	$data['rows']	=	'Total';
	return $data;
}
function get_join_task_tables(){
	$CI		= & get_instance();
	$join 	= array(db_prefix().'tasktype',db_prefix().'projects',db_prefix().'projects_status',db_prefix().'pipeline',db_prefix().'clients',db_prefix().'contacts');
	$join_cond = array(db_prefix().'tasktype  as '.db_prefix().'tasktype ON '.db_prefix().'tasktype.id = ' .db_prefix() . 'tasks.tasktype',db_prefix().'projects  as '.db_prefix().'projects ON '.db_prefix().'projects.id = ' .db_prefix() . 'tasks.rel_id AND ' .db_prefix() . 'tasks.rel_type ="project"',db_prefix().'projects_status  as '.db_prefix().'projects_status ON '.db_prefix().'projects_status.id = ' .db_prefix() . 'projects.status',db_prefix().'pipeline  as '.db_prefix().'pipeline ON '.db_prefix().'pipeline.id = ' .db_prefix() . 'projects.pipeline_id',db_prefix().'clients  as '.db_prefix().'clients ON '.db_prefix().'clients.userid = ' .db_prefix() . 'projects.clientid',db_prefix().'contacts  as '.db_prefix().'contacts ON ('.db_prefix().'contacts.id = ' .db_prefix() . 'tasks.contacts_id  OR (' .db_prefix() . 'tasks.rel_type ="contact" AND '.db_prefix().'contacts.id = ' .db_prefix() . 'tasks.rel_id) )');
	$report_task_list_column_order = (array)json_decode(get_option('report_task_list_column_order')); 
	$custom_fields = get_table_custom_fields('tasks');
       $customFieldsColumns= $locationCustomFields = $cus = [];
	foreach ($custom_fields as $key => $field) {
		$fieldtois= 'clients.userid';
		if($field['fieldto'] =='projects'){
			$fieldtois= 'projects.id';
		}elseif($field['fieldto'] =='contacts'){
			$fieldtois= 'contacts.id';
		}
		elseif($field['fieldto'] =='tasks'){
			$fieldtois= 'tasks.id';
		}
		if(isset($report_task_list_column_order[$field['slug']])){
			if($field['type'] =='location'){
				array_push($locationCustomFields, 'cvalue_' .$field['slug']);
			}
			$selectAs = 'cvalue_' .$field['slug'];
			array_push($customFieldsColumns, $selectAs);
			$cus[$field['slug']] =  'ctable_' . $key . '.value as ' . $selectAs;
			array_push($join, db_prefix().'customfieldsvalues as ctable_' . $key );
			array_push($join_cond, db_prefix().$fieldtois.' = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
		}
	}
	$fields = db_prefix().'tasks.id as id,
    '.db_prefix().'tasks.status as status,
    '.db_prefix().'tasks.name as task_name,
    '.db_prefix().'projects.name as project_name,
    '.db_prefix().'tasktype.name as tasktype,
    '.db_prefix().'contacts.firstname as project_contacts,
	'.db_prefix().'tasks.description as description,
	(SELECT GROUP_CONCAT(CONCAT(firstname," ",lastname) SEPARATOR ",") FROM '.db_prefix().'staff where staffid IN (select staffid from '.db_prefix().'task_assigned where taskid = '.db_prefix().'tasks.id)) as assignees,
	(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM '.db_prefix().'taggables JOIN '.db_prefix().'tags ON '.db_prefix().'taggables.tag_id = '.db_prefix().'tags.id WHERE rel_id = '.db_prefix().'tasks.id and rel_type="task" ORDER by tag_order ASC) as tags,priority,'.db_prefix().'projects.teamleader as p_teamleader,startdate,dateadded,datefinished,datemodified,rel_type,rel_id,contacts_id,tasktype as type_id,'.db_prefix().'projects_status.name as project_status,'.db_prefix().'pipeline.name as project_pipeline,'.db_prefix().'clients.company as company,'.db_prefix().'contacts.email as contact_email,'.db_prefix().'contacts.phonenumber as contact_phone,recurring,(CASE rel_type WHEN "contract" THEN (SELECT subject FROM '.db_prefix().'contracts WHERE '.db_prefix().'contracts.id = '.db_prefix().'tasks.rel_id) WHEN "estimate" THEN (SELECT id FROM '.db_prefix().'estimates WHERE '.db_prefix().'estimates.id = '.db_prefix().'tasks.rel_id) WHEN "proposal" THEN (SELECT id FROM '.db_prefix().'proposals WHERE '.db_prefix().'proposals.id = '.db_prefix().'tasks.rel_id) WHEN "invoice" THEN (SELECT id FROM '.db_prefix().'invoices WHERE '.db_prefix().'invoices.id = '.db_prefix().'tasks.rel_id) WHEN "ticket" THEN (SELECT CONCAT(CONCAT("#",'.db_prefix().'tickets.ticketid), " - ", '.db_prefix().'tickets.subject) FROM '.db_prefix().'tickets WHERE '.db_prefix().'tickets.ticketid='.db_prefix().'tasks.rel_id) WHEN "lead" THEN (SELECT CASE '.db_prefix().'leads.email WHEN "" THEN '.db_prefix().'leads.name ELSE CONCAT('.db_prefix().'leads.name, " - ", '.db_prefix().'leads.email) END FROM '.db_prefix().'leads WHERE '.db_prefix().'leads.id='.db_prefix().'tasks.rel_id) WHEN "customer" THEN (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM '.db_prefix().'contacts WHERE userid = '.db_prefix().'clients.userid and is_primary = 1) ELSE company END FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.userid='.db_prefix().'tasks.rel_id) WHEN "project" THEN (SELECT CONCAT(CONCAT(CONCAT("#",'.db_prefix().'projects.id)," - ",'.db_prefix().'projects.name), " - ", (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM '.db_prefix().'contacts WHERE userid = '.db_prefix().'clients.userid and is_primary = 1) ELSE company END FROM '.db_prefix().'clients WHERE userid='.db_prefix().'projects.clientid)) FROM '.db_prefix().'projects WHERE '.db_prefix().'projects.id='.db_prefix().'tasks.rel_id) WHEN "expense" THEN (SELECT CASE expense_name WHEN "" THEN '.db_prefix().'expenses_categories.name ELSE CONCAT('.db_prefix().'expenses_categories.name, " (",'.db_prefix().'expenses.expense_name,")") END FROM '.db_prefix().'expenses JOIN '.db_prefix().'expenses_categories ON '.db_prefix().'expenses_categories.id = '.db_prefix().'expenses.category WHERE '.db_prefix().'expenses.id='.db_prefix().'tasks.rel_id)
        ELSE NULL
        END) as rel_name,billed,
	(SELECT staffid FROM '.db_prefix().'task_assigned WHERE taskid='.db_prefix().'tasks.id limit 1) as is_assigned,
	(SELECT id FROM '.db_prefix().'call_history WHERE task_id='.db_prefix().'tasks.id limit 1) as call_id,
	(SELECT filename FROM '.db_prefix().'call_history WHERE task_id='.db_prefix().'tasks.id and status = "answered" limit 1) as recorded,
	(SELECT GROUP_CONCAT(staffid SEPARATOR ",") FROM '.db_prefix().'task_assigned WHERE taskid='.db_prefix().'tasks.id ORDER BY '.db_prefix().'task_assigned.staffid) as assignees_ids,
	(SELECT MAX(id) FROM '.db_prefix().'taskstimers WHERE task_id='.db_prefix().'tasks.id and staff_id=1 and end_time IS NULL) as not_finished_timer_by_current_staff,
	(SELECT staffid FROM '.db_prefix().'task_assigned WHERE taskid='.db_prefix().'tasks.id AND staffid=1 group by '.db_prefix().'task_assigned.taskid) as current_user_is_assigned,(SELECT CASE WHEN '.db_prefix().'tasks.addedfrom=1 AND is_added_from_contact=0 THEN 1 ELSE 0 END) as current_user_is_creator';
	$return_arr = array('join'=>$join,'join_cond'=>$join_cond,'cus'=>$cus,'fields'=>$fields);
	return $return_arr;
}
function get_task_qry($clmn,$crow,$view_by,$measure,$date_range,$view_type,$sum_id,$filters){
	$CI		= & get_instance();
	$qry_cond = check_year_week($view_by);
	if(empty($_REQUEST['edit_id'])){
		$report_name	=	$CI->session->userdata('report_type');
	}
	else{
		$reports1 = $CI->db->query("SELECT report_type,folder_id FROM " . db_prefix() . "report WHERE id = '".$_REQUEST['edit_id']."' ")->row();
		$report_name	=	$reports1->report_type;
	}
	if(str_contains($report_name, 'Call Performance')){
		$qry_cond .= ' and '.db_prefix().'tasks.tasktype = (select id from '.db_prefix().'tasktype where name="Call" and status ="Active" )';
	}
	if(str_contains($report_name, 'Email Performance')){
		$qry_cond .= ' and '.db_prefix().'tasks.tasktype = (select id from '.db_prefix().'tasktype where name="E-mail" and status ="Active" )';
	}
	$conds  = get_activity_filters($filters);
	$where_in = array();
	$req_projects = 1;
	if(!empty($conds) || !empty($qry_cond)){
		$i = 0;
		$tasks = array();
		$req_cond = '';
		if(!empty($conds)){
			$req_cond .= $conds;
		}
		if(!empty($qry_cond)){
			$req_cond .= $qry_cond;
		}
		$ress = $CI->db->query("SELECT id FROM " . db_prefix() . "tasks where id != '0' ".$req_cond)->result_array();
		if(!empty($ress)){
			foreach($ress as $res1){
				$tasks[$i] = $res1['id'];
				$i++;
			}
		}
		if(!empty($tasks)){
			$where_in[db_prefix().'tasks.id']   =  $tasks;
		}
		else{
			$req_projects = 0;
		}
	}
	$aColumns_temp = get_tasks_all_fields();
	$sIndexColumn = 'id';
	$sTable       = db_prefix() . 'tasks ';
	$req_tables	  = get_join_task_tables();
	$cus		  = $req_tables['cus'];
	$join		  = $req_tables['join'];
	$aColumns = array();
	$aColumns_temp = array_merge($aColumns_temp,$cus);
	$idkey = 0;
	$report_task_list_column = json_decode(get_option('report_task_list_column_order'),true); 
	foreach($report_task_list_column as $ckey=>$cval){
		if($ckey == 'id') {
			$idkey = 1;
		}
		if(isset($aColumns_temp[$ckey])){
			$aColumns[] =$aColumns_temp[$ckey];
		}
		
	}
	$fields = implode(',',$aColumns);
	//$fields	= $req_tables['fields'];
	$join_cond	= $req_tables['join_cond'];
	$my_staffids = $CI->staff_model->get_my_staffids();
	if(!empty($my_staffids) && !is_admin(get_staff_user_id())){
		$where_in[db_prefix().'projects.teamleader']  = $my_staffids;
	}
	$req_view_by = $view_by;
	if(strtolower($clmn) == 'completed'){
		$req_status = 5;
		$where[db_prefix().'tasks.status']  =  '5';
	}
	else if(strtolower($clmn) == 'today'){
		$req_status = 3;
		$where[db_prefix().'tasks.status']  =  '3';
	}
	else if(strtolower($clmn) == 'overdue'){
		$req_status = 2;
		$where[db_prefix().'tasks.status']  =  '2';
	}
	else if(strtolower($clmn) == 'upcoming'){
		$req_status = 1;
		$where[db_prefix().'tasks.status']  =  '1';
	}
	switch($view_by){
		case'startdate':
			break;
		case'dateadded':
			break;
		case'datemodified':
			break;
		case'datefinished':
			break;
		case'status':
			$where[db_prefix().'tasks.status']   =  $sum_id;
			break;
		case'assignees':
			$cond2 = (!empty($req_status))?" and t.status = '".$req_status."' ":'';
			
			if(!empty($where_in[db_prefix().'tasks.id'])){
				$ids = implode(',',$where_in[db_prefix().'tasks.id']);
				if(!empty($ids)){
					$cond2 .= " and ta.taskid in($ids)";
				}
			}
			$sql = " select ta.taskid from ".db_prefix()."task_assigned ta,".db_prefix()."tasks t  where ta.staffid = '".$sum_id."' and t.id = ta.taskid".$cond2;
			$query = $CI->db->query($sql);
			$results = $query->result_array();
			$tasks = array();
			if(!empty($results)){
				$i = 0;
				foreach($results as $res1){
					$tasks[$i] = $res1['taskid'];
					$i++;
				}
				$where_in[db_prefix().'tasks.id']   =  $tasks;
			}
			else{
				$req_projects   =  0;
			}
			break;
		case'tags':
			$cond2 = (!empty($req_status))?" and ts.status = '".$req_status."'":'';
			if(!empty($where_in[db_prefix().'tasks.id'])){
				$ids = implode(',',$where_in[db_prefix().'tasks.id']);
				if(!empty($ids)){
					$cond2 .= " and ta.rel_id in($ids)";
				}
			}
			$sql = " select ta.rel_id from ".db_prefix()."tags t,".db_prefix()."taggables ta,".db_prefix()."tasks ts where t.id = '".$sum_id."' and ta.tag_id = t.id and ta.rel_type='task' and ts.id = ta.rel_id ".$cond2;
			$query = $CI->db->query($sql);
			$results = $query->result_array();
			$tags_ids = array();
			if(!empty($results)){
				$i = 0;
				foreach($results as $res1){
					$tags_ids[$i] = $res1['rel_id'];
					$i++;
				}
				$where_in[db_prefix().'tasks.id']   =  $tags_ids;
			}
			else{
				$req_projects   =  0;
			}
			break;
		case'project_status':
			$cond2 = (!empty($req_status))?" and t.status = '".$req_status."'":'';
			if(!empty($where_in[db_prefix().'tasks.id'])){
				$ids = implode(',',$where_in[db_prefix().'tasks.id']);
				if(!empty($ids)){
					$cond2 .= " and t.id in($ids)";
				}
			}
			$sql = " select t.id from ".db_prefix()."tasks t,".db_prefix()."projects p,".db_prefix()."projects_status ps where t.rel_id = p.id and ps.id = p.status and p.deleted_status = 0 and p.status = '".$sum_id."'".$cond2;
			$query = $CI->db->query($sql);
			$results = $query->result_array();
			$projects = array();
			if(!empty($results)){
				$i = 0;
				foreach($results as $res1){
					$projects[$i] = $res1['id'];
					$i++;
				}
				$where_in[db_prefix().'tasks.id']   =  $projects;
			}
			else{
				$req_projects   =  0;
			}
			break;
		case'project_pipeline':
			$cond2 = (!empty($req_status))?" and t.status = '".$req_status."'":'';
			if(!empty($where_in[db_prefix().'tasks.id'])){
				$ids = implode(',',$where_in[db_prefix().'tasks.id']);
				if(!empty($ids)){
					$cond2 .= " and t.id in($ids)";
				}
			}
			$sql = " select t.id from ".db_prefix()."tasks t,".db_prefix()."projects p,".db_prefix()."pipeline pi where t.rel_id = p.id and pi.id = p.pipeline_id and p.deleted_status = 0 and p.pipeline_id = '".$sum_id."'".$cond2;
			$query = $CI->db->query($sql);
			$results = $query->result_array();
			$projects = array();
			if(!empty($results)){
				$i = 0;
				foreach($results as $res1){
					$projects[$i] = $res1['id'];
					$i++;
				}
				$where_in[db_prefix().'tasks.id']   =  $projects;
			}	
			else{
				$req_projects   =  0;
			}
			break;
		case 'company':
			$where[db_prefix().'tasks.rel_type']   =  'customer';
			$where[db_prefix().'tasks.rel_id']   =  $sum_id;
			$where[db_prefix().'tasks.status']   =  $req_status;
			break;
		case'teamleader':
			$cond2 = (!empty($req_status))?" and t.status = '".$req_status."' ":'';
			if(!empty($where_in[db_prefix().'tasks.id'])){
				$ids = implode(',',$where_in[db_prefix().'tasks.id']);
				if(!empty($ids)){
					$cond2 .= " and t.id in($ids)";
				}		
			}
			$sql = " select t.id from ".db_prefix()."tasks t,".db_prefix()."projects p where p.teamleader = '".$sum_id."' and t.rel_type = 'project' and t.rel_id = p.id and p.deleted_status='0' ".$cond2;
			$query = $CI->db->query($sql);
			$results = $query->result_array();
			$projects = array();
			if(!empty($results)){
				$i = 0;
				foreach($results as $res1){
					$projects[$i] = $res1['id'];
					$i++;
				}
				$where_in[db_prefix().'tasks.id']   =  $projects;
			}
			else{
				$req_projects   =  0;
			}
			break;
		case'project_contacts':
			$cond2 = (!empty($req_status))?" and t.status = '".$req_status."'":'';
			if(!empty($where_in[db_prefix().'tasks.id'])){
				$ids = implode(',',$where_in[db_prefix().'tasks.id']);
				if(!empty($ids)){
					$cond2 .= "  and t.id in($ids)";
				}
			}
			$sql = " select t.id from ".db_prefix()."contacts c,".db_prefix()."project_contacts pc,".db_prefix()."projects p,".db_prefix()."tasks t where pc.project_id = p.id and p.deleted_status = 0 and c.id = pc.contacts_id and t.rel_id = p.id and c.id = '".$sum_id."' ".$cond2;
			$query = $CI->db->query($sql);
			$results = $query->result_array();
			$projects = array();
			if(!empty($results)){
				$i = 0;
				foreach($results as $res1){
					$projects[$i] = $res1['id'];
					$i++;
				}
				$where_in[db_prefix().'tasks.id']   =  $projects;
			}
			else{
				$req_projects   =  0;
			}
			break;
		case'priority':
			$where[db_prefix().'tasks.priority']   =  $sum_id;
			if($clmn!='total_val_task'){
				$where[db_prefix().'tasks.status'] =  $req_status;
			}
			break;
		default:
			$cond2 = '';
			$cond3 = (!empty($req_status))?" and t.status = '".$req_status."'":'';
			if(!empty($where_in[db_prefix().'tasks.id'])){
				$ids = implode(',',$where_in[db_prefix().'tasks.id']);
				if(!empty($ids)){
					$cond2 .= " and c.relid in($ids)";
				}
			}
			if(!empty($sum_id)){
				$sql = " select c.relid from ".db_prefix()."customfieldsvalues c,".db_prefix()."tasks t where c.fieldto = 'tasks' and t.id = c.relid and c.fieldid = '".$sum_id."' ".$cond2.$cond3;
				$query = $CI->db->query($sql);
				$results = $query->result_array();
			}
			else{
				$results = get_custom_res($view_by,$view_type,$date_range,$crow,$cond2);
			}
			$projects = array();
			if(!empty($results)){
				$i = 0;
				foreach($results as $res1){
					$projects[$i] = $res1['relid'];
					$i++;
				}
				$where_in[db_prefix().'tasks.id']   =  $projects;
			}
			else{
				$req_projects   =  0;
			}
			break;
	}
	if($view_type == 'date' && (check_activity_date($view_by))){
		if($date_range == 'Monthly'){
			$where['month('.db_prefix().'tasks.'.$req_view_by.')']  =  $crow;
		}
		$where['year('.db_prefix().'tasks.'.$req_view_by.')']   =  date('Y');
		if($date_range == 'Quarterly'){
			if (str_contains($crow, 'Q1')) {
				$where_in['month('.db_prefix().'tasks.'.$req_view_by.')']   =  array(1,2,3);
			}
			else if (str_contains($crow, 'Q2')) {
				$where_in['month('.db_prefix().'tasks.'.$req_view_by.')']   =  array(4,5,6);
			}
			else if (str_contains($crow, 'Q3')) {
				$where_in['month('.db_prefix().'tasks.'.$req_view_by.')']   =  array(7,8,9);
			}
			else if (str_contains($crow, 'Q4')) {
				$where_in['month('.db_prefix().'tasks.'.$req_view_by.')']   =  array(10,11,12);
			}
			
		}
	}
	if($req_projects == 1){
		$result = select_join_query($fields,$sTable,$join,$join_cond,'left',$where,$where_in);
	}else{
		$result = array();
	}
	return $result;
}
function get_task_table_fields($view_by){
	$data = array();
	switch($view_by){
		case 'status':
			$data['tables']		= db_prefix() . "tasks ".db_prefix().'tasks ';
			$data['fields']		= ",".db_prefix()."tasks.status,count(".db_prefix()."tasks.status) tot_val,".db_prefix()."tasks.status req_id";
			$data['qry_cond']   = db_prefix()."tasks.status != '' group by ".db_prefix()."tasks.status order by ".db_prefix()."tasks.status asc  ";
			$data['cur_rows']	= "status";
			break;
		case 'assignees':
			$data['tables']		= db_prefix()."task_assigned ta,".db_prefix()."tasks,".db_prefix()."staff s";
			$data['fields']		= ",s.firstname,s.lastname,count(ta.staffid) tot_val,s.staffid req_id";
			$data['qry_cond']   = " ta.taskid = ".db_prefix()."tasks.id and s.staffid = ta.staffid  group by ta.staffid  order by s.firstname asc";
			$data['cur_rows']	= "firstname,lastname";
			break;
		case 'tags':
			$data['tables']		= db_prefix() . "tasks, " . db_prefix() . "tags t, ". db_prefix() . "taggables ta ";
			$data['fields']		= ",t.name,count(ta.tag_id) tot_val,ta.tag_id req_id ";
			$data['qry_cond']   = " ta.rel_id = ".db_prefix()."tasks.id and t.id = ta.tag_id and ta.rel_type= 'task' group by ta.tag_id order by t.name asc";
			$data['cur_rows']	= "name";
			break;
		case 'project_status':
			$data['tables']		= db_prefix()."projects p,".db_prefix(). "projects_status ps, ". db_prefix() ."tasks";
			$data['fields']		= ",ps.name,count(ps.id ) tot_val,ps.id req_id ";
			$data['qry_cond']   = db_prefix()."tasks.rel_id = p.id and ps.id = p.status and ".db_prefix()."tasks.rel_type= 'project' and p.deleted_status = 0 group by p.status order by ps.name asc";
			$data['cur_rows']	= "name";
			break;
		case 'project_pipeline':
			$data['tables']		= db_prefix() . "projects p, " . db_prefix() . "pipeline pi, ". db_prefix() ."tasks";
			$data['fields']		= ",pi.name,count(p.id ) tot_val,pi.id req_id ";
			$data['qry_cond']   = db_prefix()."tasks.rel_id = p.id and ".db_prefix()."tasks.rel_type= 'project' and pi.id = p.pipeline_id and p.deleted_status = 0 group by p.pipeline_id order by pi.name asc";
			$data['cur_rows']	= "name";
			break;
		case 'company':
			$data['tables']		= db_prefix()."clients c, ".db_prefix()."tasks";
			$data['fields']		= ",c.company,count(".db_prefix()."tasks.rel_id ) tot_val,".db_prefix()."tasks.rel_id req_id ";
			$data['qry_cond']   = db_prefix()."tasks.rel_type = 'customer' and c.userid =  ".db_prefix()."tasks.rel_id group by  ".db_prefix()."tasks.rel_id order by c.company asc";
			$data['cur_rows']	= "company";
			break;
		case 'teamleader':
			$data['tables']		= db_prefix() . "projects p, " . db_prefix() . "staff s,".db_prefix()."tasks";
			$data['qry_cond']   = db_prefix()."tasks.rel_id = p.id and ".db_prefix()."tasks.rel_type = 'project' and s.staffid = p.teamleader and p.deleted_status = 0 group by s.staffid order by s.firstname asc";
			$data['fields']		= ",s.firstname,s.lastname,count(".db_prefix()."tasks.rel_id) tot_val,s.staffid req_id ";
			$data['cur_rows']	= "firstname,lastname";
			break;
		case 'project_contacts':
			$data['tables']		= db_prefix() . "projects p, " . db_prefix() . "project_contacts pc," . db_prefix() . "contacts c,".db_prefix()."tasks";
			$data['fields']		= ",c.firstname,c.lastname,count(p.id ) tot_val,pc.contacts_id req_id ";
			$data['qry_cond']   = "pc.project_id = p.id and c.id = pc.contacts_id and c.is_primary = '1' and ".db_prefix()."tasks.rel_id = p.id and p.deleted_status = 0 group by pc.contacts_id order by c.firstname asc";
			$data['cur_rows']	= "firstname,lastname";
			break;
		case 'priority':
			$data['tables']		= db_prefix() . "tasks";
			$data['fields']		= ",priority,count(priority ) tot_val,priority req_id ";
			$data['qry_cond']   = " priority!='' group by priority";
			$data['cur_rows']	= "priority";
			break;
		default:
			$data['tables']		= db_prefix()."tasks,".db_prefix(). "customfields cf,".db_prefix()."customfieldsvalues cv ";
			$data['fields']		= ",cv.value,count(".db_prefix()."tasks.id ) num_deal,cv.fieldid req_id ";
			$data['qry_cond']   = " and cf.slug ='".$view_by."' and cv.fieldid = cf.id and cv.relid = ".db_prefix()."tasks.id group by cv.relid order by cv.value asc";
			$data['cur_rows']	= "value";
			break;
	}
	return $data;
}
function tasks_counts($upcoming,$overdue,$today,$in_progress,$completed,$tot_val,$view_by,$cur_rows,$tasks_vals){
	$data = array();
	$data['upcoming']			=	(!empty($upcoming) && $upcoming!=0)?get_decimal($upcoming):0;
	$data['overdue']			=	(!empty($overdue) && $overdue!=0)?get_decimal($overdue):0;
	$data['today']				=	(!empty($today) && $today!=0)?get_decimal($today):0;
	$data['in_progress']		=	(!empty($in_progress) && $in_progress!=0)?get_decimal($in_progress):0;
	$data['completed']			=	(!empty($completed) && $completed!=0)?get_decimal($completed):0;
	$data['tot_cnt'] 		= 	$tot_cnt = $tasks_vals['upcoming'] + $tasks_vals['overdue']+ $tasks_vals['today']+ $tasks_vals['in_progress']+ $tasks_vals['completed'];
	$data['total_cnt_task']		=	$tot_cnt;
	$total_tasks				= 	$upcoming + $overdue + $today + $in_progress + $completed;
	$data['total_val_task']		= 	$data['total_val_prdt'] = get_decimal($tot_val);
	$data['avg_task']			=	$data['avg_prdt_val'] = 0;
	if(!empty($tasks_vals['req_id'])){
		$data['req_id']			=	$tasks_vals['req_id'];
	}
	if($tot_cnt>0){
		$data['avg_task']		=	$data['avg_prdt_val'] = get_decimal($tot_val/$tot_cnt);
	}
	if(str_contains($cur_rows, ',')){
		$req_row  = '';
		$req_rows = explode(',',$cur_rows);
		if(!empty($req_rows)){
			foreach($req_rows as $cur_row1){
				$req_row .= $tasks_vals[$cur_row1].' ';
			}
			$req_row = rtrim($req_row.' ');
		}
		$data[$view_by] 	= $data['rows']	=	$req_row;
	}
	else{
		if(!empty($tasks_vals[$cur_rows]) ){
			$data[$view_by]	= 	$data['rows']	=	$tasks_vals[$cur_rows];
		}
		else{
			$data[$view_by]	= 	$data['rows']	=	$cur_rows;
		}
	}
	return $data;
}
function get_report_folder($folder_type){
	$CI		= & get_instance();
	$fields = "id,folder";
	$condition = array('folder_type'=>$folder_type);
	$CI->db->select($fields);
	$CI->db->from(db_prefix().'folder');
	$CI->db->where($condition); 
	$CI->db->order_by('folder','asc');
	$query = $CI->db->get();
	$res = $query->result_array();
	return $res;
}
function set_activity_summary($type){
	if($type == 'deal'){
		$colarrs = deal_all_fields();
		unset($colarrs['name']);
		unset($colarrs['product_qty']);
		unset($colarrs['product_amt']);
		unset($colarrs['project_cost']);
		$fields = deal_needed_fields();
		$needed = json_decode($fields,true);
		if((!empty($needed['need_fields']) && in_array('project_start_date', $needed['need_fields']))  ){
			 $filter_data['view_by'] = 'project_start_date';
		}
		else{
			if(!empty($colarrs)){
				foreach($colarrs as $ckey=>$cval){
					if((!empty($needed['need_fields']) && in_array($ckey, $needed['need_fields']))){
						$filter_data['view_by'] = $ckey;
					}
				}
			}
			$filter_data['view_type']	= '';
			if($filter_data['view_by'] == 'date' && (check_activity_date($filter_data['view_by']))){
			$filter_data['view_type']	=	'date';
			$filter_data['date_range1']	=	_l('weekly');
			}
			$filter_data['sel_measure']	=_l('deal_val');
		}
	}
	else{
		$colarrs = task_all_columns();
		unset($colarrs['description']);
		unset($colarrs['project_name']);
		$needed = get_tasks_need_fields();
		foreach($colarrs as $ckey=>$cval){
			if((!empty($needed['need_fields']) && in_array($ckey, $needed['need_fields']))  ){
				$filter_data['view_by'] = $ckey;
				
			}
		}
		$filter_data['view_type']	= '';
		if(check_activity_date($filter_data['view_by'])){
			$filter_data['view_type']	=	'date';
			$filter_data['date_range1']	=	_l('weekly');
		}
		$filter_data['sel_measure']	= _l('number');
	}
	return $filter_data;
}
function set_filter($type='',$all_clmns,$cus_flds){
	$CI		= & get_instance();
	$cur_id12 = '';
	if(!empty($_REQUEST['cur_id12'])){
		$cur_id12 = '_edit_'.$_REQUEST['cur_id12'];
	}
	$cur_num1	=	$_REQUEST['cur_num'];
	$cur_num	=	$_REQUEST['cur_num'] +1;
	$filter_data = array();
	$filters	=	$CI->session->userdata($type.'filters'.$cur_id12);
	if(!empty($filters)){
		$i = 0;
		foreach($filters as $key12 => $filter12){
			$filter_data[$type.'filters'.$cur_id12][$i]	=	$filter12;  
			$i++;
		}
	}
	$filters2	=	$CI->session->userdata($type.'filters2'.$cur_id12);
	$filters3	=	$CI->session->userdata($type.'filters3'.$cur_id12);
	$filters4	=	$CI->session->userdata($type.'filters4'.$cur_id12);
	if(!empty($filters2)){
		$i1 = 0;
		foreach($filters2 as $key12 => $filter1){
			$filter_data[$type.'filters2'.$cur_id12][$i1]	=	$filter1; 
			$i1++;
		}
	}
	if(!empty($filters3)){
		$i1 = 0;
		foreach($filters3 as $key12 => $filter1){
			$filter_data[$type.'filters3'.$cur_id12][$i1]	=	$filter1;  
			$i1++;
		}
	}
	if(!empty($filters4)){
		$i1 = 0;
		foreach($filters4 as $key12 => $filter1){
			$filter_data[$type.'filters4'.$cur_id12][$i1]	=	$filter1;  
			$i1++;
		}
	}
	if(!empty($filters)){
		foreach($filters as $key12 => $filter1){
			if(!empty($all_clmns[$filter1])){
				unset($all_clmns[$filter1]);
			}if(!empty($cus_flds[$filter1])){
				unset($cus_flds[$filter1]);
			}
			if(check_activity_date($filter1)){
				if(empty($filter_data[$type.'filters1'.$cur_id12][$key12]))
					$filter_data[$type.'filters1'.$cur_id12][$key12]	=	'is';
				if(empty($filter_data[$type.'filters2'.$cur_id12][$key12]))
					$filter_data[$type.'filters2'.$cur_id12][$key12]	=	'this_year';
				if(empty($filter_data[$type.'filters3'.$cur_id12][$key12]))
					$filter_data[$type.'filters3'.$cur_id12][$key12]	=	'01-01-'.date('Y');
				if(empty($filter_data[$type.'filters4'.$cur_id12][$key12]))
					$filter_data[$type.'filters4'.$cur_id12][$key12]	=	'31-12-'.date('Y');
			}
			else{
				$fields =  $CI->db->query("SELECT type,options FROM " . db_prefix() . "customfields where slug = '".$filter1."' ")->row();
				if(!empty($fields)){
					if($fields->type == 'number'){
						$filter_data[$type.'filters1'.$cur_id12][$key12]	= 'is_more_than';
					}
					else if(check_activity_date($fields->type)){
						if(empty($filter_data[$type.'filters1'.$cur_id12][$key12]))
							$filter_data[$type.'filters1'.$cur_id12][$key12]	=	'is';
						if(empty($filter_data[$type.'filters2'.$cur_id12][$key12]))
							$filter_data[$type.'filters2'.$cur_id12][$key12]	=	'this_year';
						if(empty($filter_data[$type.'filters3'.$cur_id12][$key12])){
							$filter_data[$type.'filters3'.$cur_id12][$key12]	=	'01-01-'.date('Y');
						}
						if(empty($filter_data[$type.'filters4'.$cur_id12][$key12]))
							$filter_data[$type.'filters4'.$cur_id12][$key12]	=	'31-12-'.date('Y');
					}
					else{
						$filter_data[$type.'filters1'.$cur_id12][$key12]	= 'is';
					}
				}
			}
			$filter_data[$type.'filters'.$cur_id12][$key12]	=	$filter1;
		}
	}
	if(!empty($filters1)){
		foreach($filters1 as $key1 => $filter12){
			$filter_data[$type.'filters1'.$cur_id12][$key1]	=	$filter12;
		}
	}
	if(!empty($all_clmns)){
		foreach($all_clmns as $key => $all_clmn1){
			$filter_data[$type.'filters'.$cur_id12][$cur_num1]	=	$key; 
			if($key == 'project_cost' || $key == 'product_qty' || $key == 'product_amt'){
				$filter_data[$type.'filters1'.$cur_id12][$cur_num1]	= 'is_more_than';
			}
			else{
				$filter_data[$type.'filters1'.$cur_id12][$cur_num1]	=	'is';
			}
			break;
		}
	}
	else if(!empty($cus_flds)){
		foreach($cus_flds as $key => $cus_fld1){
			$filter_data[$type.'filters'.$cur_id12][$cur_num1]	=	$key;
			$fields =  $CI->db->query("SELECT type,options FROM " . db_prefix() . "customfields where slug = '".$key."' ")->row();
			if($fields->type == 'number'){
				$filter_data[$type.'filters1'.$cur_id12][$cur_num1]	= 'is_more_than';
			}
			else if(check_activity_date($fields->type)){
				if(empty($filter_data[$type.'filters1'.$cur_id12][$key12]))
					$filter_data[$type.'filters1'.$cur_id12][$cur_num1]	=	'is';
				if(empty($filter_data[$type.'filters2'.$cur_id12][$key12]))
					$filter_data[$type.'filters2'.$cur_id12][$cur_num1]	=	'this_year';
				if(empty($filter_data[$type.'filters3'.$cur_id12][$key12]))
					$filter_data[$type.'filters3'.$cur_id12][$cur_num1]	=	'01-01-'.date('Y');
				if(empty($filter_data[$type.'filters4'.$cur_id12][$key12]))
					$filter_data[$type.'filters4'.$cur_id12][$cur_num1]	=	'31-12-'.date('Y');
			}
			else{
				$filter_data[$type.'filters1'.$cur_id12][$cur_num1]	= 'is';
			}
			break;
		}
	}
	return $filter_data;
}
function get_edit_data($type,$id){
	$type = ($type == 'deal')?'deal':'activity';
	$CI		= & get_instance();
	$data 	= array();
	$data['title'] = _l('add_report');
	if($type == 'deal'){
		$deal_val = deal_values();
		$data =  json_decode($deal_val, true);
		$fields = deal_needed_fields();
		$needed = array();
		if(!empty($fields) && $fields != 'null'){
			$needed = json_decode($fields,true);
		}
	}
	else{
		$deal_val = task_values();
		$data =  json_decode($deal_val, true);
		$needed = get_tasks_need_fields();
	}
	$data['id'] = $id;
	$data['type']= 'activity';
	$check_report = $CI->db->query("SELECT id FROM " . db_prefix() . "report WHERE id = '".$id."' ")->row();
	if(empty($check_report)){
		redirect(admin_url('reports/view_deal_folder'));
		exit;
	}
	$data['filters']	=	$filters = $CI->session->userdata('filters_edit_'.$id);
	$data['filters1']	=	$CI->session->userdata('filters1_edit_'.$id);
	$data['filters2']	=	$CI->session->userdata('filters2_edit_'.$id);
	$data['filters3']	=	$CI->session->userdata('filters3_edit_'.$id);
	$data['filters4']	=	$CI->session->userdata('filters4_edit_'.$id);
	if(!empty($filters) && $type == 'deal'){
		$i = 0;
		foreach($filters as $filter1){
			if (!empty($needed['need_fields']) && !in_array($filter1, $needed['need_fields'])){
				unset($data['filters'][$i]);
				unset($data['filters1'][$i]);
				unset($data['filters2'][$i]);
				unset($data['filters3'][$i]);
				unset($data['filters4'][$i]);
			}
			$i++;
		}
	}
	$data['folders']	=	get_report_folder($type);
	$data['teamleaders'] = $CI->staff_model->get('', [ 'active' => 1]);
	$data['links'] = $CI->db->query("SELECT report_id,link_name,link_name FROM " . db_prefix() . "report_public WHERE report_id = '".$id."' ")->result_array();
	$reports1 = $CI->db->query("SELECT report_name,report_type,folder_id FROM " . db_prefix() . "report WHERE id = '".$id."' ")->row();
	if (($key = array_search('id', $needed['need_fields'])) !== false) {
		unset($needed['need_fields'][$key]);
	}
	if (($key = array_search('project_created', $needed['need_fields'])) !== false ) {
		unset($needed['need_fields'][$key]);
	}
	if (($key = array_search('product_count', $needed['need_fields'])) !== false ) {
		unset($needed['need_fields'][$key]);
	}
	if(empty($reports1)){
		redirect(admin_url());exit;
	}
	$data['report_name']		=	$reports1->report_name.'('.$reports1->report_type.')';
	$data['folder_id']			=	$reports1->folder_id;
	$data['need_fields']		=	$needed['need_fields'];
	$data['need_fields_label']	=	$needed['need_fields_label'];
	$data['need_fields_edit']	=	$needed['need_fields_edit'];
	$data['mandatory_fields1']	=	$needed['mandatory_fields1'];
	$data['report_page'] = $type;
	$data['report_filter'] =  $CI->load->view('admin/reports/filter', $data,true);
	$data['report_footer'] =  $CI->load->view('admin/reports/report_footer', $data,true);
	$shares = $CI->db->query("SELECT share_type,id FROM " . db_prefix() ."shared where  report_id = '".$id."'")->result_array();
	$data['share_types'] = $data['share_persons'] = array();
	$share_id = '';
	if(!empty($shares)){
		$i = 0;
		foreach($shares as $share12){
			$data['share_types'][$i] = $share12['share_type'];
			$share_id = $share12['id'];
			$i++;
		}
	}
	$share_persons = $CI->db->query("SELECT staff_id FROM " . db_prefix() ."shared_staff where  share_id = '".$share_id."'")->result_array();
	if(!empty($share_persons)){
		$i = 0;
		foreach($share_persons as $share_person12){
			$data['share_persons'][$i] = $share_person12['staff_id'];
			$i++;
		}
	}
	return $data;
}
function get_th_column($view_by,$type){
	$CI		= & get_instance();
	$custom_fields = get_table_custom_fields($type);
	$customs = array_column($custom_fields, 'slug');
	if(in_array($view_by,$customs)){
		$req_key = array_search ($view_by, $customs);
		$view_by = $custom_fields[$req_key]['name'];
	}
	return $view_by;
}
function random_color() {
	return $rand = '#'.str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
}
