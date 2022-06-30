<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="customer_file_share_file_with" data-total-contacts="<?php echo count($contacts); ?>" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('share_file_with'); ?></h4>
        </div>
        <div class="modal-body">
            <?php echo form_hidden('file_id'); ?>
            <?php echo render_select('share_contacts_id[]',$contacts,array('id',array('firstname','lastname')),'customer_contacts',array(get_primary_contact_user_id($client->userid)),array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="button" class="btn btn-info" onclick="do_share_file_contacts();"><?php echo _l('confirm'); ?></button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<h4 class="no-mtop bold"><?php echo _l('customer_attachments'); ?>
<br />
<small class="text-info"><?php //echo _l('customer_files_info_message'); ?></small>
</h4>
<hr />
<?php if(isset($product)){ ?>
    <?php echo form_open_multipart(admin_url('products/upload_attachment/'.$product->id),array('class'=>'dropzone','id'=>'client-attachments-upload')); ?>
    <input type="file" name="file" multiple />
    <?php echo form_close(); ?>
    <div class="text-right mtop15">
        <button class="gpicker" data-on-pick="customerGoogleDriveSave">
            <i class="fa fa-google" aria-hidden="true"></i>
            <?php echo _l('choose_from_google_drive'); ?>
        </button>
        <div id="dropbox-chooser"></div>
    </div>
    <div class="attachments">
        <div class="mtop25">

            <table class="table dt-table scroll-responsive" data-order-col="2" data-order-type="desc">
                <thead>
                    <tr>
                        <th width="50%"><?php echo _l('customer_attachments_file'); ?></th>
                        <!-- <th><?php echo _l('customer_attachments_show_in_customers_area'); ?></th> -->
                        <th><?php echo _l('file_date_uploaded'); ?></th>
                        <th><?php echo _l('options'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($attachments as $type => $attachment){
                        $download_indicator = 'id';
                        $key_indicator = 'productid';
                        $upload_path = get_upload_path_by_type($type);
                        if($type == 'files'){
                            $url = site_url() .'download/file/product/';
                            $download_indicator = 'attachment_key';
                        }
                        ?>
                        <?php foreach($attachment as $_att){
                            ?>
                            <tr id="tr_file_<?php echo $_att['id']; ?>">
                                <td>
                                   <?php
                                   $path = $upload_path . $_att[$key_indicator] . '/' . $_att['file_name'];
                                   $is_image = false;
                                    $attachment_url = $url . $_att[$download_indicator];
                                    $is_image = is_image($path);
                                    $img_url = site_url('download/preview_image?path='.protected_file_url_by_path($path,true).'&type='.$_att['filetype']);
                                    $lightBoxUrl = site_url('download/preview_image?path='.protected_file_url_by_path($path).'&type='.$_att['filetype']);
                                
                                if($is_image){
                                    echo '<div class="preview_image">';
                                }
                                ?>
                                <a href="<?php if($is_image){ echo isset($lightBoxUrl) ? $lightBoxUrl : $img_url; } else {echo $attachment_url; } ?>"<?php if($is_image){ ?> data-lightbox="customer-profile" <?php } ?> class="display-block mbot5">
                                    <?php if($is_image){ ?>
                                        <div class="table-image">
                                            <div class="text-center"><i class="fa fa-spinner fa-spin mtop30"></i></div>
                                            <img src="#" class="img-table-loading" data-orig="<?php echo $img_url; ?>">
                                        </div>
                                    <?php } else { ?>
                                     <i class="<?php echo get_mime_class($_att['filetype']); ?>"></i> <?php echo $_att['file_name']; ?>
                                 <?php } ?>
                             </a>
                             <?php if($is_image){ echo '</div>'; } ?>
                         </td>
                         <!-- <td>
                            <div class="onoffswitch"<?php if($type != 'customer'){?> data-toggle="tooltip" data-title="<?php echo _l('customer_attachments_show_notice'); ?>" <?php } ?>>
                                <input type="checkbox" <?php if($type != 'customer'){echo 'disabled';} ?> id="<?php echo $_att['id']; ?>" data-id="<?php echo $_att['id']; ?>" class="onoffswitch-checkbox customer_file" data-switch-url="<?php echo admin_url(); ?>misc/toggle_file_visibility" <?php if(isset($_att['visible_to_customer']) && $_att['visible_to_customer'] == 1){echo 'checked';} ?>>
                                <label class="onoffswitch-label" for="<?php echo $_att['id']; ?>"></label>
                            </div>
                            
                        </td> -->
                        <td data-order="<?php echo $_att['dateadded']; ?>"><?php echo _dt($_att['dateadded']); ?></td>
                        <td>
                                <!-- <button type="button" data-toggle="modal" data-file-name="<?php echo $_att['file_name']; ?>" data-filetype="<?php echo $_att['filetype']; ?>" data-path="<?php echo $path; ?>" data-target="#send_file" class="btn btn-info btn-icon"><i class="fa fa-envelope"></i></button> -->
                            
                                <a href="<?php echo admin_url('products/delete_attachment/'.$_att['productid'].'/'.$_att['id']); ?>"  class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
                        </td>
                    <?php
                } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</div>
<?php
include_once(APPPATH . 'views/admin/products/modals/send_file_modal.php');
} ?>
