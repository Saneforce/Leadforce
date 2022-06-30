<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="_buttons">
							<a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#category_modal" onclick="check_title()"><?php echo _l('new_category'); ?></a>
						</div>
						<div class="clearfix"></div>
						<hr class="hr-panel-heading" />
						<div class="clearfix"></div>
						<?php render_datatable(array(
							_l('id'),
							_l('name'),
							_l('options')
							),'category'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="category_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">
						<span class="edit-title"><?php echo _l('category_edit'); ?></span>
						<span class="add-title"><?php echo _l('category_add'); ?></span>
					</h4>
				</div>
				<?php echo form_open('admin/category/manage',array('id'=>'tax_form')); ?>
				<?php echo form_hidden('categoryid'); ?>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							
							<?php echo render_input('name','name'); ?>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
					<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
	</div>
	<?php init_tail(); ?>
	<script>
		$(function(){

			initDataTable('.table-category', window.location.href, [2], [2], undefined, [0,'desc']);

			appValidateForm($('form'),{
				name:{
					required:true,
					remote: {
						url: admin_url + "category/category_exists",
						type: 'post',
						data: {
							categoryid:function(){
								return $('input[name="categoryid"]').val();
							}
						}
					}
				}},manage_category);

				// don't allow | charachter in tax name
				// is used for tax name and tax rate separations!
				$('#category_modal input[name="name"]').on('change',function(){
					var val = $(this).val();
					if(val.indexOf('|') > -1){
						val = val.replace('|','');
						// Clean extra spaces in case this char is in the middle with space
						val = val.replace( / +/g, ' ' );
						$(this).val(val);
					}
				});

				$('#category_modal').on('show.bs.modal', function(event) {
					var button = $(event.relatedTarget)
					var id = button.data('id');
				
					$(this).find('button[type="submit"]').prop('disabled',false);
					$('#category_modal input[name="name"]').val('').prop('disabled',false);
					$('#category_modal input[name="categoryid"]').val('')
					$('#category_modal .add-title').removeClass('hide');
					$('#category_modal .edit-title').addClass('hide');
					//$('.tax_is_used_in_expenses_warning').addClass('hide');
					//$('.tax_is_used_in_subscriptions_warning').addClass('hide');
					if (typeof(id) !== 'undefined') {
						$('input[name="categoryid"]').val(id);
						check_edit_title(id);
						var name = $(button).parents('tr').find('td').eq(1).text();
						var is_referenced_expenses = $(button).data('is-referenced-expenses');
						
						var is_referenced_subscriptions = $(button).data('is-referenced-subscriptions');
						
						$('#category_modal .add-title').addClass('hide');
						$('#category_modal .edit-title').removeClass('hide');
						//$('#tax_modal input[name="name"]').val(name).prop('disabled',(is_referenced_expenses == 1 || is_referenced_subscriptions == 1 ? true : false));
						
						//$(this).find('button[type="submit"]').prop('disabled', is_referenced_expenses == 1 || is_referenced_subscriptions == 1)
					}
				});
			});

		function manage_category(form) {
			var data = $(form).serialize();
			var url = form.action;
			$.post(url, data).done(function(response) {
				response = JSON.parse(response);
				if (response.success == true) {
					$('.table-category').DataTable().ajax.reload();
					alert_float('success', response.message);
				} else {
					if(response.message != ''){
						alert_float('warning', response.message);
					}
				}
				$('#category_modal').modal('hide');
			});
			return false;
		}
		function check_title(){
			$('#category_modal .add-title').addClass('hide');
			$('#category_modal .edit-title').removeClass('hide');
		}
		function check_edit_title(req_id){
			$('#category_modal .add-title').removeClass('hide');
			$('#category_modal .edit-title').addClass('hide');
			var data = {cat_id:req_id};
			 var ajaxRequest = $.ajax({
				type: 'POST',
				url: admin_url + 'category/getcategory',
				data: data,
				dataType: '',
				success: function(msg) {
					 $('input[name="name"]').val(msg);
				}
			});
		}
	</script>
</body>
</html>
