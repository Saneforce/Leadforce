<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Format task priority based on passed priority id
 * @param  mixed $id
 * @return string
 */
 function get_deal_vals($fields,$fields1,$table,$qry_cond,$filters = array()){
	 $CI	= & get_instance();
	 $conds = get_flters($filters);
	 $my_staffids = $CI->staff_model->get_my_staffids();
	 $qry_cond1 = '';
	 if(!is_admin(get_staff_user_id()) && $my_staffids){
		 $qry_cond1 = ' AND (p.id IN (SELECT ' . db_prefix() . 'projects.id FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id in (' . implode(',',$my_staffids) . ')) OR  p.teamleader in (' . implode(',',$my_staffids) . ') ) ';
	 }
	 if(!empty($qry_cond)){
		 $qry_cond = " where p.deleted_status ='0' ".$conds.$qry_cond1.' And '. $qry_cond;
	 }else{
		  $qry_cond = " where p.deleted_status ='0' ".$conds.$qry_cond1.$qry_cond;
	 }
	 if(!empty($fields)){
		 $fields = $fields.",";
	 }
	  if(!empty($fields1)){
		 $fields1 = "".$fields1;
	 }
	 
	 $CI			= & get_instance();
	 
	 $deal_vals 	= $CI->db->query("SELECT ".$fields."COUNT(DISTINCT IF(stage_of = '1',p.id,NULL)) AS own_count,COUNT(DISTINCT IF(stage_of = '2',p.id,NULL)) AS lost_count,COUNT(DISTINCT IF(stage_of = '0',p.id,NULL)) AS open_count ".$fields1." FROM ".$table.$qry_cond)->result_array();
	return $deal_vals;
 }
function get_counts($own,$open,$lost,$tot_val,$view_by,$cur_rows,$deal_vals,$prd_val=''){
	$data = array();
	
	$data['own']				=	(!empty($own) && $own!=0)?get_decimal($own):0;
	$data['open']				=	(!empty($open) && $open!=0)?get_decimal($open):0;
	
	$data['lost']				=	(!empty($lost) && $lost!=0)?get_decimal($lost):0;
	$data['tot_cnt'] 			= 	$tot_cnt = $deal_vals['own_count'] + $deal_vals['open_count'];
	if(!empty($deal_vals['req_id'])){
		$data['req_id']			=	$deal_vals['req_id'];
	}
	if(empty($prd_val)){
		$data['tot_cnt']		=	$tot_cnt = $tot_cnt + $deal_vals['lost_count'];
	}
	$data['total_cnt_deal']		=	$tot_cnt;
	$total_deal					= 	$own + $open + $lost;
	$data['total_num_prdts']	=	get_decimal($total_deal);
	$data['total_val_deal']		= 	$data['total_val_prdt'] = get_decimal($tot_val);
	$data['avg_deal']			=	$data['avg_prdt_val'] = 0;
	if($tot_cnt>0){
		$data['avg_deal']		=	$data['avg_prdt_val'] = get_decimal($tot_val/$tot_cnt);
	}
	if(str_contains($cur_rows, ',')){
		$req_row  = '';
		$req_rows = explode(',',$cur_rows);
		if(!empty($req_rows)){
			foreach($req_rows as $cur_row1){
				$req_row .= $deal_vals[$cur_row1].' ';
			}
			$req_row = rtrim($req_row.' ');
		}
		$data[$view_by] 	= $data['rows']	=	$req_row;
	}
	else{
		if(!empty($deal_vals[$cur_rows]) ){
			$data[$view_by]	= 	$data['rows']	=	$deal_vals[$cur_rows];
		}
		else{
			$data[$view_by]	= 	$data['rows']	=	$cur_rows;
		}
	}
	return $data;
}
function get_decimal($val)
{
	if (str_contains($val, '.')) {
		if (str_contains($val, '.00')) {
			return (int) $val;
		}else{
			return !is_int($val)?number_format((float)($val), 2, '.', ''):$val;
		}
	}else{
		return $val;
	}
}
function date_summary($qry_cond,$week1,$measure,$view_by,$filters)
{
	$cur_year	= date('Y');
	$CI			= & get_instance();
	if($measure == 'Number of Products'){
		$table = db_prefix() . "projects p,".db_prefix()."project_products pp";
		$qry_cond = "p.deleted_status = '0'and pp.projectid = p.id ".$qry_cond;
		$deal_vals = get_deal_vals('*,sum(p.project_cost) as tot_val','',$table,$qry_cond,$filters);
		$data = get_counts($deal_vals[0]['own_count'],$deal_vals[0]['open_count'],$deal_vals[0]['lost_count'],$deal_vals[0]['tot_val'],$view_by,$week1,$deal_vals[0]);
	}else if($measure == 'Product Value'){
		$req_fields = "*,sum(p.project_cost) as tot_val,sum( IF(stage_of = '1',pp.total_price,NULL)) AS own_price,sum( IF(stage_of = '0',pp.total_price,NULL)) AS open_price";
		$qry_cond = " p.deleted_status = '0' and pp.projectid = p.id".$qry_cond;
		$tables = db_prefix() . 'projects p,'.db_prefix()."project_products pp ";
		$deal_vals = get_deal_vals($req_fields,'',$tables,$qry_cond,$filters);
		$data = get_product_vals($deal_vals,$view_by,$week1);
	}else if($measure == 'Deal Value'){
		$table = db_prefix() . "projects p";
		$qry_cond = "p.deleted_status = '0' ".$qry_cond;
		$req_fields = "*,sum(project_cost) as tot_val,sum( IF(stage_of = '1',project_cost,NULL)) AS own_price,sum( IF(stage_of = '0',project_cost,NULL)) AS open_price";
		$deal_vals = get_deal_vals($req_fields,'',$table,$qry_cond,$filters);
		$data = get_product_vals($deal_vals,$view_by,$week1);
	}
	else if($measure == 'Deal weighted value'){
		$table = db_prefix() . "projects p";
		$qry_cond = "p.deleted_status = '0' ".$qry_cond;
		$req_fields = "*,sum(project_cost) as tot_val,sum( IF(stage_of = '1',(project_cost*(progress/100)),NULL)) AS own_price,sum( IF(stage_of = '0',(project_cost*(progress/100)),NULL)) AS open_price,sum( IF(stage_of = '2',(project_cost*(progress/100)),NULL)) AS lost_price";
		$deal_vals = get_deal_vals($req_fields,'',$table,$qry_cond,$filters);
		$data = get_weight_vals($deal_vals,$view_by,$week1);
	}else if($measure == 'Number'){
		$fields		= ",".db_prefix()."tasks.id";
		$tables 	= db_prefix() . "tasks";
		$task_vals  = get_task_vals('',$fields,$tables,$qry_cond,$filters);
		
		$upcoming	= (!empty($task_vals[0]['upcoming']))?$task_vals[0]['upcoming']:0;
		$overdue	= (!empty($task_vals[0]['overdue']))?$task_vals[0]['overdue']:0;
		$today		= (!empty($task_vals[0]['today']))?$task_vals[0]['today']:0;
		$in_progress= (!empty($task_vals[0]['in_progress']))?$task_vals[0]['in_progress']:0;
		$completed	= (!empty($task_vals[0]['completed']))?$task_vals[0]['completed']:0;
		$tot_val	= $upcoming + $overdue + $today + $in_progress + $completed;
		$data	   = tasks_counts($upcoming,$overdue,$today,$in_progress,$completed,$tot_val,$view_by,$week1,$task_vals);
	}
	else{
		$table = db_prefix() . "projects p";
		$qry_cond = "p.deleted_status = '0' ".$qry_cond;
		$deal_vals = get_deal_vals('*,sum(p.project_cost) as tot_val','',$table,$qry_cond,$filters);
		$data = get_counts($deal_vals[0]['own_count'],$deal_vals[0]['open_count'],$deal_vals[0]['lost_count'],$deal_vals[0]['tot_val'],$view_by,$week1,$deal_vals[0]);
	}
	return $data;
}

function summary_val($tables,$fields,$qry_cond,$measure,$view_by,$cur_rows,$filters,$report_type)
{
	$cur_year	= date('Y');
	$CI			= & get_instance();
	if($report_type == 'deal'){
		if($measure == 'Number of Products'){
			$qry_cond = "p.deleted_status = '0' ".$qry_cond;
			$deal_vals = get_deal_vals('*,sum(p.project_cost) as tot_val',$fields,$tables,$qry_cond,$filters);
			$data = get_data($view_by,$cur_rows,$deal_vals);
		}
		else if($measure == 'Product Value'){
			$req_fields = "*,sum(p.project_cost) as tot_val,sum(IF(stage_of = '1',pp.total_price,NULL)) AS own_price,sum(IF(stage_of = '2',pp.total_price,NULL)) AS lost_price,sum(IF(stage_of = '0',pp.total_price,NULL)) AS open_price";
			$qry_cond = " p.deleted_status = '0' and pp.projectid = p.id".$qry_cond;
			$tables = $tables.','.db_prefix()."project_products pp ";
			$deal_vals = get_deal_vals($req_fields,$fields,$tables,$qry_cond,$filters);
			$data = get_sumary_product_vals($deal_vals,$view_by,$cur_rows);
		}
		else if($measure == 'Deal Value'){
			$req_fields = "*,sum(p.project_cost) as tot_val,sum(IF(p.stage_of = '1',p.project_cost,NULL)) AS own_price,sum(IF(p.stage_of = '0',p.project_cost,NULL)) AS open_price";
			$qry_cond = " p.deleted_status = '0' ".$qry_cond;
			$deal_vals = get_deal_vals($req_fields,$fields,$tables,$qry_cond,$filters);
			$data = get_sumary_product_vals($deal_vals,$view_by,$cur_rows);
		}
		else if($measure == 'Deal weighted value'){
			$req_fields = "*,sum(p.project_cost) as tot_val,sum( IF(p.stage_of = '1',(p.project_cost*(p.progress/100)),NULL)) AS own_price,sum(IF(p.stage_of = '0',(p.project_cost*(p.progress/100)),NULL)) AS open_price,sum(IF(p.stage_of = '0',(p.project_cost*(p.progress/100)),NULL)) AS lost_price";
			$qry_cond = " p.deleted_status = '0' ".$qry_cond;
			$deal_vals = get_deal_vals($req_fields,$fields,$tables,$qry_cond,$filters);
			$data = get_sumary_weight_vals($deal_vals,$view_by,$cur_rows);
		}
		else{
			$qry_cond  = "p.deleted_status = '0' ".$qry_cond;
			$deal_vals = get_deal_vals('*,sum(p.project_cost) as tot_val',$fields,$tables,$qry_cond,$filters);
			$data = get_data($view_by,$cur_rows,$deal_vals);
		}
	}
	else{
		if($measure == 'Number'){
			$task_vals = get_task_vals('',$fields,$tables,$qry_cond,$filters);
			$data	   = get_task_data($view_by,$cur_rows,$task_vals);
		}
	}
	return $data;
}
function filter_cond($filter){
	if($filter!=''  &&  ( $filter != 'this_year' || $filter != 'last_year' || $filter != 'next_year' || $filter != 'this_month' || $filter != 'next_month' || $filter != 'last_month' || $filter != 'this_week' || $filter != 'last_week' || $filter != 'next_week' || $filter != 'today' || $filter != 'yesterday' || $filter != 'tomorrow' || $filter != 'custom_period' )){
		return true;
	}
	return false;
}
function filter_cond1($filter){
	if($filter!=''  &&  ( $filter != 'this_year' && $filter != 'last_year' && $filter != 'next_year' && $filter != 'this_month' && $filter != 'next_month' && $filter != 'last_month' && $filter != 'this_week' && $filter != 'last_week' && $filter != 'next_week' && $filter != 'today' && $filter != 'yesterday' && $filter != 'tomorrow' && $filter != 'custom_period' )){
		return true;
	}
	return false;
}
function get_flters($req_filters,$check_data=''){
	$CI			= 	& get_instance();
	$fields = deal_needed_fields();
	$needed = array();
	if(!empty($fields) && $fields != 'null'){
		$needed = json_decode($fields,true);
	}
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
		$custom_fields = get_table_custom_fields('projects');
		$customs = array_column($custom_fields, 'slug');
		foreach($filters as $filter12){
			if ((!empty($needed['need_fields']) && in_array($filter12, $needed['need_fields'])) || in_array($filter12, $customs)){
				$check_cond = filter_cond($filters2[$i1]);
				/*$deal_vals 	= $CI->db->query("SELECT filter_name,filter_cond,filter_type,date_field,filter FROM ".$table." where filter_name = '".$filter12."' and filter_type= '".$filters1[$i1]."' and filter = 'deal' ")->result_array();
				if(!empty($deal_vals)){
					$cur_cond = $deal_vals[0]['filter_cond'];
					$cur_cond = str_replace('db_prefix()', db_prefix(), $cur_cond);
					if(($filters1[$i1]=='is' || $filters1[$i1]=='is_more_than' || $filters1[$i1]=='is_less_than' || $filters1[$i1]=='is_not') && $deal_vals[0]['date_field'] ==0){
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
				else*/ 
				$cur_cond12 = get_filter_cond($filter12,$filters1[$i1]);
				if($filter12 == 'status' ){
					if($filters1[$i1]=='is'  && $check_cond){
						$cur_cond = " AND ( p.status in (SELECT id FROM ".db_prefix() ."projects_status where id = '".$filters2[$i1]."') )";
						$req_cond .= $cur_cond;
						array_push($where, $cur_cond);
					}
					else if($filters1[$i1]=='is_empty'){
						$cur_cond = " AND ( p.status not in (SELECT id FROM ".db_prefix() ."projects_status) )";
						$req_cond .= $cur_cond;
						array_push($where, $cur_cond);
					}
					else if($filters1[$i1]=='is_not_empty'){
						$cur_cond = " AND ( p.status in (SELECT id FROM ".db_prefix() ."projects_status) )";
						$req_cond .= $cur_cond;
						array_push($where, $cur_cond);
					}
					else if($filters1[$i1]=='is_not'){
						$cur_cond = " AND ( p.status in (SELECT id FROM ".db_prefix() ."projects_status where id != '".$filters2[$i1]."') )";
						$req_cond .= $cur_cond;
						array_push($where, $cur_cond);
					}
					else if($filters1[$i1]=='is_any_of' && $check_cond){
						$req_arrs = explode(',',$filters2[$i1]);
						$req_arr = '';
						if(!empty($req_arrs)){
							foreach($req_arrs as $req_arr1){
								$req_arr .= "'".$req_arr1."',";
							}
						}
						$req_arr = rtrim($req_arr,",");
						$cur_cond = " AND ( p.status in (SELECT id FROM ".db_prefix() ."projects_status where id in(".$req_arr.")) )";
						$req_cond .= $cur_cond;
						array_push($where, $cur_cond);
					}
				}
				else if($filter12 == 'project_status' ){
					if($filters1[$i1]=='is' && $check_cond ){
						if($filters2[$i1] == 'WON'){
							$cur_cond = " AND ( p.stage_of = '1' )";
							$req_cond .= $cur_cond;
							array_push($where, $cur_cond);
						}
						if($filters2[$i1] == 'LOSS'){
							$cur_cond = " AND ( p.stage_of = '2' )";
							$req_cond .= $cur_cond;
							array_push($where, $cur_cond);
						}
					}
					else if($filters1[$i1]=='is_not'){
						if($filters2[$i1] == 'WON'){
							$cur_cond = " AND ( p.stage_of != '1' )";
							$req_cond .= $cur_cond;
							array_push($where, $cur_cond);
						}
						if($filters2[$i1] == 'LOSS'){
							$cur_cond = " AND ( p.stage_of != '2' )";
							$req_cond .= $cur_cond;
							array_push($where, $cur_cond);
						}
					}
					else if($filters1[$i1]=='is_any_of'  ){
						$req_arrs = explode(',',$filters2[$i1]);
						$req_arr = '';
						if(!empty($req_arrs)){
							foreach($req_arrs as $req_arr1){
								if($req_arr1 == 'WON'){
									$req_arr .= "'1',";
								}
								if($req_arr1 == 'LOSS'){
									$req_arr .= "'2',";
								}
							}
						}
						$req_arr = rtrim($req_arr,",");
						$cur_cond = " AND ( p.stage_of in(".$req_arr.") )";
						$req_cond .= $cur_cond;
						array_push($where, $cur_cond);
					}
				}
				else if($cur_cond12!=''){
					$cur_cond = str_replace('db_prefix()', db_prefix(), $cur_cond12);
					if(($filters1[$i1]=='is' || $filters1[$i1]=='is_more_than' || $filters1[$i1]=='is_less_than' || $filters1[$i1]=='is_not') && $deal_vals[0]['date_field'] ==0){
						if($check_cond){
							$cur_cond = str_replace('!!cond1', "'".$filters2[$i1]."'", $cur_cond);
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
							$cur_cond = str_replace('!!in_cond', $req_arr, $cur_cond);
						}
						else{
							$cur_cond = '';
						}
					}
					if (str_contains($cur_cond, '!!date1')) {
						$date1 = "'".date('Y-m-d',strtotime($filters3[$i1]))."'";
						$cur_cond = str_replace('!!date1', $date1, $cur_cond);
					}
					if (str_contains($cur_cond, '!!date2')) {
						$date2 = "'".date('Y-m-d',strtotime($filters4[$i1]))."'";
						
						$cur_cond = str_replace('!!date2', $date2, $cur_cond);
					}
					$req_cond .= $cur_cond;
					array_push($where, $cur_cond);
				}
				else if(in_array($filter12, $customs)){
					if($filters1[$i1]=='is'){
						if($check_cond ){
							$cur_cond = " AND ( p.id in(SELECT cv.relid FROM ".db_prefix() ."customfieldsvalues cv,".db_prefix() ."customfields c where cv.value  = '".$filters2[$i1]."' and c.slug = '".$filter12."' and cv.fieldid = c.id) )";
							$req_cond .= $cur_cond;
							array_push($where, $cur_cond);
						}else{
							$cur_cond = " AND ( p.id in(SELECT cv.relid FROM ".db_prefix() ."customfieldsvalues cv,".db_prefix() ."customfields c where value  > '".date('Y-m-d',strtotime($filters3[$i1]))."' AND value < '".date('Y-m-d',strtotime($filters4[$i1]))."' and c.slug = '".$filter12."' and cv.fieldid = c.id ) )";
							$req_cond .= $cur_cond;
							array_push($where, $cur_cond);
						}
					}
					else if($filters1[$i1]=='is_empty'){
						$cur_cond = " AND ( p.id in(SELECT cv.relid FROM ".db_prefix() ."customfieldsvalues cv,".db_prefix() ."customfields c where (cv.value  = '' or cv.value = '0' or cv.value = '0000-00-00') and c.slug = '".$filter12."' and cv.fieldid = c.id) )";
						$req_cond .= $cur_cond;
						array_push($where, $cur_cond);
					}
					else if($filters1[$i1]=='is_not_empty'){
						$cur_cond = " AND ( p.id in(SELECT cv.relid FROM ".db_prefix() ."customfieldsvalues cv,".db_prefix() ."customfields c where cv.value  != '' AND cv.value != '0' AND cv.value != '0000-00-00' AND cv.fieldto = 'projects' and c.slug = '".$filter12."' and cv.fieldid = c.id) )";
						$req_cond .= $cur_cond;
						array_push($where, $cur_cond);
					}
					else if($filters1[$i1]=='is_not'){
						$cur_cond = " AND ( p.id in(SELECT cv.relid FROM ".db_prefix() ."customfieldsvalues cv,".db_prefix() ."customfields c  where cv.value  != '".$filters2[$i1]."' and c.slug = '".$filter12."' and cv.fieldid = c.id) )";
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
						$cur_cond = " AND ( p.id in(SELECT cv.relid FROM ".db_prefix() ."customfieldsvalues cv,".db_prefix() ."customfields c where cv.value  in(".$req_arr.") and c.slug = '".$filter12."' and cv.fieldid = c.id ) )";
						$req_cond .= $cur_cond;
						array_push($where, $cur_cond);
					}
					else if($filters1[$i1]=='is_more_than' && $filters2[$i1]!=''){
						$cur_cond = " AND ( p.id in(SELECT cv.relid FROM ".db_prefix() ."customfieldsvalues cv,".db_prefix() ."customfields c where value  > ".$filters2[$i1]." and c.slug = '".$filter12."' and cv.fieldid = c.id) )";
						$req_cond .= $cur_cond;
						array_push($where, $cur_cond);
					}
					else if($filters1[$i1]=='is_less_than'  && $filters2[$i1]!=''){
						$cur_cond = " AND ( p.id in(SELECT cv.relid FROM ".db_prefix() ."customfieldsvalues cv,".db_prefix() ."customfields c where cv.value  < ".$filters2[$i1]." and c.slug = '".$filter12."' and cv.fieldid = c.id) )";
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
function get_product_vals($deal_vals,$view_by,$cur_rows){
	$CI			= & get_instance();
	if(!empty($deal_vals)){	 
		foreach($deal_vals as $deal_val1){
			$tot_val =  $deal_val1['own_price'] + $deal_val1['open_price'];
			$tot_cnt=	$tot_cnt + $deal_val1['own_count'] + $deal_val1['open_count'];
			$data = get_counts($deal_val1['own_price'],$deal_val1['open_price'],0,$tot_val,$view_by,$cur_rows,$deal_val1,1);
		}
	}
	return $data;
}
function get_weight_vals($deal_vals,$view_by,$cur_rows){
	$CI			= & get_instance();
	if(!empty($deal_vals)){	 
		foreach($deal_vals as $deal_val1){
			$tot_val =  $deal_val1['own_price'] + $deal_val1['open_price'] + $deal_val1['lost_price'];
			$tot_cnt=	$tot_cnt + $deal_val1['own_count'] + $deal_val1['open_count'] + $deal_val1['lost_count'];
			$data = get_counts($deal_val1['own_price'],$deal_val1['open_price'],$deal_val1['lost_price'],$tot_val,$view_by,$cur_rows,$deal_val1);
		}
	}
	return $data;
}
function get_sumary_product_vals($deal_vals,$view_by,$cur_rows){
	$CI			= & get_instance();
	$data = array();
	if(!empty($deal_vals)  && $view_by != 'project_status'){	
		$i = $tot_cnt = $all_tot = $own = $open = $tot_avg = 0;
		foreach($deal_vals as $deal_val1){
			$own 	 =  $own + $deal_val1['own_price'];
			$open 	 =  $open + $deal_val1['open_price'];
			$tot_val =  $deal_val1['own_price'] + $deal_val1['open_price'];
			$tot_val =  $deal_val1['own_price'] + $deal_val1['open_price'];
			$all_tot =  $all_tot + $deal_val1['own_price'] + $deal_val1['open_price'];
			$tot_cnt =	$tot_cnt + $deal_val1['own_count'] + $deal_val1['open_count'] + $deal_val1['lost_count'];
			$data[$i]= get_counts($deal_val1['own_price'],$deal_val1['open_price'],0,$tot_val,$view_by,$cur_rows,$deal_val1,'');
			$tot_avg =	$tot_avg + get_decimal($data[$i]['avg_deal']);
			$i++;
		}
		$data[$i] = deal_avg($own,$open,0,$tot_cnt,$all_tot,$view_by,$i,$tot_avg);
		$i++;
		$data[$i] = deal_total($own,$open,0,$tot_cnt,$tot_val,$view_by,$tot_avg);
	}
	else{
		$data = get_project_status($deal_vals,$view_by);
	}
	return $data;
}
function get_sumary_weight_vals($deal_vals,$view_by,$cur_rows){
	$CI			= & get_instance();
	$data = array();
	if(!empty($deal_vals) && $view_by != 'project_status'){	
		$i = $tot_cnt = $all_tot = $own = $open = $tot_avg = 0;
		foreach($deal_vals as $deal_val1){
			$deal_val1['own_price']  = get_decimal($deal_val1['own_price']);
			$deal_val1['open_price'] = get_decimal($deal_val1['open_price']);
			$deal_val1['lost_price'] = get_decimal($deal_val1['lost_price']);
			$own 	 =  $own + $deal_val1['own_price'];
			$open 	 =  $open + $deal_val1['open_price'];
			$lost 	 =  $open + $deal_val1['lost_price'];
			$tot_val =  $deal_val1['own_price'] + $deal_val1['open_price']  + $deal_val1['lost_price'];
			$all_tot =  $all_tot + $tot_val;
			$tot_cnt =	$tot_cnt + $deal_val1['own_count'] + $deal_val1['open_count'] + $deal_val1['lost_count'];
			$data[$i]= get_counts($deal_val1['own_price'],$deal_val1['open_price'],$deal_val1['lost_price'],$tot_val,$view_by,$cur_rows,$deal_val1);
			$tot_avg	=	$tot_avg + get_decimal($data[$i]['avg_deal']);
			$i++;
		}
		$own  = get_decimal($own);
		$open = get_decimal($open);
		$lost = get_decimal($lost);
		$data[$i] = deal_avg($own,$open,$lost,$tot_cnt,$all_tot,$view_by,$i,$tot_avg);
		$i++;
		$data[$i] = deal_total($own,$open,$lost,$tot_cnt,$tot_val,$view_by,$tot_avg);
	}
	else{
		$data = get_project_status($deal_vals,$view_by);
	}
	return $data;
}
function get_project_status($deal_vals,$view_by){
	$data = array();
	if(!empty($deal_vals)){
		$i = $tot_avg = $tot_val = $tot_cnt = 0;
		foreach($deal_vals as $deal_val1){
			$data[$i]['total_cnt_deal'] = $deal_val1['num_deal'];
			$data[$i]['total_val_deal']	= get_decimal($deal_val1['tot_val']);
			$data[$i]['tot_cnt'] 		= $deal_val1['num_deal'];
			$data[$i]['avg_deal']		= get_decimal($data[$i]['total_val_deal']/$data[$i]['tot_cnt']);
			$data[$i][$view_by] 		= $data[$i]['rows']	=	$deal_val1['stage_of'];
			$tot_avg = $tot_avg + $data[$i]['avg_deal'];
			$tot_val = $tot_val + $data[$i]['total_val_deal'];
			$tot_cnt = $tot_cnt + $data[$i]['total_cnt_deal'];
			$i++;
		}
		$data[$i][$view_by] = $data[$i]['rows'] = 'Average';
		$data[$i]['avg_deal'] = get_decimal($tot_avg/$i);
		$data[$i]['total_cnt_deal'] = get_decimal($tot_cnt/$i);
		$data[$i]['total_val_deal'] = get_decimal($tot_val/$i);
		$i++;
		$data[$i][$view_by] = $data[$i]['rows'] = 'Total';
		$data[$i]['avg_deal'] = get_decimal($tot_avg);
		$data[$i]['total_cnt_deal'] = get_decimal($tot_cnt);
		$data[$i]['total_val_deal'] = get_decimal($tot_val);
	}
	return $data;
}
function get_qry_fields(){
	$aColumns_temp = [
    'id'=>db_prefix() . 'projects.id as id',
    'name'=>db_prefix() .'projects.name as name',
    'teamleader_name'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'staff WHERE '.db_prefix() .'staff.staffid=' . db_prefix() . 'projects.teamleader) as teamleader_name',
    'contact_name'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'project_contacts JOIN ' . db_prefix() . 'contacts on ' . db_prefix() . 'contacts.id = ' . db_prefix() . 'project_contacts.contacts_id WHERE  ' . db_prefix() . 'project_contacts.project_id=' . db_prefix() . 'projects.id AND  ' . db_prefix() . 'project_contacts.is_primary = 1) as contact_name',
    'project_cost'=>'project_cost',
    'product_qty'=>'(SELECT sum(quantity) FROM  ' . db_prefix() . 'project_products WHERE projectid = ' . db_prefix() . 'projects.id) as product_qty',
    'product_amt'=>'(SELECT sum(price) FROM  ' . db_prefix() . 'project_products WHERE projectid = ' . db_prefix() . 'projects.id) as product_amt',
   'company'=> '(SELECT company FROM ' . db_prefix() . 'clients WHERE  ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'projects.clientid) as company',
    'tags'=>'(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'projects.id and rel_type="project" ORDER by tag_order ASC) as tags',
   'start_date'=> 'start_date',
   'deadline'=> 'deadline',
    'members'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'project_members JOIN ' . db_prefix() . 'staff on ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'project_members.staff_id WHERE project_id=' . db_prefix() . 'projects.id ORDER BY staff_id) as members',
   'status'=> '(SELECT  name FROM ' . db_prefix() . 'projects_status  WHERE id = ' . db_prefix() . 'projects.status ORDER by name ASC) as status',
   'project_status'=> db_prefix() . 'projects.stage_of as project_status',
   'pipeline_id'=> 'pipeline_id',
   'contact_email1'=>'(SELECT ' . db_prefix() . 'contacts.email FROM ' . db_prefix() . 'project_contacts JOIN ' . db_prefix() . 'contacts on ' . db_prefix() . 'contacts.id = ' . db_prefix() . 'project_contacts.contacts_id WHERE ' . db_prefix() . 'project_contacts.project_id=' . db_prefix() . 'projects.id AND ' . db_prefix() . 'project_contacts.is_primary = 1) as contact_email1',
   'contact_phone1'=>'(SELECT ' . db_prefix() . 'contacts.phonenumber FROM ' . db_prefix() . 'project_contacts JOIN ' . db_prefix() . 'contacts on ' . db_prefix() . 'contacts.id = ' . db_prefix() . 'project_contacts.contacts_id WHERE ' . db_prefix() . 'project_contacts.project_id=' . db_prefix() . 'projects.id AND ' . db_prefix() . 'project_contacts.is_primary = 1) as contact_phone1',
    'won_date'=>'stage_on as won_date',
    'lost_date'=>'stage_on as lost_date',
    'loss_reason_name'=>'(SELECT ' . db_prefix() . 'deallossreasons.name FROM ' . db_prefix() . 'deallossreasons  WHERE ' . db_prefix() . 'deallossreasons.id='.db_prefix().'projects.loss_reason) as loss_reason_name',
    'project_currency'=>'project_currency',
    'project_created'=>'project_created',
    'project_modified'=>'project_modified',
    'modified_by'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'staff WHERE '.db_prefix() .'staff.staffid=' . db_prefix() . 'projects.modified_by) as modified_by',
    'created_by'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'staff WHERE '.db_prefix().'staff.staffid=' . db_prefix() . 'projects.created_by) as created_by',
    ];
	return $aColumns_temp;
}
function check_year_week($view_by){
	$CI			= & get_instance();
	if($_REQUEST['view_type'] == 'date' && $_REQUEST['date_range']=='Weekly'){
		$cur_year = date('Y');
		$months = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$months_num = array('Jan'=>31,'Feb'=>28,'Mar'=>31,'Apr'=>30,'May'=>31,'Jun'=>30,'Jul'=>31,'Aug'=>31,'Sep'=>30,'Oct'=>31,'Nov'=>30,'Dec'=>31);
		if($cur_year % 4 == 0){
			$months_num['Feb'] = 29;
		}
		$cur_month = date('M');
		$cur_date  = date('d');
		$num_dates = $m = 0;
		$crow = $_REQUEST['crow'];
		if(!empty($months_num)){
			foreach($months_num as $key => $month1){
				if($key == $cur_month){
					$num_dates = $num_dates + (int) $cur_date;
					break;
				}
				else{
					$num_dates = $num_dates + $month1;
				}	
			}
		}
		$cur_row = '';
		$weeks = ceil($num_dates/7);
		if(!empty($weeks)){
			$w_start_date	= 1;
			$w_end_date		= 7;
			for($i=0;$i<$weeks;$i++){
				if($cur_row == $_REQUEST['crow']){
					break;
				}
				$j = $i +1;
				$end_days	= $j*7;
				$start_days	= $end_days - 6;
				$num_month  =  0;$k = 1;
				$qry_cond   = '';
				foreach($months_num as $key => $req_month){
					$num_month = $num_month + $req_month;
					if($num_month >= $start_days && $num_month <= $end_days){
						$start_date	= date('Y-m-d',strtotime($w_start_date.'-'.$key.'-'.$cur_year));
						$end_date   = date('Y-m-d',strtotime($req_month.'-'.$key.'-'.$cur_year));
						if(check_activity_date($view_by)){
							$qry_cond   .= " and ".$view_by." >= '".$start_date."'";
						}
						else{
							$customs   = $CI->db->query("SELECT relid  FROM " . db_prefix() . "customfieldsvalues cv,".db_prefix()."customfields cf where cv.fieldto = 'projects' and CONVERT(cv.value,date)  >='".$start_date."' and CONVERT(cv.value,date) <='".$end_date."' and cf.slug ='".$view_by."' and cf.id = cv.fieldid")->result_array();
							$cur_projects = '';
							if(!empty($customs)){
								foreach($customs as $custom1){
									$cur_projects .= $custom1['relid'].',';
								}	
								$cur_projects = rtrim($cur_projects,",");
								$qry_cond   .= " and id in(".$cur_projects.")";
							}
							else{
								//$qry_cond   .= " and id=''";
							}
						}
						$k++;
						$req_key = array_search ($key, $months);
						$req_end_days = $w_end_date - $req_month;
						$w_start_date	= 1;
						$w_end_date		= $req_end_days;
						$start_date     = date('Y-m-d',strtotime($w_start_date.'-'.$months[$req_key+1].'-'.$cur_year));
						$end_date	    = date('Y-m-d',strtotime($req_end_days.'-'.$months[$req_key+1].'-'.$cur_year));
						if(check_activity_date($view_by)){
									
							$qry_cond 	 .= "  and ".$view_by." <= '".$end_date."'";
						}else{
							$customs   = $CI->db->query("SELECT relid  FROM " . db_prefix() . "customfieldsvalues cv,".db_prefix()."customfields cf where cv.fieldto = 'projects' and CONVERT(cv.value,date)  >='".$start_date."' and CONVERT(cv.value,date) <='".$end_date."' and cf.slug ='".$view_by."' and cf.id = cv.fieldid")->result_array();
							$cur_projects = '';
							if(!empty($customs)){
								foreach($customs as $custom1){
									$cur_projects .= $custom1['relid'].',';
								}	
								$cur_projects = rtrim($cur_projects,",");
								$qry_cond   .= " and id in(".$cur_projects.")";
							}
							else{
								//$qry_cond   .= " and id=''";
							}
						}
						$cur_row    	= 'W'.($m+1).' '.$cur_year;
						$m++;
						$w_start_date	= $w_end_date +1;
						$w_end_date		= $w_end_date +7;
						break;
					}
					else{
						if($num_month >= $start_days){
							$start_date  = date('Y-m-d',strtotime($w_start_date.'-'.$key.'-'.$cur_year));
							$end_date	 = date('Y-m-d',strtotime($w_end_date.'-'.$key.'-'.$cur_year));
							if(check_activity_date($view_by)){
								$qry_cond 	 .= " and ".$view_by." >= '".$start_date."' and ".$view_by." <= '".$end_date."'";
							}
							else{
								$customs   = $CI->db->query("SELECT relid  FROM " . db_prefix() . "customfieldsvalues cv,".db_prefix()."customfields cf where cv.fieldto = 'projects' and CONVERT(cv.value,date)  >='".$start_date."' and CONVERT(cv.value,date) <='".$end_date."' and cf.slug ='".$view_by."' and cf.id = cv.fieldid")->result_array();
								$cur_projects = '';
								if(!empty($customs)){
									foreach($customs as $custom1){
										$cur_projects .= $custom1['relid'].',';
									}	
									$cur_projects = rtrim($cur_projects,",");
									$qry_cond   .= " and id in(".$cur_projects.")";
								}
								else{
									//$qry_cond   .= " and id=''";
								}
							}
							$cur_row    = 'W'.($m+1).' '.$cur_year;
							$m++;
							$w_start_date	= $w_end_date +1;
							$w_end_date		= $w_end_date +7;
							break;
						}
					}
				}
			}
		}
		return $qry_cond;
	}
}
function get_join_tables(){
	$CI		= & get_instance();
	$join 	= array(db_prefix() . 'projects_status',db_prefix() . 'clients');
	
	$join_cond = array(db_prefix().'projects_status.id = '.db_prefix().'projects.status',db_prefix().'clients.userid = '.db_prefix().'projects.clientid');
	$custom_fields = get_table_custom_fields('projects');
	$req_fields = array_column($custom_fields, 'slug'); 
	$req_cnt = count($req_fields);
	$req_fields[$req_cnt + 1] = 'name';
	$req_fields[$req_cnt + 2] = 'teamleader_name';
	$req_fields[$req_cnt + 3] ='contact_name';
	$req_fields[$req_cnt + 4] = 'project_cost';
	$req_fields[$req_cnt + 5] = 'product_qty';
	$req_fields[$req_cnt + 6] = 'product_amt';
	$req_fields[$req_cnt + 7] = 'company';
	$req_fields[$req_cnt + 8] = 'rel_id';
	$req_fields[$req_cnt + 9]= 'start_date';
	$req_fields[$req_cnt + 10]= 'deadline';
	$req_fields[$req_cnt + 11]= 'contact_email1';
	$req_fields[$req_cnt + 12]= 'contact_phone1';
	$report_deal_list_column = (array)json_decode(get_option('report_deal_list_column_order')); 
	$custom_fields = array_merge($custom_fields,get_table_custom_fields('customers'));
	$customFieldsColumns = $cus = [];
	foreach ($custom_fields as $key => $field) {
		$fieldtois= 'clients.userid';
		if($field['fieldto'] =='projects'){
			$fieldtois= 'projects.id';
		}elseif($field['fieldto'] =='contacts'){
			$fieldtois= 'contacts.id';
		}
		if(isset($report_deal_list_column[$field['slug']])){
			$selectAs = 'cvalue_' .$field['slug'];
			array_push($customFieldsColumns, $selectAs);
			$cus[$field['slug']] =  'ctable_' . $key . '.value as ' . $selectAs;
			array_push($join, db_prefix().'customfieldsvalues as ctable_' . $key );
			array_push($join_cond, db_prefix().$fieldtois.' = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
		}
	}
	$fields = 'clientid,
    (SELECT GROUP_CONCAT(staff_id SEPARATOR ",") FROM ' . db_prefix() . 'project_members WHERE project_id=' . db_prefix() . 'projects.id ORDER BY staff_id) as members_ids,
    tblprojects.teamleader,
    (SELECT contacts_id FROM ' . db_prefix() . 'project_contacts WHERE project_id=' . db_prefix() . 'projects.id AND is_primary = 1) as primary_id,
    (select email from ' . db_prefix() . 'contacts where id = (SELECT contacts_id FROM ' . db_prefix() . 'project_contacts WHERE project_id=' . db_prefix() . 'projects.id AND is_primary = 1)) as contact_email,
    (select phonenumber from ' . db_prefix() . 'contacts where id = (SELECT contacts_id FROM ' . db_prefix() . 'project_contacts WHERE project_id=' . db_prefix() . 'projects.id AND is_primary = 1)) as contact_phone';
	$return_arr = array('join'=>$join,'join_cond'=>$join_cond,'cus'=>$cus,'fields'=>$fields);
	return $return_arr;
}
function get_qry($clmn,$crow,$view_by,$measure,$date_range,$view_type,$sum_id,$filters){
	$CI		= & get_instance();
	$qry_cond = check_year_week($view_by);
	$conds  = get_flters($filters);
	$where_in = array();
	$req_projects = 1;
	if(!empty($conds) || !empty($qry_cond)){
		$i = 0;
		$projects = array();
		$req_cond = '';
		if(!empty($conds)){
			$req_cond .= $conds;
		}
		if(!empty($qry_cond)){
			$req_cond .= $qry_cond;
		}
		$ress = $CI->db->query("SELECT id FROM " . db_prefix() . "projects p where p.deleted_status = '0' ".$req_cond)->result_array();	
		if(!empty($ress)){
			foreach($ress as $res1){
				$projects[$i] = $res1['id'];
				$i++;
			}
		}
		if(!empty($projects)){
			$where_in[db_prefix().'projects.id']   =  $projects;
		}
		else{
			$req_projects = 0;
		}
	}
	$aColumns_temp = get_qry_fields();
	$sIndexColumn = 'id';
	$sTable       = db_prefix() . 'projects ';
	$req_tables	  = get_join_tables();
	$cus		  = $req_tables['cus'];
	$join		  = $req_tables['join'];
	$aColumns = array();
	$aColumns_temp = array_merge($aColumns_temp,$cus);
	$idkey = 0;
	$report_deal_list_column = (array)json_decode(get_option('report_deal_list_column_order')); 
	foreach($report_deal_list_column as $ckey=>$cval){
		if($ckey == 'id') {
			$idkey = 1;
		}
		if($ckey == 'pipeline_id') {
			$aColumns[] = '(SELECT name FROM ' . db_prefix() . 'pipeline WHERE id = ' . db_prefix() . 'projects.pipeline_id) as pipeline_id';
		} else {
			if($ckey == 'project_start_date'){
			 $ckey = 'start_date';
			}
			else if($ckey == 'project_deadline'){
			 $ckey = 'deadline';
			}
			if(isset($aColumns_temp[$ckey])){
			$aColumns[] =$aColumns_temp[$ckey];
			}
			if($ckey == 'won_date' || $ckey == 'lost_date'){
			 $ckey = 'stage_on';
			}
		}
	}
	$fields = implode(',',$aColumns);
	$fields	.= ','.$req_tables['fields'];
	$join_cond	= $req_tables['join_cond'];
	$where  = array( db_prefix().'projects.deleted_status' =>0);
	$my_staffids = $CI->staff_model->get_my_staffids();
	if(!empty($my_staffids) && !is_admin(get_staff_user_id())){
			$where_in[db_prefix().'projects.teamleader']  = $my_staffids;
	}
	$req_view_by = $view_by;
	switch($view_by){
		case 'start_date':
			$req_view_by = 'start_date';
			break;
		case 'project_deadline':
			$req_view_by = 'deadline';
			break;
		case 'won_date':
		case 'lost_date':
			$req_view_by = 'stage_on';
			break;
		case'company':
			$where[db_prefix().'projects.clientid']   =  $sum_id;
			break;
		case'teamleader_name':
			$where[db_prefix().'projects.teamleader']   =  $sum_id;
			break;
		case'tags':
			$ids = implode(',',$where_in[db_prefix().'projects.id']);
			$cond2 = '';
			if(!empty($ids)){
				$cond2 = " and ta.rel_id in($ids)";
			}
			$sql = " select rel_id from ".db_prefix()."tags t,".db_prefix()."taggables ta where t.name = '".$crow."' and ta.tag_id = t.id and ta.rel_type='project'".$cond2;
			$query = $CI->db->query($sql);
			$results = $query->result_array();
			$tags_ids = array();
			if(!empty($results)){
				$i = 0;
				foreach($results as $res1){
					$tags_ids[$i] = $res1['rel_id'];
					$i++;
				}
				$where_in[db_prefix().'projects.id']   =  $tags_ids;
			}
			break;
		case'status':
			$where[db_prefix().'projects.status']   =  $sum_id;
			
			break;
		case'pipeline_id':
			$where[db_prefix().'projects.pipeline_id']=  $sum_id;
			
			break;
		case 'project_status':
			$where[db_prefix().'projects.stage_of']   =  $crow;
			break;
		case'contact_name':
			$ids = implode(',',$where_in[db_prefix().'projects.id']);
			$cond2 = '';
			if(!empty($ids)){
				$cond2 = " and pc.project_id in($ids)";
			}
			$sql = " select project_id from ".db_prefix()."contacts c,".db_prefix()."project_contacts pc where c.id = '".$sum_id."' and pc.contacts_id = c.id ".$cond2;
			$query = $CI->db->query($sql);
			$results = $query->result_array();
			$projects = array();
			if(!empty($results)){
				$i = 0;
				foreach($results as $res1){
					$projects[$i] = $res1['project_id'];
					$i++;
				}
				$where_in[db_prefix().'projects.id']   =  $projects;
			}
			break;
		case'contact_email1':
			$ids = implode(',',$where_in[db_prefix().'projects.id']);
			$cond2 = '';
			if(!empty($ids)){
				$cond2 = " and pc.project_id in($ids)";
			}
			$sql = " select project_id from ".db_prefix()."contacts c,".db_prefix()."project_contacts pc where c.email = '".$crow."' and pc.is_primary = '1' and pc.contacts_id = c.id".$cond2;
			$query = $CI->db->query($sql);
			$results = $query->result_array();
			$projects = array();
			if(!empty($results)){
				$i = 0;
				foreach($results as $res1){
					$projects[$i] = $res1['project_id'];
					$i++;
				}
				$where_in[db_prefix().'projects.id']   =  $projects;
			}
			break;
		case'contact_phone1':
			$ids = implode(',',$where_in[db_prefix().'projects.id']);
			$cond2 = '';
			if(!empty($ids)){
				$cond2 = " and pc.project_id in($ids)";
			}
			$sql = " select project_id from ".db_prefix()."contacts c,".db_prefix()."project_contacts pc where c.phonenumber = '".$crow."' and pc.is_primary = '1' and pc.contacts_id = c.id ".$cond2;
			$query = $CI->db->query($sql);
			$results = $query->result_array();
			$projects = array();
			if(!empty($results)){
				$i = 0;
				foreach($results as $res1){
					$projects[$i] = $res1['project_id'];
					$i++;
				}
				$where_in[db_prefix().'projects.id']   =  $projects;
			}
			
			break;
		case'members':
			$sql = " select project_id from ".db_prefix()."project_members pm,".db_prefix()."staff s where s.staffid = '".$sum_id."' and pm.staff_id = s.staffid";
			$query = $CI->db->query($sql);
			$results = $query->result_array();
			$projects = array();
			if(!empty($results)){
				$i = 0;
				foreach($results as $res1){
					$projects[$i] = $res1['project_id'];
					if(!empty($where_in[db_prefix().'projects.id'])){
						$where_in[db_prefix().'projects.id']   =  $res1['project_id'];
					}
					$i++;
				}
				if(empty($where_in[db_prefix().'projects.id'])){
					$where_in[db_prefix().'projects.id']   =  $projects;
				}
			}
			break;
		case'project_currency':
			$where[db_prefix().'projects.project_currency']   =  $crow;
			break;
		case'loss_reason_name':
			$where[db_prefix().'projects.loss_reason']   =  $sum_id;
			break;
		case'created_by':
			$where[db_prefix().'projects.created_by']   =  $sum_id;
			break;
		case'modified_by':
			$where[db_prefix().'projects.modified_by']   =  $sum_id;
			break;
		default:
			$ids = implode(',',$where_in[db_prefix().'projects.id']);
			$cond2 = '';
			if(!empty($ids)){
				$cond2 = " and relid in($ids)";
			}
			if(!empty($sum_id)){
				$cond2 .= " and value = '".$crow."' ";
				$sql = " select relid from ".db_prefix()."customfieldsvalues where fieldid = '".$sum_id."' ".$cond2." ";
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
				$where_in[db_prefix().'projects.id']   =  $projects;
			}
			else{
				//$where[db_prefix().'projects.id']  =  '';
			}
			break;
	}
	
	if($measure == 'Product Value'){
		$i = 0;
		$projects = array();
		$ids = implode(',',$where_in[db_prefix().'projects.id']);
		$cond2 = '';
		if(!empty($ids)){
			$cond2 = " where projectid in($ids)";
		}
		$ress = $CI->db->query("SELECT projectid FROM " . db_prefix() . "project_products ".$cond2)->result_array();	
		
		if(!empty($ress)){
			foreach($ress as $res1){
				$projects[$i] = $res1['projectid'];
				$i++;
			}
		}
		if(!empty($projects)){
			$where_in[db_prefix().'projects.id']   =  $projects;
		}
		else{
			//$where[db_prefix().'projects.id']   =  '0';
		}
	}
	$group = '';
	if($measure == 'Number of Products'){
		$join[]			= db_prefix().'project_products';
		$join_cond[]	= db_prefix().'projects.id ='.db_prefix().'project_products.projectid';
		$ress = $CI->db->query("SELECT id FROM " . db_prefix() . "projects p where p.deleted_status = '0' ".$req_cond)->result_array();	
		if(!empty($ress)){
			foreach($ress as $res1){
				$projects[$i] = $res1['id'];
				$i++;
			}
		}
		if(!empty($projects)){
			$where_in[db_prefix().'projects.id']   =  $projects;
		}
		$where_in[db_prefix().'project_products.projectid']   =  $projects;
		$group = db_prefix().'projects.id';
	}
	if((check_activity_date($view_by))){
		if($date_range == 'Monthly'){
			$where['month('.db_prefix().'projects.'.$req_view_by.')']  =  $crow;
		}
		$where['year('.db_prefix().'projects.'.$req_view_by.')']   =  date('Y');
		if($date_range == 'Quarterly'){
			if (str_contains($crow, 'Q1')) {
				$where_in['month('.db_prefix().'projects.'.$req_view_by.')']   =  array(1,2,3);
			}
			else if (str_contains($crow, 'Q2')) {
				$where_in['month('.db_prefix().'projects.'.$req_view_by.')']   =  array(4,5,6);
			}
			else if (str_contains($crow, 'Q3')) {
				$where_in['month('.db_prefix().'projects.'.$req_view_by.')']   =  array(7,8,9);
			}
			else if (str_contains($crow, 'Q4')) {
				$where_in['month('.db_prefix().'projects.'.$req_view_by.')']   =  array(10,11,12);
			}
		}
	}
	if($clmn == 'lost'){
		$where[db_prefix().'projects.stage_of']  =  '2';
	}
	if($clmn == 'open'){
		$where[db_prefix().'projects.stage_of']  =  '0';
	}
	if($clmn == 'own'){
		$where[db_prefix().'projects.stage_of']  =  '1';
	}
	
	if($req_projects == 1){
		$result = select_join_query($fields,$sTable,$join,$join_cond,'left',$where,$where_in,'',$group);
	}else{
		$result = array();
	}
	return $result;
}
function select_join_query($fields,$table,$join_table=null,$join_condition=null,$join_type = null,$condition=null,$where_in=null,$cond_like=null,$group=null,$order_by=null,$order=null,$limit=null,$offset=null,$cond_or_like=null,$cond_or_like_1=null){
	$CI		= & get_instance();
	$CI->db->select($fields);
	$CI->db->from($table);
	if(!empty($join_table) && !empty($join_condition)){
		$i = 0;
		foreach($join_table as $join){
			if(!empty($join_type)){
				$CI->db->join($join,$join_condition[$i],$join_type);
			}
			else{
				$CI->db->join($join,$join_condition[$i]);
			}
			$i++;
		}
	}
	if(!empty($condition))
		$CI->db->where($condition); 
	if(!empty($where_in)){
		foreach($where_in as $in_key => $where_in1){
			$CI->db->where_in($in_key,$where_in1);
		}
	}
	if(!empty($cond_or_like))
		$CI->db->or_like($cond_or_like);
	if(!empty($cond_or_like_1))
		   $CI->db->where($cond_or_like_1);

	if(!empty($group)){
		$CI->db->group_by($group); 
	}
	if(!empty($order_by) && !empty($order)){
		 $CI->db->order_by($order_by,$order);
	}
	if( !empty($offset)){
		 $CI->db->limit($offset,$limit);
	}
	$query = $CI->db->get();
	$res = $query->result_array();
	if(!empty($res))
		return $res;
	else
		return false;
}
function get_data($view_by,$cur_rows,$deal_vals,$losts = array()){
	$data = array();
	if(!empty($deal_vals) && $view_by != 'project_status'){
		$i = $own = $open = $lost = $tot_cnt = $tot_prt = $tot_val = $avg_deal = $tot_avg  =0;
		foreach($deal_vals as $deal_val1){
			$data[$i] = get_counts($deal_val1['own_count'],$deal_val1['open_count'],$deal_val1['lost_count'],$deal_val1['tot_val'],$view_by,$cur_rows,$deal_val1);
			$own	=	$own + $deal_val1['own_count'];
			$open	=	$open + $deal_val1['open_count'];
			$lost	=	$lost + $deal_val1['lost_count'];
			$tot_cnt=	$tot_cnt + $deal_val1['own_count'] + $deal_val1['open_count'] + $deal_val1['lost_count'];
			$tot_val=	$tot_val + $deal_val1['tot_val'];
			$tot_avg	=	$tot_avg + get_decimal($data[$i]['avg_deal']);
			$i++;
		}
		$data[$i] = $avg_deal = deal_avg($own,$open,$lost,$tot_cnt,$tot_val,$view_by,$i,$tot_avg);
		$i++;
		$data[$i] = deal_total($own,$open,$lost,$tot_cnt,$tot_val,$view_by,$tot_avg);
	}
	else{
		$data = get_project_status($deal_vals,$view_by);
	}
	return $data;
}
function get_table_fields($view_by){
	$data = array();
	switch($view_by){
		case 'teamleader_name':
			$data['tables']		= db_prefix() . "projects p, " . db_prefix() . "staff s  ";
			$data['fields']		= ",s.firstname,s.lastname,count(p.teamleader ) num_deal,s.staffid req_id ";
			$data['qry_cond']   = " and s.staffid = p.teamleader group by p.teamleader order by s.firstname asc";
			$data['cur_rows']	= "firstname,lastname";
			break;
		case 'contact_name':
			$data['tables']		= db_prefix() . "projects p, " . db_prefix() . "project_contacts pc, ". db_prefix()."contacts c";
			$data['fields']		= ",c.firstname,c.lastname,count(p.id) num_deal,c.id req_id ";
			$data['qry_cond']   = " and pc.project_id = p.id and c.id = pc.contacts_id group by pc.contacts_id order by c.firstname asc";
			$data['cur_rows']	= "firstname,lastname";
			break;
		case 'company':
			$data['tables']		= db_prefix() . "projects p, " . db_prefix() . "clients c ";
			$data['fields']		= ",c.company,count(p.clientid ) num_deal,c.userid req_id ";
			$data['qry_cond']   = " and c.userid = p.clientid group by p.clientid order by c.company asc";
			$data['cur_rows']	= "company";
			break;
		case 'tags':
			$data['tables']		= db_prefix() . "projects p, " . db_prefix() . "tags t, ". db_prefix() ."taggables ta";
			$data['fields']		= ",t.name,count(p.id ) num_deal,t.id req_id ";
			$data['qry_cond']   = " and ta.rel_id = p.id and t.id = ta.tag_id and ta.rel_type= 'project' group by t.name order by t.name asc";
			$data['cur_rows']	= "name";
			break;
		case 'members':
			$data['tables']		= db_prefix() . "projects p, " . db_prefix() . "project_members pm, ". db_prefix() ."staff s";
			$data['fields']		= ",s.firstname,s.lastname,count(p.id ) num_deal,s.staffid req_id ";
			$data['qry_cond']   = " and pm.project_id = p.id and s.staffid = pm.staff_id  group by pm.staff_id order by s.firstname asc";
			$data['cur_rows']	= "firstname,lastname";
			break;
		case 'project_status':
			$data['tables']		= db_prefix() . "projects p";
			$data['fields']		= ",p.stage_of,count(p.id ) num_deal,p.id req_id ";
			$data['qry_cond']   = "  group by p.stage_of order by p.stage_of asc";
			$data['cur_rows']	= "stage_of";
			break;
		case 'status':
			$data['tables']		= db_prefix() . "projects p, " . db_prefix() . "projects_status ps";
			$data['fields']		= ",ps.name,count(p.id ) num_deal,ps.id req_id ";
			$data['qry_cond']   = " and ps.id = p.status group by p.status order by ps.name asc";
			$data['cur_rows']	= "name";
			break;
		case 'pipeline_id':
			$data['tables']		= db_prefix() . "projects p, " . db_prefix() . "pipeline pi";
			$data['fields']		= ",pi.name,count(p.id ) num_deal,pi.id req_id ";
			$data['qry_cond']   = " and pi.id = p.pipeline_id group by p.pipeline_id order by pi.name asc";
			$data['cur_rows']	= "name";
			break;
		case 'contact_email1':
			$data['tables']		= db_prefix() . "projects p, " . db_prefix() . "project_contacts pc," . db_prefix() . "contacts c";
			$data['fields']		= ",c.email,count(p.id ) num_deal,c.id req_id ";
			$data['qry_cond']   = " and pc.project_id = p.id and c.id = pc.contacts_id and c.is_primary = '1' group by pc.contacts_id order by c.email asc";
			$data['cur_rows']	= "email";
			break;
		case 'contact_phone1':
			$data['tables']		= db_prefix() . "projects p, " . db_prefix() . "project_contacts pc," . db_prefix() . "contacts c";
			$data['fields']		= ",c.phonenumber,count(p.id ) num_deal,c.id req_id ";
			$data['qry_cond']   = " and pc.project_id = p.id and c.id = pc.contacts_id and c.is_primary = '1' group by pc.contacts_id order by c.phonenumber asc";
			$data['cur_rows']	= "phonenumber";
			break;
		case 'loss_reason_name':
			$data['tables']		= db_prefix() . "projects p, " . db_prefix() . "deallossreasons d";
			$data['fields']		= ",d.name,count(p.id ) num_deal,d.id req_id ";
			$data['qry_cond']   = " and d.id = p.loss_reason group by p.loss_reason order by d.name asc";
			$data['cur_rows']	= "name";
			break;
		case 'project_currency':
			$data['tables']		= db_prefix() . "projects p, " . db_prefix() . "currencies c";
			$data['fields']		= ",c.name,count(p.id ) num_deal,c.id req_id ";
			$data['qry_cond']   = " and c.name = p.project_currency group by p.project_currency order by c.name asc";
			$data['cur_rows']	= "name";
			break;
		case 'created_by':
			$data['tables']		= db_prefix() . "projects p, " . db_prefix() . "staff s";
			$data['fields']		= ",s.firstname,s.lastname,count(p.id ) num_deal,s.staffid req_id ";
			$data['qry_cond']   = " and s.staffid = p.created_by group by p.created_by order by s.firstname asc";
			$data['cur_rows']	= "firstname,lastname";
			break;
		case 'modified_by':
			$data['tables']		= db_prefix() . "projects p, " . db_prefix() . "staff s";
			$data['fields']		= ",s.firstname,s.lastname,count(p.id ) num_deal,s.staffid req_id ";
			$data['qry_cond']   = " and s.staffid = p.modified_by group by p.modified_by order by s.firstname asc";
			$data['cur_rows']	= "firstname,lastname";
			break;
		default:
			$data['tables']		= db_prefix() . "projects p, " . db_prefix() . "customfields cf," . db_prefix() . "customfieldsvalues cv";
			$data['fields']		= ",cv.value,count(p.id ) num_deal,cv.fieldid req_id ";
			$data['qry_cond']   = " and cf.slug ='".$view_by."' and cv.fieldid = cf.id and cv.relid = p.id group by cv.relid order by cv.value asc";
			$data['cur_rows']	= "value";
			break;
	}
	return $data;
}
function deal_avg($own,$open,$lost,$tot_cnt,$tot_val,$view_by,$num,$tot_avg)
{
	$data['own']	= 	$data['lost']	= 	$data['open']	= 	$data['total_cnt_deal']	= 	$data['total_num_prdts'] =  $data['total_val_deal']	= 	$data['total_val_prdt'] = $data['avg_deal']		= 	$data['avg_prdt_val'] = $data['avg_tot'] =  0;
	if($tot_cnt>0){
		$data['own']	= 	get_decimal($own/$num);
		$data['lost']	= 	get_decimal($lost/$num);
		$data['open']	= 	get_decimal($open/$num);
		$data['total_cnt_deal']	= 	$data['total_num_prdts'] =  get_decimal($tot_cnt/$num);
		$data['total_val_deal']	= 	$data['total_val_prdt'] =  get_decimal($tot_val/$num);
		$data['avg_deal']	=	$data['avg_prdt_val'] = get_decimal($tot_avg/$num);
		$data['avg_tot'] =  $data['avg_deal'] + $data['avg_tot'];
	}
	$data[$view_by]	= 	$data['rows']	=	'Average';
	return $data;
}
function deal_total($own,$open,$lost,$tot_cnt,$tot_val,$view_by,$tot_avg)
{
	$data['own']	= 	get_decimal($own);
	$data['lost']	= 	get_decimal($lost);
	$data['open']	= 	get_decimal($open);
	$data['total_cnt_deal']	= 	$data['total_num_prdts'] =  $tot_cnt;
	$data['total_val_deal']	= 	$data['total_val_prdt'] =  get_decimal($tot_val);
	$data['avg_deal']		= 	$data['avg_prdt_val'] =  0;
	if($tot_cnt>0){
		$data['avg_deal']	=	$data['avg_prdt_val'] = get_decimal($tot_avg);
	}
	$data[$view_by]	= 	$data['rows']	=	'Total';
	return $data;
}
function get_pipeline_report(){
	$CI		= & get_instance();
	$filter_data['filters1'.$cur_id12][$cur_num1]	=	'is'; 
	$cond = $ids = array();
	$filters	=	$CI->session->userdata('filters'.$cur_id12);
	$filters2	=	$CI->session->userdata('filters2'.$cur_id12);
	if(!empty($filters) && in_array('name',$filters)){
		$key = array_search ('name', $filters);
		if(isset($filters2[$key]) && $filters2[$key]!=''){
			if (str_contains($filters2[$key], ',')) {
				$conds = explode(',',$filters2[$key]);
				if(!empty($conds)){
					foreach($conds as $cond1){
						$CI->db->where('id', $cond1);
						$project = $CI->db->get(db_prefix() . 'projects')->row();
						$ids[] = $project->pipeline_id;
					}
					$pipelines = $CI->pipeline_model->getpipelinebyIdInarray($ids);
				}
			}else{
				$CI->db->where('id', $filters2[$key]);
				$project = $CI->db->get(db_prefix() . 'projects')->row();
				$id = $project->pipeline_id;
				$pipelines = $CI->pipeline_model->getpipelinebyIdarray($id);
			}
		}
		else{
			$pipelines = $CI->pipeline_model->getPipeline();
		}
	}
	else{
		$pipelines = $CI->pipeline_model->getPipeline();
	}
	return $pipelines;
}
function get_stage_report(){
	$CI		= & get_instance();
	$filter_data['filters1'.$cur_id12][$cur_num1]	=	'is'; 
	$cond = $ids = array();
	$filters	=	$CI->session->userdata('filters'.$cur_id12);
	$filters2	=	$CI->session->userdata('filters2'.$cur_id12);
	if(!empty($filters) && in_array('name',$filters)){
		$key = array_search ('name', $filters);
		if(isset($filters2[$key]) && $filters2[$key]!=''){
			if (str_contains($filters2[$key], ',')) {
				$conds = explode(',',$filters2[$key]);
				if(!empty($conds)){
					foreach($conds as $cond1){
						$CI->db->where('id', $cond1);
						$project = $CI->db->get(db_prefix() . 'projects')->row();
						$ids[] = $project->status;
					}
					$all_status = $CI->projects_model->get_status_in_array($ids);
				}
			}else{
				$CI->db->where('id', $filters2[$key]);
				$project = $CI->db->get(db_prefix() . 'projects')->row();
				$id = $project->status;
				$all_status = $CI->projects_model->get_status_array($id);
			}
		}
		else{
			$all_status = $CI->projects_model->get_project_statuses();
		}
	}
	else{
		$all_status = $CI->projects_model->get_project_statuses();
	}
	return $all_status;
}
function check_activity_date($cur_filter){
	if($cur_filter == 'startdate' || $cur_filter == 'dateadded' || $cur_filter == 'datemodified' || $cur_filter == 'datefinished' ){
		return true;
	}
	else if($cur_filter == 'project_start_date' || $cur_filter == 'project_deadline' || $cur_filter == 'won_date' || $cur_filter == 'lost_date' || $cur_filter == 'project_created' || $cur_filter == 'project_modified'  || $cur_filter == 'deadline' || $cur_filter == 'start_date'  || $cur_filter == 'stage_on'){
		return true;
	}
	else if($cur_filter == 'date_picker' || $cur_filter == 'date_picker_time' || $cur_filter == 'date_range'){
		return true;
	}
	return false;
}
function get_custom_res($view_by,$view_type,$date_range,$crow,$cond2){
	$CI		= & get_instance();
	$sql1 = " select id from ".db_prefix()."customfields where slug = '".$view_by."' ";
	$query1 = $CI->db->query($sql1);
	$results1 = $query1->result_array();
	if(!empty($results1)){
		if($view_type == 'date'){
			if($date_range == 'Monthly'){
				$cond2	.=  " and month(value) = '".$crow."'";
			}
			$cond2	.=  " and year(value) = '".date('Y')."'";
			if($date_range == 'Quarterly'){
				if (str_contains($crow, 'Q1')) {
					$cond2	.=  " and month(value) in(1,2,3)";
				}
				else if (str_contains($crow, 'Q2')) {
					$cond2	.=  " and month(value) in(4,5,6)";
				}
				else if (str_contains($crow, 'Q3')) {
					$cond2	.=  " and month(value) in(7,8,9)";
				}
				else if (str_contains($crow, 'Q4')) {
					$cond2	.=  " and month(value) in(10,11,12)";
				}
			}
		}
		$sql = " select relid from ".db_prefix()."customfieldsvalues where fieldid = '".$results1[0]['id']."' ".$cond2." ";
		$query = $CI->db->query($sql);
		$results = $query->result_array();
	}
	else{
		$results = array();
	}
	return $results;
}
function activity_performance_summary($filters,$view_by='',$view_type='',$date_range='',$sel_measure='',$staff_ids=''){
	$CI		= & get_instance();
	$cur_year  = date('Y');
	$data 	   = array();
	$data['rows']			=	array();
	if(!empty($view_by)){
		$data['view_by']	=   $view_by;
	}
	else{
		$data['view_by']	=	$view_by = $CI->session->userdata('view_by');
	}
	$view_type = '';
	if(check_activity_date($view_by)){
		$view_type = 'date';
	}
	if(empty($view_by)){
		$data['view_by']	=   $view_by = 'status';
	}
	if(!empty($view_type))
		$data['view_type']	=	$view_type;
	else
		$data['view_type']	=	$CI->session->userdata('view_type');
	if(!empty($date_range))
		$data['date_range1']=	$date_range;
	else
		$data['date_range1']=	$CI->session->userdata('date_range1');
	if(!empty($sel_measure))
		$data['sel_measure']=	$sel_measure;
	else
		$data['sel_measure']=	$CI->session->userdata('sel_measure');
	$data['columns']		=	array($view_by,'upcoming','overdue','today','completed','total_val_task');
	$i1 = $upcoming  = $overdue =  $today = $in_progress = $completed = 0;
	if(!empty($data['columns'])){
		foreach($data['columns'] as $clmn1){
			$data['summary_cls'][$i1++]['vals'] = _l($clmn1);
			$i1++;
		}
	}
	if($data['view_type'] != 'date'){
		$fields   = get_task_table_fields($view_by);
		if(empty($fields['qry_cond']) && !empty($staff_ids)){
			$fields['tables'] .= " ,".db_prefix()."task_assigned ta1 ";
			$fields['qry_cond'] = "((ta1.taskid = ".db_prefix()."tasks.id and ta1.staffid in(" . $staff_ids . ") ) or (".db_prefix()."tasks.rel_id IN(SELECT ".db_prefix()."projects.id FROM ". db_prefix()."projects join ".db_prefix()."project_members  on ".db_prefix()."project_members.project_id = " .db_prefix()."projects.id WHERE ".db_prefix()."project_members.staff_id in (". $staff_ids."))))";
		}
		else if(!empty($fields['qry_cond']) && !empty($staff_ids)){
			$fields['tables'] .= " ,".db_prefix()."task_assigned ta1 ";
			$fields['qry_cond'] = "((ta1.taskid = ".db_prefix()."tasks.id and ta1.staffid in(" . $staff_ids . ") ) or (".db_prefix()."tasks.rel_id IN(SELECT ".db_prefix()."projects.id FROM ". db_prefix()."projects join ".db_prefix()."project_members  on ".db_prefix()."project_members.project_id = " .db_prefix()."projects.id WHERE ".db_prefix()."project_members.staff_id in (". $staff_ids.")))) and ".$fields['qry_cond'];
		}
		$sum_data = summary_val($fields['tables'],$fields['fields'],$fields['qry_cond'],$data['sel_measure'],$view_by,$fields['cur_rows'],$filters,'activity');
	}
	else{
		$months = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$tot_cnt = $tot_prt = $tot_val = $avg_deal = $tot_avg = 0; 
		if($data['view_type'] == 'date' && ($data['date_range1'] == 'Monthly')){
			if(!empty($months)){
				$j = 1;$i = 0;
				foreach($months as $month1){
					if(check_activity_date($view_by)){
						$j = $i+1;
						$qry_cond   = " MONTH(".db_prefix()."tasks.".$view_by.") = '".$j."' and YEAR(".db_prefix()."tasks.".$view_by.") = '".$cur_year."'";
						$cur_row    = ($month1).' '.$cur_year;
						$sum_data[$i]	= date_summary($qry_cond,$cur_row,$data['sel_measure'],$view_by,$filters);
						$i++;
					}
					else{
						$cur_row    = ($month1).' '.$cur_year;
						$j1 = $j;
						if($j<10){
							$j1 = '0'.$j;
						}
						$ch_value = $cur_year.'-'.$j1;
						$qry_cond = '';
						$customs   = $CI->db->query("SELECT relid  FROM " . db_prefix() . "customfieldsvalues cv,".db_prefix()."customfields cf where cv.fieldto = 'tasks' and cv.value like '%".$ch_value."%' and cf.slug ='".$view_by."' and cf.id = cv.fieldid")->result_array();
						$cur_projects = '';
						if(!empty($customs)){
							foreach($customs as $custom1){
								$cur_projects .= $custom1['relid'].',';
							}	
							$cur_projects = rtrim($cur_projects,",");
							$qry_cond   = " ".db_prefix()."tasks.id in(".$cur_projects.")";
						}
						else{
							//$qry_cond   = " ".db_prefix()."tasks.id =''";
						}
						$cur_row    = ($month1).' '.$cur_year;
						$sum_data[$i]	= date_summary($qry_cond,$cur_row,$data['sel_measure'],$view_by,$filters);
						$i++;
						$j++;
					}
					$tot_avg 	= 	$tot_avg + $sum_data[$i-1]['avg_task'];
					$upcoming	=	$upcoming + $sum_data[$i-1]['upcoming'];
					$overdue	=	$overdue + $sum_data[$i-1]['overdue'];
					$today		=	$today + $sum_data[$i-1]['today'];
					$in_progress=	$in_progress + $sum_data[$i-1]['in_progress'];
					$completed	=	$completed + $sum_data[$i-1]['completed'];
					$tot_cnt=	$tot_cnt + $sum_data[$i-1]['total_val_task'];
					$tot_val=	$tot_val + $sum_data[$i-1]['total_val_task'];
				}
				$sum_data[$i] = task_avg($upcoming,$overdue,$today,$in_progress,$completed,$tot_cnt,$tot_val,$view_by,$i,$tot_avg);
				$i++;
				$sum_data[$i] = task_total($upcoming,$overdue,$today,$in_progress,$completed,$tot_cnt,$tot_val,$view_by,$tot_avg);
			}
		}
		if($data['view_type'] == 'date' && ($data['date_range1'] == 'Weekly')){
			$cur_month = date('M');
			$cur_date  = date('d');
			$num_dates = $m = $tot_avg = 0;
			$sum_data  = array();
			$months_num = array('Jan'=>31,'Feb'=>28,'Mar'=>31,'Apr'=>30,'May'=>31,'Jun'=>30,'Jul'=>31,'Aug'=>31,'Sep'=>30,'Oct'=>31,'Nov'=>30,'Dec'=>31);
			if($cur_year % 4 == 0){
				$months_num['Feb'] = 29;
			}
			if(!empty($months_num)){
				foreach($months_num as $key => $month1){
					if($key == $cur_month){
						$num_dates = $num_dates + (int) $cur_date;
						break;
					}
					else{
						$num_dates = $num_dates + $month1;
					}	
				}
			}
			$weeks = ceil($num_dates/7);
			if(!empty($weeks)){
				$w_start_date	= 1;
				$w_end_date		= 7;
				for($i=0;$i<$weeks;$i++){
					$j = $i +1;
					$end_days	= $j*7;
					$start_days	= $end_days - 6;
					$num_month =  0;$k = 1;
					$qry_cond = '';
					foreach($months_num as $key => $req_month){
						$num_month = $num_month + $req_month;
						if($num_month >= $start_days && $num_month <= $end_days){
							$start_date	= date('Y-m-d',strtotime($w_start_date.'-'.$key.'-'.$cur_year));
							$end_date   = date('Y-m-d',strtotime($req_month.'-'.$key.'-'.$cur_year));
							if(check_activity_date($view_by)){
								$qry_cond   .= "  ".db_prefix()."tasks.".$view_by." >= '".$start_date."' ";
							}
							else{
								$customs   = $CI->db->query("SELECT relid  FROM " . db_prefix() . "customfieldsvalues cv,".db_prefix()."customfields cf where cv.fieldto = 'projects' and CONVERT(cv.value,date)  >='".$start_date."' and CONVERT(cv.value,date) <='".$end_date."' and cf.slug ='".$view_by."' and cf.id = cv.fieldid")->result_array();
								$cur_projects = '';
								if(!empty($customs)){
									foreach($customs as $custom1){
										$cur_projects .= $custom1['relid'].',';
									}	
									$cur_projects = rtrim($cur_projects,",");
									$qry_cond   .= " ".db_prefix()."tasks.id in(".$cur_projects.")";
								}
								else{
									//$qry_cond   .= " and ".db_prefix()."tasks.id=''";
								}
							}
							$upcoming	=	$upcoming + $sum_data[$m-1]['upcoming'];
							$overdue	=	$overdue + $sum_data[$m-1]['overdue'];
							$today		=	$today + $sum_data[$m-1]['today'];
							$in_progress=	$in_progress + $sum_data[$m-1]['in_progress'];
							$completed	=	$completed + $sum_data[$m-1]['completed'];
							$tot_cnt=	$tot_cnt + $sum_data[$m-1]['total_val_task'];
							$tot_val=	$tot_val + $sum_data[$m-1]['total_val_task'];
							$k++;
							$req_end_days = $w_end_date - $req_month;
							$w_start_date	= 1;
							$w_end_date		= $req_end_days;
							
							$req_key = array_search ($key, $months);
							$start_date  = date('Y-m-d',strtotime($w_start_date.'-'.$months[$req_key+1].'-'.$cur_year));
							$end_date	 = date('Y-m-d',strtotime($req_end_days.'-'.$months[$req_key+1].'-'.$cur_year));
							if(check_activity_date($view_by)){
								$qry_cond 	 .= " and ".db_prefix()."tasks.".$view_by." <= '".$end_date."'";
							}else{
								$customs   = $CI->db->query("SELECT relid  FROM " . db_prefix() . "customfieldsvalues cv,".db_prefix()."customfields cf where cv.fieldto = 'projects' and CONVERT(cv.value,date)  >='".$start_date."' and CONVERT(cv.value,date) <='".$end_date."' and cf.slug ='".$view_by."' and cf.id = cv.fieldid")->result_array();
								$cur_projects = '';
								if(!empty($customs)){
									foreach($customs as $custom1){
										$cur_projects .= $custom1['relid'].',';
									}	
									$cur_projects = rtrim($cur_projects,",");
									$qry_cond   .= " and ".db_prefix()."tasks.id in(".$cur_projects.")";
								}
								else{
									//$qry_cond   .= " and ".db_prefix()."tasks.id=''";
								}
							}
							$cur_row    = 'W'.($m+1).' '.$cur_year;
							$sum_data[$m]	= date_summary($qry_cond,$cur_row,$data['sel_measure'],$view_by,$filters);
							$m++;
							
							$w_start_date	= $w_end_date +1;
							$w_end_date		= $w_end_date +7;
							break;
						}
						else{
							if($num_month >= $start_days){
								$start_date  = date('Y-m-d',strtotime($w_start_date.'-'.$key.'-'.$cur_year));
								$end_date	 = date('Y-m-d',strtotime($w_end_date.'-'.$key.'-'.$cur_year));
								if(check_activity_date($view_by)){
									$qry_cond 	 = " ".db_prefix()."tasks.".$view_by." >= '".$start_date."' and ".db_prefix()."tasks.".$view_by." <= '".$end_date."'";
								}
								else{
									$customs   = $CI->db->query("SELECT relid  FROM " . db_prefix() . "customfieldsvalues cv,".db_prefix()."customfields cf where cv.fieldto = 'projects' and CONVERT(cv.value,date)  >='".$start_date."' and CONVERT(cv.value,date) <='".$end_date."' and cf.slug ='".$view_by."' and cf.id = cv.fieldid")->result_array();
									$cur_projects = '';
									if(!empty($customs)){
										foreach($customs as $custom1){
											$cur_projects .= $custom1['relid'].',';
										}	
										$cur_projects = rtrim($cur_projects,",");
										$qry_cond   = " ".db_prefix()."tasks.id in(".$cur_projects.")";
									}
									else{
										//$qry_cond   = " ".db_prefix()."tasks.id=''";
									}
								}
								$cur_row    = 'W'.($m+1).' '.$cur_year;
								$sum_data[$m]	= date_summary($qry_cond,$cur_row,$data['sel_measure'],$view_by,$filters);
								$m++;
								$upcoming	=	$upcoming + $sum_data[$m-1]['upcoming'];
								$overdue	=	$overdue + $sum_data[$m-1]['overdue'];
								$today		=	$today + $sum_data[$m-1]['today'];
								$in_progress=	$in_progress + $sum_data[$m-1]['in_progress'];
								$completed	=	$completed + $sum_data[$m-1]['completed'];
								$tot_cnt=	$tot_cnt + $sum_data[$m-1]['total_val_task'];
								$tot_val=	$tot_val + $sum_data[$m-1]['total_val_task'];
								$tot_avg = $tot_avg + $sum_data[$m-1]['avg_task'];
								$w_start_date	= $w_end_date +1;
								$w_end_date		= $w_end_date +7;
								break;
							}
						}
						$k++;
					}
				}
				$upcoming	= array_sum(array_column($sum_data,'upcoming'));
				$overdue	= array_sum(array_column($sum_data,'overdue'));
				$today		= array_sum(array_column($sum_data,'today'));
				$in_progress= array_sum(array_column($sum_data,'in_progress'));
				$completed	= array_sum(array_column($sum_data,'completed'));
				$tot_cnt	= $tot_val = $upcoming + $overdue + $today + $in_progress+ $completed;
				$sum_data[$m] = task_avg($upcoming,$overdue,$today,$in_progress,$completed,$tot_cnt,$tot_val,$view_by,$m,$tot_avg);
				$m++;
				$sum_data[$m] = task_total($upcoming,$overdue,$today,$in_progress,$completed,$tot_cnt,$tot_val,$view_by,$tot_avg);
			}
		}
		if($data['view_type'] == 'date' && ($data['date_range1'] == 'Quarterly')){	
			$month_period = array(31,30,30,31);
			$j = 1;
			for($i=0;$i<=3;$i++){
				$k = $j+2;
				$start_date = $cur_year.'-'.$j.'-1';
				$end_date   = $cur_year.'-'.$k.'-'.$month_period[$i];
				if(check_activity_date($view_by)){
					$qry_cond   = " ".db_prefix()."tasks.".$view_by." >= '".$start_date."' and ".db_prefix()."tasks.".$view_by." <= '".$end_date."' ";
				}
				else{
					$customs   = $CI->db->query("SELECT relid  FROM " . db_prefix() . "customfieldsvalues cv,".db_prefix()."customfields cf where cv.fieldto = 'projects' and CONVERT(cv.value,date)  >='".$start_date."' and CONVERT(cv.value,date) <='".$end_date."' and cf.slug ='".$view_by."' and cf.id = cv.fieldid")->result_array();
					$cur_projects = '';
					if(!empty($customs)){
						foreach($customs as $custom1){
							$cur_projects .= $custom1['relid'].',';
						}	
						$cur_projects = rtrim($cur_projects,",");
						$qry_cond   = " ".db_prefix()."tasks.id in(".$cur_projects.")";
					}
					else{
						//$qry_cond   = " ".db_prefix()."tasks.id=''";
					}
				}
				$cur_row    = 'Q'.($i+1).' '.$cur_year;
				$sum_data[$i]	= date_summary($qry_cond,$cur_row,$data['sel_measure'],$view_by,$filters);
				
				$j = $j+3;
				$tot_avg = $tot_avg + $sum_data[$i]['avg_task'];
				$upcoming	=	$upcoming + $sum_data[$i]['upcoming'];
				$overdue	=	$overdue + $sum_data[$i]['overdue'];
				$today		=	$today + $sum_data[$i]['today'];
				$in_progress=	$in_progress + $sum_data[$i]['in_progress'];
				$completed	=	$completed + $sum_data[$i]['completed'];
				$tot_cnt=	$tot_cnt + $sum_data[$i]['total_val_task'];
				$tot_val=	$tot_val + $sum_data[$i]['total_val_task'];
			}
			$sum_data[$i] = task_avg($upcoming,$overdue,$today,$in_progress,$completed,$tot_val,$tot_val,$view_by,$i,$tot_avg);
			$i++;
			$sum_data[$i] = task_total($upcoming,$overdue,$today,$in_progress,$completed,$tot_cnt,$tot_val,$view_by,$tot_avg);
		}
		if($data['view_type'] == 'date' && ($data['date_range1'] == 'Yearly')){	
			$i = 0;
			if(check_activity_date($view_by)){
				$qry_cond   = " YEAR(".db_prefix()."tasks.".$view_by.") = '".$cur_year."'";
			}
			else{
				$customs   = $CI->db->query("SELECT relid  FROM " . db_prefix() . "customfieldsvalues cv,".db_prefix()."customfields cf where cv.fieldto = 'projects' and year(CONVERT(cv.value,date)) <='".$cur_year."' and cf.slug ='".$view_by."' and cf.id = cv.fieldid")->result_array();
					$cur_projects = '';
					if(!empty($customs)){
						foreach($customs as $custom1){
							$cur_projects .= $custom1['relid'].',';
						}	
						$cur_projects = rtrim($cur_projects,",");
						$qry_cond   = " ".db_prefix()."tasks.id in(".$cur_projects.")";
					}
					else{
						//$qry_cond   = " id=''";
					}
			}
			$sum_data[$i]	= date_summary($qry_cond,$cur_year,$data['sel_measure'],$view_by,$filters);
			$upcoming	=	$upcoming + $sum_data[$i]['upcoming'];
			$overdue	=	$overdue + $sum_data[$i]['overdue'];
			$today		=	$today + $sum_data[$i]['today'];
			$in_progress=	$in_progress + $sum_data[$i]['in_progress'];
			$completed	=	$completed + $sum_data[$i]['completed'];
			$tot_val	=	$tot_val + $sum_data[$i]['total_val_task'];
			$tot_avg	=   $tot_avg + $sum_data[$i]['avg_task'];				
			$i++;
			$sum_data[$i] = task_avg($upcoming,$overdue,$today,$in_progress,$completed,$tot_val,$tot_val,$view_by,1,$tot_avg);
			$i++;
			$sum_data[$i] = task_total($upcoming,$overdue,$today,$in_progress,$completed,$tot_val,$tot_val,$view_by,$tot_avg );
		}
	}
	$data['summary_cls'] = $sum_data;
	if(isset($sum_data[0]['rows'])){
		$data['rows'] = array_column($sum_data, 'rows');
	}
	return $data;
}