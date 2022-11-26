<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jQuery-QueryBuilder/dist/css/query-builder.default.min.css">
<style>
    .query-builder .rules-group-container {
        padding: 10px;
        padding-bottom: 6px;
        border: 1px solid #dee2e6;
        background: none;
    }
    #workflowQuerybuilder [data-add="group"]{
        margin-left: 5px;
    }

    #workflowQuerybuilder .form-control{
        margin: 5px;
    }

</style>
<div id="workflowQuerybuilder"></div>
<br>
<button type="submit" class="btn btn-primary" id="saveConditionConfig">Save</button>
<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        $('#saveConditionConfig').on('click', function() {
            var result = $('#workflowQuerybuilder').queryBuilder('getSQL','?');
            var config ={}
            var ruleswidget =$('#workflowQuerybuilder').queryBuilder('getRules');
            if (result.sql.length) {
                config ={
                    params:result.params,
                    sql:result.sql,
                    ruleswidget:ruleswidget
                }
            }
            $.ajax({
                    url: admin_url+'workflow/saveconfig/'+$('.tree .block.selected').attr('data-id'),
                    type: 'post',
                    data: config,
                    success: function(response) {
                        if(config.sql.length){
                            var description =`Check whether the following condition is true or false `;
                            workflowl.updateBlockContent($('.tree .block.selected').attr('data-id'),'',description,workflowl.queryBuilderString(ruleswidget));
                        }
                        alert_float('success', 'Setup saved successfully.');
                    }            
                });
            
        });

    });
</script>