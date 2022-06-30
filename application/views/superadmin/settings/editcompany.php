<div class="">
    <div class="page-title">
		<div class="title_left">
			<h3>Edit Company</h3>
		</div>
    </div>
    <div class="clearfix"></div>	
    <div class="row">
        <div class="col-md-12">
                <div class="x_content">
                <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal addcompany"'); ?>
                    <!-- <form class="form-horizontal" method="post" action="<?php echo base_url(); ?>superadmin/editcompany/<?php echo $companydetails['id']; ?>"> -->
						<div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Company Name <span class="required">*</span></label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
								<input type="text" class="form-control" placeholder="Company Name" name="name" value="<?php echo isset($companydetails['name']) ? $companydetails['name'] : ''; ?>" required>
                            </div>
                        </div>
                       
						<div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Email <span class="required">*</span></label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
								<input type="email" class="form-control" placeholder="name@example.com" name="email"  value="<?php echo isset($companydetails['email']) ? $companydetails['email'] : ''; ?>" required>
                            </div>
                        </div>
						<div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Phone Number <span class="required">*</span></label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
								<input type="number" class="form-control" placeholder="Phone Number" name="phone"  value="<?php echo isset($companydetails['phone']) ? $companydetails['phone'] : ''; ?>" required>
                            </div>
                        </div>
						<div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Address</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
								<textarea class="form-control" placeholder="Address" name="address" ><?php echo isset($companydetails['address']) ? $companydetails['address'] : ''; ?></textarea>
                            </div>
                        </div>
						<div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Status <span class="required">*</span></label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
								<select name="status" class="form-control" required>
									<option value="">--Select--</option>
									<option value="Active" <?php echo isset($companydetails['status']) && $companydetails['status']=='Active' ? 'selected' : ''; ?>>Active</option>
									<option value="Inactive" <?php echo isset($companydetails['status']) && $companydetails['status']=='status' ? 'selected' : ''; ?>>Inactive</option>
								</select>
                            </div>
                        </div>
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
								<button type="submit" class="btn btn-success">Save</button>
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