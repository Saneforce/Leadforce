<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Invoice_items extends AdminController
{
    private $not_importable_fields = ['id'];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_items_model');
    }

    /* List all available items */
    public function index()
    {
        if (!has_permission('items', '', 'view')) {
            access_denied('Invoice Items');
        }

        $this->load->model('taxes_model');
        $data['categories']   = $this->invoice_items_model->get_category();
        $data['taxes']        = $this->taxes_model->get();
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $data['title'] = _l('invoice_items');
        $this->load->view('admin/invoice_items/manage', $data);
    }
	public function ajax_client()
    {
        $data = $this->input->post();
        $data['category'] = $data['category'];
        if ($this->input->is_ajax_request()) {
			$this->db->where('cat_name', $this->input->post('category'));
            $total_rows = $this->db->count_all_results(db_prefix().'item_category');
			if($total_rows<=0){
            $data['id']  = $this->invoice_items_model->addCategory($data);
            $data['message']  = _l('added_successfully', _l('category'));
			}
			else{
				 $data['message']  = 'Category Aleady Exists';
			}
        }
		$data['company'] = $data['category'];
        echo json_encode($data);
        exit();

    }
    public function table()
    {
        if (!has_permission('items', '', 'view')) {
            ajax_access_denied();
        }
        $this->app->get_table_data('invoice_items');
    }

    /* Edit or update items / ajax request /*/
    public function manage()
    {
        if (has_permission('items', '', 'view')) {
            if ($this->input->post()) {
                $data = $this->input->post();
                if ($data['itemid'] == '') {
                    if (!has_permission('items', '', 'create')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    $id      = $this->invoice_items_model->add($data);
                    $success = false;
                    $message = '';
                    if ($id) {
                        $success = true;
                        $message = _l('added_successfully', _l('sales_item'));
                    }
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                        'item'    => $this->invoice_items_model->get($id),
                    ]);
                } else {
                    if (!has_permission('items', '', 'edit')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    $success = $this->invoice_items_model->edit($data);
                    $message = '';
                    if ($success) {
                        $message = _l('updated_successfully', _l('sales_item'));
                    }
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                    ]);
                }
            }
        }
    }

    public function import()
    {
        if (!has_permission('items', '', 'create')) {
            access_denied('Items Import');
        }
 $dbFields = $this->db->list_fields(db_prefix() . 'items');
 //$dbFields = array('name','code','categoryid','tax','description');
 $dbFields = array_merge($dbFields, $this->db->list_fields(db_prefix().'item_price'));
 //$dbFields = array_merge($dbFields, get_custom_fields('items'));
 if (($key = array_search('tax', $dbFields)) !== false) {
				unset($dbFields[$key]);
			}
        $this->load->library('import/import_items', [], 'import');
$this->import->setDatabaseFields($dbFields)->setCustomFields(get_custom_fields('items'));
       /* $this->import->setDatabaseFields($this->db->list_fields(db_prefix().'items'))
                     ->setCustomFields(get_custom_fields('items'));*/

        if ($this->input->post('download_sample') === 'true') {
			$req_items = get_custom_fields('items');
			$row1 = $this->db->get(db_prefix() . 'items')->row();
			$this->db->where('id ', $row1->tax);
			$row2 = $this->db->get(db_prefix() . 'taxes')->row();
			$this->db->where('item_id ', $row1->id);
			$row3 = $this->db->get(db_prefix() . 'item_price')->row();
			if(!empty($row1)){
				$req_datas = array($row1->name,$row1->code,$row1->categoryid,$row1->description,$row1->unit,$row2->name,$row3->price,$row3->currency);
			}
			else{
				$req_datas = array('sample','sample','sample','sample description','testuser','1','100','INR');
			}
			$custom_item = array();
			if(!empty($req_items)){
				foreach($req_items as $req_item12){
					if($req_item12['active'] == 1){
						$custom_item[] = $req_item12['name'];
						$this->db->where('fieldid ', $req_item12['id']);
						$row = $this->db->get(db_prefix() . 'customfieldsvalues')->row();
						if(!empty($row1)){
							$req_datas[] = $row->value;
						}
						else{
							$req_datas[] = 'Sample';
						}
					}
				}
				$dbFields = array_merge($dbFields, $custom_item);
				//$req_datas = array_merge($req_datas, $req_output);
			}
			
            $this->import->downloadSample();
		 /*  if (($key = array_search('item_id', $dbFields)) !== false) {
				unset($dbFields[$key]);
			}
			if (($key = array_search('id', $dbFields)) !== false) {
				unset($dbFields[$key]);
			}
			if (($key = array_search('long_description', $dbFields)) !== false) {
				unset($dbFields[$key]);
			}
			if (($key = array_search('rate', $dbFields)) !== false) {
				unset($dbFields[$key]);
			}
			
			if (($key = array_search('tax2', $dbFields)) !== false) {
				unset($dbFields[$key]);
			}
			if (($key = array_search('group_id', $dbFields)) !== false) {
				unset($dbFields[$key]);
			}
			if (($key = array_search('rate_currency_1', $dbFields)) !== false) {
				unset($dbFields[$key]);
			}
			if (($key = array_search('id', $dbFields)) !== false) {
				unset($dbFields[$key]);
			}
			header('Content-Type: text/csv; charset=utf-8');  
			header('Content-Disposition: attachment; filename=sample_import_file.csv');  
			$output = fopen("php://output", "w");  
			fputcsv($output, $dbFields);  
			
			if(!empty($req_datas)){
				fputcsv($output, $req_datas);
			}
			
			fclose($output); exit; */
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
				$get_error_rows  = $this->import->get_error_rows();
				$this->import->clear_error_rows();
				if(!empty($get_error_rows)){
					set_debug_alert($get_error_rows);
				}
				 redirect(admin_url('invoice_items/import'));
            }
        }

        $data['title'] = _l('import');
        $this->load->view('admin/invoice_items/import', $data);
    }

    public function add_group()
    {
        if ($this->input->post() && has_permission('items', '', 'create')) {
            $this->invoice_items_model->add_group($this->input->post());
            set_alert('success', _l('added_successfully', _l('item_group')));
        }
    }

    public function update_group($id)
    {
        if ($this->input->post() && has_permission('items', '', 'edit')) {
            $this->invoice_items_model->edit_group($this->input->post(), $id);
            set_alert('success', _l('updated_successfully', _l('item_group')));
        }
    }
	public function item_exists()
    {
        if ($this->input->post()) {
            $cat_id = $this->input->post('itemid');
            if ($cat_id != '') {
                $this->db->where('id !=', $cat_id);
				$this->db->where('name', $this->input->post('name'));
				$total_rows = $this->db->count_all_results(db_prefix().'items');
               if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
            }
            $this->db->where('name', $this->input->post('name'));
            $total_rows = $this->db->count_all_results(db_prefix().'items');
            if ($total_rows > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
            die();
        }
    }
	public function code_exists()
    {
        if ($this->input->post()) {
            $cat_id = $this->input->post('itemid');
            if ($cat_id != '') {
                $this->db->where('id !=', $cat_id);
				$this->db->where('code', $this->input->post('code'));
				$total_rows = $this->db->count_all_results(db_prefix().'items');
               if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
            }
            $this->db->where('code', $this->input->post('code'));
            $total_rows = $this->db->count_all_results(db_prefix().'items');
            if ($total_rows > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
            die();
        }
    }
    public function delete_group($id)
    {
        if (has_permission('items', '', 'delete')) {
            if ($this->invoice_items_model->delete_group($id)) {
                set_alert('success', _l('deleted', _l('item_group')));
            }
        }
        redirect(admin_url('invoice_items?groups_modal=true'));
    }

    /* Delete item*/
    public function delete($id)
    {
        if (!has_permission('items', '', 'delete')) {
            access_denied('Invoice Items');
        }

        if (!$id) {
            redirect(admin_url('invoice_items'));
        }

        $response = $this->invoice_items_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('invoice_item_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('invoice_item')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('invoice_item_lowercase')));
        }
        redirect(admin_url('invoice_items'));
    }

    public function bulk_action()
    {
        hooks()->do_action('before_do_bulk_action_for_items');
        $total_deleted = 0;
        if ($this->input->post()) {
            $ids                   = $this->input->post('ids');
            $has_permission_delete = has_permission('items', '', 'delete');
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if ($has_permission_delete) {
                            if ($this->invoice_items_model->delete($id)) {
                                $total_deleted++;
                            }
                        }
                    }
                }
            }
        }

        if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_items_deleted', $total_deleted));
        }
    }

    public function search()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            echo json_encode($this->invoice_items_model->search($this->input->post('q')));
        }
    }

    /* Get item by id / ajax */
    public function get_item_by_id($id)
    {
        if ($this->input->is_ajax_request()) {
            $item                     = $this->invoice_items_model->get($id);
            //$item->long_description   = nl2br($item->long_description);
            $item->description   = nl2br($item->description);
            $item->custom_fields_html = render_custom_fields('items', $id, [], ['items_pr' => true]);
            $item->custom_fields      = $item->unit_prices = [];

            $cf = get_custom_fields('items');

            foreach ($cf as $custom_field) {
                $val = get_custom_field_value($id, $custom_field['id'], 'items_pr');
                if ($custom_field['type'] == 'textarea') {
                    $val = clear_textarea_breaks($val);
                }
                $custom_field['value'] = $val;
                $item->custom_fields[] = $custom_field;
            }
			 $item->unit_prices = $unit_prices = $this->invoice_items_model->get_items_unit_prices($id);
			 $item->price_html = '';
			 $this->load->model('taxes_model');
			$taxes        = $this->taxes_model->get();
			 $added_currencies = $added_prices = array();
			  $this->load->model('currencies_model');
			$currencies = $this->currencies_model->get();
			 if(!empty($unit_prices)){
				 $i = 0;
				foreach($unit_prices as $unit_price1){
					if($i!=0 && !in_array($unit_price1['id'], $added_prices)){
						$item->price_html .= '<div class="price1-html" style="margin-top:10px;height:40px;"><div class="col-md-4">
								<input type="number" name="unit_price[]" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" placeholder="Price" value="'.$unit_price1['price'].'"  class="form-control" /> 
							</div>
							<div class="col-md-3">
								<select name="currency[]" class="form-control" >';
						foreach($currencies as $val) {
							if (!in_array($val['name'], $added_currencies)) {
								if($val['name'] == $unit_price1['currency']){
									$item->price_html .= '<option value="'.$val["name"].'" selected>'.$val["name"].'</option>';
								}else{
									$item->price_html .= '<option value="'.$val["name"].'">'.$val["name"].'</option>';
								}
							}
						} 
						$item->price_html .= '</select>';
						$item->price_html .= '</div>';
						$item->price_html .= '<div class="col-md-3">
                <select name="tax[]" class="form-control" ><option value="">Select Tax</option>';
				
                foreach($taxes as $val) {
					if($unit_price1['tax'] == $val["id"]){
                       $item->price_html .= '<option value="'.$val["id"].'" selected>'.$val["taxrate"].'%'.$val["name"].'</option>';
					}else{
						 $item->price_html .= '<option value="'.$val["id"].'">'.$val["taxrate"].'%<sub>'.$val["name"].'</sub></option>';
					}
                } 
                $item->price_html .= '</select>';
                $item->price_html .= '</div>';
					$item->price_html .= '<a href="javascript:void(0);" class="remove_button" title="Remove field" style="position:relative; top:8px; left:15px"><i class="fa fa-trash"></i></a>
							</div>';
					}
					array_push($added_currencies, $unit_price1['currency']);
					array_push($added_prices, $unit_price1['id']);
					$i++;
				}
			 }
			 else{
				 $item->unit_prices[0]['price']= '';
			 }
            echo json_encode($item);
        }
    }
	public function getaddfields() {
        $this->load->model('currencies_model');
        $currencies = $this->currencies_model->get();
        $postcnt = count($_POST['currency']);
        $curcnt = count($currencies);
        $html = '';
		$this->load->model('taxes_model');
        $taxes        = $this->taxes_model->get();
        if($postcnt != $curcnt) {
            $html .= '<div style="margin-top:10px;height:40px;" class="price1-html"><div class="col-md-4">
                <input type="number" name="unit_price[]" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" placeholder="Price" value=""  class="form-control" /> 
            </div>
            <div class="col-md-3">
                <select name="currency[]" class="form-control" >';
                foreach($currencies as $val) {
                    if (!in_array($val['name'], $_POST['currency'])) {
                        $html .= '<option value="'.$val["name"].'">'.$val["name"].'</option>';
                    }
                } 
                $html .= '</select>';
                $html .= '</div>';
				$html .= '<div class="col-md-3">
                <select name="tax[]" class="form-control" ><option value="">Select Tax</option>';
				
                foreach($taxes as $val) {
                       $html .= '<option value="'.$val["id"].'">'.$val["taxrate"].'%'.$val["name"].'</option>';
                } 
                $html .= '</select>';
                $html .= '</div>';
            $html .='<a href="javascript:void(0);" class="remove_button" title="Remove field" style="position:relative; top:8px; left:15px"><i class="fa fa-trash"></i></a>
            </div>';
            echo $html;
        }
        exit;
    }
	public function getpricebyid() {
        $this->load->model('currencies_model');
        if($_POST['currency']) {
            $cur = $_POST['currency'];
        } else {
            $currency = $this->currencies_model->get_base_currency();
            $cur = $currency->name;
        }
        $data = $this->invoice_items_model->getitem_price($cur);
        echo json_encode($data);
        exit();
    }
	 public function getaddproductfields() {
        $this->load->model('currencies_model');
        //pre($_POST);
        if($_POST['currency']) {
            $cur = $_POST['currency'];
        } else {
            $currency = $this->currencies_model->get_base_currency();
            $cur = $currency->name;
        }
        $data = $this->invoice_items_model->get_items($cur);
        $html = '';
            $html .= '<div style="height:40px; clear:both;" class="productdiv css-table-row" id="'.$_POST['length'].'"><div class="">
            <select name="product[]" class="form-control" onchange="getprice1(this,'.$_POST['length'].')"><option value="">--Select Item--</option>';
            foreach($data as $val) {
                    $html .= '<option value="'.$val["id"].'">'.$val["name"].'</option>';
            } 
            $html .= '</select>';
            $html .= '</div>
            <div class="">
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
}
