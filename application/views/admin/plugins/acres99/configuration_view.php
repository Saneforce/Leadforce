<div class="panel_s">
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-4">
                <p class="text-muted">Webhook URL</p>
            </div>
            <div class="col-xs-8">
                <p class=""><?php echo base_url('webhooks/acres99/lead/'.$configure_id) ?></p>
            </div>

            <div class="col-xs-4">
                <p class="text-muted">Method</p>
            </div>
            <div class="col-xs-8">
                <p class="">POST</p>
            </div>

            <div class="col-xs-4">
                <p class="text-muted">Format</p>
            </div>
            <div class="col-xs-8">
                <p class="">JSON</p>
            </div>

            <div class="col-xs-4">
                <p class="text-muted">Fields</p>
            </div>
            <div class="col-xs-8">
                <?php if ($web_form->form_data) {
                    $form_fields = array();
                    foreach (json_decode($web_form->form_data) as $field) {
                        if($field->name =='')
                            continue;
                        $form_fields[$field->name] = $field->label;
                    }
                    echo '<pre><code style="color: #c7254e;">'.trim(json_encode($form_fields, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)).'</code></pre>';
                } ?>
            </div>
        </div>
    </div>
</div>