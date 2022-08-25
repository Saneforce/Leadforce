<style>
.error{
	color:red;
}
.m-bt-10{
	margin-bottom:10px;
}
li.dropdown-header.optgroup-1 {
    display: block !important;
}
select.ui-datepicker-month,select.ui-datepicker-year {
    color: #000000;
}
ul.dropdown-menu li:first-child {
    display: block !important;
}
.autocomplete {
  position: relative;
  display: inline-block;
}
.autocomplete-items {
  position: absolute;
  border: 1px solid #d4d4d4;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}

.autocomplete-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #fff; 
  border-bottom: 1px solid #d4d4d4; 
}

/*when hovering an item:*/
.autocomplete-items div:hover {
  background-color: #e9e9e9; 
}

/*when navigating through the items using the arrow keys:*/
.autocomplete-active {
  background-color: DodgerBlue !important; 
  color: #ffffff; 
}

.cur_class {
    height: 21px;
}

/* Absolute Center Spinner */
#overlay_deal,.overlay_new {
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
#overlay_deal:before,.overlay_new:before {
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
#overlay_deal:not(:required),.overlay_new:not(:required) {
  /* hide "loading..." text */
  font: 0/0 a;
  color: transparent;
  text-shadow: none;
  background-color: transparent;
  border: 0;
}

#overlay_deal:not(:required):after,.overlay_new:not(:required):after {
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
.w_88{
	width:88%
}
</style>
<script>
function get_deal(clmn,crow,view_by,measure,date_range,sum_id){
	document.getElementById('overlay_deal1234').style.display = '';
	var view_type = $('#view_type12').val();
	var data = {clmn:clmn,crow:crow,view_by:view_by,measure:measure,date_range:date_range,view_type:view_type,sum_id:sum_id};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/get_deal_summary',
		data: data,
		dataType: '',
		success: function(msg) {
			var obj = JSON.parse(msg);
			$('#req_summary_data').html(obj.summary);
			$('#summary_head').html(obj.cur_record);
			document.getElementById('overlay_deal1234').style.display = 'none';
		}
	});
}
function check_view_by(a){
	document.getElementById('overlay_deal').style.display = '';
	var data = {view_by:a.value};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/check_view_by',
		data: data,
		dataType: '',
		success: function(msg) {
			$('#view_type').show();
			$('#view_by_div').removeClass('col-md-6');
				$('#view_by_div').addClass('col-md-3');
			if(msg!='date'){
				$('#view_type').hide();
				$('#view_by_div').removeClass('col-md-3');
				$('#view_by_div').addClass('col-md-6');
			}
			$('#view_type12').val(msg);
			document.getElementById('overlay_deal').style.display = 'none';
		}
	});
}
function load_share(a){
	document.getElementById('overlay_deal123').style.display = '';
	var data = {report_id:a};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/current_share',
		data: data,
		dataType: '',
		success: function(msg) {
			var myArr = JSON.parse(msg);
			
			$('#shared').val(myArr.shared);
			if(myArr.shared == 'Everyone'){
				$('#ch_staff').hide();
			}else{
				$("#teamleader123").empty().append(myArr.staff);
				$('#teamleader123').selectpicker('refresh');
			}
			document.getElementById('overlay_deal123').style.display = 'none';
		}
	});
}
function change_3_filter(a){
	var req_val = a;
	var cur_val = $('#start_date_edit_'+req_val).val();
	var check_search = $('#check_search_id').val();
	var cur_id12 = $('#cur_id12').val();
		//$('.dropdown-menu open').hide();
		var data = {cur_val:cur_val,req_val:req_val,cur_id12:cur_id12};
		var ajaxRequest = $.ajax({
			type: 'POST',
			url: admin_url + 'reports/set_3_filters',
			data: data,
			dataType: '',
			success: function(msg) {
				var cur_num = $('#cur_num').val();
				for(var i=1;i<=cur_num;i++){
					var a1 = 'filter_'+i;
					var b1 = $('#filter_'+i).val();
					change_filter1(a1,b1);
				}
			}
		});
}
function change_4_filter(a){
	var req_val = a;
	var cur_val = $('#start_date_edit_'+req_val).val();
	var check_search = $('#check_search_id').val();
	var cur_id12 = $('#cur_id12').val();
		//$('.dropdown-menu open').hide();
		var data = {cur_val:cur_val,req_val:req_val,cur_id12:cur_id12};
		var ajaxRequest = $.ajax({
			type: 'POST',
			url: admin_url + 'reports/set_4_filters',
			data: data,
			dataType: '',
			success: function(msg) {
				var cur_num = $('#cur_num').val();
				for(var i=1;i<=cur_num;i++){
					var a1 = 'filter_'+i;
					var b1 = $('#filter_'+i).val();
					change_filter1(a1,b1);
				}
			}
		});
}
function add_public_link(a){
	var data = {req_val:a};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/public_link',
		data: data,
		dataType: '',
		success: function(msg) {
			$('#public_all').html(msg);
		}
	});
}
function delete_link(a){
	var cur_id12 = $('#cur_id12').val();
	var data = {req_val:a,cur_id12:cur_id12};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/delete_link',
		data: data,
		dataType: '',
		success: function(msg) {
			$('#public_all').html(msg);
		}
	});
}
$(function(){
	$('#clientid_add_group_modal').on('submit', function () {
		var name_val = $('#name').val();
		$('#name_id').hide();
		if ( name_val.match(/^[a-zA-Z0-9]+/)  ) {
		} else {
			$('#name_id').show();
			return false;
		}
        return true;
    });
	$("#section_add").submit(function(e) {
		var name_val = $('#name1').val();
		e.preventDefault(); // avoid to execute the actual submit of the form.
		var form = $(this);
		var actionUrl = form.attr('action');
		if ( name_val.match(/^[a-zA-Z0-9]+/) && name_val!='' ) {
			$.ajax({
				type: form.attr('method'),
				url: actionUrl,
				data: form.serialize(), // serializes the form's elements.
				success: function(data)
				{
					alert_float('success', 'Folder Added Successfully');
					var emp = jQuery.parseJSON(data); 
					$("#folder").empty().append(emp.success);
					$('#folder').selectpicker('refresh');
					$('#section_modal').modal('toggle');
				}
			});
		}
		
	});
	$("#update_public_name").submit(function(e) {
		e.preventDefault(); // avoid to execute the actual submit of the form.
		var form = $(this);
		var actionUrl = form.attr('action');
		var name_val = $('#ch_name12').val();
		$('#ch_name12_id').hide();
		if ( name_val.match(/^[a-zA-Z0-9]+/)  ) {
			document.getElementById('overlay_deal12').style.display = '';
			$.ajax({
				type: form.attr('method'),
				url: actionUrl,
				data: form.serialize(), // serializes the form's elements.
				success: function(data)
				{
					$('.dataTable').DataTable().ajax.reload();
					alert_float('success', 'Link Name Updated Successfully');
					document.getElementById('overlay_deal12').style.display = 'none';
					$('#clientid_add_modal_public').modal('toggle');
					var a = '<?php echo $id;?>';
					load_public(a);
				}
			});
		}
		else{
			$('#ch_name12_id').show();
			return false;
		}
	});
	$("#share_report1").submit(function(e) {
		document.getElementById('overlay_deal123').style.display = '';
		e.preventDefault(); // avoid to execute the actual submit of the form.
		var form = $(this);
		var team = $('#teamleader12').val();
		var shared = $('#shared').val();
		var name_val = $('#name').val();
		$('#name_id').hide();
		
		if((team!='' && shared =='Selected Person') || shared =='Everyone'){
				var actionUrl = form.attr('action');
				$.ajax({
					type: form.attr('method'),
					url: actionUrl,
					data: form.serialize(), // serializes the form's elements.
					success: function(data)
					{
						alert_float('success', 'Report Shared Successfully');
						$('#shared_add_modal').modal('toggle');
					}
				});
			document.getElementById('overlay_deal123').style.display = 'none';
		}
		else{
			document.getElementById('overlay_deal123').style.display = 'none';
			$('#error_staff').html('This field is required');
		}
		
		
	});
	$('#end_date_edit_1').datepicker({
		 dateFormat:'dd-mm-yy',
		 calendarWeeks: true,
		autoclose: true,
		changeMonth: true,
		changeYear: true,
		timepicker: false,
		onSelect: function(selectedDate) {
			$('#year_1').val('custom_period');
			$('#year_1').selectpicker('refresh');
			change_4_filter('1');
		}
	});
	$('#start_date_edit_1').datepicker({
		 dateFormat:'dd-mm-yy',
		   timepicker: false,
		   calendarWeeks: true,
		  changeMonth: true,
		  changeYear: true,
		todayHighlight: true,
		  onSelect: function(selectedDate) {
			$('#end_date_edit_1').datepicker('option', 'minDate', selectedDate);
			change_3_filter('1');
			$('#year_1').val('custom_period');
			$('#year_1').selectpicker('refresh');
		  }
	});
	appDatepicker();
});
function check_validate(a){
	var name_val = $('#'+a.id).val();
	$('#'+a.id+'_id').hide();
	if ( name_val.match(/^[a-zA-Z0-9]+/)  ) {
	} else {
		$('#'+a.id+'_id').show();
		return false;
	}
}
function load_public(a){
	  document.getElementById('overlay_deal_public').style.display = '';
	  var data = {cur_id:a};
	  $('#cur_report').val(a);
		var ajaxRequest = $.ajax({
			type: 'POST',
			url: admin_url + 'reports/load_public',
			data: data,
			dataType: '',
			success: function(msg) {
				$('#public_all').html(msg);
				document.getElementById('overlay_deal_public').style.display = 'none';
			}
		});
  }
  function myFunction(a) {
	  /* Get the text field */
	  var copyText = document.getElementById("name_"+a);
	  /* Select the text field */
	  copyText.select();
	  copyText.setSelectionRange(0, 99999); /* For mobile devices */

	  /* Copy the text inside the text field */
	  document.execCommand( 'copy' );
	  alert_float('success', 'Link Copied Successfully');
	  
	  /* Alert the copied text */
 }
 function check_publick(a){
	 document.getElementById('overlay_deal12').style.display = '';
	 document.getElementById('overlay_deal').style.display = '';
	var data = {req_val:a};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/check_publick',
		data: data,
		dataType: '',
		success: function(msg) {
			$('#ch_name12').val(msg);
			$('#link_id').val(a);
			document.getElementById('overlay_deal').style.display = 'none';
			document.getElementById('overlay_deal12').style.display = 'none';
		}
	});
}
function check_name(a){
	$('#'+a.id).val(a.value.trim());
}
function add_filter(){
	document.getElementById('overlay_deal').style.display = '';
	var cur_num = $('#cur_num').val();
	var cur_id12 = $('#cur_id12').val();
	var c_filter_val1 = parseInt($('#c_filter_val1').val())+1;
	$('#c_filter_val1').val(c_filter_val1);
	$('#ch_1_filter1').html(c_filter_val1);
	var data = {cur_num:cur_num,cur_id12:cur_id12};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/add_filter',
		data: data,
		dataType: '',
		success: function(msg) {
			var obj = JSON.parse(msg);
			 $("#ch_ids").html(obj.output);
			 $('#cur_num').val(obj.cur_num);
			 var cur_num = $('#cur_num').val();
			 for(var i=1; i<=cur_num;i++){
				  $('#year_'+i+' option').attr('selected', false);
				 $('#year_'+i).selectpicker('refresh');
				 //$('#filter_option_'+i+' option').attr('selected', false);
				 $('#filter_option_'+i).selectpicker('refresh');
				 //$('#filter_'+i+' option').attr('selected', false);
				 $('#filter_'+i).selectpicker('refresh');
				 $('#end_date_edit_'+i).datepicker({
					 dateFormat:'dd-mm-yy',
					 calendarWeeks: true,
					autoclose: true,
					changeMonth: true,
					changeYear: true,
					timepicker: false,
					onSelect: function(selectedDate) {
						$('#year_'+i).val('custom_period');
						$('#year_'+i).selectpicker('refresh');
					}
				});
				$('#start_date_edit_'+i).datepicker({
					 dateFormat:'dd-mm-yy',
					   timepicker: false,
					   calendarWeeks: true,
					  changeMonth: true,
					  changeYear: true,
					todayHighlight: true,
					  onSelect: function(selectedDate) {
						$('#end_date_edit_'+i).datepicker('option', 'minDate', selectedDate);
						$('#year_'+i).val('custom_period');
						$('#year_'+i).selectpicker('refresh');
					  }
				});
				appDatepicker();
				var a1 = 'filter_'+i;
				var b1 = $('#filter_'+i).val();
				change_filter1(a1,b1);
				document.getElementById('overlay_deal').style.display = 'none';
			 }
		}
	});
	
}
function del_filter(a){
	document.getElementById('overlay_deal').style.display = '';
	var cur_id12 = $('#cur_id12').val();
	var data = {req_val:a,cur_id12:cur_id12};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/del_filter',
		data: data,
		dataType: '',
		success: function(msg) {
			alert_float('success', 'Filter Deleted Successfully');
			location.reload();
		}
	});
}
function check_filter(a){
	document.getElementById('overlay_deal').style.display = '';
	var cur_id = a.id;
	var req_val = cur_id.split("filter_option_");
	req_val = req_val[1];
	/*$('#2_'+req_val+"_filter").show();
	$('#3_'+req_val+"_filter").show();
	$('#4_'+req_val+"_filter").show();
	$("#start_date_edit_"+req_val).show();
	$("#end_date_edit_"+req_val).show();
	if(a.value=='is_empty' || a.value=='is_not_empty'){
		$('#2_'+req_val+"_filter").hide();
		$('#3_'+req_val+"_filter").hide();
		$('#4_'+req_val+"_filter").hide();
		$("#start_date_edit_"+req_val).hide();
		$("#end_date_edit_"+req_val).hide();
	}*/
	var cur_id12 = $('#cur_id12').val();
	var data = {cur_val:a.value,req_val:req_val,cur_id12:cur_id12};
	var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/set_first_filters/'+a.value+'/'+req_val,
		data: data,
		dataType: '',
		success: function(msg) {
			var cur_num = $('#cur_num').val();
			for(var i=1;i<=cur_num;i++){
				var a1 = 'filter_'+i;
				var b1 = $('#filter_'+i).val();
				change_filter1(a1,b1);
			}
		}
	});
}
function change_filter(a){
	var cur_id = a.id;
	var req_val = cur_id.split("filter_");
	req_val = req_val[1];
	var cur_val = a.value;
	document.getElementById('overlay_deal').style.display = '';
	var cur_id12 = $('#cur_id12').val();
	var data = {cur_val:cur_val,req_val:req_val,cur_id12:cur_id12};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/set_filters',
		data: data,
		dataType: '',
		success: function(msg) {
			var cur_num = $('#cur_num').val();
			for(var i=1;i<=cur_num;i++){
				var a1 = 'filter_'+i;
				var b1 = $('#filter_'+i).val();
				change_filter1(a1,b1);
			}
			init_selectpicker();
		}
	});
}
function change_filter1(a,b){
	document.getElementById('overlay_deal').style.display = '';
	var cur_id = a;
	var req_val = cur_id.split("filter_");
	req_val = req_val[1];
	var cur_val = b;
	var cur_id12 = $('#cur_id12').val();
	var data = {cur_val:cur_val,req_val:req_val,cur_id12:cur_id12};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/get_filters',
		data: data,
		dataType: '',
		success: function(msg) {
			$('#ch_dr_'+req_val).html(msg);
			//$('.table-projects').DataTable().ajax.reload();
			if(cur_val=='project_start_date' || cur_val == 'project_deadline'){
				$('#end_date_edit_'+req_val).datepicker({
					 dateFormat:'dd-mm-yy',
					 calendarWeeks: true,
					autoclose: true,
					changeMonth: true,
					changeYear: true,
					timepicker: false,
					onSelect: function(selectedDate) {
						$('#year_'+req_val).val('custom_period');
						$('#year_'+req_val).selectpicker('refresh');
						//change_4_filter(req_val);
					}
				});
				$('#start_date_edit_'+req_val).datepicker({
					 dateFormat:'dd-mm-yy',
					   timepicker: false,
					   calendarWeeks: true,
					  changeMonth: true,
					  changeYear: true,
					todayHighlight: true,
					  onSelect: function(selectedDate) {
						$('#end_date_edit_'+req_val).datepicker('option', 'minDate', selectedDate);
						$('#year_'+req_val).val('custom_period');
						$('#year_'+req_val).selectpicker('refresh');
						//change_3_filter(req_val);
					  }
				});
				appDatepicker();
			}
		
			var year_val = $('#year_val_'+req_val).val(); 
				if(year_val!='' && year_val.indexOf(',') != -1){
					var myArray = year_val.split(",");
					$('#year_'+req_val).selectpicker('val', myArray); 
					
				}else if(year_val!=''){
					$('#year_'+req_val).selectpicker('val',year_val);
				}
				
			 $('#year_'+req_val).selectpicker('refresh');
			 init_selectpicker();
			$('#year_'+req_val).selectpicker({
				liveSearch: true
			 });
			$('#filter_option_'+req_val).selectpicker('refresh');
			if(cur_val=='name'){
				init_ajax_search('project', '#year_'+req_val+'.ajax-search');
			}
			if(cur_val=='company'){
				init_ajax_search('customer', '#year_'+req_val+'.ajax-search');
			}
			if(cur_val=='contact_name' ){
				init_ajax_search('contacts', '#year_'+req_val+'.ajax-search');
			}
			if(cur_val=='teamleader_name'){
				init_ajax_search('manager', '#year_'+req_val+'.ajax-search');
			}
			if(cur_val=='members'|| cur_val=='modified_by' || cur_val=='created_by'){
				init_ajax_search('staff', '#year_'+req_val+'.ajax-search');
			}
			if(cur_val=='contact_email1'){
				init_ajax_search('staff_email', '#year_'+req_val+'.ajax-search');
			}
			if(cur_val=='contact_phone1'){
				init_ajax_search('staff_phone', '#year_'+req_val+'.ajax-search');
			}
			document.getElementById('overlay_deal').style.display = 'none';
			return true;
		}
	});
}
<?php if(!empty($filters)){
	$i1 = 1;
	foreach($filters as $key => $filter1){?>
	change_filter1('filter_<?php echo $i1;?>','<?php echo $filter1;?>');
	<?php $i1++;}
}
?>

 
function change_2_filter(a){
	var cur_id = a.id;
	var req_val = cur_id.split("year_");
	req_val = req_val[1];
	var cur_val = $('#'+cur_id).val();
	var check_search = $('#check_search_id').val();
	var cur_id12 = $('#cur_id12').val();
	if(check_search==''){
		//$('.dropdown-menu open').hide();
		var data = {cur_val:cur_val,req_val:req_val,cur_id12:cur_id12};
		var ajaxRequest = $.ajax({
			type: 'POST',
			url: admin_url + 'reports/set_second_filters',
			data: data,
			dataType: '',
			success: function(msg) {
				var cur_num = $('#cur_num').val();
				for(var i=1;i<=cur_num;i++){
					var a1 = 'filter_'+i;
					var b1 = $('#filter_'+i).val();
					change_filter1(a1,b1);
				}
			}
		});
	}
}
function check_all_val(){
	$('#check_search_id').val('12');
}
function check_all_val1(){
	$('#check_search_id').val('');
	$('.dropdown-menu open').hide();
}
function shared1(a){
	$('#ch_staff').hide();
	if(a.value == 'Selected Person'){
		$('#ch_staff').show();
	}
}
function ch_staff(a){
	if(a.value != ''){
		$('#error_staff').html('');
	}
}
function tab_summary(a){
	$('#filter_tab').val(a);
}
$(function(){
	 $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
     var ProjectsServerParams = {};
     $.each($('._hidden_inputs._filters input'),function(){
         ProjectsServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
     });
     initDataTable('.table-projects', admin_url+'reports/deal_table/<?php echo $id;?>', undefined, [0], ProjectsServerParams, <?php echo hooks()->apply_filters('projects_table_default_order', json_encode(array())); ?>);
     init_ajax_search('customer', '#clientid_copy_project.ajax-search');
});
</script>