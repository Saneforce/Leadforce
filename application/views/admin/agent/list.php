<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
if($vendors){ // full wrapper for agents page
	foreach($vendors as $vendor => $vendorname){
		$default_vendor = $vendor;
		continue;
	}
?>
<style>
.followers-div, .addfollower_btn, #rollback {
  display:none;
}

/* Absolute Center Spinner */
#overlay {
  position: fixed;
  z-index: 999;
  overflow: show;
  margin: auto;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  width: 50px;
  height: 50px;
}

/* Transparent Overlay */
#overlay:before {
  content: '';
  display: block;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255,255,255,0.5);
}

/* :not(:required) hides these rules from IE9 and below */
#overlay:not(:required) {
  /* hide "loading..." text */
  font: 0/0 a;
  color: transparent;
  text-shadow: none;
  background-color: transparent;
  border: 0;
}

.errmsg {
	color:red;
}



#overlay:not(:required):after {
  content: '';
  display: block;
  font-size: 10px;
  width: 50px;
  height: 50px;
  margin-top: -0.5em;

  border: 3px solid rgba(33, 150, 243, 1.0);
  border-radius: 100%;
  border-bottom-color: transparent;
  -webkit-animation: spinner 1s linear 0s infinite;
  animation: spinner 1s linear 0s infinite;


}

/* Animation */

@-webkit-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-moz-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-o-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

@-webkit-keyframes rotation {
   from {-webkit-transform: rotate(0deg);}
   to {-webkit-transform: rotate(359deg);}
}
@-moz-keyframes rotation {
   from {-moz-transform: rotate(0deg);}
   to {-moz-transform: rotate(359deg);}
}
@-o-keyframes rotation {
   from {-o-transform: rotate(0deg);}
   to {-o-transform: rotate(359deg);}
}
@keyframes rotation {
   from {transform: rotate(0deg);}
   to {transform: rotate(359deg);}
}

</style>
<div id="overlay" style="display:none; z-index: 1050;"><div class="spinner"></div></div>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAgentModal" style="float:right; margin-bottom:15px;" data-backdrop="static" data-keyboard="false">
+ Add Agent
</button>
<a type="button" href="<?= admin_url('call_settings/syncagents') ?>" class="btn btn-primary mr-2" style="float:right; margin-bottom:15px;margin-right:15px;">
Sync Agents
</a>
<div class="clearfix"></div> 
<table class="table dt-table scroll-responsive table-project-files" data-order-col="0" data-order-type="desc">
  	<thead>
		<tr>
			<th><?php echo _l('vendor'); ?></th>
			<th><?php echo _l('ivr_name'); ?></th>
			<th><?php echo _l('staff_id'); ?></th>
			<th><?php echo _l('phone'); ?></th>
			<th><?php echo _l('agent_id'); ?></th>
			<th><?php echo _l('status'); ?></th>
			<th><?php echo _l('created_date'); ?></th>
			<th><?php echo _l('options'); ?></th>
		</tr>
  	</thead>
  	<tbody>
    <?php foreach($agent_result as $agent){?>
    <tr>
        <td data-order="<?php echo $agent['source_from']; ?>"><?php echo $vendors[$agent['source_from']]; ?></td>
        <td data-order="<?php echo $agent['ivr_name']; ?>"><?php echo $agent['ivr_name']; ?></td>
        <td data-order="<?php echo $agent['staff_name']; ?>"><?php echo $agent['staff_name']; ?></td>
        <td data-order="<?php echo $agent['phone']; ?>"><?php echo $agent['phone']; ?></td>
		<td data-order="<?php echo $agent['agent_id']; ?>"><?php echo $agent['agent_id']; ?></td>
        <td data-order="<?php echo $agent['status']; ?>"><?php echo ucfirst($agent['status']); ?></td>
		<td data-order="<?php echo $agent['created_date']; ?>"><?php echo ((isset($agent['created_date']))?date('M j, Y',strtotime($agent['created_date'])):''); ?></td>
		<td>
			<a href="#" onclick="edit_agent(<?php echo $agent['id']; ?>); return false">Edit </a>
			<?php if($agent['staff_id'] >0): ?>
			<span class="text-dark"> | </span>
			<?php if($agent['source_from']=='telecmi'){?>
			<a href="#" onclick="deletAgent(<?php echo $agent['id']; ?>); return false" class="text-danger">Deactivate </a>	
			<?php }else if($agent['source_from']=='daffytel'){?>
			<a href="#" onclick="daffydeletAgent(<?php echo $agent['id']; ?>); return false" class="text-danger">Deactivate </a>	
			<?php }else{?>
			<a href="#" onclick="tatadeletAgent(<?php echo $agent['id']; ?>,''); return false" class="text-danger">Deactivate </a>	
			<?php }?>
			<?php endif; ?>
			<span class="text-dark"> | </span><a href="#" onclick="deletAgent_db(<?php echo $agent['id']; ?>,1,'<?php echo $agent['source_from']; ?>'); return false" class="text-danger">Delete </a>	
		</td> 
	</tr>
    <?php } ?>
    </tbody>
</table>
   
<hr>
<h3>Deactivated Agents:</h3><br>
<table class="table dt-table scroll-responsive table-project-files" data-order-col="0" data-order-type="desc">
  	<thead>
    <tr>
		<th><?php echo _l('vendor'); ?></th>
		<th><?php echo _l('ivr_name'); ?></th>
		<th><?php echo _l('staff_id'); ?></th>
		<th><?php echo _l('phone'); ?></th>
		<th><?php echo _l('agent_id'); ?></th>
		<th><?php echo _l('status'); ?></th>
		<th><?php echo _l('created_date'); ?></th>
		<th><?php echo _l('options'); ?></th>
    </tr>
  	</thead>
  	<tbody>
    <?php foreach($deactive_agent_result as $agent){?>
    <tr>
		<td data-order="<?php echo $agent['source_from']; ?>"><?php echo $vendors[$agent['source_from']]; ?></td>
        <td data-order="<?php echo $agent['ivr_name']; ?>"><?php echo $agent['ivr_name']; ?></td>
        <td data-order="<?php echo $agent['staff_name']; ?>"><?php echo $agent['staff_name']; ?></td>
        <td data-order="<?php echo $agent['phone']; ?>"><?php echo $agent['phone']; ?></td>

		<td data-order="<?php echo $agent['agent_id']; ?>"><?php echo $agent['agent_id']; ?></td>
        <td data-order="<?php echo $agent['status']; ?>"><?php echo ucfirst($agent['status']); ?></td>
		<td data-order="<?php echo $agent['created_date']; ?>"><?php echo ((isset($agent['created_date']))?date('M j, Y',strtotime($agent['created_date'])):''); ?></td>
    	<td>
			<!-- <a href="#" onclick="edit_agent(<?php echo $agent['id']; ?>); return false">Edit </a><span class="text-dark"> | </span> -->
			 <?php if($agent['source_from']=='telecmi'){?>
			<a href="#" onclick="activateAgent(<?php echo $agent['id']; ?>); return false" class="text-info">Activate </a>	
			 <?php }else if($agent['source_from']=='daffytel'){?>
			<a href="#" onclick="daffyactivateAgent(<?php echo $agent['id']; ?>); return false" class="text-info">Activate </a>	
			 <?php }else{?>
			<a href="#" onclick="tataactivateAgent(<?php echo $agent['id']; ?>,''); return false" class="text-info">Activate </a>	
			 <?php }?>
			 <span class="text-dark"> | </span><a href="#" onclick="deletAgent_db(<?php echo $agent['id']; ?>,0); return false" class="text-danger">Delete </a>
    	</td>
	</tr>
    <?php } ?>
    </tbody>
</table>
   
  <div class="modal" id="addAgentModal" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
	  <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4>Add Agent</h4>
	  </div>
		<form >
        <div class="modal-body" >
			<input type="hidden" name="name" id="name" value="">
			<input type="hidden" name="ext" id="ext" value="">
			<?php 
			//echo render_select('staff_id', $agents, array('staffid', array('firstname', 'lastname')), 'staff_id', '', array());
      ?>

		<div class="form-group">
			<label for="clients_default_theme" class="control-label">IVR</label>
			<select name="ivr_id" id="ivr_id" class="form-control selectpicker">
				<option value="" >Select IVR</option>
				<?php foreach($active_ivrs as $ivr): ?>
				<option id="ivr_source_from_<?= $ivr->id ?>" value="<?= $ivr->id ?>" data-source_from="<?= $ivr->source_from?>"><?= $ivr->ivr_name ?> - <?= $vendors[$ivr->source_from] ?></option>
				<?php endforeach; ?>
			</select>
			<span id="ivr_id_val" class="errmsg"></span>
		</div>

      <div class="form-group select-placeholder contactid input-group-select">
        <label class="control-label"><small class="req text-danger">* </small>Agent</label>
        <div class="dropdown bootstrap-select emp_id input-group-select show-tick bs3 bs3-has-addon" style="width: 100%;">
          <select id="staff_id" name="staff_id" class="emp_id selectpicker" data-actions-box="1" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true" tabindex="-98" required>
            <option value="">Nothing Selected</option>
            <?php
                if(isset($agents)){
                    foreach($agents as $emp){
                        echo '<option value="'.$emp['staffid'].'" >'.$emp['firstname'].'</option>';
                    }
                }
            ?>
          </select>
        </div>
        <span id="staff_val" class="errmsg"></span>
      </div>
			
			<div class="form-group">
				<label class="control-label"><small class="req text-danger">* </small><?php echo _l('phone'); ?></label>
				<input type="phone" id="phone" name="phone" maxlenght="10" class="form-control" value="" placeholder="Enter Phone Number" required>
				<span id="phone_val" class="errmsg"></span>
			</div>
			<div class="telecmi_settings_wrapper settings_wrapper">
				<div class="form-group">
					<label class="control-label"><small class="req text-danger">* </small><?php echo _l('password'); ?></label>
					<input type="password" id="password" name="password" minlenght="6" class="form-control" value="" placeholder="Enter Password" required>
					<span id="pass_val" class="errmsg"></span>
				</div>
				<div class="form-group">
					<label class="control-label"><small class="req text-danger">* </small><?php echo "Three Digit's Extension"; ?></label>
					<input type="number" id="extension_id" name="extension_id" maxlenght="3" class="form-control" value="" placeholder="Enter Password" required>
					<span id="extension_id_val" class="errmsg"></span>
				</div>
			</div>
			<div class="form-group mtop15">
				<label for="status"><?php echo _l('status'); ?></label><br />
				<select id="status" name="status" class="selectpicker" data-width="100%" >
					<option value="" ></option>
					<option value="online" selected >Online</option>
					<option value="offline" >Offline</option>
					<option value="break" >Break</option>
					<option value="dialer" >Dialer</option>
				</select>
			</div>
			<div class="telecmi_settings_wrapper settings_wrapper">
				<div class="form-group mtop15">
					<label for="sms_alert"><?php echo _l('sms_alert'); ?></label><br />
					<select id="sms_alert" name="sms_alert" class="selectpicker" data-width="100%" >
						<option value="" ></option>
						<option value="true" selected >Yes</option>
						<option value="false" >No</option>
					</select>
				</div>

				<?php $count = range(1,24); ?>
				<div class="form-group mtop15">
					<label for="starttime"><small class="req text-danger">* </small><?php echo _l('starttime'); ?></label><br />
					<select id="starttime" name="starttime" class="selectpicker" data-width="100%" >
					<option value="" ></option>
					<?php foreach($count as $cnt) { ?>
						<option value="<?php echo $cnt; ?>" ><?php echo $cnt; ?></option>
					<?php } ?>
					</select>
					<span id="start_val" class="errmsg"></span>
				</div>
				<?php $count = range(2,24); ?>
				<div class="form-group mtop15">
					<label for="endtime"><small class="req text-danger">* </small><?php echo _l('endtime'); ?></label><br />
					<select id="endtime" name="endtime" class="selectpicker" data-width="100%" >
					<option value="" ></option>
					<?php foreach($count as $cnt) { ?>
						<option value="<?php echo $cnt; ?>" ><?php echo $cnt; ?></option>
					<?php } ?>
					</select>
					<span id="end_val" class="errmsg"></span>
				</div>
			</div>
        </div>
        <div class="modal-footer">
			<button type="button" class="btn btn-primary telecmi_settings_wrapper settings_wrapper" id="addAgent">Save</button>
			<button type="button" class="btn btn-primary daffytel_settings_wrapper settings_wrapper" id="daffyaddAgent">Save</button>
			<button type="button" class="btn btn-primary tata_settings_wrapper settings_wrapper" id="tataaddAgent">Save</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		</div>
	</form>
      </div>
    </div>
  </div>


<div class="modal" id="editAgentModal" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
	  <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4>Edit Agent</h4>
	  </div>
		<form>
        <div class="modal-body" >
			<div class="form-group">
				<label for="clients_default_theme" class="control-label">IVR</label>
				<select name="ivr_id" id="ivr_id" class="form-control selectpicker">
					<option value="" >Select IVR</option>
					<?php foreach($active_ivrs as $ivr): ?>
					<option id="ivr_source_from_<?= $ivr->id ?>" value="<?= $ivr->id ?>" data-source_from="<?= $ivr->source_from?>"><?= $ivr->ivr_name ?> - <?= $vendors[$ivr->source_from] ?></option>
					<?php endforeach; ?>
				</select>
				<span id="ivr_id_val" class="errmsg"></span>
			</div>
			<input type="hidden" name="name" id="name" value="">
			<input type="hidden" name="id" id="id" value="">
			<input type="hidden" name="agentid" id="agentid" value="">
			
			<?php 
			echo render_select('staff_id', $editAgents, array('staffid', array('firstname', 'lastname')), 'staff_id', '', array());
			?>
			<span id="staff_val" class="errmsg"></span>
			<div class="form-group">
				<label class="control-label"><small class="req text-danger">* </small><?php echo _l('phone'); ?></label>
				<input type="phone" id="phone" name="phone" class="form-control" value="" placeholder="Enter Phone Number" required>
				<input type="hidden" id="edit_phone1" class="form-control" value="" placeholder="Enter Phone Number" required>
				<span id="phone_val" class="errmsg"></span>
			</div>
			<div class="telecmi_settings_wrapper settings_wrapper">
			<div class="form-group">
				<label class="control-label"><small class="req text-danger">* </small><?php echo _l('password'); ?></label>
				<input type="password" id="password" name="password" class="form-control" minlenght="6" value="" placeholder="Enter Password" required>
				<span id="pass_val" class="errmsg"></span>
			</div>
	  		</div>
			<div class="form-group mtop15">
				<label for="status"><?php echo _l('status'); ?></label><br />
				<select id="status" name="status" class="selectpicker" data-width="100%" >
					<option value="" ></option>
					<option value="online" selected >Online</option>
					<option value="offline" >Offline</option>
					<option value="break" >Break</option>
					<option value="dialer" >Dialer</option>
				</select>
			</div>
			<div class="telecmi_settings_wrapper settings_wrapper">
			<div class="form-group mtop15">
				<label for="sms_alert"><?php echo _l('sms_alert'); ?></label><br />
				<select id="sms_alert" name="sms_alert" class="selectpicker" data-width="100%" >
					<option value="" ></option>
					<option value="true" selected >Yes</option>
					<option value="false" >No</option>
				</select>
			</div>

			<?php $count = range(1,24); ?>
			<div class="form-group mtop15">
				<label for="starttime"><small class="req text-danger">* </small><?php echo _l('starttime'); ?></label><br />
				<select id="starttime" name="starttime" class="selectpicker" data-width="100%" >
				<option value="" ></option>
				<?php foreach($count as $cnt) { ?>
					<option value="<?php echo $cnt; ?>" ><?php echo $cnt; ?></option>
				<?php } ?>
				</select>
				<span id="start_val" class="errmsg"></span>
			</div>
			<?php $count = range(2,24); ?>
			<div class="form-group mtop15">
				<label for="endtime"><small class="req text-danger">* </small><?php echo _l('endtime'); ?></label><br />
				<select id="endtime" name="endtime" class="selectpicker" data-width="100%" >
				<option value="" ></option>
				<?php foreach($count as $cnt) { ?>
					<option value="<?php echo $cnt; ?>" ><?php echo $cnt; ?></option>
				<?php } ?>
				</select>
				<span id="end_val" class="errmsg"></span>
			</div>
			</div>
        </div>
        <div class="modal-footer">
			<button type="button" class="btn btn-primary telecmi_settings_wrapper settings_wrapper" id="editAgent">Save</button>
			<button type="button" class="btn btn-primary daffytel_settings_wrapper settings_wrapper" id="daffyeditAgent">Save</button>
			<button type="button" class="btn btn-primary tata_settings_wrapper settings_wrapper" id="targeteditAgent">Save</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		</div>
	</form>
      </div>
    </div>
  </div>
<style>
.alert-warning{
	top:80px !important;
}
</style>

<script>
	function show_wrapper(source_from){
		$('.settings_wrapper').hide();
		$('.'+source_from+'_settings_wrapper').show();
	}
	document.addEventListener("DOMContentLoaded", function(event) { 
		show_wrapper('<?= $default_vendor ?>');
		$('[name="ivr_id"]').change(function(){
			var source_from =$('#ivr_source_from_'+$(this).val()).attr('data-source_from');
			show_wrapper(source_from);
		});
	});
</script>

<?php
}else{
	echo 'Should have at least one active IVR';
}