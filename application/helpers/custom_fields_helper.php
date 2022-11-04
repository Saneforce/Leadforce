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

$(document).ready(function(){
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
                            $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Tax</div><div class="">Discount %</div><div class="">Total</div>');
                        } else {
                            $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Tax</div><div class="">Total</div>');
                        }
                        addFooterEmptyCell();
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
                            $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Tax</div><div class="">Discount %</div><div class="">Total</div>');
                        } else {
                            $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Tax</div><div class="">Total</div>');
                        }
                        addFooterEmptyCell();
                        
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
                        $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Discount %</div><div class="">Total</div>');
                    } else {
                        $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Total</div>');
                    }
                    addFooterEmptyCell();
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
                            $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Tax</div><div class="">Discount %</div><div class="">Total</div>');
                        } else {
                            $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Tax</div><div class="">Total</div>');
                        }
                        addFooterEmptyCell();
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
                            $('#topheading').html('<div class="" >Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Tax</div><div class="">Discount %</div><div class="">Total</div>');
                        } else {
                            $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Tax</div><div class="">Total</div>');
                        }
                        addFooterEmptyCell();
                        
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
                        $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Discount %</div><div class="">Total</div>');
                    } else {
                        $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Total</div>');
                    }
                    addFooterEmptyCell();
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
                        $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Discount %</div><div class="">Total</div>');
                    } else {
                        $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Total</div>');
                    }
                    addFooterEmptyCell();
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
                            $('#topheading').html('<div class="" >Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Tax</div><div class="">Discount %</div><div class="col-md-2">Total</div>');
                        } else {
                            $('#topheading').html('<div class="" >Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Tax</div><div class="">Total</div>');
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
                            $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Tax</div><div class="">Discount %</div><div class="">Total</div>');
                        } else {
                            $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Tax</div><div class="">Total</div>');
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
                        $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Discount %</div><div class="">Total</div>');
                    } else {
                        $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Total</div>');
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
                            $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Tax</div><div class="">Discount %</div><div class="">Total</div>');
                        } else {
                            $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Tax</div><div class="">Total</div>');
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
                            $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Tax</div><div class="">Discount %</div><div class="">Total</div>');
                        } else {
                            $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Tax</div><div class="">Total</div>');
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

function get_particulars_ordered_details(prod,index){
    
    var id =prod.value;
    if(id ==''){
        $('.item_ordered_column').val('');
    }else{
        $.ajax({
            type: "POST",
            url: admin_url+'products/get_particulars_ordered_details/'+id,
            dataType: 'json',
            success: function(result){
                $.each(result.data, function (key, val) {
                    console.log('#ordered_column_'+key+'_'+index);
                    $('#ordered_column_'+key+'_'+index).val(val);
                });
            
            }
        });
    }
    
    
}
function getdealprodprice(prod,index) {
    get_particulars_ordered_details(prod,index);
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

function get_particulars_item_ordered_headers()
{
    $CI = &get_instance();
    $CI->load->model('invoice_items_model');
    $table_data_temp = $CI->invoice_items_model->get_all_table_fields();
    $particulars_items_list_column_order = (array)json_decode(get_option('particulars_items_list_column'));
    if($particulars_items_list_column_order){
        $html ='';
        foreach($particulars_items_list_column_order as $ckey => $cval){
         $html .= '<div class="">'._l($table_data_temp[$ckey]['ll']).'</div>';
        }
        return $html;
    }
}

function get_particulars_item_ordered_inputs($id=0,$product_id=0)
{
    $CI = &get_instance();
    $CI->load->model('invoice_items_model');
    $table_data_temp = $CI->invoice_items_model->get_all_table_fields();
    if($product_id >0){
        $item_details =$CI->invoice_items_model->get_particulars_ordered_details($product_id);
    }
    
    $html ='';
    $particulars_items_list_column_order = (array)json_decode(get_option('particulars_items_list_column'));
    if($particulars_items_list_column_order){
        foreach($particulars_items_list_column_order as $ckey => $cval){
            $html .='<div class="text-muted"><input value="'.(isset($item_details->$ckey)?$item_details->$ckey:'').'" id="ordered_column_'.$ckey.'_'.$id.'" disabled class="form-control item_ordered_column" type="text" placeholder="'._l($table_data_temp[$ckey]['ll']).'"></div>';
        }
    }
    return $html;
}

function get_particular_item_headers($method,$discount_option,$discount_value)
{
    ob_start();
    if($method ==1){ ?>
        <div class="">Item</div>
        <?php echo get_particulars_item_ordered_headers() ?>
        <div class="">Price</div>
        <div class="">Quantity</div>
        <?php if ($discount_value == 1 || $discount_option == 1) { ?>
          <div class="">Discount %</div>
        <?php } ?>
        <div class="">Total</div>
    <?php }elseif($method ==2 || $method ==3){ ?>
        <div class="">Item</div>
        <?php echo get_particulars_item_ordered_headers() ?>
        <div class="">Price</div>
        <div class="">Quantity</div>
        <div class="">Tax</div>
        <?php if ($discount_value == 1 || $discount_option == 1) { ?>
          <div class="">Discount %</div>
        <?php } ?>
        <div class="">Total</div>
    <?php }else{ ?>
        <div class="">Item</div>
        <?php echo get_particulars_item_ordered_headers() ?>
        <div class="">Price</div>
        <div class="">Quantity</div>
        <?php if ($discount_value == 1 || $discount_option == 1) { ?>
          <div class="">Discount %</div>
        <?php } ?>
        <div class="">Total</div>
    <?php }

    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}