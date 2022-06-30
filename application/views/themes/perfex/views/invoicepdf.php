<?php

defined('BASEPATH') or exit('No direct script access allowed');

$invoicepdf = get_invoice_pdf_config();
//echo "<pre>"; print_r($invoicepdf); exit;
$dimensions = $pdf->getPageDimensions();

$info_right_column = '';
$info_left_column  = '';

$info_right_column .= '<span style="font-weight:bold;font-size:27px;">' . _l('invoice_pdf_heading') . '</span><br />';
$info_right_column .= '<b style="color:#4e4e4e;"># ' . $invoice_number . '</b>';

if (get_option('show_status_on_pdf_ei') == 1) {
    $info_right_column .= '<br /><span style="color:rgb(' . invoice_status_color_pdf($status) . ');text-transform:uppercase;">' . format_invoice_status($status, '', false) . '</span>';
}

if ($status != Invoices_model::STATUS_PAID && $status != Invoices_model::STATUS_CANCELLED && get_option('show_pay_link_to_invoice_pdf') == 1
    && found_invoice_mode($payment_modes, $invoice->id, false)) {
    $info_right_column .= ' - <a style="color:#84c529;text-decoration:none;text-transform:uppercase;" href="' . site_url('invoice/' . $invoice->id . '/' . $invoice->hash) . '"><1b>' . _l('view_invoice_pdf_link_pay') . '</1b></a>';
}

// Add logo
if(!empty($invoicepdf) && $invoicepdf->use_as_default == 1) {
    $info_left_column .= '<img width="150px" src="'.base_url($invoicepdf->inv_logo).'">';
} else {
    $info_left_column .= pdf_logo_url();
}

// Write top left logo and right column info/text
pdf_multi_row($info_left_column, $info_right_column, $pdf, 130);

$pdf->ln(10);

$organization_info = '<div style="color:#424242;">';

$organization_info .= format_organization_info();

$organization_info .= '</div>';

// Bill to
$invoice_info = '<b>' . _l('invoice_bill_to') . '</b>';
$invoice_info .= '<div style="color:#424242;">';
    $invoice_info .= format_customer_info($invoice, 'invoice', 'billing');
$invoice_info .= '</div>';

// ship to to
if ($invoice->include_shipping == 1 && $invoice->show_shipping_on_invoice == 1) {
    $invoice_info .= '<br /><b>' . _l('ship_to') . '</b>';
    $invoice_info .= '<div style="color:#424242;">';
    $invoice_info .= format_customer_info($invoice, 'invoice', 'shipping');
    $invoice_info .= '</div>';
}

// $invoice_info .= '<br />' . _l('invoice_data_date') . ' ' . _d($invoice->date) . '<br />';

// if (!empty($invoice->duedate)) {
//     $invoice_info .= _l('invoice_data_duedate') . ' ' . _d($invoice->duedate) . '<br />';
// }

// if ($invoice->sale_agent != 0 && get_option('show_sale_agent_on_invoices') == 1) {
//     $invoice_info .= _l('sale_agent_string') . ': ' . get_staff_full_name($invoice->sale_agent) . '<br />';
// }

// if ($invoice->project_id != 0 && get_option('show_project_on_invoice') == 1) {
//     $invoice_info .= _l('project') . ': ' . get_project_name_by_id($invoice->project_id) . '<br />';
// }
// if ($invoice->lead_id != 0 && get_option('show_project_on_invoice') == 1) {
//     $invoice_info .= _l('lead') . ': ' . get_lead_name_by_id($invoice->lead_id) . '<br />';
// }

// foreach ($pdf_custom_fields as $field) {
//     $value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
//     if ($value == '') {
//         continue;
//     }
//     $invoice_info .= $field['name'] . ': ' . $value . '<br />';
// }

// $left_info  = $swap == '1' ? $invoice_info : $organization_info;
// $right_info = $swap == '1' ? $organization_info : $invoice_info;

$invoice_info_left .= '<br />' . _l('invoice_data_date') . ' ' . _d($invoice->date) . '<br />';

if (!empty($invoice->duedate)) {
    $invoice_info_left .= _l('invoice_data_duedate') . ' ' . _d($invoice->duedate) . '<br />';
}

if ($invoice->sale_agent != 0 && get_option('show_sale_agent_on_invoices') == 1) {
    $invoice_info_left .= _l('sale_agent_string') . ': ' . get_staff_full_name($invoice->sale_agent) . '<br />';
}

if ($invoice->project_id != 0 && get_option('show_project_on_invoice') == 1) {
    $invoice_info_left .= _l('project') . ': ' . get_project_name_by_id($invoice->project_id) . '<br />';
}
if ($invoice->lead_id != 0 && get_option('show_project_on_invoice') == 1) {
    $invoice_info_left .= _l('lead') . ': ' . get_lead_name_by_id($invoice->lead_id) . '<br />';
}

foreach ($pdf_custom_fields as $field) {
    $value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
    if ($value == '') {
        continue;
    }
    $invoice_info_left .= $field['name'] . ': ' . $value . '<br />';
}

$left_info  = $swap == '1' ? $invoice_info_left : $invoice_info_left;
$right_info = $swap == '1' ? $organization_info : $invoice_info;

pdf_multi_row($left_info, $right_info, $pdf, 130);

// The Table
$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));

// The items table
$items = get_items_table_data($invoice, 'invoice', 'pdf');

//$tblhtml = $items->table();
$items_html = str_replace('width="38%"','width="25%"', $items->table());
$items_html .= '<br /><br />';
$items_html .= '';
$items_html .= '<table cellpadding="7" style="font-size:' . ($font_size + 4) . 'px">';

$itemLists = $invoice->items;
$subtotal = 0.00;
$discount = '';
$tax_txt = '';
$tax_val = '';
$i = 1;

foreach($itemLists as $pr) {
if($pr['method'] == 2 || $pr['method'] == 3) {
    if($pr['tax'] && $pr['tax'] > 0) {
        $dec1 = ($pr['tax'] / 100); //its convert 10 into 0.10
        $mult1 = $pr['total_price'] * $dec1; // gives the value for subtract from main value
        if($pr['method'] == 2) {
        $tax_txt .= '<p class="txt_'.$i.'"> Includes Tax ('.$pr['tax'].'%)</p>';
        }
        if($pr['method'] == 3) {
        $tax_txt .= '<p class="txt_'.$i.'"> Excludes Tax ('.$pr['tax'].'%)</p>';
        }
        $tax_val .= '<p class="amt_'.$i.'"> '.number_format($mult1,2).'</p>';
    }
    $i++; 
} 
$subtotal = $subtotal+$pr['total_price'];
if($pr['discount'] && $pr['discount'] > 0) {
    $dec = ($pr['discount'] / 100); //its convert 10 into 0.10
    $mult = ($pr['price']*$pr['quantity']) * $dec; // gives the value for subtract from main value
    $discount .= ' '.number_format($mult,2).',';
}
}
if($discount) {
$discount = '<small>(Includes discount of '.substr($discount,0,-1).')</small>'; 
}

$items_html .= '
<tr>
    <td align="right" width="85%"><strong>' . _l('estimate_subtotal') . ' ' . $discount . '</strong></td>
    <td align="right" width="15%">' . app_format_money($subtotal, $invoice->currency_name) . '</td>
</tr>';
if($tax_val) { 
    $items_html .= '<tr>
        <td align="right" width="85%">' . $tax_txt . '</td>
        <td align="right" width="15%">' . $tax_val . '</td>
    </tr>';
} 
$tblhtml = $items_html;
$pdf->writeHTML($tblhtml, true, false, false, false, '');

$pdf->Ln(1);

$tbltotal = '';
$tbltotal .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px">';
// $tbltotal .= '
// <tr>
//     <td align="right" width="85%"><strong>' . _l('invoice_subtotal') . '</strong></td>
//     <td align="right" width="15%">' . app_format_money($invoice->subtotal, $invoice->currency_name) . '</td>
// </tr>';

// if (is_sale_discount_applied($invoice)) {
//     $tbltotal .= '
//     <tr>
//         <td align="right" width="85%"><strong>' . _l('invoice_discount');
//     if (is_sale_discount($invoice, 'percent')) {
//         $tbltotal .= '(' . app_format_number($invoice->discount_percent, true) . '%)';
//     }
//     $tbltotal .= '</strong>';
//     $tbltotal .= '</td>';
//     $tbltotal .= '<td align="right" width="15%">-' . app_format_money($invoice->discount_total, $invoice->currency_name) . '</td>
//     </tr>';
// }

// foreach ($items->taxes() as $tax) {
//     $tbltotal .= '<tr>
//     <td align="right" width="85%"><strong>' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)' . '</strong></td>
//     <td align="right" width="15%">' . app_format_money($tax['total_tax'], $invoice->currency_name) . '</td>
// </tr>';
// }

if ((int) $invoice->adjustment != 0) {
    $tbltotal .= '<tr>
    <td align="right" width="85%"><strong>' . _l('invoice_adjustment') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($invoice->adjustment, $invoice->currency_name) . '</td>
</tr>';
}

$tbltotal .= '
<tr style="background-color:#f0f0f0;">
    <td align="right" width="85%"><strong>' . _l('invoice_total') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($invoice->total, $invoice->currency_name) . '</td>
</tr>';

if (count($invoice->payments) > 0 && get_option('show_total_paid_on_invoice') == 1) {
    $tbltotal .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('invoice_total_paid') . '</strong></td>
        <td align="right" width="15%">-' . app_format_money(sum_from_table(db_prefix().'invoicepaymentrecords', [
        'field' => 'amount',
        'where' => [
            'invoiceid' => $invoice->id,
        ],
    ]), $invoice->currency_name) . '</td>
    </tr>';
}

if (get_option('show_credits_applied_on_invoice') == 1 && $credits_applied = total_credits_applied_to_invoice($invoice->id)) {
    $tbltotal .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('applied_credits') . '</strong></td>
        <td align="right" width="15%">-' . app_format_money($credits_applied, $invoice->currency_name) . '</td>
    </tr>';
}

if (get_option('show_amount_due_on_invoice') == 1 && $invoice->status != Invoices_model::STATUS_CANCELLED) {
    $tbltotal .= '<tr style="background-color:#f0f0f0;">
       <td align="right" width="85%"><strong>' . _l('invoice_amount_due') . '</strong></td>
       <td align="right" width="15%">' . app_format_money($invoice->total_left_to_pay, $invoice->currency_name) . '</td>
   </tr>';
}

$tbltotal .= '</table>';
$pdf->writeHTML($tbltotal, true, false, false, false, '');

if (get_option('total_to_words_enabled') == 1) {
    // Set the font bold
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('num_word') . ': ' . $CI->numberword->convert($invoice->total, $invoice->currency_name), 0, 1, 'C', 0, '', 0);
    // Set the font again to normal like the rest of the pdf
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(4);
}

if (count($invoice->payments) > 0 && get_option('show_transactions_on_invoice_pdf') == 1) {
    $pdf->Ln(4);
    $border = 'border-bottom-color:#000000;border-bottom-width:1px;border-bottom-style:solid; 1px solid black;';
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('invoice_received_payments'), 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(4);
    $tblhtml = '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="0">
        <tr height="20"  style="color:#000;border:1px solid #000;">
        <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_number_heading') . '</th>
        <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_mode_heading') . '</th>
        <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_date_heading') . '</th>
        <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_amount_heading') . '</th>
    </tr>';
    $tblhtml .= '<tbody>';
    foreach ($invoice->payments as $payment) {
        $payment_name = $payment['name'];
        if (!empty($payment['paymentmethod'])) {
            $payment_name .= ' - ' . $payment['paymentmethod'];
        }
        $tblhtml .= '
            <tr>
            <td>' . $payment['paymentid'] . '</td>
            <td>' . $payment_name . '</td>
            <td>' . _d($payment['date']) . '</td>
            <td>' . app_format_money($payment['amount'], $invoice->currency_name) . '</td>
            </tr>
        ';
    }
    $tblhtml .= '</tbody>';
    $tblhtml .= '</table>';
    $pdf->writeHTML($tblhtml, true, false, false, false, '');
}

if (found_invoice_mode($payment_modes, $invoice->id, true, true)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('invoice_html_offline_payment'), 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);

    foreach ($payment_modes as $mode) {
        if (is_numeric($mode['id'])) {
            if (!is_payment_mode_allowed_for_invoice($mode['id'], $invoice->id)) {
                continue;
            }
        }
        if (isset($mode['show_on_pdf']) && $mode['show_on_pdf'] == 1) {
            $pdf->Ln(1);
            $pdf->Cell(0, 0, $mode['name'], 0, 1, 'L', 0, '', 0);
            $pdf->Ln(2);
            $pdf->writeHTMLCell('', '', '', '', $mode['description'], 0, 1, false, true, 'L', true);
        }
    }
}

if (!empty($invoice->clientnote)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('invoice_note'), 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', $invoice->clientnote, 0, 1, false, true, 'L', true);
}

// if (!empty($invoice->terms)) {
//     $pdf->Ln(4);
//     $pdf->SetFont($font_name, 'B', $font_size);
//     $pdf->Cell(0, 0, _l('terms_and_conditions'), 0, 1, 'L', 0, '', 0);
//     $pdf->SetFont($font_name, '', $font_size);
//     $pdf->Ln(2);
//     $pdf->writeHTMLCell('', '', '', '', $invoice->terms, 0, 1, false, true, 'L', true);
// }

if(!empty($invoicepdf) && $invoicepdf->use_as_default == 1) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('terms_and_conditions'), 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', $invoicepdf->tc, 0, 1, false, true, 'L', true);
    
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    //$pdf->Cell(0, 0, _l('thanks'), 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->Multicell(0,2,$invoicepdf->contact_details); 
    //$pdf->writeHTMLCell('', '', '', '', $invoicepdf->contact_details, 0, 1, false, true, 'L', true);

    
    
        // $pdf->SetFont($font_name, 'I', 8);
        // $pdf->SetTextColor(142, 142, 142);
        // $pdf->Cell(0, 70, $invoicepdf->tc, 0, false, 'C', 0, '', 0, false, 'T', 'B');
    
}