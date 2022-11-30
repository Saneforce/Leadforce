<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include_once(APPPATH.'views/admin/includes/helpers_bottom.php'); ?>

<?php hooks()->do_action('before_js_scripts_render'); ?>

<?php echo app_compile_scripts();

/**
 * Global function for custom field of type hyperlink
 */
echo get_custom_fields_hyperlink_js_function(); 

$CI = &get_instance();
if(!$CI->input->is_ajax_request()){
  echo get_custom_field_location_js_data();
}
        
        ?>
<?php
/**
 * Check for any alerts stored in session
 */
app_js_alerts();
?>
<script src="<?php echo base_url('assets/js/call_phone.js'); ?>"></script>
<?php
/**
 * Check pusher real time notifications
 */
if(get_option('pusher_realtime_notifications') == 1){ ?>
   <script type="text/javascript">
   $(function(){
         // Enable pusher logging - don't include this in production
         // Pusher.logToConsole = true;
         <?php $pusher_options = hooks()->apply_filters('pusher_options', array());
            if(!isset($pusher_options['cluster']) && get_option('pusher_cluster') != ''){
                  $pusher_options['cluster'] = get_option('pusher_cluster');
            }
         ?>
         var pusher_options = <?php echo json_encode($pusher_options); ?>;
         var pusher = new Pusher("<?php echo get_option('pusher_app_key'); ?>", pusher_options);
         var channel = pusher.subscribe('notifications-channel-<?php echo get_staff_user_id(); ?>');
         channel.bind('notification', function(data) {
            fetch_notifications();
         });
   });
   </script>
<?php } ?>
<script>
   $(document).ready(function() {
	   if(performance.navigation.type == 2){
			//location.reload(true);
		}
      $('a.nav-tabs-a').click(function(e) {
		  e.stopPropagation();
		  e.preventDefault();
		  
		  console.log('stopPropagation');
		  //$( "li.header-search.timer-button" ).addClass('open');
		  //setTimeout(function(){  
				   //$( "li.header-search.timer-button" ).addClass('open');
				   //console.log('open');
                //}, 1500);
		  $("#THcontacts").hide().removeClass('in');;
		   $("#THall").hide().removeClass('in');
		   $("#THprojects").hide().removeClass('in');
		   $("#THclients").hide().removeClass('in');
		   
		   $("ul.nav.nav-tabs li").removeClass('active');
		   $(this).parent().addClass('active');
		   
		  var getname = $(this).attr("href");
		  $(getname).show().addClass('in');
		  console.log(getname);
		  return false;
      });

      $( "#header_gsearch_top" ).on('click', function() {
        setTimeout(function(){  
          $("#header_gsearch_top").focus();
        }, 100);
      });
     
      $( "#header_gsearch_top" ).bind('keyup', function() {
         //$( "#header_gsearch_top" ).trigger('click');
            var searchVal = $(this).val();
            // var timer;
            // clearTimeout(timer);
            // timer = setTimeout(function() {
              var msg;

              console.log(searchVal);
                $.ajax({
                  type: 'POST',
                  url: "<?php echo admin_url('projects/header_gsearch')  ?>",
                  data: {globalsearch:searchVal},
                  dataType: 'json',
                  cache: false,
                  success: function(msg){
                    
                    if(searchVal == $( "#header_gsearch_top" ).val()) {
                      //$("#THheader_gsearch_top").html(msg);
                          $("#THcontacts").html(msg.contacts_html);
                          $("#THall").html(msg.all_html);
                          $("#THprojects").html(msg.projects_html);
                          $("#THclients").html(msg.clients_html);
                          
                            $("#THccontacts").html(msg.contacts_count);
                          $("#THcall").html(msg.all_count);
                          $("#THcprojects").html(msg.projects_count);
                          $("#THcclients").html(msg.clients_count);
                            setTimeout(function(){  
                              $( "li.header-search.timer-button" ).addClass('open');
                                  }, 200);
                      //$( "#header_gsearch_top" ).trigger('click');
                      } else {
                        msg = '';
                      }
                  }
                });
            //}, 1000);
      });
   });
   

</script>

<link rel="stylesheet" href="<?php echo base_url('assets/plugins/timepicker/css/jquery.timepicker.min.css'); ?>">
<script src="<?php echo base_url('assets/plugins/timepicker/js/jquery.timepicker.min.js'); ?>"></script>
<script>
$(document).ready(function(){
    $('input.timepicker').timepicker({
      timeFormat: 'h:mm p',
    interval: 10,
    startTime: '10:00',
    dynamic: false,
    dropdown: true,
    scrollbar: true,
	change: function(time) {
		var req_id = $(this).attr("id");
		$('#'+req_id+"-error").hide();
    }
    });
    $("#smtp_host").change(function(){
      var smtp = $("#smtp_host").val();
      if(smtp == 'smtp.gmail.com') {

        $(".smtpother").hide();
        $('.smtpother #smtp_host').attr('disabled','');
        $('.smtpother #smtp_port').attr('disabled','');
        $('.smtpother #smtp_encryption').attr('disabled', '');

        $(".gmail").show();
        $(".yahoo").hide();
        $(".outlook").hide();
        
        $(".smtpport #smtp_port").removeAttr('disabled');

        $('.gmail #smtp_encryption').removeAttr('disabled');
        $('.yahoo #smtp_encryption').attr('disabled', '');
        $('.outlook #smtp_encryption').attr('disabled', '');
      }
      else if(smtp == 'smtp.mail.yahoo.com') {

        $(".smtpother").hide();
        $('.smtpother #smtp_host').attr('disabled','');
        $('.smtpother #smtp_port').attr('disabled','');
        $('.smtpother #smtp_encryption').attr('disabled', '');

        $(".gmail").hide();
        $(".yahoo").show();
        $(".outlook").hide();

        $(".smtpport #smtp_port").removeAttr('disabled');

        $('.gmail #smtp_encryption').attr('disabled', '');
        $('.yahoo #smtp_encryption').removeAttr('disabled');
        $('.outlook #smtp_encryption').attr('disabled', '');
      }
      else if(smtp == 'smtp.office365.com') {

        $(".smtpother").hide();
        $('.smtpother #smtp_host').attr('disabled','');
        $('.smtpother #smtp_port').attr('disabled','');
        $('.smtpother #smtp_encryption').attr('disabled', '');

        $(".gmail").hide();
        $(".yahoo").hide();
        $(".outlook").show();

        $(".smtpport #smtp_port").removeAttr('disabled');

        $('.gmail #smtp_encryption').attr('disabled', '');
        $('.yahoo #smtp_encryption').attr('disabled', '');
        $('.outlook #smtp_encryption').removeAttr('disabled');
      } else {
        $(".smtpother").show();
        $('.smtpother #smtp_host').removeAttr('disabled');
        $('.smtpother #smtp_port').removeAttr('disabled');
        $('.smtpother #smtp_encryption').removeAttr('disabled');
        
        $(".gmail").hide();
        $(".yahoo").hide();
        $(".outlook").hide();

        $(".smtphost").hide();
        $('.smtphost #smtp_host').attr('disabled', '');

        $(".smtpport #smtp_port").attr('disabled', '');

        $('.gmail #smtp_encryption').attr('disabled', '');
        $('.yahoo #smtp_encryption').attr('disabled', '');
        $('.outlook #smtp_encryption').attr('disabled','');
      }
    });
    var smtp = $("#smtp_host").val();
    if(smtp == 'smtp.gmail.com') {

      $(".smtpother").hide();
      $('.smtpother #smtp_host').attr('disabled','');
      $('.smtpother #smtp_port').attr('disabled','');
      $('.smtpother #smtp_encryption').attr('disabled', '');

      $(".gmail").show();
      $(".yahoo").hide();
      $(".outlook").hide();

      $(".smtpport #smtp_port").removeAttr('disabled');

      $('.gmail #smtp_encryption').removeAttr('disabled');
      $('.yahoo #smtp_encryption').attr('disabled', '');
      $('.outlook #smtp_encryption').attr('disabled', '');
    }
    else if(smtp == 'smtp.mail.yahoo.com') {

      $(".smtpother").hide();
      $('.smtpother #smtp_host').attr('disabled','');
      $('.smtpother #smtp_port').attr('disabled','');
      $('.smtpother #smtp_encryption').attr('disabled', '');

      $(".gmail").hide();
      $(".yahoo").show();
      $(".outlook").hide();

      $(".smtpport #smtp_port").removeAttr('disabled');

      $('.gmail #smtp_encryption').attr('disabled', '');
      $('.yahoo #smtp_encryption').removeAttr('disabled');
      $('.outlook #smtp_encryption').attr('disabled', '');
    }
    else if(smtp == 'smtp.office365.com') {

      $(".smtpother").hide();
      $('.smtpother #smtp_host').attr('disabled','');
      $('.smtpother #smtp_port').attr('disabled','');
      $('.smtpother #smtp_encryption').attr('disabled', '');

      $(".gmail").hide();
      $(".yahoo").hide();
      $(".outlook").show();

      $(".smtpport #smtp_port").removeAttr('disabled');

      $('.gmail #smtp_encryption').attr('disabled', '');
      $('.yahoo #smtp_encryption').attr('disabled', '');
      $('.outlook #smtp_encryption').removeAttr('disabled');
    } else {
      $(".smtpother").show();
      $('.smtpother #smtp_host').removeAttr('disabled');
      $('.smtpother #smtp_port').removeAttr('disabled');
      $('.smtpother #smtp_encryption').removeAttr('disabled');

      $(".gmail").hide();
      $(".yahoo").hide();
      $(".outlook").hide();
      $(".smtphost").hide();
      $(".smtpport").hide();
      $('.smtphost #smtp_host').attr('disabled', '');

      $(".smtpport #smtp_port").attr('disabled', '');
     

      $('.gmail #smtp_encryption').attr('disabled', '');
      $('.yahoo #smtp_encryption').attr('disabled', '');
      $('.outlook #smtp_encryption').attr('disabled','');
    }
    $(".gmail #smtp_encryption").change(function(){
      $(".smtpport").show();
      $(".smtpport #smtp_port").removeAttr('disabled');
      var encryp = $(this).val();
      if(encryp == 'ssl') {
        $(".smtpport #smtp_port").val('465');
      } else {
        $(".smtpport #smtp_port").val('587');
      }
    });
    $(".yahoo #smtp_encryption").change(function(){
      $(".smtpport").show();
      $(".smtpport #smtp_port").removeAttr('disabled');
      var encryp = $(this).val();
      if(encryp == 'ssl') {
        $(".smtpport #smtp_port").val('465');
      } else {
        $(".smtpport #smtp_port").val('587');
      }
    });
    $(".outlook #smtp_encryption").change(function(){
      $(".smtpport").show();
      $(".smtpport #smtp_port").removeAttr('disabled');
      var encryp = $(this).val();
      if(encryp == 'ssl') {
        $(".smtpport #smtp_port").val('465');
      } else {
        $(".smtpport #smtp_port").val('587');
      }
    });
	
    $("#smtpserver").change(function(){
        var server = $(this).val();
        if(server == 'others') {
          $(".smtpother").show();
          $('.smtpother #smtp_host').removeAttr('disabled');
          $('.smtpother #smtp_port').removeAttr('disabled');
          $('.smtpother #smtp_encryption').removeAttr('disabled');
          
          $(".gmail").hide();
          $(".yahoo").hide();
          $(".outlook").hide();
          $(".smtphost").hide();
          $(".smtpport").hide();
          
          $('.smtphost #smtp_host').attr('disabled', '');

          $('.smtp_port #smtp_port').attr('disabled', '');
          
          $('.gmail #smtp_encryption').attr('disabled', '');
          $('.yahoo #smtp_encryption').attr('disabled', '');
          $('.outlook #smtp_encryption').attr('disabled','');
        } else {
          $(".smtpother").hide();
          $('.smtpother #smtp_host').attr('disabled', '');
          $('.smtpother #smtp_port').attr('disabled', '');
          $('.smtpother #smtp_encryption').attr('disabled', '');
          $(".smtphost").show();
          $(".smtpport").show();
          $('.smtphost #smtp_host').removeAttr('disabled');
          $(".smtpport #smtp_port").removeAttr('disabled');
          if(server == 'gmail') {
            $("#smtp_host").val('smtp.gmail.com');
          } 
          else if(server == 'yahoo') {
            $("#smtp_host").val('smtp.mail.yahoo.com');
          }
          else if(server == 'outlook') {
            $("#smtp_host").val('smtp.office365.com');
          }
          var smtp = $("#smtp_host").val();
          if(smtp == 'smtp.gmail.com') {

            $(".smtpother").hide();
            $('.smtpother #smtp_host').attr('disabled','');
            $('.smtpother #smtp_port').attr('disabled','');
            $('.smtpother #smtp_encryption').attr('disabled', '');

            $(".gmail").show();
            $(".yahoo").hide();
            $(".outlook").hide();

            $('.gmail #smtp_encryption').removeAttr('disabled');
            $('.yahoo #smtp_encryption').attr('disabled', '');
            $('.outlook #smtp_encryption').attr('disabled', '');
          }
          if(smtp == 'smtp.mail.yahoo.com') {

            $(".smtpother").hide();
            $('.smtpother #smtp_host').attr('disabled','');
            $('.smtpother #smtp_port').attr('disabled','');
            $('.smtpother #smtp_encryption').attr('disabled', '');

            $(".gmail").hide();
            $(".yahoo").show();
            $(".outlook").hide();

            $('.gmail #smtp_encryption').attr('disabled', '');
            $('.yahoo #smtp_encryption').removeAttr('disabled');
            $('.outlook #smtp_encryption').attr('disabled', '');
          }
          if(smtp == 'smtp.office365.com') {

            $(".smtpother").hide();
            $('.smtpother #smtp_host').attr('disabled','');
            $('.smtpother #smtp_port').attr('disabled','');
            $('.smtpother #smtp_encryption').attr('disabled', '');

            $(".gmail").hide();
            $(".yahoo").hide();
            $(".outlook").show();

            $('.gmail #smtp_encryption').attr('disabled', '');
            $('.yahoo #smtp_encryption').attr('disabled', '');
            $('.outlook #smtp_encryption').removeAttr('disabled');
          }
        }
      });

      $("#imap_host").change(function(){
        var imap = $("#imap_host").val();
        if(imap == 'imap.gmail.com') {
          $(".imapother").hide();
          $('.imapother #imap_host').attr('disabled', '');
          $('.imapother #imap_port').attr('disabled', '');
          $('.imapother #imap_encryption').attr('disabled', '');
          $(".imaphost").show();
          $(".imapport").show();
          $('.imaphost #imap_host').removeAttr('disabled');
          $('.imapport #imap_port').removeAttr('disabled');

          $(".imapgmail").show();
          $(".imapyahoo").hide();
          $(".imapoutlook").hide();

          $('.imapgmail #imap_encryption').removeAttr('disabled');
          $('.imapyahoo #imap_encryption').attr('disabled', '');
          $('.imapoutlook #imap_encryption').attr('disabled', '');
        }
        else if(imap == 'imap.mail.yahoo.com') {
          $(".imapother").hide();
          $('.imapother #imap_host').attr('disabled', '');
          $('.imapother #imap_port').attr('disabled', '');
          $('.imapother #imap_encryption').attr('disabled', '');
          $(".imaphost").show();
          $(".imapport").show();
          $('.imaphost #imap_host').removeAttr('disabled');
          $('.imapport #imap_port').removeAttr('disabled');
          
          $(".imapgmail").hide();
          $(".imapyahoo").show();
          $(".imapoutlook").hide();

          $('.imapgmail #imap_encryption').attr('disabled', '');
          $('.imapyahoo #imap_encryption').removeAttr('disabled');
          $('.imapoutlook #imap_encryption').attr('disabled', '');
        }
        else if(imap == 'outlook.office365.com') {
          $(".imapother").hide();
          $('.imapother #imap_host').attr('disabled', '');
          $('.imapother #imap_port').attr('disabled', '');
          $('.imapother #imap_encryption').attr('disabled', '');
          $(".imaphost").show();
          $(".imapport").show();
          $('.imaphost #imap_host').removeAttr('disabled');
          $('.imapport #imap_port').removeAttr('disabled');
          
          $(".imapgmail").hide();
          $(".imapyahoo").hide();
          $(".imapoutlook").show();

          $('.imapgmail #imap_encryption').attr('disabled', '');
          $('.imapyahoo #imap_encryption').attr('disabled', '');
          $('.imapoutlook #imap_encryption').removeAttr('disabled');
        }
        else {
            $(".imapother").show();
            $('.imapother #imap_host').removeAttr('disabled');
            $('.imapother #imap_port').removeAttr('disabled');
            $('.imapother #imap_encryption').removeAttr('disabled');

            $(".imapgmail").hide();
            $(".imapyahoo").hide();
            $(".imapoutlook").hide();
            $(".imaphost").hide();
            $(".imapport").hide();
            
            $('.imaphost #imap_host').attr('disabled', '');
            $('.imapport #imap_port').attr('disabled', '');
            
            $('.imapgmail #imap_encryption').attr('disabled', '');
            $('.imapyahoo #imap_encryption').attr('disabled', '');
            $('.imapoutlook #imap_encryption').attr('disabled','');
            
        }
    });
    var imap = $("#imap_host").val();
      if(imap == 'imap.gmail.com') {
        $(".imapother").hide();
        $('.imapother #imap_host').attr('disabled', '');
        $('.imapother #imap_port').attr('disabled', '');
        $('.imapother #imap_encryption').attr('disabled', '');
        $(".imaphost").show();
        $(".imapport").show();
        $('.imaphost #imap_host').removeAttr('disabled');
        $('.imapport #imap_port').removeAttr('disabled');

        $(".imapgmail").show();
        $(".imapyahoo").hide();
        $(".imapoutlook").hide();

        $('.imapgmail #imap_encryption').removeAttr('disabled');
        $('.imapyahoo #imap_encryption').attr('disabled', '');
        $('.imapoutlook #imap_encryption').attr('disabled', '');
      }
      else if(imap == 'imap.mail.yahoo.com') {
        $(".imapother").hide();
        $('.imapother #imap_host').attr('disabled', '');
        $('.imapother #imap_port').attr('disabled', '');
        $('.imapother #imap_encryption').attr('disabled', '');
        $(".imaphost").show();
        $(".imapport").show();
        $('.imaphost #imap_host').removeAttr('disabled');
        $('.imapport #imap_port').removeAttr('disabled');
        
        $(".imapgmail").hide();
        $(".imapyahoo").show();
        $(".imapoutlook").hide();

        $('.imapgmail #imap_encryption').attr('disabled', '');
        $('.imapyahoo #imap_encryption').removeAttr('disabled');
        $('.imapoutlook #imap_encryption').attr('disabled', '');
      }
      else if(imap == 'outlook.office365.com') {
        $(".imapother").hide();
        $('.imapother #imap_host').attr('disabled', '');
        $('.imapother #imap_port').attr('disabled', '');
        $('.imapother #imap_encryption').attr('disabled', '');
        $(".imaphost").show();
        $(".imapport").show();
        $('.imaphost #imap_host').removeAttr('disabled');
        $('.imapport #imap_port').removeAttr('disabled');
        
        $(".imapgmail").hide();
        $(".imapyahoo").hide();
        $(".imapoutlook").show();

        $('.imapgmail #imap_encryption').attr('disabled', '');
        $('.imapyahoo #imap_encryption').attr('disabled', '');
        $('.imapoutlook #imap_encryption').removeAttr('disabled');
      }
      else {
          $(".imapother").show();
          $('.imapother #imap_host').removeAttr('disabled');
          $('.imapother #imap_port').removeAttr('disabled');
          $('.imapother #imap_encryption').removeAttr('disabled');

          $(".imapgmail").hide();
          $(".imapyahoo").hide();
          $(".imapoutlook").hide();
          $(".imaphost").hide();
          $(".imapport").hide();
          
          $('.imaphost #imap_host').attr('disabled', '');
          $('.imapport #imap_port').attr('disabled', '');
          
          $('.imapgmail #imap_encryption').attr('disabled', '');
          $('.imapyahoo #imap_encryption').attr('disabled', '');
          $('.imapoutlook #imap_encryption').attr('disabled','');
          
      }
      $("#imapserver").change(function(){
        var server = $(this).val();
        if(server == 'others') {
          $(".imapother").show();
          $('.imapother #imap_host').removeAttr('disabled');
          $('.imapother #imap_port').removeAttr('disabled');
          $('.imapother #imap_encryption').removeAttr('disabled');

          $(".imapgmail").hide();
          $(".imapyahoo").hide();
          $(".imapoutlook").hide();
          $(".imaphost").hide();
          $(".imapport").hide();
          
          $('.imaphost #imap_host').attr('disabled', '');
          $('.imapport #imap_port').attr('disabled', '');
          

          $('.imapgmail #imap_encryption').attr('disabled', '');
          $('.imapyahoo #imap_encryption').attr('disabled', '');
          $('.imapoutlook #imap_encryption').attr('disabled','');
        } else {
			$('#imap_port').val('993');
          $(".imapother").hide();
          $('.imapother #imap_host').attr('disabled', '');
          $('.imapother #imap_port').attr('disabled', '');
          $('.imapother #imap_encryption').attr('disabled', '');
          $(".imaphost").show();
          $(".imapport").show();
          $('.imaphost #imap_host').removeAttr('disabled');
          $('.imapport #imap_port').removeAttr('disabled');
          if(server == 'gmail') {
            $("#imap_host").val('imap.gmail.com');
          } 
          else if(server == 'yahoo') {
            $("#imap_host").val('imap.mail.yahoo.com');
          }
          else if(server == 'outlook') {
            $("#imap_host").val('outlook.office365.com');
          }
          
          var imap = $("#imap_host").val();
          if(imap == 'imap.gmail.com') {
            $(".imapgmail").show();
            $(".imapyahoo").hide();
            $(".imapoutlook").hide();

            $('.imapgmail #imap_encryption').removeAttr('disabled');
            $('.imapyahoo #imap_encryption').attr('disabled', '');
            $('.imapoutlook #imap_encryption').attr('disabled', '');
          }
          if(imap == 'imap.mail.yahoo.com') {
            $(".imapgmail").hide();
            $(".imapyahoo").show();
            $(".imapoutlook").hide();

            $('.imapgmail #imap_encryption').attr('disabled', '');
            $('.imapyahoo #imap_encryption').removeAttr('disabled');
            $('.imapoutlook #imap_encryption').attr('disabled', '');
          }
          if(imap == 'outlook.office365.com') {
            $(".imapgmail").hide();
            $(".imapyahoo").hide();
            $(".imapoutlook").show();
            $(".imapother").hide();
            
            $('.imapgmail #imap_encryption').attr('disabled', '');
            $('.imapyahoo #imap_encryption').attr('disabled', '');
            $('.imapoutlook #imap_encryption').removeAttr('disabled');
          }
        }
      });
});
</script>

<link rel="stylesheet" href="<?php echo base_url('assets/plugins/daterangepicker/css/daterangepicker.css'); ?>">
<script src="<?php echo base_url('assets/plugins/daterangepicker/js/daterangepicker.min.js'); ?>"></script>
<script>
$(document).ready(function(){
    $('input.daterangepicker').daterangepicker({
      locale: {
      format: 'YYYY-MM-DD'
    }
    });
    $('input.datetimerangepicker').daterangepicker({
      timePicker: true,
      locale: {
      format: 'YYYY-MM-DD hh:mm A'
    }
    });
});
<?php if(get_option('connect_mail') !='yes'){ ?>
	$("#email_int_group_modal").validate({
		rules:{
			connect_email:{email: true,required: true},
			/*"mail_folder[]": {
				required: true,
				minlength: 1
			 },*/
		},
		

		messages: {
			connect_email:{email:"Enter Valid Email!",
				required:"Enter Email!"
				}
		},

		submitHandler: function(form){
			$('#email_er_data').html('');
			 document.getElementById('overlay5').style.display = '';
			$.ajax({
				url: form.action,
				type: form.method,
				data: $(form).serialize(),
				success: function(response) {
					var obj = JSON.parse(response);
					if(obj.code == 'error'){
						document.getElementById('overlay5').style.display = 'none';
						$('#email_er_data').html(obj.message);
					}
					else{
						window.location.href= obj.message;
					}
				}            
			});		
		}
	});
<?php }?>
</script>
<input type="hidden" id="call_app_token" value="<?php echo CALL_APP_TOKEN;?>">
<input type="hidden" id="call_app_id" value="<?php echo CALL_APP_ID;?>">
<input type="hidden" id="call_app_secret" value="<?php echo CALL_APP_SECRET;?>">
<input type="hidden" id="call_source_from" value="<?php echo CALL_SOURCE_FROM;?>">
<?php app_admin_footer(); ?>
