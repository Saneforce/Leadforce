<?php

defined('BASEPATH') or exit('No direct script access allowed');
$dimensions = $pdf->getPageDimensions();

$pdf_logo_url = pdf_logo_url();
$pdf->writeHTMLCell(($dimensions['wk'] - ($dimensions['rm'] + $dimensions['lm'])), '', '', '', $pdf_logo_url, 0, 1, false, true, 'L', true);

$pdf->ln(1);
// Get Y position for the separation
$y = $pdf->getY();

$proposal_info = '<div style="color:#424242;">';
    $proposal_info .= format_organization_info();
$proposal_info .= '</div>';

$pdf->writeHTMLCell(($swap == '0' ? (($dimensions['wk'] / 2) - $dimensions['rm']) : ''), '', '', ($swap == '0' ? $y : ''), $proposal_info, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);

$rowcount = max([$pdf->getNumLines($proposal_info, 80)]);

// Proposal to
$client_details = '<b>' . _l('proposal_to') . '</b>';
$client_details .= '<div style="color:#424242;">';
    $client_details .= format_proposal_info($proposal, 'pdf');
$client_details .= '</div>';

//$this->Cell(20, 10, $client_details, 0, false, 'R', 0, '', 0, false, 'T', 'M');
$pdf->writeHTMLCell(($dimensions['wk'] / 2) - $dimensions['lm'], $rowcount * 7, 150, ($swap == '1' ? $y : ''), $client_details, 0, 1, false, true, ($swap == '1' ? 'J' : 'L'), true);

$pdf->ln(6);

$proposal_date = _l('proposal_date') . ': ' . _d($proposal->date);
$open_till     = '';

if (!empty($proposal->open_till)) {
    $open_till = _l('proposal_open_till') . ': ' . _d($proposal->open_till) . '<br />';
}

$qty_heading = _l('estimate_table_quantity_heading', '', false);

if ($proposal->show_quantity_as == 2) {
    $qty_heading = _l($this->type . '_table_hours_heading', '', false);
} elseif ($proposal->show_quantity_as == 3) {
    $qty_heading = _l('estimate_table_quantity_heading', '', false) . '/' . _l('estimate_table_hours_heading', '', false);
}

// The items table
$items = get_items_table_data($proposal, 'proposal', 'pdf')
        ->set_headings('estimate');
//pre($items);
$items_html = str_replace('width="38%"','width="25%"', $items->table());
$items_html .= '<br /><br />';
$items_html .= '';
$items_html .= '<table cellpadding="7" style="font-size:' . ($font_size + 4) . 'px">';

$itemLists = $proposal->items;
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
    <td align="right" width="15%">' . app_format_money($subtotal, $proposal->currency_name) . '</td>
</tr>';

if (is_sale_discount_applied($proposal)) {
    // $items_html .= '
    // <tr>
    //     <td align="right" width="85%"><strong>' . _l('estimate_discount');
    // if (is_sale_discount($proposal, 'percent')) {
    //     $items_html .= '(' . app_format_number($proposal->discount_percent, true) . '%)';
    // }
    // $items_html .= '</strong>';
    // $items_html .= '</td>';
    // $items_html .= '<td align="right" width="15%">-' . app_format_money($proposal->discount_total, $proposal->currency_name) . '</td>
    // </tr>';
}
if($tax_val) { 
    $items_html .= '<tr>
        <td align="right" width="85%">' . $tax_txt . '</td>
        <td align="right" width="15%">' . $tax_val . '</td>
    </tr>';
} 
foreach ($items->taxes() as $tax) {
    $items_html .= '<tr>
    <td align="right" width="85%"><strong>' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)' . '</strong></td>
    <td align="right" width="15%">' . app_format_money($tax['total_tax'], $proposal->currency_name) . '</td>
</tr>';
}

if ((int)$proposal->adjustment != 0) {
    $items_html .= '<tr>
    <td align="right" width="85%"><strong>' . _l('estimate_adjustment') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($proposal->adjustment, $proposal->currency_name) . '</td>
</tr>';
}
$items_html .= '
<tr style="background-color:#f0f0f0;">
    <td align="right" width="85%"><strong>' . _l('estimate_total') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($proposal->total, $proposal->currency_name) . '</td>
</tr>';
$items_html .= '</table>';

if (get_option('total_to_words_enabled') == 1) {
    $items_html .= '<br /><br /><br />';
    $items_html .= '<strong style="text-align:center;">' . _l('num_word') . ': ' . $CI->numberword->convert($proposal->total, $proposal->currency_name) . '</strong>';
}
$proposal->content = str_replace('{proposal_items}', $items_html, $proposal->content);

// Get the proposals css
// Theese lines should aways at the end of the document left side. Dont indent these lines
$html = <<<EOF
<p style="font-size:20px;"># $number
<br /><span style="font-size:15px;">$proposal->subject</span>
</p>
$proposal_date
<br />
$open_till
<div style="width:675px !important;">
$proposal->content
</div>
EOF;
$pdf->writeHTML($html, true, false, true, false, '');
