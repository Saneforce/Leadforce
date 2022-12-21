<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Products extends AdminController
{
    /* List all clients */
    public function index()
    {
        if (!has_permission('customers', '', 'view')) {
            if (!have_assigned_customers() && !has_permission('customers', '', 'create')) {
                access_denied('customers');
            }
        }
        $this->load->model('contracts_model');
        $data['contract_types'] = $this->contracts_model->get_contract_types();
        $data['groups']         = $this->products_model->get_groups();
        $data['title']          = _l('products');

        $this->load->model('proposals_model');
        $data['proposal_statuses'] = $this->proposals_model->get_statuses();

        $this->load->model('invoices_model');
        $data['invoice_statuses'] = $this->invoices_model->get_statuses();

        $this->load->model('estimates_model');
        $data['estimate_statuses'] = $this->estimates_model->get_statuses();

        $this->load->model('projects_model');
        $data['project_statuses'] = $this->projects_model->get_project_statuses();

        $data['customer_admins'] = $this->products_model->get_customers_admin_unique_ids();

        $whereContactsLoggedIn = '';
        if (!has_permission('customers', '', 'view')) {
            $whereContactsLoggedIn = ' AND userid IN (SELECT customer_id FROM '.db_prefix().'customer_admins WHERE staff_id=' . get_staff_user_id() . ')';
        }

        $data['contacts_logged_in_today'] = $this->products_model->get_contacts('', 'last_login LIKE "' . date('Y-m-d') . '%"' . $whereContactsLoggedIn);

        $data['countries'] = $this->products_model->get_clients_distinct_countries();

        $this->load->view('admin/products/manage', $data);
    }

    public function table()
    {
        if (!has_permission('customers', '', 'view')) {
            if (!have_assigned_customers() && !has_permission('customers', '', 'create')) {
                ajax_access_denied();
            }
        }

        $this->app->get_table_data('products');
    }

    public function all_contacts()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('all_contacts');
        }

        if (is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1') {
            $this->load->model('gdpr_model');
            $data['consent_purposes'] = $this->gdpr_model->get_consent_purposes();
        }

        $data['title'] = _l('customer_contacts');
        $this->load->view('admin/products/all_contacts', $data);
    }

    /* add new client ajax*/
    public function ajax_client()
    {
        $data = $this->input->post();
//pre($data);
        $data['category'] = $data['category'];
        if ($this->input->is_ajax_request()) {
                
            $data['id']  = $this->products_model->addCategory($data);
            $data['message']  = _l('added_successfully', _l('product_category'));
                
        }

        echo json_encode($data);
        exit();

    }

    /* add new Deal ajax*/
    public function ajax_project()
    {
        $data = $this->input->post();
        $data['name'] = $data['project'];
        $data['clientid'] = $data['orgid'];
        $data['status'] = 3;
        $_REQUEST['clientid'] = $data['orgid'];
        unset($data['project']);
        unset($data['orgid']);
        if (!has_permission('customers', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }

        if ($this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('customers', '', 'create')) {
                    access_denied('customers');
                }
               
                $save_and_add_contact = false;
                if (isset($data['save_and_add_contact'])) {
                    unset($data['save_and_add_contact']);
                    $save_and_add_contact = true;
                }
                //pre($data);
                $data['id']  = $this->projects_model->add($data);
                $data['message']  = _l('added_successfully', _l('deal'));
                
            }
            $data['result'] = $this->products_model->getDealsbyID($data);
            echo json_encode($data);
            exit;
        }
        //pre($data);
        echo json_encode($data);
        exit();

    }

// Merge Contact

    public function ajax_mergecontact()
    {
        $data = $this->input->post();
//pre($data);
        $data['id']  = $this->products_model->mergecontact($data);
        $data['message']  = _l('contact_merged');

        echo json_encode($data);
        exit();
    }


    public function getDealbyOrgId() {
        $data['result'] = $this->products_model->getDealsbyID($data);
        echo json_encode($data);
        exit;
    }

    public function getContactById() {
        $data['result'] = $this->products_model->getContactById();
        echo json_encode($data);
        exit;
    }

    public function checkduplicate() {
        $result = $this->products_model->checkduplicate();
        if($result) {
            $message = "Organization name already exist!, if you still want to create the organization you can ignore this message.";
        } else {
            $message = "no";
        }
        echo json_encode([
            'message' => $message,
        ]);
        exit;
    }

    public function checkduplicate_contact() {
        $result = $this->products_model->checkduplicate_contact();
        if($result) {
            $message = "Contact name already exist!, if you still want to create the Contact you can ignore this message.";
        } else {
            $message = "no";
        }
        echo json_encode([
            'message' => $message,
        ]);
        exit;
    }

    /* Edit client or add new client*/
    public function product($id = '')
    {
        if(!isset($_GET['gsearch'])) {
            $this->session->unset_userdata('pipelines');
            $this->session->unset_userdata('member');
            $this->session->unset_userdata('gsearch');
        }
        if (!has_permission('customers', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }

        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('customers', '', 'create')) {
                    access_denied('customers');
                }
                
                $data = $this->input->post();
//pre($data);
                $unitprice = $data['unit_price'];
                $unitcurrency = $data['currency'];
                $save_and_add_contact = false;
                unset($data['unit_price']);
                unset($data['currency']);
                //pre($data);
                $id = $this->products_model->add($data);
                if ($id) {
                    $i=0;
                    foreach($unitprice as $val) {
                        $addprice = array();
                        if($val != '') {
                            $addprice['productid'] = $id;
                            $addprice['price'] = $val;
                            $addprice['currency'] = $unitcurrency[$i];
                            $this->products_model->addUnitPrice($addprice);
                        }
                        $i++;
                    }
                    set_alert('success', _l('added_successfully', _l('product')));
                    redirect(admin_url('products/product/' . $id));
                }
            } else {
                //pre($_POST);
                $update = array();
                $update['name'] = $_POST['name'];
                $update['code'] = $_POST['code'];
                $update['categoryid'] = $_POST['categoryid'];
                $update['tax'] = $_POST['tax'];
                $update['description'] = $_POST['description'];
                $update['unit'] = $_POST['unit'];
                
                $unitprice = $_POST['unit_price'];
                $unitcurrency = $_POST['currency'];
                $varid = $_POST['varid'];
                $success = $this->products_model->update($update, $id);
                //pre($_POST);
                $i=0;
                foreach($unitprice as $val) {
                    $updateprice = array();
                    if($val != '') {
                        $updateprice['productid'] = $id;
                        $updateprice['price'] = $val;
                        $updateprice['currency'] = $unitcurrency[$i];
                        //pre($updateprice);
                        $this->products_model->editUnitPrice($updateprice);
                    }
                    $i++;
                }
                
                foreach($varid as $vid) {
                    $variationid = $vid;
                    $variation_price = $_POST['variation_price_'.$vid];
                    $variation_currency = $_POST['variation_currency_'.$vid];
                    $comment = $_POST['comment_'.$vid];
                    $i=0;
                    foreach($variation_price as $val) {
                        $updateprice = array();
                        if($val != '') {
                            $updateprice['productid'] = $id;
                            $updateprice['variation_price'] = $val;
                            $updateprice['currency'] = $variation_currency[$i];
                            $updateprice['variationid'] = $variationid;
                            $updateprice['comment'] = $comment[$i];
                            $this->products_model->editUnitVariation($updateprice);
                        }
                        $i++;
                    }
                }
                // $variation_name = $_POST['variation_name'];
                // $variation_price = $_POST['variation_price'];
                // $variation_currency = $_POST['variation_currency'];
                // $comment = $_POST['comment'];
                // pre($_POST);
                // $i=0;
                // foreach($variation_price as $val) {
                //     $updateprice = array();
                //     if($val != '') {
                //         $updateprice['productid'] = $id;
                //         $updateprice['variation_price'] = $val;
                //         $updateprice['currency'] = $variation_currency[$i];
                //         $updateprice['variationid'] = $varid[$i];
                //         $updateprice['comment'] = $comment[$i];
                //         $this->products_model->editUnitVariation($updateprice);
                //     }
                //     $i++;
                // }
                if ($success == true) {
                    set_alert('success', _l('updated_successfully', _l('product')));
                }
                redirect(admin_url('products/product/' . $id));
            }
        }

        $group         = !$this->input->get('group') ? 'profile' : $this->input->get('group');
        $data['group'] = $group;

        

        if ($id == '') {
            $title = _l('add_new', _l('product_lowercase'));
        } else {
            $client                = $this->products_model->get($id);
            $data['customer_tabs'] = get_product_profile_tabs();
//pre($data['customer_tabs']);
            if (!$client) {
                show_404();
            }
            $data['unitprice'] = $this->products_model->getUnitprice($id);
            $data['variations'] = $this->products_model->getVariations($id);
            $data['unitvariation'] = $this->products_model->getUnitVariation($id);
            $data['contacts'] = $this->products_model->get_contacts($id);
            $data['tab']      = isset($data['customer_tabs'][$group]) ? $data['customer_tabs'][$group] : null;

            if (!$data['tab']) {
                show_404();
            }

            // Fetch data based on groups
            if ($group == 'profile') {
                $data['customer_groups'] = $this->products_model->get_customer_groups($id);
                $data['customer_admins'] = $this->products_model->get_admins($id);
            } elseif ($group == 'attachments') {
                $data['attachments'] = get_product_attachments($id);
                //pre($data['attachments']);
            } elseif ($group == 'notes') {
                $data['user_notes'] = $this->misc_model->get_prodnotes($id);
            } elseif ($group == 'projects') {
                $this->load->model('projects_model');
                $data['project_statuses'] = $this->projects_model->get_project_statuses();
            }

//pre($data['project_statuses']);
            $data['product'] = $client;
            $title          = 'Product';

        }

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();
        $data['unit_currencies'] = $this->currencies_model->get_unit_currencies($id);
        //pre($data['unit_currencies']);
        $data['category'] = $this->products_model->getCategory();
        

        $data['bodyclass'] = 'customer-profile dynamic-create-groups';
        $data['title']     = $title;

        $this->load->view('admin/products/product', $data);
    }

    public function savevariation() {
        
        $added = $this->products_model->addvariation();
        return true;
    }

    public function getaddfields() {
        $this->load->model('currencies_model');
        $currencies = $this->currencies_model->get();
        $postcnt = count($_POST['currency']);
        $curcnt = count($currencies);
        $html = '';
        if($postcnt != $curcnt) {
            $html .= '<div style="height:40px;"><div class="col-md-6">
                <input type="number" name="unit_price[]" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" placeholder="Price" value=""  class="form-control" /> 
            </div>
            <div class="col-md-4">
                <select name="currency[]" class="form-control" >';
                foreach($currencies as $val) {
                    if (!in_array($val['name'], $_POST['currency'])) {
                        $html .= '<option value="'.$val["name"].'">'.$val["name"].'</option>';
                    }
                } 
                $html .= '</select>';
                $html .= '</div>
            <a href="javascript:void(0);" class="remove_button" title="Remove field" style="position:relative; top:8px; left:15px"><i class="fa fa-trash"></i></a>
            </div>';
            echo $html;
        }
        exit;
    }

    public function getaddproductfields() {
        $this->load->model('currencies_model');
        //pre($_POST);
        if(isset($_POST['currency'])) {
            if(is_numeric($_POST['currency'])) {
                $currency = $this->currencies_model->get($_POST['currency']);
                $cur = $currency->name;
            } else {
                $cur = $_POST['currency'];
            }
        } else {
            $currency = $this->currencies_model->get_base_currency();
            $cur = $currency->name;
        }
      //  $data = $this->products_model->getprod_price($cur);
		$data = $this->invoice_items_model->get_items($cur);
        $html = '';
            $html .= '<div style="height:40px; clear:both;" class="productdiv css-table-row" id="'.$_POST['length'].'"><div class="">
            <select name="product[]" class="form-control" onchange="getprice1(this,'.$_POST['length'].')"><option value="">--Select Item--</option>';
            foreach($data as $val) {
                    $html .= '<option value="'.$val["id"].'">'.$val["name"].'</option>';
            } 
            $html .= '</select>';
            $html .= '</div>';
            $html .=get_particulars_item_ordered_inputs($_POST['length']);
            $html .='<div class="">
            <input type="text" name="price[]" placeholder="Price" value="" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$_POST['length'].')" class="form-control" />';
                $html .= '</div>';
                $html .= '<div class="">
                <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,'.$_POST['length'].')" value=""  class="form-control" />';
                $html .= '</div>';
                $html .= '<div class="">
                <input type="text" name="total[]" placeholder="Total" value="" readonly class="form-control" />';
                $html .= '</div>';
                $html .= '<a href="javascript:void(0);" class="removeproduct_button" title="Remove field" style="position:relative; top:8px; left:15px"><i class="fa fa-trash"></i></a>
            </div>';
            echo $html;
        exit;
    }

    public function prodgetvaraiton($id,$curncy) {
        $this->load->model('currencies_model');
        if($curncy) {
            $cur = $curncy;
        } else {
            $currency = $this->currencies_model->get_base_currency();
            $cur = $currency->name;
        }
        
        return $data = $this->products_model->getVariationfieldbyid($cur, $id);
    }

    public function getemptyproduct() {
        //$data = $this->products_model->getprod_price($cur);
        $discount_value = 0;
        $methode = 1;
        $this->load->model('currencies_model');
        $currency = $this->currencies_model->get_base_currency();
        $curId = $currency->id;
        $cur = $currency->name;
        //pre($currency);
        $result['curId'] = $curId;
        $data = $this->products_model->getitem_price($cur);
        $discount_option = get_option('product_discount_option');
        
        $result['product_index'] = 1;
        $result['currency'] = $cur;
        $result['discount_value'] = $discount_value;
        $result['discount_option'] = $discount_option;
        $result['methode'] = $methode;

        //pre($data);
        $html = '';
        $length = 0;
        $html .= '<div style="height:40px; clear:both;" class="productdiv css-table-row" id="'.$length.'"><div class=" wcb">
        <input type="hidden" name="no[]" value="'.$length.'">
                        <input type="hidden" name="status_'.$length.'" value="1" class="form-control cbox">
        <select name="product[]" class="form-control" onchange="getdealprodprice(this,'.$length.')"><option value="">--Select Item--</option>';
        foreach($data as $val) {
                $html .= '<option value="'.$val["id"].'"  >'.$val["name"].'</option>';
        } 
        $html .= '</select>';
        $html .= '</div>';
        $html .=get_particulars_item_ordered_inputs($length);
        $html .='<div class="">
        <input type="text" name="price[]" placeholder="Price" value="" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$length.')" class="form-control" />';
        $html .= '</div>';
        $html .= '<div class="">
        <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,'.$length.')" value=""  class="form-control" />';
        $html .= '</div>';
        if($discount_value == 1 || $discount_option == 1) {
            $html .= '<div class="">
                <input type="number" name="discount[]" placeholder="Discount" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,'.$length.')" value=""  class="form-control" />';
                $html .= '</div>';
        }
        $html .= '<div class="">
        <input type="text" name="total[]" placeholder="Total" value="" readonly class="form-control" />';
        $html .= '</div>';
        
        // $html .= '<span class="dropdown">
        // <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
        // <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
        //     <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
        //     <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
        //     <li><a class="dropdown-item" id="variationbtn_'.$length.'" href="#" onClick="selectVariation('.$length.');">Select Variation</a></li>
        // </ul>
        // </span>';
        $html .= '<span class="dropdown">
        <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
            <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
            <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
        </ul>
        </span>';
        
        $html .= '</div>';
        $result['html'] = $html;
        echo json_encode($result);
    }

    public function getpropsalproduct() {
        //$data = $this->products_model->getprod_price($cur);
        $result = array();
        $getProds = $this->products_model->getproposalprods();
        $result['productscnt'] = count($getProds);
        $discount_value = 0;
        $cur = '';
        $methode = 1;
        foreach($getProds as $prod) {
            if($prod['discount'] > 0) {
                $discount_value = 1;
            }
            $cur = $prod['currency'];
            $methode = $prod['method'];
        }
        $this->load->model('currencies_model');
        //pre($_POST);
        if(isset($cur) && $cur != '') {
            if(!is_numeric($cur)) {
                $currency = $this->currencies_model->get_by_name($cur);
                $curId = $currency->id;
            } else {
                $curId = $cur;
            }
        } else {
            $currency = $this->currencies_model->get_base_currency();
            $curId = $currency->id;
            $cur = $currency->name;
        }
        //pre($currency);
        $result['curId'] = $curId;
        $data = $this->products_model->getitem_price($cur);
        $discount_option = get_option('product_discount_option');
        
        $result['discount_option'] = $discount_option;
        $result['currency'] = $cur;
        $result['discount_value'] = $discount_value;
        $result['discount_option'] = $discount_option;
        $result['methode'] = $methode;

        //pre($data);
        $html = '';
        if(!empty($getProds)) {
            $i = 0;
            foreach($getProds as $prod) {
                //echo $prod['productid']; exit;
                if($methode == 1) {
                    $checked = '';
                    if($prod['status'] == 1) {
                        $checked = 'checked';
                    }
                    $html .= '<div style="height:40px; clear:both" class="productdiv css-table-row" id="'.$i.'"><div class="wcb">
                    <input type="hidden" name="no[]" value="'.$i.'">
                                <input type="hidden" name="status_'.$i.'" value="1" class="form-control cbox" '.$checked.' >
                    <select name="product[]" class="form-control" onchange="getdealprodprice(this,'.$i.')"><option value="">--Select Item--</option>';
                    foreach($data as $val) {
                        $selected = '';
                        if($val["id"] == $prod['productid']) {
                            $selected = 'selected';
                        }
                            $html .= '<option value="'.$val["id"].'"  '.$selected.'>'.$val["name"].'</option>';
                    } 
                    $html .= '</select>';
                    $html .= '</div>';
                    $html .=get_particulars_item_ordered_inputs($i,$prod['productid']);
                    $html .='<div class="">
                    <input type="text" name="price[]" placeholder="Price" value="'.$prod['price'].'" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$i.')" class="form-control" />';
                        $html .= '</div>';
                        $html .= '<div class="">
                        <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,'.$i.')" value="'.$prod['quantity'].'"  class="form-control" />';
                        $html .= '</div>';
                        if($discount_value == 1 || $discount_option == 1) {
                            $html .= '<div class="">
                            <input type="number" name="discount[]" placeholder="Discount" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,'.$i.')" value="'.$prod['discount'].'"  class="form-control" />';
                            $html .= '</div>';
                        }
                        $html .= '<div class="">
                        <input type="text" name="total[]" placeholder="Total" value="'.$prod['total_price'].'" readonly class="form-control" />';
                        $html .= '</div>';
                        
                    //     $html .= '<span class="dropdown">
                    //     <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                    //     <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                    //     <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                    //     <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                    //     <li><a class="dropdown-item" id="variationbtn_'.$i.'" href="#" onClick="selectVariation('.$i.');">Select Variation</a></li>
                    //     </ul>
                    // </span>';
                    $html .= '<span class="dropdown">
                        <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                        <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                        </ul>
                    </span>';
                    if($prod['variation']) {
                            $html .= '<div class="" id="variation_'.$i.'" style="width: 23.3%;margin: 4px 19px 15px;">
                            <label>VARIATION</label>
                            <select name="variation_'.$i.'" class="form-control" onchange="getvariationprodprice(this,'.$i.')">
                            <option value="">--Select Variation--</option>';
                            $vari = $this->prodgetvaraiton($prod['productid'],$cur) ;
                            foreach($vari as $val) {
                                $selected = '';
                                if($val["id"] == $prod['variation']) {
                                $selected = 'selected';
                                }
                                $html .= '<option value="'.$val["id"].'" '.$selected.'>'.$val["name"].'</option>';
                            } 
                        
                            $html .= '</select></div><style>#variationbtn_'.$i.'{pointer-events: none; cursor: default;}</style>';
                        }
                    $html .= '</div>';
                }
                if($methode == 2 || $methode == 3) {
                    $checked = '';
                    if($prod['status'] == 1) {
                        $checked = 'checked';
                    }
                    $this->db->where('item_id', $prod['productid']);
                    $this->db->where('currency', $cur);
                    //$unitprice = $this->db->get(db_prefix() . 'unit_price')->result_array();
                      $this->db->join(db_prefix() . 'taxes', db_prefix() . 'taxes.id=' . db_prefix() . 'item_price.tax');
                    $unitprice = $this->db->get(db_prefix() . 'item_price')->result_array();
                    $html .= '<div style="height:40px; clear:both" class="productdiv css-table-row" id="'.$i.'"><div class="wcb" style="width:20%;">
                    <input type="hidden" name="no[]" value="'.$i.'">
                                <input type="hidden" name="status_'.$i.'" value="1" class="form-control cbox" '.$checked.' >
                    <select name="product[]" class="form-control" onchange="getdealprodprice(this,'.$i.')"><option value="">--Select Item--</option>';
                    foreach($data as $val) {
                        $selected = '';
                        if($val["id"] == $prod['productid']) {
                            $selected = 'selected';
                        }
                            $html .= '<option value="'.$val["id"].'"  '.$selected.'>'.$val["name"].'</option>';
                    } 
                    $html .= '</select>';
                    $html .= '</div>';
                    $html .=get_particulars_item_ordered_inputs($i,$prod['productid']);
                    $html .='<div class="">
                    <input type="text" name="price[]" placeholder="Price" value="'.$prod['price'].'" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$i.')" class="form-control" />';
                        $html .= '</div>';
                        $html .= '<div class="">
                        <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,'.$i.')" value="'.$prod['quantity'].'"  class="form-control" />';
                        $html .= '</div>';
                        $tax = (isset($prod['tax']) && $prod['tax'] > 0) ? $prod['tax'] : 0;
                        if($tax < 1)
                            $tax = (isset($unitprice[0]['taxrate']) && $unitprice[0]['taxrate'] > 0) ? $unitprice[0]['taxrate'] : 0;
                        
                        $html .= '<div class="">
                        <input type="number" name="tax[]" placeholder="Tax" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="tax_total(this,'.$i.')" value="'.$tax.'"  class="form-control" />';
                        $html .= '</div>';
                        if($discount_value == 1 || $discount_option == 1) {
                            $html .= '<div class="">
                            <input type="number" name="discount[]" placeholder="Discount" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,'.$i.')" value="'.$prod['discount'].'"  class="form-control" />';
                            $html .= '</div>';
                        }
                        $html .= '<div class="">
                        <input type="text" name="total[]" placeholder="Total" value="'.$prod['total_price'].'" readonly class="form-control" />';
                        $html .= '</div>';
                        
                    //     $html .= '<span class="dropdown">
                    //     <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                    //     <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                    //       <li><a class="dropdown-item" href="#" onClick="gotoprod('.$i.');">Go to Product</a></li>
                    //       <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                    //       <li><a class="dropdown-item" id="variationbtn_'.$i.'" href="#" onClick="selectVariation('.$i.');">Select Variation</a></li>
                    //     </ul>
                    //   </span>';
                    $html .= '<span class="dropdown">
                        <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                          <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                          <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                        </ul>
                      </span>';
                      if($prod['variation']) {
                      $html .= '<div class="" id="variation_'.$i.'" style="width: 18.7%;margin: 4px 15px 15px;clear:both;">
                      <label>VARIATION</label>
                      <select name="variation_'.$i.'" class="form-control" onchange="getvariationprodprice(this,'.$i.')">
                      <option value="">--Select Variation--</option>';
                            $vari = $this->prodgetvaraiton($prod['productid'],$cur) ;
                            foreach($vari as $val) {
                                $selected = '';
                                if($val["id"] == $prod['variation']) {
                                $selected = 'selected';
                                }
                                $html .= '<option value="'.$val["id"].'" '.$selected.'>'.$val["name"].'</option>';
                            } 
                        
                        $html .= '</select></div><style>#variationbtn_'.$i.'{pointer-events: none; cursor: default;}</style>';
                        }
                      $html .= '</div>';
                }
                $i++;
            }
        } else {
            $length = 0;
            $discount_option = get_option('product_discount_option');
            $html .= '<div style="height:40px; clear:both;" class="productdiv css-table-row" id="'.$length.'"><div class="wcb">
            <input type="hidden" name="no[]" value="'.$length.'">
                            <input type="hidden" name="status_'.$length.'" value="1" class="form-control cbox">
            <select name="product[]" class="form-control" onchange="getdealprodprice(this,'.$length.')"><option value="">--Select Item--</option>';
            foreach($data as $val) {
                    $html .= '<option value="'.$val["id"].'"  >'.$val["name"].'</option>';
            } 
            $html .= '</select>';
            $html .= '</div>';
            $html .=get_particulars_item_ordered_inputs($length);
            $html .='<div class="">
            <input type="text" name="price[]" placeholder="Price" value="" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$length.')" class="form-control" />';
                $html .= '</div>';
                $html .= '<div class="">
                <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,'.$length.')" value=""  class="form-control" />';
                $html .= '</div>';
                if($discount_value == 1 || $discount_option == 1) {
                    $html .= '<div class="">
                        <input type="number" name="discount[]" placeholder="Discount" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,'.$length.')" value=""  class="form-control" />';
                        $html .= '</div>';
                }
                $html .= '<div class="">
                <input type="text" name="total[]" placeholder="Total" value="" readonly class="form-control" />';
                $html .= '</div>';
                
            //     $html .= '<span class="dropdown">
            //     <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
            //     <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
            //       <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
            //       <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
            //       <li><a class="dropdown-item" id="variationbtn_'.$length.'" href="#" onClick="selectVariation('.$length.');">Select Variation</a></li>
            //     </ul>
            //   </span>';
            $html .= '<span class="dropdown">
                <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                  <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                  <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                </ul>
              </span>';
              
              $html .= '</div>';
        }
        $result['html'] = $html;
        echo json_encode($result);

        exit;
    }

    public function getinvoiceproduct() {
        //$data = $this->products_model->getprod_price($cur);
        $result = array();
        $getProds = $this->products_model->getinvoiceprods();
        $result['productscnt'] = count($getProds);
        $discount_value = 0;
        $cur = '';
        $methode = 1;
        foreach($getProds as $prod) {
            if($prod['discount'] > 0) {
                $discount_value = 1;
            }
            $cur = $prod['currency'];
            $methode = $prod['method'];
        }
        $this->load->model('currencies_model');
        //pre($_POST);
        if(isset($cur) && $cur != '') {
            if(!is_numeric($cur)) {
                $currency = $this->currencies_model->get_by_name($cur);
                $curId = $currency->id;
            } else {
                $curId = $cur;
            }
        } else {
            $currency = $this->currencies_model->get_base_currency();
            $curId = $currency->id;
            $cur = $currency->name;
        }
        //pre($currency);
        $result['curId'] = $curId;
        $data = $this->products_model->getitem_price($cur);
        $discount_option = get_option('product_discount_option');
        
        $result['discount_option'] = $discount_option;
        $result['currency'] = $cur;
        $result['discount_value'] = $discount_value;
        $result['discount_option'] = $discount_option;
        $result['methode'] = $methode;

        //pre($data);
        $html = '';
        if(!empty($getProds)) {
            $i = 0;
            foreach($getProds as $prod) {
                //echo $prod['productid']; exit;
                if($methode == 1) {
                    $checked = '';
                    if($prod['status'] == 1) {
                        $checked = 'checked';
                    }
                    $html .= '<div style="height:40px; clear:both" class="productdiv css-table-row" id="'.$i.'"><div class="wcb">
                    <input type="hidden" name="no[]" value="'.$i.'">
                                <input type="hidden" name="status_'.$i.'" value="1" class="form-control cbox" '.$checked.' >
                    <select name="product[]" class="form-control" onchange="getdealprodprice(this,'.$i.')"><option value="">--Select Item--</option>';
                    foreach($data as $val) {
                        $selected = '';
                        if($val["id"] == $prod['productid']) {
                            $selected = 'selected';
                        }
                            $html .= '<option value="'.$val["id"].'"  '.$selected.'>'.$val["name"].'</option>';
                    } 
                    $html .= '</select>';
                    $html .= '</div>';
                    $html .=get_particulars_item_ordered_inputs($i,$prod['productid']);
                    $html .='<div class="">
                    <input type="text" name="price[]" placeholder="Price" value="'.$prod['price'].'" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$i.')" class="form-control" />';
                        $html .= '</div>';
                        $html .= '<div class="">
                        <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,'.$i.')" value="'.$prod['quantity'].'"  class="form-control" />';
                        $html .= '</div>';
                        if($discount_value == 1 || $discount_option == 1) {
                            $html .= '<div class="">
                            <input type="number" name="discount[]" placeholder="Discount" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,'.$i.')" value="'.$prod['discount'].'"  class="form-control" />';
                            $html .= '</div>';
                        }
                        $html .= '<div class="">
                        <input type="text" name="total[]" placeholder="Total" value="'.$prod['total_price'].'" readonly class="form-control" />';
                        $html .= '</div>';
                        
                    //     $html .= '<span class="dropdown">
                    //     <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                    //     <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                    //     <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                    //     <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                    //     <li><a class="dropdown-item" id="variationbtn_'.$i.'" href="#" onClick="selectVariation('.$i.');">Select Variation</a></li>
                    //     </ul>
                    // </span>';
                    $html .= '<span class="dropdown">
                        <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                        <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                        </ul>
                    </span>';
                    if($prod['variation']) {
                            $html .= '<div class="" id="variation_'.$i.'" style="width: 23.3%;margin: 4px 19px 15px;">
                            <label>VARIATION</label>
                            <select name="variation_'.$i.'" class="form-control" onchange="getvariationprodprice(this,'.$i.')">
                            <option value="">--Select Variation--</option>';
                            $vari = $this->prodgetvaraiton($prod['productid'],$cur) ;
                            foreach($vari as $val) {
                                $selected = '';
                                if($val["id"] == $prod['variation']) {
                                $selected = 'selected';
                                }
                                $html .= '<option value="'.$val["id"].'" '.$selected.'>'.$val["name"].'</option>';
                            } 
                        
                            $html .= '</select></div><style>#variationbtn_'.$i.'{pointer-events: none; cursor: default;}</style>';
                        }
                    $html .= '</div>';
                }
                if($methode == 2 || $methode == 3) {
                    $checked = '';
                    if($prod['status'] == 1) {
                        $checked = 'checked';
                    }
                    $this->db->where('item_id', $prod['productid']);
                    $this->db->where('currency', $cur);
                    //$unitprice = $this->db->get(db_prefix() . 'unit_price')->result_array();
                      $this->db->join(db_prefix() . 'taxes', db_prefix() . 'taxes.id=' . db_prefix() . 'item_price.tax');
                    $unitprice = $this->db->get(db_prefix() . 'item_price')->result_array();
                    $html .= '<div style="height:40px; clear:both" class="productdiv css-table-row" id="'.$i.'"><div class="wcb">
                    <input type="hidden" name="no[]" value="'.$i.'">
                                <input type="hidden" name="status_'.$i.'" value="1" class="form-control cbox" '.$checked.' >
                    <select name="product[]" class="form-control" onchange="getdealprodprice(this,'.$i.')"><option value="">--Select Item--</option>';
                    foreach($data as $val) {
                        $selected = '';
                        if($val["id"] == $prod['productid']) {
                            $selected = 'selected';
                        }
                            $html .= '<option value="'.$val["id"].'"  '.$selected.'>'.$val["name"].'</option>';
                    } 
                    $html .= '</select>';
                    $html .= '</div>';
                    $html .=get_particulars_item_ordered_inputs($i,$prod['productid']);
                    $html .='<div class="">
                    <input type="text" name="price[]" placeholder="Price" value="'.$prod['price'].'" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$i.')" class="form-control" />';
                        $html .= '</div>';
                        $html .= '<div class="">
                        <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,'.$i.')" value="'.$prod['quantity'].'"  class="form-control" />';
                        $html .= '</div>';
                        $tax = (isset($prod['tax']) && $prod['tax'] > 0) ? $prod['tax'] : 0;
                        if($tax < 1)
                            $tax = (isset($unitprice[0]['taxrate']) && $unitprice[0]['taxrate'] > 0) ? $unitprice[0]['taxrate'] : 0;
                        
                        $html .= '<div class="">
                        <input type="number" name="tax[]" placeholder="Tax" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="tax_total(this,'.$i.')" value="'.$tax.'"  class="form-control" />';
                        $html .= '</div>';
                        if($discount_value == 1 || $discount_option == 1) {
                            $html .= '<div class="">
                            <input type="number" name="discount[]" placeholder="Discount" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,'.$i.')" value="'.$prod['discount'].'"  class="form-control" />';
                            $html .= '</div>';
                        }
                        $html .= '<div class="">
                        <input type="text" name="total[]" placeholder="Total" value="'.$prod['total_price'].'" readonly class="form-control" />';
                        $html .= '</div>';
                        
                    //     $html .= '<span class="dropdown">
                    //     <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                    //     <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                    //       <li><a class="dropdown-item" href="#" onClick="gotoprod('.$i.');">Go to Product</a></li>
                    //       <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                    //       <li><a class="dropdown-item" id="variationbtn_'.$i.'" href="#" onClick="selectVariation('.$i.');">Select Variation</a></li>
                    //     </ul>
                    //   </span>';
                    $html .= '<span class="dropdown">
                        <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                          <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                          <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                        </ul>
                      </span>';
                      if($prod['variation']) {
                      $html .= '<div class="" id="variation_'.$i.'" style="width: 18.7%;margin: 4px 15px 15px;clear:both;">
                      <label>VARIATION</label>
                      <select name="variation_'.$i.'" class="form-control" onchange="getvariationprodprice(this,'.$i.')">
                      <option value="">--Select Variation--</option>';
                            $vari = $this->prodgetvaraiton($prod['productid'],$cur) ;
                            foreach($vari as $val) {
                                $selected = '';
                                if($val["id"] == $prod['variation']) {
                                $selected = 'selected';
                                }
                                $html .= '<option value="'.$val["id"].'" '.$selected.'>'.$val["name"].'</option>';
                            } 
                        
                        $html .= '</select></div><style>#variationbtn_'.$i.'{pointer-events: none; cursor: default;}</style>';
                        }
                      $html .= '</div>';
                }
                $i++;
            }
        } else {
            $length = 0;
            $discount_option = get_option('product_discount_option');
            $html .= '<div style="height:40px; clear:both;" class="productdiv css-table-row" id="'.$length.'"><div class="wcb">
            <input type="hidden" name="no[]" value="'.$length.'">
                            <input type="hidden" name="status_'.$length.'" value="1" class="form-control cbox">
            <select name="product[]" class="form-control" onchange="getdealprodprice(this,'.$length.')"><option value="">--Select Item--</option>';
            foreach($data as $val) {
                    $html .= '<option value="'.$val["id"].'"  >'.$val["name"].'</option>';
            } 
            $html .= '</select>';
            $html .= '</div>';
            $html .=get_particulars_item_ordered_inputs($length);
            $html .='<div class="">
            <input type="text" name="price[]" placeholder="Price" value="" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$length.')" class="form-control" />';
                $html .= '</div>';
                $html .= '<div class="">
                <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,'.$length.')" value=""  class="form-control" />';
                $html .= '</div>';
                if($discount_value == 1 || $discount_option == 1) {
                    $html .= '<div class="">
                        <input type="number" name="discount[]" placeholder="Discount" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,'.$length.')" value=""  class="form-control" />';
                        $html .= '</div>';
                }
                $html .= '<div class="">
                <input type="text" name="total[]" placeholder="Total" value="" readonly class="form-control" />';
                $html .= '</div>';
                
            //     $html .= '<span class="dropdown">
            //     <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
            //     <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
            //       <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
            //       <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
            //       <li><a class="dropdown-item" id="variationbtn_'.$length.'" href="#" onClick="selectVariation('.$length.');">Select Variation</a></li>
            //     </ul>
            //   </span>';
            $html .= '<span class="dropdown">
                <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                  <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                  <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                </ul>
              </span>';
              
              $html .= '</div>';
        }
        $result['html'] = $html;
        echo json_encode($result);

        exit;
    }

    public function getdealproduct() {
        //$data = $this->products_model->getprod_price($cur);
        $result = array();
        $getProds = $this->products_model->getdealprods();
        $result['productscnt'] = (!empty($getProds))?count($getProds):0;
        $discount_value = 0;
        $cur = '';
        $methode = 1;
        foreach($getProds as $prod) {
            if($prod['discount'] > 0) {
                $discount_value = 1;
            }
            $cur = $prod['currency'];
            $methode = $prod['method'];
        }
        $this->load->model('currencies_model');
        //pre($_POST);
        if(isset($cur) && $cur != '') {
            if(!is_numeric($cur)) {
                $currency = $this->currencies_model->get_by_name($cur);
                $curId = $currency->id;
            } else {
                $curId = $cur;
            }
        } else {
            $currency = $this->currencies_model->get_base_currency();
            $curId = $currency->id;
            $cur = $currency->name;
        }
        //pre($currency);
        $result['curId'] = $curId;
        $data = $this->products_model->getitem_price($cur);
        $discount_option = get_option('product_discount_option');
        
        $result['discount_option'] = $discount_option;
        $result['currency'] = $cur;
        $result['discount_value'] = $discount_value;
        $result['discount_option'] = $discount_option;
        $result['methode'] = $methode;

        //pre($data);
        $html = '';
        if(!empty($getProds)) {
            $i = 0;
            foreach($getProds as $prod) {
                //echo $prod['productid']; exit;
                if($methode == 1) {
                    $checked = '';
                    if($prod['status'] == 1) {
                        $checked = 'checked';
                    }
                    $html .= '<div style="height:40px; clear:both" class="productdiv css-table-row" id="'.$i.'"><div class="wcb">
                    <input type="hidden" name="no[]" value="'.$i.'">
                                <input type="hidden" name="status_'.$i.'" value="1" class="form-control cbox" '.$checked.' >
                    <select name="product[]" class="form-control" onchange="getdealprodprice(this,'.$i.')"><option value="">--Select Item--</option>';
                    foreach($data as $val) {
                        $selected = '';
                        if($val["id"] == $prod['productid']) {
                            $selected = 'selected';
                        }
                            $html .= '<option value="'.$val["id"].'"  '.$selected.'>'.$val["name"].'</option>';
                    } 
                    $html .= '</select>';
                    $html .= '</div>';
                    $html .=get_particulars_item_ordered_inputs($i,$prod['productid']);
                    $html .='<div class="">
                    <input type="text" name="price[]" placeholder="Price" value="'.$prod['price'].'" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$i.')" class="form-control" />';
                        $html .= '</div>';
                        $html .= '<div class="">
                        <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,'.$i.')" value="'.$prod['quantity'].'"  class="form-control" />';
                        $html .= '</div>';
                        if($discount_value == 1 || $discount_option == 1) {
                            $html .= '<div class="">
                            <input type="number" name="discount[]" placeholder="Discount" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,'.$i.')" value="'.$prod['discount'].'"  class="form-control" />';
                            $html .= '</div>';
                        }
                        $html .= '<div class="">
                        <input type="text" name="total[]" placeholder="Total" value="'.$prod['total_price'].'" readonly class="form-control" />';
                        $html .= '</div>';
                        
                    //     $html .= '<span class="dropdown">
                    //     <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                    //     <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                    //     <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                    //     <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                    //     <li><a class="dropdown-item" id="variationbtn_'.$i.'" href="#" onClick="selectVariation('.$i.');">Select Variation</a></li>
                    //     </ul>
                    // </span>';
                    $html .= '<span class="dropdown">
                        <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                        <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                        </ul>
                    </span>';
                    if($prod['variation']) {
                            $html .= '<div class="" id="variation_'.$i.'" style="width: 23.3%;margin: 4px 19px 15px;">
                            <label>VARIATION</label>
                            <select name="variation_'.$i.'" class="form-control" onchange="getvariationprodprice(this,'.$i.')">
                            <option value="">--Select Variation--</option>';
                            $vari = $this->prodgetvaraiton($prod['productid'],$cur) ;
                            foreach($vari as $val) {
                                $selected = '';
                                if($val["id"] == $prod['variation']) {
                                $selected = 'selected';
                                }
                                $html .= '<option value="'.$val["id"].'" '.$selected.'>'.$val["name"].'</option>';
                            } 
                        
                            $html .= '</select></div><style>#variationbtn_'.$i.'{pointer-events: none; cursor: default;}</style>';
                        }
                    $html .= '</div>';
                }
                if($methode == 2 || $methode == 3) {
                    $checked = '';
                    if($prod['status'] == 1) {
                        $checked = 'checked';
                    }
                    $this->db->where('item_id', $prod['productid']);
                    $this->db->where('currency', $cur);
                    //$unitprice = $this->db->get(db_prefix() . 'unit_price')->result_array();
                      $this->db->join(db_prefix() . 'taxes', db_prefix() . 'taxes.id=' . db_prefix() . 'item_price.tax');
                    $unitprice = $this->db->get(db_prefix() . 'item_price')->result_array();
                    $html .= '<div style="height:40px; clear:both" class="productdiv css-table-row" id="'.$i.'"><div class="wcb">
                    <input type="hidden" name="no[]" value="'.$i.'">
                                <input type="hidden" name="status_'.$i.'" value="1" class="form-control cbox" '.$checked.' >
                    <select name="product[]" class="form-control" onchange="getdealprodprice(this,'.$i.')"><option value="">--Select Item--</option>';
                    foreach($data as $val) {
                        $selected = '';
                        if($val["id"] == $prod['productid']) {
                            $selected = 'selected';
                        }
                            $html .= '<option value="'.$val["id"].'"  '.$selected.'>'.$val["name"].'</option>';
                    } 
                    $html .= '</select>';
                    $html .= '</div>';
                    $html .=get_particulars_item_ordered_inputs($i,$prod['productid']);
                    $html .='<div class="">
                    <input type="text" name="price[]" placeholder="Price" value="'.$prod['price'].'" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$i.')" class="form-control" />';
                        $html .= '</div>';
                        $html .= '<div class="">
                        <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,'.$i.')" value="'.$prod['quantity'].'"  class="form-control" />';
                        $html .= '</div>';
                        $tax = (isset($prod['tax']) && $prod['tax'] > 0) ? $prod['tax'] : 0;
                        if($tax < 1)
                            $tax = (isset($unitprice[0]['taxrate']) && $unitprice[0]['taxrate'] > 0) ? $unitprice[0]['taxrate'] : 0;
                        
                        $html .= '<div class="">
                        <input type="number" name="tax[]" placeholder="Tax" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="tax_total(this,'.$i.')" value="'.$tax.'"  class="form-control" />';
                        $html .= '</div>';
                        if($discount_value == 1 || $discount_option == 1) {
                            $html .= '<div class="">
                            <input type="number" name="discount[]" placeholder="Discount" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,'.$i.')" value="'.$prod['discount'].'"  class="form-control" />';
                            $html .= '</div>';
                        }
                        $html .= '<div class="">
                        <input type="text" name="total[]" placeholder="Total" value="'.$prod['total_price'].'" readonly class="form-control" />';
                        $html .= '</div>';
                        
                    //     $html .= '<span class="dropdown">
                    //     <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                    //     <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                    //       <li><a class="dropdown-item" href="#" onClick="gotoprod('.$i.');">Go to Product</a></li>
                    //       <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                    //       <li><a class="dropdown-item" id="variationbtn_'.$i.'" href="#" onClick="selectVariation('.$i.');">Select Variation</a></li>
                    //     </ul>
                    //   </span>';
                    $html .= '<span class="dropdown">
                        <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                            <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                          <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                        </ul>
                      </span>';
                      if($prod['variation']) {
                      $html .= '<div class="" id="variation_'.$i.'" style="width: 18.7%;margin: 4px 15px 15px;clear:both;">
                      <label>VARIATION</label>
                      <select name="variation_'.$i.'" class="form-control" onchange="getvariationprodprice(this,'.$i.')">
                      <option value="">--Select Variation--</option>';
                            $vari = $this->prodgetvaraiton($prod['productid'],$cur) ;
                            foreach($vari as $val) {
                                $selected = '';
                                if($val["id"] == $prod['variation']) {
                                $selected = 'selected';
                                }
                                $html .= '<option value="'.$val["id"].'" '.$selected.'>'.$val["name"].'</option>';
                            } 
                        
                        $html .= '</select></div><style>#variationbtn_'.$i.'{pointer-events: none; cursor: default;}</style>';
                        }
                      $html .= '</div>';
                }
                $i++;
            }
        } else {
            $length = 0;
            $discount_option = get_option('product_discount_option');
            $html .= '<div style="height:40px; clear:both;" class="productdiv css-table-row" id="'.$length.'"><div class="wcb">
            <input type="hidden" name="no[]" value="'.$length.'">
                            <input type="hidden" name="status_'.$length.'" value="1" class="form-control cbox">
            <select name="product[]" class="form-control" onchange="getdealprodprice(this,'.$length.')"><option value="">--Select Item--</option>';
            foreach($data as $val) {
                    $html .= '<option value="'.$val["id"].'"  >'.$val["name"].'</option>';
            } 
            $html .= '</select>';
            $html .= '</div>';
            $html .=get_particulars_item_ordered_inputs($length);
            $html .='<div class="">
            <input type="text" name="price[]" placeholder="Price" value="" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$length.')" class="form-control" />';
                $html .= '</div>';
                $html .= '<div class="">
                <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,'.$length.')" value=""  class="form-control" />';
                $html .= '</div>';
                if($discount_value == 1 || $discount_option == 1) {
                    $html .= '<div class="">
                        <input type="number" name="discount[]" placeholder="Discount" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,'.$length.')" value=""  class="form-control" />';
                        $html .= '</div>';
                }
                $html .= '<div class="">
                <input type="text" name="total[]" placeholder="Total" value="" readonly class="form-control" />';
                $html .= '</div>';
                
            //     $html .= '<span class="dropdown">
            //     <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
            //     <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
            //       <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
            //       <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
            //       <li><a class="dropdown-item" id="variationbtn_'.$length.'" href="#" onClick="selectVariation('.$length.');">Select Variation</a></li>
            //     </ul>
            //   </span>';
            $html .= '<span class="dropdown">
                <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                  <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                  <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                </ul>
              </span>';
              
              $html .= '</div>';
        }
        $result['html'] = $html;
        echo json_encode($result);

        exit;
    }

    public function getdealproductfields() {
        $this->load->model('currencies_model');
        if(isset($_POST['currency'])) {
            if(is_numeric($_POST['currency'])) {
                $currency = $this->currencies_model->get($_POST['currency']);
                $cur = $currency->name;
            } else {
                $cur = $_POST['currency'];
            }
        } else {
            $currency = $this->currencies_model->get_base_currency();
            $cur = $currency->name;
        }
        //$data = $this->products_model->getprod_price($cur);
        $data = $this->products_model->getitem_price($cur);
        $getProds = $this->products_model->getnotaxprods($cur);
        $discount_value = 0;
        foreach($getProds as $prod) {
            if($prod['discount'] > 0) {
                $discount_value = 1;
            }
        }
        $discount_option = get_option('product_discount_option');
        
        //pre($getProds);
        $html = '';
        if(!empty($getProds)) {
            $i = 0;
            foreach($getProds as $prod) {
                //echo $prod['productid']; exit;
                $checked = '';
                if($prod['status'] == 1) {
                    $checked = 'checked';
                }
                $html .= '<div style="height:40px; clear:both" class=" css-table-row" id="'.$i.'"><div class="wcb">
                <input type="hidden" name="no[]" value="'.$i.'">';
                            // <input type="hidden" name="status_'.$i.'" value="1" class="form-control cbox" '.$checked.' >
                $html .='<select name="product[]" class="form-control" onchange="getdealprodprice(this,'.$i.')"><option value="">--Select Item--</option>';
                foreach($data as $val) {
                    $selected = '';
                    if($val["id"] == $prod['productid']) {
                        $selected = 'selected';
                    }
                        $html .= '<option value="'.$val["id"].'"  '.$selected.'>'.$val["name"].'</option>';
                } 
                $html .= '</select>';
                $html .= '</div>';
                $html .=get_particulars_item_ordered_inputs($i,$prod['productid']);
                $html .='
                <div class="">
                <input type="text" name="price[]" placeholder="Price" value="'.$prod['price'].'" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$i.')" class="form-control" />';
                    $html .= '</div>';
                    $html .= '<div class="">
                    <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,'.$i.')" value="'.$prod['quantity'].'"  class="form-control" />';
                    $html .= '</div>';
                    if($discount_value == 1 || $discount_option == 1) {
                        $html .= '<div class="">
                        <input type="number" name="discount[]" placeholder="Discount" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,'.$i.')" value="'.$prod['discount'].'"  class="form-control" />';
                        $html .= '</div>';
                    }
                    $html .= '<div class="">
                    <input type="text" name="total[]" placeholder="Total" value="'.$prod['total_price'].'" readonly class="form-control" />';
                    $html .= '</div>';
                    
                //     $html .= '<span class="dropdown">
                //     <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                //     <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                //       <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                //       <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                //       <li><a class="dropdown-item" id="variationbtn_'.$i.'" href="#" onClick="selectVariation('.$i.');">Select Variation</a></li>
                //     </ul>
                //   </span>';
                $html .= '<span class="dropdown">
                    <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                      <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                      <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                    </ul>
                  </span>';
                  if($prod['variation']) {
                        $html .= '<div class="" id="variation_'.$i.'" style="width: 23.3%;margin: 4px 19px 15px;">
                        <label>VARIATION</label>
                        <select name="variation_'.$i.'" class="form-control" onchange="getvariationprodprice(this,'.$i.')">
                        <option value="">--Select Variation--</option>';
                        $vari = $this->prodgetvaraiton($prod['productid'],$cur) ;
                        foreach($vari as $val) {
                            $selected = '';
                            if($val["id"] == $prod['variation']) {
                            $selected = 'selected';
                            }
                            $html .= '<option value="'.$val["id"].'" '.$selected.'>'.$val["name"].'</option>';
                        } 
                    
                        $html .= '</select></div><style>#variationbtn_'.$i.'{pointer-events: none; cursor: default;}</style>';
                    }
                  $html .= '</div>';
                $i++;
            }

        } else {
            $getProds = $this->products_model->getprods($cur);
            $discount_value = 0;
            foreach($getProds as $prod) {
                if($prod['discount'] > 0) {
                    $discount_value = 1;
                }
            }
            $discount_option = get_option('product_discount_option');
            $html .= '<div style="height:40px; clear:both;" class="productdiv css-table-row" id="'.$_POST['length'].'"><div class="wcb">
            <input type="hidden" name="no[]" value="'.$_POST['length'].'">';
                            // <input type="hidden" name="status_'.$_POST['length'].'" value="1" class="form-control cbox">
            $html .='<select name="product[]" class="form-control" onchange="getdealprodprice(this,'.$_POST['length'].')"><option value="">--Select Item--</option>';
            foreach($data as $val) {
                    $html .= '<option value="'.$val["id"].'">'.$val["name"].'</option>';
            } 
            $html .= '</select>';
            $html .= '</div>';

            $html .=get_particulars_item_ordered_inputs($_POST['length']);

            $html .='<div class="">
            <input type="text" name="price[]" placeholder="Price" value="" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$_POST['length'].')" class="form-control" />';
                $html .= '</div>';
                $html .= '<div class="">
                <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,'.$_POST['length'].')" value=""  class="form-control" />';
                $html .= '</div>';
                if($discount_value == 1 || $discount_option == 1) {
                    $html .= '<div class="">
                        <input type="number" name="discount[]" placeholder="Discount" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,'.$_POST['length'].')" value=""  class="form-control" />';
                        $html .= '</div>';
                }
                $html .= '<div class="">
                <input type="text" name="total[]" placeholder="Total" value="" readonly class="form-control" />';
                $html .= '</div>';
                
            //     $html .= '<span class="dropdown">
            //     <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
            //     <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
            //       <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
            //       <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
            //       <li><a class="dropdown-item" id="variationbtn_'.$_POST['length'].'" href="#" onClick="selectVariation('.$_POST['length'].');">Select Variation</a></li>
            //     </ul>
            //   </span>';
            $html .= '<span class="dropdown">
                <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                  <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                  <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                </ul>
              </span>';
              
              $html .= '</div>';
        }
            echo $html;

        exit;
    }

    public function getintaxfields() {
        $this->load->model('currencies_model');
        if(isset($_POST['currency'])) {
            if(is_numeric($_POST['currency'])) {
                $currency = $this->currencies_model->get($_POST['currency']);
                $cur = $currency->name;
            } else {
                $cur = $_POST['currency'];
            }
        } else {
            $currency = $this->currencies_model->get_base_currency();
            $cur = $currency->name;
        }
       // $data = $this->products_model->getprod_price($cur);
		$data = $this->products_model->getitem_price($cur);
        $getProds = $this->products_model->gettaxprods();
        $discount_value = 0;
        foreach($getProds as $prod) {
            if($prod['discount'] > 0) {
                $discount_value = 1;
            }
        }
        $discount_option = get_option('product_discount_option');
        
        //pre($getProds);
        // echo count($getProds);
        // echo $_POST['length'];
        // exit;
        $html = '';
        // if(!empty($getProds)) {
        //     $i = 0;
        //     foreach($getProds as $prod) {
        //         $html .= '<div style="height:40px; clear:both" class="productdiv" id="'.$i.'"><div class="col-md-3">
        //         <select name="product[]" class="form-control" onchange="getdealprodprice(this,'.$i.')"><option value="">--Select Product--</option>';
        //         foreach($data as $val) {
        //             $selecetd = '';
        //             if($val["id"] == $prod['productid']) {
        //                 $selected = 'selected';
        //             }
        //                 $html .= '<option value="'.$val["id"].'"  '.$selected.'>'.$val["name"].'</option>';
        //         } 
        //         $html .= '</select>';
        //         $html .= '</div>
        //         <div class="col-md-2">
        //         <input type="text" name="price[]" placeholder="Price" value="'.$prod['price'].'" readonly class="form-control" />';
        //             $html .= '</div>';
        //             $html .= '<div class="col-md-2">
        //             <input type="number" name="qty[]" placeholder="Qty" min="1" onchange="qty_total(this,'.$i.')" value="'.$prod['quantity'].'"  class="form-control" />';
        //             $html .= '</div>';
        //             $tax = ($prod['tax'] > 0) ? $prod['tax'] : '';
        //             $html .= '<div class="col-md-2">
        //             <input type="number" name="tax[]" placeholder="Tax" min="0" onchange="tax_total(this,'.$i.')" value="'.$tax.'"  class="form-control" />';
        //             $html .= '</div>';
        //             $html .= '<div class="col-md-2">
        //             <input type="text" name="total[]" placeholder="Total" value="'.$prod['total_price'].'" readonly class="form-control" />';
        //             $html .= '</div>';
        //             $html .= '<a href="javascript:void(0);" class="removeproduct_button" title="Remove field" style="position:relative; top:8px; left:15px"><i class="fa fa-trash"></i></a>
        //         </div>';
        //         $i++;
        //     }

        // } else {
            $html .= '<div style="height:40px; clear:both" class="productdiv css-table-row" id="'.$_POST['length'].'"><div class="wcb">
            <input type="hidden" name="no[]" value="'.$_POST['length'].'">
                            <input type="hidden" name="status_'.$_POST['length'].'" value="1" class="form-control cbox">
            <select name="product[]" class="form-control" onchange="getdealprodprice(this,'.$_POST['length'].')"><option value="">--Select Item--</option>';
            foreach($data as $val) {
                    $html .= '<option value="'.$val["id"].'">'.$val["name"].'</option>';
            } 
            $html .= '</select>';
            $html .= '</div>';
            $html .=get_particulars_item_ordered_inputs($_POST['length']);
            $html .='<div class="">
            <input type="text" name="price[]" placeholder="Price" value="" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$_POST['length'].')" class="form-control" />';
                $html .= '</div>';
                $html .= '<div class="">
                <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,'.$_POST['length'].')" value=""  class="form-control" />';
                $html .= '</div>';
                $html .= '<div class="">
                <input type="number" name="tax[]" placeholder="Tax" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="tax_total(this,'.$_POST['length'].')" value=""  class="form-control" />';
                $html .= '</div>';
                if($discount_value == 1 || $discount_option == 1) {
                    $html .= '<div class="">
                    <input type="number" name="discount[]" placeholder="Discount" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,'.$_POST['length'].')" value=""  class="form-control" />';
                    $html .= '</div>';
                }
                $html .= '<div class="">
                <input type="text" name="total[]" placeholder="Total" value="" readonly class="form-control" />';
                $html .= '</div>';
                
            //     $html .= '<span class="dropdown">
            //     <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
            //     <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
            //       <li><a class="dropdown-item" href="#" onClick="gotoprod('.$_POST['length'].');">Go to Product</a></li>
            //       <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
            //       <li><a class="dropdown-item" id="variationbtn_'.$_POST['length'].'" href="#" onClick="selectVariation('.$_POST['length'].');">Select Variation</a></li>
            //     </ul>
            //   </span>';
            $html .= '<span class="dropdown">
                <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                  <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                  <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                </ul>
              </span>';
            //   if($prod['variation']) {
            //         $html .= '<div class="col-md-2" id="variation_'.$_POST['length'].'" style="width: 18.7%;margin: 4px 15px 15px;">
            //         <label>VARIATION</label>
            //         <select name="variation_'.$_POST['length'].'" class="form-control" onchange="getvariationprodprice(this,'.$_POST['length'].')">
            //       <option value="">--Select Variation--</option>';
            //             $vari = $this->prodgetvaraiton($prod['productid'],$cur) ;
            //             foreach($vari as $val) {
            //                 $selected = '';
            //                 if($val["id"] == $prod['variation']) {
            //                 $selected = 'selected';
            //                 }
            //                 $html .= '<option value="'.$val["id"].'" '.$selected.'>'.$val["name"].'</option>';
            //             } 
                    
            //         $html .= '</select></div><style>#variationbtn_'.$_POST['length'].'{pointer-events: none; cursor: default;}</style>';
            //     }
              $html .= '</div>';
        //}
            echo $html;
        exit;
    }

    public function getsalesextaxfields() {
        $this->load->model('currencies_model');
        //pre($_POST);
        if(isset($_POST['currency'])) {
            if(is_numeric($_POST['currency'])) {
                $currency = $this->currencies_model->get($_POST['currency']);
                $cur = $currency->name;
            } else {
                $cur = $_POST['currency'];
            }
        } else {
            $currency = $this->currencies_model->get_base_currency();
            $cur = $currency->name;
        }
        //echo $cur; exit;
        //$data = $this->products_model->getprod_price($cur);
        $data = $this->products_model->getitem_price($cur);
        $getProds = $this->products_model->getsalesextaxprods($cur);
        $discount_value = 0;
        foreach($getProds as $prod) {
            if($prod['discount'] > 0) {
                $discount_value = 1;
            }
        }
        $discount_option = get_option('product_discount_option');

        //pre($getProds);
        $html = '';
        if(!empty($getProds)) {
            $i = 0;
            foreach($getProds as $prod) {
                $checked = '';
                if($prod['status'] == 1) {
                    $checked = 'checked';
                }
				 $this->db->where('item_id', $prod['productid']);
				$this->db->where('currency', $cur);
				//$unitprice = $this->db->get(db_prefix() . 'unit_price')->result_array();
				  $this->db->join(db_prefix() . 'taxes', db_prefix() . 'taxes.id=' . db_prefix() . 'item_price.tax');
				$unitprice = $this->db->get(db_prefix() . 'item_price')->result_array();
                $html .= '<div style="height:40px; clear:both" class="productdiv css-table-row" id="'.$i.'"><div class="wcb">
                <input type="hidden" name="no[]" value="'.$i.'">
                            <input type="hidden" name="status_'.$i.'" value="1" class="form-control cbox" '.$checked.' >
                <select name="product[]" class="form-control" onchange="getdealprodprice(this,'.$i.')"><option value="">--Select Item--</option>';
                foreach($data as $val) {
                    $selected = '';
                    if($val["id"] == $prod['productid']) {
                        $selected = 'selected';
                    }
                        $html .= '<option value="'.$val["id"].'"  '.$selected.'>'.$val["name"].'</option>';
                } 
                $html .= '</select>';
                $html .= '</div>';
                $html .=get_particulars_item_ordered_inputs($i,$prod['productid']);
                $html .='<div class="">
                <input type="text" name="price[]" placeholder="Price" value="'.$prod['price'].'" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$i.')" class="form-control" />';
                    $html .= '</div>';
                    $html .= '<div class="">
                    <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,'.$i.')" value="'.$prod['quantity'].'"  class="form-control" />';
                    $html .= '</div>';
                    $tax = (isset($prod['tax']) && $prod['tax'] > 0) ? $prod['tax'] : 0;
                    if($tax < 1)
                        $tax = (isset($unitprice[0]['taxrate']) && $unitprice[0]['taxrate'] > 0) ? $unitprice[0]['taxrate'] : 0;
                    
                    $html .= '<div class="">
                    <input type="number" name="tax[]" placeholder="Tax" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="tax_total(this,'.$i.')" value="'.$tax.'"  class="form-control" />';
                    $html .= '</div>';
                    if($discount_value == 1 || $discount_option == 1) {
                        $html .= '<div class="">
                        <input type="number" name="discount[]" placeholder="Discount" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,'.$i.')" value="'.$prod['discount'].'"  class="form-control" />';
                        $html .= '</div>';
                    }
                    $html .= '<div class="">
                    <input type="text" name="total[]" placeholder="Total" value="'.$prod['total_price'].'" readonly class="form-control" />';
                    $html .= '</div>';
                    
                //     $html .= '<span class="dropdown">
                //     <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                //     <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                //       <li><a class="dropdown-item" href="#" onClick="gotoprod('.$i.');">Go to Product</a></li>
                //       <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                //       <li><a class="dropdown-item" id="variationbtn_'.$i.'" href="#" onClick="selectVariation('.$i.');">Select Variation</a></li>
                //     </ul>
                //   </span>';
                $html .= '<span class="dropdown">
                    <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                      <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                      <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                    </ul>
                  </span>';
                  if($prod['variation']) {
                  $html .= '<div class="" id="variation_'.$i.'" style="width: 18.7%;margin: 4px 15px 15px;clear:both;">
                  <label>VARIATION</label>
                  <select name="variation_'.$i.'" class="form-control" onchange="getvariationprodprice(this,'.$i.')">
                  <option value="">--Select Variation--</option>';
                        $vari = $this->prodgetvaraiton($prod['productid'],$cur) ;
                        foreach($vari as $val) {
                            $selected = '';
                            if($val["id"] == $prod['variation']) {
                            $selected = 'selected';
                            }
                            $html .= '<option value="'.$val["id"].'" '.$selected.'>'.$val["name"].'</option>';
                        } 
                    
                    $html .= '</select></div><style>#variationbtn_'.$i.'{pointer-events: none; cursor: default;}</style>';
                    }
                  $html .= '</div>';
                $i++;
            }

        } else {
            $html .= '<div style="height:40px; clear:both" class="productdiv css-table-row" id="'.$_POST['length'].'"><div class="wcb">
            <input type="hidden" name="no[]" value="'.$_POST['length'].'">
                            <input type="hidden" name="status_'.$_POST['length'].'" value="1" class="form-control cbox">
            <select name="product[]" class="form-control" onchange="getdealprodprice(this,'.$_POST['length'].')"><option value="">--Select Item--</option>';
            foreach($data as $val) {
                    $html .= '<option value="'.$val["id"].'">'.$val["name"].'</option>';
            } 
            $html .= '</select>';
            $html .= '</div>';
            $html .=get_particulars_item_ordered_inputs($_POST['length']);
            $html .='<div class="">
            <input type="text" name="price[]" placeholder="Price" value="" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$_POST['length'].')" class="form-control" />';
                $html .= '</div>';
                $html .= '<div class="col-md-1">
                <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,'.$_POST['length'].')" value=""  class="form-control" />';
                $html .= '</div>';
                $html .= '<div class="">
                <input type="number" name="tax[]" placeholder="Tax" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="tax_total(this,'.$_POST['length'].')" value=""  class="form-control" />';
                $html .= '</div>';
                if($discount_value == 1 || $discount_option == 1) {
                    $html .= '<div class="">
                    <input type="number" name="discount[]" placeholder="Discount" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,'.$_POST['length'].')" value=""  class="form-control" />';
                    $html .= '</div>';
                }
                $html .= '<div class="">
                <input type="text" name="total[]" placeholder="Total" value="" readonly class="form-control" />';
                $html .= '</div>';
                
            //     $html .= '<span class="dropdown">
            //     <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
            //     <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
            //       <li><a class="dropdown-item" href="#" onClick="gotoprod('.$_POST['length'].');">Go to Product</a></li>
            //       <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
            //       <li><a class="dropdown-item" id="variationbtn_'.$_POST['length'].'" href="#" onClick="selectVariation('.$_POST['length'].');">Select Variation</a></li>
            //     </ul>
            //   </span>';
            $html .= '<span class="dropdown">
                <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                  <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                  <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                </ul>
              </span>';
            //   if($prod['variation']) {
            //     $html .= '<div class="col-md-2" id="variation_'.$_POST['length'].'" style="width: 18.7%;margin: 4px 15px 15px;">
            //     <label>VARIATION</label>
            //       <select name="variation_'.$_POST['length'].'" class="form-control" onchange="getvariationprodprice(this,'.$_POST['length'].')">
            //       <option value="">--Select Variation--</option>';
            //             $vari = $this->prodgetvaraiton($prod['productid'],$cur) ;
            //             foreach($vari as $val) {
            //                 $selected = '';
            //                 if($val["id"] == $prod['variation']) {
            //                 $selected = 'selected';
            //                 }
            //                 $html .= '<option value="'.$val["id"].'" '.$selected.'>'.$val["name"].'</option>';
            //             } 
                    
            //         $html .= '</select></div><style>#variationbtn_'.$_POST['length'].'{pointer-events: none; cursor: default;}</style>';
            //     }
              $html .= '</div>';
        }
            echo $html;
        exit;
    }

    public function getextaxfields() {
        $this->load->model('currencies_model');
        if(isset($_POST['currency'])) {
            if(is_numeric($_POST['currency'])) {
                $currency = $this->currencies_model->get($_POST['currency']);
                $cur = $currency->name;
            } else {
                $cur = $_POST['currency'];
            }
        } else {
            $currency = $this->currencies_model->get_base_currency();
            $cur = $currency->name;
        }
        //pre($_POST);
        //$data = $this->products_model->getprod_price($cur);
        $data = $this->products_model->getitem_price($cur);
        $getProds = $this->products_model->getextaxprods($cur);
        $discount_value = 0;
        foreach($getProds as $prod) {
            if($prod['discount'] > 0) {
                $discount_value = 1;
            }
        }
        $discount_option = get_option('product_discount_option');

        //pre($getProds);
        $html = '';
        if(!empty($getProds)) {
            $i = 0;
            foreach($getProds as $prod) {
                $checked = '';
                if($prod['status'] == 1) {
                    $checked = 'checked';
                }
				 $this->db->where('item_id', $prod['productid']);
				$this->db->where('currency', $cur);
				//$unitprice = $this->db->get(db_prefix() . 'unit_price')->result_array();
				  $this->db->join(db_prefix() . 'taxes', db_prefix() . 'taxes.id=' . db_prefix() . 'item_price.tax');
				$unitprice = $this->db->get(db_prefix() . 'item_price')->result_array();
                $html .= '<div style="height:40px; clear:both" class="productdiv css-table-row" id="'.$i.'"><div class="wcb" >
                <input type="hidden" name="no[]" value="'.$i.'">
                            <input type="hidden" name="status_'.$i.'" value="1" class="form-control cbox" '.$checked.' >
                <select name="product[]" class="form-control" onchange="getdealprodprice(this,'.$i.')"><option value="">--Select Item--</option>';
                foreach($data as $val) {
                    $selected = '';
                    if($val["id"] == $prod['productid']) {
                        $selected = 'selected';
                    }
                        $html .= '<option value="'.$val["id"].'"  '.$selected.'>'.$val["name"].'</option>';
                } 
                $html .= '</select>';
                $html .= '</div>';
                $html .=get_particulars_item_ordered_inputs($i,$prod['productid']);
                $html .='<div class="">
                <input type="text" name="price[]" placeholder="Price" value="'.$prod['price'].'" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$i.')" class="form-control" />';
                    $html .= '</div>';
                    $html .= '<div class="">
                    <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,'.$i.')" value="'.$prod['quantity'].'"  class="form-control" />';
                    $html .= '</div>';
                    $tax = (isset($prod['tax']) && $prod['tax'] > 0) ? $prod['tax'] : 0;
                    if($tax < 1)
                        $tax = (isset($unitprice[0]['taxrate']) && $unitprice[0]['taxrate'] > 0) ? $unitprice[0]['taxrate'] : 0;
                                        
                    $html .= '<div class="">
                    <input type="number" name="tax[]" placeholder="Tax" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="tax_total(this,'.$i.')" value="'.$tax.'"  class="form-control" />';
                    $html .= '</div>';
                    if($discount_value == 1 || $discount_option == 1) {
                        $html .= '<div class="">
                        <input type="number" name="discount[]" placeholder="Discount" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,'.$i.')" value="'.$prod['discount'].'"  class="form-control" />';
                        $html .= '</div>';
                    }
                    $html .= '<div class="">
                    <input type="text" name="total[]" placeholder="Total" value="'.$prod['total_price'].'" readonly class="form-control" />';
                    $html .= '</div>';
                    
                //     $html .= '<span class="dropdown">
                //     <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                //     <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                //       <li><a class="dropdown-item" href="#" onClick="gotoprod('.$i.');">Go to Product</a></li>
                //       <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                //       <li><a class="dropdown-item" id="variationbtn_'.$i.'" href="#" onClick="selectVariation('.$i.');">Select Variation</a></li>
                //     </ul>
                //   </span>';
                $html .= '<span class="dropdown">
                <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                  <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                  <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                </ul>
              </span>';
                  if($prod['variation']) {
                  $html .= '<div class="" id="variation_'.$i.'" style="width: 18.7%;margin: 4px 15px 15px;clear:both;">
                  <label>VARIATION</label>
                  <select name="variation_'.$i.'" class="form-control" onchange="getvariationprodprice(this,'.$i.')">
                  <option value="">--Select Variation--</option>';
                        $vari = $this->prodgetvaraiton($prod['productid'],$cur) ;
                        foreach($vari as $val) {
                            $selected = '';
                            if($val["id"] == $prod['variation']) {
                            $selected = 'selected';
                            }
                            $html .= '<option value="'.$val["id"].'" '.$selected.'>'.$val["name"].'</option>';
                        } 
                    
                    $html .= '</select></div><style>#variationbtn_'.$i.'{pointer-events: none; cursor: default;}</style>';
                    }
                  $html .= '</div>';
                $i++;
            }

        } else {
            $html .= '<div style="height:40px; clear:both" class="productdiv css-table-row" id="'.$_POST['length'].'"><div class="wcb">
            <input type="hidden" name="no[]" value="'.$_POST['length'].'">
                            <input type="hidden" name="status_'.$_POST['length'].'" value="1" class="form-control cbox">
            <select name="product[]" class="form-control" onchange="getdealprodprice(this,'.$_POST['length'].')"><option value="">--Select Item--</option>';
            foreach($data as $val) {
                    $html .= '<option value="'.$val["id"].'">'.$val["name"].'</option>';
            } 
            $html .= '</select>';
            $html .= '</div>';
            $html .=get_particulars_item_ordered_inputs($_POST['length']);

            $html .='<div class="">
            <input type="text" name="price[]" placeholder="Price" value="" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$_POST['length'].')" class="form-control" />';
                $html .= '</div>';
                $html .= '<div class="">
                <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,'.$_POST['length'].')" value=""  class="form-control" />';
                $html .= '</div>';
                $html .= '<div class="">
                <input type="number" name="tax[]" placeholder="Tax" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="tax_total(this,'.$_POST['length'].')" value=""  class="form-control" />';
                $html .= '</div>';
                if($discount_value == 1 || $discount_option == 1) {
                    $html .= '<div class="">
                    <input type="number" name="discount[]" placeholder="Discount" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,'.$_POST['length'].')" value=""  class="form-control" />';
                    $html .= '</div>';
                }
                $html .= '<div class="">
                <input type="text" name="total[]" placeholder="Total" value="" readonly class="form-control" />';
                $html .= '</div>';
                
            //     $html .= '<span class="dropdown">
            //     <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
            //     <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
            //       <li><a class="dropdown-item" href="#" onClick="gotoprod('.$_POST['length'].');">Go to Product</a></li>
            //       <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
            //       <li><a class="dropdown-item" id="variationbtn_'.$_POST['length'].'" href="#" onClick="selectVariation('.$_POST['length'].');">Select Variation</a></li>
            //     </ul>
            //   </span>';
            $html .= '<span class="dropdown">
                <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                  <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                  <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                </ul>
              </span>';
            //   if($prod['variation']) {
            //     $html .= '<div class="col-md-2" id="variation_'.$_POST['length'].'" style="width: 18.7%;margin: 4px 15px 15px;">
            //     <label>VARIATION</label>
            //       <select name="variation_'.$_POST['length'].'" class="form-control" onchange="getvariationprodprice(this,'.$_POST['length'].')">
            //       <option value="">--Select Variation--</option>';
            //             $vari = $this->prodgetvaraiton($prod['productid'],$cur) ;
            //             foreach($vari as $val) {
            //                 $selected = '';
            //                 if($val["id"] == $prod['variation']) {
            //                 $selected = 'selected';
            //                 }
            //                 $html .= '<option value="'.$val["id"].'" '.$selected.'>'.$val["name"].'</option>';
            //             } 
                    
            //         $html .= '</select></div><style>#variationbtn_'.$_POST['length'].'{pointer-events: none; cursor: default;}</style>';
            //     }
              $html .= '</div>';
        }
            echo $html;
        exit;
    }

    public function getsalesproductfields() {
        $this->load->model('currencies_model');
        if(isset($_POST['currency'])) {
            if(is_numeric($_POST['currency'])) {
                $currency = $this->currencies_model->get($_POST['currency']);
                $cur = $currency->name;
            } else {
                $cur = $_POST['currency'];
            }
        } else {
            $currency = $this->currencies_model->get_base_currency();
            $cur = $currency->name;
        }
        //$data = $this->products_model->getprod_price($cur);
        $data = $this->products_model->getitem_price($cur);
        $getProds = $this->products_model->getsalesnotaxprods($cur);
        $discount_value = 0;
        foreach($getProds as $prod) {
            if($prod['discount'] > 0) {
                $discount_value = 1;
            }
        }
        $discount_option = get_option('product_discount_option');
        
        //pre($getProds);
        $html = '';
        if(!empty($getProds)) {
            $i = 0;
            foreach($getProds as $prod) {
                //echo $prod['productid']; exit;
                $checked = '';
                if($prod['status'] == 1) {
                    $checked = 'checked';
                }
                $html .= '<div style="height:40px; clear:both" class="productdiv css-table-row" id="'.$i.'"><div class=" wcb">
                <input type="hidden" name="no[]" value="'.$i.'">
                            <input type="hidden" name="status_'.$i.'" value="1" class="form-control cbox" '.$checked.' >
                <select name="product[]" class="form-control" onchange="getdealprodprice(this,'.$i.')"><option value="">--Select Item--</option>';
                foreach($data as $val) {
                    $selected = '';
                    if($val["id"] == $prod['productid']) {
                        $selected = 'selected';
                    }
                        $html .= '<option value="'.$val["id"].'"  '.$selected.'>'.$val["name"].'</option>';
                } 
                $html .= '</select>';
                $html .= '</div>';
                $html .=get_particulars_item_ordered_inputs($i,$prod['productid']);
                $html .= '<div class="">
                <input type="text" name="price[]" placeholder="Price" value="'.$prod['price'].'" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$i.')" class="form-control" />';
                    $html .= '</div>';
                    $html .= '<div class="">
                    <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,'.$i.')" value="'.$prod['quantity'].'"  class="form-control" />';
                    $html .= '</div>';
                    if($discount_value == 1 || $discount_option == 1) {
                        $html .= '<div class="">
                        <input type="number" name="discount[]" placeholder="Discount" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,'.$i.')" value="'.$prod['discount'].'"  class="form-control" />';
                        $html .= '</div>';
                    }
                    $html .= '<div class="">
                    <input type="text" name="total[]" placeholder="Total" value="'.$prod['total_price'].'" readonly class="form-control" />';
                    $html .= '</div>';
                    
                //     $html .= '<span class="dropdown">
                //     <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                //     <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                //       <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                //       <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                //       <li><a class="dropdown-item" id="variationbtn_'.$i.'" href="#" onClick="selectVariation('.$i.');">Select Variation</a></li>
                //     </ul>
                //   </span>';
                $html .= '<span class="dropdown">
                    <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                      <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                      <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                    </ul>
                  </span>';
                  if($prod['variation']) {
                        $html .= '<div class="" id="variation_'.$i.'" style="width: 23.3%;margin: 4px 19px 15px;">
                        <label>VARIATION</label>
                        <select name="variation_'.$i.'" class="form-control" onchange="getvariationprodprice(this,'.$i.')">
                        <option value="">--Select Variation--</option>';
                        $vari = $this->prodgetvaraiton($prod['productid'],$cur) ;
                        foreach($vari as $val) {
                            $selected = '';
                            if($val["id"] == $prod['variation']) {
                            $selected = 'selected';
                            }
                            $html .= '<option value="'.$val["id"].'" '.$selected.'>'.$val["name"].'</option>';
                        } 
                    
                        $html .= '</select></div><style>#variationbtn_'.$i.'{pointer-events: none; cursor: default;}</style>';
                    }
                  $html .= '</div>';
                $i++;
            }

        } else {
            $getProds = $this->products_model->getprods($cur);
            $discount_value = 0;
            foreach($getProds as $prod) {
                if($prod['discount'] > 0) {
                    $discount_value = 1;
                }
            }
            $discount_option = get_option('product_discount_option');
            $html .= '<div style="height:40px; clear:both;" class="productdiv css-table-row" id="'.$_POST['length'].'"><div class="wcb">
            <input type="hidden" name="no[]" value="'.$_POST['length'].'">
                            <input type="hidden" name="status_'.$_POST['length'].'" value="1" class="form-control cbox">
            <select name="product[]" class="form-control" onchange="getdealprodprice(this,'.$_POST['length'].')"><option value="">--Select Item--</option>';
            foreach($data as $val) {
                    $html .= '<option value="'.$val["id"].'">'.$val["name"].'</option>';
            } 
            $html .= '</select>';
            $html .= '</div>';
            $html .=get_particulars_item_ordered_inputs($_POST['length']);
            $html .= '<div class="">
            <input type="text" name="price[]" placeholder="Price" value="" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$_POST['length'].')" class="form-control" />';
                $html .= '</div>';
                $html .= '<div class="">
                <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,'.$_POST['length'].')" value=""  class="form-control" />';
                $html .= '</div>';
                if($discount_value == 1 || $discount_option == 1) {
                    $html .= '<div class="">
                        <input type="number" name="discount[]" placeholder="Discount" min="0" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,'.$_POST['length'].')" value=""  class="form-control" />';
                        $html .= '</div>';
                }
                $html .= '<div class="">
                <input type="text" name="total[]" placeholder="Total" value="" readonly class="form-control" />';
                $html .= '</div>';
                
            //     $html .= '<span class="dropdown">
            //     <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
            //     <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
            //       <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
            //       <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
            //       <li><a class="dropdown-item" id="variationbtn_'.$_POST['length'].'" href="#" onClick="selectVariation('.$_POST['length'].');">Select Variation</a></li>
            //     </ul>
            //   </span>';

            $html .= '<span class="dropdown">
                <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="false">...</button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                  <li><a class="dropdown-item" href="'.base_url().'admin/invoice_items'.'" >Go to Item</a></li>
                  <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                </ul>
              </span>';
              
              $html .= '</div>';
        }
            echo $html;

        exit;
    }

    public function getpricebyid() {
        $this->load->model('currencies_model');
        // $this->load->model('products_model');
        
        if(isset($_POST['currency'])) {
            if(is_numeric($_POST['currency'])) {
                $currency = $this->currencies_model->get($_POST['currency']);
                $cur = $currency->name;
            } else {
                $cur = $_POST['currency'];
            }
        } else {
            $currency = $this->currencies_model->get_base_currency();
            $cur = $currency->name;
        }
        $data = $this->products_model->getpricebyid($cur);
        echo json_encode($data);
        exit();
    }

    public function getvariationpricebyid() {
        $this->load->model('currencies_model');
        if(isset($_POST['currency'])) {
            if(is_numeric($_POST['currency'])) {
                $currency = $this->currencies_model->get($_POST['currency']);
                $cur = $currency->name;
            } else {
                $cur = $_POST['currency'];
            }
        } else {
            $currency = $this->currencies_model->get_base_currency();
            $cur = $currency->name;
        }
        $data = $this->products_model->getvariationpricebyid($cur);
        echo json_encode($data);
        exit();
    }

    

    public function getVariationfield() {
        $this->load->model('currencies_model');
        if(isset($_POST['currency'])) {
            if(is_numeric($_POST['currency'])) {
                $currency = $this->currencies_model->get($_POST['currency']);
                $cur = $currency->name;
            } else {
                $cur = $_POST['currency'];
            }
        } else {
            $currency = $this->currencies_model->get_base_currency();
            $cur = $currency->name;
        }
        $data = $this->products_model->getVariationfield($cur);
        $html = '';
        if($data) {
            if($_POST['method'] == 1) {
                $style = 'width: 23.3%;margin: 4px 19px 15px;clear:both;';
            } elseif ($_POST['method'] == 2 || $_POST['method'] == 3) {
                $style = 'width: 18.7%;margin: 4px 15px 15px;clear:both;';
            } else {
                $style = 'width: 23.3%;margin: 4px 19px 15px;clear:both;';
            }
            $html .= '<div class="col-md-2" id="variation_'.$_POST['index'].'" style="'.$style.'">
            <label>VARIATION</label>
            <select name="variation_'.$_POST['index'].'" class="form-control" onchange="getvariationprodprice(this,'.$_POST['index'].')">
            <option value="">--Select Variation--</option>';
            foreach($data as $val) {
                    $html .= '<option value="'.$val["id"].'">'.$val["name"].'</option>';
            } 
            $html .= '</select>';
            $html .= '</div>';
        }
        echo $html;
        exit;
    }

    public function removefields() {
        $this->load->model('currencies_model');
        if(isset($_POST['currency'])) {
            if(is_numeric($_POST['currency'])) {
                $currency = $this->currencies_model->get($_POST['currency']);
                $cur = $currency->name;
            } else {
                $cur = $_POST['currency'];
            }
        } else {
            $currency = $this->currencies_model->get_base_currency();
            $cur = $currency->name;
        }
        //$data = $this->products_model->getprod_price($cur);
        $data = $this->products_model->getitem_price($cur);
        
        $html = '';
        $html .= '<div style="height:40px;clear:both;" class="productdiv css-table-row" id="0"><div class="">
            <select name="product[]" class="form-control" onchange="getprice1(this,0)"><option value="">--Select Item--</option>';
            foreach($data as $val) {
                    $html .= '<option value="'.$val["id"].'">'.$val["name"].'</option>';
            } 
            $html .= '</select>';
            $html .= '</div>
            <div class="">
            <input type="text" name="price[]" placeholder="Price" value="" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,'.$_POST['length'].')" class="form-control" />';
                $html .= '</div>';
                $html .= '<div class="">
                <input type="number" name="qty[]" placeholder="Qty" min="1" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,0)" value=""  class="form-control" />';
                $html .= '</div>';
                $html .= '<div class="">
                <input type="text" name="total[]" placeholder="Total" value="" readonly class="form-control" />';
                $html .= '</div>';
                $html .= '<div class="col-md-1"></div>
            </div>';
            echo $html;
        exit;
    }

    public function getVariationfields() {
        $this->load->model('currencies_model');
        $currencies = $this->currencies_model->get_unit_currencies($_POST['prodid']);
        $postcnt = count($_POST['currency']);
        $curcnt = count($currencies);
        $html = '';
        if($postcnt != $curcnt) {
            $html .= '<div style="min-height:100px; clear:both;">
            <div class="">
                <input type="number" name="variation_price_'.$_POST['varid'].'[]" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" value="" placeholder="Price" class="form-control" /> 
            </div>
            <div class="">
                <select name="variation_currency_'.$_POST['varid'].'[]" class="form-control" >';
                foreach($currencies as $val) {
                    if (!in_array($val['currency'], $_POST['currency'])) {
                        $html .= '<option value="'.$val["currency"].'">'.$val["currency"].'</option>';
                    }
                } 
                $html .= '</select>';
                $html .= '</div>
            <div class=""><textarea name="comment_'.$_POST['varid'].'[]" id="comment[]" rows="5" class="form-control" placeholder="Comment" /></textarea></div>
            <a href="javascript:void(0);" class="remove_button" title="Remove field" style="position:relative; top:8px; left:15px"><i class="fa fa-trash"></i></a>
            </div>';
            echo $html;
        }
        exit;
    }

	public function view_contact($contact_id = '')
    {
		
        $group         = !$this->input->get('group') ? 'profile' : $this->input->get('group');
        $data['group'] = $group;

        if ($group != 'contacts' && $contact_id == $this->input->get('contactid')) {
            redirect(admin_url('products/product/' . $contact_id . '?group=contacts&contactid=' . $contact_id));
        }

        // Customer groups
        $data['groups'] = $this->products_model->get_groups();
        if ($contact_id == '') {
            $title = _l('add_new', _l('client_lowercase'));
        } else {
            $data['contact'] = $this->products_model->get_contact($contact_id);
			$id = $data['contact']->userid;
            $client                = $this->products_model->get($id);
			$data['customer_tabs'] = array(
			'profile'=>array('slug'=>'profile','name'=>'Profile','icon'=>'fa fa-user-circle','view'=>'admin/clients/groups/profile_contact','position'=>1,'href'=>'#','children'=>array(),),
			'projects'=>array('slug'=>'projects','name'=>_l('projects'),'icon'=>'fa fa-bars','view'=>'admin/clients/groups/projects_contact','position'=>1,'href'=>'#','children'=>array(),),
			'tasks'=>array('slug'=>'tasks','name'=>_l('tasks'),'icon'=>'fa fa-tasks','view'=>'admin/clients/groups/tasks_contact','position'=>1,'href'=>'#','children'=>array(),),
			);
		
            if (!$client) {
                show_404();
            }
            $data['tab']      = isset($data['customer_tabs'][$group]) ? $data['customer_tabs'][$group] : null;

            if (!$data['tab']) {
                show_404();
            }

            // Fetch data based on groups
            if ($group == 'profile') {
                $data['customer_groups'] = $this->products_model->get_customer_groups($id);
                $data['customer_admins'] = $this->products_model->get_admins($id);
            }  elseif ($group == 'projects') {
                $this->load->model('projects_model');
                $data['project_statuses'] = $this->projects_model->get_project_statuses();
                //pre($data);
            } 
            $data['staff'] = $this->staff_model->get('', ['active' => 1]);

            $data['client'] = $client;
            $title          = $data['contact']->firstname;

            // Get all active staff members (used to add reminder)
            $data['members'] = $data['staff'];

            if (!empty($data['client']->company)) {
                // Check if is realy empty client company so we can set this field to empty
                // The query where fetch the client auto populate firstname and lastname if company is empty
                if (is_empty_customer_company($data['client']->userid)) {
                    $data['client']->company = '';
                }
            }
        }

        

        		
		$data['bodyclass'] = 'customer-profile dynamic-create-groups';
        $data['title']     = $title;

        $this->load->view('admin/products/view_contact', $data);
	}
	
    public function export($contact_id)
    {
        if (is_admin()) {
            $this->load->library('gdpr/gdpr_contact');
            $this->gdpr_contact->export($contact_id);
        }
    }

    // Used to give a tip to the user if the company exists when new company is created
    public function check_duplicate_customer_name()
    {
        if (has_permission('customers', '', 'create')) {
            $companyName = trim($this->input->post('company'));
            $response    = [
                'exists'  => (bool) total_rows(db_prefix().'clients', ['company' => $companyName]) > 0,
                'message' => _l('company_exists_info', '<b>' . $companyName . '</b>'),
            ];
            echo json_encode($response);
        }
    }

    public function save_longitude_and_latitude($client_id)
    {
        if (!has_permission('customers', '', 'edit')) {
            if (!is_customer_admin($client_id)) {
                ajax_access_denied();
            }
        }

        $this->db->where('userid', $client_id);
        $this->db->update(db_prefix().'clients', [
            'longitude' => $this->input->post('longitude'),
            'latitude'  => $this->input->post('latitude'),
        ]);
        if ($this->db->affected_rows() > 0) {
            echo 'success';
        } else {
            echo 'false';
        }
    }

    public function form_contact($customer_id=0, $contact_id = '')
    {
        if (!has_permission('customers', '', 'view')) {
            if (!is_customer_admin($customer_id)) {
                echo _l('access_denied');
                die;
            }
        }
        $data['customer_id'] = $customer_id;
        $data['contactid']   = $contact_id;
        if ($this->input->post()) {
            $data             = $this->input->post();
            $data['password'] = $this->input->post('password', false);

            unset($data['contactid']);
            if ($contact_id == '') {
                if (!has_permission('customers', '', 'create')) {
                    if (!is_customer_admin($customer_id)) {
                        header('HTTP/1.0 400 Bad error');
                        echo json_encode([
                            'success' => false,
                            'message' => _l('access_denied'),
                        ]);
                        die;
                    }
                }
                $data['userid'] = $data['userids'] = $data['clientid'];
                unset($data['clientid']);
                // if(isset($data['clientid'])) {
                //     if(is_array($data['clientid']) && count($data['clientid']) > 0){
                //         $data['userids'] = implode(',', $data['clientid']);
                //          $data['userid'] =  isset($data['clientid'][0])?$data['clientid'][0]:0;
                //     }
                //     unset($data['clientid']);
                // }
                // assign Deals
                if (isset($data['deals'])) {
                    $deals = $data['deals'];
                    unset($data['deals']);
                }
                // pr($deals);
                // pr($_POST);
                // pre($data);
                $id      = $this->products_model->add_contact($data, $customer_id);
                $message = $card = '';
                $success = false;
                
                if ($id) {
                    handle_contact_profile_image_upload($id);
                    $success = true;
                    $message = _l('added_successfully', _l('contact'));
                    
                    //Assign Deals
                    if (isset($deals)) {
                        foreach($deals as $val) {
                            $this->db->insert(db_prefix() . 'project_contacts', [
                                'project_id' => $val,
                                'contacts_id'   => $id,
                            ]);
                        }
                    }
                    if(isset($_POST['project_id'])){
                        
                        $card = '<div class="media">
                                <div class="media-left">
                                <a href="'.admin_url('clients/view_contact/'.$id).'">
                                <img src="../../../assets/images/user-placeholder.jpg" id="contact-img" class="staff-profile-image-small">
                                </a>
                                </div>
                                <div class="media-body">
                                    <h5 class="media-heading mtop5" style="width:auto; float:left;"><a href="'.admin_url('clients/view_contact/'.$id).'">'.(isset($_POST['firstname'])?$_POST['firstname']:0).'</a> </h5>
                                    <a href="'.admin_url('/remove_team_contact/'.(isset($_POST['project_id'])?$_POST['project_id']:0).'/'.$id).'" class="text-danger _delete"><i class="fa fa fa-times"></i></a>
                                </div>
                            </div>';
                    }
                }

                

                echo json_encode([
                    'id'             =>$id,
                    'firstname'             =>$data['firstname'],
                    'success'             => $success,
                    'card'             => $card,
                    'message'             => $message,
                    'has_primary_contact' => (total_rows(db_prefix().'contacts', ['userid' => $customer_id, 'is_primary' => 1]) > 0 ? true : false),
                    'is_individual'       => is_empty_customer_company($customer_id) && total_rows(db_prefix().'contacts', ['userid' => $customer_id]) == 1,
                ]);
                die;
            }
            if (!has_permission('customers', '', 'edit')) {
                if (!is_customer_admin($customer_id)) {
                    header('HTTP/1.0 400 Bad error');
                    echo json_encode([
                            'success' => false,
                            'message' => _l('access_denied'),
                        ]);
                    die;
                }
            }
            if($contact_id) {
                // assign Deals
                if (isset($data['deals'])) {
                    $deals = $data['deals'];
                    unset($data['deals']);
                }
                // pr($deals);
                // pr($_POST);
                // pre($data);
                if (isset($deals)) {
                    $this->db->where('contacts_id', $contact_id);
                    $this->db->delete(db_prefix() . 'project_contacts');
                    foreach($deals as $val) {
                        $this->db->insert(db_prefix() . 'project_contacts', [
                            'project_id' => $val,
                            'contacts_id'   => $contact_id,
                        ]);
                    }
                }
                
            }

            $original_contact = $this->products_model->get_contact($contact_id);
            $data['userid'] = $data['userids'] = $data['clientid'];
            unset($data['clientid']);
            //  if(isset($data['clientid'])) {
            //         if(is_array($data['clientid']) && count($data['clientid']) > 0){
            //             $data['userids'] = implode(',', $data['clientid']);
            //             $data['userid'] =  isset($data['clientid'][0])?$data['clientid'][0]:0;
            //         }
            //         unset($data['clientid']);
            //     }
            $success          = $this->products_model->update_contact($data, $contact_id);
            $message          = '';
            $proposal_warning = false;
            $original_email   = '';
            $updated          = false;
            if (is_array($success)) {
                if (isset($success['set_password_email_sent'])) {
                    $message = _l('set_password_email_sent_to_client');
                } elseif (isset($success['set_password_email_sent_and_profile_updated'])) {
                    $updated = true;
                    $message = _l('set_password_email_sent_to_client_and_profile_updated');
                }
            } else {
                if ($success == true) {
                    $updated = true;
                    $message = _l('updated_successfully', _l('contact'));
                }
            }
            if (handle_contact_profile_image_upload($contact_id) && !$updated) {
                $message = _l('updated_successfully', _l('contact'));
                $success = true;
            }
            if ($updated == true) {
                $contact = $this->products_model->get_contact($contact_id);
                if (total_rows(db_prefix().'proposals', [
                        'rel_type' => 'customer',
                        'rel_id' => $contact->userid,
                        'email' => $original_contact->email,
                    ]) > 0 && ($original_contact->email != $contact->email)) {
                    $proposal_warning = true;
                    $original_email   = $original_contact->email;
                }
            }
            echo json_encode([
                    'success'             => $success,
                    'proposal_warning'    => $proposal_warning,
                    'message'             => $message,
                    'original_email'      => $original_email,
                    'has_primary_contact' => (total_rows(db_prefix().'contacts', ['userid' => $customer_id, 'is_primary' => 1]) > 0 ? true : false),
                ]);
            die;
        }
        if ($contact_id == '') {
            $title = _l('add_new', _l('contact_lowercase'));
        } else {
            $data['contact'] = $this->products_model->get_contact($contact_id);
            $data['deals'] = $this->products_model->get_dealsbyClientId($data['contact']->userids);
            $data['deals_contact'] = $this->products_model->get_dealContactsbyClientId($contact_id);
            //pre($data['deals_contact']);
            if (!$data['contact']) {
                header('HTTP/1.0 400 Bad error');
                echo json_encode([
                    'success' => false,
                    'message' => 'Contact Not Found',
                ]);
                die;
            }
            if(isset($data['contact']->userids) && !empty($data['contact']->userids)){
                $data['contact']->clientid = explode(',', $data['contact']->userids);
            }
            $title = $data['contact']->firstname . ' ' . $data['contact']->lastname;
            $data['contact']->alternative_emails = explode(',',($data['contact']->alternative_emails));
            $data['contact']->alternative_phonenumber = explode(',',($data['contact']->alternative_phonenumber));
          
        }

        $data['customer_permissions'] = get_contact_permissions();
        $data['title']                = $title;
        $this->load->view('admin/products/modals/contact', $data);
    }

    public function form_edit_contact($customer_id=0, $contact_id = '')
    {
        if (!has_permission('customers', '', 'view')) {
            if (!is_customer_admin($customer_id)) {
                echo _l('access_denied');
                die;
            }
        }
        $data['customer_id'] = $customer_id;
        $data['contactid']   = $contact_id;
        if ($this->input->post()) {
            $data             = $this->input->post();
            $data['password'] = $this->input->post('password', false);

            unset($data['contactid']);
            
            if (!has_permission('customers', '', 'edit')) {
                if (!is_customer_admin($customer_id)) {
                    header('HTTP/1.0 400 Bad error');
                    echo json_encode([
                            'success' => false,
                            'message' => _l('access_denied'),
                        ]);
                    die;
                }
            }
            if($contact_id) {
                // assign Deals
                if (isset($data['deals'])) {
                    $deals = $data['deals'];
                    unset($data['deals']);
                }
                // pr($deals);
                // pr($_POST);
                // pre($data);
                if (isset($deals)) {
                    $this->db->where('contacts_id', $contact_id);
                    $this->db->delete(db_prefix() . 'project_contacts');
                    foreach($deals as $val) {
                        $this->db->insert(db_prefix() . 'project_contacts', [
                            'project_id' => $val,
                            'contacts_id'   => $contact_id,
                        ]);
                    }
                }
                
            }

            $original_contact = $this->products_model->get_contact($contact_id);
            $data['userid'] = $data['userids'] = $data['clientid'];
            unset($data['clientid']);
            //  if(isset($data['clientid'])) {
            //         if(is_array($data['clientid']) && count($data['clientid']) > 0){
            //             $data['userids'] = implode(',', $data['clientid']);
            //             $data['userid'] =  isset($data['clientid'][0])?$data['clientid'][0]:0;
            //         }
            //         unset($data['clientid']);
            //     }
            $success          = $this->products_model->update_contact($data, $contact_id);
            $message          = '';
            $proposal_warning = false;
            $original_email   = '';
            $updated          = false;
            if (is_array($success)) {
                if (isset($success['set_password_email_sent'])) {
                    $message = _l('set_password_email_sent_to_client');
                } elseif (isset($success['set_password_email_sent_and_profile_updated'])) {
                    $updated = true;
                    $message = _l('set_password_email_sent_to_client_and_profile_updated');
                }
            } else {
                if ($success == true) {
                    $updated = true;
                    $message = _l('updated_successfully', _l('contact'));
                }
            }
            if (handle_contact_profile_image_upload($contact_id) && !$updated) {
                $message = _l('updated_successfully', _l('contact'));
                $success = true;
            }
            if ($updated == true) {
                $contact = $this->products_model->get_contact($contact_id);
                if (total_rows(db_prefix().'proposals', [
                        'rel_type' => 'customer',
                        'rel_id' => $contact->userid,
                        'email' => $original_contact->email,
                    ]) > 0 && ($original_contact->email != $contact->email)) {
                    $proposal_warning = true;
                    $original_email   = $original_contact->email;
                }
            }
            $message = _l('updated_successfully', _l('contact'));
            set_alert('success', $message);
            redirect(admin_url('clients/view_contact/' . $contact_id ));
            die;
        }
        if ($contact_id == '') {
            $title = _l('add_new', _l('contact_lowercase'));
        } else {
            $data['contact'] = $this->products_model->get_contact($contact_id);
            $data['deals'] = $this->products_model->get_dealsbyClientId($data['contact']->userids);
            $data['deals_contact'] = $this->products_model->get_dealContactsbyClientId($contact_id);
            //pre($data['deals_contact']);
            if (!$data['contact']) {
                header('HTTP/1.0 400 Bad error');
                echo json_encode([
                    'success' => false,
                    'message' => 'Contact Not Found',
                ]);
                die;
            }
            if(isset($data['contact']->userids) && !empty($data['contact']->userids)){
                $data['contact']->clientid = explode(',', $data['contact']->userids);
            }
            $title = $data['contact']->firstname . ' ' . $data['contact']->lastname;
            $data['contact']->alternative_emails = explode(',',($data['contact']->alternative_emails));
            $data['contact']->alternative_phonenumber = explode(',',($data['contact']->alternative_phonenumber));
          
        }

        $data['customer_permissions'] = get_contact_permissions();
        $data['title']                = $title;
        $this->load->view('admin/products/modals/contact', $data);
    }

    public function confirm_registration($client_id)
    {
        if (!is_admin()) {
            access_denied('Customer Confirm Registration, ID: ' . $client_id);
        }
        $this->products_model->confirm_registration($client_id);
        set_alert('success', _l('customer_registration_successfully_confirmed'));
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function update_file_share_visibility()
    {
        if ($this->input->post()) {
            $file_id           = $this->input->post('file_id');
            $share_contacts_id = [];

            if ($this->input->post('share_contacts_id')) {
                $share_contacts_id = $this->input->post('share_contacts_id');
            }

            $this->db->where('file_id', $file_id);
            $this->db->delete(db_prefix().'shared_customer_files');

            foreach ($share_contacts_id as $share_contact_id) {
                $this->db->insert(db_prefix().'shared_customer_files', [
                    'file_id'    => $file_id,
                    'contact_id' => $share_contact_id,
                ]);
            }
        }
    }

    public function delete_contact_profile_image($contact_id)
    {
        hooks()->do_action('before_remove_contact_profile_image');
        if (file_exists(get_upload_path_by_type('contact_profile_images') . $contact_id)) {
            delete_dir(get_upload_path_by_type('contact_profile_images') . $contact_id);
        }
        $this->db->where('id', $contact_id);
        $this->db->update(db_prefix().'contacts', [
            'profile_image' => null,
        ]);
    }

    public function mark_as_active($id)
    {
        $this->db->where('userid', $id);
        $this->db->update(db_prefix().'clients', [
            'active' => 1,
        ]);
        redirect(admin_url('products/product/' . $id));
    }

    public function consents($id)
    {
        if (!has_permission('customers', '', 'view')) {
            if (!is_customer_admin(get_user_id_by_contact_id($id))) {
                echo _l('access_denied');
                die;
            }
        }

        $this->load->model('gdpr_model');
        $data['purposes']   = $this->gdpr_model->get_consent_purposes($id, 'contact');
        $data['consents']   = $this->gdpr_model->get_consents(['contact_id' => $id]);
        $data['contact_id'] = $id;
        $this->load->view('admin/gdpr/contact_consent', $data);
    }

    public function update_all_proposal_emails_linked_to_customer($contact_id)
    {
        $success = false;
        $email   = '';
        if ($this->input->post('update')) {
            $this->load->model('proposals_model');

            $this->db->select('email,userid');
            $this->db->where('id', $contact_id);
            $contact = $this->db->get(db_prefix().'contacts')->row();

            $proposals = $this->proposals_model->get('', [
                'rel_type' => 'customer',
                'rel_id'   => $contact->userid,
                'email'    => $this->input->post('original_email'),
            ]);
            $affected_rows = 0;

            foreach ($proposals as $proposal) {
                $this->db->where('id', $proposal['id']);
                $this->db->update(db_prefix().'proposals', [
                    'email' => $contact->email,
                ]);
                if ($this->db->affected_rows() > 0) {
                    $affected_rows++;
                }
            }

            if ($affected_rows > 0) {
                $success = true;
            }
        }
        echo json_encode([
            'success' => $success,
            'message' => _l('proposals_emails_updated', [
                _l('contact_lowercase'),
                $contact->email,
            ]),
        ]);
    }

    public function assign_admins($id)
    {
        if (!has_permission('customers', '', 'create') && !has_permission('customers', '', 'edit')) {
            access_denied('customers');
        }
        $success = $this->products_model->assign_admins($this->input->post(), $id);
        if ($success == true) {
            set_alert('success', _l('updated_successfully', _l('client')));
        }

        redirect(admin_url('products/product/' . $id . '?tab=customer_admins'));
    }

    public function delete_customer_admin($customer_id, $staff_id)
    {
        if (!has_permission('customers', '', 'create') && !has_permission('customers', '', 'edit')) {
            access_denied('customers');
        }

        $this->db->where('customer_id', $customer_id);
        $this->db->where('staff_id', $staff_id);
        $this->db->delete(db_prefix().'customer_admins');
        redirect(admin_url('products/product/' . $customer_id) . '?tab=customer_admins');
    }

    public function delete_contact($customer_id, $id)
    {
        if (!has_permission('customers', '', 'delete')) {
            if (!is_customer_admin($customer_id)) {
                access_denied('customers');
            }
        }
        $contact      = $this->products_model->get_contact($id);
        $hasProposals = false;
        if ($contact && is_gdpr()) {
            if (total_rows(db_prefix().'proposals', ['email' => $contact->email]) > 0) {
                $hasProposals = true;
            }
        }

        $this->products_model->delete_contact($id);
        if ($hasProposals) {
            $this->session->set_flashdata('gdpr_delete_warning', true);
        }
        redirect(admin_url('products/product/' . $customer_id . '?group=contacts'));
    }

    public function restore_contact($id)
    {
        $this->products_model->restore_contact($id);
        $this->session->set_flashdata('gdpr_restore_warning', 'Contact has been Restored.');
        redirect(admin_url('clients/view_contact/' . $id));
    }

    public function restore_client($id)
    {
        $this->products_model->restore_client($id);
        $this->session->set_flashdata('gdpr_restore_warning', 'Client has been Restored.');
        redirect(admin_url('products/product/' . $id));
    }

    public function contacts($client_id)
    {
        $this->app->get_table_data('contacts', [
            'client_id' => $client_id,
        ]);
    }

    public function upload_attachment($id)
    {
        //echo  $id; exit;
        handle_product_attachments_upload($id);
    }

    public function add_external_attachment()
    {
        if ($this->input->post()) {
            $this->misc_model->add_product_attachment_to_database($this->input->post('productid'), 'product', $this->input->post('files'), $this->input->post('external'));
        }
    }

    public function delete_attachment($customer_id, $id)
    {
        if (has_permission('customers', '', 'delete') || is_customer_admin($customer_id)) {
            $this->products_model->delete_attachment($id);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    /* Delete client */
    public function delete($id)
    {
        if (!has_permission('customers', '', 'delete')) {
            access_denied('customers');
        }
        if (!$id) {
            redirect(admin_url('clients'));
        }
        $response = $this->products_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('customer_delete_transactions_warning', _l('invoices') . ', ' . _l('estimates') . ', ' . _l('credit_notes')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('client')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('client_lowercase')));
        }
        redirect(admin_url('clients'));
    }

    /* Staff can login as client */
    public function login_as_client($id)
    {
        if (is_admin()) {
            login_as_client($id);
        }
        hooks()->do_action('after_contact_login');
        redirect(site_url());
    }

    public function get_customer_billing_and_shipping_details($id)
    {
        echo json_encode($this->products_model->get_customer_billing_and_shipping_details($id));
    }

    /* Change client status / active / inactive */
    public function change_contact_status($id, $status)
    {
        if (has_permission('customers', '', 'edit') || is_customer_admin(get_user_id_by_contact_id($id))) {
            if ($this->input->is_ajax_request()) {
                $this->products_model->change_contact_status($id, $status);
            }
        }
    }

    /* Change client status / active / inactive */
    public function change_client_status($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            $result = $this->products_model->change_client_status($id, $status);
            //if($result) {
                redirect($_SERVER['REQUEST_URI'], 'refresh');
            //}
        }
    }

    /* Zip function for credit notes */
    public function zip_credit_notes($id)
    {
        $has_permission_view = has_permission('credit_notes', '', 'view');

        if (!$has_permission_view && !has_permission('credit_notes', '', 'view_own')) {
            access_denied('Zip Customer Credit Notes');
        }

        if ($this->input->post()) {
            $this->load->library('app_bulk_pdf_export', [
                'export_type'       => 'credit_notes',
                'status'            => $this->input->post('credit_note_zip_status'),
                'date_from'         => $this->input->post('zip-from'),
                'date_to'           => $this->input->post('zip-to'),
                'redirect_on_error' => admin_url('products/product/' . $id . '?group=credit_notes'),
            ]);

            $this->app_bulk_pdf_export->set_client_id($id);
            $this->app_bulk_pdf_export->in_folder($this->input->post('file_name'));
            $this->app_bulk_pdf_export->export();
        }
    }

    public function zip_invoices($id)
    {
        $has_permission_view = has_permission('invoices', '', 'view');
        if (!$has_permission_view && !has_permission('invoices', '', 'view_own')
            && get_option('allow_staff_view_invoices_assigned') == '0') {
            access_denied('Zip Customer Invoices');
        }

        if ($this->input->post()) {
            $this->load->library('app_bulk_pdf_export', [
                'export_type'       => 'invoices',
                'status'            => $this->input->post('invoice_zip_status'),
                'date_from'         => $this->input->post('zip-from'),
                'date_to'           => $this->input->post('zip-to'),
                'redirect_on_error' => admin_url('products/product/' . $id . '?group=invoices'),
            ]);

            $this->app_bulk_pdf_export->set_client_id($id);
            $this->app_bulk_pdf_export->in_folder($this->input->post('file_name'));
            $this->app_bulk_pdf_export->export();
        }
    }

    /* Since version 1.0.2 zip client estimates */
    public function zip_estimates($id)
    {
        $has_permission_view = has_permission('estimates', '', 'view');
        if (!$has_permission_view && !has_permission('estimates', '', 'view_own')
            && get_option('allow_staff_view_estimates_assigned') == '0') {
            access_denied('Zip Customer Estimates');
        }

        if ($this->input->post()) {
            $this->load->library('app_bulk_pdf_export', [
                'export_type'       => 'estimates',
                'status'            => $this->input->post('estimate_zip_status'),
                'date_from'         => $this->input->post('zip-from'),
                'date_to'           => $this->input->post('zip-to'),
                'redirect_on_error' => admin_url('products/product/' . $id . '?group=estimates'),
            ]);

            $this->app_bulk_pdf_export->set_client_id($id);
            $this->app_bulk_pdf_export->in_folder($this->input->post('file_name'));
            $this->app_bulk_pdf_export->export();
        }
    }

    public function zip_payments($id)
    {
        $has_permission_view = has_permission('payments', '', 'view');

        if (!$has_permission_view && !has_permission('invoices', '', 'view_own')
            && get_option('allow_staff_view_invoices_assigned') == '0') {
            access_denied('Zip Customer Payments');
        }

        $this->load->library('app_bulk_pdf_export', [
                'export_type'       => 'payments',
                'payment_mode'      => $this->input->post('paymentmode'),
                'date_from'         => $this->input->post('zip-from'),
                'date_to'           => $this->input->post('zip-to'),
                'redirect_on_error' => admin_url('products/product/' . $id . '?group=payments'),
            ]);

        $this->app_bulk_pdf_export->set_client_id($id);
        $this->app_bulk_pdf_export->set_client_id_column(db_prefix().'clients.userid');
        $this->app_bulk_pdf_export->in_folder($this->input->post('file_name'));
        $this->app_bulk_pdf_export->export();
    }

    public function import()
    {
        if (!has_permission('customers', '', 'create')) {
            access_denied('customers');
        }

        $dbFields = $this->db->list_fields(db_prefix().'contacts');
        foreach ($dbFields as $key => $contactField) {
            if ($contactField == 'phonenumber') {
                $dbFields[$key] = 'contact_phonenumber';
            }
        }

        $dbFields = array_merge($dbFields, $this->db->list_fields(db_prefix().'clients'));

        $this->load->library('import/import_customers', [], 'import');

        $this->import->setDatabaseFields($dbFields)
                     ->setCustomFields(get_custom_fields('customers'));

        if ($this->input->post('download_sample') === 'true') {
            $this->import->downloadSample();
        }

        if ($this->input->post()
            && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
            $this->import->setSimulation($this->input->post('simulate'))
                          ->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])
                          ->setFilename($_FILES['file_csv']['name'])
                          ->perform();


            $data['total_rows_post'] = $this->import->totalRows();

            if (!$this->import->isSimulation()) {
                set_alert('success', _l('import_total_imported', $this->import->totalImported()));
            }
        }

        $data['groups']    = $this->products_model->get_groups();
        $data['title']     = _l('import');
        $data['bodyclass'] = 'dynamic-create-groups';
        $this->load->view('admin/products/import', $data);
    }

    public function groups()
    {
        if (!is_admin()) {
            access_denied('Customer Groups');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('customers_groups');
        }
        $data['title'] = _l('customer_groups');
        $this->load->view('admin/products/groups_manage', $data);
    }

    public function group()
    {
        if (!is_admin() && get_option('staff_members_create_inline_customer_groups') == '0') {
            access_denied('Customer Groups');
        }

        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                $id      = $this->products_model->add_group($data);
                $message = $id ? _l('added_successfully', _l('customer_group')) : '';
                echo json_encode([
                    'success' => $id ? true : false,
                    'message' => $message,
                    'id'      => $id,
                    'name'    => $data['name'],
                ]);
            } else {
                $success = $this->products_model->edit_group($data);
                $message = '';
                if ($success == true) {
                    $message = _l('updated_successfully', _l('customer_group'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                ]);
            }
        }
    }

    public function delete_group($id)
    {
        if (!is_admin()) {
            access_denied('Delete Customer Group');
        }
        if (!$id) {
            redirect(admin_url('clients/groups'));
        }
        $response = $this->products_model->delete_group($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('customer_group')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('customer_group_lowercase')));
        }
        redirect(admin_url('clients/groups'));
    }

    public function bulk_action()
    {
        hooks()->do_action('before_do_bulk_action_for_customers');
        $total_deleted = 0;
        if ($this->input->post()) {
            $ids    = $this->input->post('ids');
            $groups = $this->input->post('groups');

            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if ($this->products_model->delete($id)) {
                            $total_deleted++;
                        }
                    } else {
                        if (!is_array($groups)) {
                            $groups = false;
                        }
                        $this->client_groups_model->sync_customer_groups($id, $groups);
                    }
                }
            }
        }

        if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_clients_deleted', $total_deleted));
        }
    }

    public function vault_entry_create($customer_id)
    {
        $data = $this->input->post();

        if (isset($data['fakeusernameremembered'])) {
            unset($data['fakeusernameremembered']);
        }

        if (isset($data['fakepasswordremembered'])) {
            unset($data['fakepasswordremembered']);
        }

        unset($data['id']);
        $data['creator']      = get_staff_user_id();
        $data['creator_name'] = get_staff_full_name($data['creator']);
        $data['description']  = nl2br($data['description']);
        $data['password']     = $this->encryption->encrypt($this->input->post('password', false));

        if (empty($data['port'])) {
            unset($data['port']);
        }

        $this->products_model->vault_entry_create($data, $customer_id);
        set_alert('success', _l('added_successfully', _l('vault_entry')));
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function vault_entry_update($entry_id)
    {
        $entry = $this->products_model->get_vault_entry($entry_id);

        if ($entry->creator == get_staff_user_id() || is_admin()) {
            $data = $this->input->post();

            if (isset($data['fakeusernameremembered'])) {
                unset($data['fakeusernameremembered']);
            }
            if (isset($data['fakepasswordremembered'])) {
                unset($data['fakepasswordremembered']);
            }

            $data['last_updated_from'] = get_staff_full_name(get_staff_user_id());
            $data['description']       = nl2br($data['description']);

            if (!empty($data['password'])) {
                $data['password'] = $this->encryption->encrypt($this->input->post('password', false));
            } else {
                unset($data['password']);
            }

            if (empty($data['port'])) {
                unset($data['port']);
            }

            $this->products_model->vault_entry_update($entry_id, $data);
            set_alert('success', _l('updated_successfully', _l('vault_entry')));
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function vault_entry_delete($id)
    {
        $entry = $this->products_model->get_vault_entry($id);
        if ($entry->creator == get_staff_user_id() || is_admin()) {
            $this->products_model->vault_entry_delete($id);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function vault_encrypt_password()
    {
        $id            = $this->input->post('id');
        $user_password = $this->input->post('user_password', false);
        $user          = $this->staff_model->get(get_staff_user_id());

        if (!app_hasher()->CheckPassword($user_password, $user->password)) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['error_msg' => _l('vault_password_user_not_correct')]);
            die;
        }

        $vault    = $this->products_model->get_vault_entry($id);
        $password = $this->encryption->decrypt($vault->password);

        $password = html_escape($password);

        // Failed to decrypt
        if (!$password) {
            header('HTTP/1.0 400 Bad error');
            echo json_encode(['error_msg' => _l('failed_to_decrypt_password')]);
            die;
        }

        echo json_encode(['password' => $password]);
    }

    public function get_vault_entry($id)
    {
        $entry = $this->products_model->get_vault_entry($id);
        unset($entry->password);
        $entry->description = clear_textarea_breaks($entry->description);
        echo json_encode($entry);
    }

    public function statement_pdf()
    {
        $customer_id = $this->input->get('customer_id');

        if (!has_permission('invoices', '', 'view') && !has_permission('payments', '', 'view')) {
            set_alert('danger', _l('access_denied'));
            redirect(admin_url('products/product/' . $customer_id));
        }

        $from = $this->input->get('from');
        $to   = $this->input->get('to');

        $data['statement'] = $this->products_model->get_statement($customer_id, to_sql_date($from), to_sql_date($to));

        try {
            $pdf = statement_pdf($data['statement']);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'D';
        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output(slug_it(_l('customer_statement') . '-' . $data['statement']['client']->company) . '.pdf', $type);
    }

    public function send_statement()
    {
        $customer_id = $this->input->get('customer_id');

        if (!has_permission('invoices', '', 'view') && !has_permission('payments', '', 'view')) {
            set_alert('danger', _l('access_denied'));
            redirect(admin_url('products/product/' . $customer_id));
        }

        $from = $this->input->get('from');
        $to   = $this->input->get('to');

        $send_to = $this->input->post('send_to');
        $cc      = $this->input->post('cc');

        $success = $this->products_model->send_statement_to_email($customer_id, $send_to, $from, $to, $cc);
        // In case client use another language
        load_admin_language();
        if ($success) {
            set_alert('success', _l('statement_sent_to_client_success'));
        } else {
            set_alert('danger', _l('statement_sent_to_client_fail'));
        }

        redirect(admin_url('products/product/' . $customer_id . '?group=statement'));
    }

    public function statement()
    {
        if (!has_permission('invoices', '', 'view') && !has_permission('payments', '', 'view')) {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }

        $customer_id = $this->input->get('customer_id');
        $from        = $this->input->get('from');
        $to          = $this->input->get('to');

        $data['statement'] = $this->products_model->get_statement($customer_id, to_sql_date($from), to_sql_date($to));

        $data['from'] = $from;
        $data['to']   = $to;

        $viewData['html'] = $this->load->view('admin/products/groups/_statement', $data, true);

        echo json_encode($viewData);
    }

    public function get_particulars_ordered_details($id)
    {
        $this->load->model('invoice_items_model');
        $details =$this->invoice_items_model->get_particulars_ordered_details($id);
        echo json_encode(array(
            'success'=>true,
            'data'=>$details
        ));
    }
    
}
