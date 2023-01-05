<script>
    var BASE_URL = "<?php echo base_url(); ?>";
</script>
<script>
    window.onscroll = function() {
        myFunction()
    };

    var header = document.getElementById("myHeader");
    var sticky = header.offsetTop;

    function myFunction() {
        if (window.pageYOffset > sticky) {
            header.classList.add("sticky");
        } else {
            header.classList.remove("sticky");
        }
    }

    function hide_div(a) {
        //$('#'+a).modal('hide');
    }

    function check_header() {

        $('#myHeader').hide();
        $("input:checkbox[class=check_mail]:checked").each(function() {
            $('#myHeader').show();
            var folder = $("#folder").val();
            $('#delete_ever').hide();
            $('#cur_delete').show();
            if (folder == '[Gmail]/Trash') {
                $('#delete_ever').show();
                $('#cur_delete').hide();
            }
        });
        var a = $("input[type='checkbox'][class=check_mail]");
        if (a.filter(":checked").length != a.length) {
            $("#select_all").prop('checked', false);
        } else {
            $("#select_all").prop('checked', true);
        }
    }

    function check_all(a) {
        $(".check_mail").prop('checked', false);
        if (a.checked == true) {
            $(".check_mail").prop('checked', true);
        }
        check_header();
    }

    $('#del_mail1').click(function() {
        var form = $("#formId");
        var folder = $("#folder").val();
        document.getElementById('overlay').style.display = '';
        var BASE_URL = "<?php echo base_url(); ?>";
        $.ajax({
            url: BASE_URL + 'admin/company_mail/delete_mail_all?folder=' + folder,
            type: 'POST',
            data: form.serialize(),
            success: function(data) {
                var results = JSON.parse(data);
                if (results.length > 0) {
                    for (var i = 0; i < results.length; i++) {
                        $('.' + results[0] + '_mail_row').hide();;
                    }
                }
                var pageno = $('#req_page').val();
                loadPagination(pageno);
                $('#myHeader').hide();
                alert_float('success', 'Selected Mail Deleted Successfully');
                //document.getElementById('overlay').style.display = 'none'; 
            }
        });
    });
    $('#del_mail').click(function() {
        var form = $("#formId");
        var folder = $("#folder").val();
        document.getElementById('overlay').style.display = '';
        var BASE_URL = "<?php echo base_url(); ?>";
        $.ajax({
            url: BASE_URL + 'admin/company_mail/trash?folder=' + folder,
            type: 'POST',
            data: form.serialize(),
            success: function(data) {
                var results = JSON.parse(data);
                if (results.length > 0) {
                    for (var i = 0; i < results.length; i++) {
                        $('.' + results[0] + '_mail_row').hide();;
                    }
                }
                var pageno = $('#req_page').val();
                loadPagination(pageno);
                $('#myHeader').hide();
                alert_float('success', 'Selected Mail Move To Trash Successfully');
                //document.getElementById('overlay').style.display = 'none'; 
            }
        });
    });
    $('#unread_mail').click(function() {
        document.getElementById('overlay').style.display = '';
        var form = $("#formId");
        var BASE_URL = "<?php echo base_url(); ?>";
        $.ajax({
            url: BASE_URL + 'admin/company_mail/unread',
            type: 'POST',
            data: form.serialize(),
            success: function(data) {
                var pag_no = $('#req_page').val();
                loadPagination(pag_no);
                $('#myHeader').hide();
                //window.location.href="";

            }
        });
    });
    $('#read_mail').click(function() {
        document.getElementById('overlay').style.display = '';
        var form = $("#formId");
        var BASE_URL = "<?php echo base_url(); ?>";
        $.ajax({
            url: BASE_URL + 'admin/company_mail/read_msg',
            type: 'POST',
            data: form.serialize(),
            success: function(data) {
                var pag_no = $('#req_page').val();
                loadPagination(pag_no);
                $('#myHeader').hide();
                //window.location.href="";

            }
        });
    });

    function edit_template(a) {
        var BASE_URL = "<?php echo base_url(); ?>";
        $.ajax({
            url: BASE_URL + 'admin/company_mail/edit_template',
            type: 'POST',
            data: {
                'template_id': a
            },
            success: function(data) {
                var json = $.parseJSON(data);
                //var text = tinyMCE.get('template_edit_description').getContent();

                $('#template_edit_name').val(json.template_name);
                $('#template_id1').val(json.id);
                $('#template_edit_description').val(json.description);
                tinyMCE.get('template_edit_description').setContent(json.description);

            }

        });
    }

    function deal_values() {
        $('#pipeselect').hide();
        $("#pipeline_id").append('');
        $('#pipeline_id').empty();
        $("#pipeline_id").selectpicker("refresh");
        var deal_map = $('#deal_map').val();
        var toemail = $('#toemail').val();
        var BASE_URL = "<?php echo base_url(); ?>";
        $.ajax({
            url: BASE_URL + 'admin/company_mail/deal_values',
            type: 'POST',
            data: {
                'toemail': toemail
            },
            success: function(data) {
                $('#pipeselect').hide();
                $("#pipeline_id").append(data);
                $("#pipeline_id").selectpicker("refresh");
                var deal_val = $('#pipeline_id').val();
                //$('#pipeselect').hide();

                if (data != '') {
                    $('#pipeselect').show();
                }
            }

        });
    }

    function del_template(a) {
        if (confirm('Are you want to delete this template')) {
            var BASE_URL = "<?php echo base_url(); ?>";
            $.ajax({
                url: BASE_URL + 'admin/company_mail/delete_template',
                type: 'POST',
                data: {
                    'template_id': a
                },
                success: function(data) {
                    $('.list_1' + a).hide();
                    gettemplate_list();
                }

            });
        }
    }
</script>
<script type="text/javascript">
    var frm1 = $('#template_form');

    frm1.submit(function(e) {

        e.preventDefault();
        //$('form').preventDoubleSubmission();
        $('.error1').hide();
        $.ajax({
            type: frm1.attr('method'),
            url: frm1.attr('action'),
            data: frm1.serialize(),
            success: function(data) {
                var json = $.parseJSON(data);
                if (json.status == 'success') {
                    gettemplate_list();
                    $(".tabs").removeClass("active");
                    $(".tabs h6").removeClass("font-weight-bold");
                    $(".tabs h6").addClass("text-muted");
                    $("#tab02").children("h6").removeClass("text-muted");
                    $("#tab02").children("h6").addClass("font-weight-bold");
                    $("#tab02").addClass("active");

                    current_fs = $(".active");

                    next_fs = "#tab021";

                    $("fieldset").removeClass("show");
                    $(next_fs).addClass("show");
                    alert_float('success', 'Template Created Successfully');
                    //alert('Template Created Successfully');
                } else {
                    if (json.name_error == 1) {
                        $('#name_error').show();
                    }
                    if (json.description_error == 1) {
                        $('#desc_error').show();
                    }
                }
            },
            error: function(data) {},
        });
    });
    var frm2 = $('#edit_template_form');

    frm2.submit(function(e) {

        e.preventDefault();
        //$('form').preventDoubleSubmission();
        $('.error1').hide();
        $.ajax({
            type: frm2.attr('method'),
            url: frm2.attr('action'),
            data: frm2.serialize(),
            success: function(data) {
                var json = $.parseJSON(data);
                if (json.status == 'success') {
                    $('#Edit-template').modal('hide');
                    gettemplate_list();
                    alert_float('success', 'Template Updated Successfully');
                    //a//lert('Template Updated Successfully');
                } else {
                    if (json.name_error == 1) {
                        $('#name_edit_error').show();
                    }
                    if (json.description_error == 1) {
                        $('#desc_edit_error').show();
                    }
                }
            },
            error: function(data) {},
        });
    });
    $(document).on('show.bs.modal', '.modal', function() {
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);



    });

    function tab_opon_popup() {
        $(".tabs").removeClass("active");
        $(".tabs h6").removeClass("font-weight-bold");
        $(".tabs h6").addClass("text-muted");
        $("#tab01").children("h6").removeClass("text-muted");
        $("#tab01").children("h6").addClass("font-weight-bold");
        $("#tab01").addClass("active");

        current_fs = $(".active");

        next_fs = "#tab011";
        $("fieldset").removeClass("show");
        $(next_fs).addClass("show");
        $('.list_files').html('');
        $('#m_files').html('');
        $('#toemail').val('');
        $('#toemail').val('');
        $('#toccemail').val('');
        $('#tobccemail').val('');
        $('#c_subject').val('');
        $('#mtotcnt').val(1);
        $('#mfilecnt').val(1);
        $('#mallcnt').val(0);
        $('#m_file').val('');
        check_email('', 'toemail');
        //tinyMCE.get('description').setContent('');
        $('#pipeselect').hide();
        $('#getFile').val('');
        $("#pipeline_id").append('');
        $('#pipeline_id').empty();
        $("#pipeline_id").selectpicker("refresh");
    }

    function template_description() {
        var text = tinyMCE.get('template_description').getContent();
        $('#template_description').val(text.trim());
    }

    function template_edit_description() {
        var text = tinyMCE.get('template_edit_description').getContent();
        $('#template_edit_description').val(text.trim());
    }

    function save_draft() {
        var to = $('#toemail').val();
        var c_subject = $('#c_subject').val();
        var draft = $('#cur_draft_id').val();
        var text = tinyMCE.get('description').getContent();
        if ((to != '' & text != '') || (to != '' & c_subject != '')) {
            $.ajax({
                url: BASE_URL + 'admin/company_mail/save_draft',
                type: 'POST',
                data: {
                    'to': to,
                    'subject': c_subject,
                    'text': text,
                    'draft': draft
                },
                success: function(data) {
                    $('#cur_draft_id').val(data);
                }

            });
        }
    }
    tinymce.init({
        selector: '#Edit-template textarea#template_edit_description',
        height: 100,
        menubar: true,
        plugins: [
            'advlist autolink lists charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount', 'image code', 'link',
            'emoticons template paste textcolor colorpicker textpattern imagetools', 'autosave'
        ],
        toolbar: 'fontselect fontsizeselect | forecolor backcolor | bold italic sizeselect | hr alignleft aligncenter alignright alignjustify | link image | link  | bullist numlist | restoredraft | code',
        fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
        setup: function(ed) {
            ed.on('keyup', function(e) {
                template_edit_description()
            });
        }
    });
    tinymce.init({
        selector: '#compose-modal textarea#description',
        height: 100,
        menubar: true,
        plugins: [
            'advlist autolink lists charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount', 'image code', 'link',
            'emoticons template paste textcolor colorpicker textpattern imagetools', 'autosave'
        ],
        toolbar: 'fontselect fontsizeselect | forecolor backcolor | bold italic sizeselect | hr alignleft aligncenter alignright alignjustify | link image | link  | bullist numlist | restoredraft | code',
        fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
        setup: function(ed) {
            ed.on('blur', function(e) {
                save_draft()
            });
        }
    });
    tinymce.init({
        selector: '#compose-modal textarea#template_description',
        height: 100,
        menubar: true,
        plugins: [
            'advlist autolink lists charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount', 'image code', 'link',
            'emoticons template paste textcolor colorpicker textpattern imagetools', 'autosave'
        ],
        toolbar: 'fontselect fontsizeselect | forecolor backcolor | bold italic sizeselect | hr alignleft aligncenter alignright alignjustify | link image | link  | bullist numlist | restoredraft | code',
        fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
        setup: function(ed) {
            ed.on('keyup', function(e) {
                template_description()
            });
        }
    });

    $(document).ready(function() {

        $(".tabs").click(function() {

            $(".tabs").removeClass("active");
            $(".tabs h6").removeClass("font-weight-bold");
            $(".tabs h6").addClass("text-muted");
            $(this).children("h6").removeClass("text-muted");
            $(this).children("h6").addClass("font-weight-bold");
            $(this).addClass("active");

            current_fs = $(".active");

            next_fs = $(this).attr('id');
            next_fs = "#" + next_fs + "1";

            $("fieldset").removeClass("show");
            $(next_fs).addClass("show");

            current_fs.animate({}, {
                step: function() {
                    current_fs.css({
                        'display': 'none',
                        'position': 'relative'
                    });
                    next_fs.css({
                        'display': 'block'
                    });
                }
            });
        });

    });
</script>
<script type='text/javascript'>
    function check_email(a, c_id) {

        var req_val = $('#' + c_id).val();
        var newStr = req_val.substring(0, req_val.length - 1);
        var check_str = newStr.substring(newStr.length - 4);
        var cur_val = a.substr(a.length - 1);
        var e = event.keyCode;
        if ((check_str.includes(".com") || check_str.includes(".net") || check_str.includes(".in")) && (e != 8) && e != 188) {
            var req_out = newStr + ',' + cur_val;
            $('#' + c_id).val(req_out);
        }
    }

    $(document).ready(function() {
        
        $("#toemail").autocomplete({

            source: function(request, response) {
                var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL + 'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                var terms = split($('#toemail').val());

                terms.pop();

                terms.push(ui.item.value);
                terms.push("");
                $('#toemail').val(terms);
                //$('#toemail').val(terms.join( ", " ));

                // Id
                var terms = split($('#selectuser_ids').val());

                terms.pop();
                var req_out = $('#toemail').val();
                req_out = ',' + req_out;

                //terms.push( req_out );
                terms.push(ui.item.value);
                var deal_map = $('#deal_map').val();
                if (deal_map == 'if more than one open deal – allow to map manually') {
                    deal_values();
                }
                terms.push("");
                var trim = req_out.replace(/(^,)|(,$)/g, "");
                //$('#selectuser_ids').val(terms.join( ", " ));
                $('#toemail').val(trim);
                $('#selectuser_ids').val(trim);

                return false;
            },
            minLength: 3
        });
        
        $("#toccemail").autocomplete({

            source: function(request, response) {
                var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL + 'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                var terms = split($('#toccemail').val());

                terms.pop();

                terms.push(ui.item.value);

                terms.push("");
                $('#toccemail').val(terms);
                //$('#toccemail').val(terms.join( ", " ));
                var req_out = $('#toccemail').val();
                req_out = ',' + req_out;

                // Id
                var terms = split($('#selectuser_ids').val());

                terms.pop();

                terms.push(ui.item.value);

                terms.push("");
                var trim = req_out.replace(/(^,)|(,$)/g, "");
                //$('#selectuser_ids').val(terms.join( ", " ));
                $('#toccemail').val(trim);
                $('#selectuser_ids').val(trim);

                return false;
            },
            minLength: 3
        });
        $("#tobccemail").autocomplete({

            source: function(request, response) {
                var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL + 'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                var terms = split($('#tobccemail').val());

                terms.pop();

                terms.push(ui.item.value);

                terms.push("");
                $('#tobccemail').val(terms);
                //$('#tobccemail').val(terms.join( ", " ));

                var req_out = $('#tobccemail').val();
                req_out = ',' + req_out;

                // Id
                var terms = split($('#selectuser_ids').val());

                terms.pop();

                terms.push(ui.item.value);

                terms.push("");
                var trim = req_out.replace(/(^,)|(,$)/g, "");
                //$('#selectuser_ids').val(terms.join( ", " ));
                $('#tobccemail').val(trim);
                $('#selectuser_ids').val(trim);

                return false;
            },
            minLength: 3
        });
        
        $("#forward_toemail").autocomplete({

            source: function(request, response) {
                var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL + 'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                var terms = split($('#forward_toemail').val());

                terms.pop();

                terms.push(ui.item.value);

                terms.push("");
                $('#forward_toemail').val(terms);
                //$('#forward_toemail').val(terms.join( ", " ));
                var req_out = $('#forward_toemail').val();
                req_out = ',' + req_out;
                // Id
                var terms = split($('#selectuser_ids').val());

                terms.pop();

                terms.push(ui.item.value);

                terms.push("");
                //$('#selectuser_ids').val(terms.join( ", " ));
                var trim = req_out.replace(/(^,)|(,$)/g, "");
                //$('#selectuser_ids').val(terms.join( ", " ));
                $('#forward_toemail').val(trim);
                $('#selectuser_ids').val(trim);

                return false;
            },
            minLength: 3
        });
        $("#forward_ccemail").autocomplete({

            source: function(request, response) {
                var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL + 'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                var terms = split($('#forward_ccemail').val());

                terms.pop();

                terms.push(ui.item.value);

                terms.push("");
                $('#forward_ccemail').val(terms);
                //$('#forward_ccemail').val(terms.join( ", " ));
                var req_out = $('#forward_ccemail').val();
                req_out = ',' + req_out;

                // Id
                var terms = split($('#selectuser_ids').val());

                terms.pop();

                terms.push(ui.item.value);

                terms.push("");
                //$('#selectuser_ids').val(terms.join( ", " ));
                var trim = req_out.replace(/(^,)|(,$)/g, "");
                //$('#selectuser_ids').val(terms.join( ", " ));
                $('#forward_ccemail').val(trim);
                $('#selectuser_ids').val(trim);

                return false;
            },
            minLength: 3
        });
        $("#forward_bccemail").autocomplete({

            source: function(request, response) {
                var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL + 'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                var terms = split($('#forward_bccemail').val());

                terms.pop();

                terms.push(ui.item.value);

                terms.push("");
                $('#forward_bccemail').val(terms);
                //$('#forward_bccemail').val(terms.join( ", " ));
                var req_out = $('#forward_bccemail').val();
                req_out = ',' + req_out;

                // Id
                var terms = split($('#selectuser_ids').val());

                terms.pop();

                terms.push(ui.item.value);

                terms.push("");
                //$('#selectuser_ids').val(terms.join( ", " ));
                var trim = req_out.replace(/(^,)|(,$)/g, "");
                //$('#selectuser_ids').val(terms.join( ", " ));
                $('#forward_bccemail').val(trim);
                $('#selectuser_ids').val(trim);

                return false;
            },
            minLength: 3
        });
        $("#reply_ccemail").autocomplete({

            source: function(request, response) {
                var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL + 'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                var terms = split($('#reply_ccemail').val());

                terms.pop();

                terms.push(ui.item.value);

                terms.push("");
                $('#reply_ccemail').val(terms);
                //$('#reply_ccemail').val(terms.join( ", " ));
                var req_out = $('#reply_ccemail').val();
                req_out = ',' + req_out;

                // Id
                var terms = split($('#selectuser_ids').val());

                terms.pop();

                terms.push(ui.item.value);

                terms.push("");
                //$('#selectuser_ids').val(terms.join( ", " ));
                var trim = req_out.replace(/(^,)|(,$)/g, "");
                //$('#selectuser_ids').val(terms.join( ", " ));
                $('#reply_ccemail').val(trim);
                $('#selectuser_ids').val(trim);

                return false;
            },
            minLength: 3
        });
        $("#reply_bccemail").autocomplete({

            source: function(request, response) {
                var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL + 'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                var terms = split($('#reply_bccemail').val());

                terms.pop();

                terms.push(ui.item.value);

                terms.push("");
                $('#reply_bccemail').val(terms);
                //$('#reply_bccemail').val(terms.join( ", " ));
                var req_out = $('#reply_bccemail').val();
                req_out = ',' + req_out;

                // Id
                var terms = split($('#selectuser_ids').val());

                terms.pop();

                terms.push(ui.item.value);

                terms.push("");
                //$('#selectuser_ids').val(terms.join( ", " ));
                var trim = req_out.replace(/(^,)|(,$)/g, "");
                //$('#selectuser_ids').val(terms.join( ", " ));
                $('#reply_bccemail').val(trim);
                $('#selectuser_ids').val(trim);

                return false;
            },
            minLength: 3
        });
    });

    function split(val) {
        return val.split(/,\s*/);
    }

    function extractLast(term) {
        return split(term).pop();
    }
</script>