<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'libraries/import/App_import.php');

class Import_items extends App_import
{
    protected $notImportableFields = ['id','item_id','long_description','tax2', 'rate','group_id','rate_currency_1'];

    //protected $requiredFields = ['description'];
    protected $requiredFields = [];

    public function __construct()
    {
        $this->addItemsGuidelines();

        parent::__construct();
    }

    public function perform()
    {
        $this->initialize();

        $databaseFields      = $this->getImportableDatabaseFields();
        $totalDatabaseFields = count($databaseFields);
		$req_ins_id = $req_error = '';
        foreach ($this->getRows() as $rowNumber => $row) {
            $insert = $price_insert = [];
            for ($i = 0; $i < $totalDatabaseFields; $i++) {
                $row[$i] = $this->checkNullValueAddedByUser($row[$i]);

                if ($databaseFields[$i] == 'description' && $row[$i] == '') {
                    
                }elseif ($databaseFields[$i] == 'name' && $row[$i] == '') {
                  
                }elseif ($databaseFields[$i] == 'code' && $row[$i] == '') {
                    
                } elseif ($databaseFields[$i] == 'tax' ) {
                    $row[$i] = $this->taxValue($row[$i]);
                } elseif ($databaseFields[$i] == 'categoryid' ) {
                    $row[$i] = $this->catValue($row[$i]);
                }/*elseif (startsWith($databaseFields[$i], 'rate') && !is_numeric($row[$i])) {
                    $row[$i] = 0;
                } elseif ($databaseFields[$i] == 'group_id') {
                    $row[$i] = $this->groupValue($row[$i]);
                } elseif ($databaseFields[$i] == 'tax' || $databaseFields[$i] == 'tax2') {
                    $row[$i] = $this->taxValue($row[$i]);
                }*/
				if($databaseFields[$i]!='price' && $databaseFields[$i]!='currency' && $databaseFields[$i]!='tax' ){
					$insert[$databaseFields[$i]] = $row[$i];
				}	
				else{
					$price_insert[$databaseFields[$i]] = $insert[$databaseFields[$i]] = $row[$i];
				}
            }
            $insert = $this->trimInsertValues($insert);
            $price_insert = $this->trimInsertValues($price_insert);


            if (count($insert) > 0) {
				$this->ci->db->where('name', $insert['name']);
				$total_rows = $this->ci->db->count_all_results(db_prefix().'items');
				$this->ci->db->where('code', $insert['code']);
				$total_rows_1 = $this->ci->db->count_all_results(db_prefix().'items');
				
				$this->ci->db->where('name', $price_insert['currency']);
				$total_rows1 = $this->ci->db->count_all_results(db_prefix().'currencies');
				
				if($total_rows>0){
					$req_error .= 'Your given name already exist on row no '.$rowNumber.'.<br>';
					$this->error_rows($req_error);
				}
				if($total_rows_1>0){
					$req_error .= 'Your given code already exist on row no '.$rowNumber.'.<br>';
					$this->error_rows($req_error);
				}
				if(!isset($insert['name']) || $insert['name']==''){
					$req_error .= 'Please enter the all fields on row no '.$rowNumber.'.<br>';
					$this->error_rows($req_error);
				}
				$val_name = $val_code = $val_unit = $val_description = '';
				if (!preg_match('/[A-Za-z]/', $insert['name']) && !preg_match('/[0-9]/', $insert['name']) && !empty($insert['name']) )
				{
					$val_name = 1;
					$req_error .= 'Please enter the valid name on row no '.$rowNumber.'.<br>';
					$this->error_rows($req_error);
				}
				if (!preg_match('/[A-Za-z]/', $insert['code']) && !preg_match('/[0-9]/', $insert['code'])  && !empty($insert['code']) )
				{
					$val_code = 1;
					$req_error .= 'Please enter the valid code on row no '.$rowNumber.'.<br>';
					$this->error_rows($req_error);
				}
				if (!preg_match('/[A-Za-z]/', $insert['unit']) && !preg_match('/[0-9]/', $insert['unit'])  && !empty($insert['unit']) )
				{
					$val_unit = 1;
					$req_error .= 'Please enter the valid unit on row no '.$rowNumber.'.<br>';
					$this->error_rows($req_error);
				}
				if (!preg_match('/[A-Za-z]/', $insert['description']) && !preg_match('/[0-9]/', $insert['description'])  && !empty($insert['description']) )
				{
					$val_description = 1;
					$req_error .= 'Please enter the valid description on row no '.$rowNumber.'.<br>';
					$this->error_rows($req_error);
				}
				
				if($total_rows1<=0 && !empty($price_insert['currency'])){
					$req_error .= 'Please enter the correct currency name on row no '.$rowNumber.'.<br>';
					$this->error_rows($req_error);
				}
				
				if(isset($insert['name']) && $insert['name']!='' && ($total_rows1>0 || empty($price_insert['currency'])) && ($total_rows_1<=0 || empty($insert['code'])) && $val_name!=1 && ($val_code != 1 || empty($insert['code'])) && ($val_unit != 1 || empty($insert['unit'])) && ($val_description != 1 || empty($insert['description'])) ){
					$this->incrementImported();
				}

                $id = null;
                if (!$this->isSimulation()) {
					unset($insert['price']);
					unset($insert['currency']);
					unset($insert['tax']);
					if($total_rows<=0 && $total_rows_1<=0){
						if(isset($insert['name']) && $insert['name']!='' && ($total_rows1>0 || empty($price_insert['currency']))&& $val_name != 1 && ($val_code != 1  || empty($insert['code'])) && ($val_unit != 1 || empty($insert['unit'])) && ($val_description != 1 || empty($insert['description']))  && ($total_rows_1<=0 || empty($insert['code'])) ){
							$this->ci->db->insert(db_prefix().'items', $insert);
							$id = $req_ins_id = $price_insert['item_id']= $this->ci->db->insert_id();	
							$this->ci->db->insert(db_prefix().'item_price', $price_insert);
						}
						else{
							if( ($total_rows1>0 || empty($price_insert['currency']) ) && !empty($req_ins_id) && ($val_name!=1 || empty($insert['name']))  && ($val_code != 1 || empty($insert['code'])) && ($val_unit != 1 || empty($insert['unit'])) && ($val_description != 1 || empty($insert['description']))  && ($total_rows_1<=0 || empty($insert['code'])) ){
								$price_insert['item_id']= $req_ins_id;
								$this->ci->db->insert(db_prefix().'item_price', $price_insert);
							}
							
						}
					}
                } else {
					
                    $this->simulationData[$rowNumber] = $this->formatValuesForSimulation($insert);
                }

                $this->handleCustomFieldsInsert($id, $row, $i, $rowNumber, 'items_pr');
            }

            if ($this->isSimulation() && $rowNumber >= $this->maxSimulationRows) {
                break;
            }
        }
    }

    public function formatFieldNameForHeading($field)
    {
        $this->ci->load->model('currencies_model');

        if (strtolower($field) == 'group_id') {
            return 'Group';
        } elseif (startsWith($field, 'rate')) {
            $str = 'Rate - ';
            // Base currency
            if ($field == 'rate') {
                $str .= $this->ci->currencies_model->get_base_currency()->name;
            } else {
                $str .= $this->ci->currencies_model->get(strafter($field, 'rate_currency_'))->name;
            }

            return $str;
        }

        return parent::formatFieldNameForHeading($field);
    }

    protected function failureRedirectURL()
    {
        return admin_url('invoice_items/import');
    }

    private function addItemsGuidelines()
    {
        //$this->addImportGuidelinesInfo('In the column <b>Tax</b> and <b>Tax2</b>, you <b>must</b> add either the <b>TAX NAME or the TAX ID</b>, which you can get them by navigating to <a href="' . admin_url('taxes') . '" target="_blank">Setup->Finance->Taxes</a>.');
        $this->addImportGuidelinesInfo('In the column <b>Tax</b>, you <b>must</b> add either the <b>TAX NAME or the TAX ID</b>, which you can get them by navigating to <a href="' . admin_url('taxes') . '" target="_blank">Setup->Finance->Taxes</a>.');
        //$this->addImportGuidelinesInfo('In the column <b>Group</b>, you <b>must</b> add either the <b>GROUP NAME or the GROUP ID</b>, which you can get them by clicking <a href="' . admin_url('invoice_items?groups_modal=true') . '" target="_blank">here</a>.');
        $this->addImportGuidelinesInfo('In the column <b>Category id</b>, you <b>must</b> add <b>CATEGORY Id or name</b>, which you can get them by clicking <a href="' . admin_url('category') . '" target="_blank">here</a>.');
        $this->addImportGuidelinesInfo('In the column <b>currency</b>, you <b>must</b> add <b>CURRENCY name</b>, which you can get them by clicking <a href="' . admin_url('currencies') . '" target="_blank">here</a>.');
    }

    private function formatValuesForSimulation($values)
    {
        foreach ($values as $column => $val) {
            if ($column == 'group_id' && !empty($val) && is_numeric($val)) {
                $group = $this->getGroupBy('id', $val);
                if ($group) {
                    $values[$column] = $group->name;
                }
            } elseif (($column == 'tax' || $column == 'tax2') && !empty($val) && is_numeric($val)) {
                $tax = $this->getTaxBy('id', $val);
                if ($tax) {
                    $values[$column] = $tax->name . ' (' . $tax->taxrate . '%)';
                }
            }
        }

        return $values;
    }

    private function getTaxBy($field, $idOrName)
    {
        $this->ci->db->where($field, $idOrName);

        return $this->ci->db->get(db_prefix().'taxes')->row();
    }

    private function getGroupBy($field, $idOrName)
    {
        $this->ci->db->where($field, $idOrName);

        return $this->ci->db->get(db_prefix().'items_groups')->row();
    }

    private function catValue($value)
    {
        if ($value != '') {
			if (!is_numeric($value)) {
				$this->ci->db->where('cat_name', $value);

				$req_val = $this->ci->db->get(db_prefix().'item_category')->row();
				$value = $req_val ? $req_val->id : 0;
			}
			else{
				$this->ci->db->where('id', $value);

				$req_val = $this->ci->db->get(db_prefix().'item_category')->row();
				$value = $req_val ? $req_val->id : 0;
			}
            
        } else {
            $value = 0;
        }

        return $value;
    }
	private function taxValue($value)
    {
        if ($value != '') {
            if (!is_numeric($value)) {
                $tax   = $this->getTaxBy('name', $value);
                $value = $tax ? $tax->id : 0;
            }
			else{
				 $tax   = $this->getTaxBy('id', $value);
                $value = $tax ? $tax->id : 0;
			}
        } else {
            $value = 0;
        }

        return $value;
    }

    private function groupValue($value)
    {
        if ($value != '') {
            if (!is_numeric($value)) {
                $group = $this->getGroupBy('name', $value);
                $value = $group ? $group->id : 0;
            }
        } else {
            $value = 0;
        }

        return $value;
    }
}
