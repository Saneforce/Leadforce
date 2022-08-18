<?php
if(!isset($contentEditable))
    $contentEditable =false;
function content_editable($name,$contentEditable){
    if($contentEditable)
        return ' contenteditable="true" data-content-name="'.$name.'"';
}
$earlier = new DateTime($proposal->date);
$later = new DateTime($proposal->open_till);
$abs_diff = $later->diff($earlier)->format("%a");
function get_content_from_proposal($name,$proposal)
{
    $default_content =array(
        'content1'=>'<span style="font-weight:bold">Dear Sir</span>,<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;We acknowledge with thanks the receipt of your enquiry and as desired; we have
        pleasure to submit hereunder our offer along with other terms and condition for
        supply of LRPC Strands confirming to <span style="font-weight:bold">IS: 14268:2017 Class II.</span>',
        'content2'=>'We are sure you will find our offer most competitive and looking forward to receive your valued order.',
        'content3'=>'Thanking you, we remain with best regards,',
        'freight_charge'=>'Inclusive upto your <span style="font-weight:bold">XXXX</span> Site.',
        'payment_terms'=>'100% Advance before despatch.',
        'delivery_terms'=>'Ready Stock/ As Per your requirement upto 30th August 2022.',
    );
    $template_contents =json_decode($proposal->template_contents,true);
    if(isset($template_contents['proposalpdf_domestic']) && isset($template_contents['proposalpdf_domestic'][$name])){
        return $template_contents['proposalpdf_domestic'][$name];
    }else{
        return $default_content [$name];
    }

}
function callback_proposalpdfnewheader($data)
{
    $pdf =$data['pdf_instance'];
    // get the current page break margin
    $bMargin = $pdf->getBreakMargin();
    // get current auto-page-break mode
    $auto_page_break = $pdf->getAutoPageBreak();
    // disable auto-page-break
    $pdf->SetAutoPageBreak(false, 0);
    // set bacground image
    $letterhead =get_upload_path_by_type('company').'letterhead.jpg';
    $pdf->Image($letterhead, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
    // restore auto-page-break status
    $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
    // set the starting point for the page content
    $pdf->setPageMark();
    $pdf->SetMargins(30, 35, 10, true);
    $pdf->writeHTML('<div><br><br></div>', true, false, true, false, '');
    $data ['pdf_instance'] =$pdf;
    return $data;

}

hooks()->add_action('pdf_header','callback_proposalpdfnewheader');
$proposal_date =_d($proposal->date);
$address =format_proposal_info($proposal, 'pdf');

$table ='';
if(isset($proposal->items) && $proposal->items){
    $table .='<table style="border: 1px solid black;border-collapse: collapse;padding:5px 10px;">';
    foreach($proposal->items as $count => $item){
        $table .='<tr>
        <td width="25%" style="border: 1px solid black;border-collapse: collapse;padding:10px;"><span style="font-weight:bold;">Item</span></td>
        <td width="75%"><span style="font-weight:bold;">'.getItemName($item['productid']).'</span></td>
        </tr>
        <tr>
        <td style="border: 1px solid black;border-collapse: collapse;"><span style="font-weight:bold">Quantity</span></td>
        <td style="border: 1px solid black;border-collapse: collapse;">'.$item['qty'].'</td>
        </tr>
        <tr>
        <td style="border: 1px solid black;border-collapse: collapse;"><span style="font-weight:bold">Rate '.$item['currency'].'/MT</span></td>
        <td style="border: 1px solid black;border-collapse: collapse;"><span style="font-weight:bold">'.$item['price'].' </span></td>
        </tr>';
        
        if($item['method'] ==2 || $item['method'] ==3){
            $table .='<tr>
            <td style="border: 1px solid black;border-collapse: collapse;">G.S.T.</td>';
            if($item['method'] ==2){
                $table .='<td style="border: 1px solid black;border-collapse: collapse;">@ '.$item['tax'].' % Included</td>';
            }elseif($item['method'] ==3){
                $table .='<td style="border: 1px solid black;border-collapse: collapse;">@ '.$item['tax'].' % Extra or as applicable at the time of despatch.</td>';
            }
            $table .='</tr>';
        }
        if($proposal->items[$count+1]){
            $table .='<tr><td style="border: 1px solid black;border-collapse: collapse;"></td><td style="border: 1px solid black;border-collapse: collapse;"></td></tr>';
        }
    }
    $table .='
        <tr>
        <td style="border: 1px solid black;border-collapse: collapse;">Freight Charges</td>
        <td style="border: 1px solid black;border-collapse: collapse;"'.content_editable('freight_charge',$contentEditable).'>'.get_content_from_proposal('freight_charge',$proposal).'</td>
        </tr>
        <tr>
        <td style="border: 1px solid black;border-collapse: collapse;">Payment Terms</td>
        <td style="border: 1px solid black;border-collapse: collapse;" '.content_editable('payment_terms',$contentEditable).'>'.get_content_from_proposal('payment_terms',$proposal).'</td>
        </tr>
        <tr>
        <td style="border: 1px solid black;border-collapse: collapse;">Delivery/Despatch</td>
        <td style="border: 1px solid black;border-collapse: collapse;" '.content_editable('delivery_terms',$contentEditable).'>'.get_content_from_proposal('delivery_terms',$proposal).'</td>
        </tr>
        <tr>
        <td style="border: 1px solid black;border-collapse: collapse;">Offer Validity</td>
        <td style="border: 1px solid black;border-collapse: collapse;">'.$abs_diff.' Days</td>
        </tr>';
    $table .='</table>';
}
if($contentEditable ==false){
    callback_proposalpdfnewheader(['pdf_instance'=>$pdf])['pdf_instance'];
}

$html ='';
if($contentEditable){
    
    $html .='<style>';
    $html .='[contenteditable="true"]{
        border: 2px solid green !important;
        border-radius: 5px;
    }';
    $html .='</style>';
}

$invoicepdf = get_invoice_pdf_config();
$html .='
<div style="font-size:18px;line-height:1.2;">
<h1 style="text-align:center">Quotation</h1>
<h4 style="text-align:right">'.format_proposal_number($proposal->id).'</h4>
<h4 style="text-align:right">Dt. '.$proposal_date.'</h4>
<h4 style="text-align:left">To,</h4>
<p style="text-align:left">'.$address.'</p>
<h4 style="text-align:Left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kind Atten : '.$proposal->proposal_to.'</h4> 
<h4 style="text-align:Left;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Subject : <span style="font-weight:normal">'.$proposal->subject.'</span></h4>
<p style="text-align:left" '.content_editable('content1',$contentEditable).'>'.get_content_from_proposal('content1',$proposal).'</p>
'.$table.'
<br>
<p '.content_editable('content2',$contentEditable).'>'.get_content_from_proposal('content2',$proposal).'</p>
<br>
<br>
<p '.content_editable('content3',$contentEditable).'>'.get_content_from_proposal('content3',$proposal).'</p>
<br>
<br>
<p>'.nl2br($invoicepdf->contact_details).'</p>
</div>';
if($contentEditable){
    init_head();
    echo '<div id="wrapper">';
    echo '<div class="content">';
    echo '<div class="panel_s">';
    echo '<div class="panel-body" style="width:794px;heigth:1123px;margin-left: auto;margin-right: auto;">';
    echo '<a href="'.admin_url('proposals/proposal/'.$proposal->id).'" class="btn btn-info mleft10 pull-right">'._l('edit',_l('proposal')).'</a>';
    echo '<div style="">';
    echo $html;
    echo '</div>';
    echo '<button type="submit" class="btn btn-info mleft10 pull-right savepdfTemplate">'._l('submit').'</button>';
    echo '<button type="button" class="btn btn-info mleft10 savepdfTemplate save-and-send  pull-right">'._l('save_and_send').'</button>';
    echo '<a href="#" id="choose_temp" for="template" class="mleft15 pull-right" >Click here to Choose Template</a>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    ?>
    <div class="modal fade email-template" data-editor-id=".<?php echo 'tinymce-'.$proposal->id; ?>" id="proposal_send_to_customer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <?php //echo form_open('admin/proposals/send_to_email/'.$proposal->id); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-send-template-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        <span class="edit-title">Select Templates to <?php echo _l('proposal_send_to_email_title'); ?></span>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?php //echo render_input('cc','CC'); ?>
                            <label for="template" class="control-label">Choose Template</label>
                            <select name="tempid" id="tempid" class="form-control">
                            <?php foreach($all_templates as $val) { ?>
                                <option value="<?php echo $val['emailtemplateid']; ?>"><?php echo $val['name']; ?></option>
                            <?php } ?>
                            </select><br>
                            <label for="subject" class="control-label">Subject</label>
                            <input type="text" class="form-control" id="email_sub" name="email_sub" value="<?php echo $template->subject; ?>">
                            <br>
                            <label for="message" class="control-label">Message</label>
                            <?php echo render_textarea('email_template_custom','',$template->message,array(),array(),'','tinymce-'.$proposal->id); ?>
                            <?php echo form_hidden('template_name',$template_name); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-default close-send-template-modal"><?php echo _l('close'); ?></button> -->
                    <input type="submit" id="btnSend" class="btn btn-info" value="Done">
                </div>
            </div>
        </div>
    </div>
    <?php

    init_tail();
    echo '<script>
    $(document).on("click", "#choose_temp", function() {
        $("#proposal_send_to_customer").modal({backdrop: "static", keyboard: false});
    });
    $(document).on("click", "#btnSend", function() {
        $("#proposal_send_to_customer").modal("hide");
    });

    $( ".savepdfTemplate" ).click(function() {
        var templateData ={};
        $("[contenteditable=\'true\']").each(function() {
            templateData [$(this).attr("data-content-name")] =$(this).html();
        });
        

        var saveTemplate =true;
        
        var data ={
            template:"proposalpdf_domestic",
            content:templateData,
            send:false
        }
        if($(this).hasClass("save-and-send")){
            if (confirm("Make sure you have choosed Proposal Template, Otherwise system will send Defalut Template.")) {
                data["send"] ={
                    subject :$("#email_sub").val(),
                    temp_id :$("#tempid").val(),
                    message :$("#email_template_custom").val(),
                }
                data ["saveandsend"] =true;
                saveTemplate =true;
			} else {
				saveTemplate =false;
			}
        }
        if(saveTemplate ==false){
            return ;
        }
        $.ajax({
            url: "'.base_url($this->uri->uri_string()).'",
            type: "post",
            data: data ,
            dataType: "json",
            success: function (response) {
                if(response.success ==true){
                    window.location.href = "'.admin_url('proposals#'.$proposal->id).'";
                    alert_float("success", response.responseText);
                }else{
                    alert_float("danger", response.responseText);
                }
            }
        });

    });
    </script>';
}else{
    $pdf->writeHTML($html, true, false, true, false, '');
}
