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

for($fc=1; $fc<=$_SESSION['forecast_column']; $fc++) {
  if($which == '53' && $which == $intervel) {
    $week53 = 1;
    $_SESSION['forecast_lastyear'] = $year;
    $_SESSION['forecast_lastwhich'] = $which;
    $year = $year+1;
    if($_SESSION['forecast_intervel'] == 'week') {
        $which = 1;
    } else {
        $which = 1;
    }
  }
  if($which == '52' && $which == $intervel) {
    $week53 = 0;
  }
  ?>
  <ul class="kan-ban-col">
  <li class="kan-ban-col-wrapper">
    <div class="border-right panel_s">
      <?php
      
      if(!empty($which) && $which == $intervel) {
        //echo $year;
        $projects = $this->projects_model->do_kanban_forecast_query($_SESSION['forecast_intervel'],$which,$year);
		$total_projects = 0;
		if(!empty($projects) && is_array($projects))
			$total_projects = count($projects);
        $base_currency = $this->projects_model->get_currency('');
        $amt = 0;
        $wonamt = $amt_noprob = $tot_amt_noprob = $tot_amt = $open_amt = 0;
		if(!empty($projects)){
            foreach ($projects as $project) {
              if($project['project_cost'] != '0.00') {
                  $conversion_rate = $this->projects_model->conversionrate($base_currency->name,$project['project_currency']);
                  if($conversion_rate) {
                    if($conversion_rate[0]['operation'] == '*') {
                        $amt = ($project['project_cost']*$conversion_rate[0]['rate']);
                    } else {
                        $amt = ($project['project_cost']/$conversion_rate[0]['rate']);
                    }
                  } else {
                      $amt = $project['project_cost'];
                  }
                  if($project['stage_of'] == 1) {
                    $wonamt = $wonamt + $amt;
                  } else {
                    $amt_noprob = $amt_noprob + $amt;
                    $prob = $this->projects_model->get_pipeline_prob($project['status']);
                    if(isset($prob) && !empty($prob)) {
                      $open_amt = $open_amt + (($amt/100)*$prob->progress);
                    } else {
                      $open_amt = $open_amt + $amt;
                    }
                  }
              }
            }
		}
            
            $tot_amt_noprob = $wonamt + $amt_noprob;
            $tot_amt = $wonamt + $open_amt;
			$percent = 0;
			if($amt_noprob!=0)
				$percent = round(($open_amt/$amt_noprob)*100);
            if($percent > 0) {
              $percent = $percent;
            } else {
              $percent = 0;
            }
        ?>
        <div class="panel-heading-bg primary-bg">
          <div class="forecast-heading" style="margin-top:-6px; float:left;">
          <?php if($_SESSION['forecast_intervel'] == 'quarter') { ?>
            <font class="heading-font">Q<?php echo $which.' - '.$year; ?></font>
          <?php } 
            if($_SESSION['forecast_intervel'] == 'month') {
          ?>
            <font class="heading-font"><?php echo date("F", mktime(0, 0, 0, $which, 10)).' - '.$year; ?></font>
          <?php } 
            if($_SESSION['forecast_intervel'] == 'week') {
              $val = $which;
              $sStartDate = $this->projects_model->week_start_date($val, $year); 
              $sDate = date("d M",strtotime( $sStartDate )-60*60*24*(1)+60*60*24*1 );
              $sEndDate   = date("d M",strtotime($sStartDate)+60*60*24*6 );
              
              if($week53 == 1) {
                if($val == $intervel) {
                  $weekdisp = 1;
                  $yeardisp = $year+1;
                } else {
                  $weekdisp = $val;
                  $yeardisp = $year;
                }
              } else {
                if($val == $intervel) {
                  $weekdisp = 1;
                  $yeardisp = $year+1;
                } else {
                  $weekdisp = $val+1;
                  $yeardisp = $year;
                }
              }
              
            ?>
              <font class="heading-font"><?php echo $this->projects_model->original($weekdisp).' - '.$yeardisp; ?></font>
              <div>
                <?php echo $sDate; ?> - 
                <?php echo $sEndDate; ?>
              </div>
            
          <?php } ?>
            </div>
                <div id="head_<?php echo ltrim($which, 0); ?>" onmouseover="shownoprob(<?php echo ltrim($which, 0); ?>)" onmouseout="showprob(<?php echo ltrim($which, 0); ?>)" style="float:right; font-size:12px; color:#000; text-align:right;">
                  <div class="show_prob">
                      <span style="color:#08a742;"><?php echo $base_currency->symbol.' '.number_format($wonamt); ?></span><br>
                      + <i class="fa fa-balance-scale"></i> <span><?php echo $base_currency->symbol.' '.number_format($open_amt); ?></span><br>
                      <span style="border-top:1px solid #999;"><?php echo $base_currency->symbol.' '.number_format($tot_amt); ?></span>
                  </div>
                  <div class="show_noprob">
                      <span style="color:#08a742;"><?php echo $base_currency->symbol.' '.number_format($wonamt); ?></span><br>
                      + <?php echo $percent; ?>% of <span><?php echo $base_currency->symbol.' '.number_format($amt_noprob); ?></span><br>
                      <span style="border-top:1px solid #999;"><?php echo $base_currency->symbol.' '.number_format($tot_amt_noprob); ?></span>
                  </div>
              </div>
              </div>
              <div class="kan-ban-content-wrapper" style="width:100%">
                <div class="kan-ban-content">
            <ul class="status projects-status not-sortable" >
              <?php
              foreach ($projects as $project) {echo 'sdf';exit;
                
                $this->load->view('admin/projects/_kan_ban_forecast_card',array('project'=>$project));
              } ?>
              <?php if($total_projects > 0 ){ ?>
              
             <?php } ?>
             <li class="text-center not-sortable mtop30 kanban-empty<?php if($total_projects > 0){echo ' hide';} ?>">
              <h4>
                <i class="fa fa-circle-o-notch" aria-hidden="true"></i><br /><br />
                <?php echo _l('no_projects_found'); ?></h4>
              </li>
            </ul>
          </div>
          <?php
          $_SESSION['forecast_lastyear'] = $year;
          $_SESSION['forecast_lastwhich'] = $which;
          $year = $year+1;
          if($_SESSION['forecast_intervel'] == 'week') {
              $which = 1;
          } else {
              $which = 1;
          }
          
       } else {
        // echo $year;
        // echo $which;
         $_SESSION['forecast_lastyear'] = $year;
         $_SESSION['forecast_lastwhich'] = $which;
          $projects = $this->projects_model->do_kanban_forecast_query_all($_SESSION['forecast_intervel'],$which,$year);
          //pre($projects);
		  //echo '<pre>';print_r($projects);exit;
		  $total_projects = 0;
		  if(!empty($projects) && is_array($projects))
			$total_projects = count($projects);
            $base_currency = $this->projects_model->get_currency('');
            //pre($base_currency);
            $amt = 0;
            $wonamt = $amt_noprob = $tot_amt_noprob = $tot_amt = $open_amt = 0;
			 if(!empty($projects) && is_array($projects)){
            foreach ($projects as $project) {
              if($project['project_cost'] != '0.00') {
                  $conversion_rate = $this->projects_model->conversionrate($base_currency->name,$project['project_currency']);
                  
                  if($conversion_rate) {
                    if($conversion_rate[0]['operation'] == '*') {
                        $amt = ($project['project_cost']*$conversion_rate[0]['rate']);
                    } else {
                        $amt = ($project['project_cost']/$conversion_rate[0]['rate']);
                    }
                  } else {
                      $amt = $project['project_cost'];
                  }
                  
                  if($project['stage_of'] == 1) {
                    $wonamt = $wonamt + $amt;
                  } else {
                    $amt_noprob = $amt_noprob + $amt;
                    $prob = $this->projects_model->get_pipeline_prob($project['status']);
                    if(isset($prob) && !empty($prob)) {
                      $open_amt = $open_amt + (($amt/100)*$prob->progress); 
                    } else {
                      $open_amt = $open_amt + $amt;
                    }
                  }
              }
            }
			 }
            
            $tot_amt_noprob = $wonamt + $amt_noprob;
            $tot_amt = $wonamt + $open_amt;
			$percent = 0;
			if($amt_noprob!=0)
				$percent = round(($open_amt/$amt_noprob)*100);
            if($percent > 0) {
              $percent = $percent;
            } else {
              $percent = 0;
            }
            ?>
            <div class="panel-heading-bg primary-bg">
                  <div class="forecast-heading" style="margin-top:-6px; float:left;">
                  <?php if($_SESSION['forecast_intervel'] == 'quarter') { ?>
                    <font class="heading-font">Q<?php echo $which.' - '.$year; ?></font>
                  <?php } 
                    if($_SESSION['forecast_intervel'] == 'month') {
                  ?>
                    <font class="heading-font"><?php echo date("F", mktime(0, 0, 0, $which, 10)).' - '.$year; ?></font>
                    <?php } 
                      if($_SESSION['forecast_intervel'] == 'week') {
                        if($which == 0) {
                          $year1 = $year-1;
                          $date1 = strtotime("31 December $year1");
                          $weeks1 = gmdate("W", $date1);
                          if($weeks1 == '01') {
                            $weeks1 = 52;
                          }
                          $val = $weeks1+1;
                        } else {
                          $val = $which;
                        }
                        $sStartDate = $this->projects_model->week_start_date($val, $year); 
                        $sDate = date("d M",strtotime( $sStartDate )-60*60*24*(1)+60*60*24*1 );
                        $sEndDate   = date("d M",strtotime($sStartDate)+60*60*24*6 );
                        
                        if($week53 == 1) {
                          if($val == $intervel) {
                            $weekdisp = 1;
                            $yeardisp = $year+1;
                          } else {
                            $weekdisp = $val;
                            $yeardisp = $year;
                          }
                        } else {
                          if($val == $intervel) {
                            $weekdisp = 1;
                            $yeardisp = $year+1;
                          } else {
                            $weekdisp = $val+1;
                            $yeardisp = $year;
                          }
                        }
                      ?>
                        <font class="heading-font"><?php echo $this->projects_model->original($weekdisp).' - '.$yeardisp; ?></font>
                        <div>
                          <?php echo $sDate; ?> - 
                          <?php echo $sEndDate; ?>
                        </div>
                    <?php } ?>
              </div>
                <div id="head_<?php echo ltrim($which, 0); ?>" onmouseover="shownoprob(<?php echo ltrim($which, 0); ?>)" onmouseout="showprob(<?php echo ltrim($which, 0); ?>)" style="float:right; font-size:12px; color:#000; text-align:right;">
                  <div class="show_prob">
                      <span style="color:#08a742;"><?php echo $base_currency->symbol.' '.number_format($wonamt); ?></span><br>
                      + <i class="fa fa-balance-scale"></i> <span><?php echo $base_currency->symbol.' '.number_format($open_amt); ?></span><br>
                      <span style="border-top:1px solid #999;"><?php echo $base_currency->symbol.' '.number_format($tot_amt); ?></span>
                  </div>
                  <div class="show_noprob">
                      <span style="color:#08a742;"><?php echo $base_currency->symbol.' '.number_format($wonamt); ?></span><br>
                      + <?php echo $percent; ?>% of <span><?php echo $base_currency->symbol.' '.number_format($amt_noprob); ?></span><br>
                      <span style="border-top:1px solid #999;"><?php echo $base_currency->symbol.' '.number_format($tot_amt_noprob); ?></span>
                  </div>
                </div>
                </div>
                <div class="kan-ban-content-wrapper" style="width:100%">
                  <div class="kan-ban-content">
              <ul class="status projects-status not-sortable" id="forcast_status1">
                  <?php
				  if(!empty($projects)&& is_array($projects)){
                  foreach ($projects as $project) {
                    $this->load->view('admin/projects/_kan_ban_forecast_card',array('project'=>$project));
                  }
				  }				  ?>
                  <?php if($total_projects > 0 ){ ?>
                  
                <?php } ?>
                <li class="text-center not-sortable mtop30 kanban-empty<?php if($total_projects > 0){echo ' hide';} ?>">
                  <h4>
                    <i class="fa fa-circle-o-notch" aria-hidden="true"></i><br /><br />
                    <?php echo _l('no_projects_found'); ?></h4>
                  </li>
                </ul>
              </div>
        <?php
        
        //pre($projects);
          $which++;
       }
      
      ?>
      </div>
    </li>
  </ul>
  <?php
}
?>
<script>
$(document).ready(function() {
	  var busy = false;
var limit = <?php echo get_option('projects_kanban_limit');?>;
var offset = 0;
function displayRecords(lim, off) {
		var url =  admin_url+'projects/kanban_forecast_more_load';
        $.ajax({
          type: "GET",
          async: false,
          url: url,
          data: "limit=" + lim + "&offset=" + off,
          cache: false,
          beforeSend: function() {
            $("#loader_message").html("").hide();
            $('#loader_image').show();
          },
          success: function(html) {
			  var obj = JSON.parse(html);
				$("#forcast_status1").append(obj.status_projects);

            //$("#results").append(html);
            //$('#loader_image').hide();
            if (html == "") {
             // $("#loader_message").html('<button data-atr="nodata" class="btn btn-default" type="button">No more records.</button>').show()
            } else {
              //$("#loader_message").html('<button class="btn btn-default" type="button">Loading please wait...</button>').show();
            }
          }
        });
}
/*$(window).scroll(function() {
          // make sure u give the container id of the data to be loaded in.
          if ($(window).scrollTop() + $(window).height() > $("#kan-ban").height() && !busy) {
            busy = true;
            offset = limit + offset;
            displayRecords(limit, offset);
          }
});*/
$('#kan-ban-tab').scroll(function() {
          // make sure u give the container id of the data to be loaded in.
         // if ($(window).scrollTop() + $(window).height() > $("#results").height() ) {
            busy = true;
            offset = limit + offset;
            displayRecords(limit, offset);
        //  }
})
});
</script>