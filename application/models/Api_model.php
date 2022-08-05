<?php

class Api_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

    /**
     * API RESPONSE
     * @param  string http response code
     * @param  boolean request success or not
     * @param  array response data
     * @param  string success or error message
     */
	public function response($status_code, $success,$data,$message)
    {
        header('Content-type: application/json');
        http_response_code($status_code);
        $response =[
            'success'=>$success,
            'data'=>$data,
            'message'=>$message,
        ];
        echo json_encode($response);
        die;
    }

    /**
     * API RESPONSE HTTP OK
     * @param  boolean request success or not
     * @param  array response data
     * @param  string success or error message
     */
    public function response_ok($success,$data,$message)
    {
        $this->response(200,$success,$data,$message);
    }

    /**
     * API RESPONSE HTTP NO CONTENT
     * @param  boolean request success or not
     * @param  array response data
     * @param  string success or error message
     */
    public function response_no_content($success,$data,$message)
    {
        $this->response(204,$success,$data,$message);
    }

    /**
     * API RESPONSE HTTP BAD REQUEST
     * @param  boolean request success or not
     * @param  array response data
     * @param  string success or error message
     */
    public function response_bad_request($success,$data,$message)
    {
        $this->response(400,$success,$data,$message);
    }

    /**
     * API RESPONSE HTTP UNAUTHORIZED
     * @param  boolean request success or not
     * @param  array response data
     * @param  string success or error message
     */
    public function response_unauthorized($success,$data,$message)
    {
        $this->response(401,$success,$data,$message);
    }
}


?>