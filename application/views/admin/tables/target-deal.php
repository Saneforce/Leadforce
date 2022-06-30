<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [];

if (has_permission('target', '', 'delete')) {
   // $aColumns[] = '1';
}
$colarr = array(
	"assign"=>array("ins"=>"assign","ll"=>"assign"),
	"pipeline"=>array("ins"=>"pipeline","ll"=>"pipeline"),
	"pipeline_stage"=>array("ins"=>"pipeline_stage","ll"=>"pipeline_stage"),
	"tracking_metric"=>array("ins"=>"tracking_metric","ll"=>"tracking_metric"),
	"target_type"=>array("ins"=>"target_type","ll"=>"target_type"),
	"interval"=>array("ins"=>"interval","ll"=>"interval"),
	"start_date"=>array("ins"=>"start_date","ll"=>"start_date"),
	"end_date"=>array("ins"=>"end_date","ll"=>"end_date"),
	"count_value"=>array("ins"=>"count_value","ll"=>"count_value"),
	"user"=>array("ins"=>"user","ll"=>"user"),
	"manager"=>array("ins"=>"manager","ll"=>"manager"),
	); 
	$cf = get_custom_fields('target');
	//pr($cf);exit;
	foreach($cf as $custom_field) {
		
		$cur_arr = array('ins'=>$custom_field['slug'],'ll'=>$custom_field['name']);
	$colarr[$custom_field['slug']] = $cur_arr;
	  //array_push($colarr,$cur_arr);
	}
$targets_list_column_order = (array)json_decode(get_option('target_list_column_order'));
$targets_list_column_order = array_keys($targets_list_column_order);
foreach($targets_list_column_order as $ckey=>$cval){ 
	if(isset($colarr[$cval])){
	
	}
}
//echo '<pre>';print_r($aColumns);exit;
$aColumns = array_merge($aColumns,$targets_list_column_order);
/*$aColumns = array_merge($aColumns, [
    'assign',
    'tracking_metic',
    'target_type',
   // 'tax',
    //'interval',
    'start_date',
	'end_date'
    
    ]);*/

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'target ';

$join = [
    ' LEFT JOIN  ' . db_prefix() . 'target_pipeline ON ' . db_prefix() . 'target_pipeline.target_id = ' . db_prefix() . 'target.id',
    ' LEFT JOIN  ' . db_prefix() . 'pipeline ON ' . db_prefix() . 'pipeline.id = ' . db_prefix() . 'target_pipeline.pipeline',
	'LEFT JOIN  ' . db_prefix() . 'target_interval ON ' . db_prefix() . 'target.id = ' . db_prefix() . 'target_interval.target_id',
	'LEFT JOIN  ' . db_prefix() . 'target_manager ON ' . db_prefix() . 'target_manager.target_id = ' . db_prefix() . 'target.id',
	 ' LEFT JOIN  ' . db_prefix() . 'staff s1 ON s1.staffid  = ' . db_prefix() . 'target_manager.manager ',
	'LEFT JOIN  ' . db_prefix() . 'target_user ON ' . db_prefix() . 'target_user.target_id = ' . db_prefix() . 'target.id ',
	' LEFT JOIN  ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'target_user.user',
	' LEFT JOIN  ' . db_prefix() . 'target_stage ON ' . db_prefix() . 'target_stage.target_id = ' . db_prefix() . 'target.id',
	' LEFT JOIN  ' . db_prefix() . 'projects_status ON ' . db_prefix() . 'projects_status.id = ' . db_prefix() . 'target_stage.stage_id',
	//' JOIN  ' . db_prefix() . 'target_interval',
];

$additionalSelect = array(db_prefix().'target.id');
$custom_fields = get_custom_fields('target');
$req_custom = array();
$i =0;
if(!empty($custom_fields)){
	foreach ($custom_fields as $key => $field) {
		$selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
		$req_colmn = $field['slug'];

		//array_push($customFieldsColumns, $selectAs);
		//array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
		array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ' . $req_colmn . ' ON ' . db_prefix() . 'target.id = ' . $req_colmn . '.relid AND ' . $req_colmn . '.fieldto="target" AND ' . $req_colmn . '.fieldid=' . $field['id']);
		$req_custom[$i] = $field['slug'];
		$i++;
	}
}

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}
$where        = [];
 $where = ["AND ".db_prefix()."target.target_status = 'deal'"];
//where = array('target_status'=>'deal');
if(!empty($aColumns)){
	$i = 0;
	foreach($aColumns as $aColumn1){
		if($aColumn1 == 'pipeline'){
			$aColumns[$i] = "GROUP_CONCAT(DISTINCT ". db_prefix()."pipeline.name SEPARATOR ', ') as p_search";
			//$aColumns[$i] =  db_prefix()."target_pipeline.s_search";
		}
		if($aColumn1 == 'pipeline_stage'){
			$aColumns[$i] = "GROUP_CONCAT(DISTINCT ". db_prefix()."projects_status.name SEPARATOR ', ') as ps_search";
			//$aColumns[$i] =  db_prefix()."target_pipeline.s_search";
		}
		if($aColumn1 == 'interval'){
			$aColumns[$i] =  db_prefix()."target.interval";
		}
		if($aColumn1 == 'count_value'){
			$aColumns[$i] =  db_prefix()."target_interval.s_search";
			
		}
		if($aColumn1 == 'manager'){
			$aColumns[$i] = "GROUP_CONCAT(DISTINCT CONCAT(s1.firstname, s1.lastname) SEPARATOR ', ') as m_search";
			//$aColumns[$i] =  db_prefix()."target_manager.s_search";
		}
		if($aColumn1 == 'user'){
			//$aColumns[$i] =  db_prefix()."target_user.s_search";
			$aColumns[$i] = "GROUP_CONCAT(DISTINCT CONCAT(". db_prefix()."staff.firstname, ". db_prefix()."staff.lastname) SEPARATOR ', ') as u_search";
		}
		if (in_array($aColumn1, $req_custom)){
			$aColumn1 = str_replace(' ', '_', $aColumn1);
			$aColumns[$i] = $aColumn1.".value ".$aColumn1;
		}
		$i++;
	}
}
if(!empty($req_custom)){
	$i = 0;
	foreach($req_custom as $req_custom1){
		$req_custom1 = str_replace(' ', '_', $req_custom1);
		$req_custom[$i] = $req_custom1.'.value '.$req_custom1;
		$i++;
	}
}
//$group_by =  "group by ".db_prefix().'target_pipeline.target_id,'.db_prefix().'target_pipeline.pipeline,'.db_prefix().'target_manager.manager,'.db_prefix().'target_user.user';
$group_by =  "group by ".db_prefix().'target_pipeline.target_id';
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect,$group_by);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
			if (in_array($aColumns[$i], $req_custom) ){
				$req_col = explode('.',$aColumns[$i]);
				$_data = $aRow[$req_col[0]];
			}
			else{
				$_data = $aRow[$aColumns[$i]];
			}
        }

        if ($aColumns[$i] == '1') {
            $_data = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
        }  elseif ($aColumns[$i] ==  'assign') {
            $_data = '<a href="#" data-toggle="modal" data-target="#sales_item_modal" data-id="' . $aRow['id'] . '">' . ucfirst($_data) . '</a>';
            $_data .= '<div class="row-options">';

            if (has_permission('target', '', 'edit')) {
				$req_id = "'".$aRow['id']."'";
                $_data .= '<a href="#" data-toggle="modal" data-target="#edit_deal" data-id="' . $aRow['id'] . '" onclick="check_edit('.$req_id.')" >' . _l('edit') . '</a>';
            }

            if (has_permission('target', '', 'delete')) {
                $_data .= ' | <a href="' . admin_url('target/deal_delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }
            $_data .= '</div>';
        } elseif($aColumns[$i] == 'target_type') {
			$_data = ucfirst($_data);
		}elseif($aColumns[$i] == 'start_date') {
			$_data = date('d-m-Y',strtotime($_data));
		}elseif($aColumns[$i] == 'end_date') {
			if(!empty($_data)){
				$_data = date('d-m-Y',strtotime($_data));
			}else{
				//$_data = _l('no_end_date');
				//$_data = '-';
			}
		}else {
            if (startsWith($aColumns[$i], 'ctable_') && is_date($_data)) {
                $_data = _d($_data);
            }
        }

        $row[]              = $_data;
        $row['DT_RowClass'] = 'has-row-options';
    }


    $output['aaData'][] = $row;
}
