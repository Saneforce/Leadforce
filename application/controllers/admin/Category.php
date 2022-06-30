<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Category extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('category_model');
        if (!is_admin()) {
            access_denied('Category');
        }
    }

    /* List all Categories */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('category');
        }
        $data['title'] = _l('category');
        $this->load->view('admin/category/manage', $data);
    }

    /* Add or edit category / ajax */
    public function manage()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['categoryid'] == '') {
                $success = $this->category_model->add($data);
                $message = '';
                if ($success == true) {
                    $message = _l('added_successfully', _l('category'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                ]);
            } else {
                $success = $this->category_model->edit($data);
                $message = '';
				if ($success == true) {
                    $message = _l('updated_successfully', _l('category'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                ]);
            }
        }
    }
	public function getcategory(){
		extract($_POST);
		$category = $this->category_model->get($cat_id);
		echo $category->cat_name;
	}
    /* Delete tax from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('catgory'));
        }
        $response = $this->category_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('category')));
        }
        redirect(admin_url('category'));
    }
	 public function category_exists()
    {
        if ($this->input->post()) {
            $cat_id = $this->input->post('categoryid');
            if ($cat_id != '') {
                $this->db->where('id !=', $cat_id);
				$this->db->where('cat_name', $this->input->post('name'));
				$total_rows = $this->db->count_all_results(db_prefix().'item_category');
                //$_current_cat = $this->db->get(db_prefix().'item_category')->row();
               if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
            }
            $this->db->where('cat_name', $this->input->post('name'));
            $total_rows = $this->db->count_all_results(db_prefix().'item_category');
            if ($total_rows > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
            die();
        }
    }

}
