<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="activities-completed-user-reports" class="hide">
   <table class="table table-activities-completed-user-report scroll-responsive">
      <thead>
         <tr>
            <th><?php echo _l('Users'); ?></th>
            <th><?php echo _l('All Activity '); ?></th>
            <?php foreach($tasktypes as $va){ ?>
               <th><?php echo $va['name']; ?></th>
            <?php } ?>
         </tr>
      </thead>
      <tbody></tbody>
   </table>
</div>
<div id="activities-completed-type-reports" class="hide">
   <table class="table table-activities-completed-type-report scroll-responsive">
      <thead>
         <tr>
            <th><?php echo _l('Activity'); ?></th>
            <th><?php echo _l('Result'); ?></th>
            <th><?php echo _l('Percentage'); ?></th>
         </tr>
      </thead>
      <tbody></tbody>
   </table>
</div>
