<?php

defined('BASEPATH') or exit('No direct script access allowed');

abstract class App_import
{
    /**
     * Codeigniter Instance
     * @var object
     */
    protected $ci;

    /**
     * Stores the import guidelines
     * @var array
     */
    protected $importGuidelines = [];

    /**
     * Text used for sample data tables and CSV
     * @var string
     */
    protected $sampleDataText = 'Sample Data';

    /**
     * Total imported leads
     * @var integer
     */
    protected $totalImported = 0;

    /**
     * App temp folder location
     * @var string
     */
    protected $appTmpFolder = TEMP_FOLDER;

    /**
     * After the uploaded file is moved to the $appTmpFolder, we will store the full file path here
     * for further usage
     * @var string
     */
    protected $tmpFileStoragePath;

    /**
     * Temporary file location from $_FILES
     * Used when intializing the import
     * @var string
     */
    protected $temporaryFileFromFormLocation;

    /**
     * This is actually the temporary dir in the $appTempFolder used when moving the file into $appTempFOlder
     * @var string
     */
    protected $tmpDir;

    /**
     * Uploaded file name
     * @var string
     */
    protected $filename;

    /**
     * The actual .csv file rows
     * @var array
     */
    protected $rows;

    /**
     * Total rows
     * Total count from $rows
     * @var mixed
     */
    protected $totalRows = null;
    protected $error_rows = null;

    /**
     * When the total rows passes this warning number will show a warning to split the import process
     * @var integer
     */
    private $warningOnTotalRows = 500;

    /**
     * Indicating does this import/upload is simulation
     * @var boolean
     */
    protected $isSimulation = false;

    /**
     * Total rows to show when simulating data
     * For example if user have 2500 rows in the .csv file in the simulate HTML table will be shown only $maxSimulationRows
     * @var integer
     */
    protected $maxSimulationRows = 100;

    /**
     * This is tha actual simulation data that will be shown the preview simulation tabe
     * @var array
     */
    protected $simulationData = [];

    /**
     * Database fields that will be used for import
     * @var array
     */
    protected $databaseFields = [];

    protected $uniqueId;

    /**
     * Custom fields that will be used for import
     * @var array
     */
    protected $customFields = [];

    public function __construct()
    {
        $this->ci = &get_instance();

        $this->setDefaultImportGuidelinesInfo();
    }

    /**
     * This method must be implemented on the child import class
     * This method will perform all the import actions and checks
     * @return mixed
     */
    abstract public function perform();

    /**
     * In some cases there will be some errors that we need to catch, after we catch the errors, we will redirect the user to this URL
     * This method is required and must be implemented in the child class
     * @return string
     */
    abstract protected function failureRedirectURL();

    /**
     * Format column/field name for table heading/csv
     * @param  string $field the actual field name
     * @return string
     */
    public function formatFieldNameForHeading($field)
    {
        return str_replace('_', ' ', ucfirst($field));
    }

    /**
     * Sets database fields
     * @param Object
     */
    public function setDatabaseFields($fields)
    {
        $this->databaseFields = $fields;
        return $this;
    }

    public function setUniqueId($id)
    {
        $this->uniqueId = $id;
        return $this;
    }

    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    /**
     * Get database fields
     * @return array
     */
    public function getDatabaseFields()
    {
        return $this->databaseFields;
    }

    /**
     * Get importable database fields
     * @return array
     */
    protected function getImportableDatabaseFields()
    {
        if (!property_exists($this, 'notImportableFields')) {
            return $this->databaseFields;
        }

        $fields = [];

        foreach ($this->databaseFields as $field) {
            if (in_array($field, $this->notImportableFields)) {
                continue;
            }
            $fields[] = $field;
        }
        return $fields;
    }

    /**
     * Set custom fields that will be used for import
     * @param object $fields
     */
    public function setCustomFields($fields)
    {
        $this->customFields = $fields;

        return $this;
    }

    /**
     * Get custom fields
     * @return array
     */
    public function getCustomFields()
    {
        return $this->customFields;
    }

    /**
     * Set simulation
     * @param boolean $bool
     */
    public function setSimulation($bool)
    {
        $this->isSimulation = (bool) $bool;

        return $this;
    }

    /**
     * Check whether the request is simulation
     * @return boolean
     */
    public function isSimulation()
    {
        return (bool) $this->isSimulation;
    }

    /**
     * Get all stored simulation data that will be shown in table preview for simulation
     * @return array
     */
    public function getSimulationData()
    {
        return array_values($this->simulationData);
    }

    /**
     * Set the rows from the .csv file
     * @param array $rows
     */
    public function setRows($rows)
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * Get the rows stored from the .csv file
     * @return [type] [description]
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Get total rows
     * @return mixed
     */
    public function totalRows()
    {
        return $this->totalRows;
    }
	public function error_rows($error1){
		$this->error_rows = $error1;
		return $this;
	}
	public function get_error_rows(){
		return $this->error_rows;
	}
	public function clear_error_rows(){
		$this->error_rows = '';
		return $this;
	}
    public function setSkipRows($skipRows)
    {
        $this->skipRows = $skipRows;

        return $this;
    }

    /**
     * Get the rows stored from the .csv file
     * @return [type] [description]
     */
    public function getSkipRows()
    {
        return $this->skipRows;
    }

    /**
     * Sets temporary file location from the form ($_FILES)
     * @param string $location
     */
    public function setTemporaryFileLocation($location)
    {
        $this->temporaryFileFromFormLocation = $location;

        return $this;
    }

    /**
     * Get temporary file location
     * @return mixed
     */
    public function getTemporaryFileLocation()
    {
        return $this->temporaryFileFromFormLocation;
    }

    /**
     * Sets filename from the form ($_FILES)
     * @param string $name
     */
    public function setFilename($name)
    {
        $this->filename = $name;

        return $this;
    }

    /**
     * Get filename from the form ($_FILES)
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Increment the total imported number
     * @return object
     */
    public function incrementImported()
    {
        $this->totalImported++;

        return $this;
    }

    /**
     * Get total imported
     * @return mixed
     */
    public function totalImported()
    {
        return $this->totalImported;
    }

    /**
     * Get not importable fields
     * Child class should define property e.q. protected $notImportableFields = ['name'];
     * @return array
     */
    public function getNotImportableFields()
    {
        return property_exists($this, 'notImportableFields') ? $this->notImportableFields : [];
    }

    /**
     * Checks and show HTML warning for max_input_vars based on total rows
     * @return mixed
     */
    public function maxInputVarsWarningHtml()
    {
        $max_input = ini_get('max_input_vars');
        $totalRows = $this->totalRows;

        if (($max_input > 0 && !is_null($totalRows) && $totalRows >= $max_input)) {
            return "
            <div class=\"alert alert-warning\">
                Your hosting provider has PHP config <b>max_input_vars</b> set to $max_input.<br/>
                Ask your hosting provider to increase the <b>max_input_vars</b> config to $totalRows or higher in order to import the number of data you are trying to import otherwise try splitting the import .csv file rows and try to import less rows.
              </div>";
        }

        return '';
    }

    /**
     * Get HTML form for download sample .csv file
     * @return string x
     */
    public function downloadSampleFormHtml()
    {
        $form = '';
        $form .= form_open($this->ci->uri->uri_string());
        $form .= form_hidden('download_sample', 'true');
        $form .= '<button type="submit" class="btn btn-success">Download Sample</button>';
        $form .= '<hr />';
        $form .= form_close();

        return $form;
    }

    /**
     * General info for simulation data
     * @return string
     */
    public function simulationDataInfo()
    {
        return ' <h4 class="bold">Simulation Data <small class="text-info">Max ' . $this->maxSimulationRows . ' rows are shown</small></h4>
              <p class="bold">If you are satisfied with the results upload the file again and click import.</p>';
    }

    public function addImportGuidelinesInfo($text, $isImportant = false)
    {
        $this->importGuidelines[] = [
            'text'         => $text,
            'is_important' => $isImportant,
        ];
    }

    public function importGuidelinesInfoHtml()
    {
        $html = '<ul>';
        foreach (array_reverse($this->importGuidelines) as $key => $info) {
            $num = $key + 1;
            $html .= '<li class="' . ($info['is_important'] ? 'text-danger' : '') . '">' . $num . '. ' . $info['text'];
        }
        $html .= '</ul>';

        return $html;
    }

    public function checkValidCSV() {
        $totalSampleFields = 0;
                $dbFieldKeys       = [];
                foreach ($this->getImportableDatabaseFields() as $field) {
					if($this->formatFieldNameForHeading($field) == 'Deal owner'){
						$dbFieldKeys[] = 'Deal Owner Mail Id';
					}
					else if($this->formatFieldNameForHeading($field) == 'Activity assignedto'){
						$dbFieldKeys[] = 'Assigned Person Mail Id';
					}else if($this->formatFieldNameForHeading($field) == 'Deal stage'){
						$dbFieldKeys[] = 'Deal status';
					}
					else if($this->formatFieldNameForHeading($field) == 'Deal followers'){
						$dbFieldKeys[] = 'Deal follower mail id';
					}
					else{
						$dbFieldKeys[] = $this->formatFieldNameForHeading($field);
					}
                }
                foreach ($this->getCustomFields() as $field) {
                    $dbFieldKeys[] = $field['name'];
                }
                //pre($_FILES['file']);
                $f = fopen($_FILES['file']['tmp_name'], 'r');
                $firstLine = fgets($f); //get first line of csv file
                fclose($f); // close file    
//pre($dbFieldKeys); exit;
                $foundHeaders = array_filter(str_getcsv(trim($firstLine), ',', '"'));
  //   pre($foundHeaders);          exit;
				 $output = array_merge(array_diff($foundHeaders, $dbFieldKeys), array_diff($dbFieldKeys, $foundHeaders));
				 if(count($output) == 0) {
					return true;
				 } else {
					return 'Headers are not matching. Please check with corresponding sample data.';
					die();
				 }
	
                exit;
    }

    public function downloadSample() {
        // create file name
        $fileName = 'sample_import_file.xlsx';  
        // load excel library
        $CI =& get_instance();
        $CI->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        // set Header
              
        // set Row

        $totalSampleFields = 1;
        $dbFieldKeys       = [];
        $key = 'A';
        $identifycnt = 0;
       // $mandatory = array('deals_id','person_fullname','organization_name','deal_name','deal_deadline','deal_project_currency','deal_pipeline','deal_pipeline_stage','deal_owner','activity_name','activity_tasktype','activity_startdate','activity_assignedto');
	  // $fields1 = get_option('deal_mandatory');
	   $fields1 = get_option('deal_fields');
	   $mandatory_fields = array("deal_name");
	   if(!empty($fields1) && $fields1 != 'null'){
			$mandatory_fields1 = json_decode($fields1);
			array_unshift($mandatory_fields1,"deal_name");
			$mandatory_fields = $mandatory_fields1;
		}
       // $mandatory = array('deals_id','person_fullname','organization_name','deal_name','deal_start_date','deal_project_currency','deal_pipeline','deal_pipeline_stage','deal_owner','activity_name','activity_tasktype','activity_startdate','activity_assignedto','activity_priority');
		$mandatory = array('deals_id','deal_name','deal_project_currency','activity_name','activity_tasktype','activity_startdate','activity_priority');
		$i12 = 7;
		if (in_array("activity_assignedto", $mandatory_fields)){
			$mandatory[$i12] = 'activity_assignedto';
			$i12++;
		}
		if (in_array("description", $mandatory_fields)){
			$mandatory[$i12] = 'deal_description';
			$i12++;
		}
		if (in_array("status", $mandatory_fields)){
			$mandatory[$i12] = 'deal_status';
			$i12++;
		}
		if (in_array("pipeline_id", $mandatory_fields)){
			$mandatory[$i12] = 'deal_pipeline_id';
			$i12++;
		}
		if (in_array("clientid", $mandatory_fields)){
			$mandatory[$i12] = 'deal_clientid';
			$i12++;
		}
		if (in_array("teamleader", $mandatory_fields)){
			$mandatory[$i12] = 'deal_owner';
			$i12++;
		}
		if (in_array("project_start_date", $mandatory_fields)){
			$mandatory[$i12] = 'deal_start_date';
			$i12++;
		}
		if (in_array("project_deadline", $mandatory_fields)){
			$mandatory[$i12] = 'deal_deadline';
			$i12++;
		}
		if (in_array("project_cost", $mandatory_fields)){
			$mandatory[$i12] = 'deal_project_cost';
			$i12++;
		}
		if (in_array("project_members[]", $mandatory_fields)){
			$mandatory[$i12] = 'deal_followers';
			$i12++;
		}
		//pre($mandatory);exit;
        //pre($this->getImportableDatabaseFields());exit;

        $required_fields =get_option('deal_mandatory');
        if($required_fields && is_string($required_fields)){
            $required_fields = json_decode($required_fields);
        }
        $required_db_fields =array('deal_name','deal_owner');
        foreach($required_fields as $rfield){
            switch ($rfield) {
                case 'clientid':
                    $required_db_fields [] ='organization_name';
                    break;
                case 'pipeline_id':
                    $required_db_fields [] ='deal_pipeline';
                    break;
                case 'status':
                    $required_db_fields [] ='deal_pipeline_stage';
                    break;
                case 'project_members':
                    $required_db_fields [] ='deal_followers';
                    break;
                case 'project_cost':
                    $required_db_fields [] ='deal_project_cost';
                    break;
                case 'project_start_date':
                    $required_db_fields [] ='deal_start_date';
                    break;
                case 'project_deadline':
                    $required_db_fields [] ='deal_deadline';
                    break;
                case 'description':
                    $required_db_fields [] ='deal_description';
                    break;
                case 'project_contacts[]':
                case 'primary_contact':
                    $required_db_fields [] ='person_fullname';
                    break;
                default:
                    # code...
                    break;
            }
        }
        foreach ($this->getImportableDatabaseFields() as $field) {

            if($this->formatFieldNameForHeading($field) == 'Deal owner'){
                $objPHPExcel->getActiveSheet()->getCell($key.$totalSampleFields)->setValue('Deal Owner Mail Id');
            }
            else if($this->formatFieldNameForHeading($field) == 'Activity assignedto'){
                $objPHPExcel->getActiveSheet()->getCell($key.$totalSampleFields)->setValue('Assigned Person Mail Id');
            }else if($this->formatFieldNameForHeading($field) == 'Deal followers'){
                $objPHPExcel->getActiveSheet()->getCell($key.$totalSampleFields)->setValue('Deal follower mail id');
            }else if($this->formatFieldNameForHeading($field) == 'Deal stage'){
                $objPHPExcel->getActiveSheet()->getCell($key.$totalSampleFields)->setValue('Deal status');
            }else{
                $objPHPExcel->getActiveSheet()->getCell($key.$totalSampleFields)->setValue($this->formatFieldNameForHeading($field));
            }

            if(in_array($field, $required_db_fields)) {
                // $objPHPExcel->getActiveSheet()->SetCellValue($key.$totalSampleFields, $this->formatFieldNameForHeading($field));
                // $objPHPExcel->getActiveSheet()
                // ->getStyle($key.$totalSampleFields)
                // ->getFill()
                // ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                // ->getStartColor()
                // ->setRGB('#A52A2A');
                $styleArray = array(
                    'font'  => array(
                        'bold'  => true,
                        'color' => array('rgb' => 'fa0505')
                    ));
                $objPHPExcel->getActiveSheet()->getStyle($key.$totalSampleFields)->applyFromArray($styleArray);
            }
         
            //echo '"' . $this->formatFieldNameForHeading($field) . '",';
			
			$dbFieldKeys[$totalSampleFields] = $field;
            //$totalSampleFields++;
            $key++;
            $identifycnt++;
        }
        foreach ($this->getCustomFields() as $field) {
            $objPHPExcel->getActiveSheet()->SetCellValue($key.$totalSampleFields, $this->formatFieldNameForHeading($field['name']));
            
            //$totalSampleFields++;
            $key++;
            $identifycnt++;
        }

        $totalSampleRows = 1;
        if($_POST['download_contact_sample'] === 'true') {
            $sample_CSV = array('123','Stojanovic Goran','client@gmail.com','9787654567','Manager','IN','test','test','test');
        } else {
            $sample_CSV =array();
            foreach ($this->getImportableDatabaseFields() as $field) {
                switch($field){
                    case 'person_fullname':
                        $sample_CSV []='Stojanovic Goran';
                        break;
                    case 'person_email':
                        $sample_CSV []='client@gmail.com';
                        break;
                    case 'person_phonenumber':
                        $sample_CSV []='9898989898';
                        break;
                    case 'person_phone_country_code':
                        $sample_CSV []='IN';
                        break;
                    case 'person_position':
                        $sample_CSV []='Manager';
                        break;
                    case 'organization_name':
                        $sample_CSV []='Saneforce';
                        break;
                    case 'organization_phonenumber':
                        $sample_CSV []='9898989898';
                        break;
                    case 'organization_country':
                        $sample_CSV []='IND';
                        break;
                    case 'organization_city':
                        $sample_CSV []='Chennai';
                        break;
                    case 'organization_zip':
                        $sample_CSV []='600001';
                        break;
                    case 'organization_state':
                        $sample_CSV []='Tamilnadu';
                        break;
                    case 'organization_address':
                        $sample_CSV []='143, GJ street, kolmur';
                        break;
                    case 'organization_website':
                        $sample_CSV []='www.web.com';
                        break;
                    case 'deal_name':
                        $sample_CSV []='Deal 1907';
                        break;
                    case 'deal_description':
                        $sample_CSV []='Type Description';
                        break;
                    case 'deal_pipeline_stage':
                        $sample_CSV []='Hot';
                        break;
                    case 'deal_pipeline':
                        $sample_CSV []='Payroll';
                        break;
                    case 'deal_owner':
                        $sample_CSV []='seetha@saneforce.com';
                        break;
                    case 'deal_start_date':
                        $sample_CSV []='19-05-2021';
                        break;
                    case 'deal_deadline':
                        $sample_CSV []='30-05-2021';
                        break;
                    case 'deal_created_by':
                        $sample_CSV []='seetha@saneforce.com';
                        break;
                    case 'deal_project_modified':
                        $sample_CSV []='30-05-2021';
                        break;
                    case 'deal_modified_by':
                        $sample_CSV []='seetha@saneforce.com';
                        break;
                    case 'deal_project_cost':
                        $sample_CSV []='5000';
                        break;
                    case 'deal_stage':
                        $sample_CSV []='Won';
                        break;
                    case 'deal_project_currency':
                        $sample_CSV []='INR';
                        break;
                    case 'deal_lead_id':
                        $sample_CSV []='55';
                        break;
                    case 'deal_followers':
                        $sample_CSV []='emp1@saneforce.com,emp2@saneforce.com,emp3@saneforce.com';
                        break;
                    case 'activity_name':
                        $sample_CSV []='Type name';
                        break;
                    case 'activity_tasktype':
                        $sample_CSV []='Call';
                        break;
                    case 'activity_description':
                        $sample_CSV []='Type Description';
                        break;
                    case 'activity_priority':
                        $sample_CSV []='High';
                        break;
                    case 'activity_datemodified':
                        $sample_CSV []='30-05-2021';
                        break;
                    case 'activity_startdate':
                        $sample_CSV []='30-05-2021';
                        break;
                    case 'activity_send_reminder':
                        $sample_CSV []='yes';
                        break;
                    case 'activity_call_request_id':
                        $sample_CSV []='';
                        break;
                    case 'activity_call_code':
                        $sample_CSV []='';
                        break;
                    case 'activity_call_msg':
                        $sample_CSV []='';
                        break;
                    case 'activity_assignedto':
                        $sample_CSV []='';
                        break;

                    default:
                        $sample_CSV []='Data';
                        break;
                }
            }

            //added for custom fields
            foreach ($this->getCustomFields() as $field) {
                $sample_CSV []='Custom Data';
            }
        }
        
        $sampleCnt = count($sample_CSV);
        $remainCnt = $identifycnt - $sampleCnt;
        $custArray = [];

        for($i = 0; $i < $remainCnt; $i++) {
            $custArray[] = 'Custom Data';
        }
        $sample_CSV = array_merge($sample_CSV,$custArray);
       //pre($sample_CSV);
       //exit; 
        for ($row = 0; $row < $totalSampleRows; $row++) {
            $key = 'A';
            $rowCount = 2;
            for ($f = 0; $f < $identifycnt; $f++) {
                //$sampleDataText = $this->getTableRowDataText(isset($dbFieldKeys[$f]) ? $dbFieldKeys[$f] :  null);
                $objPHPExcel->getActiveSheet()->SetCellValue($key . $rowCount, $sample_CSV[$f]);
        //         echo $key.$rowCount;
        //  echo "<br>";
                $key++;
            }
        }
       // exit;
        $filename = "sample_import_file.xls";
        header('Content-Type: application/vnd.ms-excel'); 
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0'); 
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
        $objWriter->save('php://output'); 
        exit;
    }

    /**
     * Download sample .csv file
     * @return mixed
     */
    public function downloadSample1()
    {
        $totalSampleFields = 0;
        $dbFieldKeys       = [];
        header('Pragma: public');
        header('Expires: 0');
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="sample_import_file.csv";');
        header('Content-Transfer-Encoding: binary');
        foreach ($this->getImportableDatabaseFields() as $field) {
            echo '"' . $this->formatFieldNameForHeading($field) . '",';
            $dbFieldKeys[$totalSampleFields] = $field;
            $totalSampleFields++;
        }
        
        foreach ($this->getCustomFields() as $field) {
            echo '"' . $field['name'] . '",';
            $totalSampleFields++;
        }

        echo "\n";
        $totalSampleRows = 1;
        if($_POST['download_contact_sample'] === 'true') {
            $sample_CSV = array('123','Stojanovic Goran','client@gmail.com','9787654567','Manager');
        } else {
            $sample_CSV = array('Stojanovic Goran','client@gmail.com','9787654567','Manager','Saneforce','9898989898','India','Chennai','600001','Tamilnadu','143, GJ street, kolmur, chennai.','www.web.com','Deal 1907','Type Description','Hot','Payroll','seetha@saneforce.com','19-05-2021','30-05-2021','5000','Won','USD','emp1@saneforce.com,emp2@saneforce.com,emp3@saneforce.com','Call sundar','Call','Type Description','High','19-05-2021','yes','darious@saneforce.com');
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
            for ($f = 0; $f < $totalSampleFields; $f++) {
                //$sampleDataText = $this->getTableRowDataText(isset($dbFieldKeys[$f]) ? $dbFieldKeys[$f] :  null);

                echo '"' . $sample_CSV[$f] . '",';
            }

            // Is not last in for loop
            if ($row < $totalSampleRows - 1) {
                echo "\n";
            }
        }

        echo "\n";
        exit;
    }

    public function moveSkippedData($skipped)
    {
        $totalSampleFields = 1;
        $dbFieldKeys       = [];

        $data = '';
        $dbFields = $this->getImportableDatabaseFields();
        array_unshift($dbFields , 'Skip Reason');
        //pre($dbFields);
        // load excel library
        $CI =& get_instance();
        $CI->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        // set Header
              
        // set Row

        $key = 'A';
        $identifycnt = 0;
        $mandatory = array('deals_id','person_fullname','organization_name','deal_name','deal_deadline','deal_project_currency','deal_pipeline','deal_pipeline_stage','deal_owner','activity_name','activity_tasktype','activity_startdate','activity_assignedto');
        //pre($this->getImportableDatabaseFields());
        foreach ($dbFields as $field) {
            if(in_array($field, $mandatory)) {
                $styleArray = array(
                    'font'  => array(
                        'bold'  => true,
                        'color' => array('rgb' => 'fa0505')
                    ));
                
                $objPHPExcel->getActiveSheet()->getCell($key.$totalSampleFields)->setValue($this->formatFieldNameForHeading($field));
                $objPHPExcel->getActiveSheet()->getStyle($key.$totalSampleFields)->applyFromArray($styleArray);
            } else {
                if($field == 'Skip Reason') {
                    $styleArray = array(
                        'font'  => array(
                            'bold'  => true
                        ));
                    $objPHPExcel->getActiveSheet()->SetCellValue($key.$totalSampleFields, $field);
                    $objPHPExcel->getActiveSheet()->getStyle($key.$totalSampleFields)->applyFromArray($styleArray);
                } else {
                    $objPHPExcel->getActiveSheet()->SetCellValue($key.$totalSampleFields, $this->formatFieldNameForHeading($field));
                }
            }
         
            //echo '"' . $this->formatFieldNameForHeading($field) . '",';
            $dbFieldKeys[$totalSampleFields] = $field;
            //$totalSampleFields++;
            $key++;
            $identifycnt++;
        }

        foreach ($this->getCustomFields() as $field) {
            $objPHPExcel->getActiveSheet()->SetCellValue($key.$totalSampleFields, $this->formatFieldNameForHeading($field['name']));
            
            //$totalSampleFields++;
            $key++;
            $identifycnt++;
        }

        //pre($skipped);
        $rowCount = 2;
        foreach ($skipped as $field) {
            $key = 'A';
            for ($f = 0; $f < $identifycnt; $f++) {
                //$sampleDataText = $this->getTableRowDataText(isset($dbFieldKeys[$f]) ? $dbFieldKeys[$f] :  null);
                $objPHPExcel->getActiveSheet()->SetCellValue($key . $rowCount, $field[$f]);
        //         echo $key.$rowCount;
        //  echo "<br>";
                $key++;
            }
            $rowCount++;
        }
       // exit;
        $filename = "./uploads/import_files/".$this->uniqueId."/skipped_file.xls";
        header('Content-Type: application/vnd.ms-excel'); 
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0'); 
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
        $objWriter->save($filename); 
        return true;
        //exit;
    }


    public function moveSkippedData1($skipped)
    {
        $totalSampleFields = 0;
        $dbFieldKeys       = [];

        // header('Pragma: public');
        // header('Expires: 0');
        // header('Content-Type: application/csv');
        // header('Content-Disposition: attachment; filename="Skip file.csv";');
        // header('Content-Transfer-Encoding: binary');
        $data = '';
        $dbFields = $this->getImportableDatabaseFields();
        array_unshift($dbFields , 'Skip Reason');
        foreach ($dbFields as $field) {
            //echo '"' . $this->formatFieldNameForHeading($field) . '",';
            $data .= '"' . $this->formatFieldNameForHeading($field) . '",';
            $dbFieldKeys[$totalSampleFields] = $field;
            $totalSampleFields++;
        }
        foreach ($this->getCustomFields() as $field) {
            //echo '"' . $field['name'] . '",';
            $data .= '"' . $field['name'] . '",';
            $totalSampleFields++;
        }

        //echo "\n";
        $data .= "\n";

        foreach ($skipped as $field) {
            for ($f = 0; $f < $totalSampleFields; $f++) {
                $sampleDataText = $field[$f];

                //echo '"' . $sampleDataText . '",';
                $data .= '"' . $sampleDataText . '",';
            }
                //echo "\n";
                $data .= "\n";
        }
        //echo "\n";
        $data .= "\n";

        //$file = 'Skip file.csv';
        $target_file = './uploads/import_files/'.$this->uniqueId.'/Skip file.csv';
        // Open/Create the file
        $csv_handler = fopen ($target_file,'w');
        fwrite ($csv_handler,$data);
        fclose ($csv_handler);
        return true;
        //exit;
    }

    /**
     * Create sample table for sample data and simulation table results
     * @param  boolean $simulation where the table data should be taken from simultion data
     * @return string
     */
    public function createSampleTableHtml($simulation = false)
    {
        $totalFields = 0;
        $allFields   = [];
        $dbFieldKeys = [];

        $table = '<div class="table-responsive no-dt">';
        $table .= '<table class="table table-hover table-bordered">';
        $table .= '<thead>';
        $table .= '<tr>';

        foreach ($this->getImportableDatabaseFields() as $key => $field) {
            array_push($allFields, $field);
            $dbFieldKeys[$key] = $field;

            $table .= '<th class="bold database_field_' . $field . '">';
            if (in_array($field, $this->getRequiredFields())) {
                $table .= '<span class="text-danger">*</span> ';
            }
            $table .= $this->formatFieldNameForHeading($field);

            // Only for database fields
            if (method_exists($this, 'afterSampleTableHeadingText')) {
                $table .= $this->afterSampleTableHeadingText($field);
            }
            $table .= '</th>';
        }

        foreach ($this->getCustomFields() as $field) {
            array_push($allFields, $field['name']);
            $table .= '<th class="bold custom_field_' . $field['id'] . '">';
            $table .= $field['name'];
            $table .= '</th>';
        }

        $totalFields = count($allFields);

        $table .= '</tr>';
        $table .= '</thead>';
        $table .= '<tbody>';

        if ($simulation == false) {
            for ($i = 0; $i < 1; $i++) {
                $table .= '<tr>';
                for ($x = 0; $x < $totalFields; $x++) {
                    $sampleDataText = $this->getTableRowDataText(isset($dbFieldKeys[$x]) ? $dbFieldKeys[$x] :  null);

                    $table .= '<td>' . $sampleDataText . '</td>';
                }
                $table .= '</tr>';
            }
        } else {
            $simulationData = $this->getSimulationData();

            $totalSimulationRows = count($simulationData);
            for ($i = 0; $i < $totalSimulationRows; $i++) {
                $table .= '<tr>';
                for ($x = 0; $x < $totalFields; $x++) {
                    if (!isset($simulationData[$i][$allFields[$x]])) {
                        $table .= '<td>/</td>';
                    } else {
                        $table .= '<td>' . $simulationData[$i][$allFields[$x]] . '</td>';
                    }
                }
                $table .= '</tr>';
            }
        }
        $table .= '</tbody>';
        $table .= '</table>';
        $table .= '</div>';

        return $table;
    }

    /**
     * Get required import fields
     * Child class should define property e.q. protected $requiredFields = ['name'];
     * @return array
     */
    public function getRequiredFields()
    {
        return property_exists($this, 'requiredFields') ? $this->requiredFields : [];
    }

    /**
     * This is the main function that will initialize the import before parsing all data
     * **** IMPORTANT ***** The child class must call this method inside the perform method
     * @return object
     */
    protected function initialize()
    {
        if (empty($this->temporaryFileFromFormLocation)) {
            set_alert('warning', _l('import_upload_failed'));
            redirect($this->failureRedirectURL());
        }

        $tmpDir = $this->appTmpFolder . '/' . time() . uniqid() . '/';

        $this->maybeCreateDir($this->appTmpFolder);

        $this->maybeCreateDir($tmpDir);

        $this->tmpDir = $tmpDir;

        $this->moveUploadedFile();

        $this->readFileRows();

        return $this;
    }

    /**
     * Format field name
     * @param  string $fieldName field name, if passed will check for custom row data formatter in child class
     * @return string
     */
    private function getTableRowDataText($fieldName = null)
    {
        if (!$fieldName) {
            return $this->sampleDataText;
        }

        $customFormatSampleDataMethod = $fieldName . '_formatSampleData';
        // Only for database fields
        if (method_exists($this, $customFormatSampleDataMethod)) {
            return$this->{$customFormatSampleDataMethod}();
        }

        return $this->sampleDataText;
    }

    /**
     * Create dir if the dir do not exists
     * @param  string $path the dir/path where to create
     * @return boolena
     */
    private function maybeCreateDir($path)
    {
        if (!file_exists($path)) {
            return mkdir($path, 0755);
        }

        return false;
    }

    /**
     * Move the uploaded file into the corresponding temporary directory
     * @return boolean
     */
    private function moveUploadedFile()
    {
        $newFilePath = $this->tmpDir . $this->filename;

        if (move_uploaded_file($this->temporaryFileFromFormLocation, $newFilePath)) {
            $this->tmpFileStoragePath = $newFilePath;

            return true;
        }

        return false;
    }

    /**
     * Read the rows and store them into $rows
     * @return mixed
     */
    protected function readFileRows()
    {
        $fd   = fopen($this->tmpFileStoragePath, 'r');
        $rows = [];
        while ($row = fgetcsv($fd)) {
            $rows[] = $row;
        }

        fclose($fd);

        $this->totalRows = count($rows);

        if ($this->totalRows <= 1) {
            set_alert('warning', 'Not enought rows for importing');
            redirect($this->failureRedirectURL());
        }

        unset($rows[0]);

        $this->setRows($rows);

        if ($this->isSimulation() && $this->totalRows > $this->warningOnTotalRows) {
            $warningMsg = 'Recommended splitting the CSV file into smaller files. Our recomendation is ' . $this->warningOnTotalRows . ' row, your CSV file has ' . $this->totalRows;

            set_alert('warning', $warningMsg);
        }

        return $this;
    }

    /**
     * Some users enter in the .csv rows data e.q. NULL or null
     * To prevent storing this as string in database we shoul make the value empty
     * This is useful too when checking for required fields
     * @param  string $val
     * @return mixed
     */
    protected function checkNullValueAddedByUser($val)
    {
        if ($val === 'NULL' || $val === 'null') {
            $val = '';
        }

        return $val;
    }

    /**
     * Trim the values before inserting
     * @param  array $insert
     * @return array
     */
    public function trimInsertValues($insert)
    {
        foreach ($insert as $key => $val) {
            $insert[$key] = !is_null($val) ? trim($val) : $val;
        }
        return $insert;
    }

    /**
     * Function responsible to store the import custom fields
     * @param  mixed $rel_id        the ID e.q. lead_id or item_id
     * @param  array $row           the actual row from the loop in the child class
     * @param  mixed &$fieldNumber  field number
     * @param  mixed $rowNumber     the row number, used for simulation data
     * @param  string $customFieldTo where this custom fields belongs
     * @return null
     */
    protected function handleCustomFieldsInsert($rel_id, $row, &$fieldNumber, $rowNumber, $customFieldTo)
    {
        foreach ($this->getCustomFields() as $field) {
            if ($this->isSimulation()) {
                $this->simulationData[$rowNumber][$field['name']] = $row[$fieldNumber];
                $fieldNumber++;

                continue;
            }

            if ($row[$fieldNumber] != '' && $row[$fieldNumber] !== 'NULL' && $row[$fieldNumber] !== 'null') {
                $customFieldData = [
                                        'relid'   => $rel_id,
                                        'fieldid' => $field['id'],
                                        'value'   => trim($row[$fieldNumber]),
                                        'fieldto' => $customFieldTo,
                                    ];
                $this->ci->db->insert(db_prefix().'customfieldsvalues', $customFieldData);
            }
            $fieldNumber++;
        }
    }

    private function setDefaultImportGuidelinesInfo()
    {
        //$this->addImportGuidelinesInfo('If the column <b>you are trying to import is date make sure that is formatted in format Y-m-d (' . date('Y-m-d') . ').</b>');

        $this->addImportGuidelinesInfo('Your CSV data should be in the format below. The first line of your CSV file should be the column headers as in the table example. Also make sure that your file is <b>UTF-8</b> to avoid unnecessary <b>encoding problems</b>.');
    }

    /**
     * Clear the temporary dir if exists while moved the uploaded file
     */
    public function __destruct()
    {
        if (!is_null($this->tmpDir) && is_dir($this->tmpDir)) {
            @delete_dir($this->tmpDir);
        }
    }
}
