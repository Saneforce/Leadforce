

document.addEventListener("DOMContentLoaded", function (event) {
    const container = document.querySelector('#workflowwrapper');

    let startY;
    let startX;
    let scrollLeft;
    let scrollTop;
    let isDown;

    container.addEventListener('mousedown', e => mouseIsDown(e));
    container.addEventListener('mouseup', e => mouseUp(e))
    container.addEventListener('mouseleave', e => mouseLeave(e));
    container.addEventListener('mousemove', e => mouseMove(e));

    function mouseIsDown(e) {
        isDown = true;
        startY = e.pageY - container.offsetTop;
        startX = e.pageX - container.offsetLeft;
        scrollLeft = container.scrollLeft;
        scrollTop = container.scrollTop;
    }
    function mouseUp(e) {
        isDown = false;
    }
    function mouseLeave(e) {
        isDown = false;
    }
    function mouseMove(e) {
        if (isDown) {
            e.preventDefault();
            //Move vertcally
            const y = e.pageY - container.offsetTop;
            const walkY = y - startY;
            container.scrollTop = scrollTop - walkY;

            //Move Horizontally
            const x = e.pageX - container.offsetLeft;
            const walkX = x - startX;
            container.scrollLeft = scrollLeft - walkX;
        }
    }

});

var workflowl =function(module){

    // for setup module
    var workflowlmodule =module;

    //for setup module triggers
    var workflowltriggers ={};

    var workflowmergefields ={};

    var moudleplacehoders ={};
    var whatsappVariables ={};
    var workflowlnotificationto={
        customer:'Send to customer',
        staff:'Send to staff',
    }

    workflowl.setTriggers =function(triggers){
        workflowltriggers =triggers;
    }

    workflowl.getTriggerBlock =function(slug,id){
        var block ='';
        if(workflowltriggers[slug]){
            trigger =workflowltriggers[slug];
            title =trigger.title;
            datatitle =trigger.title;
            description =trigger.description;

            
            block =`<div class="block" type="button" aria-pressed="false" data-js="node"`;
            block +=` data-id="`+id+`" data-name="`+slug+`" data-title="`+datatitle+`">
                <div class="block-content">
                    <div class="block-icon">
                        `+trigger.icon+`
                    </div>
                    <div>
                        <h5 class="block-title">`+title+`</h5>
                        <p class="block-description text-muted">`+trigger.description+`</p>
                    </div>
                </div>
            </div>  `;
        }
        
        return block;
    }

    workflowl.Init =function(flows){
        var title ='';
        var description ='';
        $('.tree').append(
            `<li>
                <div class="block selected rootnode" type="button" aria-pressed="true" data-id="0" data-js="node" data-name="`+workflowlmodule.name+`" data-title="`+workflowlmodule.title+`" style="text-align:center;padding:15px">
                    `+workflowlmodule.icon+`
                    `+workflowlmodule.title+`
                </div>
            </li>`
        );
        $.each(flows, function(key, flow) {
            var chosenNode =$('.tree .block[data-id="'+flow.parent_id+'"]').parent();
            if(chosenNode.children().siblings('ul').length  ==0){
                $(chosenNode).append('<ul></ul>');
            }

            trigger =workflowltriggers[flow.action];
            
            var block =`<li>`+workflowl.getTriggerBlock(flow.action,flow.id)+`</li>`;
            chosenNode.children().siblings('ul').append(block);

            if(flow.configure){
                if(trigger.type =='notification' && trigger.medium =='email'){
                    
                    if(flow.configure.sendto =='customer'){
                        var title ='Send to customer';
                    }else if(flow.configure.sendto =='staff'){
                        var title ='Send to staff';
                    }
                    var description =flow.configure.subject;
                    workflowl.updateBlockContent(flow.id,title,description);
                }else if(trigger.type =='notification' && trigger.medium =='whatsapp'){
                    if(flow.configure.sendto =='customer'){
                        var title ='Send to customer';
                    }else if(flow.configure.sendto =='staff'){
                        var title ='Send to staff';
                    }
                    var description =flow.configure.template;
                    workflowl.updateBlockContent(flow.id,title,'Template : <b>'+description+'</b>');
                }else if(flow.action =='approval_level'){
                    if(flow.configure){
                        description =`Assigned to `;
                        if(flow.configure.approver ==0 || flow.configure.approver=='0'){
                            description +=`<b>Reporting Level </b>`;
                        }else{
                            description +=`<b>`+$('#ApprovalConfig [name="approver"] option[value='+flow.configure.approver+']').html()+`</b>`;
                        }
                        workflowl.updateBlockContent(flow.id,'',description);
                    }
                }else if(flow.action =='condition'){
                    if(flow.configure){
                        description =`Check whether the following condition is true or false <b>`+flow.configure.sql+`</b>`;
                        workflowl.updateBlockContent(flow.id,'',description);
                    }
                }else if(flow.action =='lead_assign_staff'){
                    if(flow.configure){
                        description =`Assign staff to lead. <b>`+$('[name="type"] option[value="'+flow.configure.type+'"]').html()+`</b>`;
                        workflowl.updateBlockContent(flow.id,'',description);
                    }
                }
            }
        });

        $('#sidebarTriggers').collapse('show');
        workflowl.RenderTriggers();
        $('body').on('click', '.tree .block', function(e) {
            workflowl.selectNode($(this));
        });
        $('body').on('click', '.trigger', function(e) {
            workflowl.addChild($(this).attr('data-name'));
        });

        $('#deleteNode').click(function(){
            workflowl.deleteNode();
        })
    }

    workflowl.addChild =function(slug) {
        if (document.querySelector('.tree .selected')) {
            var parent_id =$('.tree .selected').attr('data-id');
            var service ='';
            $.ajax({
                url: admin_url+'workflow/addflow',
                type: "post",
                data: {
                    module: workflowlmodule.name,
                    action: slug,
                    service: service,
                    parent_id: parent_id
                },
                dataType: "json",
                success: function(response) {
                    if (response.success == true) {
                        var chosenNode =$('.tree .selected').parent();
                        if(chosenNode.children().siblings('ul').length  ==0){
                            $(chosenNode).append('<ul></ul>');
                        }
                        var block =`<li>`+workflowl.getTriggerBlock(slug,response.inserted_id)+`</li>`;
                        chosenNode.children().siblings('ul').append(block);
                        $('.block[data-id="'+response.inserted_id+'"]').trigger('click');
                    }else{
                        alert_float('danger', response.msg);
                    }
                },
            });
        }
    }

    workflowl.RenderTriggers =function(){
        var trigger_name =$('.tree .selected').attr('data-name');
        var allowed_triggers =[];
        if($('.tree .selected').hasClass('rootnode')){
            allowed_triggers =workflowlmodule.triggers;
        }else{
            allowed_triggers = workflowltriggers[trigger_name].triggers;
        }
        
        $('#sidebarTriggers').html('');
        if(allowed_triggers.length>0){
            $.each(allowed_triggers, function(key, triggername) {
                var trigger =workflowltriggers[triggername];                
                var html =`<li class="trigger" data-name="`+triggername+`">
                    <div class="trigger-icon">
                        `+trigger.icon+`
                    </div>
                    <div>
                        <h5 class="trigger-title">`+trigger.title+`</h5>
                        <p class="trigger-description text-muted">`+trigger.description+`</p>
                    </div>
                </li>  `;
    
                $('#sidebarTriggers').append(html);
            });
        }else{
            $('#sidebarTriggers').append(`<p class="text-muted">No triggers available.</p>`);
        }
           
    }

    workflowl.deleteNode =function() {
        var node = $('.tree .block.selected');
        if(node.hasClass('rootnode')){
            alert_float('warning', 'Could not delete the root node.');
        }else{
            var allchilds =$('li .block',node.parent());
            $.each(allchilds, function(key, child) {
                workflowl.deleteNodeById($(child).attr('data-id'));
            });
            workflowl.deleteNodeById(node.attr('data-id'));
            var ul =node.parent().parent();
            node.parent().remove();
            if(ul.children().length ==0){
                ul.remove();
            }

        }
    }

    workflowl.deleteNodeById =function(id){
        var triggername =$('.block[data-id="'+id+'"]').attr('data-name');
        $.ajax({
            url:admin_url+'workflow/deleteflow/'+id,
            type: "post",
        });
    }

    workflowl.selectNode =function(clicker) {
        // Hang on - do we need to do anything?
        if (clicker.attr('aria-pressed') === 'false') {
            workflowl.deselectNodes();
            clicker.attr('aria-pressed', 'true');
            clicker.addClass('selected');
        }
        $('#selectedBlockTitle').html($('.tree .block.selected').attr('data-title'));
        workflowl.RenderTriggers();
        workflowl.renderSettings();
    }

    workflowl.renderSettings =function(){
        var blockname =$('.tree .block.selected').attr('data-name');
        var flow_id =$('.tree .block.selected').attr('data-id');
        var flow ='';
        $.ajax({
            url:admin_url+'workflow/getflow/'+flow_id,
            type: "post",
            async:false,
            dataType: "json",
            success: function(response) {
                if (response.success == true) {
                    flow =response.data;
                }
            }
        });
        var trigger =workflowltriggers[blockname];
        $('.sidebar-setup').removeClass('show');
        $('#sidebarSettingsTitle').html("");
        if(trigger){
            if(trigger.type =='notification' && trigger.medium =='email'){
                if(flow.configure){
                    $('form#EmailConfig [name="sendto"]').val(flow.configure.sendto);
                    $('form#EmailConfig [name="subject"]').val(flow.configure.subject);
                    $('form#EmailConfig [name="fromname"]').val(flow.configure.fromname);
                    if(flow.configure.plaintext=='on'){
                        $('form#EmailConfig [name="plaintext"]').attr('checked','checked')
                    }
                    tinymce.get('message').setContent(flow.configure.message);
                }
                $('#sidebarSettingsTitle').html("Setup email template");
                $('#sidebarsetupemail').addClass('show');
            }else if(trigger.type =='notification' && trigger.medium =='whatsapp'){
                if(flow.configure){
                    workflowl.setWhatsappVariables(flow.configure.variables);
                    $('form#WhatsappConfig [name="sendto"]').val(flow.configure.sendto);
                    $('form#WhatsappConfig [name="template"]').val(flow.configure.template).trigger('change');

                    if(typeof flow.configure.header_variable != 'undefined'){
                        $('form#WhatsappConfig [name="header_variable"]').val(flow.configure.header_variable);
                    }
                    if(typeof flow.configure.header_media_link != 'undefined'){
                        $('form#WhatsappConfig [name="header_media_link"]').val(flow.configure.header_media_link);
                    }
                    if(typeof flow.configure.header_media_caption != 'undefined'){
                        $('form#WhatsappConfig [name="header_media_caption"]').val(flow.configure.header_media_caption);
                    }
                }
                $('#sidebarSettingsTitle').html("Setup whatsapp template");
                $('#sidebarsetupwhatsapp').addClass('show');
            }else if(blockname =='approval_level'){
                $('#ApprovalConfig [name="approver"] option[value="0"]').html(`Reporting Level `);
                if(flow.configure){
                    $('#ApprovalConfig [name="approver"] option[value="'+flow.configure.approver+'"]').attr('selected','selected');
                }
                $('#ApprovalConfig [name="approver"]').selectpicker('refresh');
                $('#sidebarSettingsTitle').html("Setup approval settings");
                $('#sidebarsetupapproval').addClass('show');
            }else if(blockname =='condition'){
                if(flow.configure){
                    $('#workflowQuerybuilder').queryBuilder('setRules', flow.configure.ruleswidget);
                }else{
                    $('#workflowQuerybuilder').queryBuilder('reset');

                }
                $('#sidebarSettingsTitle').html("Setup Conditions");
                $('#sidebarsetupcondition').addClass('show');
            }else if(blockname =='lead_assign_staff'){

                if(flow.configure){
                    $('#LeadAssignStaffConfig [name="type"]').val(flow.configure.type).trigger('change');
                    if(flow.configure.type=='direct_assign'){
                        $('#LeadAssignStaffConfig [name="assignto"]').val(flow.configure.assignto)
                        $('#LeadAssignStaffConfig [name="assignto"]').selectpicker('refresh');
                    }else if(flow.configure.type=='round_robin_method'){
                        $('#LeadAssignStaffConfig [name="stafftype"]').val(flow.configure.stafftype).trigger('change');

                        if(flow.configure.stafftype =='staff'){
                            $('#LeadAssignStaffConfig [name="assigntogroup[]"]').val(flow.configure.assigntogroup);
                            $('#LeadAssignStaffConfig [name="assigntogroup[]"]').selectpicker('refresh');
                        }else if(flow.configure.stafftype =='roles'){
                            $('#LeadAssignStaffConfig [name="assigntorole"]').val(flow.configure.assigntorole);
                        }else if(flow.configure.stafftype =='designation'){
                            $('#LeadAssignStaffConfig [name="assigntodesignation"]').val(flow.configure.assigntodesignation);
                        }
                        
                    }
                }

                $('#sidebarSettingsTitle').html("Assign User");
                $('#sidebarsetupleadstaffassign').addClass('show');   
            }else{
                $('#sidabarnosetup').addClass('show');
            }
        }else{
            $('#sidabarnosetup').addClass('show');
        }
    }

    workflowl.deselectNodes =function() {
        // This needs to run from scratch as new nodes might have been added
        var selectedBtns = [...document.querySelectorAll('.tree [aria-pressed="true"]')]
        // I mean, in theory, there should only be one selected button, but, you know, bugs...
        for (var i = 0; i < selectedBtns.length; i++) {
            selectedBtns[i].setAttribute('aria-pressed', 'false');
            selectedBtns[i].classList.remove('selected');
        }
    }

    workflowl.updateBlockContent =function (id , title, description){
        if(title !=''){
            $('.block[data-id="'+id+'"] .block-title').html(title);
        }
        if(description !=''){
            $('.block[data-id="'+id+'"] .block-description').html(description);
        }
    }

    workflowl.setPlaceHolders =function(placehoders){
        moudleplacehoders =placehoders;
    }

    workflowl.getPlaceHolderPicker =function (){
        var placeholdershtml =`
        <a type="button" class="dropdown-toggle add-placeholder-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-plus" aria-hidden="true"></i>Add placeholder
        </a>
        <div class="dropdown-menu"><div class="row">`;
            $.each(moudleplacehoders, function(field, fieldsData) {
                placeholdershtml +=`<div class="col-md-12"><h5>`+fieldsData.name+`</h5></div>`;
                $.each(fieldsData.placeholders, function(placeholder, placeholderName) {
                    placeholdershtml +=`<div class="col-md-6"><a class="dropdown-item click-to-copy" data-placeholder="`+placeholder+`" data-toggle="tooltip" data-placement="bottom" title="Click to add">`+placeholderName+`  </a></div>`;
                });
                placeholdershtml +=`<hr class="hr-panel-heading">`;
            });
            placeholdershtml +=`</div>
        </div>`;

        return placeholdershtml;
    }

    workflowl.setWhatsappVariables= function(variables){
        whatsappVariables = variables;
    }

    workflowl.getWhatsappVariables= function(){
        return whatsappVariables;
    }


}