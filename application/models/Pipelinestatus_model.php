<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pipelinestatus_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

/**
 * Get Pipelin-estatuss
**/
	public function getPipelinestatuss()
    {
		$this->db->where('status', 'Active');
        return $this->db->get(db_prefix() . 'projects_status')->result_array();
    }
	
/**
 * Check Pipelin-estatus exist
**/
	public function checkPipelinestatusexist($name)
    {
		$this->db->where('LOWER(name)', strtolower($name));
        return $this->db->get(db_prefix() . 'projects_status')->row();
    }
	
/**
 * View Pipelin-estatus
**/
	public function getPipelinestatus($id)
    {
		$this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'projects_status')->row();
    }
	
/** 
 * Add Pipelin-estatus
**/
    public function addPipelinestatus($data)
    {
        $data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by']  = get_staff_user_id();
        $this->db->insert(db_prefix() . 'projects_status', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Pipelin-estatus added [Pipelin-estatus-ID: '.$insert_id.']');
        }
        return $insert_id;
    }

/**
 * Update Pipelin-estatus
**/
    public function updatePipelinestatus($data, $id)
    {
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by']  = get_staff_user_id();
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'projects_status', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Pipelin-estatus Updated [Pipelin-estatus-ID: ' . $id . ']');
            return true;
        }
        return false;
    }
	
/**
 * Delete Pipelinestatus
**/
    public function deletePipelinestatus()
    {
        if($_POST['delete'] == 'delete') {
            $this->db->where('id', $_POST['id']);
            $this->db->delete(db_prefix() . 'projects_status');
            if ($this->db->affected_rows() > 0) {
                log_activity('Pipeline-status Deleted [Pipeline-status-ID: ' . $_POST['id'] . ']');
                return true;
            }
        } else {
            if($_POST['selected_option'] == 'delete') {
                $data['deleted_status'] = 1;
                $this->db->where('status', $_POST['id']);
                $this->db->update(db_prefix() . 'projects', $data);
                if ($this->db->affected_rows() > 0) {
                    $this->db->where('id', $_POST['id']);
                    $this->db->delete(db_prefix() . 'projects_status');
                    if ($this->db->affected_rows() > 0) {
                        log_activity('Pipeline-status Deleted [Pipeline-status-ID: ' . $_POST['id'] . ']');
                        return true;
                    }
                }
            } else {
                $data['status'] = $_POST['pipeline_status'];
                $this->db->where('status', $_POST['id']);
                $this->db->update(db_prefix() . 'projects', $data);
                if ($this->db->affected_rows() > 0) {
                    $this->db->where('id', $_POST['id']);
                    $this->db->delete(db_prefix() . 'projects_status');
                    if ($this->db->affected_rows() > 0) {
                        log_activity('Pipeline-status Deleted [Pipeline-status-ID: ' . $_POST['id'] . ']');
                        return true;
                    }
                }
            }
        }
        
        /*
        $this->db->where('id', $project_id);
        $this->db->delete(db_prefix() . 'projects');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('project_id', $project_id);
            $this->db->delete(db_prefix() . 'project_members');

            $this->db->where('project_id', $project_id);
            $this->db->delete(db_prefix() . 'project_notes');

            $this->db->where('project_id', $project_id);
            $this->db->delete(db_prefix() . 'milestones');

            // Delete the custom field values
            $this->db->where('relid', $project_id);
            $this->db->where('fieldto', 'projects');
            $this->db->delete(db_prefix() . 'customfieldsvalues');

            $this->db->where('rel_id', $project_id);
            $this->db->where('rel_type', 'project');
            $this->db->delete(db_prefix() . 'taggables');


            $this->db->where('project_id', $project_id);
            $discussions = $this->db->get(db_prefix() . 'projectdiscussions')->result_array();
            foreach ($discussions as $discussion) {
                $discussion_comments = $this->get_discussion_comments($discussion['id'], 'regular');
                foreach ($discussion_comments as $comment) {
                    $this->delete_discussion_comment_attachment($comment['file_name'], $discussion['id']);
                }
                $this->db->where('discussion_id', $discussion['id']);
                $this->db->delete(db_prefix() . 'projectdiscussioncomments');
            }
            $this->db->where('project_id', $project_id);
            $this->db->delete(db_prefix() . 'projectdiscussions');

            $files = $this->get_files($project_id);
            foreach ($files as $file) {
                $this->remove_file($file['id']);
            }

            $tasks = $this->get_tasks($project_id);
            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id'], false);
            }

            $this->db->where('project_id', $project_id);
            $this->db->delete(db_prefix() . 'project_settings');

            $this->db->where('project_id', $project_id);
            $this->db->delete(db_prefix() . 'project_activity');

            $this->db->where('project_id', $project_id);
            $this->db->update(db_prefix() . 'expenses', [
                'project_id' => 0,
            ]);

            $this->db->where('project_id', $project_id);
            $this->db->update(db_prefix() . 'invoices', [
                'project_id' => 0,
            ]);

            $this->db->where('project_id', $project_id);
            $this->db->update(db_prefix() . 'creditnotes', [
                'project_id' => 0,
            ]);

            $this->db->where('project_id', $project_id);
            $this->db->update(db_prefix() . 'estimates', [
                'project_id' => 0,
            ]);

            $this->db->where('project_id', $project_id);
            $this->db->update(db_prefix() . 'tickets', [
                'project_id' => 0,
            ]);

            $this->db->where('project_id', $project_id);
            $this->db->delete(db_prefix() . 'pinned_projects');

            log_activity('Project Deleted [ID: ' . $project_id . ', Name: ' . $project_name . ']');

            return true;
        }*/
        
        return false;
    }

    public function getpipelinedealstatus($id)
    {
        $this->db->where('status', $id);
        $deals = $this->db->get(db_prefix() . 'projects')->result_array();
        $data = array();
        $cnt = count($deals);
        $this->db->where('id', $id);
        $pipeline_name = $this->db->get(db_prefix() . 'projects_status')->row();
        $data['name'] = $pipeline_name->name;
        if($cnt > 0) {
            $data['count'] = $cnt;
            $this->db->where('id !=', $id);
            $html = '';
            $pipelines = $this->db->get(db_prefix() . 'projects_status')->result_array();
            foreach($pipelines as $val) {
                $html .= '<option value="'.$val['id'].'">'.$val['name'].'</option>';
            }
            $data['pipelines'] = $html;
        } else {
            $data['count'] = 0;
        }
        return $data;
    }
}