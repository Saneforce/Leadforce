<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Render custom fields inputs
 * @param  string  $belongs_to             where this custom field belongs eq invoice, customers
 * @param  mixed   $rel_id                 relation id to set values
 * @param  array   $where                  where in sql - additional
 * @param  array $items_cf_params          used only for custom fields for items operations
 * @return mixed
 */
function render_custom_fields_indiaMart($belongs_to, $rel_id = false, $where = [], $items_cf_params = [], $in_val = false)
{
    // Is custom fields for items and in add/edit
    $items_add_edit_preview = isset($items_cf_params['add_edit_preview']) && $items_cf_params['add_edit_preview'] ? true : false;

    // Is custom fields for items and in add/edit area for this already added
    $items_applied = isset($items_cf_params['items_applied']) && $items_cf_params['items_applied'] ? true : false;

    // Used for items custom fields to add additional name on input
    $part_item_name = isset($items_cf_params['part_item_name']) ? $items_cf_params['part_item_name'] : '';

    // Is this custom fields for predefined items Sales->Items
    $items_pr = isset($items_cf_params['items_pr']) && $items_cf_params['items_pr'] ? true : false;

    $is_admin = is_admin();

    $CI = & get_instance();
    $CI->db->where('active', 1);
    $CI->db->where('fieldto', $belongs_to);

    if (is_array($where) && count($where) > 0 || is_string($where) && $where != '') {
        $CI->db->where($where);
    }

    $CI->db->order_by('field_order', 'asc');
    $fields = $CI->db->get(db_prefix() . 'customfields')->result_array();
    $fields_html = '';

    if (count($fields)) {
        if (!$items_add_edit_preview && !$items_applied) {
            $fields_html .= '<div class="row custom-fields-form-row">';
        }

        foreach ($fields as $field) {
            if ($field['only_admin'] == 1 && !$is_admin) {
                continue;
            }

            $field['name'] = _maybe_translate_custom_field_name($field['name'], $field['slug']);

            $value = '';
            if ($field['bs_column'] == '' || $field['bs_column'] == 0) {
                $field['bs_column'] = 12;
            }

            if (!$items_add_edit_preview && !$items_applied) {
                $fields_html .= '<div class="col-md-' . $field['bs_column'] . '">';
            } elseif ($items_add_edit_preview) {
                $fields_html .= '<td class="custom_field" data-id="' . $field['id'] . '">';
            } elseif ($items_applied) {
                $fields_html .= '<td class="custom_field">';
            }

            if ($is_admin
                && ($items_add_edit_preview == false && $items_applied == false)
                && (!defined('CLIENTS_AREA') || hooks()->apply_filters('show_custom_fields_edit_link_on_clients_area', false))) {
               // $fields_html .= '<a href="' . admin_url('custom_fields/field/' . $field['id']) . '" tabindex="-1" target="_blank" class="custom-field-inline-edit-link"><i class="fa fa-pencil-square-o"></i></a>';
            }

            $CI->db->where('slug', $rel_id);
            $field_val = $CI->db->get(db_prefix() . 'leads_sources')->row();
            $fvs = json_decode($field_val->fields);
            //pre($fvs->custom_fields->leads);
            if(!empty($fvs->custom_fields->leads) && isset($fvs->custom_fields->leads)) {
                foreach($fvs->custom_fields->leads as $fkey => $fval) {
                    if($fkey == $field['id']) {
                        $value = $fval;
                    }
                }
            }

            // if ($rel_id !== false) {
            //     if (!is_array($rel_id)) {
            //         $value = get_custom_field_value($rel_id, $field['id'], ($items_pr ? 'items_pr' : $belongs_to), false);
            //     } else {
            //         if (is_custom_fields_smart_transfer_enabled()) {
            //             // Used only in:
            //             // 1. Convert proposal to estimate, invoice
            //             // 2. Convert estimate to invoice
            //             // This feature is executed only on CREATE, NOT EDIT
            //             $transfer_belongs_to = $rel_id['belongs_to'];
            //             $transfer_rel_id     = $rel_id['rel_id'];
            //             $tmpSlug             = explode('_', $field['slug'], 2);
            //             if (isset($tmpSlug[1])) {
            //                 $CI->db->where('fieldto', $transfer_belongs_to);
            //                 $CI->db->where('slug LIKE "' . $rel_id['belongs_to'] . '_' . $tmpSlug[1] . '%" AND type="' . $field['type'] . '" AND options="' . $field['options'] . '" AND active=1');
            //                 $cfTransfer = $CI->db->get(db_prefix() . 'customfields')->result_array();

            //                 // Don't make mistakes
            //                 // Only valid if 1 result returned
            //                 // + if field names similarity is equal or more then CUSTOM_FIELD_TRANSFER_SIMILARITY%
            //                 //
            //                 if (count($cfTransfer) == 1 && ((similarity($field['name'], $cfTransfer[0]['name']) * 100) >= CUSTOM_FIELD_TRANSFER_SIMILARITY)) {
            //                     $value = get_custom_field_value($transfer_rel_id, $cfTransfer[0]['id'], $transfer_belongs_to, false);
            //                 }
            //             }
            //         }
            //     }
            // }

            $_input_attrs = [];

            if ($field['required'] == 1) {
                $_input_attrs['data-custom-field-required'] = true;
                $_input_attrs['required'] = true;
            }

            if ($field['disalow_client_to_edit'] == 1 && is_client_logged_in()) {
                $_input_attrs['disabled'] = true;
            }

            $_input_attrs['data-fieldto'] = $field['fieldto'];
            $_input_attrs['data-fieldid'] = $field['id'];

            $cf_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';

            if ($part_item_name != '') {
                $cf_name = $part_item_name . '[custom_fields][items][' . $field['id'] . ']';
            }

            if ($items_add_edit_preview) {
                $cf_name = '';
            }

            $field_name = $field['name'];

            // if ($field['type'] == 'input' || $field['type'] == 'number') {
            //     $t = $field['type'] == 'input' ? 'text' : 'number';
            //     $fields_html .= render_input($cf_name, $field_name, $value, $t, $_input_attrs);
            // } elseif ($field['type'] == 'date_picker') {
            //     $fields_html .= render_date_input($cf_name, $field_name, _d($value), $_input_attrs);
            // }  elseif ($field['type'] == 'time_picker') {
            //     $fields_html .= render_time_picker($cf_name, $field_name, ($value), $_input_attrs);
            // } elseif ($field['type'] == 'date_range') {
            //     $fields_html .= render_date_range_picker($cf_name, $field_name, ($value), $_input_attrs);
            // } elseif ($field['type'] == 'date_time_range') {
            //     $fields_html .= render_date_time_range_picker($cf_name, $field_name, ($value), $_input_attrs);
            // } elseif ($field['type'] == 'date_picker_time') {
            //     $fields_html .= render_datetime_input($cf_name, $field_name, _dt($value), $_input_attrs);
            // } elseif ($field['type'] == 'textarea') {
            //     $fields_html .= render_textarea($cf_name, $field_name, $value, $_input_attrs);
            // } elseif ($field['type'] == 'colorpicker') {
            //     $fields_html .= render_color_picker($cf_name, $field_name, $value, $_input_attrs);
            // } elseif ($field['type'] == 'select' || $field['type'] == 'multiselect') {
                $_select_attrs = [];
                $select_attrs  = '';
                $select_name   = $cf_name;

                if ($field['required'] == 1) {
                    $_select_attrs['data-custom-field-required'] = true;
                    $_select_attrs['required'] = true;
                }

                if ($field['disalow_client_to_edit'] == 1 && is_client_logged_in()) {
                    $_select_attrs['disabled'] = true;
                }

                $_select_attrs['data-fieldto'] = $field['fieldto'];
                $_select_attrs['data-fieldid'] = $field['id'];

                if ($field['type'] == 'multiselect') {
                    $_select_attrs['multiple'] = true;
                    $select_name .= '[]';
                }

                foreach ($_select_attrs as $key => $val) {
                    $select_attrs .= $key . '=' . '"' . $val . '" ';
                }

                $fields_html .= '<div class="form-group">';
				
				 if ($field['required'] == 1) {
					 $fields_html .= '<label for="' . $cf_name . '" class="control-label" style="margin-bottom:9px;"> <small class="req text-danger">* </small>' . $field_name . '</label>';
				 }else{
					$fields_html .= '<label for="' . $cf_name . '" class="control-label" style="margin-bottom:9px;">' . $field_name . '</label>';
				 }
                $fields_html .= '<select ' . $select_attrs . ' name="' . $select_name . '" class="' . ($items_add_edit_preview == false ? 'select-placeholder ': '') . 'selectpicker form-control' . ($field['type'] == 'multiselect' ? ' custom-field-multi-select' : '') . '" data-width="100%" data-none-selected-text="' . _l('dropdown_non_selected_tex') . '"  data-live-search="true">';

                $fields_html .= '<option value=""' . ($field['type'] == 'multiselect' ? ' class="hidden"' : '') . '></option>';

                //$options = explode(',', $field['options']);
               /* $options = array('QUERY_ID' => 'QUERY_ID',
                        'QTYPE' => 'QTYPE',
                        'SENDERNAME' => 'SENDERNAME',
                        'SENDERMAIL' => 'SENDERMAIL',
                        'MOB' => 'MOB',
                        'GLUSER_USR_COMPANYNAME' => 'GLUSER_USR_COMPANYNAME',
                        'ENQ_ADDRESS' => 'ENQ_ADDRESS',
                        'ENQ_CITY' => 'ENQ_CITY',
                        'ENQ_STATE' => 'ENQ_STATE',
                        'COUNTRY_ISO' => 'COUNTRY_ISO',
                        'PRODUCT_NAME' => 'PRODUCT_NAME',
                        'ENQ_MESSAGE' => 'ENQ_MESSAGE',
                        'DATE_RE' => 'DATE_RE',
                        'DATE_R' => 'DATE_R',
                        'DATE_TIME_RE' => 'DATE_TIME_RE',
                        'LOG_TIME' => 'LOG_TIME',
                        'QUERY_MODID' => 'QUERY_MODID',
                        'ENQ_CALL_DURATION' => 'ENQ_CALL_DURATION',
                        'ENQ_RECEIVER_MOB' => 'ENQ_RECEIVER_MOB',
                        'EMAIL_ALT' => 'EMAIL_ALT',
                        'MOBILE_ALT' => 'MOBILE_ALT');*/
				 $options = array('UNIQUE_QUERY_ID' => 'QUERY_ID',
        'QUERY_TYPE' => 'QTYPE',
        'SENDER_NAME' => 'SENDERNAME',
        'SENDER_EMAIL' => 'SENDERMAIL',
        'SENDER_MOBILE' => 'MOB',
       // 'GLUSER_USR_COMPANYNAME' => 'GLUSER_USR_COMPANYNAME',
        'SENDER_ADDRESS' => 'ENQ_ADDRESS',
        'SENDER_CITY' => 'ENQ_CITY',
        'ENQ_STATE' => 'ENQ_STATE',
        'SENDER_COUNTRY_ISO' => 'COUNTRY_ISO',
        'QUERY_PRODUCT_NAME' => 'PRODUCT_NAME',
        'QUERY_MESSAGE' => 'ENQ_MESSAGE',
        //'DATE_RE' => 'DATE_RE',
      //  'DATE_R' => 'DATE_R',
       // 'DATE_TIME_RE' => 'DATE_TIME_RE',
       // 'LOG_TIME' => 'LOG_TIME',
  //      'QUERY_MODID' => 'QUERY_MODID',
        'CALL_DURATION' => 'ENQ_CALL_DURATION',
        'RECEIVER_MOBILE' => 'ENQ_RECEIVER_MOB',
        'SENDER_EMAIL_ALT' => 'EMAIL_ALT',
        'SENDER_MOBILE_ALT' => 'MOBILE_ALT');

                if ($field['type'] == 'multiselect') {
                    $value = explode(',', $value);
                }

                foreach ($options as $kay => $option) {
                    $option = trim($option);
                    if ($option != '') {
                        $selected = '';
                        if ($option == $value) {
                            $selected = ' selected';
                        }
                        $fields_html .= '<option value="' . $option . '"' . $selected . '>' . $option . '</option>';
                    }
                }
                $fields_html .= '</select>';
                $fields_html .= '</div>';
            // } elseif ($field['type'] == 'checkbox') {
            //     $fields_html .= '<div class="form-group chk">';

            //     $fields_html .= '<br /><label class="control-label' . ($field['display_inline'] == 0 ? ' no-mbot': '') . '" for="' . $cf_name . '[]">' . $field_name . '</label>' . ($field['display_inline'] == 1 ? ' <br />': '');

            //     $options = explode(',', $field['options']);

            //     $value = explode(',', $value);

            //     foreach ($options as $option) {
            //         $checked = '';
            //         // Replace double quotes with single.
            //         $option = htmlentities($option);
            //         $option = trim($option);
            //         foreach ($value as $v) {
            //             $v = trim($v);
            //             if ($v == $option) {
            //                 $checked = 'checked';
            //             }
            //         }

            //         $_chk_attrs                 = [];
            //         $chk_attrs                  = '';
            //         $_chk_attrs['data-fieldto'] = $field['fieldto'];
            //         $_chk_attrs['data-fieldid'] = $field['id'];

            //         if ($field['required'] == 1) {
            //             $_chk_attrs['data-custom-field-required'] = true;
            //             $_chk_attrs['required'] = true;
            //         }

            //         if ($field['disalow_client_to_edit'] == 1 && is_client_logged_in()) {
            //             $_chk_attrs['disabled'] = true;
            //         }
            //         foreach ($_chk_attrs as $key => $val) {
            //             $chk_attrs .= $key . '=' . '"' . $val . '" ';
            //         }

            //         $input_id = 'cfc_' . $field['id'] . '_' . slug_it($option) . '_' . app_generate_hash();

            //         $fields_html .= '<div class="checkbox' . ($field['display_inline'] == 1 ? ' checkbox-inline': '') . '">';
            //         $fields_html .= '<input class="custom_field_checkbox" ' . $chk_attrs . ' ' . set_checkbox($cf_name . '[]', $option) . ' ' . $checked . ' value="' . $option . '" id="' . $input_id . '" type="checkbox" name="' . $cf_name . '[]">';
			// 		if ($field['required'] == 1) {
			// 			$fields_html .= '<label for="' . $input_id . '" class="cf-chk-label"> <small class="req text-danger">* </small>' . $option . '</label>';
			// 		}else{
			// 			$fields_html .= '<label for="' . $input_id . '" class="cf-chk-label">' . $option . '</label>';
			// 		}
            //         $fields_html .= '<input type="hidden" name="' . $cf_name . '[]" value="cfk_hidden">';
            //         $fields_html .= '</div>';
            //     }
            //     $fields_html .= '</div>';
            // } elseif ($field['type'] == 'link') {
            //     $fields_html .= '<div class="form-group cf-hyperlink" data-fieldto="' . $field['fieldto'] . '" data-field-id="' . $field['id'] . '" data-value="' . html_escape($value) . '" data-field-name="' . html_escape($field_name) . '">';
            //     $fields_html .= '<label class="control-label" for="custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']">' . $field_name . '</label></br>';

            //     $fields_html .= '<a id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_popover" type="button" href="javascript:">' . _l('cf_translate_input_link_tip') . '</a>';

            //     $fields_html .= '<input type="hidden" ' . ($field['required'] == 1 ? 'data-custom-field-required="1"' : '') . ' value="" id="custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']" name="custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']">';

            //     $field_template = '';
            //     $field_template .= '<div id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_popover-content" class="hide cfh-field-popover-template"><div class="form-group">';
            //     $field_template .= '<div class="row"><div class="col-md-12"><label class="control-label" for="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_title">' . _l('cf_translate_input_link_title') . '</label>';
            //     $field_template .= '<input type="text"' . ($field['disalow_client_to_edit'] == 1 && is_client_logged_in() ? ' disabled="true" ' : ' ') . 'id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_title" value="" class="form-control">';
            //     $field_template .= '</div>';
            //     $field_template .= '</div>';
            //     $field_template .= '</div>';
            //     $field_template .= '<div class="form-group">';
            //     $field_template .= '<div class="row">';
            //     $field_template .= '<div class="col-md-12">';
            //     $field_template .= '<label class="control-label" for="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_link">' . _l('cf_translate_input_link_url') . '</label>';
            //     $field_template .= '<div class="input-group"><input type="text"' . ($field['disalow_client_to_edit'] == 1 && is_client_logged_in() ? ' disabled="true" ' : ' ') . 'id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_link" value="" class="form-control"><span class="input-group-addon"><a href="#" id="cf_hyperlink_open_' . $field['id'] . '" target="_blank"><i class="fa fa-globe"></i></a></span></div>';
            //     $field_template .= '</div>';
            //     $field_template .= '</div>';
            //     $field_template .= '</div>';
            //     $field_template .= '<div class="row">';
            //     $field_template .= '<div class="col-md-6">';
            //     $field_template .= '<button type="button" id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_btn-cancel" class="btn btn-default btn-md pull-left" value="">' . _l('cancel') . '</button>';
            //     $field_template .= '</div>';
            //     $field_template .= '<div class="col-md-6">';
            //     $field_template .= '<button type="button" id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_btn-save" class="btn btn-info btn-md pull-right" value="">' . _l('apply') . '</button>';
            //     $field_template .= '</div>';
            //     $field_template .= '</div>';
            //     $fields_html .= '<script>';
            //     $fields_html .= 'cfh_popover_templates[\'' . $field['id'] . '\'] = \'' . $field_template . '\';';
            //     $fields_html .= '</script>';
            //     $fields_html .= '</div>';
            // }

            $name = $cf_name;

            if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
                $name .= '[]';
            }

            $fields_html .= form_error($name);
            if (!$items_add_edit_preview && !$items_applied) {
                $fields_html .= '</div>';
            } elseif ($items_add_edit_preview) {
                $fields_html .= '</td>';
            } elseif ($items_applied) {
                $fields_html .= '</td>';
            }
        }

        // close row
        if (!$items_add_edit_preview && !$items_applied) {
            $fields_html .= '</div>';
        }
    }

    return $fields_html;
}

function render_custom_fields($belongs_to, $rel_id = false, $where = [], $items_cf_params = [])
{
    // Is custom fields for items and in add/edit
    $items_add_edit_preview = isset($items_cf_params['add_edit_preview']) && $items_cf_params['add_edit_preview'] ? true : false;

    // Is custom fields for items and in add/edit area for this already added
    $items_applied = isset($items_cf_params['items_applied']) && $items_cf_params['items_applied'] ? true : false;

    // Used for items custom fields to add additional name on input
    $part_item_name = isset($items_cf_params['part_item_name']) ? $items_cf_params['part_item_name'] : '';

    // Is this custom fields for predefined items Sales->Items
    $items_pr = isset($items_cf_params['items_pr']) && $items_cf_params['items_pr'] ? true : false;

    $is_admin = is_admin();

    $CI = & get_instance();
    $CI->db->where('active', 1);
    $CI->db->where('fieldto', $belongs_to);

    if (is_array($where) && count($where) > 0 || is_string($where) && $where != '') {
        $CI->db->where($where);
    }

    $CI->db->order_by('field_order', 'asc');
    $fields = $CI->db->get(db_prefix() . 'customfields')->result_array();

    $fields_html = '';

    if (count($fields)) {
        if (!$items_add_edit_preview && !$items_applied) {
            $fields_html .= '<div class="row custom-fields-form-row">';
        }

        foreach ($fields as $field) {
            if ($field['only_admin'] == 1 && !$is_admin) {
                continue;
            }

            $field['name'] = _maybe_translate_custom_field_name($field['name'], $field['slug']);

            $value = '';
            if ($field['bs_column'] == '' || $field['bs_column'] == 0) {
                $field['bs_column'] = 12;
            }

            if (!$items_add_edit_preview && !$items_applied) {
                $fields_html .= '<div class="col-md-' . $field['bs_column'] . '">';
            } elseif ($items_add_edit_preview) {
                $fields_html .= '<td class="custom_field" data-id="' . $field['id'] . '">';
            } elseif ($items_applied) {
                $fields_html .= '<td class="custom_field">';
            }

            if ($is_admin
                && ($items_add_edit_preview == false && $items_applied == false)
                && (!defined('CLIENTS_AREA') || hooks()->apply_filters('show_custom_fields_edit_link_on_clients_area', false))) {
               // $fields_html .= '<a href="' . admin_url('custom_fields/field/' . $field['id']) . '" tabindex="-1" target="_blank" class="custom-field-inline-edit-link"><i class="fa fa-pencil-square-o"></i></a>';
            }

            if ($rel_id !== false) {
                if (!is_array($rel_id)) {
                    $value = get_custom_field_value($rel_id, $field['id'], ($items_pr ? 'items_pr' : $belongs_to), false);
                } else {
                    if (is_custom_fields_smart_transfer_enabled()) {
                        // Used only in:
                        // 1. Convert proposal to estimate, invoice
                        // 2. Convert estimate to invoice
                        // This feature is executed only on CREATE, NOT EDIT
                        $transfer_belongs_to = $rel_id['belongs_to'];
                        $transfer_rel_id     = $rel_id['rel_id'];
                        $tmpSlug             = explode('_', $field['slug'], 2);
                        if (isset($tmpSlug[1])) {
                            $CI->db->where('fieldto', $transfer_belongs_to);
                            $CI->db->where('slug LIKE "' . $rel_id['belongs_to'] . '_' . $tmpSlug[1] . '%" AND type="' . $field['type'] . '" AND options="' . $field['options'] . '" AND active=1');
                            $cfTransfer = $CI->db->get(db_prefix() . 'customfields')->result_array();

                            // Don't make mistakes
                            // Only valid if 1 result returned
                            // + if field names similarity is equal or more then CUSTOM_FIELD_TRANSFER_SIMILARITY%
                            //
                            if (count($cfTransfer) == 1 && ((similarity($field['name'], $cfTransfer[0]['name']) * 100) >= CUSTOM_FIELD_TRANSFER_SIMILARITY)) {
                                $value = get_custom_field_value($transfer_rel_id, $cfTransfer[0]['id'], $transfer_belongs_to, false);
                            }
                        }
                    }
                }
            }

            $_input_attrs = [];

            if ($field['required'] == 1) {
                $_input_attrs['data-custom-field-required'] = true;
                $_input_attrs['required'] = true;
            }

            if ($field['disalow_client_to_edit'] == 1 && is_client_logged_in()) {
                $_input_attrs['disabled'] = true;
            }

            $_input_attrs['data-fieldto'] = $field['fieldto'];
            $_input_attrs['data-fieldid'] = $field['id'];

            $cf_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';

            if ($part_item_name != '') {
                $cf_name = $part_item_name . '[custom_fields][items][' . $field['id'] . ']';
            }

            if ($items_add_edit_preview) {
                $cf_name = '';
            }

            $field_name = $field['name'];

            if ($field['type'] == 'input' || $field['type'] == 'number') {
                $t = $field['type'] == 'input' ? 'text' : 'number';
                $fields_html .= render_input($cf_name, $field_name, $value, $t, $_input_attrs);
            } elseif ($field['type'] == 'date_picker') {
                $fields_html .= render_date_input($cf_name, $field_name, _d($value), $_input_attrs);
            }  elseif ($field['type'] == 'time_picker') {
                $fields_html .= render_time_picker($cf_name, $field_name, ($value), $_input_attrs);
            } elseif ($field['type'] == 'date_range') {
                $fields_html .= render_date_range_picker($cf_name, $field_name, ($value), $_input_attrs);
            } elseif ($field['type'] == 'date_time_range') {
                $fields_html .= render_date_time_range_picker($cf_name, $field_name, ($value), $_input_attrs);
            } elseif ($field['type'] == 'location') {
                $fields_html .= render_location_picker($cf_name, $field_name, ($value), $_input_attrs);
            } elseif ($field['type'] == 'date_picker_time') {
                $fields_html .= render_datetime_input($cf_name, $field_name, _dt($value), $_input_attrs);
            } elseif ($field['type'] == 'textarea') {
                $fields_html .= render_textarea($cf_name, $field_name, $value, $_input_attrs);
            } elseif ($field['type'] == 'colorpicker') {
                $fields_html .= render_color_picker($cf_name, $field_name, $value, $_input_attrs);
            } elseif ($field['type'] == 'select' || $field['type'] == 'multiselect') {
                $_select_attrs = [];
                $select_attrs  = '';
                $select_name   = $cf_name;

                if ($field['required'] == 1) {
                    $_select_attrs['data-custom-field-required'] = true;
                    $_select_attrs['required'] = true;
                }

                if ($field['disalow_client_to_edit'] == 1 && is_client_logged_in()) {
                    $_select_attrs['disabled'] = true;
                }

                $_select_attrs['data-fieldto'] = $field['fieldto'];
                $_select_attrs['data-fieldid'] = $field['id'];

                if ($field['type'] == 'multiselect') {
                    $_select_attrs['multiple'] = true;
                    $select_name .= '[]';
                }

                foreach ($_select_attrs as $key => $val) {
                    $select_attrs .= $key . '=' . '"' . $val . '" ';
                }

                $fields_html .= '<div class="form-group">';
				
				 if ($field['required'] == 1) {
					 $fields_html .= '<label for="' . $cf_name . '" class="control-label" style="margin-bottom:9px;"> <small class="req text-danger">* </small>' . $field_name . '</label>';
				 }else{
					$fields_html .= '<label for="' . $cf_name . '" class="control-label" style="margin-bottom:9px;">' . $field_name . '</label>';
				 }
                $fields_html .= '<select ' . $select_attrs . ' name="' . $select_name . '" class="' . ($items_add_edit_preview == false ? 'select-placeholder ': '') . 'selectpicker form-control' . ($field['type'] == 'multiselect' ? ' custom-field-multi-select' : '') . '" data-width="100%" data-none-selected-text="' . _l('dropdown_non_selected_tex') . '"  data-live-search="true">';

                $fields_html .= '<option value=""' . ($field['type'] == 'multiselect' ? ' class="hidden"' : '') . '></option>';

                $options = explode(',', $field['options']);

                if ($field['type'] == 'multiselect') {
                    $value = explode(',', $value);
                }

                foreach ($options as $option) {
                    $option = trim($option);
                    if ($option != '') {
                        $selected = '';
                        if ($field['type'] == 'select') {
                            if ($option == $value) {
                                $selected = ' selected';
                            }
                        } else {
                            foreach ($value as $v) {
                                $v = trim($v);
                                if ($v == $option) {
                                    $selected = ' selected';
                                }
                            }
                        }

                        $fields_html .= '<option value="' . $option . '"' . $selected . '' . set_select($cf_name, $option) . '>' . $option . '</option>';
                    }
                }
                $fields_html .= '</select>';
                $fields_html .= '</div>';
            } elseif ($field['type'] == 'checkbox') {
                $fields_html .= '<div class="form-group chk">';

                $fields_html .= '<br /><label class="control-label' . ($field['display_inline'] == 0 ? ' no-mbot': '') . '" for="' . $cf_name . '[]">' . $field_name . '</label>' . ($field['display_inline'] == 1 ? ' <br />': '');

                $options = explode(',', $field['options']);

                $value = explode(',', $value);

                foreach ($options as $option) {
                    $checked = '';
                    // Replace double quotes with single.
                    $option = htmlentities($option);
                    $option = trim($option);
                    foreach ($value as $v) {
                        $v = trim($v);
                        if ($v == $option) {
                            $checked = 'checked';
                        }
                    }

                    $_chk_attrs                 = [];
                    $chk_attrs                  = '';
                    $_chk_attrs['data-fieldto'] = $field['fieldto'];
                    $_chk_attrs['data-fieldid'] = $field['id'];

                    if ($field['required'] == 1) {
                        $_chk_attrs['data-custom-field-required'] = true;
                        $_chk_attrs['required'] = true;
                    }

                    if ($field['disalow_client_to_edit'] == 1 && is_client_logged_in()) {
                        $_chk_attrs['disabled'] = true;
                    }
                    foreach ($_chk_attrs as $key => $val) {
                        $chk_attrs .= $key . '=' . '"' . $val . '" ';
                    }

                    $input_id = 'cfc_' . $field['id'] . '_' . slug_it($option) . '_' . app_generate_hash();

                    $fields_html .= '<div class="checkbox' . ($field['display_inline'] == 1 ? ' checkbox-inline': '') . '">';
                    $fields_html .= '<input class="custom_field_checkbox" ' . $chk_attrs . ' ' . set_checkbox($cf_name . '[]', $option) . ' ' . $checked . ' value="' . $option . '" id="' . $input_id . '" type="checkbox" name="' . $cf_name . '[]">';
					if ($field['required'] == 1) {
						$fields_html .= '<label for="' . $input_id . '" class="cf-chk-label"> <small class="req text-danger">* </small>' . $option . '</label>';
					}else{
						$fields_html .= '<label for="' . $input_id . '" class="cf-chk-label">' . $option . '</label>';
					}
                    $fields_html .= '<input type="hidden" name="' . $cf_name . '[]" value="cfk_hidden">';
                    $fields_html .= '</div>';
                }
                $fields_html .= '</div>';
            } elseif ($field['type'] == 'link') {
                $fields_html .= '<div class="form-group cf-hyperlink" data-fieldto="' . $field['fieldto'] . '" data-field-id="' . $field['id'] . '" data-value="' . html_escape($value) . '" data-field-name="' . html_escape($field_name) . '">';
                $fields_html .= '<label class="control-label" for="custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']">' . $field_name . '</label></br>';

                $fields_html .= '<a id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_popover" type="button" href="javascript:">' . _l('cf_translate_input_link_tip') . '</a>';

                $fields_html .= '<input type="hidden" ' . ($field['required'] == 1 ? 'data-custom-field-required="1"' : '') . ' value="" id="custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']" name="custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']">';

                $field_template = '';
                $field_template .= '<div id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_popover-content" class="hide cfh-field-popover-template"><div class="form-group">';
                $field_template .= '<div class="row"><div class="col-md-12"><label class="control-label" for="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_title">' . _l('cf_translate_input_link_title') . '</label>';
                $field_template .= '<input type="text"' . ($field['disalow_client_to_edit'] == 1 && is_client_logged_in() ? ' disabled="true" ' : ' ') . 'id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_title" value="" class="form-control">';
                $field_template .= '</div>';
                $field_template .= '</div>';
                $field_template .= '</div>';
                $field_template .= '<div class="form-group">';
                $field_template .= '<div class="row">';
                $field_template .= '<div class="col-md-12">';
                $field_template .= '<label class="control-label" for="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_link">' . _l('cf_translate_input_link_url') . '</label>';
                $field_template .= '<div class="input-group"><input type="text"' . ($field['disalow_client_to_edit'] == 1 && is_client_logged_in() ? ' disabled="true" ' : ' ') . 'id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_link" value="" class="form-control"><span class="input-group-addon"><a href="#" id="cf_hyperlink_open_' . $field['id'] . '" target="_blank"><i class="fa fa-globe"></i></a></span></div>';
                $field_template .= '</div>';
                $field_template .= '</div>';
                $field_template .= '</div>';
                $field_template .= '<div class="row">';
                $field_template .= '<div class="col-md-6">';
                $field_template .= '<button type="button" id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_btn-cancel" class="btn btn-default btn-md pull-left" value="">' . _l('cancel') . '</button>';
                $field_template .= '</div>';
                $field_template .= '<div class="col-md-6">';
                $field_template .= '<button type="button" id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_btn-save" class="btn btn-info btn-md pull-right" value="">' . _l('apply') . '</button>';
                $field_template .= '</div>';
                $field_template .= '</div>';
                $fields_html .= '<script>';
                $fields_html .= 'cfh_popover_templates[\'' . $field['id'] . '\'] = \'' . $field_template . '\';';
                $fields_html .= '</script>';
                $fields_html .= '</div>';
            }

            $name = $cf_name;

            if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
                $name .= '[]';
            }

            $fields_html .= form_error($name);
            if (!$items_add_edit_preview && !$items_applied) {
                $fields_html .= '</div>';
            } elseif ($items_add_edit_preview) {
                $fields_html .= '</td>';
            } elseif ($items_applied) {
                $fields_html .= '</td>';
            }
        }

        // close row
        if (!$items_add_edit_preview && !$items_applied) {
            $fields_html .= '</div>';
        }

        // adding custom location field scripts
        $CI = &get_instance();
        if($CI->input->is_ajax_request()){
            $fields_html .=get_custom_field_location_js_data();
        }
        
    }

    return $fields_html;
}

function render_custom_fields_edit($belongs_to, $rel_id = false, $where = [], $items_cf_params = [])
{
    // Is custom fields for items and in add/edit
    $items_add_edit_preview = isset($items_cf_params['add_edit_preview']) && $items_cf_params['add_edit_preview'] ? true : false;

    // Is custom fields for items and in add/edit area for this already added
    $items_applied = isset($items_cf_params['items_applied']) && $items_cf_params['items_applied'] ? true : false;

    // Used for items custom fields to add additional name on input
    $part_item_name = isset($items_cf_params['part_item_name']) ? $items_cf_params['part_item_name'] : '';

    // Is this custom fields for predefined items Sales->Items
    $items_pr = isset($items_cf_params['items_pr']) && $items_cf_params['items_pr'] ? true : false;

    $is_admin = is_admin();

    $CI = & get_instance();
    $CI->db->where('active', 1);
    $CI->db->where('fieldto', $belongs_to);

    if (is_array($where) && count($where) > 0 || is_string($where) && $where != '') {
        $CI->db->where($where);
    }

    $CI->db->order_by('field_order', 'asc');
    $fields = $CI->db->get(db_prefix() . 'customfields')->result_array();

    $fields_html = '';

    if (count($fields)) {
        if (!$items_add_edit_preview && !$items_applied) {
            $fields_html .= '<div class="row custom-fields-form-row">';
        }

        foreach ($fields as $field) {
            if ($field['only_admin'] == 1 && !$is_admin) {
                continue;
            }

            $field['name'] = _maybe_translate_custom_field_name($field['name'], $field['slug']);

            $value = '';
            if ($field['bs_column'] == '' || $field['bs_column'] == 0) {
                $field['bs_column'] = 12;
            }

            if (!$items_add_edit_preview && !$items_applied) {
                $fields_html .= '<div class="col-md-' . $field['bs_column'] . '">';
            } elseif ($items_add_edit_preview) {
                $fields_html .= '<td class="custom_field" data-id="' . $field['id'] . '">';
            } elseif ($items_applied) {
                $fields_html .= '<td class="custom_field">';
            }

            if ($is_admin
                && ($items_add_edit_preview == false && $items_applied == false)
                && (!defined('CLIENTS_AREA') || hooks()->apply_filters('show_custom_fields_edit_link_on_clients_area', false))) {
               // $fields_html .= '<a href="' . admin_url('custom_fields/field/' . $field['id']) . '" tabindex="-1" target="_blank" class="custom-field-inline-edit-link"><i class="fa fa-pencil-square-o"></i></a>';
            }

            if ($rel_id !== false) {
                if (!is_array($rel_id)) {
                    $value = get_custom_field_value($rel_id, $field['id'], ($items_pr ? 'items_pr' : $belongs_to), false);
                } else {
                    if (is_custom_fields_smart_transfer_enabled()) {
                        // Used only in:
                        // 1. Convert proposal to estimate, invoice
                        // 2. Convert estimate to invoice
                        // This feature is executed only on CREATE, NOT EDIT
                        $transfer_belongs_to = $rel_id['belongs_to'];
                        $transfer_rel_id     = $rel_id['rel_id'];
                        $tmpSlug             = explode('_', $field['slug'], 2);
                        if (isset($tmpSlug[1])) {
                            $CI->db->where('fieldto', $transfer_belongs_to);
                            $CI->db->where('slug LIKE "' . $rel_id['belongs_to'] . '_' . $tmpSlug[1] . '%" AND type="' . $field['type'] . '" AND options="' . $field['options'] . '" AND active=1');
                            $cfTransfer = $CI->db->get(db_prefix() . 'customfields')->result_array();

                            // Don't make mistakes
                            // Only valid if 1 result returned
                            // + if field names similarity is equal or more then CUSTOM_FIELD_TRANSFER_SIMILARITY%
                            //
                            if (count($cfTransfer) == 1 && ((similarity($field['name'], $cfTransfer[0]['name']) * 100) >= CUSTOM_FIELD_TRANSFER_SIMILARITY)) {
                                $value = get_custom_field_value($transfer_rel_id, $cfTransfer[0]['id'], $transfer_belongs_to, false);
                            }
                        }
                    }
                }
            }

            $_input_attrs = [];

            if ($field['required'] == 1) {
                $_input_attrs['data-custom-field-required'] = true;
                $_input_attrs['required'] = true;
            }

            if ($field['disalow_client_to_edit'] == 1 && is_client_logged_in()) {
                $_input_attrs['disabled'] = true;
            }

            $_input_attrs['data-fieldto'] = $field['fieldto'];
            $_input_attrs['data-fieldid'] = $field['id'];

            $cf_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';

            if ($part_item_name != '') {
                $cf_name = $part_item_name . '[custom_fields][items][' . $field['id'] . ']';
            }

            if ($items_add_edit_preview) {
                $cf_name = '';
            }

            $field_name = $field['name'];
			$req_field_name = "'".$field['id']."'";
			$fields_html .= '<div class="form-group select-placeholder"><label for="status" class="control-label required">'.$field_name.'</label>
					<select required="true" data-actions-box="false" name="'.$field['id'].'" class="selectpicker" data-width="100%" onchange="check_need_field('.$req_field_name.',this)">
						<option value="'._l('keep_current_value').'" >'._l('keep_current_value').'</option>
						<option value="'. _l('keep_current_value').'" >'._l('keep_current_value').'</option>
						<option value="'._l('edit_current_value').'" >'._l('edit_current_value').'</option>
					</select></div><div id="div_'.$field['id'].'" style="display:none;margin-top:15px;">';
            if ($field['type'] == 'input' || $field['type'] == 'number') {
                $t = $field['type'] == 'input' ? 'text' : 'number';
				
                $fields_html .= render_input($cf_name, $field_name, $value, $t, $_input_attrs);
            } elseif ($field['type'] == 'date_picker') {
                $fields_html .= render_date_input($cf_name, $field_name, _d($value), $_input_attrs);
            }  elseif ($field['type'] == 'time_picker') {
                $fields_html .= render_time_picker($cf_name, $field_name, ($value), $_input_attrs);
            } elseif ($field['type'] == 'date_range') {
                $fields_html .= render_date_range_picker($cf_name, $field_name, ($value), $_input_attrs);
            } elseif ($field['type'] == 'date_time_range') {
                $fields_html .= render_date_time_range_picker($cf_name, $field_name, ($value), $_input_attrs);
            } elseif ($field['type'] == 'date_picker_time') {
                $fields_html .= render_datetime_input($cf_name, $field_name, _dt($value), $_input_attrs);
            } elseif ($field['type'] == 'textarea') {
                $fields_html .= render_textarea($cf_name, $field_name, $value, $_input_attrs);
            } elseif ($field['type'] == 'colorpicker') {
                $fields_html .= render_color_picker($cf_name, $field_name, $value, $_input_attrs);
            } elseif ($field['type'] == 'select' || $field['type'] == 'multiselect') {
                $_select_attrs = [];
                $select_attrs  = '';
                $select_name   = $cf_name;

                if ($field['required'] == 1) {
                    $_select_attrs['data-custom-field-required'] = true;
                    $_select_attrs['required'] = true;
                }

                if ($field['disalow_client_to_edit'] == 1 && is_client_logged_in()) {
                    $_select_attrs['disabled'] = true;
                }

                $_select_attrs['data-fieldto'] = $field['fieldto'];
                $_select_attrs['data-fieldid'] = $field['id'];

                if ($field['type'] == 'multiselect') {
                    $_select_attrs['multiple'] = true;
                    $select_name .= '[]';
                }

                foreach ($_select_attrs as $key => $val) {
                    $select_attrs .= $key . '=' . '"' . $val . '" ';
                }

                $fields_html .= '<div class="form-group">';
				
				 if ($field['required'] == 1) {
					 $fields_html .= '<label for="' . $cf_name . '" class="control-label" style="margin-bottom:9px;"> <small class="req text-danger">* </small>' . $field_name . '</label>';
				 }else{
					$fields_html .= '<label for="' . $cf_name . '" class="control-label" style="margin-bottom:9px;">' . $field_name . '</label>';
				 }
                $fields_html .= '<select ' . $select_attrs . ' name="' . $select_name . '" class="' . ($items_add_edit_preview == false ? 'select-placeholder ': '') . 'selectpicker form-control' . ($field['type'] == 'multiselect' ? ' custom-field-multi-select' : '') . '" data-width="100%" data-none-selected-text="' . _l('dropdown_non_selected_tex') . '"  data-live-search="true">';

                $fields_html .= '<option value=""' . ($field['type'] == 'multiselect' ? ' class="hidden"' : '') . '></option>';

                $options = explode(',', $field['options']);

                if ($field['type'] == 'multiselect') {
                    $value = explode(',', $value);
                }

                foreach ($options as $option) {
                    $option = trim($option);
                    if ($option != '') {
                        $selected = '';
                        if ($field['type'] == 'select') {
                            if ($option == $value) {
                                $selected = ' selected';
                            }
                        } else {
                            foreach ($value as $v) {
                                $v = trim($v);
                                if ($v == $option) {
                                    $selected = ' selected';
                                }
                            }
                        }

                        $fields_html .= '<option value="' . $option . '"' . $selected . '' . set_select($cf_name, $option) . '>' . $option . '</option>';
                    }
                }
                $fields_html .= '</select>';
                $fields_html .= '</div>';
            } elseif ($field['type'] == 'checkbox') {
                $fields_html .= '<div class="form-group chk">';

                $fields_html .= '<br /><label class="control-label' . ($field['display_inline'] == 0 ? ' no-mbot': '') . '" for="' . $cf_name . '[]">' . $field_name . '</label>' . ($field['display_inline'] == 1 ? ' <br />': '');

                $options = explode(',', $field['options']);

                $value = explode(',', $value);

                foreach ($options as $option) {
                    $checked = '';
                    // Replace double quotes with single.
                    $option = htmlentities($option);
                    $option = trim($option);
                    foreach ($value as $v) {
                        $v = trim($v);
                        if ($v == $option) {
                            $checked = 'checked';
                        }
                    }

                    $_chk_attrs                 = [];
                    $chk_attrs                  = '';
                    $_chk_attrs['data-fieldto'] = $field['fieldto'];
                    $_chk_attrs['data-fieldid'] = $field['id'];

                    if ($field['required'] == 1) {
                        $_chk_attrs['data-custom-field-required'] = true;
                        $_chk_attrs['required'] = true;
                    }

                    if ($field['disalow_client_to_edit'] == 1 && is_client_logged_in()) {
                        $_chk_attrs['disabled'] = true;
                    }
                    foreach ($_chk_attrs as $key => $val) {
                        $chk_attrs .= $key . '=' . '"' . $val . '" ';
                    }

                    $input_id = 'cfc_' . $field['id'] . '_' . slug_it($option) . '_' . app_generate_hash();

                    $fields_html .= '<div class="checkbox' . ($field['display_inline'] == 1 ? ' checkbox-inline': '') . '">';
                    $fields_html .= '<input class="custom_field_checkbox" ' . $chk_attrs . ' ' . set_checkbox($cf_name . '[]', $option) . ' ' . $checked . ' value="' . $option . '" id="' . $input_id . '" type="checkbox" name="' . $cf_name . '[]">';
					if ($field['required'] == 1) {
						$fields_html .= '<label for="' . $input_id . '" class="cf-chk-label"> <small class="req text-danger">* </small>' . $option . '</label>';
					}else{
						$fields_html .= '<label for="' . $input_id . '" class="cf-chk-label">' . $option . '</label>';
					}
                    $fields_html .= '<input type="hidden" name="' . $cf_name . '[]" value="cfk_hidden">';
                    $fields_html .= '</div>';
                }
                $fields_html .= '</div>';
            } elseif ($field['type'] == 'link') {
                $fields_html .= '<div class="form-group cf-hyperlink" data-fieldto="' . $field['fieldto'] . '" data-field-id="' . $field['id'] . '" data-value="' . html_escape($value) . '" data-field-name="' . html_escape($field_name) . '">';
                $fields_html .= '<label class="control-label" for="custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']">' . $field_name . '</label></br>';

                $fields_html .= '<a id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_popover" type="button" href="javascript:">' . _l('cf_translate_input_link_tip') . '</a>';

                $fields_html .= '<input type="hidden" ' . ($field['required'] == 1 ? 'data-custom-field-required="1"' : '') . ' value="" id="custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']" name="custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']">';

                $field_template = '';
                $field_template .= '<div id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_popover-content" class="hide cfh-field-popover-template"><div class="form-group">';
                $field_template .= '<div class="row"><div class="col-md-12"><label class="control-label" for="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_title">' . _l('cf_translate_input_link_title') . '</label>';
                $field_template .= '<input type="text"' . ($field['disalow_client_to_edit'] == 1 && is_client_logged_in() ? ' disabled="true" ' : ' ') . 'id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_title" value="" class="form-control">';
                $field_template .= '</div>';
                $field_template .= '</div>';
                $field_template .= '</div>';
                $field_template .= '<div class="form-group">';
                $field_template .= '<div class="row">';
                $field_template .= '<div class="col-md-12">';
                $field_template .= '<label class="control-label" for="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_link">' . _l('cf_translate_input_link_url') . '</label>';
                $field_template .= '<div class="input-group"><input type="text"' . ($field['disalow_client_to_edit'] == 1 && is_client_logged_in() ? ' disabled="true" ' : ' ') . 'id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_link" value="" class="form-control"><span class="input-group-addon"><a href="#" id="cf_hyperlink_open_' . $field['id'] . '" target="_blank"><i class="fa fa-globe"></i></a></span></div>';
                $field_template .= '</div>';
                $field_template .= '</div>';
                $field_template .= '</div>';
                $field_template .= '<div class="row">';
                $field_template .= '<div class="col-md-6">';
                $field_template .= '<button type="button" id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_btn-cancel" class="btn btn-default btn-md pull-left" value="">' . _l('cancel') . '</button>';
                $field_template .= '</div>';
                $field_template .= '<div class="col-md-6">';
                $field_template .= '<button type="button" id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_btn-save" class="btn btn-info btn-md pull-right" value="">' . _l('apply') . '</button>';
                $field_template .= '</div>';
                $field_template .= '</div>';
                $fields_html .= '<script>';
                $fields_html .= 'cfh_popover_templates[\'' . $field['id'] . '\'] = \'' . $field_template . '\';';
                $fields_html .= '</script>';
                $fields_html .= '</div>';
            }

            $name = $cf_name;

            if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
                $name .= '[]';
            }

            $fields_html .= form_error($name);
            if (!$items_add_edit_preview && !$items_applied) {
                $fields_html .= '</div>';
            } elseif ($items_add_edit_preview) {
                $fields_html .= '</td>';
            } elseif ($items_applied) {
                $fields_html .= '</td>';
            }
			$fields_html .='</div>';
        }

        // close row
        if (!$items_add_edit_preview && !$items_applied) {
            $fields_html .= '</div>';
        }
    }

    return $fields_html;
}

/**
 * Get custom fields
 * @param  string  $field_to
 * @param  array   $where
 * @param  boolean $exclude_only_admin
 * @return array
 */
function get_custom_fields($field_to, $where = [], $exclude_only_admin = false)
{
    $is_admin = is_admin();
    $CI       = & get_instance();
    $CI->db->where('fieldto', $field_to);
    if ((is_array($where) && count($where) > 0) || (!is_array($where) && $where != '')) {
        $CI->db->where($where);
    }
    if (!$is_admin || $exclude_only_admin == true) {
        $CI->db->where('only_admin', 0);
    }
    $CI->db->where('active', 1);
    $CI->db->order_by('field_order', 'asc');

    $results = $CI->db->get(db_prefix() . 'customfields')->result_array();

    foreach ($results as $key => $result) {
        $results[$key]['name'] = _maybe_translate_custom_field_name($result['name'], $result['slug']);
    }

    return $results;
}

function _maybe_translate_custom_field_name($name, $slug)
{
    return _l('cf_translate_' . $slug, '', false) != 'cf_translate_' . $slug ? _l('cf_translate_' . $slug, '', false) : $name;
}

/**
 * Return custom fields checked to be visible to tables
 * @param  string $field_to field relation
 * @return array
 */
function get_table_custom_fields($field_to)
{
    return get_custom_fields($field_to, ['show_on_table' => 1]);
}
/**
 * Get custom field value
 * @param  mixed $rel_id              the main ID from the table, e.q. the customer id, invoice id
 * @param  mixed $field_id_or_slug    field id, the custom field ID or custom field slug
 * @param  string $field_to           belongs to e.q leads, customers, staff
 * @param  string $format             format date values
 * @return string
 */
function get_custom_field_value($rel_id, $field_id_or_slug, $field_to, $format = true)
{
    $CI = & get_instance();

    $CI->db->select(db_prefix() . 'customfieldsvalues.value,' . db_prefix() . 'customfields.type');
    $CI->db->join(db_prefix() . 'customfields', db_prefix() . 'customfields.id=' . db_prefix() . 'customfieldsvalues.fieldid');
    $CI->db->where(db_prefix() . 'customfieldsvalues.relid', $rel_id);
    if (is_numeric($field_id_or_slug)) {
        $CI->db->where(db_prefix() . 'customfieldsvalues.fieldid', $field_id_or_slug);
    } else {
        $CI->db->where(db_prefix() . 'customfields.slug', $field_id_or_slug);
    }
    $CI->db->where(db_prefix() . 'customfieldsvalues.fieldto', $field_to);

    $row = $CI->db->get(db_prefix() . 'customfieldsvalues')->row();

    $result = '';
    if ($row) {
        $result = $row->value;
        if ($format == true) {
            if ($row->type == 'date_picker') {
                $result = _d($result);
            } elseif ($row->type == 'date_picker_time') {
                $result = _dt($result);
            }
        }
    }

    return $result;
}

function getItemName($id) {
    $CI           = & get_instance();
    $CI->db->where('id', $id);
    $row = $CI->db->get(db_prefix() . 'items')->row();
    return $row->name;
}

/**
 * Check for custom fields, update on $_POST
 * @param  mixed $rel_id        the main ID from the table
 * @param  array $custom_fields all custom fields with id and values
 * @return boolean
 */
function handle_custom_fields_post($rel_id, $custom_fields, $is_cf_items = false)
{
    $affectedRows = 0;
    $CI           = & get_instance();
    foreach ($custom_fields as $key => $fields) {
        foreach ($fields as $field_id => $field_value) {
            $CI->db->where('relid', $rel_id);
            $CI->db->where('fieldid', $field_id);
            $CI->db->where('fieldto', ($is_cf_items ? 'items_pr' : $key));
            $row = $CI->db->get(db_prefix() . 'customfieldsvalues')->row();
            if (!is_array($field_value)) {
                $field_value = trim($field_value);
            }
            // Make necessary checkings for fields
            if (!defined('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST')) {
                $CI->db->where('id', $field_id);
                $field_checker = $CI->db->get(db_prefix() . 'customfields')->row();
                if ($field_checker->type == 'date_picker') {
                    $field_value = to_sql_date($field_value);
                } elseif ($field_checker->type == 'date_picker_time') {
                    $field_value = to_sql_date($field_value, true);
                } elseif ($field_checker->type == 'textarea') {
                    $field_value = nl2br($field_value);
                } elseif ($field_checker->type == 'checkbox' || $field_checker->type == 'multiselect') {
                    if ($field_checker->disalow_client_to_edit == 1 && is_client_logged_in()) {
                        continue;
                    }
                    if (is_array($field_value)) {
                        $v = 0;
                        foreach ($field_value as $chk) {
                            if ($chk == 'cfk_hidden') {
                                unset($field_value[$v]);
                            }
                            $v++;
                        }
                        $field_value = implode(', ', $field_value);
                    }
                }
            }
            if ($row) {
                $CI->db->where('id', $row->id);
                $CI->db->update(db_prefix() . 'customfieldsvalues', [
                    'value' => $field_value,
                ]);
                if ($CI->db->affected_rows() > 0) {
                    $affectedRows++;
                }
            } else {
                if ($field_value != '') {
                    $CI->db->insert(db_prefix() . 'customfieldsvalues', [
                        'relid'   => $rel_id,
                        'fieldid' => $field_id,
                        'fieldto' => $is_cf_items ? 'items_pr' : $key,
                        'value'   => $field_value,
                    ]);
                    $insert_id = $CI->db->insert_id();
                    if ($insert_id) {
                        $affectedRows++;
                    }
                }
            }
        }
    }
    if ($affectedRows > 0) {
        return true;
    }

    return false;
}

/**
 * Return items custom fields array for table html eq invoice html invoice pdf based on usage
 * @param  mixed $rel_id   rel id eq invoice id
 * @param  string $rel_type relation type eq invoice
 * @return array
 */
function get_items_custom_fields_for_table_html($rel_id, $rel_type)
{
    $whereSQL = 'id IN (SELECT fieldid FROM ' . db_prefix() . 'customfieldsvalues WHERE value != "" AND value IS NOT NULL AND fieldto="items" AND relid IN (SELECT id FROM ' . db_prefix() . 'itemable WHERE rel_type="' . $rel_type . '" AND rel_id="' . $rel_id . '") GROUP BY id HAVING COUNT(id) > 0)';

    $whereSQL = hooks()->apply_filters('items_custom_fields_for_table_sql', $whereSQL);

    return get_custom_fields('items', $whereSQL);
}
/**
 * Render custom fields for table add/edit preview area
 * @return string
 */
function render_custom_fields_items_table_add_edit_preview()
{
    $where = hooks()->apply_filters('custom_fields_where_items_table_add_edit_preview', []);

    return render_custom_fields('items', false, $where, [
        'add_edit_preview' => true,
    ]);
}
/**
 * Render custom fields for items for table which are already applied to eq. Invoice
 * @param  array $item      the $item variable from the foreach loop
 * @param  mixed $part_item_name the input name for items eq. newitems or items for existing items
 * @return string
 */
function render_custom_fields_items_table_in($item, $part_item_name)
{
    $item_id = false;

    // When converting eq proposal to estimate,invoice etc to get tha previous item values for auto populate
    if (isset($item['parent_item_id'])) {
        $item_id = $item['parent_item_id'];
    } elseif (isset($item['id']) && $item['id'] != 0) {
        $item_id = $item['id'];
    }

    return render_custom_fields('items', $item_id, [], [
        'items_applied'  => true,
        'part_item_name' => $part_item_name,
    ]);
}

/**
 * Get manually added company custom fields
 * @since Version 1.0.4
 * @return array
 */
function get_company_custom_fields()
{
    $fields = get_custom_fields('company');
    $i      = 0;
    foreach ($fields as $field) {
        $fields[$i]['label'] = $field['name'];
        $fields[$i]['value'] = get_custom_field_value(0, $field['id'], 'company');
        $i++;
    }

    return $fields;
}
/**
 * Custom helper function to check if custom field is of type date
 * @param  array  $field the custom field in loop
 * @return boolean
 */
function is_cf_date($field)
{
    if ($field['type'] == 'date_picker' || $field['type'] == 'date_picker_time') {
        return true;
    }

    return false;
}
/**
* Custom fields only where show on client portal is checked if:
* Is client logged in
* None is logged in
* The format is for email sending, means that the client will get the format
* The request is coming from clients area
* The request is from cron job
*/
function is_custom_fields_for_customers_portal()
{
    if (is_data_for_customer() || DEFINED('CRON')) {
        return true;
    }

    return false;
}
/**
 * Function used for JS to render custom field hyperlink
 * @return stirng
 */

function get_custom_fields_hyperlink_js_function()
{
    ob_start(); ?>
    <script>
        function custom_fields_hyperlink(){
         var cf_hyperlink = $('body').find('.cf-hyperlink');
         if(cf_hyperlink.length){
             $.each(cf_hyperlink,function(){
                var cfh_wrapper = $(this);
                var cfh_field_to = cfh_wrapper.attr('data-fieldto');
                var cfh_field_id = cfh_wrapper.attr('data-field-id');
                var textEl = $('body').find('#custom_fields_'+cfh_field_to+'_'+cfh_field_id+'_popover');
                var hiddenField = $("#custom_fields\\\["+cfh_field_to+"\\\]\\\["+cfh_field_id+"\\\]");
                var cfh_value = cfh_wrapper.attr('data-value');
                hiddenField.val(cfh_value);

                if($(hiddenField.val()).html() != ''){
                    textEl.html($(hiddenField.val()).html());
                }
                var cfh_field_name = cfh_wrapper.attr('data-field-name');
                textEl.popover({
                    html: true,
                    trigger: "manual",
                    placement: "top",
                    title:cfh_field_name,
                    content:function(){
                        return $(cfh_popover_templates[cfh_field_id]).html();
                    }
                }).on("click", function(e){
                    var $popup = $(this);
                    $popup.popover("toggle");
                    var titleField = $("#custom_fields_"+cfh_field_to+"_"+cfh_field_id+"_title");
                    var urlField = $("#custom_fields_"+cfh_field_to+"_"+cfh_field_id+"_link");
                    var ttl = $(hiddenField.val()).html();
                    var cfUrl = $(hiddenField.val()).attr("href");
                    if(cfUrl){
                        $('#cf_hyperlink_open_'+cfh_field_id).attr('href',(cfUrl.indexOf('://') === -1 ? 'http://' + cfUrl : cfUrl));
                    }
                    titleField.val(ttl);
                    urlField.val(cfUrl);
                    $("#custom_fields_"+cfh_field_to+"_"+cfh_field_id+"_btn-save").click(function(){
                        hiddenField.val((urlField.val() != '' ? '<a href="'+urlField.val()+'" target="_blank">' + titleField.val() + '</a>' : ''));
                        textEl.html(titleField.val() == "" ? "<?php echo _l('cf_translate_input_link_tip'); ?>" : titleField.val());
                        $popup.popover("toggle");
                    });
                    $("#custom_fields_"+cfh_field_to+"_"+cfh_field_id+"_btn-cancel").click(function(){
                        if(urlField.val() == ''){
                            hiddenField.val('');
                        }
                        $popup.popover("toggle");
                    });
                });
            });
         }
     }
 </script>

<script type="text/javascript">

function createActivityForivr(data){
    $.ajax({
        type: "POST",
        url: admin_url+'call_settings/createActivity',
        data: data,
        dataType: 'json',
        success: function(result1){
            if(result1.status == 'success') {
                alert_float('success', 'Call Connecting...');
                setTimeout(function(){
                    window.location.reload();
                },1000);
                document.getElementById('overlay_12').style.display = 'none'; 
            } else {
                alert_float('warning', result1.message);
                setTimeout(function(){
                    window.location.reload();
                },1000);
                document.getElementById('overlay_12').style.display = 'none'; 
            }
        }
    });
}
function callTeleCmi(data){
    $.ajax({
        type: "POST",
        url: 'https://piopiy.telecmi.com/v1/adminConnect',
        contentType: "application/json",
        data: JSON.stringify({
            agent_id:data.agent_id,
            token:data.token,
            to:data.to,
            custom:''
        }),
        dataType: 'json',
        async: false,
        success: function(res){
            if(res.code == '200') {
                var request = res.request_id;
                var msg = res.msg;
                var code = res.code;
                createActivityForivr({req:request,msg:msg,code:code,deal_id:data.deal_id,contact_id:data.contact_id,type:data.type,agent:data.agent_id,to:data.to});
            }
        }
    });
}

function callTeleCmiSoftphone(data){
    if(data.channel =='national_softphone'){
        var url = 'https://rest.telecmi.com/v2/ind/click2call';
    }else{
        var url = 'https://rest.telecmi.com/v2/click2call';
    }
    to =String(data.calling_code)+String(data.to);
    $.ajax({
        url: url,
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({
            token: telecmi_get_agent_token(data.agent_id,data.password),
            to: parseInt(to),
        }),
        dataType: 'json',
        async: false,
        success: function(res){
            if(res.code == '200') {
                var request = res.request_id;
                var msg = res.msg;
                var code = res.code;
                createActivityForivr({req:request,msg:msg,code:code,deal_id:data.deal_id,contact_id:data.contact_id,type:data.type,agent:data.agent_id,to:data.to});
            }else{
                alert_float('warning', 'Call Not Connected');
                setTimeout(function(){
                    document.getElementById('overlay_12').style.display = 'none'; 
                    window.location.reload();
                },1000);
            }
        }
    });
}

function calldaffy(to_no,phone,contact_id,deal,agent_id,ftype,cur_val){
	document.getElementById('overlay_12').style.display = '';
	var url1 =  'https://portal.daffytel.com/api/v2/voice/c2c';
	 $.ajax({
		type: "POST",
		url: url1,
		contentType: "application/json",
		data: JSON.stringify({
			from:cur_val.code+phone,
			to:cur_val.code+to_no,
			bridge:cur_val.code+cur_val.app_secret,
			record:1,
			webhook_id:cur_val.webhook
		}),
		dataType: 'json',
		headers: {
			 "Authorization": "Bearer "+cur_val.app_id,
             "Accept": "application/json"
		},
		success: function(result){
			console.info(result);
			var result2 = JSON.parse(JSON.stringify(result));
			if(result2.status!='ERROR'){
                createActivityForivr({req:'',msg:result2.message,code:'200',deal_id:deal,contact_id:contact_id,type:ftype,agent:agent_id,to:to_no,token:''});
			}else{
				alert_float('warning', result2.message);
				setTimeout(function(){
					document.getElementById('overlay_12').style.display = 'none'; 
					window.location.reload();
				},1000);
			}
		},
		error: function(xhr, status, error) {
		  alert_float('warning', 'Call Not Connected');
			setTimeout(function(){
				document.getElementById('overlay_12').style.display = 'none'; 
				window.location.reload();
			},1000);
		}
	 });
}
function calltata(to_no,phone,contact_id,deal,agent_id,ftype,cur_val){
	document.getElementById('overlay_12').style.display = '';
	if(cur_val==''){
	<?php if(empty(CALL_APP_TOKEN)){?>
		var url1 =  'https://api-smartflo.tatateleservices.com/v1/auth/login';
	<?php }else{?>
		var url1 =  'https://api-smartflo.tatateleservices.com/v1/auth/refresh';
	<?php }?>
	}else{
		var url1 =  'https://api-smartflo.tatateleservices.com/v1/auth/login';
	}
	 var loginid = '<?php echo CALL_APP_ID;?>';
	 var secret = '<?php echo CALL_APP_SECRET;?>';
	 $.ajax({
		type: "POST",
		url: url1,
		contentType: "application/json",
		data: JSON.stringify({
			email:loginid,
			<?php if(!empty(CALL_APP_TOKEN)){?>
			token:'<?php echo CALL_APP_TOKEN;?>',
			<?php }?>
			password:secret
		}),
		<?php if(!empty(CALL_APP_TOKEN)){?>
		headers: {
			 "Authorization": "<?php echo CALL_APP_TOKEN;?>"
		},
		<?php }?>
		dataType: 'json',
		async: false,
		success: function(res){
			var res1 = JSON.parse(JSON.stringify(res));
			if(res1.success) {
				 var url2 =  'https://api-smartflo.tatateleservices.com/v1/click_to_call';
				 $.ajax({
					type: "POST",
					url: url2,
					contentType: "application/json",
					data: JSON.stringify({
						agent_number:phone,
						destination_number:to_no
					}),
					dataType: 'json',
					headers: {
						 "Authorization": "Bearer "+res1.access_token
					},
					success: function(result){
						var result2 = JSON.parse(JSON.stringify(result));
                        createActivityForivr({req:'',msg:result2.message,code:'200',deal_id:deal,contact_id:contact_id,type:ftype,agent:agent_id,to:to_no,token:res1.access_token});
					},
					error: function(xhr, status, error) {
					  alert_float('warning', 'Call Not Connected');
						setTimeout(function(){
							document.getElementById('overlay_12').style.display = 'none'; 
							window.location.reload();
						},1000);
					}
				 });
			}
			else{
				tataupdate_access_token_call(to_no,phone,contact_id,deal,agent_id,ftype);
			}
		},
		error: function(xhr, status, error) {
			<?php if(!empty(CALL_APP_TOKEN)){?>
				tataupdate_access_token_call(to_no,phone,contact_id,deal,agent_id,ftype);
			<?php }else{?>
		  alert_float('warning', 'Invalid Credentials');
			setTimeout(function(){
				document.getElementById('overlay_12').style.display = 'none'; 
				window.location.reload();
			},1000);
			<?php }?>
		}
	 });
}
function tataupdate_access_token_call(to_no,phone,contact_id,deal,agent_id,ftype){
	var url13 =  admin_url+'call_settings/updatetoken';
	$.ajax({
		type: "POST",
		url: url13,
		data: {
			token:'',
		},
		dataType: 'json',
		success: function(result){
			var msg1 = JSON.parse(JSON.stringify(result));
			if(msg1.access_token ==''){
				calltata(to_no,phone,contact_id,deal,agent_id,ftype,1)
				
			}else{
				alert_float('warning', 'Invalid Credentials');
				setTimeout(function(){
					window.location.reload();
				},1000);
			}
		}
	});
}

function telecmi_get_agent_token(agent_id,password){
    var token ='';
    $.ajax({
        type: "POST",
        url: 'https://rest.telecmi.com/v2/user/login',
        data: {
            id: agent_id,
            password: password
        },
        dataType: 'json',
        async:false,
        success: function(res){
            if(res.token){
                token =res.token;
            }
        }
    });
    return token;
}
function callfromperson(contact, phone,calling_code) {
    var url =  admin_url+'call_settings/getPersonDeals';
    $.ajax({
        type: "POST",
        url: url,
        data: {contact:contact,phone:phone,listOwn:true},
        dataType: 'json',
        success: function(msg){
            if(msg.status == 'success') {
                if(msg.cnt > 0) {
                    $('#call_person_modal').modal('show');
                    var groupFilter = $('#deals_list');
                        groupFilter.selectpicker('val', '');
                        groupFilter.find('option').remove();
                        groupFilter.selectpicker("refresh");
                    $('#deals_list').append(msg.result);
                    $('#deals_list').selectpicker('refresh');
                    $('#con_id').val(msg.contactId);
                    $('#contact_no').val(msg.contactNumber);
                    $('#calling_code').val(calling_code);
                } else {
                    var deal = msg.pid;
                    var contact = msg.contactId;
                    var contact_no = msg.contactNumber;
                    var ftype = '';
                    if (confirm('Do you want to Make Call?')) {
                        //alert(contact_no); alert(deal);
                        var url =  admin_url+'call_settings/getAppAgentDetails';
                        //$('.followers-div').show();
                        $.ajax({
                            type: "POST",
                            url: url,
                            data: {contact_no:contact_no},
                            dataType: 'json',
                            success: function(msg){
								<?php if(CALL_SOURCE_FROM =='telecmi'){?>
                                if(msg.status == 'success') {
                                    if(msg.channel =='international_softphone' || msg.channel =='national_softphone'){
                                        callTeleCmiSoftphone({channel:msg.channel,agent_id:msg.agent_id,password:msg.password,token:msg.app_secret,to:msg.contact_no,deal_id:0,contact_id:contact,type:ftype,calling_code:calling_code});
                                    }else{
                                        callTeleCmi({agent_id:msg.agent_id,token:msg.app_secret,to:msg.contact_no,deal_id:0,contact_id:contact,type:ftype});
                                    }
                                }
							<?php }
							else  if(CALL_SOURCE_FROM =='daffytel'){ ?>
							calldaffy(phone,msg.agent_no,contact,deal,msg.agent_id,'',msg);
						<?php }else{?>
							console.info(msg);
								calltata(phone,msg.agent_no,contact,deal,msg.agent_id,'','');
							<?php }?>
                                console.log(msg);
                            }
                        });
						
                    }
                }
            } else {
                $('#con_id').val('');
                $('#contact_no').val('');
                alert(msg.result);
            }
        }
    });
}
function clicktocall_create() {
    var deal = $('#deals_list').val();
    var contact = $('#con_id').val();
    var contact_no = $('#contact_no').val();
    var phone = $('#contact_no').val();
    var calling_code = $('#calling_code').val();
    var ftype = '';
    if (confirm('Do you want to Make Call?')) {
        //alert(contact_no); alert(deal);
        var url =  admin_url+'call_settings/getAppAgentDetails';
        //$('.followers-div').show();
        $.ajax({
            type: "POST",
            url: url,
            data: {contact_no:contact_no},
            dataType: 'json',
            success: function(msg){
				<?php if(CALL_SOURCE_FROM =='telecmi'){?>
                if(msg.status == 'success') {
                    if(msg.channel =='international_softphone' || msg.channel =='national_softphone'){
                        callTeleCmiSoftphone({channel:msg.channel,agent_id:msg.agent_id,password:msg.password,token:msg.app_secret,to:msg.contact_no,deal_id:deal,contact_id:contact,type:ftype,calling_code:calling_code});
                    }else{
                        callTeleCmi({agent_id:msg.agent_id,token:msg.app_secret,to:msg.contact_no,deal_id:deal,contact_id:contact,type:ftype});
                    }
                }
			<?php }
				else  if(CALL_SOURCE_FROM =='daffytel'){ ?>
				calldaffy(phone,msg.agent_no,contact,deal,msg.agent_id,'',msg);
			<?php }else{?>
				console.info(msg);
					calltata(phone,msg.agent_no,contact,deal,msg.agent_id,'','');
				<?php }?>
                console.log(msg);
            }
        });
    }

}
function callfromdeal(contact, deal, contact_no, ftype,calling_code) {

    if (confirm('Do you want to Make Call?')) {
        //alert(contact_no); alert(deal);
        var url =  admin_url+'call_settings/getAppAgentDetails';
        //$('.followers-div').show();
        $.ajax({
            type: "POST",
            url: url,
            data: {contact_no:contact_no},
            dataType: 'json',
            success: function(msg){
				<?php if(CALL_SOURCE_FROM =='telecmi'){?>
                if(msg.status == 'success') {
                    var to1 = msg.contact_no;
                    var agent1 = msg.agent_id;
                    if(msg.channel =='international_softphone' || msg.channel =='national_softphone'){
                        callTeleCmiSoftphone({channel:msg.channel,agent_id:msg.agent_id,password:msg.password,token:msg.app_secret,to:msg.contact_no,deal_id:deal,contact_id:contact,type:ftype,calling_code:calling_code});
                    }else{
                        callTeleCmi({agent_id:msg.agent_id,token:msg.app_secret,to:msg.contact_no,deal_id:deal,contact_id:contact,type:ftype});
                    }
                }
			<?php }else  if(CALL_SOURCE_FROM =='daffytel'){ ?>
				calldaffy(contact_no,msg.agent_no,contact,deal,msg.agent_id,ftype,msg);
			<?php }else{?>
					calltata(contact_no,msg.agent_no,contact,deal,msg.agent_id,ftype,'');
			<?php }?>
                console.log(msg);
            }
        });
    }
}

function tele_delete_agent_db(id,dbdel) {
	var url =  admin_url+'call_settings/getAgentDetails';
	//$('.followers-div').show();
	$.ajax({
		type: "POST",
		url: url,
		data: {id:id},
		dataType: 'json',
		success: function(msg){
			//alert(msg.status);
			if(msg.status) {
                if(msg.channel =='international_softphone' || msg.channel =='national_softphone'){
                    var url2 ='https://rest.telecmi.com/v2/user/remove';
                }else{
				    var url2 = 'https://piopiy.telecmi.com/v1/agent/remove';
                }

                
                //$('.followers-div').show();
                $.ajax({
                    type: "POST",
                    url: url2,
                    contentType: "application/json",
                    data: JSON.stringify({
                        id:msg.agent_id,
                        appid:parseInt(msg.app_id),
                        secret:msg.app_secret
                    }),
                    dataType: 'json',
                    async: false,
                    success: function(res){
                        if(res.code == 'cmi-200' || res.code == '200') {

                            if(dbdel==true){
                                deleteagentfromdb(id);
                            }else{
                                var url3 =  admin_url+'call_settings/delete_agent';
                                //$('.followers-div').show();
                                $.ajax({
                                    type: "POST",
                                    url: url3,
                                    data: {
                                        id:id
                                    },
                                    dataType: 'json',
                                    success: function(result){
                                        if(result.status == 'success') {
                                            alert_float('success', result.msg);
                                            setTimeout(function(){
                                                window.location.reload();
                                            },1000);
                                        } else {
                                            alert_float('warning', result.msg);
                                            setTimeout(function(){
                                                window.location.reload();
                                            },1000);
                                        }
                                    }
                                });
                            }
                            //alert(res.msg);
                        }
                        else{
                            if(dbdel==true){
                                deleteagentfromdb(id);
                            }
                            alert_float('warning','Please Delete Manually on Telecmi Portal or sync agents');
                            setTimeout(function(){
                                window.location.reload();
                            },1000);
                        }
                        
                    }
                });
				


			} else {
				alert_float('warning', msg.msg);
				setTimeout(function(){
					window.location.reload();
				},1000);
			}
		},
		error: function(xhr, status, error) {
			console.info(error);
		}
	});
    //return true;
}

function tatadeletAgent_1_db(id,cur_val,dbdel) {
	//if (confirm('Do you want to Deactivate this Agent?')) {
	var url =  admin_url+'call_settings/getAgentDetails';
	//$('.followers-div').show();
	$.ajax({
		type: "POST",
		url: url,
		data: {id:id},
		dataType: 'json',
		success: function(msg){
			var msg1 = JSON.parse(JSON.stringify(msg));
			console.info(msg1);
			
			 if(msg1.phone) {
				 var edit_phone = msg1.phone;
				//var url2 = 'https://piopiy.telecmi.com/v1/agent/remove';
				if(cur_val == ''){
				<?php if(empty(CALL_APP_TOKEN)){?>
					var url2 = 'https://api-smartflo.tatateleservices.com/v1/auth/login';
				<?php }else{?>
					var url2 = 'https://api-smartflo.tatateleservices.com/v1/auth/refresh';
				<?php }?>
				}else{
					var url2 = 'https://api-smartflo.tatateleservices.com/v1/auth/login';
				}
				//$('.followers-div').show();
				$.ajax({
					type: "POST",
					url: url2,
					contentType: "application/json",
					data: JSON.stringify({
						email:msg1.app_id,
						<?php if(!empty(CALL_APP_TOKEN)){?>
							token:'<?php echo CALL_APP_TOKEN;?>',
						<?php }?>
						password:msg1.app_secret
					}),
					<?php if(!empty(CALL_APP_TOKEN)){?>
					headers: {
						 "Authorization": "<?php echo CALL_APP_TOKEN;?>"
					},
					<?php }?>
					dataType: 'json',
					async: false,
					success: function(res){
						var res1 = JSON.parse(JSON.stringify(res));
						if(res1.success) {
							//var url3 =  admin_url+'call_settings/delete_agent';
							var url2 = 'https://api-smartflo.tatateleservices.com/v1/agents';
							//$('.followers-div').show();
							$.ajax({
								type: "GET",
								url: url2,
								contentType: "application/json",
								dataType: 'json',
								headers: {
									 "Authorization": "Bearer "+res1.access_token
								},
								success: function(result){
									console.log(result);
										var req_id = '';
										if(result.length>0) {
											
											for (var i = 0, j = result.length; i < j; i += 1) { 
												 if( result[i].follow_me_number.includes(edit_phone)){
													 
													 req_id = result[i].id;
												 }
											}
											  var url3 = 'https://api-smartflo.tatateleservices.com/v1/agent/'+req_id;
											  $.ajax({
												type: "DELETE",
												url: url3,
												contentType: "application/json",
												dataType: 'json',
												headers: {
													 "Authorization": "Bearer "+res1.access_token
												},
												success: function(result1){
													if(result1.success){
                                                        if(dbdel ==true){
                                                            deleteagentfromdb(id);
                                                        }else{
														    var url3 =  admin_url+'call_settings/delete_agent';
														//$('.followers-div').show();
															$.ajax({
																type: "POST",
																url: url3,
																data: {
																	token:res1.access_token,
																	id:id
																},
																dataType: 'json',
																success: function(result){
																	console.log(result);
																	if(result.status == 'success') {
																		
																	} else {
																		alert_float('warning', result.msg+' <br> Please Delete Manually on Tata Tele Services Portal');
																		setTimeout(function(){
																			window.location.reload();
																		},1000);
																	}
																}
															});

                                                        }
															//alert(res.msg);
														} else {
															alert_float('warning', res.msg+' <br> Please Delete Manually on Tata Tele Services Portal');
															setTimeout(function(){
																window.location.reload();
															},1000);
														}
														}
											        });
													}
													else {
														alert_float('warning', result1.message+' <br> Please Delete Manually on Tata Tele Services Portal');
														setTimeout(function(){
															window.location.reload();
														},1000);
													}
												}
											  });
									} else {
										tataupdate_access_token_db('delete',id);
									}
								},
								error: function(xhr, status, error) {
									<?php if(!empty(CALL_APP_TOKEN)){?>
										tataupdate_access_token_db('delete',id);
									<?php }else{?>
								  alert_float('warning', 'Invalid Credentials'+' <br> Please Delete Manually on Tata Tele Services Portal');
									setTimeout(function(){
										window.location.reload();
									},1000);
									<?php }?>
								}
							});
							//alert(res.msg);
						} else {
							alert_float('warning', msg.msg);
							setTimeout(function(){
								window.location.reload();
							},1000);
						}
					}
				});
				return true;
			//}
}
function tataupdate_access_token_db(red_url,id){
	var url13 =  admin_url+'call_settings/updatetoken';
	$.ajax({
		type: "POST",
		url: url13,
		data: {
			token:'',
		},
		dataType: 'json',
		success: function(result){
			var msg1 = JSON.parse(JSON.stringify(result));
			if(msg1.access_token ==''){
				if(red_url == 'delete'){
					tatadeletAgent_db(id,1);
				}
			}else{
				alert_float('warning', 'Invalid Credentials'+' <br> Please Delete Manually on Tata Tele Services Portal');
				setTimeout(function(){
					window.location.reload();
				},1000);
			}
		}
	});
}
function tatadeletAgent_db(id,cur_val) {
	if(cur_val==''){
	if (confirm('Do you want to Deactivate this Agent?')) {
		tatadeletAgent_1_db(id,cur_val);
	}
	else {
        return false;
    }
	}else{
		tatadeletAgent_1_db(id,cur_val);
	}
}

function deleteagentfromdb(id){
    var url3 =  admin_url+'call_settings/delete_agent_db';
    $.ajax({
        type: "POST",
        url: url3,
        data: {
            id:id
        },
        dataType: 'json',
        success: function(result){
            console.log(result);
            if(result.status == 'success') {
                alert_float('success', result.msg);
                setTimeout(function(){
                    window.location.reload();
                },1000);
            } else {
                alert_float('warning', result.msg);
                setTimeout(function(){
                    window.location.reload();
                },1000);
            }
        }
    });
}
function deletAgent_db(id,req_id1,source_from){
	 if (confirm('Do you want to Delete this Agent?')) {
        if(req_id1 == 1){
            if(source_from == 'telecmi'){
                tele_delete_agent_db(id,true);
                return;
            }
            else if(source_from == 'tata'){
                tatadeletAgent_1_db(id,'');
            }
        }else{
            deleteagentfromdb(id);
        }
	 }
}
function deletAgent(id) {
    if (confirm('Do you want to Deactivate this Agent?')) {
        tele_delete_agent_db(id,false);
    } else {
        return false;
    }
}
function tatadeletAgent(id,cur_val) {
	if(cur_val==''){
	if (confirm('Do you want to Deactivate this Agent?')) {
		tatadeletAgent_1(id,cur_val);
	}
	else {
        return false;
    }
	}else{
		tatadeletAgent_1(id,cur_val);
	}
}
function daffydeletAgent(id,cur_val) {
	if(cur_val==''){
        if (confirm('Do you want to Deactivate this Agent?')) {
            var url3 =  admin_url+'call_settings/delete_agent';
            $.ajax({
                type: "POST",
                url: url3,
                data: {
                    token:'',
                    id:id
                },
                dataType: 'json',
                success: function(result){
                    console.log(result);
                    if(result.status == 'success') {
                        alert_float('success', result.msg);
                        setTimeout(function(){
                            window.location.reload();
                        },1000);
                    } else {
                        alert_float('warning', result.msg);
                        setTimeout(function(){
                            window.location.reload();
                        },1000);
                    }
                }
            });
        }
	}
}

function tatadeletAgent_1(id,cur_val) {
    //if (confirm('Do you want to Deactivate this Agent?')) {
        var url =  admin_url+'call_settings/getAgentDetails';
        //$('.followers-div').show();
        $.ajax({
            type: "POST",
            url: url,
            data: {id:id},
            dataType: 'json',
            success: function(msg){
                var msg1 = JSON.parse(JSON.stringify(msg));
				console.info(msg1);
				
				 if(msg1.phone) {
					 var edit_phone = msg1.phone;
                    //var url2 = 'https://piopiy.telecmi.com/v1/agent/remove';
					if(cur_val == ''){
					<?php if(empty(CALL_APP_TOKEN)){?>
						var url2 = 'https://api-smartflo.tatateleservices.com/v1/auth/login';
					<?php }else{?>
						var url2 = 'https://api-smartflo.tatateleservices.com/v1/auth/refresh';
					<?php }?>
					}else{
						var url2 = 'https://api-smartflo.tatateleservices.com/v1/auth/login';
					}
                    //$('.followers-div').show();
                    $.ajax({
                        type: "POST",
                        url: url2,
                        contentType: "application/json",
                        data: JSON.stringify({
                            email:msg1.app_id,
							<?php if(!empty(CALL_APP_TOKEN)){?>
								token:'<?php echo CALL_APP_TOKEN;?>',
							<?php }?>
							password:msg1.app_secret
                        }),
						<?php if(!empty(CALL_APP_TOKEN)){?>
						headers: {
							 "Authorization": "<?php echo CALL_APP_TOKEN;?>"
						},
						<?php }?>
                        dataType: 'json',
                        async: false,
                        success: function(res){
							var res1 = JSON.parse(JSON.stringify(res));
                            if(res1.success) {
                                //var url3 =  admin_url+'call_settings/delete_agent';
                                var url2 = 'https://api-smartflo.tatateleservices.com/v1/agents';
                                //$('.followers-div').show();
                                $.ajax({
                                    type: "GET",
                                    url: url2,
                                    contentType: "application/json",
									dataType: 'json',
									headers: {
										 "Authorization": "Bearer "+res1.access_token
									},
                                    success: function(result){
                                        console.log(result);
                                            var req_id = '';
											if(result.length>0) {
												
												for (var i = 0, j = result.length; i < j; i += 1) { 
													 if( result[i].follow_me_number.includes(edit_phone)){
														 
														 req_id = result[i].id;
													 }
												}
												  var url3 = 'https://api-smartflo.tatateleservices.com/v1/agent/'+req_id;
												  $.ajax({
													type: "DELETE",
													url: url3,
													contentType: "application/json",
													dataType: 'json',
													headers: {
														 "Authorization": "Bearer "+res1.access_token
													},
													success: function(result1){
														if(result1.success){
															 var url3 =  admin_url+'call_settings/delete_agent';
															//$('.followers-div').show();
																$.ajax({
																	type: "POST",
																	url: url3,
																	data: {
																		token:res1.access_token,
																		id:id
																	},
																	dataType: 'json',
																	success: function(result){
																		console.log(result);
																		if(result.status == 'success') {
																			alert_float('success', result.msg);
																			setTimeout(function(){
																				window.location.reload();
																			},1000);
																		} else {
																			alert_float('warning', result.msg);
																			setTimeout(function(){
																				window.location.reload();
																			},1000);
																		}
																	}
																});
																//alert(res.msg);
															} else {
																alert_float('warning', res.msg);
																setTimeout(function(){
																	window.location.reload();
																},1000);
															}
															}
												  });
														}
														else {
															alert_float('warning', result1.message);
															setTimeout(function(){
																window.location.reload();
															},1000);
														}
													}
												  });
                                        } else {
                                            tataupdate_access_token('delete',id);
                                        }
                                    },
									error: function(xhr, status, error) {
										<?php if(!empty(CALL_APP_TOKEN)){?>
											tataupdate_access_token('delete',id);
										<?php }else{?>
									  alert_float('warning', 'Invalid Credentials');
										setTimeout(function(){
											window.location.reload();
										},1000);
										<?php }?>
									}
                                });
                                //alert(res.msg);
                            } else {
                                alert_float('warning', msg.msg);
                                setTimeout(function(){
                                    window.location.reload();
                                },1000);
                            }
                        }
                    });
                //}
}

function edit_agent(id) {
    var url =  admin_url+'call_settings/getAgentDetails';
    //$('.followers-div').show();
    $('#editAgentModal').modal('show');
    $.ajax({
        type: "POST",
        url: url,
        data: {id:id},
        dataType: 'json',
        success: function(msg){
            $('.errmsg').html('');
            show_wrapper(msg.source_from);
            $('#editAgentModal select[name="ivr_id"]').attr('disabled',true);
            $('#editAgentModal select[name=ivr_id]').val(msg.ivr_id);
			$('#editAgentModal select[name=ivr_id]').selectpicker('refresh');

            $('#editAgentModal #agentid').val(msg.agent_id);
            $('#editAgentModal #id').val(msg.id);
            $('#editAgentModal #phone').val(msg.phone);
            $('#editAgentModal #edit_phone1').val(msg.phone);
            $('#editAgentModal #password').val(msg.password);
            $('#editAgentModal #name').val(msg.staff_name);
            
            if(msg.staff_id >0){
                $('#editAgentModal select#staff_id').selectpicker('val',msg.staff_id);
                $('#editAgentModal select#staff_id').attr('disabled',true);
                $('#editAgentModal select#staff_id option[value='+msg.staff_id+']').attr('selected','selected');
                $('#editAgentModal select#staff_id').selectpicker('refresh');
            }else{
                $('#editAgentModal select#staff_id').removeAttr('disabled');
                $('#editAgentModal select#staff_id').val('');
                $('#editAgentModal select#staff_id').selectpicker('refresh');
            }
            
           

            $('#editAgentModal select#status').selectpicker('val',msg.status);
            $('#editAgentModal select#status option[value='+msg.status+']').attr('selected','selected');
            $('#editAgentModal select#status').selectpicker('refresh');

            $('#editAgentModal select#sms_alert').selectpicker('val',msg.sms_alert);
			if(msg.sms_alert!=''){
				$('#editAgentModal select#sms_alert option[value='+msg.sms_alert+']').attr('selected','selected');
			}
            $('#editAgentModal select#sms_alert').selectpicker('refresh');

            $('#editAgentModal select#starttime').selectpicker('val',msg.start_time);
            $('#editAgentModal select#starttime option[value='+msg.start_time+']').attr('selected','selected');
            $('#editAgentModal select#starttime').selectpicker('refresh');

            $('#editAgentModal select#endtime').selectpicker('val',msg.end_time);
            $('#editAgentModal select#endtime option[value='+msg.end_time+']').attr('selected','selected');
            $('#editAgentModal select#endtime').selectpicker('refresh');

            // -----Country Code Selection
            $("#editAgentModal #phone").intlTelInput({
                initialCountry: msg.phone_country_code,
                separateDialCode: true,
                // utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.4/js/utils.js"
            });
            $("#editAgentModal  #phone_country_code").val(msg.phone_country_code);
            $("#editAgentModal  #phone_code").val(msg.dial_code);
            $("#editAgentModal #phone_iti_wrapper .iti__flag-container ul li").click(function(){

                var dial_code =$(this).attr('data-dial-code');
                var country_code =$(this).attr('data-country-code').toUpperCase();
                $("#editAgentModal  #phone_country_code").val(country_code);
                $("#editAgentModal  #phone_code").val(dial_code);
            });

            // if(msg.phone) {
            // $('#addAgentModal #phone').val(msg.phone);
            // $('#addAgentModal #ext').val(emp_id);
            // $('#addAgentModal #name').val(msg.name);
            // } else {
            // $('#addAgentModal #phone').val('');
            // $('#addAgentModal #name').val('');
            // }
        }
    });
}
function playrecord(url) {
        $html = '';
        var surl = admin_url.split("/admin");
        $('#play_record').modal('show');
        $html += '<audio id="myAudio" controls controlsList="nodownload"><source src="'+surl[0]+'/uploads/recordings/'+url+'"></audio>';
        $('#playhtml').html($html);
}
function view_history(id) {
        $('#view_history').modal('show');
        if(id) {
            var url =  admin_url+'call_settings/getCallHistory';
            //$('.followers-div').show();
            $.ajax({
                type: "POST",
                url: url,
                data: {id:id},
                dataType: 'json',
                success: function(msg){
                    console.log(msg.result);
                  if(msg.result) {
                    $('#historyhtml').html(msg.result);
                  }
                }
            });
        }
}

function activateAgent(id) {
    var url1 =  admin_url+'call_settings/getAgentDetails';
    //$('.followers-div').show();
    $.ajax({
        type: "POST",
        url: url1,
        data: {id:id},
        dataType: 'json',
        success: function(msg1){
            var extid = msg1.staff_id;
            var name = msg1.staff_name;
            var phone_number = msg1.phone;
            var start_time = msg1.start_time;
            var end_time = msg1.end_time;
            var status = msg1.status;
            var password = msg1.password;
            var extension = msg1.agent_id.split("_")[0];
            var appid = msg1.app_id;
            var secret = msg1.app_secret;
            var sms_alert = msg1.sms_alert;
            if(msg1.channel =='international_softphone' || msg1.channel =='national_softphone'){
                var url = 'https://rest.telecmi.com/v2/user/add';
            }else{
                var url = 'https://piopiy.telecmi.com/v1/agent/add';
                phone_number =parseInt(phone_number);
            }
            //$('.followers-div').show();
            $.ajax({
                type: "POST",
                url: url,
                contentType: "application/json",
                data: JSON.stringify({
                    name:name,
                    phone_number:phone_number,
                    start_time:parseInt(start_time),
                    end_time:parseInt(end_time),
                    password:password,
                    extension:parseInt(extension),
                    appid:parseInt(appid),
                    secret:secret,
                    sms_alert: (sms_alert == 'true' ? true : false)
                }),
                dataType: 'json',
                async: false,
                success: function(msg){
                    if(msg.status == 'success') {
                        if(msg1.channel =='international_softphone' || msg1.channel =='national_softphone'){
                            var url2 = 'https://rest.telecmi.com/v2/user/status';
                        }else{
                            var url2 = 'https://piopiy.telecmi.com/v1/agent/status';
                        }
                        //$('.followers-div').show();
                        $.ajax({
                            type: "POST",
                            url: url2,
                            contentType: "application/json",
                            data: JSON.stringify({
                                id:msg.agent.agent_id,
                                appid:parseInt(appid),
                                secret:secret,
                                status:status
                            }),
                            dataType: 'json',
                            async: false,
                            success: function(res){
                                if(res.code == 'cmi-200' || res.code =='200') {
                                    var url3 =  admin_url+'call_settings/activateAgent';
                                    //$('.followers-div').show();
                                    $.ajax({
                                        type: "POST",
                                        url: url3,
                                        data: {
                                            id:id
                                        },
                                        dataType: 'json',
                                        success: function(result){
                                            console.log(result);
                                            if(result.status == 'success') {
                                                alert_float('success', result.msg);
                                                setTimeout(function(){
                                                    window.location.reload();
                                                },1000);
                                            } else {
                                                alert_float('warning', result.msg);
                                                setTimeout(function(){
                                                    window.location.reload();
                                                },1000);
                                            }
                                        }
                                    });
                                    //alert(res.msg);
                                } else {
                                    alert_float('warning', res.msg);
                                    setTimeout(function(){
                                        window.location.reload();
                                    },1000);
                                }
                            }
                        });
                    } else {
                        alert_float('warning', msg.msg);
                        setTimeout(function(){
                            window.location.reload();
                        },1000);
                    }
                }
            });
        }
    });
}

function daffyactivateAgent(id,cur_val) {
	var url3 =  admin_url+'call_settings/activateAgent';
	//$('.followers-div').show();
	$.ajax({
		type: "POST",
		url: url3,
		data: {
			id:id,
			token:'',
			agentid:0,
		},
		dataType: 'json',
		success: function(result){
			console.log(result);
			if(result.status == 'success') {
				alert_float('success', result.msg);
				setTimeout(function(){
					window.location.reload();
				},1000);
			} else {
				alert_float('warning', result.msg);
				setTimeout(function(){
					window.location.reload();
				},1000);
			}
		}
	});
}
function tataactivateAgent(id,cur_val) {
    var url1 =  admin_url+'call_settings/getAgentDetails';
    //$('.followers-div').show();
    $.ajax({
        type: "POST",
        url: url1,
        data: {id:id},
        dataType: 'json',
        success: function(msg1){
            var extid = msg1.staff_id;
            var name = msg1.staff_name;
            var phone_number = msg1.phone;
            var start_time = msg1.start_time;
            var end_time = msg1.end_time;
            var status = msg1.status;
            var password = msg1.password;
            var extension = (100 + parseInt(extid));
            var appid = msg1.app_id;
            var secret = msg1.app_secret;
            var sms_alert = msg1.sms_alert;
			if(cur_val == ''){
			<?php if(empty(CALL_APP_TOKEN)){?>
				var url = 'https://api-smartflo.tatateleservices.com/v1/auth/login';
			<?php }else{?>
				var url = 'https://api-smartflo.tatateleservices.com/v1/auth/refresh';
			<?php }?>
			}else{
				var url = 'https://api-smartflo.tatateleservices.com/v1/auth/login';
			}
            //$('.followers-div').show();
            $.ajax({
                type: "POST",
                url: url,
                contentType: "application/json",
                data: JSON.stringify({
                    email:appid,
					<?php if(!empty(CALL_APP_TOKEN)){?>
						token:'<?php echo CALL_APP_TOKEN;?>',
					<?php }?>
                    password:secret,
                   
                }),
				<?php if(!empty(CALL_APP_TOKEN)){?>
				headers: {
					 "Authorization": "<?php echo CALL_APP_TOKEN;?>"
				},
				<?php }?>
                dataType: 'json',
                async: false,
                success: function(msg2){
					var msg3 = JSON.parse(JSON.stringify(msg2));
                    if(msg3.success) {
                        var url2 = 'https://api-smartflo.tatateleservices.com/v1/agent';
                        //$('.followers-div').show();
                        $.ajax({
                            type: "POST",
                            url: url2,
                            contentType: "application/json",
                            data: JSON.stringify({
                                name:msg1.staff_name,
                                follow_me_number:msg1.phone
                            }),
							Accept: "application/json",
							headers: {
								 "Authorization": "Bearer "+msg3.access_token
							},
                            dataType: 'json',
                            async: false,
                            success: function(res){
								var res1 = JSON.parse(JSON.stringify(res));
                                if(res1.success) {
                                    var url3 =  admin_url+'call_settings/activateAgent';
                                    //$('.followers-div').show();
                                    $.ajax({
                                        type: "POST",
                                        url: url3,
                                        data: {
                                            id:id,
											token:msg3.access_token,
											agentid:res1.agent_id,
                                        },
                                        dataType: 'json',
                                        success: function(result){
                                            console.log(result);
                                            if(result.status == 'success') {
                                                alert_float('success', result.msg);
                                                setTimeout(function(){
                                                    window.location.reload();
                                                },1000);
                                            } else {
                                                alert_float('warning', result.msg);
                                                setTimeout(function(){
                                                    window.location.reload();
                                                },1000);
                                            }
                                        }
                                    });
                                    //alert(res.msg);
                                } else {
                                    alert_float('warning', res1.message);
                                    setTimeout(function(){
                                        window.location.reload();
                                    },1000);
                                }
                            }
                        });
                    } else {
						tataupdate_access_token('activate',id);
					}
                },
				error: function(xhr, status, error) {
					<?php if(!empty(CALL_APP_TOKEN)){?>
						tataupdate_access_token('activate',id);
					<?php }else{?>
				  alert_float('warning', 'Invalid Credentials');
					setTimeout(function(){
						window.location.reload();
					},1000);
					<?php }?>
				}
            });
        }
    });
}
function tataupdate_access_token(red_url,id){
	var url13 =  admin_url+'call_settings/updatetoken';
	$.ajax({
		type: "POST",
		url: url13,
		data: {
			token:'',
		},
		dataType: 'json',
		success: function(result){
			var msg1 = JSON.parse(JSON.stringify(result));
			if(msg1.access_token ==''){
				if(red_url == 'edit'){
					tataeditagent(1);
				}
				else if(red_url == 'add'){
					tataaddagent(1);
				}
				else if(red_url == 'activate'){
					tataactivateAgent(id,1)
				}
				else if(red_url == 'delete'){
					tatadeletAgent(id,1);
				}
			}else{
				alert_float('warning', 'Invalid Credentials');
				setTimeout(function(){
					window.location.reload();
				},1000);
			}
		}
	});
}
function tataeditagent(cur_val){
	$('#targeteditAgent').attr('disabled','disabled');
	var extension = $('#editAgentModal #agentid').val();
        var name = $('#editAgentModal #name').val();
        var phone_number = $('#editAgentModal #phone').val();
        var edit_phone = $('#editAgentModal #edit_phone1').val();
       
        var status = $('#editAgentModal #status').val();
        var extid = $('#editAgentModal #id').val();
        var staff_id = $('#editAgentModal #staff_id').val();

        var ivr_id = $('#editAgentModal #ivr_id').val();
        var ivr_details =get_ivr_details(ivr_id);
        if (typeof ivr_details.app_id == 'undefined'){
            alert_float('warning', 'IVR should be selected');
            setTimeout(function(){
                window.location.reload();
            },1000);
        }else{
            var appid = ivr_details.app_id;
            var secret = ivr_details.app_secret;
            var channel = ivr_details.channel;
        }

		if(cur_val==''){
	<?php if(empty(CALL_APP_TOKEN)){?>
		var url = 'https://api-smartflo.tatateleservices.com/v1/auth/login';
	<?php }else{?>
		var url = 'https://api-smartflo.tatateleservices.com/v1/auth/refresh';
	<?php }?>
		}else{
			var url = 'https://api-smartflo.tatateleservices.com/v1/auth/login';
		}
	$.ajax({
		type: "POST",
		url: url,
		contentType: "application/json",
		Accept: "application/json",
		data: JSON.stringify({
			email:appid,
			<?php if(!empty(CALL_APP_TOKEN)){?>
			token:'<?php echo CALL_APP_TOKEN;?>',
			<?php }?>
			password:secret
		}),
		<?php if(!empty(CALL_APP_TOKEN)){?>
		headers: {
			 "Authorization": "<?php echo CALL_APP_TOKEN;?>"
		},
		<?php }?>
		dataType: 'json',
		async: false,
		success: function(msg){
			var msg1 = JSON.parse(JSON.stringify(msg));
		  if(msg1.success) {
		   // var url2 = 'https://piopiy.telecmi.com/v1/agent/status';
			var url2 = 'https://api-smartflo.tatateleservices.com/v1/agents';
			//$('.followers-div').show();
			$.ajax({
				type: "GET",
				url: url2,
				contentType: "application/json",
				dataType: 'json',
				headers: {
					 "Authorization": "Bearer "+msg1.access_token
				},
				async: false,
				success: function(res){
					var req_id = '';
					if(res.length>0) {
						
						for (var i = 0, j = res.length; i < j; i += 1) { 
							 if( res[i].follow_me_number.includes(edit_phone)){
								 
								 req_id = res[i].id;
							 }
						}
					   // var url3 =  admin_url+'call_settings/updateAgent';
						var url3 = 'https://api-cloudphone.tatateleservices.com/v1/agent/'+req_id;
						//$('.followers-div').show();
						$.ajax({
							type: "PUT",
							url: url3,
							data: {
								name:name,
								follow_me_number:phone_number,
								
							},
							dataType: 'json',
							headers: {
								 "Authorization": "Bearer "+msg1.access_token
							},
							success: function(result){
								if(result.success) {
									var url3 =  admin_url+'call_settings/updateAgent';
									$.ajax({
										type: "POST",
										url: url3,
										data: {
											id:extid,
											phone_number:phone_number,
											token:msg1.access_token,
											 status:status,
											staff_id:staff_id,
                                            phone_country_code,phone_country_code
											
										},
										dataType: 'json',
										
										success: function(result1){
											if(result1.status == 'success') {
												alert_float('success', result1.msg);
												setTimeout(function(){
													window.location.reload();
												},1000);
											} else {
												alert_float('warning', result1.msg);
												setTimeout(function(){
													window.location.reload();
												},1000);
											}
										}
									});
									
								} else {
									alert_float('warning', result.message);
									setTimeout(function(){
										window.location.reload();
									},1000);
								}
							},
							error: function(result1) {
								alert_float('warning','Call forward number already exists');
								setTimeout(function(){
									window.location.reload();
								},1000);
							}
						});
						//alert(res.msg);
					} else {
						alert_float('warning', res.message);
						setTimeout(function(){
							window.location.reload();
						},1000);
					}
				}
			});
		  }
		  else{
			  tataupdate_access_token('edit','');
		  }
		},
		error: function(msg) {
			<?php if(!empty(CALL_APP_TOKEN)){?>
				tataupdate_access_token('edit','');
			<?php }else{?>
			var msg1 = JSON.parse(JSON.stringify(msg));
			console.info(msg1);
			alert_float('warning', 'Invalid Credentials');
			setTimeout(function(){
				//window.location.reload();
			},1000);
			<?php }?>
		}
	});

	
}
function tataaddagent(cur_val){
	var extid = $('#addAgentModal #ext').val();
	var name = $('#addAgentModal #name').val();
	var phone_number = $('#addAgentModal #phone').val();
   
	var status = $('#addAgentModal #status').val();
	var phone_country_code = $('#addAgentModal #phone_country_code').val();
	var extension = (100 + parseInt(extid));
    
    var ivr_id =$('#addAgentModal #ivr_id').val();
    var ivr_details =get_ivr_details(ivr_id);
    if (typeof ivr_details.app_id == 'undefined'){
        alert_float('warning', 'IVR shuold be selected');
        setTimeout(function(){
            window.location.reload();
        },1000);
    }else{
        var appid = ivr_details.app_id;
        var secret = ivr_details.app_secret;
    }
	$('#tataaddAgent').attr('disabled','disabled');
	if(cur_val==''){
	<?php if(empty(CALL_APP_TOKEN)){?>
		var url = 'https://api-smartflo.tatateleservices.com/v1/auth/login';
	<?php }else{?>
		var url = 'https://api-smartflo.tatateleservices.com/v1/auth/refresh';
	<?php }?>
	}else{
		var url = 'https://api-smartflo.tatateleservices.com/v1/auth/login';
	}
	//$('.followers-div').show();
	$.ajax({
		type: "POST",
		url: url,
		contentType: "application/json",
		Accept: "application/json",
		data: JSON.stringify({
			email:appid,
			<?php if(!empty(CALL_APP_TOKEN)){?>
			token:'<?php echo CALL_APP_TOKEN;?>',
			<?php }?>
			password:secret                    
		}),
		<?php if(!empty(CALL_APP_TOKEN)){?>
		headers: {
			 "Authorization": "<?php echo CALL_APP_TOKEN;?>"
		},
		<?php }?>
		dataType: 'json',
		async: false,
		success: function(msg){
			console.info(msg);
			var msg1 = JSON.parse(JSON.stringify(msg));
			console.info(msg1);
		  if(msg1.success) {
			var url2 = 'https://api-smartflo.tatateleservices.com/v1/agent';
			//$('.followers-div').show();
			$.ajax({
				type: "POST",
				url: url2,
				Authorization: "Bearer "+msg1.access_token,
				contentType: 'application/json',
				Accept: "application/json",
				headers: {
					 "Authorization": "Bearer "+msg1.access_token
				},
				
				data: JSON.stringify({
					name:name,
					follow_me_number:phone_number
				}),
				dataType: 'json',
				async: false,
				success: function(res){
					
					var res1 = JSON.parse(JSON.stringify(res));
					if(res1.success) {
						var url3 =  admin_url+'call_settings/saveAgent';
						//$('.followers-div').show();
						$.ajax({
							type: "POST",
							url: url3,
							data: {
								extid:extid,
								phone_number:phone_number,
								agentid:res1.agent_id,
								secret:secret,
								token:msg1.access_token,
								status:status,
                                ivr_id:ivr_id,
                                phone_country_code:phone_country_code
							},
							dataType: 'json',
							success: function(result){
								console.log(result);
								const result1 = JSON.parse(JSON.stringify(result));
								if(result1.status == 'success') {
									alert_float('success', result1.msg);
									setTimeout(function(){
										window.location.reload();
									},1000);
								} else {
									alert_float('warning', result.msg);
									setTimeout(function(){
										window.location.reload();
									},1000);
								}
							}
						});
						//alert(res.msg);
					} else {
						alert_float('warning', res1.message);
						setTimeout(function(){
							window.location.reload();
						},1000);
					}
				},
				error: function(res1) {
					var res2 = JSON.parse(JSON.stringify(res1));
					alert_float('warning', 'Call forward number already exists');
					setTimeout(function(){
						window.location.reload();
					},1000);
				}
			});
		  } else {
			tataupdate_access_token('add','');
		  }
		},
		error: function(msg) {
			<?php if(!empty(CALL_APP_TOKEN)){?>
				tataupdate_access_token('add','');
			<?php }else{?>
			alert_float('warning', 'Invalid Credentials');
			setTimeout(function(){
				window.location.reload();
			},1000);
			<?php }?>
		}
	});

}
$(document).ready(function(){
    $('#closeaudio').on('click', function() {
        var myAudio = document.getElementById("myAudio");
        myAudio.pause();
        $('#play_record').modal('hide');
    });

    $('#addAgentModal select#staff_id').on('change', function() {
        var emp_id = this.value;
        if(emp_id) {
            var url =  admin_url+'call_settings/getEmpDetail';
            //$('.followers-div').show();
            $.ajax({
                type: "POST",
                url: url,
                data: {emp_id:emp_id},
                dataType: 'json',
                success: function(msg){
                  console.log(msg.phone);
                  //if(msg.phone) {
                    $('#addAgentModal #phone').val(msg.phone);
                    $('#addAgentModal #ext').val(emp_id);
                    $('#addAgentModal #name').val(msg.name);
                //   } else {
                //     $('#addAgentModal #phone').val('');
                //     $('#addAgentModal #name').val('');
                //   }
                }
            });
        }
    });
	
	$('#tataaddAgent').on('click', function() {
        var extid = $('#addAgentModal #ext').val();
        var name = $('#addAgentModal #name').val();
        var phone_number = $('#addAgentModal #phone').val();
       
        var status = $('#addAgentModal #status').val();
        var extension = (100 + parseInt(extid));
        // Validations
        var validate = 0;

        var ivr_details =get_ivr_details($('#ivr_id').val());
        if (typeof ivr_details.app_id == 'undefined'){
            validate = 1;
            $('#addAgentModal #ivr_id_val').html('IVR should be selected');
        }else{
            var appid = ivr_details.app_id;
            var secret = ivr_details.app_secret;
        }
        $('#addAgentModal .errmsg').html ('');
        if(!extid) {
            $('#addAgentModal #staff_val').html('Please select Staff');
            validate = 1;
        } else {
            $('#addAgentModal #staff_val').html('');
        }

        if(!phone_number) {
            $('#addAgentModal #phone_val').html('Please enter Phone number, It should have minimum 7 digits to maximum 20 digits');
            validate = 1;
        } else {
            if(phone_number.length > 6 && phone_number.length < 21) {
                $('#addAgentModal #phone_val').html('');
            } else {
                $('#addAgentModal #phone_val').html('Please enter Valid Phone number, It should have minimum 7 digits to maximum 20 digits');
                validate = 1;
            }
        }

        if(validate == 1) {
            return false;
        }

        if(appid) {
			tataaddagent('');
            } else {
            alert_float('warning', 'Please enable call settings.');
            setTimeout(function(){
                window.location.reload();
            },1000);
        }
    });
	
	$('#daffyaddAgent').on('click', function() {
        var extid = $('#addAgentModal #ext').val();
        var name = $('#addAgentModal #name').val();
        var phone_number = $('#addAgentModal #phone').val();
       
        var status = $('#addAgentModal #status').val();
        var phone_country_code = $('#addAgentModal #phone_country_code').val();
        var extension = (100 + parseInt(extid));

        // Validations
        var validate = 0;

        var ivr_id =$('#addAgentModal #ivr_id').val();
        var ivr_details =get_ivr_details(ivr_id);
        if (typeof ivr_details.app_id == 'undefined'){
            validate = 1;
            $('#addAgentModal #ivr_id_val').html('IVR should be selected');
        }else{
            var appid = ivr_details.app_id;
            var secret = ivr_details.app_secret;
        }

        $('#addAgentModal .errmsg').html ('');
        if(!extid) {
            $('#addAgentModal #staff_val').html('Please select Staff');
            validate = 1;
        } else {
            $('#daffyaddAgentModal #staff_val').html('');
        }

        if(!phone_number) {alert(2);
            $('#addAgentModal #phone_val').html('Please enter Phone number, It should have minimum 7 digits to maximum 20 digits');
            validate = 1;
        } else {
            if(phone_number.length > 6 && phone_number.length < 21) {
                $('#addAgentModal #phone_val').html('');
            } else {
                $('#addAgentModal #phone_val').html('Please enter Valid Phone number, It should have minimum 7 digits to maximum 20 digits');
                validate = 1;
            }
        }
        if(validate == 1) {
            return false;
        }

        if(appid) {
			var url3 =  admin_url+'call_settings/saveAgent';
						//$('.followers-div').show();
						$.ajax({
							type: "POST",
							url: url3,
							data: {
								extid:extid,
								phone_number:phone_number,
								agentid:0,
								secret:secret,
								token:'',
								status:status,
                                ivr_id:ivr_id,
                                phone_country_code:phone_country_code
							},
							dataType: 'json',
							success: function(result){
								console.log(result);
								const result1 = JSON.parse(JSON.stringify(result));
								if(result1.status == 'success') {
									alert_float('success', result1.msg);
									setTimeout(function(){
										window.location.reload();
									},1000);
								} else {
									alert_float('warning', result.msg);
									setTimeout(function(){
										window.location.reload();
									},1000);
								}
							}
						});
            } else {
            alert_float('warning', 'Please enable call settings.');
            setTimeout(function(){
                window.location.reload();
            },1000);
        }
    });

    function get_ivr_details(id){
        var ivr_details ={};
        if(id>0){
            $.ajax({
                type: "POST",
                url:  admin_url+'call_settings/getIvr/'+id,
                dataType: 'json',
                async : false,
                success: function(result){
                    if(result.success ==true){
                        ivr_details = result.data;
                    }
                }
            });
        }
        
        return ivr_details;
    }
    $('#addAgent').on('click', function() {
        var extid = $('#addAgentModal #ext').val();
        var name = $('#addAgentModal #name').val();
        var phone_number = $('#addAgentModal #phone').val();
        var start_time = $('#addAgentModal #starttime').val();
        var end_time = $('#addAgentModal #endtime').val();
        var status = $('#addAgentModal #status').val();
        var password = $('#addAgentModal #password').val();
        var extension = $('#addAgentModal #extension_id').val();
        var ivr_id =$('#addAgentModal #ivr_id').val();
        var ivr_details =get_ivr_details(ivr_id);
        var sms_alert = $('#addAgentModal #sms_alert').val();
        var phone_country_code = $('#addAgentModal #phone_country_code').val();
        
        $('#addAgentModal .errmsg').html ('');
        
        // Validations
        var validate = 0;
        if (typeof ivr_details.app_id == 'undefined'){
            validate = 1;
            $('#addAgentModal #ivr_id_val').html('IVR should be selected');
        }else{
            var appid = ivr_details.app_id;
            var secret = ivr_details.app_secret;
            var channel = ivr_details.channel;
            
        }
        if(extension.length != 3) {
            $('#addAgentModal #extension_id_val').html('Extension id should be three digit code');
            validate = 1;
        }
        if(!extid) {
            $('#addAgentModal #staff_val').html('Please select Staff');
            validate = 1;
        } else {
            $('#addAgentModal #staff_val').html('');
        }

        if(!phone_number) {
            $('#addAgentModal #phone_val').html('Please enter Phone number, It should have minimum 7 digits to maximum 20 digits');
            validate = 1;
        } else {
            if(phone_number.length > 6 && phone_number.length < 21) {
                $('#addAgentModal #phone_val').html('');
            } else {
                $('#addAgentModal #phone_val').html('Please enter Valid Phone number, It should have minimum 7 digits to maximum 20 digits');
                validate = 1;
            }
        }

        if(!password) {
            $('#addAgentModal #pass_val').html('Please enter Password');
            validate = 1;
        } else {
            if(password.length < 6) {
                $('#addAgentModal #pass_val').html('Password must contain minimum 6 characters');
                validate = 1;
            } else {
                $('#addAgentModal #pass_val').html('');
            }
        }

        if(!start_time) {
            $('#addAgentModal #start_val').html('Please select Start time');
            validate = 1;
        } else {
            if(parseInt(end_time) <= parseInt(start_time)) {
                $('#addAgentModal #start_val').html('Start time should be less than End time.');
                validate = 1;
            } else {
                $('#addAgentModal #start_val').html('');
            }
        }

        if(!end_time) {
            $('#addAgentModal #end_val').html('Please select End time');
            validate = 1;
        } else {
            if(parseInt(end_time) <= parseInt(start_time)) {
                $('#addAgentModal #end_val').html('End time should be greater than Start time.');
                validate = 1;
            } else {
                $('#addAgentModal #end_val').html('');
            }
        }

        if(validate == 1) {
            return false;
        }

        if(appid) {

            $('#addAgent').attr('disabled','disabled');
            if(channel =='international_softphone' || channel =='national_softphone'){
                var url = 'https://rest.telecmi.com/v2/user/add';
                phone_number_new =$('#addAgentModal #phone_code').val()+phone_number;
            }else{
                var url = 'https://piopiy.telecmi.com/v1/agent/add';
                phone_number_new =parseInt(phone_number);
            }
            //$('.followers-div').show();
            $.ajax({
                type: "POST",
                url: url,
                contentType: "application/json",
                data: JSON.stringify({
                    name:name,
                    phone_number:phone_number_new,
                    start_time:parseInt(start_time),
                    end_time:parseInt(end_time),
                    password:password,
                    extension:parseInt(extension),
                    appid:parseInt(appid),
                    secret:secret,
                    sms_alert: (sms_alert == 'true' ? true : false)
                }),
                dataType: 'json',
                async: false,
                success: function(msg){
                if(msg.status == 'success') {
                    if(channel =='international_softphone' || channel =='national_softphone'){
                        var url2 = 'https://rest.telecmi.com/v2/user/status';
                    }else{
                        var url2 = 'https://piopiy.telecmi.com/v1/agent/status';
                    }
                    //$('.followers-div').show();
                    $.ajax({
                        type: "POST",
                        url: url2,
                        contentType: "application/json",
                        data: JSON.stringify({
                            id:msg.agent.agent_id,
                            appid:parseInt(appid),
                            secret:secret,
                            status:status
                        }),
                        dataType: 'json',
                        async: false,
                        success: function(res){
                            if(res.code == 'cmi-200' || res.code == '200') {
                                var url3 =  admin_url+'call_settings/saveAgent';
                                //$('.followers-div').show();
                                $.ajax({
                                    type: "POST",
                                    url: url3,
                                    data: {
                                        extid:extid,
                                        phone_number:phone_number,
                                        start_time:start_time,
                                        end_time:end_time,
                                        password:password,
                                        agentid:msg.agent.agent_id,
                                        secret:secret,
                                        sms_alert:sms_alert,
                                        status:res.status,
                                        ivr_id:ivr_id,
                                        phone_country_code:phone_country_code,
                                    },
                                    dataType: 'json',
                                    success: function(result){
                                        console.log(result);
                                        if(result.status == 'success') {
                                            alert_float('success', result.msg);
                                            setTimeout(function(){
                                                window.location.reload();
                                            },1000);
                                        } else {
                                            alert_float('warning', result.msg);
                                            setTimeout(function(){
                                                window.location.reload();
                                            },1000);
                                        }
                                    }
                                });
                                //alert(res.msg);
                            } else {
                                alert_float('warning', res.msg);
                                setTimeout(function(){
                                    window.location.reload();
                                },1000);
                            }
                        }
                    });
                } else {
                    alert_float('warning', msg.msg);
                    setTimeout(function(){
                        window.location.reload();
                    },1000);
                }
                }
            });

            
        } else {
            alert_float('warning', 'Please enable call settings.');
            setTimeout(function(){
                window.location.reload();
            },1000);
        }
    });
    
    
    $('#editAgentModal select#staff_id').on('change', function() {
        var emp_id = this.value;
        if(emp_id) {
            var url =  admin_url+'call_settings/getEmpDetail';
            //$('.followers-div').show();
            $.ajax({
                type: "POST",
                url: url,
                data: {emp_id:emp_id},
                dataType: 'json',
                success: function(msg){
                  console.log(msg.phone);
                  if(msg.phone) {
                    $('#editAgentModal #phone').val(msg.phone);
                    $('#editAgentModal #ext').val(emp_id);
                    $('#editAgentModal #name').val(msg.name);
                  } else {
                    $('#editAgentModal #phone').val('');
                    $('#editAgentModal #name').val('');
                  }
                }
            });
        }
    });

    $('#editAgent').on('click', function() {
        var extension = $('#editAgentModal #agentid').val();
        var name = $('#editAgentModal #name').val();
        var phone_number = $('#editAgentModal #phone').val();
        var start_time = $('#editAgentModal #starttime').val();
        var end_time = $('#editAgentModal #endtime').val();
        var status = $('#editAgentModal #status').val();
        var password = $('#editAgentModal #password').val();
        
        var sms_alert = $('#editAgentModal #sms_alert').val();
        var extid = $('#editAgentModal #id').val();
        var staff_id = $('#editAgentModal #staff_id').val();
        var ivr_id = $('#editAgentModal #ivr_id').val();
        var phone_country_code = $('#editAgentModal #phone_country_code').val();
        //alert(staff_id);

// Validations
        var validate = 0;
        
        var ivr_details =get_ivr_details(ivr_id);
        if (typeof ivr_details.app_id == 'undefined'){
            validate = 1;
            $('#editAgentModal #ivr_id_val').html('IVR should be selected');
        }else{
            var appid = ivr_details.app_id;
            var secret = ivr_details.app_secret;
            var channel = ivr_details.channel;
        }
        if(!staff_id) {
            $('#editAgentModal #staff_val').html('Please select Staff');
            validate = 1;
        } else {
            $('#editAgentModal #staff_val').html('');
        }
        if($('#editAgentModal #staff_id').is(':disabled') == false){
            $.ajax({
                type: "POST",
                url: admin_url+'call_settings/validate_agent_id/'+staff_id,
                contentType: "application/json",
                dataType: 'json',
                async: false,
                success: function(msg){
                    if(msg.success ==false){
                        $('#editAgentModal #staff_val').html(msg.message);
                        validate = 1;  
                    }
                }
            });
        }
        


        if(!phone_number) {
            $('#editAgentModal #phone_val').html('Please enter Phone number, It should have minimum 7 digits to maximum 20 digits');
            validate = 1;
        } else {
            if(phone_number.length > 6 && phone_number.length < 21) {
                $('#editAgentModal #phone_val').html('');
            } else {
                $('#editAgentModal #phone_val').html('Please enter Valid Phone number, It should have minimum 7 digits to maximum 20 digits');
                validate = 1;
            }
            
        }

        if(!password) {
            $('#editAgentModal #pass_val').html('Please enter Password');
            validate = 1;
        } else {
            if(password.length < 6) {
                $('#editAgentModal #pass_val').html('Password must contain minimum 6 characters');
                validate = 1;
            } else {
                $('#editAgentModal #pass_val').html('');
            }
        }

        if(!start_time) {
            $('#editAgentModal #start_val').html('Please select Start time');
            validate = 1;
        } else {
            if(parseInt(end_time) <= parseInt(start_time)) {
                $('#editAgentModal #start_val').html('Start time should be less than End time.');
                validate = 1;
            } else {
                $('#editAgentModal #start_val').html('');
            }
        }

        if(!end_time) {
            $('#editAgentModal #end_val').html('Please select End time');
            validate = 1;
        } else {
            if(parseInt(end_time) <= parseInt(start_time)) {
                $('#editAgentModal #end_val').html('End time should be greater than Start time.');
                validate = 1;
            } else {
                $('#editAgentModal #end_val').html('');
            }
        }

        if(validate == 1) {
            return false;
        }
        if(appid) {
            $('#editAgent').attr('disabled','disabled');
            if(channel =='international_softphone' || channel =='national_softphone'){
                var url = 'https://rest.telecmi.com/v2/user/update';
                phone_number_new =$('#editAgentModal #phone_code').val()+phone_number
            }else{
                var url = 'https://piopiy.telecmi.com/v1/agent/update';
                phone_number_new =parseInt(phone_number);
            }
            //$('.followers-div').show();
            $.ajax({
                type: "POST",
                url: url,
                contentType: "application/json",
                data: JSON.stringify({
                    name:name,
                    phone_number:phone_number_new,
                    start_time:parseInt(start_time),
                    end_time:parseInt(end_time),
                    password:password,
                    id:extension,
                    appid:parseInt(appid),
                    secret:secret,
                    sms_alert: (sms_alert == 'true' ? true : false)
                }),
                dataType: 'json',
                async: false,
                success: function(msg){
                if(msg.status == 'success') {
                    if(channel =='international_softphone' || channel =='national_softphone'){
                        var url2 = 'https://rest.telecmi.com/v2/user/status';
                    }else{
                        var url2 = 'https://piopiy.telecmi.com/v1/agent/status';
                    }
                    
                    //$('.followers-div').show();
                    $.ajax({
                        type: "POST",
                        url: url2,
                        contentType: "application/json",
                        data: JSON.stringify({
                            id:extension,
                            appid:parseInt(appid),
                            secret:secret,
                            status:status
                        }),
                        dataType: 'json',
                        async: false,
                        success: function(res){
                            if(res.code == 'cmi-200' || res.code == '200') {
                                var url3 =  admin_url+'call_settings/updateAgent';
                                //$('.followers-div').show();
                                $.ajax({
                                    type: "POST",
                                    url: url3,
                                    data: {
                                        id:extid,
                                        phone_number:phone_number,
                                        start_time:start_time,
                                        end_time:end_time,
                                        password:password,
                                        agentid:extension,
                                        sms_alert:sms_alert,
                                        status:res.status,
                                        staff_id:staff_id,
                                        phone_country_code:phone_country_code,
                                    },
                                    dataType: 'json',
                                    success: function(result){
                                        if(result.status == 'success') {
                                            alert_float('success', result.msg);
                                            setTimeout(function(){
                                                window.location.reload();
                                            },1000);
                                        } else {
                                            alert_float('warning', result.msg);
                                            setTimeout(function(){
                                                window.location.reload();
                                            },1000);
                                        }
                                    }
                                });
                                //alert(res.msg);
                            } else {
                                alert_float('warning', res.msg);
                                setTimeout(function(){
                                    window.location.reload();
                                },1000);
                            }
                        }
                    });
                } else {
                    alert_float('warning', msg.msg);
                    setTimeout(function(){
                        window.location.reload();
                    },1000);
                }
                }
            });

        } else {
            alert_float('warning', 'Please enable call settings.');
            setTimeout(function(){
                window.location.reload();
            },1000);
        }
    });
	
	
	$('#daffyeditAgent').on('click', function() {
		var extension = $('#editAgentModal #agentid').val();
        var name = $('#editAgentModal #name').val();
        var phone_number = $('#editAgentModal #phone').val();
        var edit_phone = $('#editAgentModal #edit_phone1').val();
       
        var status = $('#editAgentModal #status').val();
        var extid = $('#editAgentModal #id').val();
        var staff_id = $('#editAgentModal #staff_id').val();
		var validate = 0;

        var ivr_id = $('#editAgentModal #ivr_id').val();
        var phone_country_code = $('#editAgentModal #phone_country_code').val();
        var ivr_details =get_ivr_details(ivr_id);
        if (typeof ivr_details.app_id == 'undefined'){
            validate = 1;
            $('#editAgentModal #ivr_id_val').html('IVR should be selected');
        }else{
            var appid = ivr_details.app_id;
            var secret = ivr_details.app_secret;
            var channel = ivr_details.channel;
        }

        if(!staff_id) {
            $('#editAgentModal #staff_val').html('Please select Staff');
            validate = 1;
        } else {
            $('#editAgentModal #staff_val').html('');
        }

        if(!phone_number) {
            $('#editAgentModal #phone_val').html('Please enter Phone number, It should have minimum 7 digits to maximum 20 digits');
            validate = 1;
        } else {
            if(phone_number.length > 6 && phone_number.length < 21) {
                $('#editAgentModal #phone_val').html('');
            } else {
                $('#editAgentModal #phone_val').html('Please enter Valid Phone number, It should have minimum 7 digits to maximum 20 digits');
                validate = 1;
            }
            
        }


        if(validate == 1) {
            return false;
        }

        if(appid) {
			daffyeditagent('');
		} else {
			alert_float('warning', 'Please enable call settings.');
			setTimeout(function(){
				window.location.reload();
			},1000);
		}
	});
	function daffyeditagent(cur_val){
		$('#targeteditAgent').attr('disabled','disabled');
		var extension = $('#editAgentModal #agentid').val();
        var name = $('#editAgentModal #name').val();
        var phone_number = $('#editAgentModal #phone').val();
        var edit_phone = $('#editAgentModal #edit_phone1').val();
       
        var status = $('#editAgentModal #status').val();
        var extid = $('#editAgentModal #id').val();
        var staff_id = $('#editAgentModal #staff_id').val();

        var ivr_id = $('#editAgentModal #ivr_id').val();
        var phone_country_code = $('#editAgentModal #phone_country_code').val();
        var ivr_details =get_ivr_details(ivr_id);
        if (typeof ivr_details.app_id == 'undefined'){
            alert_float('warning', 'IVR should be selected');
            setTimeout(function(){
                window.location.reload();
            },1000);
        }else{
            var appid = ivr_details.app_id;
            var secret = ivr_details.app_secret;
            var channel = ivr_details.channel;
        }
        
		var url3 =  admin_url+'call_settings/updateAgent';
		$.ajax({
			type: "POST",
			url: url3,
			data: {
				id:extid,
				phone_number:phone_number,
				token:'',
				 status:status,
				staff_id:staff_id,
                phone_country_code:phone_country_code
				
			},
			dataType: 'json',
			
			success: function(result1){
				if(result1.status == 'success') {
					alert_float('success', result1.msg);
					setTimeout(function(){
						window.location.reload();
					},1000);
				} else {
					alert_float('warning', result1.msg);
					setTimeout(function(){
						window.location.reload();
					},1000);
				}
			}
		});
	}
	$('#targeteditAgent').on('click', function() {
        var extension = $('#editAgentModal #agentid').val();
        var name = $('#editAgentModal #name').val();
        var phone_number = $('#editAgentModal #phone').val();
        var edit_phone = $('#editAgentModal #edit_phone1').val();
       
        var status = $('#editAgentModal #status').val();
        var extid = $('#editAgentModal #id').val();
        var staff_id = $('#editAgentModal #staff_id').val();
        var phone_country_code = $('#editAgentModal #phone_country_code').val();
        //alert(staff_id);

// Validations
		var validate = 0;

        var ivr_id = $('#editAgentModal #ivr_id').val();
        var ivr_details =get_ivr_details(ivr_id);
        if (typeof ivr_details.app_id == 'undefined'){
            validate = 1;
            $('#editAgentModal #ivr_id_val').html('IVR should be selected');
        }else{
            var appid = ivr_details.app_id;
            var secret = ivr_details.app_secret;
            var channel = ivr_details.channel;
        }

        if(!staff_id) {
            $('#editAgentModal #staff_val').html('Please select Staff');
            validate = 1;
        } else {
            $('#editAgentModal #staff_val').html('');
        }

        if(!phone_number) {
            $('#editAgentModal #phone_val').html('Please enter Phone number, It should have minimum 7 digits to maximum 20 digits');
            validate = 1;
        } else {
            if(phone_number.length > 6 && phone_number.length < 21) {
                $('#editAgentModal #phone_val').html('');
            } else {
                $('#editAgentModal #phone_val').html('Please enter Valid Phone number, It should have minimum 7 digits to maximum 20 digits');
                validate = 1;
            }
            
        }


        if(validate == 1) {
            return false;
        }

        if(appid) {
			tataeditagent('');
		} else {
			alert_float('warning', 'Please enable call settings.');
			setTimeout(function(){
				window.location.reload();
			},1000);
		}
	});
    
    


    $(".addfollower_btn").click(function(){
        var employee= $("select[name=\'employee[]\']").map(function() {
            return $(this).val();
        }).toArray();
        var emp_id = $('#emp_id').val();
        $('.addfollower_btn').css("pointer-events", "none");
        $('.addfollower_btn').css("cursor", "default");
        //var length = Object.keys(product).length
        var length = $('#product_index').val();
        length = parseInt(length)+parseInt(1);
            var url =  admin_url+'AssignFollowers/getaddfollowerfields';
            $.ajax({
                type: "POST",
                url: url,
                data: {employee:employee,length:length,emp_id,emp_id},
                success: function(msg){
                    $('#product_index').val(length);
                    $(".field_follower_wrapper").append(msg);
                    $('.addfollower_btn').css("pointer-events", "auto");
                    $('.addfollower_btn').css("cursor", "pointer");
                }
            });
    });
    $(".field_follower_wrapper").on('click', '.removefollower_button', function(e){
        e.preventDefault();
        var divid = $(this).parent('div').parent('div').attr('id');
        if(divid) {
            $('#'+divid).remove();
        } else {
            var divid = $(this).parent().parent().parent().parent().attr('id');
            $('#'+divid).remove(); //Remove field html
        }
    });
});
$(document).ready(function(){
    var maxField = 10; //Input fields increment limitation
    var addButton = $('.add_button'); //Add button selector
    var addButton1 = $('.add_button1'); //Add button selector
    var addeditButton = $('.addedit_button'); 
    var editvariationButton = $('.editvariation_button'); 
    var wrapper = $('.field_wrapper'); //Input field wrapper
    var wrapperVariation = $('.field_variation_wrapper'); //Input field 
    var addproduts_btn = $('.addproduts_btn'); //Add button selector
    var addproduts_btn1 = $('.addproduts_btn1'); //Add button selector
    var wrapperproduct = $('.field_product_wrapper'); //Input field 
    
    var x = 1; //Initial field counter is 1
    
    //Once add button is clicked
    $(addButton).click(function(){
        var currency= $("select[name=\'currency[]\']").map(function() {
            return $(this).val();
        }).toArray();
        //alert(currency);
        if(x < maxField){ 
            x++; //Increment field counter
            var url =  admin_url+'products/getaddfields';
            
            $.ajax({
                type: "POST",
                url: url,
                data: {currency:currency},
                success: function(msg){
                    $(wrapper).append(msg);
                }
            });
             //Add field html
        }
    });
	
	//Once add button is clicked
    $(addButton1).click(function(){
        var currency= $("select[name=\'currency[]\']").map(function() {
            return $(this).val();
        }).toArray();
        if(x < maxField){ 
            x++; //Increment field counter
            var url =  'invoice_items/getaddfields';
            
            $.ajax({
                type: "POST",
                url: url,
                data: {currency:currency},
                success: function(msg){
                    $('.field_wrapper').append(msg);
                }
            });
             //Add field html
        }
    });

    $(addproduts_btn).click(function(){
        var product= $("select[name=\'product[]\']").map(function() {
            return $(this).val();
        }).toArray();
        $('.addproduts_btn').css("pointer-events", "none");
        $('.addproduts_btn').css("cursor", "default");
        //var length = Object.keys(product).length
        var length = $('#product_index').val();
        var currency = $('#currency').val();
        length = parseInt(length)+parseInt(1);
            var url =  admin_url+'products/getaddproductfields';
            
            $.ajax({
                type: "POST",
                url: url,
                data: {product:product,length:length,currency:currency},
                success: function(msg){
                    $('#product_index').val(length);
                    $(wrapperproduct).append(msg);
                    $('.addproduts_btn').css("pointer-events", "auto");
                    $('.addproduts_btn').css("cursor", "pointer");
                }
            });
    });
	
	$(addproduts_btn1).click(function(){
        var product= $("select[name=\'product[]\']").map(function() {
            return $(this).val();
        }).toArray();
        $('.addproduts_btn').css("pointer-events", "none");
        $('.addproduts_btn').css("cursor", "default");
        //var length = Object.keys(product).length
        var length = $('#product_index').val();
        var currency = $('#currency').val();
        length = parseInt(length)+parseInt(1);
            var url =  admin_url+'invoice_items/getaddproductfields';
            
            $.ajax({
                type: "POST",
                url: url,
                data: {product:product,length:length,currency:currency},
                success: function(msg){
                    $('#product_index').val(length);
                    $(wrapperproduct).append(msg);
                    $('.addproduts_btn').css("pointer-events", "auto");
                    $('.addproduts_btn').css("cursor", "pointer");
                }
            });
    });

    $('.savevariation').click(function(){
        var productid = $('#product_id').val();
        var variation = $('#variation').val();
        if(variation) {
            var url =  admin_url+'products/savevariation';
            $.ajax({
                type: "POST",
                url: url,
                data: {product:productid,variation:variation},
                success: function(msg){
                    window.location.href = admin_url+'products/product/'+productid;
                }
            });
        } else {
            alert('Please enter the variation name.');
            return false;
        }
    });

    var editproduts_notax_btn = $('.editproduts_notax_btn'); //Add button selector
    $(editproduts_notax_btn).click(function(){
        
        var product= $("select[name=\'product[]\']").map(function() {
            return $(this).val();
        }).toArray();
        $('.editproduts_notax_btn').css("pointer-events", "none");
        $('.editproduts_notax_btn').css("cursor", "default");
        var method = $('#method').val();
        //var length = Object.keys(product).length
        var length = $('#product_index').val();
        length = parseInt(length)+parseInt(1);
        var currency = $('#currency').val();
        if(!currency) {
            var currency = $('#currency1').val();   
        }
        if(method == 1) {
            var url =  admin_url+'products/getdealproductfields';
            var project = $('#prject_id').val();
            $.ajax({
                type: "POST",
                url: url,
                data: {projectnew:project,product:product,length:length,currency:currency},
                success: function(msg){
                    $('#product_index').val(length);
                    $(wrapperproduct).append(msg);
                    $('.editproduts_notax_btn').css("pointer-events", "auto");
                    $('.editproduts_notax_btn').css("cursor", "pointer");
                }
            });
        }
        if(method == 2) {
            var url =  admin_url+'products/getintaxfields';
            var project = $('#prject_id').val();
            $.ajax({
                type: "POST",
                url: url,
                data: {project:project,length:length,currency:currency},
                success: function(msg){
                    $('#method').val(2);
                    $('#product_index').val(length);
                    $(wrapperproduct).append(msg);
                    $('.editproduts_notax_btn').css("pointer-events", "auto");
                    $('.editproduts_notax_btn').css("cursor", "pointer");
                }
            });
        }

        if(method == 3) {
            var url =  admin_url+'products/getintaxfields';
            var project = $('#prject_id').val();
            $.ajax({
                type: "POST",
                url: url,
                data: {project:project,length:length,currency:currency},
                success: function(msg){
                    $('#method').val(3);
                    $('#product_index').val(length);
                    $(wrapperproduct).append(msg);
                    $('.editproduts_notax_btn').css("pointer-events", "auto");
                    $('.editproduts_notax_btn').css("cursor", "pointer");
                }
            });
        }
    });

    $('#intax').click(function(){
        $("#suptotaltxt").html('');
        $("#suptotal").html('');
        var discount_value = $('#discount_value').val();
        var discount_option = $('#discount_option').val();
            var url =  admin_url+'products/getextaxfields';
            var project = $('#prject_id').val();
            var length = $('#product_index').val();
            length = parseInt(length)+parseInt(1);
            var currency = $('#currency').val();
            $.ajax({
                type: "POST",
                url: url,
                data: {project:project,length:length,currency:currency},
                success: function(msg){
                    $('#product_index').val(length);
                    $('#method').val(2);
                    $(wrapperproduct).html(msg);
                    var sum = 0;
                    var inps = document.getElementsByName('total[]');
                    for (var i = 0; i <inps.length; i++) {
                        var inp=inps[i];
                        if(inp.value)
                            sum = parseFloat(sum)+parseFloat(inp.value);
                    }
                    $('#grandtotal').html(sum.toFixed(2));
                    $('#gtot').val(sum.toFixed(2));
                    $('input[name="project_cost"]').val(sum.toFixed(2));
                    $('input[name="project_cost"]').attr('readonly', true);

                    var method = $("#method").val();
            //Discount
            var prical = document.getElementsByName('price[]');
            var quancal = document.getElementsByName('qty[]');
            var disc_txt = '';
            var discal = document.getElementsByName('discount[]');
            for (var i = 0; i < discal.length; i++) {
                var dis=discal[i];
                if(dis.value && dis.value > 0) {
                    var inp=inps[i];
                    var pri = prical[i];
                    var quanc = quancal[i];
                    var totc = (pri.value * quanc.value);
                    var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
                    var mult1 = totc * dec1; // gives the value for subtract from main value
                    disc_txt += ' '+mult1.toFixed(2)+',';
                }
            }
            if(disc_txt != '') {
                disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
                $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
            } else {
                $("#stxt").html('<p>Subtotal</p>');
            }
            $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');    
                    if(method == 2) {
                        if(discount_value == 1 || discount_option == 1) {
                            $('#topheading').html('<div class="col-md-2" style="width:20%;">Item</div><div class="col-md-2">Price</div><div class="col-md-1">Quantity</div><div class="col-md-2">Tax</div><div class="col-md-2">Discount %</div><div class="col-md-2">Total</div>');
                        } else {
                            $('#topheading').html('<div class="col-md-2" style="width:20%;">Item</div><div class="col-md-2">Price</div><div class="col-md-1">Quantity</div><div class="col-md-2">Tax</div><div class="col-md-2">Total</div>');
                        }
                        //$("#stxt").html('<p>Subtotal</p>');
                        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                        //$('#'+index+' input[name="tax[]"]').val('');
                        for (var index = 0; index <inps.length; index++) {
                            var taxvalue = $('#'+index+' input[name="tax[]"]').val();
                            var tprice = $('#'+index+' input[name="total[]"]').val();
                            //var total = value*price;
                            if(taxvalue) {
                                var taxprice = (tprice * taxvalue) / 100;
                                
                                if($('.txt_'+index).length == 0) {
                                    $('#suptotaltxt').append('<p class="txt_'+index+'">Includes Tax ('+taxvalue+'%)</p>');
                                    $('#suptotal').append('<p class="amt_'+index+'">'+taxprice.toFixed(2)+'</p>');
                                } else {
                                    $('.txt_'+index).html('Includes Tax ('+taxvalue+'%)');
                                    $('.amt_'+index).html(taxprice.toFixed(2));
                                }
                            }
                        }
                        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                        $('#grandtotal').html(sum.toFixed(2));
                        $('#gtot').val(sum.toFixed(2));
                    }
                }
            });
    });

    $('#extax').click(function(){
        $("#suptotaltxt").html('');
        $("#suptotal").html('');
        var discount_value = $('#discount_value').val();
        var discount_option = $('#discount_option').val();
        
            var url =  admin_url+'products/getextaxfields';
            var project = $('#prject_id').val();
            var length = $('#product_index').val();
            length = parseInt(length)+parseInt(1);
            var currency = $('#currency').val();
            $.ajax({
                type: "POST",
                url: url,
                data: {project:project,length:length,currency:currency},
                success: function(msg){
                    $('#product_index').val(length);
                    $('#method').val(3);
                    $(wrapperproduct).html(msg);
                    var sum = 0;
                    var inps = document.getElementsByName('total[]');
                    for (var i = 0; i <inps.length; i++) {
                        var inp=inps[i];
                        if(inp.value)
                            sum = parseFloat(sum)+parseFloat(inp.value);
                    }
                    $('#grandtotal').html(sum.toFixed(2));
                    $('#gtot').val(sum.toFixed(2));
                    $('input[name="project_cost"]').val(sum.toFixed(2));
                    $('input[name="project_cost"]').attr('readonly', true);

                    var method = $("#method").val();
            //Discount
            var prical = document.getElementsByName('price[]');
            var quancal = document.getElementsByName('qty[]');
            var disc_txt = '';
            var discal = document.getElementsByName('discount[]');
            for (var i = 0; i < discal.length; i++) {
                var dis=discal[i];
                if(dis.value && dis.value > 0) {
                    var inp=inps[i];
                    var pri = prical[i];
                    var quanc = quancal[i];
                    var totc = (pri.value * quanc.value);
                    var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
                    var mult1 = totc * dec1; // gives the value for subtract from main value
                    disc_txt += ' '+mult1.toFixed(2)+',';
                }
            }
            if(disc_txt != '') {
                disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
                $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
            }
                    if(method == 3) {
                        if(discount_value == 1 || discount_option == 1) {
                            $('#topheading').html('<div class="col-md-2" style="width:20%;">Item</div><div class="col-md-2">Price</div><div class="col-md-1">Quantity</div><div class="col-md-2">Tax</div><div class="col-md-2">Discount %</div><div class="col-md-2">Total</div>');
                        } else {
                            $('#topheading').html('<div class="col-md-2" style="width:20%;">Item</div><div class="col-md-2">Price</div><div class="col-md-1">Quantity</div><div class="col-md-2">Tax</div><div class="col-md-2">Total</div>');
                        }
                        
                        
                        //$('#'+index+' input[name="tax[]"]').val('');
                        for (var index = 0; index <inps.length; index++) {
                            var taxvalue = $('#'+index+' input[name="tax[]"]').val();
                            var tprice = $('#'+index+' input[name="total[]"]').val();
                            //var total = value*price;
                            if(taxvalue) {
                                var taxprice = (tprice * taxvalue) / 100;

                                //$("#stxt").html('<p>Subtotal</p>');
                                $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                                if($('.txt_'+index).length == 0) {
                                    $('#suptotaltxt').append('<p class="txt_'+index+'">Excludes Tax ('+taxvalue+'%)</p>');
                                    $('#suptotal').append('<p class="amt_'+index+'">'+taxprice.toFixed(2)+'</p>');
                                } else {
                                    $('.txt_'+index).html('Excludes Tax ('+taxvalue+'%)');
                                    $('.amt_'+index).html(taxprice.toFixed(2));
                                }
                            }
                        }
                        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                        var taxpr = document.getElementsByName('tax[]');
                        var tosumtpr = 0;
                        for (var i = 0; i <taxpr.length; i++) {
                            var tp=taxpr[i];
                            var inp=inps[i];
                            if(tp.value) {
                                var tottax = (inp.value * tp.value) / 100;
                                tosumtpr = parseFloat(tosumtpr)+parseFloat(tottax)+parseFloat(inp.value);
                            } else {
                                if(tp.value) {
                                    //tosumtpr = parseFloat(tosumtpr)+parseFloat(inp.value);
                                }
                            }
                        }
                        $('#grandtotal').html(tosumtpr.toFixed(2));
                        $('#gtot').val(tosumtpr.toFixed(2));
                    }
                }
            });
    });

    $('#notax').click(function(){
        $("#suptotaltxt").html('');
        $("#suptotal").html('');
        var discount_value = $('#discount_value').val();
        var discount_option = $('#discount_option').val();
            var url =  admin_url+'products/getdealproductfields';
            var project = $('#prject_id').val();
            var length = $('#product_index').val();
            length = parseInt(length)+parseInt(1);
            var currency = $('#currency').val();
            $.ajax({
                type: "POST",
                url: url,
                data: {project:project,length:length,currency:currency},
                success: function(msg){
                    if(discount_value == 1 || discount_option == 1) {
                        $('#topheading').html('<div class="col-md-3">Item</div><div class="col-md-2">Price</div><div class="col-md-2">Quantity</div><div class="col-md-2">Discount %</div><div class="col-md-2">Total</div>');
                    } else {
                        $('#topheading').html('<div class="col-md-3">Item</div><div class="col-md-2">Price</div><div class="col-md-2">Quantity</div><div class="col-md-2">Total</div>');
                    }
                    
                    $('#product_index').val(length);
                    $('#method').val(1);
                    $(wrapperproduct).html(msg);
                    var sum = 0;
                    var inps = document.getElementsByName('total[]');
                    for (var i = 0; i <inps.length; i++) {
                        var inp=inps[i];
                        if(inp.value)
                            sum = parseFloat(sum)+parseFloat(inp.value);
                    }
            //Discount
            var prical = document.getElementsByName('price[]');
            var quancal = document.getElementsByName('qty[]');
            var disc_txt = '';
            var discal = document.getElementsByName('discount[]');
            for (var i = 0; i < discal.length; i++) {
                var dis=discal[i];
                if(dis.value && dis.value > 0) {
                    var inp=inps[i];
                    var pri = prical[i];
                    var quanc = quancal[i];
                    var totc = (pri.value * quanc.value);
                    var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
                    var mult1 = totc * dec1; // gives the value for subtract from main value
                    disc_txt += ' '+mult1.toFixed(2)+',';
                }
            }
                    if(disc_txt != '') {
                        disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
                        $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
                    } else {
                        $("#stxt").html('<p>Subtotal</p>');
                    }
                    $('#stotal').html('<p>'+sum.toFixed(2)+'</p>');
                    $('#grandtotal').html(sum.toFixed(2));
                    $('#gtot').val(sum.toFixed(2));
                    $('input[name="project_cost"]').val(sum.toFixed(2));
                    $('input[name="project_cost"]').attr('readonly', true);
                }
            });
    });


    //For Sales product LIKE proposal, estimation, invoice
    $('#salesintax').click(function(){
        $("#suptotaltxt").html('');
        $("#suptotal").html('');
        var discount_value = $('#discount_value').val();
        var discount_option = $('#discount_option').val();
        var change_items = $('#change_items').val();
            var project = $('#prject_id').val();
            if(project.length == 0 || change_items == 1) {
                var project = $('#rel_id').val();
                var url =  admin_url+'/products/getextaxfields';
            } else {
                var url =  admin_url+'products/getsalesextaxfields';
            }
            var length = $('#product_index').val();
            length = parseInt(length)+parseInt(1);
            var currency = $('#currency1').val();
            var item_type = $('#item_for').val();
            $.ajax({
                type: "POST",
                url: url,
                data: {project:project,length:length,currency:currency,item_type:item_type},
                success: function(msg){
                    $('#product_index').val(length);
                    $('#method').val(2);
                    $(wrapperproduct).html(msg);
                    var sum = 0;
                    var inps = document.getElementsByName('total[]');
                    for (var i = 0; i <inps.length; i++) {
                        var inp=inps[i];
                        if(inp.value)
                            sum = parseFloat(sum)+parseFloat(inp.value);
                    }
                    $('#grandtotal').html(sum.toFixed(2));
                    $('#gtot').val(sum.toFixed(2));
                    $('input[name="project_cost"]').val(sum.toFixed(2));
                    $('input[name="project_cost"]').attr('readonly', true);

                    var method = $("#method").val();
                    //Discount
                    var prical = document.getElementsByName('price[]');
                    var quancal = document.getElementsByName('qty[]');
                    var disc_txt = '';
                    var discal = document.getElementsByName('discount[]');
                    for (var i = 0; i < discal.length; i++) {
                        var dis=discal[i];
                        if(dis.value && dis.value > 0) {
                            var inp=inps[i];
                            var pri = prical[i];
                            var quanc = quancal[i];
                            var totc = (pri.value * quanc.value);
                            var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
                            var mult1 = totc * dec1; // gives the value for subtract from main value
                            disc_txt += ' '+mult1.toFixed(2)+',';
                        }
                    }
                    if(disc_txt != '') {
                        disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
                        $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
                    } else {
                        $("#stxt").html('<p>Subtotal</p>');
                    }
                    $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');    
                    if(method == 2) {
                        if(discount_value == 1 || discount_option == 1) {
                            $('#topheading').html('<div class="col-md-2" style="width:20%;">Item</div><div class="col-md-2">Price</div><div class="col-md-1">Quantity</div><div class="col-md-2">Tax</div><div class="col-md-2">Discount %</div><div class="col-md-2">Total</div>');
                        } else {
                            $('#topheading').html('<div class="col-md-2" style="width:20%;">Item</div><div class="col-md-2">Price</div><div class="col-md-1">Quantity</div><div class="col-md-2">Tax</div><div class="col-md-2">Total</div>');
                        }
                        //$("#stxt").html('<p>Subtotal</p>');
                        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                        //$('#'+index+' input[name="tax[]"]').val('');
                        for (var index = 0; index <inps.length; index++) {
                            var taxvalue = $('#'+index+' input[name="tax[]"]').val();
                            
                            var tprice = $('#'+index+' input[name="total[]"]').val();
                            //var total = value*price;
                            if(taxvalue) {
                                var taxprice = (tprice * taxvalue) / 100;
                                
                                //if($('.txt_'+index).length == 0) {
                                   // console.log('<p class="txt_'+index+'">Includes Tax ('+taxvalue+'%)</p>');
                                    $('#suptotaltxt').append('<p class="txt_'+index+'">Includes Tax ('+taxvalue+'%)</p>');
                                    $('#suptotal').append('<p class="amt_'+index+'">'+taxprice.toFixed(2)+'</p>');
                                // } else {
                                //     $('.txt_'+index).html('Includes Tax ('+taxvalue+'%)');
                                //     $('.amt_'+index).html(taxprice.toFixed(2));
                                // }
                            }
                        }
                        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                        $('#grandtotal').html(sum.toFixed(2));
                        $('#gtot').val(sum.toFixed(2));
                    }
                }
            });
    });

    $('#salesextax').click(function(){
        $("#suptotaltxt").html('');
        $("#suptotal").html('');
        var discount_value = $('#discount_value').val();
        var discount_option = $('#discount_option').val();
        var change_items = $('#change_items').val();
            var project = $('#prject_id').val();
            if(project.length == 0 || change_items == 1) {
                var project = $('#rel_id').val();
                var url =  admin_url+'/products/getextaxfields';
            } else {
                var url =  admin_url+'products/getsalesextaxfields';
            }
            var length = $('#product_index').val();
            var item_type = $('#item_for').val();
            //alert(item_type);
            length = parseInt(length)+parseInt(1);
            var currency = $('#currency1').val();
            $.ajax({
                type: "POST",
                url: url,
                data: {project:project,length:length,currency:currency,item_type:item_type},
                success: function(msg){
                    $('#product_index').val(length);
                    $('#method').val(3);
                    $(wrapperproduct).html(msg);
                    var sum = 0;
                    var inps = document.getElementsByName('total[]');
                    for (var i = 0; i <inps.length; i++) {
                        var inp=inps[i];
                        if(inp.value)
                            sum = parseFloat(sum)+parseFloat(inp.value);
                    }
                    $('#grandtotal').html(sum.toFixed(2));
                    $('#gtot').val(sum.toFixed(2));
                    $('input[name="project_cost"]').val(sum.toFixed(2));
                    $('input[name="project_cost"]').attr('readonly', true);

                    var method = $("#method").val();
                    //Discount
                    var prical = document.getElementsByName('price[]');
                    var quancal = document.getElementsByName('qty[]');
                    var disc_txt = '';
                    var discal = document.getElementsByName('discount[]');
                    for (var i = 0; i < discal.length; i++) {
                        var dis=discal[i];
                        if(dis.value && dis.value > 0) {
                            var inp=inps[i];
                            var pri = prical[i];
                            var quanc = quancal[i];
                            var totc = (pri.value * quanc.value);
                            var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
                            var mult1 = totc * dec1; // gives the value for subtract from main value
                            disc_txt += ' '+mult1.toFixed(2)+',';
                        }
                    }
                    if(disc_txt != '') {
                        disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
                        $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
                    } else {
                        $("#stxt").html('<p>Subtotal</p>');
                    }
                    if(method == 3) {
                        if(discount_value == 1 || discount_option == 1) {
                            $('#topheading').html('<div class="col-md-2" style="width:20%;">Item</div><div class="col-md-2">Price</div><div class="col-md-1">Quantity</div><div class="col-md-2">Tax</div><div class="col-md-2">Discount %</div><div class="col-md-2">Total</div>');
                        } else {
                            $('#topheading').html('<div class="col-md-2" style="width:20%;">Item</div><div class="col-md-2">Price</div><div class="col-md-1">Quantity</div><div class="col-md-2">Tax</div><div class="col-md-2">Total</div>');
                        }
                        
                        
                        //$('#'+index+' input[name="tax[]"]').val('');
                        for (var index = 0; index <inps.length; index++) {
                            var taxvalue = $('#'+index+' input[name="tax[]"]').val();
                            var tprice = $('#'+index+' input[name="total[]"]').val();
                            //var total = value*price;
                            if(taxvalue) {
                                var taxprice = (tprice * taxvalue) / 100;

                                //$("#stxt").html('<p>Subtotal</p>');
                                $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                                //if($('.txt_'+index).length == 0) {
                                    $('#suptotaltxt').append('<p class="txt_'+index+'">Excludes Tax ('+taxvalue+'%)</p>');
                                    $('#suptotal').append('<p class="amt_'+index+'">'+taxprice.toFixed(2)+'</p>');
                                // } else {
                                //     $('.txt_'+index).html('Excludes Tax ('+taxvalue+'%)');
                                //     $('.amt_'+index).html(taxprice.toFixed(2));
                                // }
                            }
                        }
                        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                        var taxpr = document.getElementsByName('tax[]');
                        var tosumtpr = 0;
                        for (var i = 0; i <taxpr.length; i++) {
                            var tp=taxpr[i];
                            var inp=inps[i];
                            if(tp.value) {
                                var tottax = (inp.value * tp.value) / 100;
                                tosumtpr = parseFloat(tosumtpr)+parseFloat(tottax)+parseFloat(inp.value);
                            } else {
                                if(tp.value) {
                                    //tosumtpr = parseFloat(tosumtpr)+parseFloat(inp.value);
                                }
                            }
                        }
                        $('#grandtotal').html(tosumtpr.toFixed(2));
                        $('#gtot').val(tosumtpr.toFixed(2));
                    }
                }
            });
    });

    $('#salesnotax').click(function(){
            $("#suptotaltxt").html('');
            $("#suptotal").html('');
            var discount_value = $('#discount_value').val();
            var discount_option = $('#discount_option').val();
            var project = $('#prject_id').val();
            var change_items = $('#change_items').val();
            if(project.length == 0 || change_items == 1) {
                var project = $('#rel_id').val();
                var url =  admin_url+'/products/getdealproductfields';
            } else {
                var url =  admin_url+'products/getsalesproductfields';
            }
            var length = $('#product_index').val();
            length = parseInt(length)+parseInt(1);
            var item_type = $('#item_for').val();
            var currency = $('#currency1').val();
            $.ajax({
                type: "POST",
                url: url,
                data: {project:project,length:length,currency:currency,item_type:item_type},
                success: function(msg){
                    if(discount_value == 1 || discount_option == 1) {
                        $('#topheading').html('<div class="col-md-3">Item</div><div class="col-md-2">Price</div><div class="col-md-2">Quantity</div><div class="col-md-2">Discount %</div><div class="col-md-2">Total</div>');
                    } else {
                        $('#topheading').html('<div class="col-md-3">Item</div><div class="col-md-2">Price</div><div class="col-md-2">Quantity</div><div class="col-md-2">Total</div>');
                    }
                    
                    $('#product_index').val(length);
                    $('#method').val(1);
                    $(wrapperproduct).html(msg);
                    var sum = 0;
                    var inps = document.getElementsByName('total[]');
                    for (var i = 0; i <inps.length; i++) {
                        var inp=inps[i];
                        if(inp.value)
                            sum = parseFloat(sum)+parseFloat(inp.value);
                    }
                    //Discount
                    var prical = document.getElementsByName('price[]');
                    var quancal = document.getElementsByName('qty[]');
                    var disc_txt = '';
                    var discal = document.getElementsByName('discount[]');
                    for (var i = 0; i < discal.length; i++) {
                        var dis=discal[i];
                        if(dis.value && dis.value > 0) {
                            var inp=inps[i];
                            var pri = prical[i];
                            var quanc = quancal[i];
                            var totc = (pri.value * quanc.value);
                            var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
                            var mult1 = totc * dec1; // gives the value for subtract from main value
                            disc_txt += ' '+mult1.toFixed(2)+',';
                        }
                    }
                    if(disc_txt != '') {
                        disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
                        $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
                    } else {
                        $("#stxt").html('<p>Subtotal</p>');
                    }
                    $('#stotal').html('<p>'+sum.toFixed(2)+'</p>');
                    $('#grandtotal').html(sum.toFixed(2));
                    $('#gtot').val(sum.toFixed(2));
                    $('input[name="project_cost"]').val(sum.toFixed(2));
                    $('input[name="project_cost"]').attr('readonly', true);
                }
            });
    });

    

    $('.currencyswitcher').on('change', function (e) {
        var optionSelected = $("option:selected", this);
        var currency = this.value;
        var method = $('#method').val();
        var discount_value = $('#discount_value').val();
        var discount_option = $('#discount_option').val();
        $("#suptotaltxt").html('');
        $("#suptotal").html('');
        var project = $('#prject_id').val();
        var length = $('#product_index').val();
        length = parseInt(length)+parseInt(1);
        if(method == 1) {
            var url =  admin_url+'products/getdealproductfields';
            $.ajax({
                type: "POST",
                url: url,
                data: {project:project,length:length,currency:currency},
                success: function(msg){
                    if(discount_value == 1 || discount_option == 1) {
                        $('#topheading').html('<div class="col-md-3">Item</div><div class="col-md-2">Price</div><div class="col-md-2">Quantity</div><div class="col-md-2">Discount %</div><div class="col-md-2">Total</div>');
                    } else {
                        $('#topheading').html('<div class="col-md-3">Item</div><div class="col-md-2">Price</div><div class="col-md-2">Quantity</div><div class="col-md-2">Total</div>');
                    }
                    
                    $('#product_index').val(length);
                    $('#method').val(1);
                    $('.field_product_wrapper').html(msg);
                    var sum = 0;
                    var inps = document.getElementsByName('total[]');
                    for (var i = 0; i <inps.length; i++) {
                        var inp=inps[i];
                        if(inp.value)
                            sum = parseFloat(sum)+parseFloat(inp.value);
                    }
            //Discount
            var prical = document.getElementsByName('price[]');
            var quancal = document.getElementsByName('qty[]');
            var disc_txt = '';
            var discal = document.getElementsByName('discount[]');
            for (var i = 0; i < discal.length; i++) {
                var dis=discal[i];
                if(dis.value && dis.value > 0) {
                    var inp=inps[i];
                    var pri = prical[i];
                    var quanc = quancal[i];
                    var totc = (pri.value * quanc.value);
                    var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
                    var mult1 = totc * dec1; // gives the value for subtract from main value
                    disc_txt += ' '+mult1.toFixed(2)+',';
                }
            }
                    if(disc_txt != '') {
                        disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
                        $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
                    } else {
                        $("#stxt").html('<p>Subtotal</p>');
                    }
                    $('#stotal').html('<p>'+sum.toFixed(2)+'</p>');
                    $('#grandtotal').html(sum.toFixed(2));
                    $('#gtot').val(sum.toFixed(2));
                    $('input[name="project_cost"]').val(sum.toFixed(2));
                    $('input[name="project_cost"]').attr('readonly', true);
                }
            });
        } 
        if(method == 2) {
            var url =  admin_url+'/products/getextaxfields';
            $.ajax({
                type: "POST",
                url: url,
                data: {project:project,length:length,currency:currency},
                success: function(msg){
                    $('#product_index').val(length);
                    $('#method').val(2);
                    $('.field_product_wrapper').html(msg);
                    var sum = 0;
                    var inps = document.getElementsByName('total[]');
                    for (var i = 0; i <inps.length; i++) {
                        var inp=inps[i];
                        if(inp.value)
                            sum = parseFloat(sum)+parseFloat(inp.value);
                    }
                    $('#grandtotal').html(sum.toFixed(2));
                    $('#gtot').val(sum.toFixed(2));
                    $('input[name="project_cost"]').val(sum.toFixed(2));
                    $('input[name="project_cost"]').attr('readonly', true);

                    var method = $("#method").val();
            //Discount
            var prical = document.getElementsByName('price[]');
            var quancal = document.getElementsByName('qty[]');
            var disc_txt = '';
            var discal = document.getElementsByName('discount[]');
            for (var i = 0; i < discal.length; i++) {
                var dis=discal[i];
                if(dis.value && dis.value > 0) {
                    var inp=inps[i];
                    var pri = prical[i];
                    var quanc = quancal[i];
                    var totc = (pri.value * quanc.value);
                    var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
                    var mult1 = totc * dec1; // gives the value for subtract from main value
                    disc_txt += ' '+mult1.toFixed(2)+',';
                }
            }
            if(disc_txt != '') {
                disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
                $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
            } else {
                $("#stxt").html('<p>Subtotal</p>');
            }
            $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');    
                    if(method == 2) {
                        if(discount_value == 1 || discount_option == 1) {
                            $('#topheading').html('<div class="col-md-2" style="width:20%;">Item</div><div class="col-md-2">Price</div><div class="col-md-1">Quantity</div><div class="col-md-2">Tax</div><div class="col-md-2">Discount %</div><div class="col-md-2">Total</div>');
                        } else {
                            $('#topheading').html('<div class="col-md-2" style="width:20%;">Item</div><div class="col-md-2">Price</div><div class="col-md-1">Quantity</div><div class="col-md-2">Tax</div><div class="col-md-2">Total</div>');
                        }
                        
                        //$("#stxt").html('<p>Subtotal</p>');
                        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                        //$('#'+index+' input[name="tax[]"]').val('');
                        for (var index = 0; index <inps.length; index++) {
                            var taxvalue = $('#'+index+' input[name="tax[]"]').val();
                            var tprice = $('#'+index+' input[name="total[]"]').val();
                            //var total = value*price;
                            if(taxvalue) {
                                var taxprice = (tprice * taxvalue) / 100;
                                
                                if($('.txt_'+index).length == 0) {
                                    $('#suptotaltxt').append('<p class="txt_'+index+'">Includes Tax ('+taxvalue+'%)</p>');
                                    $('#suptotal').append('<p class="amt_'+index+'">'+taxprice.toFixed(2)+'</p>');
                                } else {
                                    $('.txt_'+index).html('Includes Tax ('+taxvalue+'%)');
                                    $('.amt_'+index).html(taxprice.toFixed(2));
                                }
                            }
                        }
                        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                        $('#grandtotal').html(sum.toFixed(2));
                        $('#gtot').val(sum.toFixed(2));
                    }
                }
            });
        }
        if(method == 3) {
            var url =  admin_url+'/products/getextaxfields';
            $.ajax({
                type: "POST",
                url: url,
                data: {project:project,length:length,currency:currency},
                success: function(msg){
                    $('#product_index').val(length);
                    $('#method').val(3);
                    $('.field_product_wrapper').html(msg);
                    var sum = 0;
                    var inps = document.getElementsByName('total[]');
                    for (var i = 0; i <inps.length; i++) {
                        var inp=inps[i];
                        if(inp.value)
                            sum = parseFloat(sum)+parseFloat(inp.value);
                    }
                    $('#grandtotal').html(sum.toFixed(2));
                    $('#gtot').val(sum.toFixed(2));
                    $('input[name="project_cost"]').val(sum.toFixed(2));
                    $('input[name="project_cost"]').attr('readonly', true);

                    var method = $("#method").val();
            //Discount
            var prical = document.getElementsByName('price[]');
            var quancal = document.getElementsByName('qty[]');
            var disc_txt = '';
            var discal = document.getElementsByName('discount[]');
            for (var i = 0; i < discal.length; i++) {
                var dis=discal[i];
                if(dis.value && dis.value > 0) {
                    var inp=inps[i];
                    var pri = prical[i];
                    var quanc = quancal[i];
                    var totc = (pri.value * quanc.value);
                    var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
                    var mult1 = totc * dec1; // gives the value for subtract from main value
                    disc_txt += ' '+mult1.toFixed(2)+',';
                }
            }
            if(disc_txt != '') {
                disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
                $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
            }
                    if(method == 3) {
                        if(discount_value == 1 || discount_option == 1) {
                            $('#topheading').html('<div class="col-md-2" style="width:20%;">Item</div><div class="col-md-2">Price</div><div class="col-md-1">Quantity</div><div class="col-md-2">Tax</div><div class="col-md-2">Discount %</div><div class="col-md-2">Total</div>');
                        } else {
                            $('#topheading').html('<div class="col-md-2" style="width:20%;">Item</div><div class="col-md-2">Price</div><div class="col-md-1">Quantity</div><div class="col-md-2">Tax</div><div class="col-md-2">Total</div>');
                        }
                        
                        //$('#'+index+' input[name="tax[]"]').val('');
                        for (var index = 0; index <inps.length; index++) {
                            var taxvalue = $('#'+index+' input[name="tax[]"]').val();
                            var tprice = $('#'+index+' input[name="total[]"]').val();
                            //var total = value*price;
                            if(taxvalue) {
                                var taxprice = (tprice * taxvalue) / 100;

                                //$("#stxt").html('<p>Subtotal</p>');
                                $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                                if($('.txt_'+index).length == 0) {
                                    $('#suptotaltxt').append('<p class="txt_'+index+'">Excludes Tax ('+taxvalue+'%)</p>');
                                    $('#suptotal').append('<p class="amt_'+index+'">'+taxprice.toFixed(2)+'</p>');
                                } else {
                                    $('.txt_'+index).html('Excludes Tax ('+taxvalue+'%)');
                                    $('.amt_'+index).html(taxprice.toFixed(2));
                                }
                            }
                        }
                        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                        var taxpr = document.getElementsByName('tax[]');
                        var tosumtpr = 0;
                        for (var i = 0; i <taxpr.length; i++) {
                            var tp=taxpr[i];
                            var inp=inps[i];
                            if(tp.value) {
                                var tottax = (inp.value * tp.value) / 100;
                                tosumtpr = parseFloat(tosumtpr)+parseFloat(tottax)+parseFloat(inp.value);
                            } else {
                                if(tp.value) {
                                    //tosumtpr = parseFloat(tosumtpr)+parseFloat(inp.value);
                                }
                            }
                        }
                        $('#grandtotal').html(tosumtpr.toFixed(2));
                        $('#gtot').val(tosumtpr.toFixed(2));
                    }
                }
            });
        }
    });

    $('.currencyswitcher1').change(function(){
        var optionSelected = $("option:selected", this);
        var currency = $('#currency1').val();
        var method = $('#method').val();
        var discount_value = $('#discount_value').val();
        var discount_option = $('#discount_option').val();
        $("#suptotaltxt").html('');
        $("#suptotal").html('');
        var project = $('#prject_id').val();
        var length = $('#product_index').val();
        var item_type = $('#item_for').val();
        var change_items = $('#change_items').val();
        length = parseInt(length)+parseInt(1);
        if(method == 1) {
            if(project.length == 0 || change_items == 1) {
                var project = $('#rel_id').val();
                var url =  admin_url+'/products/getdealproductfields';
            } else {
                var url =  admin_url+'products/getsalesproductfields';
            }
            console.log(currency);
            console.log(url);
            //var url =  admin_url+'products/getsalesproductfields';
            $.ajax({
                type: "POST",
                url: url,
                data: {project:project,length:length,currency:currency,item_type:item_type},
                success: function(msg){
                    if(discount_value == 1 || discount_option == 1) {
                        $('#topheading').html('<div class="col-md-3">Item</div><div class="col-md-2">Price</div><div class="col-md-2">Quantity</div><div class="col-md-2">Discount %</div><div class="col-md-2">Total</div>');
                    } else {
                        $('#topheading').html('<div class="col-md-3">Item</div><div class="col-md-2">Price</div><div class="col-md-2">Quantity</div><div class="col-md-2">Total</div>');
                    }
                    
                    $('#product_index').val(length);
                    $('#method').val(1);
                    $('.field_product_wrapper').html(msg);
                    var sum = 0;
                    var inps = document.getElementsByName('total[]');
                    for (var i = 0; i <inps.length; i++) {
                        var inp=inps[i];
                        if(inp.value)
                            sum = parseFloat(sum)+parseFloat(inp.value);
                    }
                    //Discount
                    var prical = document.getElementsByName('price[]');
                    var quancal = document.getElementsByName('qty[]');
                    var disc_txt = '';
                    var discal = document.getElementsByName('discount[]');
                    for (var i = 0; i < discal.length; i++) {
                        var dis=discal[i];
                        if(dis.value && dis.value > 0) {
                            var inp=inps[i];
                            var pri = prical[i];
                            var quanc = quancal[i];
                            var totc = (pri.value * quanc.value);
                            var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
                            var mult1 = totc * dec1; // gives the value for subtract from main value
                            disc_txt += ' '+mult1.toFixed(2)+',';
                        }
                    }
                    if(disc_txt != '') {
                        disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
                        $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
                    } else {
                        $("#stxt").html('<p>Subtotal</p>');
                    }
                    $('#stotal').html('<p>'+sum.toFixed(2)+'</p>');
                    $('#grandtotal').html(sum.toFixed(2));
                    $('#gtot').val(sum.toFixed(2));
                    $('input[name="project_cost"]').val(sum.toFixed(2));
                    $('input[name="project_cost"]').attr('readonly', true);
                }
            });
        } 
        if(method == 2) {
            if(project.length == 0 || change_items == 1) {
                var project = $('#rel_id').val();
                var url =  admin_url+'/products/getextaxfields';
            } else {
                var url =  admin_url+'products/getsalesextaxfields';
            }
            $.ajax({
                type: "POST",
                url: url,
                data: {project:project,length:length,currency:currency,item_type:item_type},
                success: function(msg){
                    $('#product_index').val(length);
                    $('#method').val(2);
                    $('.field_product_wrapper').html(msg);
                    var sum = 0;
                    var inps = document.getElementsByName('total[]');
                    for (var i = 0; i <inps.length; i++) {
                        var inp=inps[i];
                        if(inp.value)
                            sum = parseFloat(sum)+parseFloat(inp.value);
                    }
                    $('#grandtotal').html(sum.toFixed(2));
                    $('#gtot').val(sum.toFixed(2));
                    $('input[name="project_cost"]').val(sum.toFixed(2));
                    $('input[name="project_cost"]').attr('readonly', true);

                    var method = $("#method").val();
                    //Discount
                    var prical = document.getElementsByName('price[]');
                    var quancal = document.getElementsByName('qty[]');
                    var disc_txt = '';
                    var discal = document.getElementsByName('discount[]');
                    for (var i = 0; i < discal.length; i++) {
                        var dis=discal[i];
                        if(dis.value && dis.value > 0) {
                            var inp=inps[i];
                            var pri = prical[i];
                            var quanc = quancal[i];
                            var totc = (pri.value * quanc.value);
                            var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
                            var mult1 = totc * dec1; // gives the value for subtract from main value
                            disc_txt += ' '+mult1.toFixed(2)+',';
                        }
                    }
                    if(disc_txt != '') {
                        disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
                        $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
                    } else {
                        $("#stxt").html('<p>Subtotal</p>');
                    }
                    $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');    
                    if(method == 2) {
                        if(discount_value == 1 || discount_option == 1) {
                            $('#topheading').html('<div class="col-md-2" style="width:20%;">Item</div><div class="col-md-2">Price</div><div class="col-md-1">Quantity</div><div class="col-md-2">Tax</div><div class="col-md-2">Discount %</div><div class="col-md-2">Total</div>');
                        } else {
                            $('#topheading').html('<div class="col-md-2" style="width:20%;">Item</div><div class="col-md-2">Price</div><div class="col-md-1">Quantity</div><div class="col-md-2">Tax</div><div class="col-md-2">Total</div>');
                        }
                        
                        //$("#stxt").html('<p>Subtotal</p>');
                        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                        //$('#'+index+' input[name="tax[]"]').val('');
                        for (var index = 0; index <inps.length; index++) {
                            var taxvalue = $('#'+index+' input[name="tax[]"]').val();
                            var tprice = $('#'+index+' input[name="total[]"]').val();
                            //var total = value*price;
                            console.log(taxvalue);
                            if(taxvalue) {
                                var taxprice = (tprice * taxvalue) / 100;
                                
                                //if($('.txt_'+index).length == 0) {
                                    $('#suptotaltxt').append('<p class="txt_'+index+'">Includes Tax ('+taxvalue+'%)</p>');
                                    $('#suptotal').append('<p class="amt_'+index+'">'+taxprice.toFixed(2)+'</p>');
                                // } else {
                                //     $('.txt_'+index).html('Includes Tax ('+taxvalue+'%)');
                                //     $('.amt_'+index).html(taxprice.toFixed(2));
                                // }
                            }
                        }
                        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                        $('#grandtotal').html(sum.toFixed(2));
                        $('#gtot').val(sum.toFixed(2));
                    }
                }
            });
        }
        if(method == 3) {
            if(project.length == 0 || change_items == 1) {
                var project = $('#rel_id').val();
                var url =  admin_url+'/products/getextaxfields';
            } else {
                var url =  admin_url+'products/getsalesextaxfields';
            }
            $.ajax({
                type: "POST",
                url: url,
                data: {project:project,length:length,currency:currency,item_type:item_type},
                success: function(msg){
                    $('#product_index').val(length);
                    $('#method').val(3);
                    $('.field_product_wrapper').html(msg);
                    var sum = 0;
                    var inps = document.getElementsByName('total[]');
                    for (var i = 0; i <inps.length; i++) {
                        var inp=inps[i];
                        if(inp.value)
                            sum = parseFloat(sum)+parseFloat(inp.value);
                    }
                    $('#grandtotal').html(sum.toFixed(2));
                    $('#gtot').val(sum.toFixed(2));
                    $('input[name="project_cost"]').val(sum.toFixed(2));
                    $('input[name="project_cost"]').attr('readonly', true);

                    var method = $("#method").val();
                    //Discount
                    var prical = document.getElementsByName('price[]');
                    var quancal = document.getElementsByName('qty[]');
                    var disc_txt = '';
                    var discal = document.getElementsByName('discount[]');
                    for (var i = 0; i < discal.length; i++) {
                        var dis=discal[i];
                        if(dis.value && dis.value > 0) {
                            var inp=inps[i];
                            var pri = prical[i];
                            var quanc = quancal[i];
                            var totc = (pri.value * quanc.value);
                            var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
                            var mult1 = totc * dec1; // gives the value for subtract from main value
                            disc_txt += ' '+mult1.toFixed(2)+',';
                        }
                    }
                    if(disc_txt != '') {
                        disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
                        $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
                    }
                    if(method == 3) {
                        if(discount_value == 1 || discount_option == 1) {
                            $('#topheading').html('<div class="col-md-2" style="width:20%;">Item</div><div class="col-md-2">Price</div><div class="col-md-1">Quantity</div><div class="col-md-2">Tax</div><div class="col-md-2">Discount %</div><div class="col-md-2">Total</div>');
                        } else {
                            $('#topheading').html('<div class="col-md-2" style="width:20%;">Item</div><div class="col-md-2">Price</div><div class="col-md-1">Quantity</div><div class="col-md-2">Tax</div><div class="col-md-2">Total</div>');
                        }
                        
                        //$('#'+index+' input[name="tax[]"]').val('');
                        for (var index = 0; index <inps.length; index++) {
                            var taxvalue = $('#'+index+' input[name="tax[]"]').val();
                            var tprice = $('#'+index+' input[name="total[]"]').val();
                            //var total = value*price;
                            if(taxvalue) {
                                var taxprice = (tprice * taxvalue) / 100;

                                //$("#stxt").html('<p>Subtotal</p>');
                                $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                                //if($('.txt_'+index).length == 0) {
                                    $('#suptotaltxt').append('<p class="txt_'+index+'">Excludes Tax ('+taxvalue+'%)</p>');
                                    $('#suptotal').append('<p class="amt_'+index+'">'+taxprice.toFixed(2)+'</p>');
                                // } else {
                                //     $('.txt_'+index).html('Excludes Tax ('+taxvalue+'%)');
                                //     $('.amt_'+index).html(taxprice.toFixed(2));
                                // }
                            }
                        }
                        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                        var taxpr = document.getElementsByName('tax[]');
                        var tosumtpr = 0;
                        for (var i = 0; i <taxpr.length; i++) {
                            var tp=taxpr[i];
                            var inp=inps[i];
                            if(tp.value) {
                                var tottax = (inp.value * tp.value) / 100;
                                tosumtpr = parseFloat(tosumtpr)+parseFloat(tottax)+parseFloat(inp.value);
                            } else {
                                if(tp.value) {
                                    //tosumtpr = parseFloat(tosumtpr)+parseFloat(inp.value);
                                }
                            }
                        }
                        $('#grandtotal').html(tosumtpr.toFixed(2));
                        $('#gtot').val(tosumtpr.toFixed(2));
                    }
                }
            });
        }
        return false;
    });

    
    // $(".product").onChange('option:selected').each(function () { 
    //     var id = $(this).closest('div.productdiv').attr('id');
    //     alert(id);
    // });
    

    $(addeditButton).click(function(){
        var currency= $("select[name=\'currency[]\']").map(function() {
            return $(this).val();
        }).toArray();
        //alert(currency);
        if(x < maxField){ 
            x++; //Increment field counter
            var url =  admin_url+'getaddfields';
            
            $.ajax({
                type: "POST",
                url: url,
                data: {currency:currency},
                success: function(msg){
                    $(wrapper).append(msg);
                }
            });
             //Add field html
        }
    });

    $(".addproducts").click(function(){
        $(".showproducts").show();
        $(".removeproducts").show();
        $(".addproducts").hide();
    });

    $(".removeproducts").click(function(){
        var url =  admin_url+'products/removefields';
        var currency = $('#currency').val();
        $.ajax({
            type: "POST",
            url: url,
            data: {currency:currency},
            success: function(msg){
                $(wrapperproduct).html(msg);
                $('#grandtotal').html('0.00');
                $('input[name="project_cost"]').attr('readonly', false);
                $('input[name="project_cost"]').val('');
            }
        });
        $(".showproducts").hide();
        $(".removeproducts").hide();
        $(".addproducts").show();
    });

    $(editvariationButton).click(function(){
        var divid = $(this).parent().parent().attr('id');
        var varid = $("#"+divid+" #varid").val();
        var prodid = $("#product_id").val();
        var currency= $("#"+divid+" select[name=\'variation_currency_"+varid+"[]\']").map(function() {
            return $(this).val();
        }).toArray();
        
        var url =  admin_url+'getVariationfields';
        
        $.ajax({
            type: "POST",
            url: url,
            data: {currency:currency,varid:varid,prodid:prodid},
            success: function(msg){
                $("#"+divid+" .field_variation_wrapper").append(msg);
            }
        });
             //Add field html
    });
    
    
    
    //Once remove button is clicked
    $(wrapper).on('click', '.remove_button', function(e){
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        x--; //Decrement field counter
    });

    $(wrapperVariation).on('click', '.remove_button', function(e){
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        x--; //Decrement field counter
    });

    $(wrapperproduct).on('click', '.removeproduct_button', function(e){
        e.preventDefault();
        var divid = $(this).parent('div').attr('id');
        //alert(divid);
        if(divid) {
            $(this).parent('div').remove(); //Remove field html
        } else {
            var divid = $(this).parent().parent().parent().parent().attr('id');
            //alert(divid);
            $('#'+divid).remove(); //Remove field html
        }
        
        var sum = 0;
        var inps = document.getElementsByName('total[]');
        for (var i = 0; i <inps.length; i++) {
            var inp=inps[i];
            if(inp.value)
                sum = parseFloat(sum)+parseFloat(inp.value);
        }
        $('input[name="project_cost"]').val(sum.toFixed(2));
        $('input[name="project_cost"]').attr('readonly', true);
        $('#grandtotal').html(sum.toFixed(2));
            //Discount
        var prical = document.getElementsByName('price[]');
        var quancal = document.getElementsByName('qty[]');
        var disc_txt = '';
        var discal = document.getElementsByName('discount[]');
        for (var i = 0; i < discal.length; i++) {
            var dis=discal[i];
            if(dis.value && dis.value > 0) {
                var inp=inps[i];
                var pri = prical[i];
                var quanc = quancal[i];
                var totc = (pri.value * quanc.value);
                var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
                var mult1 = totc * dec1; // gives the value for subtract from main value
                disc_txt += ' '+mult1.toFixed(2)+',';
            }
        }
        if(disc_txt != '') {
            disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
            $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
        } else {
            $("#stxt").html('<p>Subtotal</p>');
        }
        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');  

        var method = $("#method").val();
        if(method == 1) {    
            $('#grandtotal').html(sum.toFixed(2));
            $('#gtot').val(sum.toFixed(2));
        }
        
        if(method == 2) {
            $('.txt_'+divid).remove();    
            $('.amt_'+divid).remove();
            $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
            $('#grandtotal').html(sum.toFixed(2));
            $('#gtot').val(sum.toFixed(2));
        }

        if(method == 3) {
            $('.txt_'+divid).remove();    
            $('.amt_'+divid).remove();
            $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
            var taxpr = document.getElementsByName('tax[]');
            var tosumtpr = 0;
            for (var i = 0; i <taxpr.length; i++) {
                var tp=taxpr[i];
                var inp=inps[i];
                if(tp.value) {
                    var tottax = (inp.value * tp.value) / 100;
                    tosumtpr = parseFloat(tosumtpr)+parseFloat(tottax)+parseFloat(inp.value);
                } else {
                    //tosumtpr = parseFloat(tosumtpr)+parseFloat(inp.value);
                }
            }
            $('#grandtotal').html(tosumtpr.toFixed(2));
            $('#gtot').val(tosumtpr.toFixed(2));
        }
    });
});


$('#categoryid_add_group_modal').submit(function(e) {
	$('#cur_cat').hide();
	$('#cur_cat').html('');
    e.preventDefault();
    var form = $(this);
    var category = $('#category').val();
    if (category) {
        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: {category:category},
            dataType: 'json',
            success: function(msg) {
				if(msg.message == 'Category Aleady Exists'){
					$('#cur_cat').html(msg.message);
					$('#cur_cat').show();
				}
				else{
                $('#categoryid').append('<option value="' + msg.id + '" selected="selected">' + msg
                    .company + '</option>');
                $('#categoryid').val(msg.id);
                $('#categoryid_add_group_modal input[name="category"]').val('');
                alert_float('success', msg.message);
                setTimeout(function() {
                    $('#categoryid').selectpicker('refresh');
                    $('.categoryiddiv div.filter-option-inner-inner').html(msg.category)
                }, 500);
                $('#categoryid_add_modal').modal('hide');
				}
            }
        });
    }
});

$('#addprojnote').on('click', function (e) {
    if (tinymce.EditorManager.get('content').getContent() === '') {
        alert('Please enter you notes.');
        return false;
    }
 })

init_ajax_search('product_category', '#categoryid.ajax-search');

function getprice(prod,index) {
    var value = prod.value;
    var url =  admin_url+'products/getpricebyid';
    var currency = $('#currency').val();

    $.ajax({
        type: "POST",
        url: url,
        data: {value:value,currency:currency},
        dataType: 'json',
        success: function(result){
            $('#'+index+' input[name="price[]"]').val(result[0].price);
            $('#'+index+' input[name="total[]"]').val(result[0].price);
            $('#'+index+' input[name="qty[]"]').val(1);
            var sum = 0;
            var inps = document.getElementsByName('total[]');
            for (var i = 0; i <inps.length; i++) {
                var inp=inps[i];
                if(inp.value)
                    sum = parseFloat(sum)+parseFloat(inp.value);
            }
            $('#grandtotal').html(sum.toFixed(2));
            $('#gtot').val(sum.toFixed(2));
            $('input[name="project_cost"]').val(sum.toFixed(2));
            $('input[name="project_cost"]').attr('readonly', true);
        }
    });
}

function getprice1(prod,index) {
    var value = prod.value;
    var url =  admin_url+'invoice_items/getpricebyid';
    var currency = $('#currency').val();

    $.ajax({
        type: "POST",
        url: url,
        data: {value:value,currency:currency},
        dataType: 'json',
        success: function(result){
            $('#'+index+' input[name="price[]"]').val(result[0].price);
            $('#'+index+' input[name="total[]"]').val(result[0].price);
            $('#'+index+' input[name="qty[]"]').val(1);
            var sum = 0;
            var inps = document.getElementsByName('total[]');
            for (var i = 0; i <inps.length; i++) {
                var inp=inps[i];
                if(inp.value)
                    sum = parseFloat(sum)+parseFloat(inp.value);
            }
            $('#grandtotal').html(sum.toFixed(2));
            $('#gtot').val(sum.toFixed(2));
            $('input[name="project_cost"]').val(sum.toFixed(2));
            $('input[name="project_cost"]').attr('readonly', true);
        }
    });
}


function getdealprodprice(prod,index) {
    var value = prod.value;
    var url =  admin_url+'products/getpricebyid';
    var currency = $('#currency').val();   
    if(!currency) {
        var currency = $('#currency1').val();   
    }    
    $.ajax({
        type: "POST",
        url: url,
        data: {value:value,currency:currency},
        dataType: 'json',
        success: function(result){
            $('#variationbtn_'+index).css("pointer-events", "auto");
            $('#variationbtn_'+index).css("cursor", "pointer");
            $('#variation_'+index).remove();
            $('#'+index+' input[name="price[]"]').val(result[0].price);
            $('#'+index+' input[name="total[]"]').val(result[0].price);
            $('#'+index+' input[name="tax[]"]').val(result[0].prodtax);
            $('#'+index+' input[name="qty[]"]').val(1);
            $('#'+index+' input[name="discount[]"]').val(0);
            var sum = 0;
            var inps = document.getElementsByName('total[]');
            for (var i = 0; i <inps.length; i++) {
                var inp=inps[i];
                if(inp.value)
                    sum = parseFloat(sum)+parseFloat(inp.value);
            }
            $('#grandtotal').html(sum.toFixed(2));
            $('#gtot').val(sum.toFixed(2));
            $('input[name="project_cost"]').val(sum.toFixed(2));
            $('input[name="project_cost"]').attr('readonly', true);

            var method = $("#method").val();
           //Discount
            var prical = document.getElementsByName('price[]');
            var quancal = document.getElementsByName('qty[]');
            var disc_txt = '';
            var discal = document.getElementsByName('discount[]');
            for (var i = 0; i < discal.length; i++) {
                var dis=discal[i];
                if(dis.value && dis.value > 0) {
                    var inp=inps[i];
                    var pri = prical[i];
                    var quanc = quancal[i];
                    var totc = (pri.value * quanc.value);
                    var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
                    var mult1 = totc * dec1; // gives the value for subtract from main value
                    disc_txt += ' '+mult1.toFixed(2)+',';
                }
            }
        if(disc_txt != '') {
            disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
            $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
        } else {
            $("#stxt").html('<p>Subtotal</p>');
        }   
        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
            if(method == 2) {
                //$('#'+index+' input[name="tax[]"]').val('');
                var taxvalue = $('#'+index+' input[name="tax[]"]').val();
                var tprice = $('#'+index+' input[name="total[]"]').val();
                //var total = value*price;
                var taxprice = (tprice * taxvalue) / 100;

                //$("#stxt").html('<p>Subtotal</p>');
                $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                if($('.txt_'+index).length == 0) {
                    $('#suptotaltxt').append('<p class="txt_'+index+'">Includes Tax ('+taxvalue+'%)</p>');
                    $('#suptotal').append('<p class="amt_'+index+'">'+taxprice.toFixed(2)+'</p>');
                } else {
                    $('.txt_'+index).html('Includes Tax ('+taxvalue+'%)');
                    $('.amt_'+index).html(taxprice.toFixed(2));
                }

                $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                $('#grandtotal').html(sum.toFixed(2));
                $('#gtot').val(sum.toFixed(2));
            }

            if(method == 3) {
                //$('#'+index+' input[name="tax[]"]').val('');

                var taxvalue = $('#'+index+' input[name="tax[]"]').val();
                var tprice = $('#'+index+' input[name="total[]"]').val();
                //var total = value*price;
                var taxprice = (tprice * taxvalue) / 100;

                //$("#stxt").html('<p>Subtotal</p>');
                $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                if($('.txt_'+index).length == 0) {
                    $('#suptotaltxt').append('<p class="txt_'+index+'">Excludes Tax ('+taxvalue+'%)</p>');
                    $('#suptotal').append('<p class="amt_'+index+'">'+taxprice.toFixed(2)+'</p>');
                } else {
                    $('.txt_'+index).html('Excludes Tax ('+taxvalue+'%)');
                    $('.amt_'+index).html(taxprice.toFixed(2));
                }
                
                $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                var taxpr = document.getElementsByName('tax[]');
                var tosumtpr = 0;
                for (var i = 0; i <taxpr.length; i++) {
                    var tp=taxpr[i];
                    var inp=inps[i];
                    if(tp.value) {
                        var tottax = (inp.value * tp.value) / 100;
                        tosumtpr = parseFloat(tosumtpr)+parseFloat(tottax)+parseFloat(inp.value);
                    } else {
                        //tosumtpr = parseFloat(tosumtpr)+parseFloat(inp.value);
                    }
                }
                $('#grandtotal').html(tosumtpr.toFixed(2));
                $('#gtot').val(tosumtpr.toFixed(2));
            }

        }
    });
}



$('.currency').on('change', function (e) {
    var optionSelected = $("option:selected", this);
    var currency = this.value;
    var product= $("select[name=\'product[]\']").map(function() {
        return $(this).val();
    }).toArray();
    $('.addproduts_btn').css("pointer-events", "none");
    $('.addproduts_btn').css("cursor", "default");
    //var length = Object.keys(product).length
    var length = $('#product_index').val();
    length = parseInt(length)+parseInt(1);
        var url =  admin_url+'/products/getaddproductfields';
        
        $.ajax({
            type: "POST",
            url: url,
            data: {product:product,length:length,currency:currency},
            success: function(msg){
                $('#product_index').val(length);
                $('.field_product_wrapper').html(msg);
                $('.addproduts_btn').css("pointer-events", "auto");
                $('.addproduts_btn').css("cursor", "pointer");
                $('#project_cost .input-group-addon').html('<i class="fa fa-'+currency.toLowerCase()+'"></i>');
                $('input[name="project_cost"]').val('');
            }
        });
});

$('.currency1').on('change', function (e) {
    var optionSelected = $("option:selected", this);
    var currency = this.value;
    var product= $("select[name=\'product[]\']").map(function() {
        return $(this).val();
    }).toArray();
    $('.addproduts_btn').css("pointer-events", "none");
    $('.addproduts_btn').css("cursor", "default");
    //var length = Object.keys(product).length
    var length = $('#product_index').val();
    length = parseInt(length)+parseInt(1);
        var url =  admin_url+'/invoice_items/getaddproductfields';
        
        $.ajax({
            type: "POST",
            url: url,
            data: {product:product,length:length,currency:currency},
            success: function(msg){
                $('#product_index').val(length);
                $('.field_product_wrapper').html(msg);
                $('.addproduts_btn').css("pointer-events", "auto");
                $('.addproduts_btn').css("cursor", "pointer");
                $('#project_cost .input-group-addon').html('<i class="fa fa-'+currency.toLowerCase()+'"></i>');
                $('input[name="project_cost"]').val('');
            }
        });
});

function getvariationprodprice(prod,index) {
    var value = prod.value;
    var currency = $('#currency').val();
    if(value) {
        var url =  admin_url+'products/getvariationpricebyid';
    
                    
            $.ajax({
                type: "POST",
                url: url,
                data: {value:value,currency:currency},
                dataType: 'json',
                success: function(result){
                    
                    $('#'+index+' input[name="price[]"]').val(result[0].variation_price);
                    var dcnt = $('#'+index+' input[name="discount[]"]').val();
                    var quant = $('#'+index+' input[name="qty[]"]').val();
                    var totprice = quant*result[0].variation_price;
                    if(dcnt) {
                        var dec = (dcnt / 100).toFixed(2); //its convert 10 into 0.10
                        var mult = totprice * dec; // gives the value for subtract from main value
                        var total = totprice - mult;
                    } else {
                        var total = totprice;
                    }
                    $('#'+index+' input[name="total[]"]').val(total.toFixed(2));
                    
                    var sum = 0;
                    var inps = document.getElementsByName('total[]');
                    for (var i = 0; i <inps.length; i++) {
                        var inp=inps[i];
                        if(inp.value)
                            sum = parseFloat(sum)+parseFloat(inp.value);
                    }
                    $('#grandtotal').html(sum.toFixed(2));
                    $('#gtot').val(sum.toFixed(2));
                    $('input[name="project_cost"]').val(sum.toFixed(2));
                    $('input[name="project_cost"]').attr('readonly', true);

                    var method = $("#method").val();
                //Discount
                var prical = document.getElementsByName('price[]');
                var quancal = document.getElementsByName('qty[]');
                var disc_txt = '';
                var discal = document.getElementsByName('discount[]');
                for (var i = 0; i < discal.length; i++) {
                    var dis=discal[i];
                    if(dis.value && dis.value > 0) {
                        var inp=inps[i];
                        var pri = prical[i];
                        var quanc = quancal[i];
                        var totc = (pri.value * quanc.value);
                        var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
                        var mult1 = totc * dec1; // gives the value for subtract from main value
                        disc_txt += ' '+mult1.toFixed(2)+',';
                    }
                }
                if(disc_txt != '') {
                    disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
                    $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
                } else {
                    $("#stxt").html('<p>Subtotal</p>');
                }
                $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');    
                    if(method == 2) {
                        //$('#'+index+' input[name="tax[]"]').val('');
                        var taxvalue = $('#'+index+' input[name="tax[]"]').val();
                        var tprice = $('#'+index+' input[name="total[]"]').val();
                        //var total = value*price;
                        var taxprice = (tprice * taxvalue) / 100;

                        //$("#stxt").html('<p>Subtotal</p>');
                        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                        if($('.txt_'+index).length == 0) {
                            $('#suptotaltxt').append('<p class="txt_'+index+'">Includes Tax ('+taxvalue+'%)</p>');
                            $('#suptotal').append('<p class="amt_'+index+'">'+taxprice.toFixed(2)+'</p>');
                        } else {
                            $('.txt_'+index).html('Includes Tax ('+taxvalue+'%)');
                            $('.amt_'+index).html(taxprice.toFixed(2));
                        }

                        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                        $('#grandtotal').html(sum.toFixed(2));
                        $('#gtot').val(sum.toFixed(2));
                    }

                    if(method == 3) {
                        //$('#'+index+' input[name="tax[]"]').val('');

                        var taxvalue = $('#'+index+' input[name="tax[]"]').val();
                        var tprice = $('#'+index+' input[name="total[]"]').val();
                        //var total = value*price;
                        var taxprice = (tprice * taxvalue) / 100;

                        //$("#stxt").html('<p>Subtotal</p>');
                        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                        if($('.txt_'+index).length == 0) {
                            $('#suptotaltxt').append('<p class="txt_'+index+'">Excludes Tax ('+taxvalue+'%)</p>');
                            $('#suptotal').append('<p class="amt_'+index+'">'+taxprice.toFixed(2)+'</p>');
                        } else {
                            $('.txt_'+index).html('Excludes Tax ('+taxvalue+'%)');
                            $('.amt_'+index).html(taxprice.toFixed(2));
                        }
                        
                        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                        var taxpr = document.getElementsByName('tax[]');
                        var tosumtpr = 0;
                        for (var i = 0; i <taxpr.length; i++) {
                            var tp=taxpr[i];
                            var inp=inps[i];
                            if(tp.value) {
                                var tottax = (inp.value * tp.value) / 100;
                                tosumtpr = parseFloat(tosumtpr)+parseFloat(tottax)+parseFloat(inp.value);
                            } else {
                                //tosumtpr = parseFloat(tosumtpr)+parseFloat(inp.value);
                            }
                        }
                        $('#grandtotal').html(tosumtpr.toFixed(2));
                        $('#gtot').val(tosumtpr.toFixed(2));
                    }

                }
            });
    } else {
        var value = $('#'+index+' select[name="product[]"]').val();
        var url =  admin_url+'products/getpricebyid';
        var currency = $('#currency').val();
            $.ajax({
                type: "POST",
                url: url,
                data: {value:value,currency:currency},
                dataType: 'json',
                success: function(result){
                    
                    $('#'+index+' input[name="price[]"]').val(result[0].price);
                    var dcnt = $('#'+index+' input[name="discount[]"]').val();
                    var quant = $('#'+index+' input[name="qty[]"]').val();
                    var totprice = quant*result[0].price;
                   
                    if(dcnt) {
                        var dec = (dcnt / 100).toFixed(2); //its convert 10 into 0.10
                        var mult = totprice * dec; // gives the value for subtract from main value
                        var total = totprice - mult;
                    } else {
                        var total = totprice;
                    }
                    $('#'+index+' input[name="total[]"]').val(total.toFixed(2));
                    
                    var sum = 0;
                    var inps = document.getElementsByName('total[]');
                    for (var i = 0; i <inps.length; i++) {
                        var inp=inps[i];
                        if(inp.value)
                            sum = parseFloat(sum)+parseFloat(inp.value);
                    }
                    $('#grandtotal').html(sum.toFixed(2));
                    $('#gtot').val(sum.toFixed(2));
                    $('input[name="project_cost"]').val(sum.toFixed(2));
                    $('input[name="project_cost"]').attr('readonly', true);

                    var method = $("#method").val();
                //Discount
                var prical = document.getElementsByName('price[]');
                var quancal = document.getElementsByName('qty[]');
                var disc_txt = '';
                var discal = document.getElementsByName('discount[]');
                for (var i = 0; i < discal.length; i++) {
                    var dis=discal[i];
                    if(dis.value && dis.value > 0) {
                        var inp=inps[i];
                        var pri = prical[i];
                        var quanc = quancal[i];
                        var totc = (pri.value * quanc.value);
                        var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
                        var mult1 = totc * dec1; // gives the value for subtract from main value
                        disc_txt += ' '+mult1.toFixed(2)+',';
                    }
                }
                if(disc_txt != '') {
                    disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
                    $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
                } else {
                    $("#stxt").html('<p>Subtotal</p>');
                }
                $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');    
                    if(method == 2) {
                        //$('#'+index+' input[name="tax[]"]').val('');
                        var taxvalue = $('#'+index+' input[name="tax[]"]').val();
                        var tprice = $('#'+index+' input[name="total[]"]').val();
                        //var total = value*price;
                        var taxprice = (tprice * taxvalue) / 100;

                        //$("#stxt").html('<p>Subtotal</p>');
                        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                        if($('.txt_'+index).length == 0) {
                            $('#suptotaltxt').append('<p class="txt_'+index+'">Includes Tax ('+taxvalue+'%)</p>');
                            $('#suptotal').append('<p class="amt_'+index+'">'+taxprice.toFixed(2)+'</p>');
                        } else {
                            $('.txt_'+index).html('Includes Tax ('+taxvalue+'%)');
                            $('.amt_'+index).html(taxprice.toFixed(2));
                        }

                        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                        $('#grandtotal').html(sum.toFixed(2));
                        $('#gtot').val(sum.toFixed(2));
                    }

                    if(method == 3) {
                        //$('#'+index+' input[name="tax[]"]').val('');

                        var taxvalue = $('#'+index+' input[name="tax[]"]').val();
                        var tprice = $('#'+index+' input[name="total[]"]').val();
                        //var total = value*price;
                        var taxprice = (tprice * taxvalue) / 100;

                        //$("#stxt").html('<p>Subtotal</p>');
                        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                        if($('.txt_'+index).length == 0) {
                            $('#suptotaltxt').append('<p class="txt_'+index+'">Excludes Tax ('+taxvalue+'%)</p>');
                            $('#suptotal').append('<p class="amt_'+index+'">'+taxprice.toFixed(2)+'</p>');
                        } else {
                            $('.txt_'+index).html('Excludes Tax ('+taxvalue+'%)');
                            $('.amt_'+index).html(taxprice.toFixed(2));
                        }
                        
                        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                        var taxpr = document.getElementsByName('tax[]');
                        var tosumtpr = 0;
                        for (var i = 0; i <taxpr.length; i++) {
                            var tp=taxpr[i];
                            var inp=inps[i];
                            if(tp.value) {
                                var tottax = (inp.value * tp.value) / 100;
                                tosumtpr = parseFloat(tosumtpr)+parseFloat(tottax)+parseFloat(inp.value);
                            } else {
                                //tosumtpr = parseFloat(tosumtpr)+parseFloat(inp.value);
                            }
                        }
                        $('#grandtotal').html(tosumtpr.toFixed(2));
                        $('#gtot').val(tosumtpr.toFixed(2));
                    }

                }
            });
    }
}

function price_update(prod,index) {
    var price = prod.value;
    var value = $('#'+index+' input[name="qty[]"]').val();

    var discount = $('#'+index+' input[name="discount[]"]').val();
    if(discount && discount > 0) {
        var totprice = value*price;
        var dec = (discount / 100).toFixed(2); //its convert 10 into 0.10
        var mult = totprice * dec; // gives the value for subtract from main value
        var total = totprice - mult;
    } else {
        var total = value*price;
    }

    $('#'+index+' input[name="total[]"]').val(total.toFixed(2));
    var sum = 0;
    var inps = document.getElementsByName('total[]');
    for (var i = 0; i <inps.length; i++) {
        var inp=inps[i];
        if(inp.value)
            sum = parseFloat(sum)+parseFloat(inp.value);
    }
    $('#grandtotal').html(sum.toFixed(2));
    $('#gtot').val(sum.toFixed(2));
    $('input[name="project_cost"]').val(sum.toFixed(2));
    $('input[name="project_cost"]').attr('readonly', true);

    //Discount
    var prical = document.getElementsByName('price[]');
    var quancal = document.getElementsByName('qty[]');
    var disc_txt = '';
    var discal = document.getElementsByName('discount[]');
    for (var i = 0; i < discal.length; i++) {
        var dis=discal[i];
        if(dis.value && dis.value > 0) {
            var inp=inps[i];
            var pri = prical[i];
            var quanc = quancal[i];
            var totc = (pri.value * quanc.value);
            var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
            var mult1 = totc * dec1; // gives the value for subtract from main value
            disc_txt += ' '+mult1.toFixed(2)+',';
        }
    }
    if(disc_txt != '') {
        disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
        $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
    } else {
        $("#stxt").html('<p>Subtotal</p>');
    }   
    $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');

    var method = $("#method").val();
    if(method == 2) {
        var taxvalue = $('#'+index+' input[name="tax[]"]').val();
        var tprice = $('#'+index+' input[name="total[]"]').val();
        //var total = value*price;
        var taxprice = (tprice * taxvalue) / 100;

        $('.txt_'+index).html('Includes Tax ('+taxvalue+'%)');
        $('.amt_'+index).html(taxprice.toFixed(2));
        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
        $('#grandtotal').html(sum.toFixed(2));
        $('#gtot').val(sum.toFixed(2));
    }

    if(method == 3) {
        var taxvalue = $('#'+index+' input[name="tax[]"]').val();
        var tprice = $('#'+index+' input[name="total[]"]').val();
        //var total = value*price;
        var taxprice = (tprice * taxvalue) / 100;

        $('.txt_'+index).html('Excludes Tax ('+taxvalue+'%)');
        $('.amt_'+index).html(taxprice.toFixed(2));
        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
        var taxpr = document.getElementsByName('tax[]');
        var tosumtpr = 0;
        for (var i = 0; i <taxpr.length; i++) {
            var tp=taxpr[i];
            var inp=inps[i];
            if(tp.value) {
                var tottax = (inp.value * tp.value) / 100;
                tosumtpr = parseFloat(tosumtpr)+parseFloat(tottax)+parseFloat(inp.value);
            } else {
                //tosumtpr = parseFloat(tosumtpr)+parseFloat(inp.value);
            }
        }
        $('#grandtotal').html(tosumtpr.toFixed(2));
        $('#gtot').val(tosumtpr.toFixed(2));
    }
}

function qty_total(prod,index) {
    var value = prod.value;
    var price = $('#'+index+' input[name="price[]"]').val();

    var discount = $('#'+index+' input[name="discount[]"]').val();
    if(discount && discount > 0) {
        var totprice = value*price;
        var dec = (discount / 100).toFixed(2); //its convert 10 into 0.10
        var mult = totprice * dec; // gives the value for subtract from main value
        var total = totprice - mult;
    } else {
        var total = value*price;
    }

    $('#'+index+' input[name="total[]"]').val(total.toFixed(2));
    var sum = 0;
    var inps = document.getElementsByName('total[]');
    for (var i = 0; i <inps.length; i++) {
        var inp=inps[i];
        if(inp.value)
            sum = parseFloat(sum)+parseFloat(inp.value);
    }
    $('#grandtotal').html(sum.toFixed(2));
    $('#gtot').val(sum.toFixed(2));
    $('input[name="project_cost"]').val(sum.toFixed(2));
    $('input[name="project_cost"]').attr('readonly', true);

    //Discount
    var prical = document.getElementsByName('price[]');
    var quancal = document.getElementsByName('qty[]');
    var disc_txt = '';
    var discal = document.getElementsByName('discount[]');
    for (var i = 0; i < discal.length; i++) {
        var dis=discal[i];
        if(dis.value && dis.value > 0) {
            var inp=inps[i];
            var pri = prical[i];
            var quanc = quancal[i];
            var totc = (pri.value * quanc.value);
            var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
            var mult1 = totc * dec1; // gives the value for subtract from main value
            disc_txt += ' '+mult1.toFixed(2)+',';
        }
    }
    if(disc_txt != '') {
        disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
        $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
    } else {
        $("#stxt").html('<p>Subtotal</p>');
    }   
    $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');

    var method = $("#method").val();
    if(method == 2) {
        var taxvalue = $('#'+index+' input[name="tax[]"]').val();
        var tprice = $('#'+index+' input[name="total[]"]').val();
        //var total = value*price;
        var taxprice = (tprice * taxvalue) / 100;

        $('.txt_'+index).html('Includes Tax ('+taxvalue+'%)');
        $('.amt_'+index).html(taxprice.toFixed(2));
        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
        $('#grandtotal').html(sum.toFixed(2));
        $('#gtot').val(sum.toFixed(2));
    }

    if(method == 3) {
        var taxvalue = $('#'+index+' input[name="tax[]"]').val();
        var tprice = $('#'+index+' input[name="total[]"]').val();
        //var total = value*price;
        var taxprice = (tprice * taxvalue) / 100;

        $('.txt_'+index).html('Excludes Tax ('+taxvalue+'%)');
        $('.amt_'+index).html(taxprice.toFixed(2));
        $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
        var taxpr = document.getElementsByName('tax[]');
        var tosumtpr = 0;
        for (var i = 0; i <taxpr.length; i++) {
            var tp=taxpr[i];
            var inp=inps[i];
            if(tp.value) {
                var tottax = (inp.value * tp.value) / 100;
                tosumtpr = parseFloat(tosumtpr)+parseFloat(tottax)+parseFloat(inp.value);
            } else {
                //tosumtpr = parseFloat(tosumtpr)+parseFloat(inp.value);
            }
        }
        $('#grandtotal').html(tosumtpr.toFixed(2));
        $('#gtot').val(tosumtpr.toFixed(2));
    }
}

function discount_total(prod,index) {
    var value = prod.value;
    var price = $('#'+index+' input[name="price[]"]').val();
    var qty = $('#'+index+' input[name="qty[]"]').val();

    var totprice = qty*price;
    var dec = (value / 100).toFixed(2); //its convert 10 into 0.10
    var mult = totprice * dec; // gives the value for subtract from main value
    var total = totprice - mult;
    $('#'+index+' input[name="total[]"]').val(total.toFixed(2));
    var sum = 0;
    var inps = document.getElementsByName('total[]');
    for (var i = 0; i <inps.length; i++) {
        var inp=inps[i];
        if(inp.value)
            sum = parseFloat(sum)+parseFloat(inp.value);
    }
    $('#grandtotal').html(sum.toFixed(2));
    $('#gtot').val(sum.toFixed(2));
    $('input[name="project_cost"]').val(sum.toFixed(2));
    $('input[name="project_cost"]').attr('readonly', true);

    //Discount
    var prical = document.getElementsByName('price[]');
    var quancal = document.getElementsByName('qty[]');
    var disc_txt = '';
    var discal = document.getElementsByName('discount[]');
    for (var i = 0; i < discal.length; i++) {
        var dis=discal[i];
        if(dis.value && dis.value > 0) {
            var inp=inps[i];
            var pri = prical[i];
            var quanc = quancal[i];
            var totc = (pri.value * quanc.value);
            var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
            var mult1 = totc * dec1; // gives the value for subtract from main value
            disc_txt += ' '+mult1.toFixed(2)+',';
        }
    }
    if(disc_txt != '') {
        disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
        $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
    }

    var method = $("#method").val();
    $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
    if(method == 2) {
        var taxvalue = $('#'+index+' input[name="tax[]"]').val();
        var tprice = $('#'+index+' input[name="total[]"]').val();
        //var total = value*price;
        var taxprice = (tprice * taxvalue) / 100;

        $('.txt_'+index).html('Includes Tax ('+taxvalue+'%)');
        $('.amt_'+index).html(taxprice.toFixed(2));
        
        $('#grandtotal').html(sum.toFixed(2));
        $('#gtot').val(sum.toFixed(2));
    }

    if(method == 3) {
        var taxvalue = $('#'+index+' input[name="tax[]"]').val();
        var tprice = $('#'+index+' input[name="total[]"]').val();
        //var total = value*price;
        var taxprice = (tprice * taxvalue) / 100;

        $('.txt_'+index).html('Excludes Tax ('+taxvalue+'%)');
        $('.amt_'+index).html(taxprice.toFixed(2));
        
        var taxpr = document.getElementsByName('tax[]');
        var tosumtpr = 0;
        for (var i = 0; i <taxpr.length; i++) {
            var tp=taxpr[i];
            var inp=inps[i];
            if(tp.value) {
                var tottax = (inp.value * tp.value) / 100;
                tosumtpr = parseFloat(tosumtpr)+parseFloat(tottax)+parseFloat(inp.value);
            } else {
                //tosumtpr = parseFloat(tosumtpr)+parseFloat(inp.value);
            }
        }
        $('#grandtotal').html(tosumtpr.toFixed(2));
        $('#gtot').val(tosumtpr.toFixed(2));
    }
}

function gotoprod(index) {
    var product = $('#'+index+' select[name="product[]"]').val();
    if(product) {
        window.location.href = admin_url+'products/product/'+product;
    } else {
        alert('Please Select Product.');
    }
}

function selectVariation(index) {
    var product = $('#'+index+' select[name="product[]"]').val();
    var method = $('#method').val();
    var currency = $('#currency').val();
    if(product) {
        var value = product;
        var url =  admin_url+'/products/getVariationfield';
        $('#variationbtn_'+index).css("pointer-events", "none");
        $('#variationbtn_'+index).css("cursor", "default");
        $.ajax({
            type: "POST",
            url: url,
            data: {value:value,index:index,method:method,currency:currency},
            success: function(result){
                if(result) {
                    $('#'+index).append(result);
                } else {
                    $('#variationbtn_'+index).css("pointer-events", "auto");
                    $('#variationbtn_'+index).css("cursor", "pointer");
                    alert('No Variations Found.');
                }
                
            }
        });
    } else {
        alert('Please Select Product.');
    }
}

// $(".field_product_wrapper [type='number']").keypress(function (evt) {
//     evt = (evt) ? evt : window.event;
//     let charCode = (evt.which) ? evt.which : evt.keyCode;
//     if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
//       evt.preventDefault();
//     } else {
//       return true;
//     }
// });


function tax_total(prod,index) {
    var value = prod.value;
    var price = $('#'+index+' input[name="total[]"]').val();
    //var total = value*price;
    var taxprice = (price * value) / 100;
    var method = $('#method').val();
    var sum = 0;
    var inps = document.getElementsByName('total[]');
    for (var i = 0; i <inps.length; i++) {
        var inp=inps[i];
        if(inp.value)
            sum = parseFloat(sum)+parseFloat(inp.value);
    }

    //Discount
    var prical = document.getElementsByName('price[]');
    var quancal = document.getElementsByName('qty[]');
    var disc_txt = '';
    var discal = document.getElementsByName('discount[]');
    for (var i = 0; i < discal.length; i++) {
        var dis=discal[i];
        if(dis.value && dis.value > 0) {
            var inp=inps[i];
            var pri = prical[i];
            var quanc = quancal[i];
            var totc = (pri.value * quanc.value);
            var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
            var mult1 = totc * dec1; // gives the value for subtract from main value
            disc_txt += ' '+mult1.toFixed(2)+',';
        }
    }
    if(disc_txt != '') {
        disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
        $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
    } else {
        $("#stxt").html('<p>Subtotal</p>');
    }
    $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');    
    if(method == 2) {
        //$("#stxt").html('<p>Subtotal</p>');
        //$("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
        if($('.txt_'+index).length == 0) {
            $('#suptotaltxt').append('<p class="txt_'+index+'">Includes Tax ('+value+'%)</p>');
            $('#suptotal').append('<p class="amt_'+index+'">'+taxprice.toFixed(2)+'</p>');
        } else {
            $('.txt_'+index).html('Includes Tax ('+value+'%)');
            $('.amt_'+index).html(taxprice.toFixed(2));
        }
    }
    if(method == 3) {
        //$("#stxt").html('<p>Subtotal</p>');
        //$("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
        var taxpr = document.getElementsByName('tax[]');
        var tosumtpr = 0;
        for (var i = 0; i <taxpr.length; i++) {
            var tp=taxpr[i];
            var inp=inps[i];
            
            if(tp.value) {
                var tottax = (parseFloat(inps[i].value) * parseFloat(tp.value)) / 100;
                tosumtpr = parseFloat(tosumtpr)+parseFloat(tottax)+parseFloat(inps[i].value);
            } else {
               // tosumtpr = parseFloat(tosumtpr)+parseFloat(inps[i].value);
            }
        }
        // $(".txt_"+index).remove();
        // $(".amt_"+index).remove();
        if($('.txt_'+index).length == 0) {
            $('#suptotaltxt').append('<p class="txt_'+index+'">Excludes Tax ('+value+'%)</p>');
            $('#suptotal').append('<p class="amt_'+index+'">'+taxprice.toFixed(2)+'</p>');
        } else {
            $('.txt_'+index).html('Excludes Tax ('+value+'%)');
            $('.amt_'+index).html(taxprice.toFixed(2));
        }
        //alert(tosumtpr);
        $('#grandtotal').html(tosumtpr.toFixed(2));
        $('#gtot').val(tosumtpr.toFixed(2));
    }
}


</script>

 <?php
    $contents = ob_get_contents();
    ob_end_clean();

    return $contents;
}

function is_custom_fields_smart_transfer_enabled()
{
    if (!defined('CUSTOM_FIELDS_SMART_TRANSFER')) {
        return true;
    }

    if (defined('CUSTOM_FIELDS_SMART_TRANSFER') && CUSTOM_FIELDS_SMART_TRANSFER) {
        return true;
    }

    return false;
}

function custom_field_location_icon_link($latlng){
    return '<a class="mx-auto" target="_blank" href = "https://maps.google.com/?q='.$latlng.'" style="color:#DD4B3E"><i class="fa fa-map-marker" style="font-size:32px;" aria-hidden="true"></i></a>';
}

/**
 * Store the custom field location details
 */
global $location_js_data;
function set_custom_field_location_js_data($lat,$lng,$fieldName)
{
    global $location_js_data;
    if(!is_array($location_js_data))
        $location_js_data =[];

    $id =count($location_js_data);
    $location_js_data[] =array("id"=>$id,"lat"=>$lat,"lng"=>$lng,"fieldName"=>$fieldName);
}

/**
 * return the custom field location details
 */
function get_custom_field_location_js_data()
{ 
    global $location_js_data;
    ob_start();
?>
<script>
            <?php
            if(is_array($location_js_data)){
            foreach($location_js_data as $location){
            ?>
            window.loadGoogleMaps = function(){
            if(typeof cgmapLoaded == 'undefined' || cgmapLoaded==true){cgmapinitialize(); return;}
            var script_tag = document.createElement('script');
            script_tag.setAttribute("type","text/javascript");
            script_tag.setAttribute("src","https://maps.google.com/maps/api/js?libraries=places&key=AIzaSyAER5hPywUW-5DRlyKJZEfsqgZlaqytxoU&callback=cgmapinitialize");
            (document.getElementsByTagName("body")[0] || document.documentElement).appendChild(script_tag);
            }
            function cgmapinitialize(){
                cgmapLoaded =true;
                var cgampOptions ={
                    center: {lat: parseFloat(<?php echo $location['lat']; ?>), lng: parseFloat(<?php echo $location['lng']; ?>)},
                    zoom: 13
                };
                new Cgmap(
                    new google.maps.Map(document.getElementById('cgmap'),cgampOptions),
                    <?php echo $location['lat']; ?>,
                    <?php echo $location['lng']; ?>,
                    '<?php echo $location['fieldName']; ?>'
                );
            };
            loadGoogleMaps();
            <?php
            break;}}
            ?>
</script>
<?php 
    $scripts = ob_get_contents();
    ob_end_clean();
    return '<style>
    .cgmapsearchInput{
        border: 2px solid #444;
        padding: 10px;
        border-radius: 5px;
        top: 10px !important;
    }
    .pac-container{
        z-index:10000;
    }
    </style>'.$scripts;
}