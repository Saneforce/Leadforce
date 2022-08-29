<div class="btn-group pull-right mleft4 mbot25 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('projects_list_column'); ?>">
       <!-- Button trigger modal -->
<button type="button" class="btn btn-default" data-toggle="modal" data-target="#projects_list_column_orderModal">
  <i class="fa fa-list" aria-hidden="true"></i>
</button>

<!-- Modal -->
<div class="modal fade" id="projects_list_column_orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<?php echo form_open_multipart(admin_url('settings/report_deal_list_column'),array('id'=>'projects_list_column')); ?>
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo _l('projects_list_column_order'); ?></h5>
      </div>
      <div class="modal-body">
        <div class="form-group">

<?php $colarr = deal_all_fields(); 

$custom_fields = get_table_custom_fields('projects');
$cus_1 = array();
foreach($custom_fields as $cfkey=>$cfval){
    $cus_1[$cfval['slug']] = $colarr[$cfval['slug']] = array("ins"=>$cfval['name'],"ll"=>$cfval['name']);
}

$custom_fields = get_table_custom_fields('customers');
foreach($custom_fields as $cfkey=>$cfval){
    $cus_1[$cfval['slug']] = $colarr[$cfval['slug']] = array("ins"=>$cfval['name'],"ll"=>$cfval['name']);
}
?>  
  <ul id="sortable" class="ui-sortable">
  <?php $projects_list_column_order = (array)json_decode(get_option('report_deal_list_column_order')); 
  ?>
  <?php foreach($projects_list_column_order as $ckey=>$cval){
	  if((!empty($need_fields) && in_array($ckey, $need_fields)) || !empty($cus_1[$ckey])){
	  ?>
	  <li class="ui-state-default ui-sortable-handle"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
	  <input type="checkbox" name="settings[report_deal_list_column][<?php echo $ckey; ?>]" value="1" checked="checked" /> <?php echo _l($colarr[$ckey]['ll']); ?>
	  </li>
	  <?php }
	  } ?>
  <?php  foreach($colarr as $ckey=>$cval){  
	
	
  if((!empty($need_fields) && in_array($ckey, $need_fields)) || !empty($cus_1[$ckey])){
	 
	  if(!isset($projects_list_column_order[$ckey])){?>
	  <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
	  <input type="checkbox" name="settings[report_deal_list_column][<?php echo $ckey; ?>]" value="1"/> <?php echo _l($cval['ll']); ?>
	  </li>
  <?php }
  }
  } ?>
  
</ul>
  
</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
	  </form>
    </div>
  </div>
</div>
</div>