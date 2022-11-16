<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/App_items_table_template.php');

class App_items_table extends App_items_table_template
{
    public function __construct($transaction, $type, $for = 'html', $admin_preview = false)
    {
        // Required
        $this->type          = strtolower($type);
        $this->admin_preview = $admin_preview;
        $this->for           = $for;

        $this->set_transaction($transaction);
        $this->set_items($transaction->items);

        parent::__construct();
    }

    /**
     * Builds the actual table items rows preview
     * @return string
     */
    public function items()
    {
        $html          = '';
        // $custom_fields = $this->get_custom_fields_for_table();

        $i = 1;
        foreach ($this->items as $item) {

            $itemHTML = '';

            // Open table row
            $itemHTML .= '<tr nobr="true"' . $this->tr_attributes($item) . '>';

            // Table data number
            $itemHTML .= '<td' . $this->td_attributes() . ' align="center">' . $i . '</td>';

            $itemHTML .= '<td class="description" align="left;">';

            /**
             * Item description
             */
            // if (!empty($item['description'])) {
            //     $itemHTML .= '<span style="font-size:' . $this->get_pdf_font_size() . 'px;"><strong>' . $item['description'] . '</strong></span>';

            //     if (!empty($item['long_description'])) {
            //         $itemHTML .= '<br />';
            //     }
            // }

            /**
             * Item long description
             */
            //if (!empty($item['long_description'])) {
                //$itemHTML .= '<span style="color:#424242;">' . $item['long_description'] . '</span>';
            //}
            $itemHTML .= '<span style="color:#424242;">' . getItemName($item['productid']) . '</span>';

            $itemHTML .= '</td>';

            // for ordered particulars columns
            $CI = &get_instance();
            $CI->load->model('invoice_items_model');
            $details =$CI->invoice_items_model->get_particulars_ordered_details($item['productid']);
            $particulars_items_list_column_order = (array)json_decode(get_option('particulars_items_list_column'));
            if($particulars_items_list_column_order){
                foreach($particulars_items_list_column_order as $key => $detail){
                    if(isset($details->$key)){
                        $itemHTML .= '<td align="left">' .$details->$key . '</td>';
                    }
                    else{
                        $itemHTML .= '<td align="left"></td>';
                    }
                        
                }
            }
            /**
             * Item custom fields
             */
            // foreach ($custom_fields as $custom_field) {
            //     $itemHTML .= '<td align="left">' . get_custom_field_value($item['id'], $custom_field['id'], 'items') . '</td>';
            // }

            /**
             * Item quantity
             */
            if(isset($item['qty'])){
                $itemHTML .= '<td align="right">' . floatVal($item['qty']);
            }elseif(isset($item['quantity'])){
                $itemHTML .= '<td align="right">' . floatVal($item['quantity']);
            }else{
                $itemHTML .= '<td align="right">';
            }

            /**
             * Maybe item has added unit?
             */
            if ($item['unit']) {
                $itemHTML .= ' ' . $item['unit'];
            }

            $itemHTML .= '</td>';

            /**
             * Item rate
             * @var string
             */
            if(isset($item['price'])){
                $rate = hooks()->apply_filters(
                    'item_preview_rate',
                    app_format_money($item['price'], $this->transaction->currency_name, $this->exclude_currency()),
                    ['item' => $item, 'transaction' => $this->transaction]
                );
            }elseif(isset($item['rate'])){
                $rate = hooks()->apply_filters(
                    'item_preview_rate',
                    app_format_money($item['rate'], $this->transaction->currency_name, $this->exclude_currency()),
                    ['item' => $item, 'transaction' => $this->transaction]
                );
            }else{
                $rate =0;
            }

            $itemHTML .= '<td align="right">' . $rate . '</td>';

            /**
             * Items table taxes HTML custom function because it's too general for all features/options
             * @var string
             */
            $itemHTML .= '<td align="right">' . $item['tax'] . '%</td>';
            $itemHTML .= '<td align="right">' . $item['discount'] . '%</td>';
            //$itemHTML .= $this->taxes_html($item);

            /**
             * Possible action hook user to include tax in item total amount calculated with the quantiy
             * eq Rate * QTY + TAXES APPLIED
             */
            $item_amount_with_quantity = hooks()->apply_filters(
                'item_preview_amount_with_currency',
                app_format_money(($item['qty'] * $item['rate']), $this->transaction->currency_name, $this->exclude_currency()),
                $item,
                $this->transaction,
                $this->exclude_currency()
            );

            $itemHTML .= '<td class="amount" align="right">' . $item['total_price'] . '</td>';

            // Close table row
            $itemHTML .= '</tr>';

            $html .= $itemHTML;

            $i++;
        }

        return $html;
    }

    /**
     * Html headings preview
     * @return string
     */
    public function html_headings()
    {
        $html = '<tr>';
        $html .= '<th align="center">' . $this->number_heading() . '</th>';
        $html .= '<th class="description" width="20%" align="left">' . $this->item_heading() . '</th>';

        $CI = &get_instance();
        $CI->load->model('invoice_items_model');
        $table_data_temp = $CI->invoice_items_model->get_all_table_fields();
        $particulars_items_list_column_order = (array)json_decode(get_option('particulars_items_list_column'));
        if($particulars_items_list_column_order){
            foreach($particulars_items_list_column_order as $ckey => $cval){
                $html .= '<th class="custom_field" align="left">' . _l($table_data_temp[$ckey]['ll']) . '</th>';
            };
        }

        // $custom_fields = $this->get_custom_fields_for_table();
        // foreach ($custom_fields as $cf) {
        //     $html .= '<th class="custom_field" align="left">' . $cf['name'] . '</th>';
        // }

        $html .= '<th align="right">' . $this->qty_heading() . '</th>';
        $html .= '<th align="right">' . $this->rate_heading() . '</th>';
        if ($this->show_tax_per_item()) {
            $html .= '<th align="right">' . $this->tax_heading() . '</th>';
        }
        $html .= '<th align="right">' . $this->discount_heading() . '</th>';
        $html .= '<th align="right">' . $this->amount_heading() . '</th>';
        $html .= '</tr>';

        return $html;
    }

    /**
     * PDF headings preview
     * @return string
     */
    public function pdf_headings()
    {
        $item_width = 38;

        // If show item taxes is disabled in PDF we should increase the item width table heading
        $item_width = $this->show_tax_per_item() == 0 ? $item_width + 15 : $item_width;

        // $custom_fields_items = $this->get_custom_fields_for_table();
        // Calculate headings width, in case there are custom fields for items
        $total_headings = $this->show_tax_per_item() == 1 ? 4 : 3;
        // $total_headings += count($custom_fields_items);
        $headings_width = (100 - ($item_width + 6)) / $total_headings;

        $tblhtml = '<tr height="30" bgcolor="' . get_option('pdf_table_heading_color') . '" style="color:' . get_option('pdf_table_heading_text_color') . ';">';

        $tblhtml .= '<th width="5%;" align="center">' . $this->number_heading() . '</th>';
        $tblhtml .= '<th width="' . $item_width . '%" align="left">' . $this->item_heading() . '</th>';

        // foreach ($custom_fields_items as $cf) {
        //     $tblhtml .= '<th width="' . $headings_width . '%" align="left">' . $cf['name'] . '</th>';
        // }

        $tblhtml .= '<th width="' . $headings_width . '%" align="right">' . $this->qty_heading() . '</th>';
        $tblhtml .= '<th width="' . $headings_width . '%" align="right">' . $this->rate_heading() . '</th>';

        if ($this->show_tax_per_item()) {
            $tblhtml .= '<th width="' . $headings_width . '%" align="right">' . $this->tax_heading() . '</th>';
        }
        $tblhtml .= '<th width="' . $headings_width . '%" align="right">' . $this->discount_heading() . '</th>';

        $tblhtml .= '<th width="' . $headings_width . '%" align="right">' . $this->amount_heading() . '</th>';
        $tblhtml .= '</tr>';

        return $tblhtml;
    }
}
