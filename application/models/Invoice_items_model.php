<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Invoice_items_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get invoice item by ID
     * @param  mixed $id
     * @return mixed - array if not passed id, object if id passed
     */
    public function get($id = '')
    {
        $columns             = $this->db->list_fields(db_prefix() . 'items');
        $rateCurrencyColumns = '';
        foreach ($columns as $column) {
            if (strpos($column, 'rate_currency_') !== false) {
                $rateCurrencyColumns .= $column . ',';
            }
        }
        /*$this->db->select($rateCurrencyColumns . '' . db_prefix() . 'items.id as itemid,rate,
            t1.taxrate as taxrate,t1.id as taxid,t1.name as taxname,
            t2.taxrate as taxrate_2,t2.id as taxid_2,t2.name as taxname_2,
            description,long_description,group_id,' . db_prefix() . 'items_groups.name as group_name,unit');*/
		$this->db->select(db_prefix() . 'items.id as itemid,'.db_prefix() . 'items.name as name,code,categoryid,
            t1.taxrate as taxrate,t1.id as taxid,t1.name as taxname,
            description,' . db_prefix() . 'item_category.cat_name as cat_name,unit');
        $this->db->from(db_prefix() . 'items');
        $this->db->join( db_prefix() . 'taxes t1', 't1.id = ' . db_prefix() . 'items.tax', 'left');
        $this->db->join(db_prefix() . 'item_category', '' . db_prefix() . 'item_category.id = ' . db_prefix() . 'items.categoryid', 'left');
        $this->db->order_by('description', 'asc');
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'items.id', $id);

            return $this->db->get()->row();
        }

        return $this->db->get()->result_array();
    }

    public function get_grouped()
    {
        $items = [];
        $this->db->order_by('name', 'asc');
        $groups = $this->db->get(db_prefix() . 'items_groups')->result_array();

        array_unshift($groups, [
            'id'   => 0,
            'name' => '',
        ]);

        foreach ($groups as $group) {
            $this->db->select('*,' . db_prefix() . 'items_groups.name as group_name,' . db_prefix() . 'items.id as id');
            $this->db->where('group_id', $group['id']);
            $this->db->join(db_prefix() . 'items_groups', '' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
            $this->db->order_by('description', 'asc');
            $_items = $this->db->get(db_prefix() . 'items')->result_array();
            if (count($_items) > 0) {
                $items[$group['id']] = [];
                foreach ($_items as $i) {
                    array_push($items[$group['id']], $i);
                }
            }
        }

        return $items;
    }

    /**
     * Add new invoice item
     * @param array $data Invoice item data
     * @return boolean
     */
	 public function add($data)
    {
		
        unset($data['itemid']);
		$unit_prices = $unit_currency = $unit_tax = array();
		 if (isset($data['unit_price'])) {
            $unit_prices = $data['unit_price'];
        }
		 if (isset($data['currency'])) {
            $unit_currency = $data['currency'];
        }
		if (isset($data['tax'])) {
            $unit_tax = $data['tax'];
        }

        unset($data['unit_price']);
        unset($data['currency']);
        unset($data['tax']);
		if ($data['name'] == '') {
            unset($data['name']);
        }
		if ($data['code'] == '') {
            unset($data['code']);
        }
		if ($data['categoryid'] == '') {
            unset($data['categoryid']);
        }
		if ($data['unit'] == '') {
            unset($data['unit']);
        }
        if ($data['tax'] == '') {
            unset($data['tax']);
        }
		if ($data['description'] == '') {
            unset($data['description']);
        } 
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $columns = $this->db->list_fields(db_prefix() . 'items');
        $this->load->dbforge();
        foreach ($data as $column => $itemData) {
            if (!in_array($column, $columns) && strpos($column, 'rate_currency_') !== false) {
                $field = [
                        $column => [
                            'type' => 'decimal(15,' . get_decimal_places() . ')',
                            'null' => true,
                        ],
                ];
                $this->dbforge->add_column('items', $field);
            }
        }

        $this->db->insert(db_prefix() . 'items', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
			if(!empty($unit_prices)){
				$i = 0;
				foreach($unit_prices as $unit_price1){
					$ins_data['item_id']  = $insert_id;
					$ins_data['tax']  	   = $unit_tax[$i];
					$ins_data['price']    = $unit_price1;
					$ins_data['currency'] = $unit_currency[$i];
					$this->db->insert(db_prefix() . 'item_price', $ins_data);
					$i++;
				}
			}
            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields, true);
            }

            hooks()->do_action('item_created', $insert_id);

            log_activity('New Invoice Item Added [ID:' . $insert_id . ', ' . $data['description'] . ']');

            return $insert_id;
        }

        return false;
    }
    public function add_back($data)
    {
        unset($data['itemid']);
        if ($data['tax'] == '') {
            unset($data['tax']);
        }

        if (isset($data['tax2']) && $data['tax2'] == '') {
            unset($data['tax2']);
        }

        if (isset($data['group_id']) && $data['group_id'] == '') {
            $data['group_id'] = 0;
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $columns = $this->db->list_fields(db_prefix() . 'items');
        $this->load->dbforge();
        foreach ($data as $column => $itemData) {
            if (!in_array($column, $columns) && strpos($column, 'rate_currency_') !== false) {
                $field = [
                        $column => [
                            'type' => 'decimal(15,' . get_decimal_places() . ')',
                            'null' => true,
                        ],
                ];
                $this->dbforge->add_column('items', $field);
            }
        }

        $this->db->insert(db_prefix() . 'items', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields, true);
            }

            hooks()->do_action('item_created', $insert_id);

            log_activity('New Invoice Item Added [ID:' . $insert_id . ', ' . $data['description'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Update invoiec item
     * @param  array $data Invoice data to update
     * @return boolean
     */
    public function edit($data)
    {
        $itemid = $data['itemid'];
		$data_prices = $data['unit_price'];
		$data_currency = $data['currency'];
		$data_tax = $data['tax'];
        unset($data['itemid']);
        unset($data['unit_price']);
        unset($data['currency']);
        unset($data['tax']);

       /* if (isset($data['group_id']) && $data['group_id'] == '') {
            $data['group_id'] = 0;
        }*/

        if (isset($data['tax']) && $data['tax'] == '') {
            $data['tax'] = null;
        }

        /*if (isset($data['tax2']) && $data['tax2'] == '') {
            $data['tax2'] = null;
        }*/

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $columns = $this->db->list_fields(db_prefix() . 'items');
        $this->load->dbforge();

       /* foreach ($data as $column => $itemData) {
            if (!in_array($column, $columns) && strpos($column, 'rate_currency_') !== false) {
                $field = [
                        $column => [
                            'type' => 'decimal(15,' . get_decimal_places() . ')',
                            'null' => true,
                        ],
                ];
                $this->dbforge->add_column('items', $field);
            }
        }*/

        $affectedRows = 0;

        $data = hooks()->apply_filters('before_update_item', $data, $itemid);

        $this->db->where('id', $itemid);
        $this->db->update(db_prefix() . 'items', $data);
		$this->db->where('item_id', $itemid);
		$this->db->delete(db_prefix().'item_price');
		$ins_price = array();
		if(!empty($data_prices)){
			$i = 0;
			foreach($data_prices as $unit_price_1){
				$ins_price['item_id'] = $itemid;
				$ins_price['price'] = $unit_price_1;
				$ins_price['tax'] = $data_tax[$i];
				$ins_price['currency'] = $data_currency[$i];
				$this->db->insert(db_prefix() . 'item_price', $ins_price);
				$i++;
			}
		}
        if ($this->db->affected_rows() > 0) {
            log_activity('Invoice Item Updated [ID: ' . $itemid . ', ' . $data['description'] . ']');
            $affectedRows++;
        }

        if (isset($custom_fields)) {
            if (handle_custom_fields_post($itemid, $custom_fields, true)) {
                $affectedRows++;
            }
        }

        if ($affectedRows > 0) {
            hooks()->do_action('item_updated', $itemid);
        }

        return $affectedRows > 0 ? true : false;
    }

    public function search($q)
    {
        $this->db->select('rate, id, description as name, long_description as subtext');
        $this->db->like('description', $q);
        $this->db->or_like('long_description', $q);

        $items = $this->db->get(db_prefix() . 'items')->result_array();

        foreach ($items as $key => $item) {
            $items[$key]['subtext'] = strip_tags(mb_substr($item['subtext'], 0, 200)) . '...';
            $items[$key]['name']    = '(' . app_format_number($item['rate']) . ') ' . $item['name'];
        }

        return $items;
    }

    /**
     * Delete invoice item
     * @param  mixed $id
     * @return boolean
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'items');
        if ($this->db->affected_rows() > 0) {
			$this->db->where('item_id', $id);
            $this->db->delete(db_prefix() . 'item_price');
			
            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'items_pr');
            $this->db->delete(db_prefix() . 'customfieldsvalues');

            log_activity('Invoice Item Deleted [ID: ' . $id . ']');

            hooks()->do_action('item_deleted', $id);

            return true;
        }

        return false;
    }

    public function get_groups()
    {
        $this->db->order_by('name', 'asc');

        return $this->db->get(db_prefix() . 'items_groups')->result_array();
    }

    public function add_group($data)
    {
        $this->db->insert(db_prefix() . 'items_groups', $data);
        log_activity('Items Group Created [Name: ' . $data['name'] . ']');

        return $this->db->insert_id();
    }

    public function edit_group($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'items_groups', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Items Group Updated [Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    public function delete_group($id)
    {
        $this->db->where('id', $id);
        $group = $this->db->get(db_prefix() . 'items_groups')->row();

        if ($group) {
            $this->db->where('group_id', $id);
            $this->db->update(db_prefix() . 'items', [
                'group_id' => 0,
            ]);

            $this->db->where('id', $id);
            $this->db->delete(db_prefix() . 'items_groups');

            log_activity('Item Group Deleted [Name: ' . $group->name . ']');

            return true;
        }

        return false;
    }
	public function get_category()
    {
        $this->db->select('id,cat_name,status,created_date');
        $items = $this->db->get(db_prefix() . 'item_category')->result_array();
        return $items;
    }
	public function addCategory($data) {
        $contact_data = [];
        if (isset($data['category'])) {
            $contact_data['cat_name'] = $data['category'];
            unset($data['category']);
        }
        $this->db->insert(db_prefix() . 'item_category', $contact_data);
        $cat_id = $this->db->insert_id();
        return $cat_id;
    }
	public function get_items($name = '') {
        return $this->db->query('SELECT a.id as id, a.name as name,b.price as price   FROM ' . db_prefix() . 'items as a JOIN ' . db_prefix() . 'item_price as b ON b.item_id=a.id  where  a.id = b.item_id and b.currency = "'.$name.'"')->result_array();
    }
	public function get_items_unit_prices($id = '') {
        return $this->db->query('SELECT id,item_id,tax,price,currency FROM ' . db_prefix() . 'item_price where item_id = "'.$id.'"')->result_array();
    }
	public function getitem_price($name = '') {
		$cur_val = $_REQUEST['value'];
        return $this->db->query('SELECT a.id as id, a.name as name,b.price as price   FROM ' . db_prefix() . 'items as a JOIN ' . db_prefix() . 'item_price as b ON b.item_id=a.id  where  a.id = b.item_id and b.currency = "'.$name.'" and a.id = "'.$cur_val.'"')->result_array();
    }
}
