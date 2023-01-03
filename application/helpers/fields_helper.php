<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * For more readable code created this function to render only yes or not values for settings
 * @param  string $option_value option from database to compare
 * @param  string $label        input label
 * @param  string $tooltip      tooltip
 */
function render_yes_no_option($option_value, $label, $tooltip = '', $replace_yes_text = '', $replace_no_text = '', $replace_1 = '', $replace_0 = '')
{
    ob_start(); ?>
    <div class="form-group">
        <label for="<?php echo $option_value; ?>" class="control-label clearfix">
            <?php echo($tooltip != '' ? '<i class="fa fa-question-circle" data-toggle="tooltip" data-title="' . _l($tooltip, '', false) . '"></i> ': '') . _l($label, '', false); ?>
        </label>
        <div class="radio radio-primary radio-inline">
            <input type="radio" id="y_opt_1_<?php echo $label; ?>" name="settings[<?php echo $option_value; ?>]" value="<?php echo $replace_1 == '' ? 1 : $replace_1; ?>" <?php if (get_option($option_value) == ($replace_1 == '' ? '1' : $replace_1)) {
        echo 'checked';
    } ?>>
            <label for="y_opt_1_<?php echo $label; ?>">
                <?php echo $replace_yes_text == '' ? _l('settings_yes') : $replace_yes_text; ?>
            </label>
        </div>
        <div class="radio radio-primary radio-inline">
                <input type="radio" id="y_opt_2_<?php echo $label; ?>" name="settings[<?php echo $option_value; ?>]" value="<?php echo $replace_0 == '' ? 0 : $replace_0; ?>" <?php if (get_option($option_value) == ($replace_0 == '' ? '0' : $replace_0)) {
        echo 'checked';
    } ?>>
                <label for="y_opt_2_<?php echo $label; ?>">
                    <?php echo $replace_no_text == '' ? _l('settings_no') : $replace_no_text; ?>
                </label>
        </div>
    </div>
    <?php
    $settings = ob_get_contents();
    ob_end_clean();
    echo $settings;
}

/**
 * Function that renders input for admin area based on passed arguments
 * @param  string $name             input name
 * @param  string $label            label name
 * @param  string $value            default value
 * @param  string $type             input type eq text,number
 * @param  array  $input_attrs      attributes on <input
 * @param  array  $form_group_attr  <div class="form-group"> html attributes
 * @param  string $form_group_class additional form group class
 * @param  string $input_class      additional class on input
 * @return string
 */
function render_input($name, $label = '', $value = '', $type = 'text', $input_attrs = [], $form_group_attr = [], $form_group_class = '', $input_class = '')
{
	$CI = & get_instance();
	$uri1 = $CI->uri->segment(2);
	$uri2 = $CI->uri->segment(3);
    $input            = '';
    $_form_group_attr = '';
    $_input_attrs     = '';
	$cur_req = 1;
    foreach ($input_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
		if ($key == 'required') {
			$cur_req = 2;
		}
        $_input_attrs .= $key . '=' . '"' . $val . '" ';
    }

    $_input_attrs = rtrim($_input_attrs);

    $form_group_attr['app-field-wrapper'] = $name;
	
    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
		
        $_form_group_attr .= $key . '=' . '"' . $val . '" ';
    }

    $_form_group_attr = rtrim($_form_group_attr);

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }
    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }
    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . ' id="ch_'.$name.'">';
    if ($label != '') {
		if($cur_req==1){
			if($uri1== 'projects' && $uri2== 'project' && $label=='project_name'){
				$fields3 = get_option('deal_important_msg');
				$msg_name = '';
				if(!empty($fields3) && $fields3 != 'null'){
					$important_messages = json_decode($fields3);
					$msg_name = $important_messages->name;
				}
				if(!empty($msg_name)){
					$input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '<span style="color: #d2be19;margin-left: 5px;" title="'. $msg_name.'" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span></label>';
				}
				else{
					$input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
				}
			}
			else if($uri1== 'projects' && $uri2== 'project' && $label=='project_total_cost'){
				$fields3 = get_option('deal_important_msg');
				$msg_name = '';
				if(!empty($fields3) && $fields3 != 'null'){
					$important_messages = json_decode($fields3);
					$msg_name = $important_messages->project_cost;
				}
				if(!empty($msg_name)){
					$input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '<span style="color: #d2be19;margin-left: 5px;" title="'. $msg_name.'" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span></label>';
				}else{
					$input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
				}
			}
			else{
				if($uri1== 'projects' && $uri2== 'project' && $label=='project_total_cost'){
					$fields3 = get_option('deal_important_msg');
					$msg_name = '';
					if(!empty($fields3) && $fields3 != 'null'){
						$important_messages = json_decode($fields3);
						$msg_name = $important_messages->project_cost;
					}
					if(!empty($msg_name)){
						$input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '<span style="color: #d2be19;margin-left: 5px;" title="'.$msg_name.'" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span></label>';
					}else{
						$input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
					}
				}
				else{
					$input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
				}
			}
		}else{
			if($label !="connect_email_addr"){
				$input .= '<label for="' . $name . '" class="control-label" > <small class="req text-danger">* </small>' . _l($label, '', false) . '</label>';
			}
			else{
				$input .= '<label for="' . $name . '" class="control-label" style="color:black !important"> <small class="req text-danger">* </small>' . _l($label, '', false) . '</label>';
			}
			
		}
    }
    if($label == 'project_total_cost') {
        
        //$curr = $CI->projects_model->get_currency();
        //pre($currency);
            $input .= '<div class="input-group">';
            $input .= '<div class="input-group-addon">
            <i class="fa fa-'.strtolower($form_group_attr['currency']).'"></i>
        </div>';
       $req_auto = 'new-'.$type;
        $input .= '<input type="' . $type . '" id="' . $name . '" name="' . $name . '" class="form-control' . $input_class . '" ' . $_input_attrs . ' value="' . set_value($name, $value) . '" autocomplete="'.$req_auto.'">';
       
        $input .= '</div>';
        $input .= '</div>';
    } else {
		$req_auto = 'new-'.$type;
        $input .= '<input type="' . $type . '" id="' . $name . '" name="' . $name . '" class="form-control' . $input_class . '" ' . $_input_attrs . ' value="' . set_value($name, $value) . '" autocomplete="'.$req_auto.'">';
        $input .= '</div>';
    }
    

    return $input;
}
/**
 * Render color picker input
 * @param  string $name        input name
 * @param  string $label       field name
 * @param  string $value       default value
 * @param  array  $input_attrs <input sttributes
 * @return string
 */
function render_color_picker($name, $label = '', $value = '', $input_attrs = [])
{
    $_input_attrs = '';
	$cur_req = 1;
    foreach ($input_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
		if ($key == 'required') {
			$cur_req = 2;
		}
        $_input_attrs .= $key . '=' . '"' . $val . '"';
    }

    $picker = '';
    $picker .= '<div class="form-group" app-field-wrapper="' . $name . '">';
	if ($cur_req == 1) {
		$picker .= '<label for="' . $name . '" class="control-label">' . $label . '</label>';
	}else{
		$picker .= '<label for="' . $name . '" class="control-label"> <small class="req text-danger">* </small>' . $label . '</label>';
	}
    $picker .= '<div class="input-group mbot15 colorpicker-input">
    <input type="text" value="' . set_value($name, $value) . '" name="' . $name . '" id="' . $name . '" class="form-control" ' . $_input_attrs . ' />
    <span class="input-group-addon"><i></i></span>
</div>';
    $picker .= '</div>';

    return $picker;
}
function render_time_picker($name, $label = '', $value = '', $input_attrs = [], $form_group_attr = [], $form_group_class = '', $input_class = '')
{
    $input            = '';
    $_form_group_attr = '';
    $_input_attrs     = '';
	$cur_req = 1;
    foreach ($input_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
		if ($key == 'required') {
			$cur_req = 2;
		}
        $_input_attrs .= $key . '=' . '"' . $val . '" ';
    }

    $_input_attrs = rtrim($_input_attrs);

    $form_group_attr['app-field-wrapper'] = $name;

    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '" ';
    }

    $_form_group_attr = rtrim($_form_group_attr);

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }
    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }
    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
		if ($cur_req == 1) {
			$input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
		}else{
			$input .= '<label for="' . $name . '" class="control-label"> <small class="req text-danger">* </small>' . _l($label, '', false) . '</label>';
		}
    }
    $input .= '<div class="input-group timepicker3">';
    $input .= '<input type="text" id="' . $name . '" name="' . $name . '" class="form-control timepicker ' . $input_class . '" ' . $_input_attrs . ' value="' . set_value($name, $value) . '" autocomplete="off">';
    $input .= '<div class="input-group-addon">
    <i class="fa fa-clock-o clock-icon"></i>
</div>';
    $input .= '</div>';
    $input .= '</div>';

    return $input;
}
function render_date_range_picker($name, $label = '', $value = '', $input_attrs = [], $form_group_attr = [], $form_group_class = '', $input_class = '')
{
    $input            = '';
    $_form_group_attr = '';
    $_input_attrs     = '';
	$cur_req = 1;
    foreach ($input_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
		if ($key == 'required') {
			$cur_req = 2;
		}
        $_input_attrs .= $key . '=' . '"' . $val . '" ';
    }

    $_input_attrs = rtrim($_input_attrs);

    $form_group_attr['app-field-wrapper'] = $name;

    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '" ';
    }

    $_form_group_attr = rtrim($_form_group_attr);

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }
    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }
    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
		if ($cur_req == 1) {
			$input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
		}else{
			$input .= '<label for="' . $name . '" class="control-label"> <small class="req text-danger">* </small>' . _l($label, '', false) . '</label>';
		}
    }
    $input .= '<div class="input-group">';
    $input .= '<input type="text" id="' . $name . '" name="' . $name . '" class="form-control daterangepicker ' . $input_class . '" ' . $_input_attrs . ' value="' . set_value($name, $value) . '" autocomplete="off">';
    $input .= '<div class="input-group-addon">
    <i class="fa fa-calendar calendar-icon"></i>
</div>';
    $input .= '</div>';
    $input .= '</div>';

    return $input;
}
function render_date_time_range_picker($name, $label = '', $value = '', $input_attrs = [], $form_group_attr = [], $form_group_class = '', $input_class = '')
{
    $input            = '';
    $_form_group_attr = '';
    $_input_attrs     = '';
	$cur_req = 1;
    foreach ($input_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
		if ($key == 'required') {
			$cur_req = 2;
		}
        $_input_attrs .= $key . '=' . '"' . $val . '" ';
    }

    $_input_attrs = rtrim($_input_attrs);

    $form_group_attr['app-field-wrapper'] = $name;

    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '" ';
    }

    $_form_group_attr = rtrim($_form_group_attr);

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }
    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }
    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
		if ($cur_req == 1) {
			$input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
		}else{
			$input .= '<label for="' . $name . '" class="control-label"> <small class="req text-danger">* </small>' . _l($label, '', false) . '</label>';
		}
    }
    $input .= '<div class="input-group">';
    $input .= '<input type="text" id="' . $name . '" name="' . $name . '" class="form-control datetimerangepicker' . $input_class . '" ' . $_input_attrs . ' value="' . set_value($name, $value) . '" autocomplete="off">';
    $input .= '<div class="input-group-addon">
    <i class="fa fa-calendar calendar-icon"></i>
</div>';
    $input .= '</div>';
    $input .= '</div>';

    return $input;
}
/**
 * Render date picker input for admin area
 * @param  [type] $name             input name
 * @param  string $label            input label
 * @param  string $value            default value
 * @param  array  $input_attrs      input attributes
 * @param  array  $form_group_attr  <div class="form-group"> div wrapper html attributes
 * @param  string $form_group_class form group div wrapper additional class
 * @param  string $input_class      <input> additional class
 * @return string
 */
function render_date_input($name, $label = '', $value = '', $input_attrs = [], $form_group_attr = [], $form_group_class = '', $input_class = '')
{
	$CI = & get_instance();
	$uri1 = $CI->uri->segment(2);
	$uri2 = $CI->uri->segment(3);
    $input            = '';
    $_form_group_attr = '';
    $_input_attrs     = '';
	$cur_req = 1;
    foreach ($input_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
		if ($key == 'required') {
			$cur_req = 2;
		}
        $_input_attrs .= $key . '=' . '"' . $val . '" ';
    }

    $_input_attrs = rtrim($_input_attrs);

    $form_group_attr['app-field-wrapper'] = $name;

    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '" ';
    }

    $_form_group_attr = rtrim($_form_group_attr);

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }
    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }
    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
		if ($cur_req == 1) {
			if($uri1== 'projects' && $uri2== 'project' && $label=='project_start_date'){
				$fields3 = get_option('deal_important_msg');
				$msg_name = '';
				if(!empty($fields3) && $fields3 != 'null'){
					$important_messages = json_decode($fields3);
					$msg_name = $important_messages->project_start_date;
				}
				$input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false);
				if(!empty($msg_name)){
					$input .= '<span style="color: #d2be19;margin-left: 5px;" title="'. $msg_name.'" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span>';
				}
				$input .= '</label>';
			}
			else if($uri1== 'projects' && $uri2== 'project' && $label=='project_deadline'){
				$fields3 = get_option('deal_important_msg');
				$msg_name = '';
				if(!empty($fields3) && $fields3 != 'null'){
					$important_messages = json_decode($fields3);
					$msg_name = $important_messages->project_deadline;
				}
				$input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false);
				if(!empty($msg_name)){
					$input .= '<span style="color: #d2be19;margin-left: 5px;" title="'. $msg_name.'" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span>';
				}
				$input .= '</label>';
			}
			else{
				$input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
			}
		}else{
			if($uri1== 'projects' && $uri2== 'project' && $label=='project_start_date'){
				$fields3 = get_option('deal_important_msg');
				$msg_name = '';
				if(!empty($fields3) && $fields3 != 'null'){
					$important_messages = json_decode($fields3);
					$msg_name = $important_messages->project_start_date;
				}
				$input .= '<label for="' . $name . '" class="control-label"> <small class="req text-danger">* </small>' . _l($label, '', false);
				if(!empty($msg_name)){
					$input .= '<span style="color: #d2be19;margin-left: 5px;" title="'. _l('field_important').'" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span>';
				}
				$input .= '</label>';
			}
			else if($uri1== 'projects' && $uri2== 'project' && $label=='project_deadline'){
				$fields3 = get_option('deal_important_msg');
				$msg_name = '';
				if(!empty($fields3) && $fields3 != 'null'){
					$important_messages = json_decode($fields3);
					$msg_name = $important_messages->project_deadline;
				}
				$input .= '<label for="' . $name . '" class="control-label"> <small class="req text-danger">* </small>' . _l($label, '', false);
				//$input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false);
				if(!empty($msg_name)){
					$input .= '<span style="color: #d2be19;margin-left: 5px;" title="'. _l('field_important').'" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span>';
				}
				$input .= '</label>';
			}
			else{
				$input .= '<label for="' . $name . '" class="control-label"> <small class="req text-danger">* </small>' . _l($label, '', false) . '</label>';
			}
		}
    }
    $input .= '<div class="input-group date">';
    $input .= '<input type="text" id="' . $name . '" name="' . $name . '" class="form-control datepicker' . $input_class . '" ' . $_input_attrs . ' value="' . set_value($name, $value) . '" autocomplete="off">';
    $input .= '<div class="input-group-addon">
    <i class="fa fa-calendar calendar-icon"></i>
</div>';
    $input .= '</div>';
    $input .= '</div>';

    return $input;
}
/**
 * Render date time picker input for admin area
 * @param  [type] $name             input name
 * @param  string $label            input label
 * @param  string $value            default value
 * @param  array  $input_attrs      input attributes
 * @param  array  $form_group_attr  <div class="form-group"> div wrapper html attributes
 * @param  string $form_group_class form group div wrapper additional class
 * @param  string $input_class      <input> additional class
 * @return string
 */
function render_datetime_input($name, $label = '', $value = '', $input_attrs = [], $form_group_attr = [], $form_group_class = '', $input_class = '')
{
    $html = render_date_input($name, $label, $value, $input_attrs, $form_group_attr, $form_group_class, $input_class);
    $html = str_replace('datepicker', 'datetimepicker', $html);

    return $html;
}
/**
 * Render textarea for admin area
 * @param  [type] $name             textarea name
 * @param  string $label            textarea label
 * @param  string $value            default value
 * @param  array  $textarea_attrs      textarea attributes
 * @param  array  $form_group_attr  <div class="form-group"> div wrapper html attributes
 * @param  string $form_group_class form group div wrapper additional class
 * @param  string $textarea_class      <textarea> additional class
 * @return string
 */
function render_textarea($name, $label = '', $value = '', $textarea_attrs = [], $form_group_attr = [], $form_group_class = '', $textarea_class = '')
{
    $textarea         = '';
    $_form_group_attr = '';
    $_textarea_attrs  = '';
    if (!isset($textarea_attrs['rows'])) {
        $textarea_attrs['rows'] = 4;
    }

    if (isset($textarea_attrs['class'])) {
        $textarea_class .= ' ' . $textarea_attrs['class'];
        unset($textarea_attrs['class']);
    }
	$cur_req = 1;
    foreach ($textarea_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
		if ($key == 'required') {
			$cur_req = 2;
		}
        $_textarea_attrs .= $key . '=' . '"' . $val . '" ';
    }

    $_textarea_attrs = rtrim($_textarea_attrs);

    $form_group_attr['app-field-wrapper'] = $name;

    foreach ($form_group_attr as $key => $val) {
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '" ';
    }

    $_form_group_attr = rtrim($_form_group_attr);

    if (!empty($textarea_class)) {
        $textarea_class = trim($textarea_class);
        $textarea_class = ' ' . $textarea_class;
    }
    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }
    $textarea .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
		if ($cur_req == 1) {
			$textarea .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
		}else{
			$textarea .= '<label for="' . $name . '" class="control-label"> <small class="req text-danger">* </small>' . _l($label, '', false) . '</label>';
		}
    }

    $v = clear_textarea_breaks($value);
    if (strpos($textarea_class, 'tinymce') !== false) {
        $v = $value;
    }
    $textarea .= '<textarea id="' . $name . '" name="' . $name . '" class="form-control' . $textarea_class . '" ' . $_textarea_attrs . '>' . set_value($name, $v) . '</textarea>';

    $textarea .= '</div>';

    return $textarea;
}
/**
 * Render <select> field optimized for admin area and bootstrap-select plugin
 * @param  string  $name             select name
 * @param  array  $options          option to include
 * @param  array   $option_attrs     additional options attributes to include, attributes accepted based on the bootstrap-selectp lugin
 * @param  string  $label            select label
 * @param  string  $selected         default selected value
 * @param  array   $select_attrs     <select> additional attributes
 * @param  array   $form_group_attr  <div class="form-group"> div wrapper html attributes
 * @param  string  $form_group_class <div class="form-group"> additional class
 * @param  string  $select_class     additional <select> class
 * @param  boolean $include_blank    do you want to include the first <option> to be empty
 * @return string
 */
function render_select($name, $options, $option_attrs = [], $label = '', $selected = '', $select_attrs = [], $form_group_attr = [], $form_group_class = '', $select_class = '', $include_blank = true)
{
	$CI = & get_instance();
	$uri1 = $CI->uri->segment(2);
	$uri2 = $CI->uri->segment(3);
	$fields2 = get_option('deal_important');
	$important_fields = array();
	if(!empty($fields2) && $fields2 != 'null'){
			$important_fields = json_decode($fields2);
		}
    $callback_translate = '';
    if (isset($options['callback_translate'])) {
        $callback_translate = $options['callback_translate'];
        unset($options['callback_translate']);
    }
    $select           = '';
    $_form_group_attr = '';
    $_select_attrs    = '';
    if (!isset($select_attrs['data-width'])) {
        $select_attrs['data-width'] = '100%';
    }
    if (!isset($select_attrs['data-none-selected-text'])) {
        $select_attrs['data-none-selected-text'] = _l('dropdown_non_selected_tex');
    }
	$cur_req = 1;
    foreach ($select_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
		if ($key == 'required') {
			$cur_req = 2;
		}
        $_select_attrs .= $key . '=' . '"' . $val . '" ';
    }

    $_select_attrs = rtrim($_select_attrs);
	
    $form_group_attr['app-field-wrapper'] = $name;
    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '" ';
    }
    $_form_group_attr = rtrim($_form_group_attr);
    if (!empty($select_class)) {
        $select_class = ' ' . $select_class;
    }
    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }
    $select .= '<div class="select-placeholder form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
		$fields3 = get_option('deal_important_msg');
		if ($cur_req == 1) {
			if($uri1== 'projects' && $uri2== 'project' && $name=='pipeline_id'){
				$fields3 = get_option('deal_important_msg');
				$msg_name = '';
				if(!empty($fields3) && $fields3 != 'null'){
					$important_messages = json_decode($fields3);
					$msg_name = $important_messages->pipeline_id;
				}
				$select .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false);
				if(!empty($important_fields) && in_array("pipeline_id", $important_fields)){
					$select .= '<span style="color: #d2be19;margin-left: 5px;" title="'.$msg_name.'" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span>';
				}
				$select .= '</label>';
			}
			else if($uri1== 'projects' && $uri2== 'project' && $name=='teamleader'){
				$fields3 = get_option('deal_important_msg');
				$msg_name = '';
				if(!empty($fields3) && $fields3 != 'null'){
					$important_messages = json_decode($fields3);
					$msg_name = $important_messages->teamleader;
				}
				$select .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false);
				if(!empty($important_fields) && in_array("teamleader", $important_fields)){
					$select .= '<span style="color: #d2be19;margin-left: 5px;" title="'.$msg_name.'" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span>';
				}
				$select .= '</label>';
			}
			else if($uri1== 'projects' && $uri2== 'project' && $name=='project_members[]'){
				$fields3 = get_option('deal_important_msg');
				$msg_name = '';
				if(!empty($fields3) && $fields3 != 'null'){
					$important_messages = json_decode($fields3);
					$msg_name = $important_messages->project_members;
				}
				$select .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false);
				if(!empty($important_fields) && in_array("project_members[]", $important_fields)){
					$select .= '<span style="color: #d2be19;margin-left: 5px;" title="'.$msg_name.'" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span>';
				}
				$select .= '</label>';
			}
			else{
				$select .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
			}
		}else{
			if($uri1== 'projects' && $uri2== 'project' && $name=='pipeline_id'){
				$select .= '<label for="' . $name . '" class="control-label"> <small class="req text-danger">* </small>' . _l($label, '', false);
				$fields3 = get_option('deal_important_msg');
				$msg_name = '';
				if(!empty($fields3) && $fields3 != 'null'){
					$important_messages = json_decode($fields3);
					$msg_name = $important_messages->pipeline_id;
				}
				if(!empty($important_fields) && in_array("pipeline_id", $important_fields)){
					$select .= '<span style="color: #d2be19;margin-left: 5px;" title="'. $msg_name.'" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span>';
				}
				$select .= '</label>';
			}
			else if($uri1== 'projects' && $uri2== 'project' && $name=='teamleader'){
				$fields3 = get_option('deal_important_msg');
				$msg_name = '';
				if(!empty($fields3) && $fields3 != 'null'){
					$important_messages = json_decode($fields3);
					$msg_name = $important_messages->teamleader;
				}
				$select .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false);
				if(!empty($important_fields) && in_array("teamleader", $important_fields)){
					$select .= '<span style="color: #d2be19;margin-left: 5px;" title="'. $msg_name.'" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span>';
				}
				$select .= '</label>';
			}
			else if($uri1== 'projects' && $uri2== 'project' && $name=='project_members[]'){
				$fields3 = get_option('deal_important_msg');
				$msg_name = '';
				if(!empty($fields3) && $fields3 != 'null'){
					$important_messages = json_decode($fields3);
					$msg_name = $important_messages->project_members;
				}
				$select .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false);
				if(!empty($important_fields) && in_array("project_members[]", $important_fields)){
					$select .= '<span style="color: #d2be19;margin-left: 5px;" title="'. $msg_name.'" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span>';
				}
				$select .= '</label>';
			}
			else{
				$select .= '<label for="' . $name . '" class="control-label"> <small class="req text-danger">* </small>' . _l($label, '', false) . '</label>';
			}
		}
    }
	$req_ch_id = $name;
	$call_data = '';
	if($uri1== 'projects' && $uri2== 'project' && $name=='project_members[]'){
		$req_ch_id = 'project_members11';
		$call_data = "onchange='ch_project_member(this)'";
	}
    $select .= '<select id="' . $name . '" name="' . $name . '" class="selectpicker' . $select_class . '" ' . $_select_attrs . ' '.$call_data.' data-live-search="true">';
    if(is_admin(get_staff_user_id())) {
        if ($include_blank == true) {
            $select .= '<option value="">Nothing Selected</option>';
        }
    }
    foreach ($options as $option) {
        $val       = '';
        $_selected = '';
        $key       = '';
        if (isset($option[$option_attrs[0]]) && !empty($option[$option_attrs[0]])) {
            $key = $option[$option_attrs[0]];
        }
        if (!is_array($option_attrs[1])) {
            $val = $option[$option_attrs[1]];
        } else {
            foreach ($option_attrs[1] as $_val) {
                $val .= $option[$_val] . ' ';
            }
        }
        $val = trim($val);

        if ($callback_translate != '') {
            if (function_exists($callback_translate) && is_callable($callback_translate)) {
                $val = call_user_func($callback_translate, $key);
            }
        }

        $data_sub_text = '';
        if (!is_array($selected)) {
            if ($selected != '') {
                if ($selected == $key) {
                    $_selected = ' selected';
                }
            }
        } else {
            foreach ($selected as $id) {
                if ($key == $id) {
                    $_selected = ' selected';
                }
            }
        }
        if (isset($option_attrs[2])) {
            if (strpos($option_attrs[2], ',') !== false) {
                $sub_text = '';
                $_temp    = explode(',', $option_attrs[2]);
                foreach ($_temp as $t) {
                    if (isset($option[$t])) {
                        $sub_text .= $option[$t] . ' ';
                    }
                }
            } else {
                if (isset($option[$option_attrs[2]])) {
                    $sub_text = $option[$option_attrs[2]];
                } else {
                    $sub_text = $option_attrs[2];
                }
            }
            $data_sub_text = ' data-subtext=' . '"' . $sub_text . '"';
        }
        $data_content = '';
        if (isset($option['option_attributes'])) {
            foreach ($option['option_attributes'] as $_opt_attr_key => $_opt_attr_val) {
                $data_content .= $_opt_attr_key . '=' . '"' . $_opt_attr_val . '"';
            }
            if ($data_content != '') {
                $data_content = ' ' . $data_content;
            }
        }
        $select .= '<option value="' . $key . '"' . $_selected . $data_content . $data_sub_text . '>' . $val . '</option>';
    }
    $select .= '</select>';
    $select .= '</div>';

    return $select;
}

function render_select_with_input_group($name, $options, $option_attrs = [], $label = '', $selected = '', $input_group_contents = '', $select_attrs = [], $form_group_attr = [], $form_group_class = '', $select_class = '', $include_blank = true)
{
    $select_class .= ' _select_input_group';
    $select = render_select($name, $options, $option_attrs, $label, $selected, $select_attrs, $form_group_attr, $form_group_class, $select_class, $include_blank);
    $select = str_replace('form-group', 'input-group input-group-select select-' . $name, $select);
    $select = str_replace('select-placeholder ', '', $select);
    $select = str_replace('</select>', '</select><div class="input-group-addon">' . $input_group_contents . '</div>', $select);

    $re = '/<label.*<\/label>/i';
    preg_match($re, $select, $label);

    if (count($label) > 0) {
        $select = preg_replace($re, '', $select);
        $select = '<div class="select-placeholder form-group form-group-select-input-' . $name . ' input-group-select">' . $label[0] . $select . '</div>';
    }

    return $select;
}


if (!function_exists('render_form_builder_field')) {
    /**
     * Used for customer forms eq. leads form, builded from the form builder plugin
     * @param  object $field field from database
     * @return mixed
     */
    function render_form_builder_field($field)
    {
        $type         = $field->type;
        $classNameCol = 'col-md-12';
        if (isset($field->className)) {
            if (strpos($field->className, 'form-col') !== false) {
                $classNames = explode(' ', $field->className);
                if (is_array($classNames)) {
                    $classNameColArray = array_filter($classNames, function ($class) {
                        return startsWith($class, 'form-col');
                    });

                    $classNameCol = implode(' ', $classNameColArray);
                    $classNameCol = trim($classNameCol);

                    $classNameCol = str_replace('form-col-xs', 'col-xs', $classNameCol);
                    $classNameCol = str_replace('form-col-sm', 'col-sm', $classNameCol);
                    $classNameCol = str_replace('form-col-md', 'col-md', $classNameCol);
                    $classNameCol = str_replace('form-col-lg', 'col-lg', $classNameCol);

                    // Default col-md-X
                    $classNameCol = str_replace('form-col', 'col-md', $classNameCol);
                }
            }
        }

        echo '<div class="' . $classNameCol . '">';
        if ($type == 'header' || $type == 'paragraph') {
            echo '<' . $field->subtype . ' class="' . (isset($field->className) ? $field->className : '') . '">' . check_for_links(nl2br($field->label)) . '</' . $field->subtype . '>';
        } else {
            echo '<div class="form-group" data-type="' . $type . '" data-name="' . $field->name . '" data-required="' . (isset($field->required) ? true : 'false') . '">';
            echo '<label class="control-label" for="' . $field->name . '">' . (isset($field->required) ? ' <span class="text-danger">* </span> ': '') . $field->label . '' . (isset($field->description) ? ' <i class="fa fa-question-circle" data-toggle="tooltip" data-title="' . $field->description . '" data-placement="' . (is_rtl(true) ? 'left' : 'right') . '"></i>' : '') . '</label>';
            if (isset($field->subtype) && $field->subtype == 'color') {
                echo '<div class="input-group colorpicker-input">
         <input' . (isset($field->required) ? ' required="true"': '') . ' placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '" type="text"' . (isset($field->value) ? ' value="' . $field->value . '"' : '') . ' name="' . $field->name . '" id="' . $field->name . '" class="' . (isset($field->className) ? $field->className : '') . '" />
             <span class="input-group-addon"><i></i></span>
         </div>';
            } elseif ($type == 'file' || $type == 'text' || $type == 'number') {
                if($field->type == 'file') {
                    $ftype = isset($field->subtype) ? $field->subtype : $type;
                    echo '<input' . (isset($field->required) ? ' required="true"': '') . (isset($field->placeholder) ? ' placeholder="' . $field->placeholder . '"' : '') . ' multiple type="' . $ftype . '" name="' . $field->name . '[]" id="' . $field->name . '" class="' . (isset($field->className) ? $field->className : '') . '" value="' . (isset($field->value) ? $field->value : '') . '"' . ($field->type == 'file' ? ' accept="' . get_form_accepted_mimes() . '" filesize="' . file_upload_max_size() . '"' : '') . '>';
                } else {
                    $ftype = isset($field->subtype) ? $field->subtype : $type;
                    echo '<input' . (isset($field->required) ? ' required="true"': '') . (isset($field->placeholder) ? ' placeholder="' . $field->placeholder . '"' : '') . ' type="' . $ftype . '" name="' . $field->name . '" id="' . $field->name . '" class="' . (isset($field->className) ? $field->className : '') . '" value="' . (isset($field->value) ? $field->value : '') . '"' . ($field->type == 'file' ? ' accept="' . get_form_accepted_mimes() . '" filesize="' . file_upload_max_size() . '"' : '') . '>';
                }
            } elseif ($type == 'textarea') {
                echo '<textarea' . (isset($field->required) ? ' required="true"': '') . ' id="' . $field->name . '" name="' . $field->name . '" rows="' . (isset($field->rows) ? $field->rows : '4') . '" class="' . (isset($field->className) ? $field->className : '') . '" placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '">' . (isset($field->value) ? $field->value : '') . '</textarea>';
            } elseif ($type == 'date') {
                echo '<input' . (isset($field->required) ? ' required="true"': '') . ' placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '" type="text" class="' . (isset($field->className) ? $field->className : '') . ' datepicker" name="' . $field->name . '" id="' . $field->name . '" value="' . (isset($field->value) ? _d($field->value) : '') . '">';
            } elseif ($type == 'datetime-local') {
                echo '<input' . (isset($field->required) ? ' required="true"': '') . ' placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '" type="text" class="' . (isset($field->className) ? $field->className : '') . ' datetimepicker" name="' . $field->name . '" id="' . $field->name . '" value="' . (isset($field->value) ? _dt($field->value) : '') . '">';
            } elseif ($type == 'select') {
                echo '<select' . (isset($field->required) ? ' required="true"': '') . '' . (isset($field->multiple) ? ' multiple="true"' : '') . ' class="' . (isset($field->className) ? $field->className : '') . '" name="' . $field->name . (isset($field->multiple) ? '[]' : '') . '" id="' . $field->name . '"' . (isset($field->values) && count($field->values) > 10 ? 'data-live-search="true"': '') . 'data-none-selected-text="' . (isset($field->placeholder) ? $field->placeholder : '') . '">';
                $values = [];
                if (isset($field->values) && count($field->values) > 0) {
                    foreach ($field->values as $option) {
                        echo '<option value="' . $option->value . '" ' . (isset($option->selected) ? ' selected' : '') . '>' . $option->label . '</option>';
                    }
                }
                echo '</select>';
            } elseif ($type == 'checkbox-group') {
                $values = [];
                if (isset($field->values) && count($field->values) > 0) {
                    $i = 0;
                    echo '<div class="chk">';
                    foreach ($field->values as $checkbox) {
                        echo '<div class="checkbox' . ((isset($field->inline) && $field->inline == 'true') || (isset($field->className) && strpos($field->className, 'form-inline-checkbox') !== false) ? ' checkbox-inline' : '') . '">';
                        echo '<input' . (isset($field->required) ? ' required="true"': '') . ' class="' . (isset($field->className) ? $field->className : '') . '" type="checkbox" id="chk_' . $field->name . '_' . $i . '" value="' . $checkbox->value . '" name="' . $field->name . '[]"' . (isset($checkbox->selected) ? ' checked' : '') . '>';
                        echo '<label for="chk_' . $field->name . '_' . $i . '">';
                        echo $checkbox->label;
                        echo '</label>';
                        echo '</div>';
                        $i++;
                    }
                    echo '</div>';
                }
            }
            echo '</div>';
        }
        echo '</div>';
    }
}

/**
 * The function will do the necessar checking to use custom fields in the form builder eq leads forms
 * @param  array $custom_fields custom fields to check
 * @return array
 */
function format_external_form_custom_fields($custom_fields)
{
    $cfields = [];
    foreach ($custom_fields as $f) {
        $_field_object = new stdClass();
        $type          = $f['type'];
        $subtype       = '';
        $className     = 'form-control';

        if ($f['type'] == 'colorpicker') {
            $type    = 'text';
            $subtype = 'color';
        } elseif ($f['type'] == 'date_picker') {
            $type = 'date';
        } elseif ($f['type'] == 'date_picker_time') {
            $type = 'datetime-local';
        } elseif ($f['type'] == 'checkbox') {
            $type      = 'checkbox-group';
            $className = '';
            if ($f['display_inline'] == 1) {
                $className .= 'form-inline-checkbox';
            }
        } elseif ($f['type'] == 'input') {
            $type = 'text';
        } elseif ($f['type'] == 'multiselect') {
            $type = 'select';
        }

        $field_array = [
                'subtype'   => $subtype,
                'type'      => $type,
                'label'     => $f['name'],
                'className' => $className,
                'name'      => 'form-cf-' . $f['id'],
            ];

        if ($f['type'] == 'multiselect') {
            $field_array['multiple'] = true;
        }

        if ($f['required'] == 1) {
            $field_array['required'] = true;
        }

        if ($f['type'] == 'checkbox' || $f['type'] == 'select' || $f['type'] == 'multiselect') {
            $field_array['values'] = [];
            $options               = explode(',', $f['options']);
            // leave first field blank
            if ($f['type'] == 'select') {
                array_push($field_array['values'], [
                        'label' => '',
                        'value' => '',
                    ]);
            }
            foreach ($options as $option) {
                $option = trim($option);
                if ($option != '') {
                    array_push($field_array['values'], [
                            'label' => $option,
                            'value' => $option,
                        ]);
                }
            }
        }

        $_field_object->label    = $f['name'];
        $_field_object->name     = 'form-cf-' . $f['id'];
        $_field_object->fields   = [];
        $_field_object->fields[] = $field_array;
        $cfields[]               = $_field_object;
    }

    return $cfields;
}

function render_location_picker($name, $label = '', $value = '', $input_attrs = [], $form_group_attr = [], $form_group_class = '', $input_class = '')
{   
    $lat=$lng='0';
    if(set_value($name, $value)){
        list($lat,$lng) =explode(',',set_value($name,$value));
    }
    set_custom_field_location_js_data($lat,$lng,$name);
    $input            = '';
    $_form_group_attr = '';
    $_input_attrs     = '';
	$cur_req = 1;
    foreach ($input_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
		if ($key == 'required') {
			$cur_req = 2;
		}
        $_input_attrs .= $key . '=' . '"' . $val . '" ';
    }

    $_input_attrs = rtrim($_input_attrs);

    $form_group_attr['app-field-wrapper'] = $name;

    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '" ';
    }

    $_form_group_attr = rtrim($_form_group_attr);

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }
    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }
    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
		if ($cur_req == 1) {
			$input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
		}else{
			$input .= '<label for="' . $name . '" class="control-label"> <small class="req text-danger">* </small>' . _l($label, '', false) . '</label>';
		}
    }
    $input .= '<div class="input-group" style="width:100%">';

    $input .='<input id="cgmapsearchInput" class="controls mapsearchInput" type="text" placeholder="Search location">';
    $input .='<div id="cgmap" style="height: 400px; width: 100%; position: relative; overflow: hidden;"></div>';
    $input .= '<input type="hidden" '.(($cur_req==2)?'required="true"':"").' id="' . $name . '" name="' . $name . '" value="' . set_value($name, $value) . '"  />';
    $input .= '</div>';
    $input .= '</div>';
    return $input;
}
function render_deal_lead_list_by_email($email){
    $CI = &get_instance();
    $staff_id = get_staff_user_id();
    $cur_mail = $email;
    $req_val = '';
    if(!empty($cur_mail)){
    $req_mail = explode(',',$cur_mail);
    if(!empty($req_mail)){
        $cur_mail = $req_mail[0];
    }
    $all_vals = $CI->projects_model->deal_values($cur_mail,$staff_id);
    $req_val = '';
    if(get_option('link_deal')== 'yes' && get_option('deal_map') != 'if more than one open deal – allow to map manually'){
        
        $all_vals = get_deal_name($cur_mail,get_option('deal_map'));
        //echo '<pre>';print_r($all_vals);exit;
        if(!empty($all_vals)){
            $req_val .= '<option value="'.$all_vals['project_id'].'">'.$all_vals['project_name'].'</option>';
        }
        else{
            $req_val .= '<option value="">None</option>';
        }
    }
    else{
        
        if(!empty($all_vals)){
            $req_val .='<optgroup label="Deals">';
            foreach($all_vals as $all_val1){
                $req_val .= '<option value="project_'.$all_val1['id'].'">'.$all_val1['name'].'</option>';
            }
            $req_val .='</optgroup>';
        }
    }
    
    $leads =$CI->leads_model->get_leads_by_contact_email($cur_mail,$staff_id);
    if($leads){
        $req_val .='<optgroup label="Leads">';
        foreach($leads as $lead){
            $req_val .= '<option value="lead_'.$lead->id.'">'.$lead->name.'</option>';
        }
        $req_val .='</optgroup>';
    }
    }
    return $req_val ;
}
