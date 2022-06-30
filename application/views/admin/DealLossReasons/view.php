<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body tc-content">
						<h4 class="bold no-margin"><?php echo _l('deallossreasons_details'); ?></h4>
						<hr class="hr-panel-heading" />
						<div class="clearfix"></div>
						<div class="kb-deallossreasons">
							<div class="deallossreasonsname">
								<b><?php echo _l('deallossreasons_name'); ?> </b><?php echo $deallossreasons->name; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
</body>
</html>