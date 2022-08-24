<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('authorization');
		$this->load->model('base');
		$this->load->model('dbcreations');
	}

/**
 * Company list
**/
	public function company()
	{
		$data = array();
		$data['pagetitle'] = 'Company';
		$loggeduserid = $this->session->userdata('loggeduserid');
		if(!empty($loggeduserid)) {
			$admins = $this->base->getAll('tblsuperadmin', array('id' => $loggeduserid));
			$data['admins'] = $admins[0];
		} else {
			redirect(site_url('superadmin/login'));
			exit;
		}
		
		$this->load->view("superadmin/includes/header", $data);
		$this->load->view("superadmin/settings/company", $data);
		$this->load->view("superadmin/includes/footer");
	}

/**
 * Store list ajax
**/
	public function companyView()
	{
		$data = array();
		$requestData= $_REQUEST;
		$company_sql = $this->base->executeQuery('SELECT id,name,shortcode,email,phone,password,address,demodata,status,created_on,created_by,updated_on,updated_by FROM tblcompany ORDER BY id ASC');
		$company_res = $company_sql->result_array();
		$company_count = count($company_res);
		$columns = array( 
			0 => 'id',
			1 => 'name',
			2 => 'status'
		);
		if( !empty($requestData['search']['value']) )
		{
			$query  = "SELECT id,name,shortcode,email,phone,password,address,demodata,status,created_on,created_by,updated_on,updated_by FROM tblcompany WHERE (id LIKE '".$requestData['search']['value']."%'";
			$query .= " OR name LIKE '%".$requestData['search']['value']."%'";
			$query .= " OR status LIKE '%".$requestData['search']['value']."%')";
			$query .= " ORDER BY id ASC";
			$query .= " LIMIT ".$requestData['start'].",".$requestData['length']."";
			$company_sql = $this->base->executeQuery($query);
			$companys = $company_sql->result_array();
			$company_count = count($companys);
		}
		else {
			$company_sql = $this->base->executeQuery("SELECT id,name,shortcode,email,phone,password,address,demodata,status,created_on,created_by,updated_on,updated_by FROM tblcompany ORDER BY id ASC LIMIT ".$requestData['start'].",".$requestData['length']."");
			$companys = $company_sql->result_array();
		}
		$i = $requestData['start']+1;
		foreach($companys as $company)
		{
			$nestedData = array();
			$nestedData[] = $company["name"];
			$nestedData[] = $company["shortcode"];
			$nestedData[] = $company["phone"];
			$nestedData[] = $company["email"];
			$nestedData[] = $company["status"];
			$confirm = "return confirm('Are you sure want to take database backup of this company?')";
			$nestedData[] = '<a title="Edit" href="'.site_url('superadmin/editcompany').'/'.$company["id"].'"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>    <a onclick="'.$confirm.'" title="Download" href="'.site_url('superadmin/downloadcompany').'/'.$company["shortcode"].'"><i class="fa fa-download" aria-hidden="true"></i></a>';
			$data[] = $nestedData;
			$i++;
		}
		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),
			"recordsTotal"    => intval( $company_res ),
			"recordsFiltered" => intval( $company_count ),
			"data"            => $data
		);
		echo json_encode($json_data);
	}

/**
 * Add company
**/
	public function addcompany()
	{
		//echo "<pre>"; print_r($fields['demodata']); exit;
		$data = array();
		$data['pagetitle'] = 'Add Company';
		$loggeduserid = $this->session->userdata('loggeduserid');
		if(!empty($loggeduserid)) {
			$admins = $this->base->getAll('tblsuperadmin', array('id' => $loggeduserid));
			$data['admins'] = $admins[0];
		} else {
			redirect(site_url('superadmin/login'));
		}
		
		$fields = $_POST;
		if (!empty($fields))
		{
			$this->base->getAll('tblsuperadmin', array('id' => $loggeduserid));
			$fields['created_by'] = $loggeduserid;
			$this->base->getAll('tblsuperadmin', array('id' => $loggeduserid));
			$slug = strtolower($fields['shortcode']);
			$slug_exist = $this->base->executeQuery('SELECT id,name,shortcode,email,phone,password,address,demodata,status,created_on,created_by,updated_on,updated_by FROM tblcompany where shortcode = "'.$slug.'"');
			$slug_res = $slug_exist->result_array();
			$slug_count = count($slug_res);
			
			if($slug_count > 0) {
				$this->session->set_flashdata('error_msg','Shortcode Already Exist');
				echo "addcompany";
				exit;
				//redirect(site_url('superadmin/addcompany'));
			} else {
				//$fields['password'] = $this->rand_string(8);
				$fields['password'] = 'admin@123';
			//Create database and Import data's
			//pre($fields);
				$this->dbcreations->createDB($fields);
				$this->base->insert('tblcompany', $fields);
				if($this->sendEmail($fields)) {
					$this->session->set_flashdata('success_msg','Company Details Added Successfully');
					echo "company";
					exit;
					//redirect(site_url('superadmin/company'));
				}
			}
		}
		
		$this->load->view("superadmin/includes/header", $data);
		$this->load->view("superadmin/settings/addcompany", $data);
		$this->load->view("superadmin/includes/footer");
	}
	
	function rand_string( $length ) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		return substr(str_shuffle($chars),0,$length);
	}
	
	

/**
 * Edit company
**/
	public function editcompany()
	{
		$data = array();
		$data['pagetitle'] = 'Edit Company';
		$id = $this->uri->segment(3);
		$loggeduserid = $this->session->userdata('loggeduserid');
		if(!empty($loggeduserid)) {
			$admins = $this->base->getAll('tblsuperadmin', array('id' => $loggeduserid));
			$data['admins'] = $admins[0];
		} else {
			redirect(site_url('superadmin/login'));
		}
		
		$company_query = $this->base->executeQuery("SELECT id,name,shortcode,email,phone,password,address,demodata,status,created_on,created_by,updated_on,updated_by FROM tblcompany WHERE id = ".$id);
		$companydetails = $company_query->row_array();
		$data['companydetails'] = $companydetails;
		
		$fields = $_POST;
		if (!empty($fields))
		{
			
			$fields['updated_by'] = $loggeduserid;
			$where = array('id' => $id);
			$this->base->update('tblcompany', $fields, $where);
			
			$this->session->set_flashdata('success_msg','Company Details Updated Successfully');
			echo 'company';
			exit;
			redirect(site_url('superadmin/company'));
		}
		
		$this->load->view("superadmin/includes/header", $data);
		$this->load->view("superadmin/settings/editcompany", $data);
		$this->load->view("superadmin/includes/footer");
	}

/**
 * Delete company
**/
	public function deletecompany()
	{
		$data = array();
		$data['pagetitle'] = 'Delete Company';
		$id = $this->uri->segment(3);
		$loggeduserid = $this->session->userdata('loggeduserid');
		if(!empty($loggeduserid)) {
			$admins = $this->base->getAll('tblsuperadmin', array('id' => $loggeduserid));
			$data['admins'] = $admins[0];
		}
		
		$this->base->executeQuery("DELETE FROM tblcompany WHERE id = '$id'");
		$this->session->set_flashdata('success_msg','Company Details Deleted Successfully');
		redirect(site_url('superadmin/company'));
	}

	/**
	 * Delete company
	**/
	public function downloadcompany()
	{
		$data = array();
		$data['pagetitle'] = 'Download Company';
		$shortcode = $this->uri->segment(3);
		
		$dbhost = "localhost";
		$dbuser = "root";
		$dbpass = "";
		$dbname = "perfexcrm";
		$this->export_database($dbhost,$dbuser,$dbpass,$dbname,  $tables=false, $shortcode);

		$this->session->set_flashdata('success_msg','Database Backup Downloaded Successfully');
		redirect(site_url('superadmin/company'));
	}

   function export_database($host,$user,$pass,$name,  $tables=false, $backup_name)
    {
        $mysqli = new mysqli($host,$user,$pass,$name); 
        $mysqli->select_db($name); 
        $mysqli->query("SET NAMES 'utf8'");

		$queryTables    = $mysqli->query('SHOW TABLES'); 
		
        while($row = $queryTables->fetch_row()) 
        { 
            $target_tables[] = $row[0]; 
        }   
        if($tables !== false) 
        { 
            $target_tables = array_intersect( $target_tables, $tables); 
        }
        foreach($target_tables as $table)
        {
            $result         =   $mysqli->query('SELECT * FROM '.$table);  
            $fields_amount  =   $result->field_count;  
            $rows_num=$mysqli->affected_rows;     
            $res            =   $mysqli->query('SHOW CREATE TABLE '.$table); 
            $TableMLine     =   $res->fetch_row();
            $content        = (!isset($content) ?  '' : $content) . "\n\n".$TableMLine[1].";\n\n";

            for ($i = 0, $st_counter = 0; $i < $fields_amount;   $i++, $st_counter=0) 
            {
                while($row = $result->fetch_row())  
                { //when started (and every after 100 command cycle):
                    if ($st_counter%100 == 0 || $st_counter == 0 )  
                    {
                            $content .= "\nINSERT INTO ".$table." VALUES";
                    }
                    $content .= "\n(";
                    for($j=0; $j<$fields_amount; $j++)  
                    { 
                        $row[$j] = str_replace("\n","\\n", addslashes($row[$j]) ); 
                        if (isset($row[$j]))
                        {
                            $content .= '"'.$row[$j].'"' ; 
                        }
                        else 
                        {   
                            $content .= '""';
                        }     
                        if ($j<($fields_amount-1))
                        {
                                $content.= ',';
                        }      
                    }
                    $content .=")";
                    //every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
                    if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num) 
                    {   
                        $content .= ";";
                    } 
                    else 
                    {
                        $content .= ",";
                    } 
                    $st_counter=$st_counter+1;
                }
            } $content .="\n\n\n";
        }
        //$backup_name = $backup_name ? $backup_name : $name."___(".date('H-i-s')."_".date('d-m-Y').")__rand".rand(1,11111111).".sql";
        $backup_name = $backup_name.".sql";
        header('Content-Type: application/octet-stream');   
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"".$backup_name."\"");  
		echo $content;
		return 1;
		exit;
	}

	/**
	 * Send E-Mail
	**/
	public function sendEmail($postdata)
	{
		//echo "<pre>"; print_r($postdata); exit;
		if(!empty($postdata))
		{
			$username = $postdata['name'];
			$shortcode = $postdata['shortcode'];
			$email = $postdata['email'];
			$password = $postdata['password'];
			$subject = 'LEADFORCE Company Creation';
			$content = 'Dear '.$username.'<br/>
			Thanks for connecting with us,<br/>
			Your account has been created successfully.<br/>
			Company Code : '.$shortcode.'<br>
			Admin Login Email-id : '.$email.'<br>
			Password : '.$password.'<br>
			Thank You.<br/>';

			$this->load->library('email');
			$smtpconf = array();
			$smtpsettings = $this->db->get(db_prefix() . 'options')->result_array();
			foreach($smtpsettings as $config) {
				if($config['name'] == 'smtp_host')
					$smtpconf['host'] = $config['value'];
				if($config['name'] == 'smtp_encryption')
					$smtpconf['encrypto'] = $config['value'];
				if($config['name'] == 'smtp_username')
					$smtpconf['username'] = $config['value'];
				if($config['name'] == 'smtp_password')
					$smtpconf['password'] = $config['value'];
				if($config['name'] == 'smtp_port')
					$smtpconf['port'] = $config['value'];
			}
			$smtpconf['validate'] = true;
			// $config = array(
			//     'protocol'     => smtp_protocol,
			//     'smtp_host'     => smtp_host,
			//     'smtp_port'     => smtp_port,
			//     'smtp_user' => smtp_user,
			//     'smtp_pass'     => smtp_pass,
			//     'charset'     => smtp_charset,
			//     'mailtype'     => smtp_mailtype,
			//     'newline' => smtp_newline
			// );

			$this->email->initialize($smtpconf);

			$this->email->from($smtpconf['username'], $subject);
			$this->email->to($email);
			$this->email->subject($subject);
			$this->email->message($content);
			if ($this->email->send()) {
					$data['msg'] = 'Message has been sent';
					// $data['msg'] = $mail->ErrorInfo;
				//	to_error($data);
				}
				else {
					//to_response('Message has been sent');
					$data['msg'] = 'There is problem in sending mail, please contact administrators';
				}
				return $data;
		}
		else {
			$data['msg'] = 'Invalid request';
			//to_error($data);
		}
	}


}