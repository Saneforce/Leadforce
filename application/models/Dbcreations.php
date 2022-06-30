<?php

class Dbcreations extends CI_Model
{
	
	public function __construct()
	{
        parent::__construct();
		$this->load->model('base');
    }

	public function createDB($data) { 
		$uniqueDBname = APP_DB_NAME.'_'.$data['shortcode'];
		$this->createTenancyDatabase($uniqueDBname);
		//Dynamic DB Connection
		$this->dynamicDB = array(
			'hostname' => APP_DB_HOSTNAME,
			'username' => APP_DB_USERNAME,
			'password' => APP_DB_PASSWORD,
			'database' => $uniqueDBname,
			'dbdriver' => 'mysqli',
			'dbprefix' => 'tbl',
			'pconnect' => FALSE,
			'db_debug' => TRUE,
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci'
		);
		$this->db2 = $this->load->database($this->dynamicDB, TRUE); 
		$getTableQuery = 'SELECT table_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = "'. APP_DB_NAME . '" GROUP by table_name ';
		$getTableRes = $this->base->executeQuery($getTableQuery);
		$listTables = $getTableRes->result_array();
		
		foreach ($listTables as $tnkey => $tnvalue) {
			//pre($tnvalue);
			$createTableRes = $this->base->executeQuery('SHOW CREATE TABLE '.$tnvalue['TABLE_NAME']);
			$tableRes = $createTableRes->result_array();
			
			if (isset($tableRes[0]['Create Table'])) {
				$tblQuery = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $tableRes[0]['Create Table']);
			}
			$this->db2->query($tblQuery);
		}
		$data_for_table = DBT_BASETABLE;
		if (isset($data['demodata']) && $data['demodata'] == 'on') {
			$data_for_table = array_merge($data_for_table, DBT_DATATABLE);
		} else {
			$curdate = date("Y-m-d H:i:s");
			$createTableRes = $this->db2->query("INSERT INTO `tblpipeline` (`name`,`teamleader`,`teammembers`,`status`, `clientid`, `publishstatus`, `created_date`, `created_by`, `updated_date`, `updated_by`) VALUES ('Test','','','', '0', '1', '".$curdate."', '1', '".$curdate."', '1')");
			$this->db2->query("ALTER TABLE tblstaff AUTO_INCREMENT=1");
		}
		//pre($data_for_table);
		foreach ($data_for_table as $key => $value) {
			//created_date
		// Get table data from default
			foreach ($this->db->get($value)->result_array() as $tableRecords) {
				if (array_key_exists("created_date",$tableRecords)){
					$curdate1 = date("Y-m-d H:i:s");
					$tableRecords['created_date'] = $curdate1;
				}
				if (array_key_exists("updated_date",$tableRecords)){
					$curdate1 = date("Y-m-d H:i:s");
					$tableRecords['updated_date'] = $curdate1;
				}
			// Save data to tenancy database - default db connection
				$this->db2->insert($value, $tableRecords);
			}
			//echo "<pre>"; print_r($data_for_table); exit;
		}
		$adminData = array();
		$adminData['firstname'] = $data['name'];
		$adminData['email'] = $data['email'];
		$adminData['password'] = app_hash_password($data['password']);
		$adminData['phonenumber'] = $data['phone'];
		$adminData['admin'] = 1;
		$adminData['role'] = 1;
		$adminData['action_for'] = 'Active';
		$adminData['active'] = 1;
		$adminData['default_language'] = 'english';
		
		$this->db2->insert('staff', $adminData);
		return true;
	}

	public function createTenancyDatabase($dbname) {
        $this->load->dbforge();
		$this->dbforge->create_database($dbname);
		return true;
	}
	
}


?>