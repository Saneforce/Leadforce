<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    #wrapper {
        background-color: #f8fafb;
    }

    .plugin {
        background-color: #fff;
        border-radius: 5px;
        padding: 20px 15px;
        box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
        margin-bottom: 15px;
        border: 1px solid #e6e6e6;
    }

    .plugin-logo {
        width: 100%;
    }

    .plugin-title h4 {
        margin-bottom: 5px !important;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .plugin-description p {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .plugin-tags .badge {
        border-radius: 5px;
        padding: 5px 10px;
        margin-bottom: 5px;
    }
</style>
<div id="wrapper">
    <div class="content">
        <h3 class="font-weight-bold">Integrations</h3>
        <p class="text-muted">Improve your workspace.</p>
        <div class="col-xs-12" style="margin-bottom: 20px;">
            <form class="form-inline my-2 my-lg-0 pull-right">
                <label for="searchTerm" class="sr-only">Search:</label>
                <input type="text" class="form-control mr-sm-2" name="searchTerm" id="searchTerm" placeholder="Search">
                <button class="btn btn-info my-2 my-sm-0" type="submit" id="searchButton">Search</button>
            </form>
        </div>
        <div class="row plugins-wrapper" id="integrationsContainer">
            <?php $this->load->view('admin/plugins/pluginslist') ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(document).ready(function() {
        $("form").submit(function(e) {
            e.preventDefault();
            var searchTerm = $("#searchTerm").val();
            $.ajax({
                type: "POST",
                url: "<?php echo admin_url('plugins/search'); ?>",
                data: {
                    searchTerm: searchTerm,
                },
                success: function(response) {
                    $("#integrationsContainer").html(response);
                }
            });
        });
    });
</script>
</body>

</html>