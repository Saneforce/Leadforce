<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                     <div class="col-md-4 border-right">
                      <h4 class="no-margin font-medium"><i class="fa fa-usd" aria-hidden="true"></i> <?php echo _l('activities_report_heading'); ?></h4>
                      <hr />
                      <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'activities-added'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('Activities Added'); ?></a>
                      </p>
                        <hr class="hr-10" />
                     <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'activities-completed'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('Activities Completed'); ?></a>
                      </p>
                      <hr class="hr-10" />
                      
                  </div>
                  <div class="col-md-4 border-right">
                    <?php
					echo form_hidden('type-reports');

                    $assigned_attrs = array('data-none-selected-text'=>'All');
                    $tm = array("id" => "", "name" => "All");
                    array_unshift($pipelines, $tm);
                    echo render_select('pipeline_id', $pipelines, array('id', 'name'), 'pipeline', '', $assigned_attrs);
                    ?>
					<?php 
					  if(count($teammembers) > 1) {
							  array_unshift($teammembers, $tm);
					  }
					  if(!empty($need_fields) && in_array("teamleader", $need_fields) ){
						echo render_select('teamleader', $teammembers, array('id', 'name'), 'teamleader', '', $assigned_attrs);
					  }
					?>
                 </div>
                 <div class="col-md-4">
                      <div class="bg-light-gray border-radius-4">
                        <div class="p8">
                             <?php if(isset($currencies)){ ?>
                  <div id="currency" class="form-group hide">
                     <label for="currency"><i class="fa fa-question-circle" data-toggle="tooltip" title="<?php echo _l('report_activities_base_currency_select_explanation'); ?>"></i> <?php echo _l('currency'); ?></label><br />
                     <select class="selectpicker" name="currency" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <?php foreach($currencies as $currency){
                           $selected = '';
                           if($currency['isdefault'] == 1){
                              $selected = 'selected';
                           }
                           ?>
                           <option value="<?php echo $currency['id']; ?>" <?php echo $selected; ?>><?php echo $currency['name']; ?></option>
                           <?php } ?>
                        </select>
                     </div>
                     <?php } ?>
                     <div id="income-years" class="hide mbot15">
                        <label for="payments_years"><?php echo _l('year'); ?></label><br />
                        <select class="selectpicker" name="payments_years" data-width="100%" onchange="total_income_bar_report();" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <?php foreach($payments_years as $year) { ?>
                           <option value="<?php echo $year['year']; ?>"<?php if($year['year'] == date('Y')){echo 'selected';} ?>>
                              <?php echo $year['year']; ?>
                           </option>
                           <?php } ?>
                        </select>
                     </div>
                     <div class="form-group hide" id="report-time">
                        <label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
                        <select class="selectpicker" name="months-report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
                           <option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
                           <option value="today"><?php echo _l('Today'); ?></option>
                           <option value="yesterday"><?php echo _l('Yesterday'); ?></option>
                           <option value="this_week"><?php echo _l('This Week'); ?></option>
                           <option value="last_week"><?php echo _l('Last Week'); ?></option>
                           <option value="this_month"><?php echo _l('this_month'); ?></option>
                           <option value="1"><?php echo _l('last_month'); ?></option>
                           <option value="this_year"><?php echo _l('this_year'); ?></option>
                           <option value="last_year"><?php echo _l('last_year'); ?></option>
                           <option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
                           <option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
                           <option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
                           <option value="custom"><?php echo _l('period_datepicker'); ?></option>
                        </select>
                     </div>
                     <div id="date-range" class="hide mbot15">
                        <div class="row">
                           <div class="col-md-6">
                              <label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
                              <div class="input-group date">
                                 <input type="text" class="form-control datepicker" id="report-from" name="report-from">
                                 <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
                              <div class="input-group date">
                                 <input type="text" class="form-control datepicker" disabled="disabled" id="report-to" name="report-to">
                                 <div class="input-group-addon"><i class="fa fa-calendar calendar-icon"></i>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                        </div>
                      </div>
                  </div>
               </div>
               <div id="report" class="hide">
               <hr class="hr-panel-heading" />
               <h4 class="no-mtop"><span id="heading_dynamic"></span> <?php echo _l('reports_activities_generated_report'); ?>
					<div class="form-group pull-right" style="margin: -10px auto 0px auto; width: 20%;">
                        <select class="selectpicker" name="activities-based-by" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option></option>   
                        <option value="name"><?php echo _l('By Users'); ?></option>
                           <option value="type"><?php echo _l('By Activities Type'); ?></option>
                        </select>
                    </div>
			   </h4>
               <hr class="hr-panel-heading" />
               <?php $this->load->view('admin/reports/includes/activities_added'); ?>
               <?php $this->load->view('admin/reports/includes/activities_completed'); ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
</div>
<?php init_tail(); ?>
<?php $this->load->view('admin/reports/includes/activities_js'); ?>
<style>
.dropdown.open .activities_wons_details{
   margin: 0px auto 0px auto !important;
}
.divdwdr,.divdldr,.divdsdr{
   position: absolute !important;
   width: 90%;
   margin-left: -15%;
   overflow-y: auto;
   z-index: 1;
}
.divdldr{
   margin-left: -10%;

.yes_select a{
	text-decoration : underline;
}
</style>

</body>
</html>
