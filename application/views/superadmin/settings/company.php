<div class="">
	<div class="page-title">
		<div class="title_left">
			<h3>Company</h3>
		</div>
		<div style="float:right">
			<a href="<?php echo site_url(); ?>/superadmin/addcompany" class="btn btn-primary btncolor">Add Company</a>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="row">
		<div class="col-md-12">
			<div class="x_panel">
				<div class="x_content">
					<!-- start project list -->
					<table class="table table-striped" id="store" cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
						<thead>
							<tr>
								<th>Company Name</th>
								<th>Shortcode</th>
								<th>Phone</th>
								<th>Email</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
table td {
  word-wrap: break-word;
  max-width: 400px;
}
.dataTables_length{
	display: none;
}
.btncolor {
	background-color:#0069e8 !important;
	border-color: #0069e8 !important;
}
</style>