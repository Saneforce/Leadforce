<?php
    $targets_list_column_order = (array)json_decode(get_option('leads_list_column'));
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