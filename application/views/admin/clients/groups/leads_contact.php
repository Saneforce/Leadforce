<?php
    $targets_list_column_order = (array)json_decode(get_option('leads_list_column'));
   
    $colarr = array(
        "name"=>array("ins"=>"name","ll"=>"leads_dt_name"),
        "company"=>array("ins"=>"company","ll"=>"lead_company"),
        "email"=>array("ins"=>"email","ll"=>"leads_dt_email"),
        "phonenumber"=>array("ins"=>"phonenumber","ll"=>"leads_dt_phonenumber"),
        "country"=>array("ins"=>"country","ll"=>"lead_country"),
        "state"=>array("ins"=>"state","ll"=>"lead_state"),
        "city"=>array("ins"=>"city","ll"=>"lead_city"),
        "assigned_firstname"=>array("ins"=>"assigned_firstname","ll"=>"leads_dt_assigned"),
        "source_name"=>array("ins"=>"source_name","ll"=>"leads_source"),
        "dateadded"=>array("ins"=>"dateadded","ll"=>"leads_dt_datecreated")
    ); 
    $cf = get_custom_fields('leads');
    foreach($cf as $custom_field) {

    $cur_arr = array('ins'=>$custom_field['slug'],'ll'=>$custom_field['name']);
    $colarr[$custom_field['slug']] = $cur_arr;
    //array_push($colarr,$cur_arr);
    }

    $req_table = array();
    foreach($targets_list_column_order as $ckey=>$cval){
        $req_table[] = _l($colarr[$ckey]['ll']);
    }
    $table_data = [];
    $table_data = array_merge($table_data, $req_table);
    array_unshift($table_data, [
        'name'     => '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="leads"><label></label></div>',
        'th_attrs' => ['class' => (isset($bulk_actions) ? '' : 'not_visible')],
    ]);
   echo '<input type="hidden" name="contact_id" value="'.$contact->id.'">';
   render_datatable($table_data,'leads');  
?>