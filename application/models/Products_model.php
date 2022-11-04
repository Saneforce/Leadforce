<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Products_model extends App_Model {

    private $contact_columns;

    public function __construct() {
        parent::__construct();

        $this->contact_columns = hooks()->apply_filters('contact_columns', ['firstname', 'lastname', 'email', 'phonenumber', 'title', 'password', 'send_set_password_email', 'donotsendwelcomeemail', 'permissions', 'direction', 'invoice_emails', 'estimate_emails', 'credit_note_emails', 'contract_emails', 'task_emails', 'project_emails', 'ticket_emails', 'is_primary']);

        $this->load->model(['client_vault_entries_model', 'client_groups_model', 'statement_model']);
    }

    /**
     * Get client object based on passed clientid if not passed clientid return array of all clients
     * @param  mixed $id    client id
     * @param  array  $where
     * @return mixed
     */
    public function getprod_price($name = '') {
        return $this->db->query('SELECT a.id as id, a.name as name  FROM ' . db_prefix() . 'products as a JOIN ' . db_prefix() . 'unit_price as b ON b.productid=a.id  where  a.id = b.productid and b.currency = "'.$name.'"')->result_array();
    }

    public function getpricebyid($name = '') {
        $res = $this->db->query('SELECT id,tax,price,item_id,currency FROM ' . db_prefix() . 'item_price where item_id = "'.$_POST['value'].'" and currency = "'.$name.'"')->result_array();
        if($res[0]['tax'] > 0) {
            $this->db->select('item_price.id,item_price.item_id,item_price.tax,item_price.price,item_price.id,'.db_prefix() . 'item_price.currency,' . db_prefix() . 'taxes.taxrate as prodtax');
            $this->db->where(db_prefix() . 'item_price.item_id', $_POST['value']);
            $this->db->where(db_prefix() . 'item_price.currency', $name);
            $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id=' . db_prefix() . 'item_price.item_id');
            $this->db->join(db_prefix() . 'taxes', db_prefix() . 'taxes.id=' . db_prefix() . 'item_price.tax');
            $products = $this->db->get(db_prefix() . 'item_price')->result_array();
        } else {
            $this->db->select('item_price.*');
            $this->db->where(db_prefix() . 'item_price.item_id', $_POST['value']);
            $this->db->where(db_prefix() . 'item_price.currency', $name);
            $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id=' . db_prefix() . 'item_price.item_id');
            $products = $this->db->get(db_prefix() . 'item_price')->result_array();
            $products[0]['prodtax']  = 0;
        }
        return $products;
    }

    public function get($id = '', $where = []) {
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'products.id', $id);
            $client = $this->db->get(db_prefix() . 'products')->row();
            $GLOBALS['client'] = $client;
            return $client;
        }
        $this->db->order_by('created_date', 'asc');
        return $this->db->get(db_prefix() . 'products')->result_array();
    }

    public function add_deals_products($data, $id, $currency) {
        $i = 0;
        $insertdate = array();
        $insertdate['projectid'] = $id;
        foreach($data['product'] as $val) {
            if($val) {
                $insertdate['productid'] = $val;
                $insertdate['currency'] = $currency;
                $insertdate['price'] = $data['price'][$i];
                $insertdate['quantity'] = $data['qty'][$i];
                $insertdate['total_price'] = $data['total'][$i];
                $this->db->insert(db_prefix() . 'project_products', $insertdate);
                $userid = $this->db->insert_id();
            }
            $i++;
        }
        return $userid;
    }

    public function save_deals_products($data, $id, $currency) {
        $this->db->where('projectid',$id);
        $this->db->delete(db_prefix() . 'project_products');
        $i = 0;
        
        foreach($data['product'] as $val) {
            if($val) {
                $insertdate = array();
                $insertdate['projectid'] = $id;
                $insertdate['productid'] = $val;
                $insertdate['currency'] = $currency;
                $insertdate['price'] = $data['price'][$i];
                $insertdate['quantity'] = $data['qty'][$i];
                $insertdate['total_price'] = $data['total'][$i];
                $insertdate['method'] = $data['method'];
                if($data['tax'][$i]) {
                    $insertdate['tax'] = $data['tax'][$i];
                }
                if($data['discount'][$i]) {
                    $insertdate['discount'] = $data['discount'][$i];
                }
                if($data['status'][$i]) {
                    $insertdate['status'] = 1;
                } else {
                    $insertdate['status'] = 0;
                }
                if($data['variation'][$i] && $data['variation'][$i] > 0) {
                    $insertdate['variation'] = $data['variation'][$i];
                }
                $this->db->insert(db_prefix() . 'project_products', $insertdate);
            }
            $i++;
        }
        $update['project_cost'] = $data['grandtotal'];
        $update['project_currency'] = $currency;
        $this->db->where('id', $id);
        $updateid = $this->db->update(db_prefix() . 'projects', $update);
        return $updateid;
    }

    public function getCategories($id = '', $where = []) {
            $this->db->where($where);
            if (is_numeric($id)) {
                $this->db->where('id', $id);
            }
            $category = $this->db->get(db_prefix() . 'product_category')->result_array();
            return $category;
    }

    public function getdeals_products($proj_id, $currency) {
        // return $this->db->query('SELECT a.*,b.tax as prod_tax FROM 
        // ' . db_prefix() . 'project_products as a JOIN ' . db_prefix() . 'products as b ON b.id=a.productid')
        // ->result_array();
        $this->db->where('projectid', $proj_id);
        $this->db->where('currency', $currency);
        $products = $this->db->get(db_prefix() . 'project_products')->result_array();
         //echo $this->db->last_query();
         //exit;
        return $products;
    }

    public function getsales_products($proj_id, $currency, $type) {
        // return $this->db->query('SELECT a.*,b.tax as prod_tax FROM 
        // ' . db_prefix() . 'project_products as a JOIN ' . db_prefix() . 'products as b ON b.id=a.productid')
        // ->result_array();
        if(is_numeric($currency)) {
            $currency = $this->currencies_model->get($currency);
            $cur = $currency->name;
        } else {
            $cur = $currency;
        }
        $this->db->where('projectid', $proj_id);
        $this->db->where('currency', $cur);
        $this->db->where('rel_type', $type);
        $products = $this->db->get(db_prefix() . 'itemable')->result_array();
         //echo $this->db->last_query();
         //exit;
        return $products;
    }

    public function gettaxprods() {
        $this->db->where('projectid', $_POST['project']);
        //$this->db->where('method', 2);
        $products = $this->db->get(db_prefix() . 'project_products')->result_array();
        return $products;
    }

    public function getprods($currency) {
        $this->db->where('projectid', $_POST['projectnew']);
        $this->db->where('currency', $currency);
        //$this->db->where('method', 1);
        $products = $this->db->get(db_prefix() . 'project_products')->result_array();
        if($products) {
            return $products;
        } else {
            $data = array();
            $this->db->where('projectid', $_POST['projectnew']);
            $products = $this->db->get(db_prefix() . 'project_products')->result_array();
            foreach($products as $prod) {
                if($prod['variation']) {
                    $this->db->where('variationid', $prod['variation']);
                    $this->db->where('productid', $prod['productid']);
                    $this->db->where('currency', $currency);
                    $unitprice = $this->db->get(db_prefix() . 'variation_price')->result_array();
                    if($unitprice) {
                        $newcur_prod = array();
                        $newcur_prod['id'] = $prod['id'];
                        $newcur_prod['projectid'] = $prod['projectid'];
                        $newcur_prod['productid'] = $prod['productid'];
                        $newcur_prod['currency'] = $currency;
                        $newcur_prod['variation'] = $prod['variation'];
                        $newcur_prod['quantity'] = $prod['quantity'];
                        $newcur_prod['tax'] = $prod['tax'];
                        $newcur_prod['discount'] = $prod['discount'];
                        $newcur_prod['method'] = $prod['method'];
                        $newcur_prod['status'] = $prod['status'];
                        $newcur_prod['created_date'] = $prod['created_date'];
                        $newcur_prod['price'] = $unitprice[0]['variation_price'];
                        if($prod['discount'] && $prod['discount'] > 0) {
                            $dec = ($prod['discount'] / 100); //its convert 10 into 0.10
                            $mult = ($unitprice[0]['variation_price']*$prod['quantity']) - $dec; // gives the value for subtract from main value
                            $newcur_prod['total_price'] = $mult;
                        } else {
                            $mult = ($unitprice[0]['variation_price']*$prod['quantity']);
                            $newcur_prod['total_price'] = $mult;
                        }
                        $data[] = $newcur_prod;
                        //pre($data);
                    }
                } else {
                    $this->db->where('productid', $prod['productid']);
                    $this->db->where('currency', $currency);
                    $unitprice = $this->db->get(db_prefix() . 'unit_price')->result_array();
                    if($unitprice) {
                        $newcur_prod = array();
                        $newcur_prod['id'] = $prod['id'];
                        $newcur_prod['projectid'] = $prod['projectid'];
                        $newcur_prod['productid'] = $prod['productid'];
                        $newcur_prod['currency'] = $currency;
                        $newcur_prod['variation'] = $prod['variation'];
                        $newcur_prod['quantity'] = $prod['quantity'];
                        $newcur_prod['tax'] = $prod['tax'];
                        $newcur_prod['discount'] = $prod['discount'];
                        $newcur_prod['method'] = $prod['method'];
                        $newcur_prod['status'] = $prod['status'];
                        $newcur_prod['created_date'] = $prod['created_date'];
                        $newcur_prod['price'] = $unitprice[0]['price'];
                        if($prod['discount'] && $prod['discount'] > 0) {
                            $dec = ($prod['discount'] / 100); //its convert 10 into 0.10
                            $mult = ($unitprice[0]['price']*$prod['quantity']) - $dec; // gives the value for subtract from main value
                            $newcur_prod['total_price'] = $mult;
                        } else {
                            $mult = ($unitprice[0]['price']*$prod['quantity']);
                            $newcur_prod['total_price'] = $mult;
                        }
                        $data[] = $newcur_prod;
                    }
                }
            }
            return $data;
        }
    }

    public function getdealprods() {
        $this->db->where('projectid', $_POST['project']);
        $products = $this->db->get(db_prefix() . 'project_products')->result_array();
        if($products) {
            return $products;
        } else {
            return false;
        }
    }

    public function getinvoiceprods() {
        $this->db->where('projectid', $_POST['project']);
        $products = $this->db->get(db_prefix() . 'itemable')->result_array();
        if($products) {
            return $products;
        } else {
            return false;
        }
    }

    public function getproposalprods() {
        $this->db->where('projectid', $_POST['project']);
        $products = $this->db->get(db_prefix() . 'itemable')->result_array();
        if($products) {
            return $products;
        } else {
            return false;
        }
    }


    public function getnotaxprods($currency) {
        $this->db->where('projectid', $_POST['project']);
        $this->db->where('currency', $currency);
        //$this->db->where('method', 1);
        $products = $this->db->get(db_prefix() . 'project_products')->result_array();
        if($products) {
            return $products;
        } else {
            $data = array();
            $this->db->where('projectid', $_POST['project']);
            $products = $this->db->get(db_prefix() . 'project_products')->result_array();
            foreach($products as $prod) {
                /*if($prod['variation']) {
                    $this->db->where('variationid', $prod['variation']);
                    $this->db->where('productid', $prod['productid']);
                    $this->db->where('currency', $currency);
                    $unitprice = $this->db->get(db_prefix() . 'variation_price')->result_array();
                    if($unitprice) {
                        $newcur_prod = array();
                        $newcur_prod['id'] = $prod['id'];
                        $newcur_prod['projectid'] = $prod['projectid'];
                        $newcur_prod['productid'] = $prod['productid'];
                        $newcur_prod['currency'] = $currency;
                        $newcur_prod['variation'] = $prod['variation'];
                        $newcur_prod['quantity'] = $prod['quantity'];
                        $newcur_prod['tax'] = $prod['tax'];
                        $newcur_prod['discount'] = $prod['discount'];
                        $newcur_prod['method'] = $prod['method'];
                        $newcur_prod['status'] = $prod['status'];
                        $newcur_prod['created_date'] = $prod['created_date'];
                        $newcur_prod['price'] = $unitprice[0]['variation_price'];
                        if($prod['discount'] && $prod['discount'] > 0) {
                            $dec = ($prod['discount'] / 100); //its convert 10 into 0.10
                            $mult = ($unitprice[0]['variation_price']*$prod['quantity']) - $dec; // gives the value for subtract from main value
                            $newcur_prod['total_price'] = $mult;
                        } else {
                            $mult = ($unitprice[0]['variation_price']*$prod['quantity']);
                            $newcur_prod['total_price'] = $mult;
                        }
                        $data[] = $newcur_prod;
                        //pre($data);
                    }
                }*/ //else {
                   // $this->db->where('productid', $prod['productid']);
                    $this->db->where('item_id', $prod['productid']);
                    $this->db->where('currency', $currency);
                   // $unitprice = $this->db->get(db_prefix() . 'unit_price')->result_array();
                    $unitprice = $this->db->get(db_prefix() . 'item_price')->result_array();
                    if($unitprice) {
                        $newcur_prod = array();
                        $newcur_prod['id'] = $prod['id'];
                        $newcur_prod['projectid'] = $prod['projectid'];
                        $newcur_prod['productid'] = $prod['productid'];
                        $newcur_prod['currency'] = $currency;
                        $newcur_prod['variation'] = $prod['variation'];
                        $newcur_prod['quantity'] = $prod['quantity'];
                        $newcur_prod['tax'] = $prod['tax'];
                        $newcur_prod['discount'] = $prod['discount'];
                        $newcur_prod['method'] = $prod['method'];
                        $newcur_prod['status'] = $prod['status'];
                        $newcur_prod['created_date'] = $prod['created_date'];
                        $newcur_prod['price'] = $unitprice[0]['price'];
                        if($prod['discount'] && $prod['discount'] > 0) {
                            $dec = ($prod['discount'] / 100); //its convert 10 into 0.10
                            $mult = ($unitprice[0]['price']*$prod['quantity']) - $dec; // gives the value for subtract from main value
                            $newcur_prod['total_price'] = $mult;
                        } else {
                            $mult = ($unitprice[0]['price']*$prod['quantity']);
                            $newcur_prod['total_price'] = $mult;
                        }
                        $data[] = $newcur_prod;
                    }
               // }
            }
            return $data;
        }
    }

    public function getextaxprods($currency) {
        //pre($_POST);
       // $this->db->select('tblproject_products.*,tblproducts.tax as prodtax');
        $this->db->select(db_prefix().'project_products.*,'.db_prefix() . 'items.tax as prodtax');
        $this->db->where('projectid', $_POST['project']);
        $this->db->where('currency', $currency);
        //$this->db->where('method', 3);
        //$this->db->join(db_prefix() . 'products', db_prefix() . 'products.id=' . db_prefix() . 'project_products.productid');
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id=' . db_prefix() . 'project_products.productid');
        $products = $this->db->get(db_prefix() . 'project_products')->result_array();
        //pre($products);
        if($products) {
            return $products;
        } else {
            $data = array();
            $this->db->where('projectid', $_POST['project']);
            $products = $this->db->get(db_prefix() . 'project_products')->result_array();
            foreach($products as $prod) {
               /* if($prod['variation']) {
                    $this->db->where('variationid', $prod['variation']);
                    $this->db->where('productid', $prod['productid']);
                    $this->db->where('currency', $currency);
                    $unitprice = $this->db->get(db_prefix() . 'variation_price')->result_array();
                    if($unitprice) {
                        $this->db->where('id', $prod['productid']);
                        $prod_tax = $this->db->get(db_prefix() . 'products')->result_array();
                        $newcur_prod = array();
                        $newcur_prod['id'] = $prod['id'];
                        $newcur_prod['projectid'] = $prod['projectid'];
                        $newcur_prod['productid'] = $prod['productid'];
                        $newcur_prod['currency'] = $currency;
                        $newcur_prod['variation'] = $prod['variation'];
                        $newcur_prod['quantity'] = $prod['quantity'];
                        $newcur_prod['prodtax'] = $prod_tax[0]['tax'];
                        $newcur_prod['discount'] = $prod['discount'];
                        $newcur_prod['method'] = $prod['method'];
                        $newcur_prod['status'] = $prod['status'];
                        $newcur_prod['created_date'] = $prod['created_date'];
                        $newcur_prod['price'] = $unitprice[0]['variation_price'];
                        if($prod['discount'] && $prod['discount'] > 0) {
                            $dec = ($prod['discount'] / 100); //its convert 10 into 0.10
                            $mult = ($unitprice[0]['variation_price']*$prod['quantity']) - $dec; // gives the value for subtract from main value
                            $newcur_prod['total_price'] = $mult;
                        } else {
                            $mult = ($unitprice[0]['variation_price']*$prod['quantity']);
                            $newcur_prod['total_price'] = $mult;
                        }
                        $data[] = $newcur_prod;
                        //pre($data);
                    }
                }*/ //else {
                   // $this->db->where('productid', $prod['productid']);
                    $this->db->where('item_id', $prod['productid']);
                    $this->db->where('currency', $currency);
                    //$unitprice = $this->db->get(db_prefix() . 'unit_price')->result_array();
                    $unitprice = $this->db->get(db_prefix() . 'item_price')->result_array();
                    if($unitprice) {
                        //$this->db->where('id', $prod['productid']);
                        $this->db->where('id', $prod['productid']);
                       // $prod_tax = $this->db->get(db_prefix() . 'products')->result_array();
                        $prod_tax = $this->db->get(db_prefix() . 'items')->result_array();
                        $newcur_prod = array();
                        $newcur_prod['id'] = $prod['id'];
                        $newcur_prod['projectid'] = $prod['projectid'];
                        $newcur_prod['productid'] = $prod['productid'];
                        $newcur_prod['currency'] = $currency;
                        $newcur_prod['variation'] = $prod['variation'];
                        $newcur_prod['quantity'] = $prod['quantity'];
                       // $newcur_prod['prodtax'] = $prod_tax[0]['tax'];
                        $newcur_prod['prodtax'] = $unitprice[0]['tax'];
                        $newcur_prod['discount'] = $prod['discount'];
                        $newcur_prod['method'] = $prod['method'];
                        $newcur_prod['status'] = $prod['status'];
                        $newcur_prod['created_date'] = $prod['created_date'];
                        $newcur_prod['price'] = $unitprice[0]['price'];
                        if($prod['discount'] && $prod['discount'] > 0) {
                            $dec = ($prod['discount'] / 100); //its convert 10 into 0.10
                            $multqty = ($unitprice[0]['price']*$prod['quantity']);
                            $mult = $multqty*$dec; // gives the value for subtract from main value
                            $newcur_prod['total_price'] = $multqty - $mult;
                        } else {
                            $mult = ($unitprice[0]['price']*$prod['quantity']);
                            $newcur_prod['total_price'] = $mult;
                        }
                        $data[] = $newcur_prod;
                    }
                //}
            }
            return $data;
        }
    }

    public function getsalesnotaxprods($currency) {
        $this->db->where('projectid', $_POST['project']);
        $this->db->where('currency', $currency);
        $this->db->where('rel_type', $_POST['item_type']);
        $products = $this->db->get(db_prefix() . 'itemable')->result_array();
        if($products) {
            return $products;
        } else {
            $data = array();
            $this->db->where('projectid', $_POST['project']);
            $this->db->where('rel_type', $_POST['item_type']);
            $products = $this->db->get(db_prefix() . 'itemable')->result_array();
            foreach($products as $prod) {
                $this->db->where('item_id', $prod['productid']);
                $this->db->where('currency', $currency);
                $unitprice = $this->db->get(db_prefix() . 'item_price')->result_array();
                if($unitprice) {
                    $newcur_prod = array();
                    $newcur_prod['id'] = $prod['id'];
                    $newcur_prod['projectid'] = $prod['projectid'];
                    $newcur_prod['productid'] = $prod['productid'];
                    $newcur_prod['currency'] = $currency;
                    $newcur_prod['variation'] = $prod['variation'];
                    $newcur_prod['quantity'] = $prod['quantity'];
                    $newcur_prod['tax'] = $prod['tax'];
                    $newcur_prod['discount'] = $prod['discount'];
                    $newcur_prod['method'] = $prod['method'];
                    $newcur_prod['status'] = $prod['status'];
                    $newcur_prod['created_date'] = $prod['created_date'];
                    $newcur_prod['price'] = $unitprice[0]['price'];
                    if($prod['discount'] && $prod['discount'] > 0) {
                        $dec = ($prod['discount'] / 100); //its convert 10 into 0.10
                        $multqty = ($unitprice[0]['price']*$prod['quantity']);
                        $mult = $multqty*$dec; // gives the value for subtract from main value
                        $newcur_prod['total_price'] = $multqty - $mult;
                    } else {
                        $mult = ($unitprice[0]['price']*$prod['quantity']);
                        $newcur_prod['total_price'] = $mult;
                    }
                    $data[] = $newcur_prod;
                }
            }
            return $data;
        }
    }


    public function getsalesextaxprods($currency) {
        
         $this->db->select(db_prefix().'itemable.*,'.db_prefix() . 'items.tax as prodtax');
         $this->db->where('projectid', $_POST['project']);
         $this->db->where('currency', $currency);
         $this->db->where('rel_type', $_POST['item_type']);
         $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id=' . db_prefix() . 'itemable.productid');
         $products = $this->db->get(db_prefix() . 'itemable')->result_array();
         if($products) {
             return $products;
         } else {
             $data = array();
             $this->db->where('projectid', $_POST['project']);
             $this->db->where('rel_type', $_POST['item_type']);
             $products = $this->db->get(db_prefix() . 'itemable')->result_array();
             foreach($products as $prod) {
                     $this->db->where('item_id', $prod['productid']);
                     $this->db->where('currency', $currency);
                     $unitprice = $this->db->get(db_prefix() . 'item_price')->result_array();
                    // pre($prod);
                     if($unitprice) {
                         $this->db->where('id', $prod['productid']);
                         $prod_tax = $this->db->get(db_prefix() . 'items')->result_array();
                         $newcur_prod = array();
                         $newcur_prod['id'] = $prod['id'];
                         $newcur_prod['projectid'] = $prod['projectid'];
                         $newcur_prod['productid'] = $prod['productid'];
                         $newcur_prod['currency'] = $currency;
                         $newcur_prod['variation'] = $prod['variation'];
                         $newcur_prod['quantity'] = $prod['quantity'];
                        // $newcur_prod['prodtax'] = $prod_tax[0]['tax'];
                         $newcur_prod['prodtax'] = $unitprice[0]['tax'];
                         $newcur_prod['discount'] = $prod['discount'];
                         $newcur_prod['method'] = $prod['method'];
                         $newcur_prod['status'] = $prod['status'];
                         $newcur_prod['created_date'] = $prod['created_date'];
                         $newcur_prod['price'] = $unitprice[0]['price'];
                         if($prod['discount'] && $prod['discount'] > 0) {
                             $dec = ($prod['discount'] / 100); //its convert 10 into 0.10
                             $multqty = ($unitprice[0]['price']*$prod['quantity']);
                             $mult = $multqty*$dec; // gives the value for subtract from main value
                             $newcur_prod['total_price'] = $multqty - $mult;
                         } else {
                             $mult = ($unitprice[0]['price']*$prod['quantity']);
                             $newcur_prod['total_price'] = $mult;
                         }
                         $data[] = $newcur_prod;
                     }
             }
             return $data;
         }
     }

    /**
     * Get customers contacts
     * @param  mixed $customer_id
     * @param  array  $where       perform where in query
     * @return array
     */
    public function get_clients_list($customer_id = '', $where = ['active' => 1]) {
        $this->db->where($where);
        if ($customer_id != '') {
            $this->db->where('userid', $customer_id);
        }
        $g_clients_list = $this->db->get(db_prefix() . 'clients')->result_array();
        $gc = array();
        foreach ($g_clients_list as $key => $value) {
            $gc[$value['userid']] = $value['company'];
        }
        return $gc;
    }

    /**
     * Get customers contacts
     * @param  mixed $customer_id
     * @param  array  $where       perform where in query
     * @return array
     */
    public function get_contacts($customer_id = '', $where = ['active' => 1]) {
        $this->db->where($where);
        if ($customer_id != '') {
            $d = "( userid='$customer_id')";
            $this->db->where($d);
        }
        $this->db->where('deleted_status',0);
        return $this->db->get(db_prefix() . 'contacts')->result_array();
    }

    /**
     * Get single contacts
     * @param  mixed $id contact id
     * @return object
     */
    public function get_contact($id) {
        return $this->db->query('SELECT a.*,b.company  FROM ' . db_prefix() . 'contacts as a JOIN ' . db_prefix() . 'clients as b ON b.userid=a.userid  where id = '.$id)->row();
    }

    public function get_dealContactsbyClientId($id) {
        $this->db->select('project_id');
        $this->db->where('contacts_id', $id);

        return $this->db->get(db_prefix() . 'project_contacts')->result_array();
    }

    public function get_dealsbyClientId($id) {
        $this->db->select('id, name');
        $this->db->where('clientid', $id);

        return $this->db->get(db_prefix() . 'projects')->result_array();
    }

    /**
     * @param array $_POST data
     * @param client_request is this request from the customer area
     * @return integer Insert ID
     * Add new client to database
     */

    public function addCategory($data) {
        $contact_data = [];
        if (isset($data['category'])) {
            $contact_data['cat_name'] = $data['category'];
            unset($data['category']);
        }

        $this->db->insert(db_prefix() . 'product_category', $contact_data);
        $userid = $this->db->insert_id();
        return $userid;
    }
    public function addUnitPrice($data) {
        $this->db->where('productid', $data['productid']);
        $this->db->where('currency', $data['currency']);
        $result = $this->db->get(db_prefix() . 'unit_price')->result_array();
        if(empty($result)) {
            $this->db->insert(db_prefix() . 'unit_price', $data);
            $userid = $this->db->insert_id();
            return $userid;
        }
    }

    public function editUnitPrice($data) {
        $this->db->select('*');
        $this->db->where('productid', $data['productid']);
        $this->db->where('currency', $data['currency']);
        $result = $this->db->get(db_prefix() . 'unit_price')->result_array();
        //pre($result);
        if($result) {
            $this->db->where('id', $result[0]['id']);
            $this->db->update(db_prefix() . 'unit_price', $data);
        } else {
            $this->db->insert(db_prefix() . 'unit_price', $data);
            $userid = $this->db->insert_id();
        }
        return true;
    }

    public function editUnitVariation($data) {
        //pre($data);
        $this->db->select('*');
        $this->db->where('productid', $data['productid']);
        $this->db->where('currency', $data['currency']);
        $this->db->where('variationid', $data['variationid']);
        $result = $this->db->get(db_prefix() . 'variation_price')->result_array();
        //pre($result);
        if($result) {
            $this->db->where('id', $result[0]['id']);
            $this->db->update(db_prefix() . 'variation_price', $data);
        } else {
            $this->db->insert(db_prefix() . 'variation_price', $data);
            //echo $this->db->last_query();
            $userid = $this->db->insert_id();
        }
        return true;
    }

    public function add($data) {
        $this->db->insert(db_prefix() . 'products', $data);
        $userid = $this->db->insert_id();
        return $userid;
    }
    public function getCategory() {
        $this->db->select('*');
        return $this->db->get(db_prefix() . 'product_category')->result_array();
    }

    public function getUnitprice($id) {
        $this->db->select('*');
        $this->db->where('productid', $id);
        return $this->db->get(db_prefix() . 'unit_price')->result_array();
    }

    public function getVariations($id) {
        $this->db->select('*');
        $this->db->where('productid', $id);
        return $this->db->get(db_prefix() . 'variations')->result_array();
    }

    public function getUnitVariation($id) {
        $this->db->select('*');
        $this->db->where('productid', $id);
        return $this->db->get(db_prefix() . 'variation_price')->result_array();
    }

    public function getVariationfield($currency) {
        return $this->db->query('SELECT a.id,a.productid,a.name FROM ' . db_prefix() . 'variations as a, ' . db_prefix() . 'variation_price as b where a.productid = b.productid and b.variationid = a.id and b.currency = "'.$currency.'" and a.productid = "'.$_POST['value'].'"')->result_array();
    }

    public function getVariationfieldbyid($currency, $id) {
       
        return $this->db->query('SELECT a.id,a.productid,a.name FROM ' . db_prefix() . 'variations as a, ' . db_prefix() . 'variation_price as b where a.productid = b.productid and b.variationid = a.id and b.currency = "'.$currency.'" and a.productid = "'.$id.'"')->result_array();
    }

    public function getvariationpricebyid($currency) {
        $this->db->select('*');
        $this->db->where('currency', $currency);
        $this->db->where('variationid', $_POST['value']);
        return $this->db->get(db_prefix() . 'variation_price')->result_array();
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update client informations
     */
    public function update($data, $id) {

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'products', $data);

        return true;
    }

    /**
     * Update contact data
     * @param  array  $data           $_POST data
     * @param  mixed  $id             contact id
     * @param  boolean $client_request is request from customers area
     * @return mixed
     */
    public function update_contact($data, $id, $client_request = false) {
        $affectedRows = 0;
        $contact = $this->get_contact($id);
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = app_hash_password($data['password']);
            $data['last_password_change'] = date('Y-m-d H:i:s');
        }
        
        if (is_array($data['alternative_emails'])) {
            $data['alternative_emails'] = implode(',',array_filter($data['alternative_emails']));
        }
        if (is_array($data['alternative_phonenumber'])) {
            $data['alternative_phonenumber'] = implode(',',array_filter($data['alternative_phonenumber']));
        }

        $send_set_password_email = isset($data['send_set_password_email']) ? true : false;
        $send_set_password_email = true;
        $set_password_email_sent = false;

        $permissions = isset($data['permissions']) ? $data['permissions'] : [];
        $data['is_primary'] = isset($data['is_primary']) ? 1 : 0;

        // if($data['is_primary']){
        //     // $contacts_clients['is_primary'] = 0;
        //     $this->db->where('userid', $contact->userid);
        //     $this->db->update(db_prefix() . 'contacts_clients', ['is_primary' => 0,]);
        // }else{
        //     // $contacts_clients['is_primary'] = 0;
        // }
        
        // Contact cant change if is primary or not
        if ($client_request == true) {
            unset($data['is_primary']);
            
        }
        
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        if ($client_request == false) {
            $data['invoice_emails'] = isset($data['invoice_emails']) ? 1 : 0;
            $data['estimate_emails'] = isset($data['estimate_emails']) ? 1 : 0;
            $data['credit_note_emails'] = isset($data['credit_note_emails']) ? 1 : 0;
            $data['contract_emails'] = isset($data['contract_emails']) ? 1 : 0;
            $data['task_emails'] = isset($data['task_emails']) ? 1 : 0;
            $data['project_emails'] = isset($data['project_emails']) ? 1 : 0;
            $data['ticket_emails'] = isset($data['ticket_emails']) ? 1 : 0;
        }

        $data = hooks()->apply_filters('before_update_contact', $data, $id);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contacts', $data);

        // pr($data);

        // if (isset($data['userids']) && !empty($data['userids'])) {
        //     $contacts_clients['contactid'] = $id;
        //     $this->db->delete(db_prefix() . 'contacts_clients', ['contactid' => $id,]);
        //     foreach (explode(',', $data['userids']) as $keycu => $valuecu) {
        //         $contacts_clients['userid'] = $valuecu;
        //         $this->db->insert(db_prefix() . 'contacts_clients', $contacts_clients);
        //     }
        //     if ($client_request == true) {
        //         $this->db->where('contactid', $id);
        //         $this->db->where_in('userid', explode(',', $data['userids']));
        //         $this->db->update(db_prefix() . 'contacts_clients', ['is_primary' => 1,]);
        //     }
        // }

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            if (isset($data['is_primary']) && $data['is_primary'] == 1) {
                $this->db->where('userid', $contact->userid);
                $this->db->where('id !=', $id);
                $this->db->update(db_prefix() . 'contacts', [
                    'is_primary' => 0,
                ]);
            }
        }

        if ($client_request == false) {
            $customer_permissions = $this->roles_model->get_contact_permissions($id);
            if (sizeof($customer_permissions) > 0) {
                foreach ($customer_permissions as $customer_permission) {
                    if (!in_array($customer_permission['permission_id'], $permissions)) {
                        $this->db->where('userid', $id);
                        $this->db->where('permission_id', $customer_permission['permission_id']);
                        $this->db->delete(db_prefix() . 'contact_permissions');
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                }
                foreach ($permissions as $permission) {
                    $this->db->where('userid', $id);
                    $this->db->where('permission_id', $permission);
                    $_exists = $this->db->get(db_prefix() . 'contact_permissions')->row();
                    if (!$_exists) {
                        $this->db->insert(db_prefix() . 'contact_permissions', [
                            'userid' => $id,
                            'permission_id' => $permission,
                        ]);
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                }
            } else {
                foreach ($permissions as $permission) {
                    $this->db->insert(db_prefix() . 'contact_permissions', [
                        'userid' => $id,
                        'permission_id' => $permission,
                    ]);
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
            if ($send_set_password_email) {
                $set_password_email_sent = $this->authentication_model->set_password_email($data['email'], 0);
            }
        }
        if ($affectedRows > 0 && !$set_password_email_sent) {
            log_activity('Contact Updated [ID: ' . $id . ']');

            return true;
        } elseif ($affectedRows > 0 && $set_password_email_sent) {
            return [
                'set_password_email_sent_and_profile_updated' => true,
            ];
        } elseif ($affectedRows == 0 && $set_password_email_sent) {
            return [
                'set_password_email_sent' => true,
            ];
        }

        return false;
    }

    /**
     * Add new contact
     * @param array  $data               $_POST data
     * @param mixed  $customer_id        customer id
     * @param boolean $not_manual_request is manual from admin area customer profile or register, convert to lead
     */
    public function add_contact($data, $customer_id, $not_manual_request = false) {
        $send_set_password_email = isset($data['send_set_password_email']) ? true : false;
        $projectid = '';
        if(isset($data['project_id'])){
            $projectid = $data['project_id'];
            unset($data['project_id']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        if (isset($data['permissions'])) {
            $permissions = $data['permissions'];
            unset($data['permissions']);
        }
        if (is_array($data['alternative_emails'])) {
            $data['alternative_emails'] = implode(',',array_filter($data['alternative_emails']));
        }
        if (is_array($data['alternative_phonenumber'])) {
            $data['alternative_phonenumber'] = implode(',',array_filter($data['alternative_phonenumber']));
        }
        $data['email_verified_at'] = date('Y-m-d H:i:s');

        $send_welcome_email = true;

        if (isset($data['donotsendwelcomeemail'])) {
            $send_welcome_email = false;
        }

        if (defined('CONTACT_REGISTERING')) {
            $send_welcome_email = true;

            // Do not send welcome email if confirmation for registration is enabled
            if (get_option('customers_register_require_confirmation') == '1') {
                $send_welcome_email = false;
            }

            // If client register set this contact as primary
            $data['is_primary'] = 1;
            if (is_email_verification_enabled() && !empty($data['email'])) {
                // Verification is required on register
                $data['email_verified_at'] = null;
                $data['email_verification_key'] = app_generate_hash();
            }
        }

        if (isset($data['is_primary'])) {
            $data['is_primary'] = 1;
            $this->db->where('userid', $customer_id);
            $this->db->update(db_prefix() . 'contacts', [
                'is_primary' => 0,
            ]);
            // // $contacts_clients['is_primary'] = 1;
            // $this->db->where('userid', $customer_id);
            // $this->db->update(db_prefix() . 'contacts_clients', ['is_primary' => 0,]);
        } else {
            $data['is_primary'] = 0;
        }

        $password_before_hash = '';
        
        if (isset($data['password'])) {
            $password_before_hash = $data['password'];
            $data['password'] = app_hash_password($data['password']);
        }

        $data['datecreated'] = date('Y-m-d H:i:s');

        if (!$not_manual_request) {
            $data['invoice_emails'] = isset($data['invoice_emails']) ? 1 : 0;
            $data['estimate_emails'] = isset($data['estimate_emails']) ? 1 : 0;
            $data['credit_note_emails'] = isset($data['credit_note_emails']) ? 1 : 0;
            $data['contract_emails'] = isset($data['contract_emails']) ? 1 : 0;
            $data['task_emails'] = isset($data['task_emails']) ? 1 : 0;
            $data['project_emails'] = isset($data['project_emails']) ? 1 : 0;
            $data['ticket_emails'] = isset($data['ticket_emails']) ? 1 : 0;
        }

        $data['email'] = trim($data['email']);
        unset($data['ci_csrf_token']);
        $data = hooks()->apply_filters('before_create_contact', $data);
        
        $this->db->insert(db_prefix() . 'contacts', $data);
        $contact_id = $this->db->insert_id();
        if (isset($data['userids']) && !empty($data['userids'])) {
            $contacts_clients['contactid'] = $contact_id;
            foreach (explode(',', $data['userids']) as $keycu => $valuecu) {
                $contacts_clients['userid'] = $valuecu;
                $this->db->insert(db_prefix() . 'contacts_clients', $contacts_clients);
            }
        }
        if (isset($projectid) && !empty($projectid)) {
            $proj_contatcts['contacts_id'] = $contact_id;
            $proj_contatcts['project_id'] = $projectid;
            $this->db->insert(db_prefix() . 'project_contacts', $proj_contatcts);
        }

        if ($contact_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($contact_id, $custom_fields);
            }
            // request from admin area
            if (!isset($permissions) && $not_manual_request == false) {
                $permissions = [];
            } elseif ($not_manual_request == true) {
                $permissions = [];
                $_permissions = get_contact_permissions();
                $default_permissions = @unserialize(get_option('default_contact_permissions'));
                if (is_array($default_permissions)) {
                    foreach ($_permissions as $permission) {
                        if (in_array($permission['id'], $default_permissions)) {
                            array_push($permissions, $permission['id']);
                        }
                    }
                }
            }

            if ($not_manual_request == true) {
                // update all email notifications to 0
                $this->db->where('id', $contact_id);
                $this->db->update(db_prefix() . 'contacts', [
                    'invoice_emails' => 0,
                    'estimate_emails' => 0,
                    'credit_note_emails' => 0,
                    'contract_emails' => 0,
                    'task_emails' => 0,
                    'project_emails' => 0,
                    'ticket_emails' => 0,
                ]);
            }
            foreach ($permissions as $permission) {
                $this->db->insert(db_prefix() . 'contact_permissions', [
                    'userid' => $contact_id,
                    'permission_id' => $permission,
                ]);

                // Auto set email notifications based on permissions
                if ($not_manual_request == true) {
                    if ($permission == 6) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contacts', ['project_emails' => 1, 'task_emails' => 1]);
                    } elseif ($permission == 3) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contacts', ['contract_emails' => 1]);
                    } elseif ($permission == 2) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contacts', ['estimate_emails' => 1]);
                    } elseif ($permission == 1) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contacts', ['invoice_emails' => 1, 'credit_note_emails' => 1]);
                    } elseif ($permission == 5) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contacts', ['ticket_emails' => 1]);
                    }
                }
            }

            // if ($send_welcome_email == true) {
            //     send_mail_template('customer_created_welcome_mail', $data['email'], $data['userid'], $contact_id, $password_before_hash);
            // }

            // if ($send_set_password_email) {
            //     $this->authentication_model->set_password_email($data['email'], 0);
            // }

            // if (defined('CONTACT_REGISTERING')) {
            //     $this->send_verification_email($contact_id);
            // } else {
            //     // User already verified because is added from admin area, try to transfer any tickets
            //     $this->load->model('tickets_model');
            //     $this->tickets_model->transfer_email_tickets_to_contact($data['email'], $contact_id);
            // }

            log_activity('Contact Created [ID: ' . $contact_id . ']');

            hooks()->do_action('contact_created', $contact_id);

            return $contact_id;
        }

        return false;
    }

    /**
     * Used to update company details from customers area
     * @param  array $data $_POST data
     * @param  mixed $id
     * @return boolean
     */
    public function update_company_details($data, $id) {
        $affectedRows = 0;
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }
        if (isset($data['country']) && $data['country'] == '' || !isset($data['country'])) {
            $data['country'] = 0;
        }
        if (isset($data['billing_country']) && $data['billing_country'] == '') {
            $data['billing_country'] = 0;
        }
        if (isset($data['shipping_country']) && $data['shipping_country'] == '') {
            $data['shipping_country'] = 0;
        }

        // From v.1.9.4 these fields are textareas
        $data['address'] = trim($data['address']);
        $data['address'] = nl2br($data['address']);
        if (isset($data['billing_street'])) {
            $data['billing_street'] = trim($data['billing_street']);
            $data['billing_street'] = nl2br($data['billing_street']);
        }
        if (isset($data['shipping_street'])) {
            $data['shipping_street'] = trim($data['shipping_street']);
            $data['shipping_street'] = nl2br($data['shipping_street']);
        }

        $data = hooks()->apply_filters('customer_update_company_info', $data, $id);

        $this->db->where('userid', $id);
        $this->db->update(db_prefix() . 'clients', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        if ($affectedRows > 0) {
            hooks()->do_action('customer_updated_company_info', $id);
            log_activity('Customer Info Updated From Clients Area [ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Get customer staff members that are added as customer admins
     * @param  mixed $id customer id
     * @return array
     */
    public function get_admins($id) {
        $this->db->where('customer_id', $id);

        return $this->db->get(db_prefix() . 'customer_admins')->result_array();
    }

    /**
     * Get unique staff id's of customer admins
     * @return array
     */
    public function get_customers_admin_unique_ids() {
        return $this->db->query('SELECT DISTINCT(staff_id) FROM ' . db_prefix() . 'customer_admins')->result_array();
    }

    /**
     * Assign staff members as admin to customers
     * @param  array $data $_POST data
     * @param  mixed $id   customer id
     * @return boolean
     */
    public function assign_admins($data, $id) {
        $affectedRows = 0;

        if (count($data) == 0) {
            $this->db->where('customer_id', $id);
            $this->db->delete(db_prefix() . 'customer_admins');
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }
        } else {
            $current_admins = $this->get_admins($id);
            $current_admins_ids = [];
            foreach ($current_admins as $c_admin) {
                array_push($current_admins_ids, $c_admin['staff_id']);
            }
            foreach ($current_admins_ids as $c_admin_id) {
                if (!in_array($c_admin_id, $data['customer_admins'])) {
                    $this->db->where('staff_id', $c_admin_id);
                    $this->db->where('customer_id', $id);
                    $this->db->delete(db_prefix() . 'customer_admins');
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
            foreach ($data['customer_admins'] as $n_admin_id) {
                if (total_rows(db_prefix() . 'customer_admins', [
                            'customer_id' => $id,
                            'staff_id' => $n_admin_id,
                        ]) == 0) {
                    $this->db->insert(db_prefix() . 'customer_admins', [
                        'customer_id' => $id,
                        'staff_id' => $n_admin_id,
                        'date_assigned' => date('Y-m-d H:i:s'),
                    ]);
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
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
     * @param  integer ID
     * @return boolean
     * Delete client, also deleting rows from, dismissed client announcements, ticket replies, tickets, autologin, user notes
     */
    public function delete($id) {
        $affectedRows = 0;

        if (!is_gdpr() && is_reference_in_table('clientid', db_prefix() . 'invoices', $id)) {
            return [
                'referenced' => true,
            ];
        }

        if (!is_gdpr() && is_reference_in_table('clientid', db_prefix() . 'estimates', $id)) {
            return [
                'referenced' => true,
            ];
        }

        if (!is_gdpr() && is_reference_in_table('clientid', db_prefix() . 'creditnotes', $id)) {
            return [
                'referenced' => true,
            ];
        }

        hooks()->do_action('before_client_deleted', $id);

        $last_activity = get_last_system_activity_id();
        $company = get_company_name($id);

//Update Deleted Status
        $update = array();
        $update['deleted_status'] = 1;
        $this->db->where('userid', $id);
        $this->db->update(db_prefix() . 'clients',$update);
        if ($this->db->affected_rows() > 0) {
            log_activity('Client Deleted [ID: ' . $id . ']');
            return true;
        } else {
            return false;
        }

        /*
        $this->db->where('userid', $id);
        $this->db->delete(db_prefix() . 'clients');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            // Delete all user contacts
            $this->db->where('userid', $id);
            $contacts = $this->db->get(db_prefix() . 'contacts')->result_array();
            foreach ($contacts as $contact) {
                $this->delete_contact($contact['id']);
            }

            // Delete all tickets start here
            $this->db->where('userid', $id);
            $tickets = $this->db->get(db_prefix() . 'tickets')->result_array();
            $this->load->model('tickets_model');
            foreach ($tickets as $ticket) {
                $this->tickets_model->delete($ticket['ticketid']);
            }

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'customer');
            $this->db->delete(db_prefix() . 'notes');

            if (is_gdpr() && get_option('gdpr_on_forgotten_remove_invoices_credit_notes') == '1') {
                $this->load->model('invoices_model');
                $this->db->where('clientid', $id);
                $invoices = $this->db->get(db_prefix() . 'invoices')->result_array();
                foreach ($invoices as $invoice) {
                    $this->invoices_model->delete($invoice['id'], true);
                }

                $this->load->model('credit_notes_model');
                $this->db->where('clientid', $id);
                $credit_notes = $this->db->get(db_prefix() . 'creditnotes')->result_array();
                foreach ($credit_notes as $credit_note) {
                    $this->credit_notes_model->delete($credit_note['id'], true);
                }
            } elseif (is_gdpr()) {
                $this->db->where('clientid', $id);
                $this->db->update(db_prefix() . 'invoices', ['deleted_customer_name' => $company]);

                $this->db->where('clientid', $id);
                $this->db->update(db_prefix() . 'creditnotes', ['deleted_customer_name' => $company]);
            }

            $this->db->where('clientid', $id);
            $this->db->update(db_prefix() . 'creditnotes', [
                'clientid' => 0,
                'project_id' => 0,
            ]);

            $this->db->where('clientid', $id);
            $this->db->update(db_prefix() . 'invoices', [
                'clientid' => 0,
                'recurring' => 0,
                'recurring_type' => null,
                'custom_recurring' => 0,
                'cycles' => 0,
                'last_recurring_date' => null,
                'project_id' => 0,
                'subscription_id' => 0,
                'cancel_overdue_reminders' => 1,
                'last_overdue_reminder' => null,
            ]);

            if (is_gdpr() && get_option('gdpr_on_forgotten_remove_estimates') == '1') {
                $this->load->model('estimates_model');
                $this->db->where('clientid', $id);
                $estimates = $this->db->get(db_prefix() . 'estimates')->result_array();
                foreach ($estimates as $estimate) {
                    $this->estimates_model->delete($estimate['id'], true);
                }
            } elseif (is_gdpr()) {
                $this->db->where('clientid', $id);
                $this->db->update(db_prefix() . 'estimates', ['deleted_customer_name' => $company]);
            }

            $this->db->where('clientid', $id);
            $this->db->update(db_prefix() . 'estimates', [
                'clientid' => 0,
                'project_id' => 0,
                'is_expiry_notified' => 1,
            ]);

            $this->load->model('subscriptions_model');
            $this->db->where('clientid', $id);
            $subscriptions = $this->db->get(db_prefix() . 'subscriptions')->result_array();
            foreach ($subscriptions as $subscription) {
                $this->subscriptions_model->delete($subscription['id'], true);
            }
            // Get all client contracts
            $this->load->model('contracts_model');
            $this->db->where('client', $id);
            $contracts = $this->db->get(db_prefix() . 'contracts')->result_array();
            foreach ($contracts as $contract) {
                $this->contracts_model->delete($contract['id']);
            }
            // Delete the custom field values
            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'customers');
            $this->db->delete(db_prefix() . 'customfieldsvalues');

            // Get customer related tasks
            $this->db->where('rel_type', 'customer');
            $this->db->where('rel_id', $id);
            $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();

            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id'], false);
            }

            $this->db->where('rel_type', 'customer');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'reminders');

            $this->db->where('customer_id', $id);
            $this->db->delete(db_prefix() . 'customer_admins');

            $this->db->where('customer_id', $id);
            $this->db->delete(db_prefix() . 'vault');

            $this->db->where('customer_id', $id);
            $this->db->delete(db_prefix() . 'customer_groups');

            $this->load->model('proposals_model');
            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'customer');
            $proposals = $this->db->get(db_prefix() . 'proposals')->result_array();
            foreach ($proposals as $proposal) {
                $this->proposals_model->delete($proposal['id']);
            }
            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'customer');
            $attachments = $this->db->get(db_prefix() . 'files')->result_array();
            foreach ($attachments as $attachment) {
                $this->delete_attachment($attachment['id']);
            }

            $this->db->where('clientid', $id);
            $expenses = $this->db->get(db_prefix() . 'expenses')->result_array();

            $this->load->model('expenses_model');
            foreach ($expenses as $expense) {
                $this->expenses_model->delete($expense['id'], true);
            }

            $this->db->where('client_id', $id);
            $this->db->delete(db_prefix() . 'user_meta');

            $this->db->where('client_id', $id);
            $this->db->update(db_prefix() . 'leads', ['client_id' => 0]);

            // Delete all projects
            $this->load->model('projects_model');
            $this->db->where('clientid', $id);
            $projects = $this->db->get(db_prefix() . 'projects')->result_array();
            foreach ($projects as $project) {
                $this->projects_model->delete($project['id']);
            }
        }
        if ($affectedRows > 0) {
            hooks()->do_action('after_client_deleted', $id);

            // Delete activity log caused by delete customer function
            if ($last_activity) {
                $this->db->where('id >', $last_activity->id);
                $this->db->delete(db_prefix() . 'activity_log');
            }

            log_activity('Client Deleted [ID: ' . $id . ']');

            return true;
        }
        */

        return false;
    }

    /**
     * Delete customer contact
     * @param  mixed $id contact id
     * @return boolean
     */
    public function delete_contact($id) {
        hooks()->do_action('before_delete_contact', $id);

        $this->db->where('id', $id);
        $result = $this->db->get(db_prefix() . 'contacts')->row();
        $customer_id = $result->userid;

        $last_activity = get_last_system_activity_id();

        $data = array();
        $data['deleted_status'] = 1;
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contacts', $data);

        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('contact_deleted', $id, $result);
            return true;
        }

       /*
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'contacts');

        if ($this->db->affected_rows() > 0) {
            if (is_dir(get_upload_path_by_type('contact_profile_images') . $id)) {
                delete_dir(get_upload_path_by_type('contact_profile_images') . $id);
            }

            $this->db->where('contact_id', $id);
            $this->db->delete(db_prefix() . 'consents');

            $this->db->where('contact_id', $id);
            $this->db->delete(db_prefix() . 'shared_customer_files');

            $this->db->where('userid', $id);
            $this->db->where('staff', 0);
            $this->db->delete(db_prefix() . 'dismissed_announcements');

            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'contacts');
            $this->db->delete(db_prefix() . 'customfieldsvalues');

            $this->db->where('userid', $id);
            $this->db->delete(db_prefix() . 'contact_permissions');

            $this->db->where('user_id', $id);
            $this->db->where('staff', 0);
            $this->db->delete(db_prefix() . 'user_auto_login');

            $this->db->select('ticketid');
            $this->db->where('contactid', $id);
            $this->db->where('userid', $customer_id);
            $tickets = $this->db->get(db_prefix() . 'tickets')->result_array();

            $this->load->model('tickets_model');
            foreach ($tickets as $ticket) {
                $this->tickets_model->delete($ticket['ticketid']);
            }

            $this->load->model('tasks_model');

            $this->db->where('addedfrom', $id);
            $this->db->where('is_added_from_contact', 1);
            $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();

            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id'], false);
            }

            // Added from contact in customer profile
            $this->db->where('contact_id', $id);
            $this->db->where('rel_type', 'customer');
            $attachments = $this->db->get(db_prefix() . 'files')->result_array();

            foreach ($attachments as $attachment) {
                $this->delete_attachment($attachment['id']);
            }

            // Remove contact files uploaded to tasks
            $this->db->where('rel_type', 'task');
            $this->db->where('contact_id', $id);
            $filesUploadedFromContactToTasks = $this->db->get(db_prefix() . 'files')->result_array();

            foreach ($filesUploadedFromContactToTasks as $file) {
                $this->tasks_model->remove_task_attachment($file['id']);
            }

            $this->db->where('contact_id', $id);
            $tasksComments = $this->db->get(db_prefix() . 'task_comments')->result_array();
            foreach ($tasksComments as $comment) {
                $this->tasks_model->remove_comment($comment['id'], true);
            }

            $this->load->model('projects_model');

            $this->db->where('contact_id', $id);
            $files = $this->db->get(db_prefix() . 'project_files')->result_array();
            foreach ($files as $file) {
                $this->projects_model->remove_file($file['id'], false);
            }

            $this->db->where('contact_id', $id);
            $discussions = $this->db->get(db_prefix() . 'projectdiscussions')->result_array();
            foreach ($discussions as $discussion) {
                $this->projects_model->delete_discussion($discussion['id'], false);
            }

            $this->db->where('contact_id', $id);
            $discussionsComments = $this->db->get(db_prefix() . 'projectdiscussioncomments')->result_array();
            foreach ($discussionsComments as $comment) {
                $this->projects_model->delete_discussion_comment($comment['id'], false);
            }

            $this->db->where('contact_id', $id);
            $this->db->delete(db_prefix() . 'user_meta');

            $this->db->where('(email="' . $result->email . '" OR bcc LIKE "%' . $result->email . '%" OR cc LIKE "%' . $result->email . '%")');
            $this->db->delete(db_prefix() . 'mail_queue');

            if (is_gdpr()) {
                $this->db->where('email', $result->email);
                $this->db->delete(db_prefix() . 'listemails');

                if (!empty($result->last_ip)) {
                    $this->db->where('ip', $result->last_ip);
                    $this->db->delete(db_prefix() . 'knowedge_base_article_feedback');
                }

                $this->db->where('email', $result->email);
                $this->db->delete(db_prefix() . 'tickets_pipe_log');

                $this->db->where('email', $result->email);
                $this->db->delete(db_prefix() . 'tracked_mails');

                $this->db->where('contact_id', $id);
                $this->db->delete(db_prefix() . 'project_activity');

                $this->db->where('(additional_data LIKE "%' . $result->email . '%" OR full_name LIKE "%' . $result->firstname . ' ' . $result->lastname . '%")');
                $this->db->where('additional_data != "" AND additional_data IS NOT NULL');
                $this->db->delete(db_prefix() . 'sales_activity');

                $contactActivityQuery = false;
                if (!empty($result->email)) {
                    $this->db->or_like('description', $result->email);
                    $contactActivityQuery = true;
                }
                if (!empty($result->firstname)) {
                    $this->db->or_like('description', $result->firstname);
                    $contactActivityQuery = true;
                }
                if (!empty($result->lastname)) {
                    $this->db->or_like('description', $result->lastname);
                    $contactActivityQuery = true;
                }

                if (!empty($result->phonenumber)) {
                    $this->db->or_like('description', $result->phonenumber);
                    $contactActivityQuery = true;
                }

                if (!empty($result->last_ip)) {
                    $this->db->or_like('description', $result->last_ip);
                    $contactActivityQuery = true;
                }

                if ($contactActivityQuery) {
                    $this->db->delete(db_prefix() . 'activity_log');
                }
            }

            // Delete activity log caused by delete contact function
            if ($last_activity) {
                $this->db->where('id >', $last_activity->id);
                $this->db->delete(db_prefix() . 'activity_log');
            }

            hooks()->do_action('contact_deleted', $id, $result);

            return true;
        }
        */

        return false;
    }

    public function restore_contact($id) {
        hooks()->do_action('before_restore_contact', $id);

        $this->db->where('id', $id);
        $result = $this->db->get(db_prefix() . 'contacts')->row();
        $customer_id = $result->userid;

        $last_activity = get_last_system_activity_id();

        $data = array();
        $data['deleted_status'] = 0;
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contacts', $data);

        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('contact_restored', $id, $result);
            return true;
        }
        return false;
    }

    public function restore_client($id) {
        hooks()->do_action('before_restore_client', $id);

        $data = array();
        $data['deleted_status'] = 0;
        $this->db->where('userid', $id);
        $this->db->update(db_prefix() . 'clients', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Get customer default currency
     * @param  mixed $id customer id
     * @return mixed
     */
    public function get_customer_default_currency($id) {
        $this->db->select('default_currency');
        $this->db->where('userid', $id);
        $result = $this->db->get(db_prefix() . 'clients')->row();
        if ($result) {
            return $result->default_currency;
        }

        return false;
    }

    /**
     *  Get customer billing details
     * @param   mixed $id   customer id
     * @return  array
     */
    public function get_customer_billing_and_shipping_details($id) {
        $this->db->select('billing_street,billing_city,billing_state,billing_zip,billing_country,shipping_street,shipping_city,shipping_state,shipping_zip,shipping_country');
        $this->db->from(db_prefix() . 'clients');
        $this->db->where('userid', $id);

        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            $result[0]['billing_street'] = clear_textarea_breaks($result[0]['billing_street']);
            $result[0]['shipping_street'] = clear_textarea_breaks($result[0]['shipping_street']);
        }

        return $result;
    }

    /**
     * Get customer files uploaded in the customer profile
     * @param  mixed $id    customer id
     * @param  array  $where perform where
     * @return array
     */
    public function get_customer_files($id, $where = []) {
        $this->db->where($where);
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'customer');
        $this->db->order_by('dateadded', 'desc');

        return $this->db->get(db_prefix() . 'files')->result_array();
    }

    /**
     * Delete customer attachment uploaded from the customer profile
     * @param  mixed $id attachment id
     * @return boolean
     */
    public function delete_attachment($id) {
        $this->db->where('id', $id);
        $attachment = $this->db->get(db_prefix() . 'product_files')->row();
        $deleted = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                $relPath = get_upload_path_by_type('files') . $attachment->productid . '/';
                $fullPath = $relPath . $attachment->file_name;
                unlink($fullPath);
                $fname = pathinfo($fullPath, PATHINFO_FILENAME);
                $fext = pathinfo($fullPath, PATHINFO_EXTENSION);
                $thumbPath = $relPath . $fname . '_thumb.' . $fext;
                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }
            }

            $this->db->where('id', $id);
            $this->db->delete(db_prefix() . 'product_files');
            
            if (is_dir(get_upload_path_by_type('files') . $attachment->productid)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('files') . $attachment->productid);
                if (count($other_attachments) == 0) {
                    delete_dir(get_upload_path_by_type('files') . $attachment->productid);
                }
            }
        }

        return $deleted;
    }

    /**
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update contact status Active/Inactive
     */
    public function change_contact_status($id, $status) {
        $status = hooks()->apply_filters('change_contact_status', $status, $id);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contacts', [
            'active' => $status,
        ]);
        if ($this->db->affected_rows() > 0) {
            log_activity('Contact Status Changed [ContactID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update client status Active/Inactive
     */
    public function change_client_status($id, $status) {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'products', [
            'status' => $status,
        ]);

        if ($this->db->affected_rows() > 0) {
            
            log_activity('Product Status Changed [ID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');

            return true;
        }

        return false;
    }

    /**
     * Change contact password, used from client area
     * @param  mixed $id          contact id to change password
     * @param  string $oldPassword old password to verify
     * @param  string $newPassword new password
     * @return boolean
     */
    public function change_contact_password($id, $oldPassword, $newPassword) {
        // Get current password
        $this->db->where('id', $id);
        $client = $this->db->get(db_prefix() . 'contacts')->row();

        if (!app_hasher()->CheckPassword($oldPassword, $client->password)) {
            return [
                'old_password_not_match' => true,
            ];
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contacts', [
            'last_password_change' => date('Y-m-d H:i:s'),
            'password' => app_hash_password($newPassword),
        ]);

        if ($this->db->affected_rows() > 0) {
            log_activity('Contact Password Changed [ContactID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Get customer groups where customer belongs
     * @param  mixed $id customer id
     * @return array
     */
    public function get_customer_groups($id) {
        return $this->client_groups_model->get_customer_groups($id);
    }

    /**
     * Get all customer groups
     * @param  string $id
     * @return mixed
     */
    public function get_groups($id = '') {
        return $this->client_groups_model->get_groups($id);
    }

    /**
     * Delete customer groups
     * @param  mixed $id group id
     * @return boolean
     */
    public function delete_group($id) {
        return $this->client_groups_model->delete($id);
    }

    /**
     * Add new customer groups
     * @param array $data $_POST data
     */
    public function add_group($data) {
        return $this->client_groups_model->add($data);
    }

    /**
     * Edit customer group
     * @param  array $data $_POST data
     * @return boolean
     */
    public function edit_group($data) {
        return $this->client_groups_model->edit($data);
    }

    /**
     * Create new vault entry
     * @param  array $data        $_POST data
     * @param  mixed $customer_id customer id
     * @return boolean
     */
    public function vault_entry_create($data, $customer_id) {
        return $this->client_vault_entries_model->create($data, $customer_id);
    }

    /**
     * Update vault entry
     * @param  mixed $id   vault entry id
     * @param  array $data $_POST data
     * @return boolean
     */
    public function vault_entry_update($id, $data) {
        return $this->client_vault_entries_model->update($id, $data);
    }

    /**
     * Delete vault entry
     * @param  mixed $id entry id
     * @return boolean
     */
    public function vault_entry_delete($id) {
        return $this->client_vault_entries_model->delete($id);
    }

    /**
     * Get customer vault entries
     * @param  mixed $customer_id
     * @param  array  $where       additional wher
     * @return array
     */
    public function get_vault_entries($customer_id, $where = []) {
        return $this->client_vault_entries_model->get_by_customer_id($customer_id, $where);
    }

    /**
     * Get single vault entry
     * @param  mixed $id vault entry id
     * @return object
     */
    public function get_vault_entry($id) {
        return $this->client_vault_entries_model->get($id);
    }

    /**
     * Get customer statement formatted
     * @param  mixed $customer_id customer id
     * @param  string $from        date from
     * @param  string $to          date to
     * @return array
     */
    public function get_statement($customer_id, $from, $to) {
        return $this->statement_model->get_statement($customer_id, $from, $to);
    }

    /**
     * Send customer statement to email
     * @param  mixed $customer_id customer id
     * @param  array $send_to     array of contact emails to send
     * @param  string $from        date from
     * @param  string $to          date to
     * @param  string $cc          email CC
     * @return boolean
     */
    public function send_statement_to_email($customer_id, $send_to, $from, $to, $cc = '') {
        return $this->statement_model->send_statement_to_email($customer_id, $send_to, $from, $to, $cc);
    }

    /**
     * When customer register, mark the contact and the customer as inactive and set the registration_confirmed field to 0
     * @param  mixed $client_id  the customer id
     * @return boolean
     */
    public function require_confirmation($client_id) {
        $contact_id = get_primary_contact_user_id($client_id);
        $this->db->where('userid', $client_id);
        $this->db->update(db_prefix() . 'clients', ['active' => 0, 'registration_confirmed' => 0]);

        $this->db->where('id', $contact_id);
        $this->db->update(db_prefix() . 'contacts', ['active' => 0]);

        return true;
    }

    public function confirm_registration($client_id) {
        $contact_id = get_primary_contact_user_id($client_id);
        $this->db->where('userid', $client_id);
        $this->db->update(db_prefix() . 'clients', ['active' => 1, 'registration_confirmed' => 1]);

        $this->db->where('id', $contact_id);
        $this->db->update(db_prefix() . 'contacts', ['active' => 1]);

        $contact = $this->get_contact($contact_id);

        if ($contact) {
            send_mail_template('customer_registration_confirmed', $contact);

            return true;
        }

        return false;
    }

    public function send_verification_email($id) {
        $contact = $this->get_contact($id);

        if (empty($contact->email)) {
            return false;
        }

        $success = send_mail_template('customer_contact_verification', $contact);

        if ($success) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'contacts', ['email_verification_sent_at' => date('Y-m-d H:i:s')]);
        }

        return $success;
    }

    public function mark_email_as_verified($id) {
        $contact = $this->get_contact($id);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contacts', [
            'email_verified_at' => date('Y-m-d H:i:s'),
            'email_verification_key' => null,
            'email_verification_sent_at' => null,
        ]);

        if ($this->db->affected_rows() > 0) {

            // Check for previous tickets opened by this email/contact and link to the contact
            $this->load->model('tickets_model');
            $this->tickets_model->transfer_email_tickets_to_contact($contact->email, $contact->id);

            return true;
        }

        return false;
    }

    public function get_clients_distinct_countries() {
        return $this->db->query('SELECT DISTINCT(country_id), short_name FROM ' . db_prefix() . 'clients JOIN ' . db_prefix() . 'countries ON ' . db_prefix() . 'countries.country_id=' . db_prefix() . 'clients.country')->result_array();
    }

    public function send_notification_customer_profile_file_uploaded_to_responsible_staff($contact_id, $customer_id) {
        $staff = $this->get_staff_members_that_can_access_customer($customer_id);
        $merge_fields = $this->app_merge_fields->format_feature('client_merge_fields', $customer_id, $contact_id);
        $notifiedUsers = [];


        foreach ($staff as $member) {
            mail_template('customer_profile_uploaded_file_to_staff', $member['email'], $member['staffid'])
                    ->set_merge_fields($merge_fields)
                    ->send();

            if (add_notification([
                        'touserid' => $member['staffid'],
                        'description' => 'not_customer_uploaded_file',
                        'link' => 'clients/client/' . $customer_id . '?group=attachments',
                    ])) {
                array_push($notifiedUsers, $member['staffid']);
            }
        }
        pusher_trigger_notification($notifiedUsers);
    }

    public function get_staff_members_that_can_access_customer($id) {
		$staff_fields = "staffid,email,firstname,lastname,facebook,linkedin,phonenumber,skype,password,datecreated,profile_image,last_ip,last_login,last_activity,last_password_change,new_pass_key,new_pass_key_requested,admin,role,designation,reporting_to,emp_id,action_for,active,default_language,direction,media_path_slug,is_not_staff,hourly_rate,two_factor_auth_enabled,two_factor_auth_code,two_factor_auth_code_requested,email_signature,deavite_re_assign,deavite_follow,deavite_follow_ids,login_fails,login_locked_on";

        return $this->db->query('SELECT '.$staff_fields.' FROM ' . db_prefix() . 'staff
            WHERE (
                    admin=1
                    OR staffid IN (SELECT staff_id FROM ' . db_prefix() . "customer_admins WHERE customer_id='.$id.')
                    OR staffid IN(SELECT staff_id FROM " . db_prefix() . 'staff_permissions WHERE feature = "customers" AND capability="view")
                )
            AND active=1')->result_array();
    }

    private function check_zero_columns($data) {
        if (!isset($data['show_primary_contact'])) {
            $data['show_primary_contact'] = 0;
        }

        if (isset($data['default_currency']) && $data['default_currency'] == '' || !isset($data['default_currency'])) {
            $data['default_currency'] = 0;
        }

        if (isset($data['country']) && $data['country'] == '' || !isset($data['country'])) {
            $data['country'] = 0;
        }

        if (isset($data['billing_country']) && $data['billing_country'] == '' || !isset($data['billing_country'])) {
            $data['billing_country'] = 0;
        }

        if (isset($data['shipping_country']) && $data['shipping_country'] == '' || !isset($data['shipping_country'])) {
            $data['shipping_country'] = 0;
        }

        return $data;
    }

    public function add_contact_fromapi($data, $customer_id, $not_manual_request = false) {
        $send_set_password_email = isset($data['send_set_password_email']) ? true : false;
        if(isset($data['project_id'])){
            unset($data['project_id']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        if (isset($data['permissions'])) {
            $permissions = $data['permissions'];
            unset($data['permissions']);
        }
        if (is_array($data['alternative_emails'])) {
            $data['alternative_emails'] = implode(',',array_filter($data['alternative_emails']));
        }
        if (is_array($data['alternative_phonenumber'])) {
            $data['alternative_phonenumber'] = implode(',',array_filter($data['alternative_phonenumber']));
        }
        $data['email_verified_at'] = date('Y-m-d H:i:s');

        $send_welcome_email = true;

        if (isset($data['donotsendwelcomeemail'])) {
            $send_welcome_email = false;
        }

        if (defined('CONTACT_REGISTERING')) {
            $send_welcome_email = true;

            // Do not send welcome email if confirmation for registration is enabled
            if (get_option('customers_register_require_confirmation') == '1') {
                $send_welcome_email = false;
            }

            // If client register set this contact as primary
            $data['is_primary'] = 1;
            if (is_email_verification_enabled() && !empty($data['email'])) {
                // Verification is required on register
                $data['email_verified_at'] = null;
                $data['email_verification_key'] = app_generate_hash();
            }
        }

        if (isset($data['is_primary'])) {
            $data['is_primary'] = 1;
            $this->db->where('userid', $customer_id);
            $this->db->update(db_prefix() . 'contacts', [
                'is_primary' => 0,
            ]);
        } else {
            $data['is_primary'] = 0;
        }

        $password_before_hash = '';
        
        if (isset($data['password'])) {
            $password_before_hash = $data['password'];
            $data['password'] = app_hash_password($data['password']);
        }

        $data['datecreated'] = date('Y-m-d H:i:s');

        if (!$not_manual_request) {
            $data['invoice_emails'] = isset($data['invoice_emails']) ? 1 : 0;
            $data['estimate_emails'] = isset($data['estimate_emails']) ? 1 : 0;
            $data['credit_note_emails'] = isset($data['credit_note_emails']) ? 1 : 0;
            $data['contract_emails'] = isset($data['contract_emails']) ? 1 : 0;
            $data['task_emails'] = isset($data['task_emails']) ? 1 : 0;
            $data['project_emails'] = isset($data['project_emails']) ? 1 : 0;
            $data['ticket_emails'] = isset($data['ticket_emails']) ? 1 : 0;
        }

        $data['email'] = trim($data['email']);

        $data = hooks()->apply_filters('before_create_contact', $data);

        $this->db->insert(db_prefix() . 'contacts', $data);
        $contact_id = $this->db->insert_id();

        if ($contact_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($contact_id, $custom_fields);
            }
            // request from admin area
            if (!isset($permissions) && $not_manual_request == false) {
                $permissions = [];
            } elseif ($not_manual_request == true) {
                $permissions = [];
                $_permissions = get_contact_permissions();
                $default_permissions = @unserialize(get_option('default_contact_permissions'));
                if (is_array($default_permissions)) {
                    foreach ($_permissions as $permission) {
                        if (in_array($permission['id'], $default_permissions)) {
                            array_push($permissions, $permission['id']);
                        }
                    }
                }
            }

            if ($not_manual_request == true) {
                // update all email notifications to 0
                $this->db->where('id', $contact_id);
                $this->db->update(db_prefix() . 'contacts', [
                    'invoice_emails' => 0,
                    'estimate_emails' => 0,
                    'credit_note_emails' => 0,
                    'contract_emails' => 0,
                    'task_emails' => 0,
                    'project_emails' => 0,
                    'ticket_emails' => 0,
                ]);
            }
            foreach ($permissions as $permission) {
                $this->db->insert(db_prefix() . 'contact_permissions', [
                    'userid' => $contact_id,
                    'permission_id' => $permission,
                ]);

                // Auto set email notifications based on permissions
                if ($not_manual_request == true) {
                    if ($permission == 6) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contacts', ['project_emails' => 1, 'task_emails' => 1]);
                    } elseif ($permission == 3) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contacts', ['contract_emails' => 1]);
                    } elseif ($permission == 2) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contacts', ['estimate_emails' => 1]);
                    } elseif ($permission == 1) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contacts', ['invoice_emails' => 1, 'credit_note_emails' => 1]);
                    } elseif ($permission == 5) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contacts', ['ticket_emails' => 1]);
                    }
                }
            }


            return $contact_id;
        }

        return false;
    }

    public function addorganisation_byapi($data, $client_or_lead_convert_request = false) {
        $contact_data = [];
        foreach ($this->contact_columns as $field) {
            if (isset($data[$field])) {
                $contact_data[$field] = $data[$field];
                // Phonenumber is also used for the company profile
                if ($field != 'phonenumber') {
                    unset($data[$field]);
                }
            }
        }

        // From customer profile register
        if (isset($data['contact_phonenumber'])) {
            $contact_data['phonenumber'] = $data['contact_phonenumber'];
            unset($data['contact_phonenumber']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        if (isset($data['groups_in'])) {
            $groups_in = $data['groups_in'];
            unset($data['groups_in']);
        }

        $data = $this->check_zero_columns($data);

        $data['datecreated'] = date('Y-m-d H:i:s');

        if (is_staff_logged_in()) {
            $data['addedfrom'] = get_staff_user_id();
        }

        // New filter action
        $data = hooks()->apply_filters('before_client_added', $data);

        $this->db->insert(db_prefix() . 'clients', $data);

        $userid = $this->db->insert_id();
        if ($userid) {
            if (isset($custom_fields)) {
                $_custom_fields = $custom_fields;
                // Possible request from the register area with 2 types of custom fields for contact and for comapny/customer
                if (count($custom_fields) == 2) {
                    unset($custom_fields);
                    $custom_fields['customers'] = $_custom_fields['customers'];
                    $contact_data['custom_fields']['contacts'] = $_custom_fields['contacts'];
                } elseif (count($custom_fields) == 1) {
                    if (isset($_custom_fields['contacts'])) {
                        $contact_data['custom_fields']['contacts'] = $_custom_fields['contacts'];
                        unset($custom_fields);
                    }
                }
                handle_custom_fields_post($userid, $custom_fields);
            }
            /**
             * Used in Import, Lead Convert, Register
             */
            if ($client_or_lead_convert_request == true) {
                $contact_id = $this->add_contact_fromapi($contact_data, $userid, $client_or_lead_convert_request);
            }
            if (isset($groups_in)) {
                foreach ($groups_in as $group) {
                    $this->db->insert(db_prefix() . 'customer_groups', [
                        'customer_id' => $userid,
                        'groupid' => $group,
                    ]);
                }
            }
           
        }

        return $userid;
    }

    public function getAllContacts() {
		$contact_fields = "id,userid,userids,is_primary,firstname,lastname,email,phonenumber,alternative_emails,alternative_phonenumber,title,datecreated,password,new_pass_key,new_pass_key_requested,email_verified_at,email_verification_key,email_verification_sent_at,last_ip,last_login,last_password_change,active,profile_image,direction,invoice_emails,estimate_emails,credit_note_emails,contract_emails,task_emails,project_emails,ticket_emails,deleted_status,addedfrom";
        $sql = 'SELECT '.$contact_fields.' FROM tblcontacts where deleted_status=0 group by firstname';
        $query = $this->db->query($sql);
        return $result = $query->result_array();
    }

    public function checkduplicate() {
        if($_POST['cid']) {
            $this->db->where('userid !=', $_POST['cid']); 
        }
        $this->db->where('company', $_POST['name']);
        return $result = $this->db->get(db_prefix() . 'clients')->result_array();
    }

    public function checkduplicate_contact() {
        if($_POST['pid']) {
            $this->db->where('id !=', $_POST['pid']); 
        }
        $this->db->where('firstname', $_POST['name']);
        return $result = $this->db->get(db_prefix() . 'contacts')->result_array();
    }

    public function getContactById() {
        $sql = 'SELECT id, firstname, email, (select count(tblprojects.id) as dealcnt from tblprojects where tblprojects.clientid = tblclients.userid) as dealcount, tblcontacts.phonenumber as phone, company, tblclients.userid as clientid FROM tblcontacts
        LEFT JOIN  tblclients ON tblclients.userid = tblcontacts.userid
        WHERE  firstname = "'.$_REQUEST['contactid'].'"';
        $query = $this->db->query($sql);
        $output = '<table class="table table-striped">';
        $output .= '<thead><tr><th></th>';
        $output .= '<th>Name</th>';
        $output .= '<th>Email</th>';
        $output .= '<th>Phone number</th>';
        $output .= '<th>Organization</th>';
        $output .= '<th>No of Deals</th></tr></thead>';
        $output .= '<tbody>';
        foreach($query->result_array() as $val) {
            $output .= '<tr>';
            $output .= '<td><input type="radio" id="selectedcontact" name="selectedcontact" value="'.$val['id'].'" ></td>';
            $output .= '<td>'.$val['firstname'].'</td>';
            $output .= '<td>'.$val['email'].'</td>';
            $output .= '<td>'.$val['phone'].'</td>';
            $output .= '<td>'.$val['company'].'</td>';
            $output .= '<td>'.$val['dealcount'].'</td>';
            $output .= '</tr>';
        }
        $output .= '</tbody>';
        $output .= '</table>';
        return $output;
    }

    public function mergecontact() {
        $sql = 'SELECT id, userid FROM tblcontacts WHERE firstname = "'.$_REQUEST['cont_name'].'"';
        $query = $this->db->query($sql);
        $updateData = array();
        $updateData['contacts_id'] = $_REQUEST['contactid'];
        foreach($query->result_array() as $val) {
            if($val['id'] != $_REQUEST['contactid']) {
                //Update Deal Contact Person
                $this->db->where('contacts_id', $val['id']);
                $this->db->update(db_prefix() . 'project_contacts', $updateData);

                //Update Activity Contact Person
                $this->db->where('contacts_id', $val['id']);
                $this->db->update(db_prefix() . 'tasks', $updateData);

                //Delete Contact
                $this->db->where('id', $val['id']);
                $this->db->delete(db_prefix() . 'contacts');
            }
        }
        return true;
    }

    public function getDealsbyID($data = ''){
        $sql = 'SELECT id, name,company, start_date, deadline, status ,clientid FROM tblprojects
        LEFT JOIN  tblclients ON tblclients.userid = tblprojects.clientid
        WHERE  clientid="'.$_REQUEST['clientid'].'" AND tblprojects.deleted_status=0 ORDER BY status ASC';
        $query = $this->db->query($sql);
        $result = $query->result_array();
        $select = '';
        foreach($query->result_array() as $val) {
            $selected = '';
            if($data) {
                if($val['id'] == $data['id']) {
                    $selected = 'selected';
                }
            }
            $select .= '<option value="'.$val['id'].'" '.$selected.'>'.$val['name'].'</option>';
        }
        if($select == '')
            $select = '<option>Nothing Selected</option>';
        return $select;
    }

    public function get_userids() {
        
        $sql = 'SELECT clientid FROM tblprojects
        WHERE  teamleader ="'.get_staff_user_id().'" group by clientid';
        $query = $this->db->query($sql);
        $result_tl = $query->result_array();

        $sql = 'SELECT clientid FROM tblprojects
        WHERE  tblprojects.id IN (SELECT project_id FROM '.db_prefix().'project_members WHERE staff_id=' . get_staff_user_id() . ')  group by clientid';
        $query = $this->db->query($sql);
        $result_mem = $query->result_array();

        // $sql = 'SELECT clientid FROM tblprojects
        // WHERE  tblprojects.id IN (SELECT project_id FROM '.db_prefix().'project_contacts WHERE contacts_id=' . get_staff_user_id() . ')   group by clientid';
        // $query = $this->db->query($sql);
        // $result_cont = $query->result_array();
//pre($result_cont);
        $sql = 'SELECT userid as clientid FROM tblcontacts
        WHERE  tblcontacts.email=(SELECT email FROM '.db_prefix().'staff WHERE staffid=' . get_staff_user_id() . ')';
        $query = $this->db->query($sql);
        $result_org = $query->result_array();

        //$result = array_merge ($result_tl, $result_mem, $result_cont, $result_org);
        $result = array_merge ($result_tl, $result_mem, $result_org);
        $uids = array();
        foreach($result as $val) {
            $uids[] = $val['clientid'];
        }

        return array_unique($uids);

    }

    public function addvariation() {
        $data['name'] = $_POST['variation'];
        $data['productid'] = $_POST['product'];
        $this->db->insert(db_prefix() . 'variations', $data);
        return true;
    }
	public function getitem_price($name = '') {
        if(is_numeric($name)) {
            $currency = $this->currencies_model->get($name);
            $name = $currency->name;
        } else {
            $name = $name;
        }
        return $this->db->query('SELECT a.id as id, a.name as name  FROM ' . db_prefix() . 'items as a JOIN ' . db_prefix() . 'item_price as b ON b.item_id=a.id  where  a.id = b.item_id and b.currency = "'.$name.'"')->result_array();
    }
}
