<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="<?= admin_url('plugin/facebook/connectleadads') ?>" type="button" class="btn btn-primary" style="float:right; margin-bottom:15px;">
                        + Connect LeadAd
                        </a>

                        <!-- template records -->
                        <div>
                            <table class="table dt-table scroll-responsive">
                                <thead>
                                <tr>
                                    <th><?php echo _l('no'); ?></th>
                                    <th>User name</th>
                                    <th>Page name</th>
                                    <th>Lead Ad name</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($leadads as $key => $leadad):?>
                                    <tr>
                                        <td><?php echo $key+1 ?></td>
                                        <td data-order="<?php echo $leadad->config['facebookLoginName']; ?>"><?php echo $leadad->config['facebookLoginName']; ?></td>
                                        <td data-order="<?php echo $leadad->config['page']; ?>"><?php echo $leadad->config['page']; ?></td>
                                        <td data-order="<?php echo $leadad->config['form']; ?>"><?php echo $leadad->config['form']; ?></td>
                                        <td><a href="<?= admin_url('plugin/facebook/deleteleadad/'.$leadad->id) ?>" class="btn text-danger delete_link"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
</body>
</html>

