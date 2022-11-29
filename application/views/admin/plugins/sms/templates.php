<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <button type="button" class="btn btn-primary" id="newTemplateModel" style="float:right; margin-bottom:15px;">
                        + Add Template
                        </button>

                        <!-- template records -->
                        <div>
                            <table class="table dt-table scroll-responsive">
                                <thead>
                                <tr>
                                    <th><?php echo _l('no'); ?></th>
                                    <th><?php echo _l('template_name'); ?></th>
                                    <th><?php echo _l('dlt_template_id'); ?></th>
                                    <th><?php echo _l('sender'); ?></th>
                                    <th><?php echo _l('route'); ?></th>
                                    <th><?php echo _l('content') ?></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($templates as $key => $template):?>
                                    <tr>
                                        <td><?php echo $key+1 ?></td>
                                        <td data-order="<?php echo $template->name; ?>"><?php echo $template->name; ?></td>
                                        <td data-order="<?php echo $template->template_id; ?>"><?php echo $template->template_id; ?></td>
                                        <td data-order="<?php echo $template->sender; ?>"><?php echo $template->sender; ?></td>
                                        <td data-order="<?php echo $template->route; ?>"><?php echo $template->route; ?></td>
                                        <td><?php echo $template->content; ?></td>
                                        <td><a href="<?= admin_url('plugins/sms/deleteTemplate/'.$template->id) ?>" class="btn text-danger delete_link"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end template records -->


<div class="modal" id="templateModal" style="display: none;">
    <div class="modal-dialog">
		<div class="modal-content">
			<?php echo form_open(admin_url('plugins/sms/saveTemplate'),array('id'=>'templateForm'));?>
			<div class="modal-header">
				<span class="title">Add Template </span>
			    <button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body" >
                <div class="form-group" >
                    <label for="name" class="control-label "><?php echo _l('template_name') ?></label>
                    <input type="text" id="name" name="name" class="form-control" required placeholder="" >
                </div>
                <div class="form-group" >
                    <label for="template_id" class="control-label "><?php echo _l('dlt_template_id') ?></label>
                    <input type="text" id="template_id" name="template_id" class="form-control" required placeholder="" >
                </div>
                <div class="form-group" >
                    <label for="sender" class="control-label "><?php echo _l('sender') ?></label>
                    <input type="text" id="sender" name="sender" class="form-control" required placeholder="" >
                </div>
                <div class="form-group" >
                    <label for="route" class="control-label "><?php echo _l('route') ?></label>
                    <select name="route" id="route" class="form-control" required>
                        <option value="Transactional">Transactional</option>
                        <option value="Promotional">Promotional</option>
                    </select>
                </div>
                <div class="form-group" >
                    <label for="content" class="control-label "><?php echo _l('content') ?></label>
                    <textarea type="text" id="content" name="content" class="form-control" rows="5" required></textarea>
                </div>
                
            </div>
            <div class="modal-footer">
				<button type="submit" class="btn btn-primary" id="save_template">Save</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
    document.addEventListener("DOMContentLoaded", function(event) { 

        $('.delete_link').click(function(e) {
            if (confirm('Do you want to delete this?') ==false) {
                e.preventDefault();
            }
        });

        $('#newTemplateModel').click(function(){
            $('.template_form_errors').remove();
            $('#templateForm').trigger('reset');
            $('#templateModal').modal('show');
        });

        appValidateForm(
            '#templateForm',
            {
                name: {
                    required: true
                },
                template_id: {
                    required: true
                },
                sender: {
                    required: true
                },
                route: {
                    required: true
                },
                content: {
                    required: true
                }
            },
            function(form) {
                $('.template_form_errors').remove();
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    dataType : "json",
                    success: function (response) {
                        if(response.success ==true){
                            alert_float('success', response.msg);
                            setTimeout(function(){
                                window.location.reload();
                            },1000);
                        }else{
                            alert_float('warning', response.msg);
                            $.each(response.errors, function(k, v) {
                                $('[name="'+k+'"]').parent().append(`<p class="text-danger template_form_errors">`+v+`</p>`)
                            });
                        }
                    },
                });
            }
        );
    });
</script>
</body>
</html>