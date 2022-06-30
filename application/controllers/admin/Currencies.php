<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Currencies extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('currencies_model');
        if (!is_admin()) {
            access_denied('Currencies');
        }
    }

    /* List all currencies */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('currencies');
        }
        $data['title'] = _l('currencies');
        $this->load->view('admin/currencies/manage', $data);
    }

    public function getconvertion_rate() {
        //pre($_POST);
        $getconvrates = $this->currencies_model->getConvRate();
        //pre($getconvrates);
        $getallcurrency = $this->currencies_model->getAllCurrency();
        
        $html = '';
        if($getconvrates) {
            foreach($getconvrates as $val) {
                $multi = '';
                $divi = '';
                if($val["operation"] == '*') {
                    $multi = 'selected';
                } else {
                    $divi = 'selected';
                }
                $html .= '<div style="height:40px; clear:both"><div class="col-md-3">
                <select name="currency_to[]" class="form-control" readonly>
                <option value="'.$val["currency_to"].'">'.$val["currency_to"].'</option>
                </select>';
                $html .= '</div>
                <div class="col-md-4">
                    <input type="number" name="rate[]" value="'.$val["rate"].'" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" placeholder="Price" value=""  class="form-control" /> 
                </div>
                <div class="col-md-3">
                <select name="operation[]" class="form-control" >
                <option value="*" '.$multi.'>Multiply</option>
                <option value="/" '.$divi.'>Division</option>
                </select>
                </div>
                </div>';
            }
            echo $html;
        } else {
            $html .= '<div style="height:40px; clear:both"><div class="col-md-3">
            <select name="currency_to[]" class="form-control" >';
            foreach($getallcurrency as $val) {
                $html .= '<option value="'.$val["name"].'">'.$val["name"].'</option>';
            } 
            $html .= '</select>';
            $html .= '</div>
            <div class="col-md-4">
                <input type="number" name="rate[]" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" placeholder="Price" value=""  class="form-control" /> 
            </div>
            <div class="col-md-3">
            <select name="operation[]" class="form-control" ><option value="*">Multiply</option><option value="/">Division</option></select>
            </div>
            <a href="javascript:void(0);" class="remove_button" title="Remove field" style="position:relative; top:8px; left:15px"><i class="fa fa-trash"></i></a>
            </div>';
            echo $html;
        }
       // pre($getallcurrency);
    }

    public function getaddfields() {
        $getallcurrency = $this->currencies_model->getAllCurrency();
        $postcnt = count($_POST['currency']);
        $curcnt = count($getallcurrency);
        $html = '';
        if($postcnt != $curcnt) {
            $html .= '<div style="height:40px; clear:both"><div class="col-md-3">
            <select name="currency_to[]" class="form-control" >';
            foreach($getallcurrency as $val) {
                if (!in_array($val['name'], $_POST['currency'])) {
                    $html .= '<option value="'.$val["name"].'">'.$val["name"].'</option>';
                }
            } 
            $html .= '</select>';
            $html .= '</div>
            <div class="col-md-4">
                <input type="number" name="rate[]" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" placeholder="Price" value=""  class="form-control" /> 
            </div>
            <div class="col-md-3">
            <select name="operation[]" class="form-control" ><option value="*">Multiply</option><option value="/">Division</option></select>
            </div>
            <a href="javascript:void(0);" class="remove_button" title="Remove field" style="position:relative; top:8px; left:15px"><i class="fa fa-trash"></i></a>
            </div>';
            echo $html;
        }
        exit;
    }

    /* Update currency or add new / ajax */
    public function manage()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['currencyid'] == '') {
                $success = $this->currencies_model->add($data);
                $message = '';
                if ($success == true) {
                    $message = _l('added_successfully', _l('currency'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                ]);
            } else {
                $currency_to = $_POST['currency_to'];
                $rate = $_POST['rate'];
                $operation = $_POST['operation'];
                $i=0;
                
                foreach($rate as $val) {
                    $updateprice = array();
                    if($val != '') {
                        $updateprice['currency_from'] = $_POST['name'];
                        $updateprice['currency_to'] = $currency_to[$i];
                        $updateprice['rate'] = $val;
                        $updateprice['operation'] = $operation[$i];
                        //pre($updateprice);
                        $this->currencies_model->editConversionRate($updateprice);
                    }
                    $i++;
                }
                $success = $this->currencies_model->edit($data);
                $message = '';
                if ($success == 1) {
                    $success = true;
                    $message = _l('updated_successfully', _l('currency'));
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                    ]);
                    exit;
                } 
                if ($success == 2) {
                    $success = true;
                    $message = _l('currency_update_error', _l('currency'));
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                    ]);
                    exit;
                }
                
            }
        }
    }

    /* Make currency your base currency */
    public function make_base_currency($id)
    {
        if (!$id) {
            redirect(admin_url('currencies'));
        }
        $response = $this->currencies_model->make_base_currency($id);
        if (is_array($response) && isset($response['has_transactions_currency'])) {
            set_alert('danger', _l('has_transactions_currency_base_change'));
        } elseif ($response == true) {
            set_alert('success', _l('base_currency_set'));
        }
        redirect(admin_url('currencies'));
    }

    /* Delete currency from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('currencies'));
        }
        $response = $this->currencies_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('currency_lowercase')));
        } elseif (is_array($response) && isset($response['is_default'])) {
            set_alert('warning', _l('cant_delete_base_currency'));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('currency')));
        } else {
            set_alert('warning', _l('currency_exist', _l('currency_lowercase')));
        }
        redirect(admin_url('currencies'));
    }

    /* Get symbol by currency id passed */
    public function get_currency_symbol($id)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode([
                'symbol' => $this->currencies_model->get_currency_symbol($id),
            ]);
        }
    }
}
