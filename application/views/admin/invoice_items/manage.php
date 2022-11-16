<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $table_data_temp = $this->invoice_items_model->get_all_table_fields(); ?>
<?php $items_list_column_order = (array)json_decode(get_option('items_list_column')); ?>
<?php $particulars_items_list_column_order = (array)json_decode(get_option('particulars_items_list_column')); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <?php if (has_permission('items', '', 'delete')) { ?>
              <a href="#" data-toggle="modal" data-table=".table-invoice-items" data-target="#items_bulk_actions" class="hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>
              <div class="modal fade bulk_actions" id="items_bulk_actions" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                    </div>
                    <div class="modal-body">
                      <?php //if(has_permission('leads','','delete')){ 
                      ?>
                      <div class="checkbox checkbox-danger">
                        <input type="checkbox" name="mass_delete" id="mass_delete">
                        <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                      </div>
                      <!-- <hr class="mass_delete_separator" /> -->
                      <?php //} 
                      ?>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                      <a href="#" class="btn btn-info" onclick="items_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                    </div>
                  </div>
                  <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
              </div>
              <!-- /.modal -->
            <?php } ?>
            <?php hooks()->do_action('before_items_page_content'); ?>
            <?php if (has_permission('items', '', 'create')) { ?>
              <div class="_buttons">
                <a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#sales_item_modal" onclick="sales_item1()"><?php echo _l('new_invoice_item'); ?></a>
                <?php /* <a href="#" class="btn btn-info pull-left mleft5" data-toggle="modal" data-target="#groups"><?php echo _l('item_groups'); ?></a>*/ ?>
                <a href="<?php echo admin_url('invoice_items/import'); ?>" class="btn btn-info pull-left mleft5"><?php echo _l('import_items'); ?></a>

                <!-- start column order -->
                <div class="btn-group pull-right mleft4 mbot25 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('items_list_column'); ?>">
                  <button type="button" class="btn btn-default" data-toggle="modal" data-target="#items_list_column_orderModal">
                    <i class="fa fa-list" aria-hidden="true"></i>
                  </button>
                </div>
                <!-- Modal -->
                <div class="modal fade" id="items_list_column_orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <?php echo form_open_multipart(admin_url('settings/item_list_column'), array('id' => 'items_list_column')); ?>
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><?php echo _l('items_list_column'); ?></h5>
                      </div>
                      <div class="modal-body">
                        <div class="form-group">

                          <ul id="sortable" class="ui-sortable">
                            <?php if ($items_list_column_order) {
                              foreach ($items_list_column_order as $ckey => $cval) { ?>
                                <li class="ui-state-default ui-sortable-handle"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                  <input type="checkbox" name="settings[items_list_column][<?php echo $ckey; ?>]" value="1" checked="checked" /> <?php echo _l($table_data_temp[$ckey]['ll']); ?>
                                </li>
                            <?php }
                            } ?>
                            <?php foreach ($table_data_temp as $ckey => $cval) {
                              if (!isset($items_list_column_order[$ckey])) { ?>
                                <li class="ui-state-default ui-sortable-handle"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                  <input type="checkbox" name="settings[items_list_column][<?php echo $ckey; ?>]" value="1" /> <?php echo _l($cval['ll']); ?>
                                </li>
                            <?php }
                            } ?>

                          </ul>

                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                      </div>
                      </form>
                    </div>
                  </div>
                </div>
                <!-- end column order -->

                <!-- start particulars column order -->
                <div class="btn-group pull-right mleft4 mbot25 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('particulars_items_list_column'); ?>">
                  <button type="button" class="btn btn-default" data-toggle="modal" data-target="#particulars_items_list_column_orderModal">
                    <i class="fa fa-money" aria-hidden="true"></i>
                  </button>
                </div>
                <!-- Modal -->
                <div class="modal fade" id="particulars_items_list_column_orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <?php echo form_open_multipart(admin_url('settings/particular_item_list_column'), array('id' => 'particulars_items_list_column')); ?>
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><?php echo _l('particulars_items_list_column'); ?></h5>
                      </div>
                      <div class="modal-body">
                        <div class="form-group">

                          <ul id="sortable1" class="ui-sortable">
                            <?php if ($particulars_items_list_column_order) {
                              foreach ($particulars_items_list_column_order as $ckey => $cval) { if($ckey =='name'){continue;}?>
                                <li class="ui-state-default ui-sortable-handle"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                  <input class="particulars_items_list_column_checkbox" type="checkbox" name="settings[particulars_items_list_column][<?php echo $ckey; ?>]" value="1" checked="checked" /> <?php echo _l($table_data_temp[$ckey]['ll']); ?>
                                </li>
                            <?php }
                            } ?>
                            <?php foreach ($table_data_temp as $ckey => $cval) { if($ckey =='name'){continue;}
                              if (!isset($particulars_items_list_column_order[$ckey])) { ?>
                                <li class="ui-state-default ui-sortable-handle"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                  <input class="particulars_items_list_column_checkbox" type="checkbox" name="settings[particulars_items_list_column][<?php echo $ckey; ?>]" value="1" /> <?php echo _l($cval['ll']); ?>
                                </li>
                            <?php }
                            } ?>

                          </ul>

                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                      </div>
                      </form>
                    </div>
                  </div>
                </div>
                <!-- end particulars column order -->


              </div>
              <div class="clearfix"></div>
              <hr class="hr-panel-heading" />
            <?php } ?>
            <?php
            $table_data = [];

            if (has_permission('items', '', 'delete')) {
              $table_data[] = '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="invoice-items"><label></label></div>';
            }
            foreach ($items_list_column_order as $ckey => $cval) {
              if (isset($table_data_temp[$ckey])) {
                $table_data[] = _l($table_data_temp[$ckey]['ll']);
              }
            }
            render_datatable($table_data, 'invoice-items'); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->load->view('admin/invoice_items/item'); ?>
<div class="modal fade" id="groups" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          <?php echo _l('item_groups'); ?>
        </h4>
      </div>
      <div class="modal-body">
        <?php if (has_permission('items', '', 'create')) { ?>
          <div class="input-group">
            <input type="text" name="item_group_name" id="item_group_name" class="form-control" placeholder="<?php echo _l('item_group_name'); ?>">
            <span class="input-group-btn">
              <button class="btn btn-info p7" type="button" id="new-item-group-insert"><?php echo _l('new_item_group'); ?></button>
            </span>
          </div>
          <hr />
        <?php } ?>
        <div class="row">
          <div class="container-fluid">
            <table class="table dt-table table-items-groups" data-order-col="1" data-order-type="asc">
              <thead>
                <tr>
                  <th><?php echo _l('id'); ?></th>
                  <th><?php echo _l('item_group_name'); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($items_groups as $group) { ?>
                  <tr class="row-has-options" data-group-row-id="<?php echo $group['id']; ?>">
                    <td data-order="<?php echo $group['id']; ?>"><?php echo $group['id']; ?></td>
                    <td data-order="<?php echo $group['name']; ?>">
                      <span class="group_name_plain_text"><?php echo $group['name']; ?></span>
                      <div class="group_edit hide">
                        <div class="input-group">
                          <input type="text" class="form-control">
                          <span class="input-group-btn">
                            <button class="btn btn-info p8 update-item-group" type="button"><?php echo _l('submit'); ?></button>
                          </span>
                        </div>
                      </div>
                      <div class="row-options">
                        <?php if (has_permission('items', '', 'edit')) { ?>
                          <a href="#" class="edit-item-group">
                            <?php echo _l('edit'); ?>
                          </a>
                        <?php } ?>
                        <?php if (has_permission('items', '', 'delete')) { ?>
                          | <a href="<?php echo admin_url('invoice_items/delete_group/' . $group['id']); ?>" class="delete-item-group _delete text-danger">
                            <?php echo _l('delete'); ?>
                          </a>
                        <?php } ?>
                      </div>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
<style>
  p#tax\[\]-error {
    position: absolute;
    margin-top: 35px;
    white-space: nowrap;
  }

  p#currency\[\]-error {
    position: absolute;
    margin-top: 35px;
    white-space: nowrap;
  }

  .control-label,
  label {
    text-transform: capitalize;
  }
</style>
<script>
  $(function() {

    var notSortableAndSearchableItemColumns = [];
    <?php if (has_permission('items', '', 'delete')) { ?>
      notSortableAndSearchableItemColumns.push(0);
    <?php } ?>

    initDataTable('.table-invoice-items', admin_url + 'invoice_items/table', notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns, 'undefined', [1, 'asc']);

    if (get_url_param('groups_modal')) {
      // Set time out user to see the message
      setTimeout(function() {
        $('#groups').modal('show');
      }, 1000);
    }

    $('#new-item-group-insert').on('click', function() {
      var group_name = $('#item_group_name').val();
      if (group_name != '') {
        $.post(admin_url + 'invoice_items/add_group', {
          name: group_name
        }).done(function() {
          window.location.href = admin_url + 'invoice_items?groups_modal=true';
        });
      }
    });

    $('body').on('click', '.edit-item-group', function(e) {
      e.preventDefault();
      var tr = $(this).parents('tr'),
        group_id = tr.attr('data-group-row-id');
      tr.find('.group_name_plain_text').toggleClass('hide');
      tr.find('.group_edit').toggleClass('hide');
      tr.find('.group_edit input').val(tr.find('.group_name_plain_text').text());
    });

    $('body').on('click', '.update-item-group', function() {
      var tr = $(this).parents('tr');
      var group_id = tr.attr('data-group-row-id');
      name = tr.find('.group_edit input').val();
      if (name != '') {
        $.post(admin_url + 'invoice_items/update_group/' + group_id, {
          name: name
        }).done(function() {
          window.location.href = admin_url + 'invoice_items';
        });
      }
    });
  });

  function items_bulk_action(event) {
    if (confirm_delete()) {
      var mass_delete = $('#mass_delete').prop('checked');
      var ids = [];
      var data = {};

      if (mass_delete == true) {
        data.mass_delete = true;
      }

      var rows = $('.table-invoice-items').find('tbody tr');
      $.each(rows, function() {
        var checkbox = $($(this).find('td').eq(0)).find('input');
        if (checkbox.prop('checked') === true) {
          ids.push(checkbox.val());
        }
      });
      data.ids = ids;
      $(event).addClass('disabled');
      setTimeout(function() {
        $.post(admin_url + 'invoice_items/bulk_action', data).done(function() {
          window.location.reload();
        }).fail(function(data) {
          alert_float('danger', data.responseText);
        });
      }, 200);
    }
  }
  jQuery.extend(jQuery.validator.messages, {
    code: {
      remote: 'fdssf'
    },
    required: "This field is required.",
    remote: "This item already exists.",
    email: "Please enter a valid email address.",
    url: "Please enter a valid URL.",
    date: "Please enter a valid date.",
    dateISO: "Please enter a valid date (ISO).",
    number: "Please enter a valid number.",
    digits: "Please enter only digits.",
    creditcard: "Please enter a valid credit card number.",
    equalTo: "Please enter the same value again.",
    accept: "Please enter a value with a valid extension.",
    maxlength: jQuery.validator.format("Please enter no more than {0} characters."),
    minlength: jQuery.validator.format("Please enter at least {0} characters."),
    rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
    range: jQuery.validator.format("Please enter a value between {0} and {1}."),
    max: jQuery.validator.format("Please enter a value less than or equal to {0}."),
    min: jQuery.validator.format("Please enter a value greater than or equal to {0}.")
  });
</script>

<script>
  $( function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
  } );
  $( function() {
    $('.particulars_items_list_column_checkbox').change(function () {
        var checkedcolumns =$('.particulars_items_list_column_checkbox:checked');
        if(checkedcolumns.length>4){
          $(this).removeAttr('checked');
          alert_float('danger', 'Maximum 4 fields are allowed');
        }
    });
    $( "#sortable1" ).sortable();
    $( "#sortable1" ).disableSelection();
  } );
</script>

</body>

</html>