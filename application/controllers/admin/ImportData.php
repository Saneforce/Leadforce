<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ImportData extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ImportData_model');
    }
/**
 * DealLossReasons List
**/
    public function index()
    {
    	if (!has_permission('ImportData', '', 'view')) {
            access_denied('ImportData');
        }
        $dbFields = array();
		
		$fields = get_option('deal_fields');
		$fields1 = get_option('deal_mandatory');
		$data['need_fields'] = $data['mandatory_fields'] = array("name",'teamleader_name');
		if(!empty($fields) && $fields != 'null'){
			$data['need_fields'] = json_decode($fields);
		}
		if(!empty($fields1) && $fields1 != 'null'){
			$mandatory_fields = json_decode($fields1);
			array_unshift($mandatory_fields,"name");
			$data['mandatory_fields'] = $mandatory_fields;
		}
        //$dbFields = $this->db->list_fields(db_prefix().'contacts');
        if((isset($_POST['contact']) && $_POST['contact'] == 'contact') || $this->input->post('download_contact_sample') === 'true') {
            $dbFieldsContact = $this->db->list_fields(db_prefix().'contacts');
            $dbFieldsContact = preg_filter('/^/', 'person_', $dbFieldsContact);
            $dbFieldsContact = str_replace('person_firstname', 'person_fullname', array_values($dbFieldsContact));
            $dbFieldsContact = str_replace('person_title', 'person_position', array_values($dbFieldsContact));
            $dbFields = $dbFieldsContact;
            array_unshift($dbFields , 'deals_id');
            $customfield = get_custom_fields('contacts');
        }
        if($_POST['deals'] == 'deals' || $this->input->post('download_sample') === 'true') {
            $dbFieldsContact = $this->db->list_fields(db_prefix().'contacts');
            $dbFieldsDeal = $this->db->list_fields(db_prefix().'projects');
            $dbFieldsDeal1 = $data['need_fields'];
            $dbFieldsDeal[] = 'deal_followers';
			if (!in_array("description", $dbFieldsDeal1)){
				if (($key = array_search('description', $dbFieldsDeal)) !== false) {
					unset($dbFieldsDeal[$key]);
				}
			}
			if (!in_array("status", $dbFieldsDeal1)){
				if (($key = array_search('status', $dbFieldsDeal)) !== false) {
					unset($dbFieldsDeal[$key]);
				}
			}
			if (!in_array("pipeline_id", $dbFieldsDeal1)){
				if (($key = array_search('pipeline_id', $dbFieldsDeal)) !== false) {
					unset($dbFieldsDeal[$key]);
				}
			}
			if (!in_array("clientid", $dbFieldsDeal1)){
				if (($key = array_search('clientid', $dbFieldsDeal)) !== false) {
					unset($dbFieldsDeal[$key]);
				}
			}
			if (!in_array("teamleader", $dbFieldsDeal1)){
				if (($key = array_search('owner', $dbFieldsDeal)) !== false) {
					unset($dbFieldsDeal[$key]);
				}
			}
			if (!in_array("start_date", $dbFieldsDeal1)){
				if (($key = array_search('start_date', $dbFieldsDeal)) !== false) {
					//unset($dbFieldsDeal[$key]);
				}
			}
			if (!in_array("deadline", $dbFieldsDeal1)){
				if (($key = array_search('deadline', $dbFieldsDeal)) !== false) {
					//unset($dbFieldsDeal[$key]);
				}
			}
			if (!in_array("project_cost", $dbFieldsDeal1)){
				if (($key = array_search('project_cost', $dbFieldsDeal)) !== false) {
					unset($dbFieldsDeal[$key]);
				}
			}
			if (!in_array("project_members[]", $dbFieldsDeal1)){
				if (($key = array_search('deal_followers', $dbFieldsDeal)) !== false) {
					unset($dbFieldsDeal[$key]);
				}
			}


            if (!in_array("project_start_date", $dbFieldsDeal1)){
				if (($key = array_search('start_date', $dbFieldsDeal)) !== false) {
					unset($dbFieldsDeal[$key]);
				}
			}
            if (!in_array("project_deadline", $dbFieldsDeal1)){
				if (($key = array_search('deadline', $dbFieldsDeal)) !== false) {
					unset($dbFieldsDeal[$key]);
				}
			}


		   //array_unshift($dbFieldsDeal,"id");
            $dbFieldsOrg = $this->db->list_fields(db_prefix().'clients');
            $dbFieldsTask = $this->db->list_fields(db_prefix().'tasks');
            $dbFieldsDeal = preg_filter('/^/', 'deal_', $dbFieldsDeal);
            $dbFieldsOrg = preg_filter('/^/', 'organization_', $dbFieldsOrg);
            

            if (in_array("primary_contact", $dbFieldsDeal1) || in_array("project_contacts[]", $dbFieldsDeal1)){
                $dbFieldsContact = preg_filter('/^/', 'person_', $dbFieldsContact);
                $dbFieldsContact = str_replace('person_firstname', 'person_fullname', array_values($dbFieldsContact));
                $dbFieldsContact = str_replace('person_title', 'person_position', array_values($dbFieldsContact));

                $dbFieldsTask = preg_filter('/^/', 'activity_', $dbFieldsTask);
                if (($key = array_search('activity_datemodified', $dbFieldsTask)) !== false) {
                    unset($dbFieldsTask[$key]);
                }
                if (($key = array_search('activity_call_request_id', $dbFieldsTask)) !== false) {
                    unset($dbFieldsTask[$key]);
                }
                if (($key = array_search('activity_call_msg', $dbFieldsTask)) !== false) {
                    unset($dbFieldsTask[$key]);
                }
                if (($key = array_search('activity_call_code', $dbFieldsTask)) !== false) {
                    unset($dbFieldsTask[$key]);
                }
                /*if (!in_array("project_contacts[]", $dbFieldsDeal1) && !in_array("primary_contact", $dbFieldsDeal1) ){
                    $dbFieldsContact = array();
                }
                else{*/
                    $dbFieldsTask[] = 'activity_assignedto';
                //}
            }else{
                $dbFieldsContact =array();
                $dbFieldsTask =array();
            }
            

            $dbFieldsDeal = str_replace('deal_pipeline_id', 'deal_pipeline', array_values($dbFieldsDeal));
            $dbFieldsDeal = str_replace('deal_status', 'deal_pipeline_stage', array_values($dbFieldsDeal));
            $dbFieldsDeal = str_replace('deal_teamleader', 'deal_owner', array_values($dbFieldsDeal));
            $dbFieldsDeal = str_replace('deal_stage_of', 'deal_stage', array_values($dbFieldsDeal));
            
            $dbFieldsOrg = str_replace('organization_company', 'organization_name', array_values($dbFieldsOrg));

            // unsettings noneeded fields form deal table
            if (($key = array_search('deal_lead_id', $dbFieldsDeal)) !== false) {
                unset($dbFieldsDeal[$key]);
            }
            if (($key = array_search('deal_project_modified', $dbFieldsDeal)) !== false) {
                unset($dbFieldsDeal[$key]);
            }
            if (($key = array_search('deal_modified_by', $dbFieldsDeal)) !== false) {
                unset($dbFieldsDeal[$key]);
            }
           // $dbFieldsTask[] = 'activity_assignedto';
        
        
            //pre($dbFieldsDeal);exit;
            //pre($dbFieldsTask);
            //pre($dbFieldsDeal);exit;
            // pr($dbFieldsOrg); exit;
            foreach ($dbFields as $key => $contactField) {
                if ($contactField == 'phonenumber') {
                    $dbFields[$key] = 'contact_phonenumber';
                }
            }
			
			if (!in_array("clientid", $dbFieldsDeal1)  ){
				$dbFieldsOrg = array();
			}
            $dbFields = array_merge($dbFieldsContact,$dbFieldsOrg,$dbFieldsDeal,$dbFieldsTask);
			
            //pre($dbFields);
            //$dbFields = array_merge($dbFields, $this->db->list_fields(db_prefix().'clients'));
            $customfield = get_custom_fields('customers');
            $customfield = array_merge($customfield,get_custom_fields('projects'));
            $customfield = array_merge($customfield,get_custom_fields('contacts'));
        }
        $this->load->library('import/import_deals', [], 'import');
        
        $this->import->setDatabaseFields($dbFields)
                     ->setCustomFields($customfield);
        if ($this->input->post('download_sample') === 'true' || $this->input->post('download_contact_sample') === 'true') {
            $this->load->library('excel');
            $this->import->downloadSample();
        }

        if ($this->input->post() && isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
//pre($_POST);
            if($_POST['deals'] == 'deals') {
                $validCSV = $this->import->checkValidCSV();
                if($validCSV != 1) {
                    set_alert('warning', $validCSV);
                    redirect(admin_url('importData'));
                }
                $uniqueId = strtotime(date('Y-m-d H:i:s'));
                if (!is_dir('uploads/import_files/'.$uniqueId)) {
                    mkdir('./uploads/import_files/'.$uniqueId, 0777, true);
                }
                $target_dir = './uploads/import_files/'.$uniqueId.'/';
                $this->import->setUniqueId($uniqueId);
        
                $this->load->library('upload');
                $config = array(
                    'upload_path' => $target_dir,
                    'allowed_types' => "csv",
                    'overwrite' => TRUE
                );
                $this->upload->initialize($config);
                if($this->upload->do_upload('file'))
                {
                    $data = array('upload_data' => $this->upload->data());
					
                    $this->import->setSimulation($this->input->post('simulate'))
                          ->setTemporaryFileLocation($_FILES['file']['tmp_name'])
                          ->setFilename($_FILES['file']['name']);
                    $result = $this->import->perform();
                    if($result['skippedCnt'] > 0) {
                        $this->import->moveSkippedData($result['skipped']);
                    }

                    if (!$this->import->isSimulation()) {
                        //set_alert('success', _l('import_total_imported', $this->import->totalImported()));
                        set_alert('success', $result['importedCnt'].' Imported, '.$result['skippedCnt'].' Skipped.');
                        redirect(admin_url('ImportData'));
                    }
                }
                else
                {
                    set_alert('warning', 'Please Import CSV file.');
                    redirect(admin_url('ImportData'));
                }
            } else {
                $allowed = array('csv');
                $filename = $_FILES['file']['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if (!in_array($ext, $allowed)) {
                    set_alert('warning', 'Please Import CSV file.');
                } else {
                    $validCSV = $this->import->checkValidCSV();
                    if($validCSV != 1) {
                        set_alert('warning', $validCSV);
                        redirect(admin_url('ImportData'));
                    }
                    $this->import->setSimulation($this->input->post('simulate'))
                            ->setTemporaryFileLocation($_FILES['file']['tmp_name'])
                            ->setFilename($_FILES['file']['name']);
                    $result = $this->import->perform();
                    
                    set_alert('success', $result['importedCnt'].' Imported, '.$result['skippedCnt'].' Skipped.');
                    
                    redirect(admin_url('ImportData'));
                }
            }
        }

        $data['files']    = $this->projects_model->get_uploaded_files();
//        pre($data['files']);
        $data['groups']    = $this->clients_model->get_groups();
        $data['title']     = _l('import');
		$data['mandatory_fields1'] = array();
		
		if(!empty($data['mandatory_fields'])){
			$i = 0;
			foreach($data['mandatory_fields'] as $mandatory_field12){
				if($mandatory_field12 == 'name'){
					$data['mandatory_fields1'][$i] = 'Deal name';
				}
				else if($mandatory_field12 == 'project_contacts[]' || $mandatory_field12 == 'primary_contact'){
					if (!in_array("Person fullname", $data['mandatory_fields1'])){
						$data['mandatory_fields1'][$i] = 'Person fullname';
						$i++;
						$data['mandatory_fields1'][$i] = 'Assigned Person Mail Id';
					}
					else{
						$i--;
					}
				}
				else if($mandatory_field12 == 'clientid' ){
					$data['mandatory_fields1'][$i] = 'Organisation name';
				}
				else if($mandatory_field12 == 'pipeline_id' ){
					$data['mandatory_fields1'][$i] = 'Pipeline';
				}
				else if($mandatory_field12 == 'status' ){
					$data['mandatory_fields1'][$i] = 'Pipeline stage';
				}
				else if($mandatory_field12 == 'project_members[]' ){
					$data['mandatory_fields1'][$i] = 'Deal owner mail id';
				}
				else if($mandatory_field12 == 'project_cost' ){
					$data['mandatory_fields1'][$i] = 'Deal value';
				}
				else if($mandatory_field12 == 'project_start_date' ){
					$data['mandatory_fields1'][$i] = 'Deal start date';
				}
				else if($mandatory_field12 == 'project_deadline' ){
					$data['mandatory_fields1'][$i] = 'Deal end date';
				}
				else if($mandatory_field12 == 'tags' ){
					$data['mandatory_fields1'][$i] = 'Tags';
				}
				else if($mandatory_field12 == 'description' ){
					$data['mandatory_fields1'][$i] = 'Deal description';
				}
				$i++;
			}
			$data['mandatory_fields1'][$i] = 'Project currency';
		}
		
        $data['bodyclass'] = 'dynamic-create-groups';
        $this->load->view('admin/clients/import_deals', $data);
    }
	
/**
 * Add new or edit existing DealLossReasons
**/
    public function save($id = '')
    {
        if (!has_permission('DealLossReasons', '', 'view')) {
            access_denied('DealLossReasons');
        }
        if ($this->input->post())
		{
            $data = $this->input->post();
            if ($id == '') {
				$checkDealLossReasonsexist = $this->DealLossReasons_model->checkDealLossReasonsExist($data['name']);
				if(!empty($checkDealLossReasonsexist)) {
					set_alert('warning', _l('already_exist', _l('DealLossReasons')));
					redirect(admin_url('DealLossReasons'));
				}
				else {
					if (!has_permission('DealLossReasons', '', 'create')) {
						access_denied('DealLossReasons');
					}
					
					
					$id = $this->DealLossReasons_model->add_DealLossReasons($data);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('DealLossReasons')));
						redirect(admin_url('DealLossReasons'));
					}
				}
            }
			else {
				$checkDealLossReasonsexist = $this->DealLossReasons_model->checkDealLossReasonsExist($data['name']);
				if(!empty($checkDealLossReasonsexist) && $checkDealLossReasonsexist->id != $id) {
					set_alert('warning', _l('already_exist', _l('DealLossReasons')));
					redirect(admin_url('DealLossReasons'));
				}
				else {
					if (!has_permission('DealLossReasons', '', 'edit')) {
						access_denied('DealLossReasons');
					}
					
					$success = $this->DealLossReasons_model->update_DealLossReasons($data, $id);
					if ($success) {
						set_alert('success', _l('updated_successfully', _l('DealLossReasons')));
					}
					redirect(admin_url('DealLossReasons'));
				}
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('DealLossReasons_lowercase'));
        }
		else {
            $DealLossReasons = $this->DealLossReasons_model->getDealLossReasonsbyId($id);
            $data['DealLossReasons'] = $DealLossReasons;
            $title = _l('edit', _l('DealLossReasons')) . ' ' . $DealLossReasons->name;
        }
        $data['bodyclass'] = 'kb-DealLossReasons';
        $data['title']     = $title;
        $this->load->view('admin/DealLossReasons/form', $data);
    }

/**
 * View existing DealLossReasons details
**/
	public function view($id)
    {
        if (!has_permission('DealLossReasons', '', 'view')) {
            access_denied('View DealLossReasons');
        }
        $data['deallossreasons'] = $this->DealLossReasons_model->getDealLossReasonsbyId($id);

        if (!$data['deallossreasons']) {
            show_404();
        }
        add_views_tracking('kb_DealLossReasons', $id);
        $data['title'] = $data['deallossreasons']->name;
        $this->load->view('admin/DealLossReasons/view', $data);
    }
	
/**
 * Delete existing DealLossReasons details
**/
    public function delete_DealLossReasons($id)
    {
        if (!has_permission('DealLossReasons', '', 'delete')) {
            access_denied('DealLossReasons');
        }
        if (!$id) {
            redirect(admin_url('DealLossReasons'));
        }
        $response = $this->DealLossReasons_model->delete_DealLossReasons($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('DealLossReasons')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('DealLossReasons_lowercase')));
        }
        redirect(admin_url('DealLossReasons'));
    }

    public function downloadcsv() {
        //$url = './uploads/import_files/1622029439/sample_import_file_(6).csv';  
        $url = './uploads/import_files/'.$_REQUEST['filepath'].'/'.$_REQUEST['filename'];
        //exit;
        // Use basename() function to return
        // the base name of file  
        $file_name = basename($url); 
        // Checking if the file is a
        // CSV file or not
        $info = pathinfo($file_name);
          
        if ($info["extension"] == "csv") {
              
            /* Use file_get_contents() function
            to get the file from url and use 
            file_put_contents() function to save
            the file by using base name */   
            header("Content-Description: File Transfer"); 
            header("Content-Type: application/octet-stream"); 
            header(
            "Content-Disposition: attachment; filename=\""
            . $file_name . "\""); 
            readfile ($url);
            //echo "File downloaded successfully";
        }
        
        exit;
    }

    public function downloadxl() {
        // create file name
        $fileName = 'sample_import_file.xlsx';  
        // load excel library
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        // set Header
              
        // set Row

        $totalSampleFields = 0;
        $dbFieldKeys       = [];
        $key = 'A';
        foreach ($this->import->getImportableDatabaseFields() as $field) {
            $objPHPExcel->getActiveSheet()->SetCellValue($key.$totalSampleFields, $this->formatFieldNameForHeading($field));
        
            //echo '"' . $this->formatFieldNameForHeading($field) . '",';
            $dbFieldKeys[$totalSampleFields] = $field;
            $totalSampleFields++;
            $key++;
        }

        foreach ($this->import->getCustomFields() as $field) {
            $objPHPExcel->getActiveSheet()->SetCellValue($key.$totalSampleFields, $this->formatFieldNameForHeading($field));
            $totalSampleFields++;
            $key++;
        }

        $totalSampleRows = 1;
        if($_POST['download_contact_sample'] === 'true') {
            $sample_CSV = array('123','Stojanovic Goran','client@gmail.com','9787654567','Manager');
        } else {
            $sample_CSV = array('Stojanovic Goran','client@gmail.com','9787654567','Manager','Saneforce','9898989898','India','Chennai','600001','Tamilnadu','143, GJ street, kolmur, chennai.','www.web.com','Deal 1907','Type Description','Hot','Payroll','seetha@saneforce.com','19-05-2021','30-05-2021','5000','USD','emp1@saneforce.com,emp2@saneforce.com,emp3@saneforce.com','Call sundar','Call','Type Description','High','19-05-2021','yes','darious@saneforce.com');
        }
        
        $sampleCnt = count($sample_CSV);
        $remainCnt = $totalSampleFields - $sampleCnt;
        $custArray = [];

        for($i = 0; $i < $remainCnt; $i++) {
            $custArray[] = 'Custom Data';
        }
        $sample_CSV = array_merge($sample_CSV,$custArray);
        //pre($sample_CSV);
        
        for ($row = 0; $row < $totalSampleRows; $row++) {
            $key = 'A';
            for ($f = 0; $f < $totalSampleFields; $f++) {
                //$sampleDataText = $this->getTableRowDataText(isset($dbFieldKeys[$f]) ? $dbFieldKeys[$f] :  null);
                $objPHPExcel->getActiveSheet()->SetCellValue($key . $rowCount, $sample_CSV[$f]);
                $key++;
                $rowCount++;
            }
        }
        
        $filename = "sample_import_file.csv";
        header('Content-Type: application/vnd.ms-excel'); 
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0'); 
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');  
        $objWriter->save('php://output'); 
        exit;
    }

    public function downloadskipcsv() {
        //$url = './uploads/import_files/1622029439/sample_import_file_(6).csv';  
        $url = './uploads/import_files/'.$_REQUEST['filepath'].'/skipped_file.xls';
        if(file_exists($url)) {
            //$zipname = 'cron/json/mstr_data.zip';
            header('Content-Type: application/zip');
            header('Content-disposition: attachment; filename=' . basename($url));
            header('Content-Length: ' . filesize($url));
            readfile($url);
        }
        //exit;
        // Use basename() function to return
        // the base name of file  
        //$file_name = basename($url); 
        // Checking if the file is a
        // CSV file or not
        // $info = pathinfo($file_name);
          
        // if ($info["extension"] == "csv") {
              
        //     /* Use file_get_contents() function
        //     to get the file from url and use 
        //     file_put_contents() function to save
        //     the file by using base name */   
        //     header("Content-Description: File Transfer"); 
        //     header("Content-Type: application/octet-stream"); 
        //     header(
        //     "Content-Disposition: attachment; filename=\""
        //     . $file_name . "\""); 
        //     readfile ($url);
        //     //echo "File downloaded successfully";
        // }
        
        exit;
    }

    public function revertData($id) {
        $result = $this->projects_model->reverData($id);
        if($result) {
            set_alert('success', ' Records Reverted Successfully.');
            redirect(admin_url('ImportData'));
        }
    }
}