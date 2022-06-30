<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Invoicepdfconfig extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Invoicepdf_model');
    }

    
    public function index()
    {
    	if (!has_permission('Invoicepdfconfig', '', 'view')) {
            access_denied('Invoicepdfconfig');
        }
        $data = array();
        $data['invoice'] = $this->Invoicepdf_model->get_invoice_config();
        if ($this->input->post()) {
            
            if(isset($_POST['submit']) && $_POST['submit'] == 'Save'){
                //pre($_POST);
                $dataUpdate = array();
                if($this->input->post('signature')) {
                    $signature = process_digital_invoice_signature_image($this->input->post('signature', false), PROPOSAL_ATTACHMENTS_FOLDER);
                    $dataUpdate['signature'] = $signature;
                }
                if(!empty($_FILES["inv_logo"]['name'])) {
                    $target_dir = "uploads/company/";
                    $target_file = $target_dir . 'invoice_' . basename($_FILES["inv_logo"]["name"]);
                    $uploadOk = 1;
                    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                    // Check if image file is a actual image or fake image
                    $check = getimagesize($_FILES["inv_logo"]["tmp_name"]);
                    if($check !== false) {
                        $message =  "File is an image - " . $check["mime"] . ".";
                        $uploadOk = 1;
                    } else {
                        $message =  "File is not an image.";
                        $uploadOk = 0;
                    }

                    // Check file size
                    if ($_FILES["inv_logo"]["size"] > 500000) {
                        $message =  "Sorry, your file is too large.";
                        $uploadOk = 0;
                    }

                    // Allow certain file formats
                    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                    && $imageFileType != "gif" ) {
                        $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                        $uploadOk = 0;
                    }

                    // Check if $uploadOk is set to 0 by an error
                    if ($uploadOk == 0) {
                        set_alert('danger', $message);
                        redirect(admin_url('Invoicepdfconfig'));
                    // if everything is ok, try to upload file
                    } else {
                        //echo "1111";
                        if (move_uploaded_file($_FILES["inv_logo"]["tmp_name"], $target_file)) {
                            $dataUpdate['inv_logo'] = $target_file;
                        }
                    }
                    
                }
                
                $dataUpdate['contact_details'] = $_POST['contact_details'];
                $dataUpdate['tc'] = $_POST['tc'];
                $dataUpdate['use_as_default'] = $_POST['use_as_default'];
                //pre($dataUpdate);
                if($data['invoice'] && count($data['invoice']) > 0) {
                    $this->db->where('id',1);
                    $this->db->update(db_prefix() . 'invoice_pdf_config', $dataUpdate);
                } else {
                    $this->Invoicepdf_model->storeInvConfig($dataUpdate);
                }
                set_alert('success', "Records Saved Successfully.");
                redirect(admin_url('Invoicepdfconfig'));   
            }
            
        }

        $data['title']     = _l('invoice_pdf_config');
        $data['bodyclass'] = 'dynamic-create-groups';
        $this->load->view('admin/clients/invoiceconfig', $data);
    }

	

}