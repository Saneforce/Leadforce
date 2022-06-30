<?php defined('BASEPATH') or exit('No direct script access allowed');
$is_admin = is_admin();
$year = date('Y');
//pre($_SESSION['nav']);
if($_SESSION['forecast_intervel'] == 'month' && isset($_SESSION['nav'])) {
  
  if($_SESSION['nav'] == '1') {
    $diff = $_SESSION['startfrom'] + 1;
    if($diff > 12) {
        $year = $_SESSION['forcast_year']+1;
        $which = 1;
        $_SESSION['startfrom'] = $which;
        $_SESSION['forcast_year'] = $year;
        //pre($_SESSION);
    } else {
        $which = $diff;
        $_SESSION['startfrom'] = $which;
        $year = $_SESSION['forcast_year'];
    }
  }
  if($_SESSION['nav'] == '-1') {
    $diff = $_SESSION['startfrom'] - 1;
    //pre($_SESSION);
    if($diff == 0) {
        $year = $_SESSION['forcast_year']-1;
        $which = 12;
        $_SESSION['startfrom'] = $which;
        $_SESSION['forcast_year'] = $year;
    } else {
        $which = $diff;
        $_SESSION['startfrom'] = $which;
        $year = $_SESSION['forcast_year'];
    }
  }
  
  if($_SESSION['nav'] == 'forward') {
      $diff = $_SESSION['forecast_lastwhich'] + 1;
      if($diff > 12) {
          $year = $_SESSION['forecast_lastyear']+1;
          $which = 1;
          $_SESSION['startfrom'] = $which;
          $_SESSION['forcast_year'] = $year;
          //pre($_SESSION);
      } else {
          $which = $diff;
          $_SESSION['startfrom'] = $which;
          $year = $_SESSION['forcast_year'] = $_SESSION['forecast_lastyear'];
      }
  }

  if($_SESSION['nav'] == 'backward') {
    //pr($_SESSION);
      //$lastcnt = $_SESSION['startfrom'] - $_SESSION['forecast_column']; 
      
      $which = $_SESSION['startfrom'];
      for($i=0; $i<$_SESSION['forecast_column']; $i++) {
        $which = $which-1;
          if($which == 0) {
            $year = $_SESSION['forcast_year']-1;
            $which = 12;
            $_SESSION['startfrom'] = $which;
            $_SESSION['forcast_year'] = $year;
          } else {
              $_SESSION['startfrom'] = $which;
              $year = $_SESSION['forcast_year'];
          }
          
      }
      //pre($_SESSION);  
  }
  if($_SESSION['nav'] == 'start') {
      $which = date('m');
      $year = date('Y');
      $_SESSION['startfrom'] = $which;
      $_SESSION['forcast_year'] = $year;
  }

}

if($_SESSION['forecast_intervel'] == 'week' && isset($_SESSION['nav'])) {
  $date = strtotime("31 December $year");
  $weeks = gmdate("W", $date);
  if($weeks == '01') {
    $weeks = 52;
  }
  //pre($weeks);
  if($_SESSION['nav'] == '1') {
    $diff = $_SESSION['startfrom'] + 1;
    if($diff > $weeks) {
        $year = $_SESSION['forcast_year']+1;
        $which = 1;
        $_SESSION['startfrom'] = $which;
        $_SESSION['forcast_year'] = $year;
        //pre($_SESSION);
    } else {
        $which = $diff;
        $_SESSION['startfrom'] = $which;
        $year = $_SESSION['forcast_year'];
    }
  }

  if($_SESSION['nav'] == '-1') {
    $diff = $_SESSION['startfrom'] - 1;
    if($diff == 0) {
        $year = $_SESSION['forcast_year']-1;
        $date = strtotime("31 December $year");
        $weeks = gmdate("W", $date);
        if($weeks == '01') {
          $weeks = 52;
        }
        $which = $weeks;
        $_SESSION['startfrom'] = $which;
        $_SESSION['forcast_year'] = $year;
    } else {
        $which = $diff;
        $_SESSION['startfrom'] = $which;
        $year = $_SESSION['forcast_year'];
    }
  }
  if($_SESSION['nav'] == 'forward') {
      $diff = $_SESSION['forecast_lastwhich'] + 1;
      if($diff > $weeks) {
          $year = $_SESSION['forecast_lastyear']+1;
          $which = 1;
          $_SESSION['startfrom'] = $which;
          $_SESSION['forcast_year'] = $year;
          //pre($_SESSION);
      } else {
          $which = $diff;
          $_SESSION['startfrom'] = $which;
          $year = $_SESSION['forcast_year'] = $_SESSION['forecast_lastyear'];
      }
  }


  if($_SESSION['nav'] == 'backward') {
    //pr($_SESSION);
    //$lastcnt = $_SESSION['startfrom'] - $_SESSION['forecast_column']; 

    $which = $_SESSION['startfrom'];
    for($i=0; $i<$_SESSION['forecast_column']; $i++) {
      $which = $which-1;
        if($which == 0) {
            $year = $_SESSION['forcast_year']-1;
            $date = strtotime("31 December $year");
            $weeks = gmdate("W", $date);
            if($weeks == '01') {
              $weeks = 52;
            }
            $which = $weeks;
            $_SESSION['startfrom'] = $which;
            $_SESSION['forcast_year'] = $year;
        } else {
            $_SESSION['startfrom'] = $which;
            $year = $_SESSION['forcast_year'];
        }
        
    }
  //pre($_SESSION);  
  }

  if($_SESSION['nav'] == 'start') {
    $year = date('Y');
    $which = date('W');
    $_SESSION['startfrom'] = $which;
    $_SESSION['forcast_year'] = $year;
  }
}

if($_SESSION['forecast_intervel'] == 'quarter' && isset($_SESSION['nav'])) {
  
  if($_SESSION['nav'] == '1') {
    $diff = $_SESSION['startfrom'] + 1;
    
    if($diff > 4) {
        $year = $_SESSION['forcast_year']+1;
        $which = 1;
        $_SESSION['startfrom'] = $which;
        $_SESSION['forcast_year'] = $year;
        //pre($_SESSION);
    } else {
        $which = $diff;
        $_SESSION['startfrom'] = $which;
        $year = $_SESSION['forcast_year'];
    }
  }
  if($_SESSION['nav'] == '-1') {
    $diff = $_SESSION['startfrom'] - 1;
    //pre($_SESSION);
    if($diff == 0) {
        $year = $_SESSION['forcast_year']-1;
        $which = 4;
        $_SESSION['startfrom'] = $which;
        $_SESSION['forcast_year'] = $year;
    } else {
        $which = $diff;
        $_SESSION['startfrom'] = $which;
        $year = $_SESSION['forcast_year'];
    }
  }
  
  if($_SESSION['nav'] == 'forward') {
    $diff = $_SESSION['forecast_lastwhich'] + 1;
    if($diff > 4) {
        $year = $_SESSION['forecast_lastyear']+1;
        $which = 1;
        $_SESSION['startfrom'] = $which;
        $_SESSION['forcast_year'] = $year;
        //pre($_SESSION);
    } else {
        $which = $diff;
        $_SESSION['startfrom'] = $which;
        $year = $_SESSION['forcast_year'] = $_SESSION['forecast_lastyear'];
    }
}

if($_SESSION['nav'] == 'backward') {
  //pr($_SESSION);
    //$lastcnt = $_SESSION['startfrom'] - $_SESSION['forecast_column']; 
    
    $which = $_SESSION['startfrom'];
    for($i=0; $i<$_SESSION['forecast_column']; $i++) {
      $which = $which-1;
        if($which == 0) {
          $year = $_SESSION['forcast_year']-1;
          $which = 4;
          $_SESSION['startfrom'] = $which;
          $_SESSION['forcast_year'] = $year;
        } else {
            $_SESSION['startfrom'] = $which;
            $year = $_SESSION['forcast_year'];
        }
        
    }
    //pre($_SESSION);  
}
  if($_SESSION['nav'] == 'start') {
      $year = date('Y');
      $which = ceil(date('n') / 3);
      $_SESSION['startfrom'] = $which;
      $_SESSION['forcast_year'] = $year;
  }
}

if(!isset($_SESSION['nav'])) {
  $which = $_SESSION['startfrom'];
  $year = $_SESSION['forcast_year'];
}
unset($_SESSION['nav']);
if($_SESSION['forecast_intervel'] == 'month') {
  $intervel = 12;
}
if($_SESSION['forecast_intervel'] == 'quarter') {
  $intervel = 4;
}
if($_SESSION['forecast_intervel'] == 'week') {
  //$year = $_SESSION['forcast_year'];
  $date = strtotime("31 December $year");
  $weeks = gmdate("W", $date);
  if($weeks == '01') {
    $weeks = 52;
  }
  $intervel = $weeks;
}
$req_html = '';
$page = ($_REQUEST['offset']/$_REQUEST['limit'])+1;
$_SESSION['forecast_lastyear'] = $year;
$_SESSION['forecast_lastwhich'] = $which;
$projects = $this->projects_model->do_kanban_forecast_query($_SESSION['forecast_intervel'],$which,$year,false,$this->input->get('search'),$page,array('sort_by'=>$this->input->get('sort_by'),'sort'=>$this->input->get('sort')));
//pre($projects);
//echo '<pre>';print_r($projects);exit;
$total_projects = 0;
$req_data = array();
 if(!empty($projects)&& is_array($projects)){
  foreach ($projects as $project) {
	$req_html .= $this->load->view('admin/projects/_kan_ban_forecast_card',array('project'=>$project),true);
  }
  $req_data['status_projects'] = $req_html;
}			
echo json_encode($req_data);
?>
