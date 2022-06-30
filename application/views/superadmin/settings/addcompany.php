<div class="">
    <div class="page-title">
		<div class="title_left">
			<h3>Add Company</h3>
		</div>
    </div>
    <div class="clearfix"></div>	
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
         <div class="x_content">
                <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal addcompany"'); ?>
                    <!-- <form class="form-horizontal" method="post" action="<?php echo base_url(); ?>superadmin/addcompany"> -->
						<div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Company Name <span class="required">*</span></label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
								<input type="text" class="form-control" placeholder="Company Name" name="name" required>
                            </div>
                        </div>
						<div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Shortcode <span class="required">*</span></label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
								<input type="text" class="form-control" placeholder="Enter Shortcode" name="shortcode" id="shortcode" minlength="3" maxlength="15" required>
                                <p style="color:#ccc">Shortcode should not have Special Charectors</p>
                            </div>
                        </div>
						<div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Admin Email <span class="required">*</span></label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
								<input type="email" class="form-control" placeholder="name@example.com" name="email" required>
                            </div>
                        </div>
						<div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Phone Number <span class="required">*</span></label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
								<input type="number" class="form-control" placeholder="Phone Number" name="phone" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Copy Demo Data </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                            <input type="checkbox" class="form-control" name="demodata" style="width:19px; border:0px; margin:0px;" >
                            </div>
                        </div>
						<div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Address</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
								<textarea class="form-control" placeholder="Address" name="address" ></textarea>
                            </div>
                        </div>
						<div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Status <span class="required">*</span></label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
								<select name="status" class="form-control" required>
									<option value="">--Select--</option>
									<option value="Active">Active</option>
									<option value="Inactive">Inactive</option>
								</select>
                            </div>
                        </div>
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
								<button type="submit" id="submit" class="btn btn-success">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="overlay">
      <div class="loader"></div>
  </div>
<style>
.required{
	color:red;
}
</style>