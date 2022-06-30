				</div>
				<!-- /page content -->
				<!-- footer content -->
				<footer>
					<div class="pull-right">
						Developed by TTS
					</div>
					<div class="clearfix"></div>
				</footer>
				<!-- /footer content -->
			</div>
		</div>

		<!-- jQuery -->
		<script src="<?php echo base_url();?>assets/superadmin/jquery/dist/jquery.min.js"></script>
		<!-- Bootstrap -->
		<script src="<?php echo base_url();?>assets/superadmin/bootstrap/dist/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url() ?>assets/superadmin/js/jquery.js"></script>
		<script type="text/javascript" src="<?php echo base_url() ?>assets/superadmin/js/jquery.dataTables.js"></script>
		<!-- Custom Theme Scripts -->
		<script src="<?php echo base_url();?>assets/superadmin/js/custom.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			var base_url = "<?php echo site_url(); ?>/";
			
			if($('#store').length > 0)
			{
				var transactiondataTable = $('#store').DataTable
				({
					"processing": false,
					"oLanguage": {
						"sInfoFiltered": ""
					},
					"serverSide": true,
					"ajax": {
						url: base_url + 'superadmin/companyView', // json datasource
						type: "post",
						error: function () {  // error handling
							$(".store-error").html("");
							$("#store").append('<tbody class="store-error"><tr><th colspan="4"><?php echo 'No data available'; ?></th></tr></tbody>');
							$("#store_processing").css("display", "none");
						}
					}
				});
			}

			
			$("#shortcode").keypress(function(e) {
				var k;  
				document.all ? k = e.keyCode : k = e.which;  
				return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || (k >= 48 && k <= 57));  
			});
		
			
			var frm = $('.addcompany');

			frm.submit(function (e) {
				e.preventDefault();
				$('#overlay').show();
				$.ajax({
					type: frm.attr('method'),
					url: frm.attr('action'),
					data: frm.serialize(),
					success: function (data) {
						$('#overlay').hide();
						window.location.href = 'superadmin/'+data;
					},
					error: function (data) {
						console.log('An error occurred.');
						console.log(data);
					},
				});
			});
		});
		</script>
	</body>
</html>