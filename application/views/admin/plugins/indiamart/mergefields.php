<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="panel_s">
            <div class="panel-body">
                <?php echo form_open(admin_url('plugin/indiamart/saveMergeFields'),array('id'=>'indiamartMergeFiedls')); ?>
                <div class="row">
                    <input type="hidden" name="source_id" id="source_id" value="">
                    <div class="col-md-12">
                        <div class="col-md-6 row">
                            <div class="row org" app-field-wrapper="name">
                                <div class="col-md-12">
                                    <label for="name" class="control-label">
                                        <small class="req text-danger">* </small>Registered Mobile Number</label>
                                </div>
                                <div class="form-group col-md-12">
                                    <input type="text" class="form-control" name="user_account" id="user_account" value="<?php echo $result->user_account; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="org" app-field-wrapper="name">
                                <div class="col-md-12" style="width:110%;">
                                    <div class="col-md-12">
                                        <label for="name" class="control-label">
                                            <small class="req text-danger">* </small>IndiaMART Key</label>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <input type="text" class="form-control" name="unique_key" id="unique_key" value="<?php echo $result->unique_key; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="col-md-12 row">
                            <div class="row org" app-field-wrapper="name">
                                <div class="col-md-12">
                                    <label for="name" class="control-label">
                                        <small class="req text-danger">* </small>Name</label>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select required="1" name="name" class="selectpicker" data-none-selected-text="Nothing Selected">
                                            <option value="0"></option>
                                            <option value="">Nothing Selected</option>
                                            <?php foreach ($indiaMart as $kay => $val) {
                                                if ($val != '') {
                                                    $selected = '';
                                                    if (isset($name[0]) && $val == $name[0]) {
                                                        $selected = ' selected';
                                                    }
                                            ?>
                                                    <option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
                                            <?php
                                                }
                                            } ?>
                                            <!-- <option value="0" <?php if (isset($form) && $form->txt_shape == 0) {
                                                                        echo 'selected';
                                                                    } ?>>Box</option>
                                        <option value="1" <?php if (isset($form) && $form->txt_shape == 1) {
                                                                echo 'selected';
                                                            } ?>>Radious</option> -->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <select required="1" name="name1" class="selectpicker" data-none-selected-text="Nothing Selected">
                                        <option value="0"></option>
                                        <option value="">Nothing Selected</option>
                                        <?php foreach ($indiaMart as $kay => $val) {
                                            if ($val != '') {
                                                $selected = '';
                                                if (isset($name[1]) && $val == $name[1]) {
                                                    $selected = ' selected';
                                                }
                                        ?>
                                                <option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
                                        <?php
                                            }
                                        } ?>
                                        <!-- <option value="0" <?php if (isset($form) && $form->txt_shape == 0) {
                                                                    echo 'selected';
                                                                } ?>>Box</option>
                                    <option value="1" <?php if (isset($form) && $form->txt_shape == 1) {
                                                            echo 'selected';
                                                        } ?>>Radious</option> -->
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select required="1" name="name2" class="selectpicker" data-none-selected-text="Nothing Selected">
                                        <option value="0"></option>
                                        <option value="">Nothing Selected</option>
                                        <?php foreach ($indiaMart as $kay => $val) {
                                            if ($val != '') {
                                                $selected = '';
                                                if (isset($name[2]) && $val == $name[2]) {
                                                    $selected = ' selected';
                                                }
                                        ?>
                                                <option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
                                        <?php
                                            }
                                        } ?>
                                        <!-- <option value="0" <?php if (isset($form) && $form->txt_shape == 0) {
                                                                    echo 'selected';
                                                                } ?>>Box</option>
                                    <option value="1" <?php if (isset($form) && $form->txt_shape == 1) {
                                                            echo 'selected';
                                                        } ?>>Radious</option> -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="form-group row other" app-field-wrapper="name">
                                <div class="col-md-12">
                                    <label for="name" class="control-label">Position</label>
                                </div>
                                <div class="col-md-12">
                                    <select required="1" name="title" class="selectpicker" data-none-selected-text="Nothing Selected">
                                        <option value="0"></option>
                                        <option value="">Nothing Selected</option>
                                        <?php foreach ($indiaMart as $kay => $val) {
                                            if ($val != '') {
                                                $selected = '';
                                                if ($val == $fvs->title) {
                                                    $selected = ' selected';
                                                }
                                        ?>
                                                <option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
                                        <?php
                                            }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="form-group row other" app-field-wrapper="name">
                                <div class="col-md-12">
                                    <label for="name" class="control-label">Email Address</label>
                                </div>
                                <div class="col-md-12">
                                    <select required="1" name="email" class="selectpicker" data-none-selected-text="Nothing Selected">
                                        <option value="0"></option>
                                        <option value="">Nothing Selected</option>
                                        <?php foreach ($indiaMart as $kay => $val) {
                                            if ($val != '') {
                                                $selected = '';
                                                if ($val == $fvs->email) {
                                                    $selected = ' selected';
                                                }
                                        ?>
                                                <option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
                                        <?php
                                            }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 row">
                            <div class="form-group row other" app-field-wrapper="name">
                                <div class="col-md-12">
                                    <label for="name" class="control-label">Website</label>
                                </div>
                                <div class="col-md-12">
                                    <select required="1" name="website" class="selectpicker" data-none-selected-text="Nothing Selected">
                                        <option value="0"></option>
                                        <option value="">Nothing Selected</option>
                                        <?php foreach ($indiaMart as $kay => $val) {
                                            if ($val != '') {
                                                $selected = '';
                                                if ($val == $fvs->website) {
                                                    $selected = ' selected';
                                                }
                                        ?>
                                                <option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
                                        <?php
                                            }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="form-group row other" app-field-wrapper="name">
                                <div class="col-md-12">
                                    <label for="name" class="control-label">Phone</label>
                                </div>
                                <div class="col-md-12">
                                    <select required="1" name="phonenumber" class="selectpicker" data-none-selected-text="Nothing Selected">
                                        <option value="0"></option>
                                        <option value="">Nothing Selected</option>
                                        <?php foreach ($indiaMart as $kay => $val) {
                                            if ($val != '') {
                                                $selected = '';
                                                if ($val == $fvs->phonenumber) {
                                                    $selected = ' selected';
                                                }
                                        ?>
                                                <option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
                                        <?php
                                            }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="row org" app-field-wrapper="name">
                                <div class="col-md-12">
                                    <label for="name" class="control-label">
                                        <small class="req text-danger">* </small>Organization</label>
                                </div>
                                <div class="col-md-4 form-group">
                                    <select required="1" name="lead_company" class="selectpicker" data-none-selected-text="Nothing Selected">
                                        <option value="0"></option>
                                        <option value="">Nothing Selected</option>
                                        <?php foreach ($indiaMart as $kay => $val) {
                                            if ($val != '') {
                                                $selected = '';
                                                if (isset($lead_company[0]) && $val == $lead_company[0]) {
                                                    $selected = ' selected';
                                                }
                                        ?>
                                                <option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
                                        <?php
                                            }
                                        } ?>
                                        <!-- <option value="0" <?php if (isset($form) && $form->txt_shape == 0) {
                                                                    echo 'selected';
                                                                } ?>>Box</option>
                                <option value="1" <?php if (isset($form) && $form->txt_shape == 1) {
                                                        echo 'selected';
                                                    } ?>>Radious</option> -->
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select required="1" name="lead_company1" class="selectpicker" data-none-selected-text="Nothing Selected">
                                        <option value="0"></option>
                                        <option value="">Nothing Selected</option>
                                        <?php foreach ($indiaMart as $kay => $val) {
                                            if ($val != '') {
                                                $selected = '';
                                                if (isset($lead_company[1]) && $val == $lead_company[1]) {
                                                    $selected = ' selected';
                                                }
                                        ?>
                                                <option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
                                        <?php
                                            }
                                        } ?>
                                        <!-- <option value="0" <?php if (isset($form) && $form->txt_shape == 0) {
                                                                    echo 'selected';
                                                                } ?>>Box</option>
                                <option value="1" <?php if (isset($form) && $form->txt_shape == 1) {
                                                        echo 'selected';
                                                    } ?>>Radious</option> -->
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select required="1" name="lead_company2" class="selectpicker" data-none-selected-text="Nothing Selected">
                                        <option value="0"></option>
                                        <option value="">Nothing Selected</option>
                                        <?php foreach ($indiaMart as $kay => $val) {
                                            if ($val != '') {
                                                $selected = '';
                                                if (isset($lead_company[2]) && $val == $lead_company[2]) {
                                                    $selected = ' selected';
                                                }
                                        ?>
                                                <option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
                                        <?php
                                            }
                                        } ?>
                                        <!-- <option value="0" <?php if (isset($form) && $form->txt_shape == 0) {
                                                                    echo 'selected';
                                                                } ?>>Box</option>
                                <option value="1" <?php if (isset($form) && $form->txt_shape == 1) {
                                                        echo 'selected';
                                                    } ?>>Radious</option> -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="form-group row other" app-field-wrapper="name">
                                <div class="col-md-12">
                                    <label for="name" class="control-label">Description</label>
                                </div>
                                <div class="col-md-12">
                                    <select required="1" name="description" class="selectpicker" data-none-selected-text="Nothing Selected">
                                        <option value="0"></option>
                                        <option value="">Nothing Selected</option>
                                        <?php foreach ($indiaMart as $kay => $val) {
                                            if ($val != '') {
                                                $selected = '';
                                                if ($val == $fvs->description) {
                                                    $selected = ' selected';
                                                }
                                        ?>
                                                <option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
                                        <?php
                                            }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-12 row">
                            <div class="form-group row other" app-field-wrapper="name">
                                <div class="col-md-12">
                                    <label for="name" class="control-label">Address</label>
                                </div>
                                <div class="col-md-12">
                                    <select required="1" name="address" class="selectpicker" data-none-selected-text="Nothing Selected">
                                        <option value="0"></option>
                                        <option value="">Nothing Selected</option>
                                        <?php foreach ($indiaMart as $kay => $val) {
                                            if ($val != '') {
                                                $selected = '';
                                                if ($val == $fvs->address) {
                                                    $selected = ' selected';
                                                }
                                        ?>
                                                <option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
                                        <?php
                                            }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="form-group row other" app-field-wrapper="name">
                                <div class="col-md-12">
                                    <label for="name" class="control-label">City</label>
                                </div>
                                <div class="col-md-12">
                                    <select required="1" name="city" class="selectpicker" data-none-selected-text="Nothing Selected">
                                        <option value="0"></option>
                                        <option value="">Nothing Selected</option>
                                        <?php foreach ($indiaMart as $kay => $val) {
                                            if ($val != '') {
                                                $selected = '';
                                                if ($val == $fvs->city) {
                                                    $selected = ' selected';
                                                }
                                        ?>
                                                <option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
                                        <?php
                                            }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="form-group row other" app-field-wrapper="name">
                                <div class="col-md-12">
                                    <label for="name" class="control-label">State</label>
                                </div>
                                <div class="col-md-12">
                                    <select required="1" name="state" class="selectpicker" data-none-selected-text="Nothing Selected">
                                        <option value="0"></option>
                                        <option value="">Nothing Selected</option>
                                        <?php foreach ($indiaMart as $kay => $val) {
                                            if ($val != '') {
                                                $selected = '';
                                                if ($val == $fvs->state) {
                                                    $selected = ' selected';
                                                }
                                        ?>
                                                <option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
                                        <?php
                                            }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="form-group row other" app-field-wrapper="name">
                                <div class="col-md-12">
                                    <label for="name" class="control-label">Country</label>
                                </div>
                                <div class="col-md-12">
                                    <select required="1" name="country" class="selectpicker" data-none-selected-text="Nothing Selected">
                                        <option value="0"></option>
                                        <option value="">Nothing Selected</option>
                                        <?php foreach ($indiaMart as $kay => $val) {
                                            if ($val != '') {
                                                $selected = '';
                                                if ($val == $fvs->country) {
                                                    $selected = ' selected';
                                                }
                                        ?>
                                                <option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
                                        <?php
                                            }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="form-group row other" app-field-wrapper="name">
                                <div class="col-md-12">
                                    <label for="name" class="control-label">Zip</label>
                                </div>
                                <div class="col-md-12">
                                    <select required="1" name="zip" class="selectpicker" data-none-selected-text="Nothing Selected">
                                        <option value="0"></option>
                                        <option value="">Nothing Selected</option>
                                        <?php foreach ($indiaMart as $kay => $val) {
                                            if ($val != '') {
                                                $selected = '';
                                                if ($val == $fvs->zip) {
                                                    $selected = ' selected';
                                                }
                                        ?>
                                                <option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
                                        <?php
                                            }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form_assigned">
                            <?php
                            $assigned_attrs = array();
                            $selected = (isset($fvs) ? $fvs->assigned : get_staff_user_id());
                            if (
                                isset($fvs)
                                && $fvs->assigned == get_staff_user_id()
                                && !is_admin($fvs->assigned)
                                && !has_permission('leads', '', 'view')
                            ) {
                                $assigned_attrs['disabled'] = true;
                            }
                            echo render_select('assigned', $members, array('staffid', array('firstname', 'lastname')), 'lead_add_edit_assigned', $selected, $assigned_attrs);
                            ?>
                        </div>

                    </div>
                    <div class="col-md-12">
                        <h4>Custom Fields</h4>
                    </div>
                    <div class="col-md-12 mtop15">
                        <?php echo render_custom_fields_indiaMart('leads', $result->slug); ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    $(function() {
        appValidateForm($('#indiamartMergeFiedls'), {
            name: 'required',
            user_account: 'required',
            unique_key: 'required',
            lead_company: 'required'
        });
    });

    function merge_fields(invoker, id) {
        var name = $(invoker).data('name');
        $('#source_id').val(id);
        $('#merge').modal('show');
    }
</script>
</body>

</html>