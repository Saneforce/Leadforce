function createActivityForivr(data){
    $.ajax({
        type: "POST",
        url: admin_url+'call_settings/createActivity',
        data: data,
        dataType: 'json',
        success: function(result1){
            if(result1.status == 'success') {
                alert_float('success', 'Call Connecting...');
                setTimeout(function(){
                    window.location.reload();
                },1000);
                document.getElementById('overlay_12').style.display = 'none'; 
            } else {
                alert_float('warning', result1.message);
                setTimeout(function(){
                    window.location.reload();
                },1000);
                document.getElementById('overlay_12').style.display = 'none'; 
            }
        }
    });
}
function callTeleCmi(data){
    $.ajax({
        type: "POST",
        url: 'https://piopiy.telecmi.com/v1/adminConnect',
        contentType: "application/json",
        data: JSON.stringify({
            agent_id:data.agent_id,
            token:data.token,
            to:data.to,
            custom:''
        }),
        dataType: 'json',
        async: false,
        success: function(res){
            if(res.code == '200') {
                var request = res.request_id;
                var msg = res.msg;
                var code = res.code;
                createActivityForivr({req:request,msg:msg,code:code,deal_id:data.deal_id,contact_id:data.contact_id,type:data.type,agent:data.agent_id,to:data.to});
            }
        }
    });
}

function callTeleCmiSoftphone(data){
    if(data.channel =='national_softphone'){
        var url = 'https://rest.telecmi.com/v2/ind/click2call';
    }else{
        var url = 'https://rest.telecmi.com/v2/click2call';
    }
    to =String(data.calling_code)+String(data.to);
    $.ajax({
        url: url,
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({
            token: telecmi_get_agent_token(data.agent_id,data.password),
            to: parseInt(to),
        }),
        dataType: 'json',
        async: false,
        success: function(res){
            if(res.code == '200') {
                var request = res.request_id;
                var msg = res.msg;
                var code = res.code;
                createActivityForivr({req:request,msg:msg,code:code,deal_id:data.deal_id,contact_id:data.contact_id,type:data.type,agent:data.agent_id,to:to});
            }else{
                alert_float('warning', 'Call Not Connected');
                setTimeout(function(){
                    document.getElementById('overlay_12').style.display = 'none'; 
                    window.location.reload();
                },1000);
            }
        }
    });
}

function calldaffy(to_no,phone,contact_id,deal,agent_id,ftype,cur_val){
	document.getElementById('overlay_12').style.display = '';
	var url1 =  'https://portal.daffytel.com/api/v2/voice/c2c';
	 $.ajax({
		type: "POST",
		url: url1,
		contentType: "application/json",
		data: JSON.stringify({
			from:cur_val.code+phone,
			to:cur_val.code+to_no,
			bridge:cur_val.code+cur_val.app_secret,
			record:1,
			webhook_id:cur_val.webhook
		}),
		dataType: 'json',
		headers: {
			 "Authorization": "Bearer "+cur_val.app_id,
             "Accept": "application/json"
		},
		success: function(result){
			console.info(result);
			var result2 = JSON.parse(JSON.stringify(result));
			if(result2.status!='ERROR'){
                createActivityForivr({req:'',msg:result2.message,code:'200',deal_id:deal,contact_id:contact_id,type:ftype,agent:agent_id,to:to_no,token:''});
			}else{
				alert_float('warning', result2.message);
				setTimeout(function(){
					document.getElementById('overlay_12').style.display = 'none'; 
					window.location.reload();
				},1000);
			}
		},
		error: function(xhr, status, error) {
		  alert_float('warning', 'Call Not Connected');
			setTimeout(function(){
				document.getElementById('overlay_12').style.display = 'none'; 
				window.location.reload();
			},1000);
		}
	 });
}
function calltata(to_no,phone,contact_id,deal,agent_id,ftype,cur_val){
	document.getElementById('overlay_12').style.display = '';
	var call_app_token	=	$('#call_app_token').val();
	var loginid			=	$('#call_app_id').val();
	var secret			=	$('#call_app_secret').val();
	if(cur_val==''){
		if(call_app_token == ''){
			var url1 =  'https://api-smartflo.tatateleservices.com/v1/auth/login';
		}else{
			var url1 =  'https://api-smartflo.tatateleservices.com/v1/auth/refresh';
		}
	}else{
		var url1 =  'https://api-smartflo.tatateleservices.com/v1/auth/login';
	}
	var token = header = "";
	if(call_app_token !=''){
		token = "token:"+call_app_token;
		header = 'headers: {"Authorization": '+call_app_token+'}';
	}
	 $.ajax({
		type: "POST",
		url: url1,
		contentType: "application/json",
		data: JSON.stringify({
			email:loginid,
			token,
			password:secret
		}),
		header,
		dataType: 'json',
		async: false,
		success: function(res){
			var res1 = JSON.parse(JSON.stringify(res));
			if(res1.success) {
				 var url2 =  'https://api-smartflo.tatateleservices.com/v1/click_to_call';
				 $.ajax({
					type: "POST",
					url: url2,
					contentType: "application/json",
					data: JSON.stringify({
						agent_number:phone,
						destination_number:to_no
					}),
					dataType: 'json',
					headers: {
						 "Authorization": "Bearer "+res1.access_token
					},
					success: function(result){
						var result2 = JSON.parse(JSON.stringify(result));
                        createActivityForivr({req:'',msg:result2.message,code:'200',deal_id:deal,contact_id:contact_id,type:ftype,agent:agent_id,to:to_no,token:res1.access_token});
					},
					error: function(xhr, status, error) {
					  alert_float('warning', 'Call Not Connected');
						setTimeout(function(){
							document.getElementById('overlay_12').style.display = 'none'; 
							window.location.reload();
						},1000);
					}
				 });
			}
			else{
				tataupdate_access_token_call(to_no,phone,contact_id,deal,agent_id,ftype);
			}
		},
		error: function(xhr, status, error) {
			var call_app_token	=	$('#call_app_token').val();
			if(call_app_token==''){
				tataupdate_access_token_call(to_no,phone,contact_id,deal,agent_id,ftype);
			}else{
				alert_float('warning', 'Invalid Credentials');
				setTimeout(function(){
					document.getElementById('overlay_12').style.display = 'none'; 
					window.location.reload();
				},1000);
			}
		}
	 });
}
function tataupdate_access_token_call(to_no,phone,contact_id,deal,agent_id,ftype){
	var url13 =  admin_url+'call_settings/updatetoken';
	$.ajax({
		type: "POST",
		url: url13,
		data: {
			token:'',
		},
		dataType: 'json',
		success: function(result){
			var msg1 = JSON.parse(JSON.stringify(result));
			if(msg1.access_token ==''){
				calltata(to_no,phone,contact_id,deal,agent_id,ftype,1)
				
			}else{
				alert_float('warning', 'Invalid Credentials');
				setTimeout(function(){
					window.location.reload();
				},1000);
			}
		}
	});
}

function telecmi_get_agent_token(agent_id,password){
    var token ='';
    $.ajax({
        type: "POST",
        url: 'https://rest.telecmi.com/v2/user/login',
        data: {
            id: agent_id,
            password: password
        },
        dataType: 'json',
        async:false,
        success: function(res){
            if(res.token){
                token =res.token;
            }
        }
    });
    return token;
}
function callfromperson(contact, phone,calling_code) {
    var url =  admin_url+'call_settings/getPersonDeals';
    $.ajax({
        type: "POST",
        url: url,
        data: {contact:contact,phone:phone,listOwn:true},
        dataType: 'json',
        success: function(msg){
            if(msg.status == 'success') {
                if(msg.cnt > 0) {
                    $('#call_person_modal').modal('show');
                    var groupFilter = $('#deals_list');
					groupFilter.selectpicker('val', '');
					groupFilter.find('option').remove();
					groupFilter.selectpicker("refresh");
                    $('#deals_list').append(msg.result);
                    $('#deals_list').selectpicker('refresh');
                    $('#con_id').val(msg.contactId);
                    $('#contact_no').val(msg.contactNumber);
                    $('#calling_code').val(calling_code);
                } else {
                    var deal = msg.pid;
                    var contact = msg.contactId;
                    var contact_no = msg.contactNumber;
                    var ftype = '';
                    if (confirm('Do you want to Make Call?')) {
                        //alert(contact_no); alert(deal);
                        var url =  admin_url+'call_settings/getAppAgentDetails';
						var call_source_from =  $('#call_source_from').val();
                        //$('.followers-div').show();
                        $.ajax({
                            type: "POST",
                            url: url,
                            data: {contact_no:contact_no},
                            dataType: 'json',
                            success: function(msg){
								var call_source_from =  $('#call_source_from').val();
								if(call_source_from =='telecmi'){
									if(msg.status == 'success') {
										if(msg.channel =='international_softphone' || msg.channel =='national_softphone'){
											callTeleCmiSoftphone({channel:msg.channel,agent_id:msg.agent_id,password:msg.password,token:msg.app_secret,to:msg.contact_no,deal_id:0,contact_id:contact,type:ftype,calling_code:calling_code});
										}else{
											callTeleCmi({agent_id:msg.agent_id,token:msg.app_secret,to:msg.contact_no,deal_id:0,contact_id:contact,type:ftype});
										}
									}
								}
								else  if(call_source_from =='daffytel'){
									calldaffy(phone,msg.agent_no,contact,deal,msg.agent_id,'',msg);
								}else{
									console.info(msg);
									calltata(phone,msg.agent_no,contact,deal,msg.agent_id,'','');
								}
                                console.log(msg);
                            }
                        });
						
                    }
                }
            } else {
                $('#con_id').val('');
                $('#contact_no').val('');
                alert(msg.result);
            }
        }
    });
}
function clicktocall_create() {
    var deal = $('#deals_list').val();
    var contact = $('#con_id').val();
    var contact_no = $('#contact_no').val();
    var phone = $('#contact_no').val();
    var calling_code = $('#calling_code').val();
    var ftype = '';
    if (confirm('Do you want to Make Call?')) {
        //alert(contact_no); alert(deal);
        var url =  admin_url+'call_settings/getAppAgentDetails';
        //$('.followers-div').show();
        $.ajax({
            type: "POST",
            url: url,
            data: {contact_no:contact_no},
            dataType: 'json',
            success: function(msg){
				var call_source_from =  $('#call_source_from').val();
				if(call_source_from =='telecmi'){
					if(msg.status == 'success') {
						if(msg.channel =='international_softphone' || msg.channel =='national_softphone'){
							callTeleCmiSoftphone({channel:msg.channel,agent_id:msg.agent_id,password:msg.password,token:msg.app_secret,to:msg.contact_no,deal_id:deal,contact_id:contact,type:ftype,calling_code:calling_code});
						}else{
							callTeleCmi({agent_id:msg.agent_id,token:msg.app_secret,to:msg.contact_no,deal_id:deal,contact_id:contact,type:ftype});
						}
					}
				}
				else  if(call_source_from =='daffytel'){
					calldaffy(phone,msg.agent_no,contact,deal,msg.agent_id,'',msg);
				}else{
					console.info(msg);
					calltata(phone,msg.agent_no,contact,deal,msg.agent_id,'','');
				}
                console.log(msg);
            }
        });
    }

}
function callfromdeal(contact, deal, contact_no, ftype,calling_code) {

    if (confirm('Do you want to Make Call?')) {
        //alert(contact_no); alert(deal);
        var url =  admin_url+'call_settings/getAppAgentDetails';
        //$('.followers-div').show();
        $.ajax({
            type: "POST",
            url: url,
            data: {contact_no:contact_no},
            dataType: 'json',
            success: function(msg){
				var call_source_from =  $('#call_source_from').val();
				if(call_source_from =='telecmi'){
					if(msg.status == 'success') {
						var to1 = msg.contact_no;
						var agent1 = msg.agent_id;
						if(msg.channel =='international_softphone' || msg.channel =='national_softphone'){
							callTeleCmiSoftphone({channel:msg.channel,agent_id:msg.agent_id,password:msg.password,token:msg.app_secret,to:msg.contact_no,deal_id:deal,contact_id:contact,type:ftype,calling_code:calling_code});
						}else{
							callTeleCmi({agent_id:msg.agent_id,token:msg.app_secret,to:msg.contact_no,deal_id:deal,contact_id:contact,type:ftype});
						}
					}
				}else  if(call_source_from =='daffytel'){
					calldaffy(contact_no,msg.agent_no,contact,deal,msg.agent_id,ftype,msg);
				}else{
					calltata(contact_no,msg.agent_no,contact,deal,msg.agent_id,ftype,'');
				}
                console.log(msg);
            }
        });
    }
}

function tele_delete_agent_db(id,dbdel) {
	var url =  admin_url+'call_settings/getAgentDetails';
	//$('.followers-div').show();
	$.ajax({
		type: "POST",
		url: url,
		data: {id:id},
		dataType: 'json',
		success: function(msg){
			//alert(msg.status);
			if(msg.status) {
                if(msg.channel =='international_softphone' || msg.channel =='national_softphone'){
                    var url2 ='https://rest.telecmi.com/v2/user/remove';
                }else{
				    var url2 = 'https://piopiy.telecmi.com/v1/agent/remove';
                }

                
                //$('.followers-div').show();
                $.ajax({
                    type: "POST",
                    url: url2,
                    contentType: "application/json",
                    data: JSON.stringify({
                        id:msg.agent_id,
                        appid:parseInt(msg.app_id),
                        secret:msg.app_secret
                    }),
                    dataType: 'json',
                    async: false,
                    success: function(res){
                        if(res.code == 'cmi-200' || res.code == '200') {

                            if(dbdel==true){
                                deleteagentfromdb(id);
                            }else{
                                var url3 =  admin_url+'call_settings/delete_agent';
                                //$('.followers-div').show();
                                $.ajax({
                                    type: "POST",
                                    url: url3,
                                    data: {
                                        id:id
                                    },
                                    dataType: 'json',
                                    success: function(result){
                                        if(result.status == 'success') {
                                            alert_float('success', result.msg);
                                            setTimeout(function(){
                                                window.location.reload();
                                            },1000);
                                        } else {
                                            alert_float('warning', result.msg);
                                            setTimeout(function(){
                                                window.location.reload();
                                            },1000);
                                        }
                                    }
                                });
                            }
                            //alert(res.msg);
                        }
                        else{
                            if(dbdel==true){
                                deleteagentfromdb(id);
                            }
                            alert_float('warning','Please Delete Manually on Telecmi Portal or sync agents');
                            setTimeout(function(){
                                window.location.reload();
                            },1000);
                        }
                        
                    }
                });

			} 
			else {
				alert_float('warning', msg.msg);
				setTimeout(function(){
					window.location.reload();
				},1000);
			}
		},
		error: function(xhr, status, error) {
			console.info(error);
		}
	});
    //return true;
}
function tatadeletAgent_1_db(id,cur_val,dbdel) {
	//if (confirm('Do you want to Deactivate this Agent?')) {
	var url =  admin_url+'call_settings/getAgentDetails';
	//$('.followers-div').show();
	$.ajax({
		type: "POST",
		url: url,
		data: {id:id},
		dataType: 'json',
		success: function(msg){
			var call_app_token =  $('#call_app_token').val();
			var msg1 = JSON.parse(JSON.stringify(msg));
			console.info(msg1);
			 if(msg1.phone) {
				 var edit_phone = msg1.phone;
				//var url2 = 'https://piopiy.telecmi.com/v1/agent/remove';
				if(cur_val == ''){
					if(call_app_token==''){
						var url2 = 'https://api-smartflo.tatateleservices.com/v1/auth/login';
					}else{
						var url2 = 'https://api-smartflo.tatateleservices.com/v1/auth/refresh';
					}
					}else{
						var url2 = 'https://api-smartflo.tatateleservices.com/v1/auth/login';
					}
					var token = header = '';
					if(call_app_token!=''){
						token = "token:"+call_app_token;
						header = 'headers: {"Authorization": '+call_app_token+'}';
					}
					//$('.followers-div').show();
					$.ajax({
						type: "POST",
						url: url2,
						contentType: "application/json",
						data: JSON.stringify({
							email:msg1.app_id,
							token,
							password:msg1.app_secret
						}),
						header,
						dataType: 'json',
						async: false,
						success: function(res){
							var res1 = JSON.parse(JSON.stringify(res));
							if(res1.success) {
								//var url3 =  admin_url+'call_settings/delete_agent';
								var url2 = 'https://api-smartflo.tatateleservices.com/v1/agents';
								//$('.followers-div').show();
								$.ajax({
									type: "GET",
									url: url2,
									contentType: "application/json",
									dataType: 'json',
									headers: {
										 "Authorization": "Bearer "+res1.access_token
									},
									success: function(result){
										console.log(result);
										var req_id = '';
										if(result.length>0) {
											for (var i = 0, j = result.length; i < j; i += 1) { 
												if( result[i].follow_me_number.includes(edit_phone)){
													req_id = result[i].id;
												}
											}
											var url3 = 'https://api-smartflo.tatateleservices.com/v1/agent/'+req_id;
											$.ajax({
												type: "DELETE",
												url: url3,
												contentType: "application/json",
												dataType: 'json',
												headers: {
													 "Authorization": "Bearer "+res1.access_token
												},
											success: function(result1){
												if(result1.success){
													if(dbdel ==true){
														deleteagentfromdb(id);
													}
													else{
														var url3 =  admin_url+'call_settings/delete_agent';
														//$('.followers-div').show();
														$.ajax({
															type: "POST",
															url: url3,
															data: {
																token:res1.access_token,
																id:id
															},
															dataType: 'json',
															success: function(result){
																console.log(result);
																if(result.status == 'success') {
																			
																} 
																else {
																	alert_float('warning', result.msg+' <br> Please Delete Manually on Tata Tele Services Portal');
																	setTimeout(function(){
																		window.location.reload();
																	},1000);
																}
															}
														});
													}
																//alert(res.msg);
												} 
												else {
													alert_float('warning', res.msg+' <br> Please Delete Manually on Tata Tele Services Portal');
													setTimeout(function(){
														window.location.reload();
													},1000);
												}
											}
										});
									}
									else {
										alert_float('warning', result1.message+' <br> Please Delete Manually on Tata Tele Services Portal');
										setTimeout(function(){
											window.location.reload();
										},1000);
									}
								}
							});
						} 
						else {
							tataupdate_access_token_db('delete',id);
						}
					},
					error: function(xhr, status, error) {
						var call_app_token =  $('#call_app_token').val();
						if(call_app_token!=''){
							tataupdate_access_token_db('delete',id);
						}
						else{
							alert_float('warning', 'Invalid Credentials'+' <br> Please Delete Manually on Tata Tele Services Portal');
							setTimeout(function(){
								window.location.reload();
							},1000);
						}
					}
				});
			} 
			else {
				alert_float('warning', msg.msg);
				setTimeout(function(){
					window.location.reload();
				},1000);
			}
		}
	});
	return true;
			//}
}
function tataupdate_access_token_db(red_url,id){
	var url13 =  admin_url+'call_settings/updatetoken';
	$.ajax({
		type: "POST",
		url: url13,
		data: {
			token:'',
		},
		dataType: 'json',
		success: function(result){
			var msg1 = JSON.parse(JSON.stringify(result));
			if(msg1.access_token ==''){
				if(red_url == 'delete'){
					tatadeletAgent_db(id,1);
				}
			}else{
				alert_float('warning', 'Invalid Credentials'+' <br> Please Delete Manually on Tata Tele Services Portal');
				setTimeout(function(){
					window.location.reload();
				},1000);
			}
		}
	});
}
function tatadeletAgent_db(id,cur_val) {
	if(cur_val==''){
	if (confirm('Do you want to Deactivate this Agent?')) {
		tatadeletAgent_1_db(id,cur_val);
	}
	else {
        return false;
    }
	}else{
		tatadeletAgent_1_db(id,cur_val);
	}
}

function deleteagentfromdb(id){
    var url3 =  admin_url+'call_settings/delete_agent_db';
    $.ajax({
        type: "POST",
        url: url3,
        data: {
            id:id
        },
        dataType: 'json',
        success: function(result){
            console.log(result);
            if(result.status == 'success') {
                alert_float('success', result.msg);
                setTimeout(function(){
                    window.location.reload();
                },1000);
            } else {
                alert_float('warning', result.msg);
                setTimeout(function(){
                    window.location.reload();
                },1000);
            }
        }
    });
}
function deletAgent_db(id,req_id1,source_from){
	 if (confirm('Do you want to Delete this Agent?')) {
        if(req_id1 == 1){
            if(source_from == 'telecmi'){
                tele_delete_agent_db(id,true);
                return;
            }
            else if(source_from == 'tata'){
                tatadeletAgent_1_db(id,'');
            }
        }else{
            deleteagentfromdb(id);
        }
	 }
}
function deletAgent(id) {
    if (confirm('Do you want to Deactivate this Agent?')) {
        tele_delete_agent_db(id,false);
    } else {
        return false;
    }
}
function tatadeletAgent(id,cur_val) {
	if(cur_val==''){
	if (confirm('Do you want to Deactivate this Agent?')) {
		tatadeletAgent_1(id,cur_val);
	}
	else {
        return false;
    }
	}else{
		tatadeletAgent_1(id,cur_val);
	}
}
function daffydeletAgent(id,cur_val) {
	if(cur_val==''){
        if (confirm('Do you want to Deactivate this Agent?')) {
            var url3 =  admin_url+'call_settings/delete_agent';
            $.ajax({
                type: "POST",
                url: url3,
                data: {
                    token:'',
                    id:id
                },
                dataType: 'json',
                success: function(result){
                    console.log(result);
                    if(result.status == 'success') {
                        alert_float('success', result.msg);
                        setTimeout(function(){
                            window.location.reload();
                        },1000);
                    } else {
                        alert_float('warning', result.msg);
                        setTimeout(function(){
                            window.location.reload();
                        },1000);
                    }
                }
            });
        }
	}
}

function tatadeletAgent_1(id,cur_val) {
    //if (confirm('Do you want to Deactivate this Agent?')) {
        var url =  admin_url+'call_settings/getAgentDetails';
        //$('.followers-div').show();
        $.ajax({
            type: "POST",
            url: url,
            data: {id:id},
            dataType: 'json',
            success: function(msg){
				var call_app_token =  $('#call_app_token').val();
                var msg1 = JSON.parse(JSON.stringify(msg));
				console.info(msg1);
				
				 if(msg1.phone) {
					 var edit_phone = msg1.phone;
                    //var url2 = 'https://piopiy.telecmi.com/v1/agent/remove';
					if(cur_val == ''){
						if(call_app_token == ''){
							var url2 = 'https://api-smartflo.tatateleservices.com/v1/auth/login';
						}else{
							var url2 = 'https://api-smartflo.tatateleservices.com/v1/auth/refresh';
						}
					}else{
						var url2 = 'https://api-smartflo.tatateleservices.com/v1/auth/login';
					}
					var token = header = '';
					if(call_app_token!=''){
						token = "token:"+call_app_token;
						header = 'headers: {"Authorization": '+call_app_token+'}';
					}
                    //$('.followers-div').show();
                    $.ajax({
                        type: "POST",
                        url: url2,
                        contentType: "application/json",
                        data: JSON.stringify({
                            email:msg1.app_id,
							token,
							password:msg1.app_secret
                        }),
						header,
                        dataType: 'json',
                        async: false,
                        success: function(res){
							var res1 = JSON.parse(JSON.stringify(res));
                            if(res1.success) {
                                //var url3 =  admin_url+'call_settings/delete_agent';
                                var url2 = 'https://api-smartflo.tatateleservices.com/v1/agents';
                                //$('.followers-div').show();
                                $.ajax({
                                    type: "GET",
                                    url: url2,
                                    contentType: "application/json",
									dataType: 'json',
									headers: {
										 "Authorization": "Bearer "+res1.access_token
									},
                                    success: function(result){
                                        console.log(result);
                                            var req_id = '';
											if(result.length>0) {
												for (var i = 0, j = result.length; i < j; i += 1) { 
													if( result[i].follow_me_number.includes(edit_phone)){
														req_id = result[i].id;
													}
												}
												var url3 = 'https://api-smartflo.tatateleservices.com/v1/agent/'+req_id;
												$.ajax({
													type: "DELETE",
													url: url3,
													contentType: "application/json",
													dataType: 'json',
													headers: {
														"Authorization": "Bearer "+res1.access_token
													},
													success: function(result1){
														if(result1.success){
															 var url3 =  admin_url+'call_settings/delete_agent';
																$.ajax({
																	type: "POST",
																	url: url3,
																	data: {
																		token:res1.access_token,
																		id:id
																	},
																	dataType: 'json',
																	success: function(result){
																		console.log(result);
																		if(result.status == 'success') {
																			alert_float('success', result.msg);
																			setTimeout(function(){
																				window.location.reload();
																			},1000);
																		} else {
																			alert_float('warning', result.msg);
																			setTimeout(function(){
																				window.location.reload();
																			},1000);
																		}
																	}
																});
																//alert(res.msg);
															} else {
																alert_float('warning', res.msg);
																setTimeout(function(){
																	window.location.reload();
																},1000);
															}
															}
												  });
														}
														else {
															alert_float('warning', result1.message);
															setTimeout(function(){
																window.location.reload();
															},1000);
														}
													}
												  });
                                        } else {
                                            tataupdate_access_token('delete',id);
                                        }
                                    },
									error: function(xhr, status, error) {
										var call_app_token =  $('#call_app_token').val();
										if(call_app_token!=''){
											tataupdate_access_token('delete',id);
										}else{
											alert_float('warning', 'Invalid Credentials');
											setTimeout(function(){
												window.location.reload();
											},1000);
										}
									}
                                });
                                //alert(res.msg);
                            } else {
                                alert_float('warning', msg.msg);
                                setTimeout(function(){
                                    window.location.reload();
                                },1000);
                            }
                        }
                    });
                //}
}

function edit_agent(id) {
    var url =  admin_url+'call_settings/getAgentDetails';
    //$('.followers-div').show();
    $('#editAgentModal').modal('show');
    $.ajax({
        type: "POST",
        url: url,
        data: {id:id},
        dataType: 'json',
        success: function(msg){
            $('.errmsg').html('');
            show_wrapper(msg.source_from);
            $('#editAgentModal select[name="ivr_id"]').attr('disabled',true);
            $('#editAgentModal select[name=ivr_id]').val(msg.ivr_id);
			$('#editAgentModal select[name=ivr_id]').selectpicker('refresh');

            $('#editAgentModal #agentid').val(msg.agent_id);
            $('#editAgentModal #id').val(msg.id);
            $('#editAgentModal #phone').val(msg.phone);
            $('#editAgentModal #edit_phone1').val(msg.phone);
            $('#editAgentModal #password').val(msg.password);
            $('#editAgentModal #name').val(msg.staff_name);
            
            if(msg.staff_id >0){
                $('#editAgentModal select#staff_id').selectpicker('val',msg.staff_id);
                $('#editAgentModal select#staff_id').attr('disabled',true);
                $('#editAgentModal select#staff_id option[value='+msg.staff_id+']').attr('selected','selected');
                $('#editAgentModal select#staff_id').selectpicker('refresh');
            }else{
                $('#editAgentModal select#staff_id').removeAttr('disabled');
                $('#editAgentModal select#staff_id').val('');
                $('#editAgentModal select#staff_id').selectpicker('refresh');
            }
            
           

            $('#editAgentModal select#status').selectpicker('val',msg.status);
            $('#editAgentModal select#status option[value='+msg.status+']').attr('selected','selected');
            $('#editAgentModal select#status').selectpicker('refresh');

            $('#editAgentModal select#sms_alert').selectpicker('val',msg.sms_alert);
			if(msg.sms_alert!=''){
				$('#editAgentModal select#sms_alert option[value='+msg.sms_alert+']').attr('selected','selected');
			}
            $('#editAgentModal select#sms_alert').selectpicker('refresh');

            $('#editAgentModal select#starttime').selectpicker('val',msg.start_time);
            $('#editAgentModal select#starttime option[value='+msg.start_time+']').attr('selected','selected');
            $('#editAgentModal select#starttime').selectpicker('refresh');

            $('#editAgentModal select#endtime').selectpicker('val',msg.end_time);
            $('#editAgentModal select#endtime option[value='+msg.end_time+']').attr('selected','selected');
            $('#editAgentModal select#endtime').selectpicker('refresh');

            // -----Country Code Selection
            $("#editAgentModal #phone").intlTelInput({
                initialCountry: msg.phone_country_code,
                separateDialCode: true,
                // utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.4/js/utils.js"
            });
            $("#editAgentModal  #phone_country_code").val(msg.phone_country_code);
            $("#editAgentModal  #phone_code").val(msg.dial_code);
            $("#editAgentModal #phone_iti_wrapper .iti__flag-container ul li").click(function(){

                var dial_code =$(this).attr('data-dial-code');
                var country_code =$(this).attr('data-country-code').toUpperCase();
                $("#editAgentModal  #phone_country_code").val(country_code);
                $("#editAgentModal  #phone_code").val(dial_code);
            });

            // if(msg.phone) {
            // $('#addAgentModal #phone').val(msg.phone);
            // $('#addAgentModal #ext').val(emp_id);
            // $('#addAgentModal #name').val(msg.name);
            // } else {
            // $('#addAgentModal #phone').val('');
            // $('#addAgentModal #name').val('');
            // }
        }
    });
}
function playrecord(url) {
        $html = '';
        var surl = admin_url.split("/admin");
        $('#play_record').modal('show');
        $html += '<audio id="myAudio" controls controlsList="nodownload"><source src="'+surl[0]+'/uploads/recordings/'+url+'"></audio>';
        $('#playhtml').html($html);
}
function view_history(id) {
        $('#view_history').modal('show');
        if(id) {
            var url =  admin_url+'call_settings/getCallHistory';
            //$('.followers-div').show();
            $.ajax({
                type: "POST",
                url: url,
                data: {id:id},
                dataType: 'json',
                success: function(msg){
                    console.log(msg.result);
                  if(msg.result) {
                    $('#historyhtml').html(msg.result);
                  }
                }
            });
        }
}

function activateAgent(id) {
    var url1 =  admin_url+'call_settings/getAgentDetails';
    //$('.followers-div').show();
    $.ajax({
        type: "POST",
        url: url1,
        data: {id:id},
        dataType: 'json',
        success: function(msg1){
            var extid = msg1.staff_id;
            var name = msg1.staff_name;
            var phone_number = msg1.phone;
            var start_time = msg1.start_time;
            var end_time = msg1.end_time;
            var status = msg1.status;
            var password = msg1.password;
            var extension = msg1.agent_id.split("_")[0];
            var appid = msg1.app_id;
            var secret = msg1.app_secret;
            var sms_alert = msg1.sms_alert;
            if(msg1.channel =='international_softphone' || msg1.channel =='national_softphone'){
                var url = 'https://rest.telecmi.com/v2/user/add';
            }else{
                var url = 'https://piopiy.telecmi.com/v1/agent/add';
                phone_number =parseInt(phone_number);
            }
            //$('.followers-div').show();
            $.ajax({
                type: "POST",
                url: url,
                contentType: "application/json",
                data: JSON.stringify({
                    name:name,
                    phone_number:phone_number,
                    start_time:parseInt(start_time),
                    end_time:parseInt(end_time),
                    password:password,
                    extension:parseInt(extension),
                    appid:parseInt(appid),
                    secret:secret,
                    sms_alert: (sms_alert == 'true' ? true : false)
                }),
                dataType: 'json',
                async: false,
                success: function(msg){
                    if(msg.status == 'success') {
                        if(msg1.channel =='international_softphone' || msg1.channel =='national_softphone'){
                            var url2 = 'https://rest.telecmi.com/v2/user/status';
                        }else{
                            var url2 = 'https://piopiy.telecmi.com/v1/agent/status';
                        }
                        //$('.followers-div').show();
                        $.ajax({
                            type: "POST",
                            url: url2,
                            contentType: "application/json",
                            data: JSON.stringify({
                                id:msg.agent.agent_id,
                                appid:parseInt(appid),
                                secret:secret,
                                status:status
                            }),
                            dataType: 'json',
                            async: false,
                            success: function(res){
                                if(res.code == 'cmi-200' || res.code =='200') {
                                    var url3 =  admin_url+'call_settings/activateAgent';
                                    //$('.followers-div').show();
                                    $.ajax({
                                        type: "POST",
                                        url: url3,
                                        data: {
                                            id:id
                                        },
                                        dataType: 'json',
                                        success: function(result){
                                            console.log(result);
                                            if(result.status == 'success') {
                                                alert_float('success', result.msg);
                                                setTimeout(function(){
                                                    window.location.reload();
                                                },1000);
                                            } else {
                                                alert_float('warning', result.msg);
                                                setTimeout(function(){
                                                    window.location.reload();
                                                },1000);
                                            }
                                        }
                                    });
                                    //alert(res.msg);
                                } else {
                                    alert_float('warning', res.msg);
                                    setTimeout(function(){
                                        window.location.reload();
                                    },1000);
                                }
                            }
                        });
                    } else {
                        alert_float('warning', msg.msg);
                        setTimeout(function(){
                            window.location.reload();
                        },1000);
                    }
                }
            });
        }
    });
}

function daffyactivateAgent(id,cur_val) {
	var url3 =  admin_url+'call_settings/activateAgent';
	//$('.followers-div').show();
	$.ajax({
		type: "POST",
		url: url3,
		data: {
			id:id,
			token:'',
			agentid:0,
		},
		dataType: 'json',
		success: function(result){
			console.log(result);
			if(result.status == 'success') {
				alert_float('success', result.msg);
				setTimeout(function(){
					window.location.reload();
				},1000);
			} else {
				alert_float('warning', result.msg);
				setTimeout(function(){
					window.location.reload();
				},1000);
			}
		}
	});
}
function tataactivateAgent(id,cur_val) {
    var url1 =  admin_url+'call_settings/getAgentDetails';
    //$('.followers-div').show();
    $.ajax({
        type: "POST",
        url: url1,
        data: {id:id},
        dataType: 'json',
        success: function(msg1){
            var extid = msg1.staff_id;
            var name = msg1.staff_name;
            var phone_number = msg1.phone;
            var start_time = msg1.start_time;
            var end_time = msg1.end_time;
            var status = msg1.status;
            var password = msg1.password;
            var extension = (100 + parseInt(extid));
            var appid = msg1.app_id;
            var secret = msg1.app_secret;
            var sms_alert = msg1.sms_alert;
			var call_app_token =  $('#call_app_token').val();
			if(cur_val == ''){
				if(call_app_token){
					var url = 'https://api-smartflo.tatateleservices.com/v1/auth/login';
				}else{
					var url = 'https://api-smartflo.tatateleservices.com/v1/auth/refresh';
				}
			}else{
				var url = 'https://api-smartflo.tatateleservices.com/v1/auth/login';
			}
            //$('.followers-div').show();
			var token = header = '';
			if(call_app_token !=''){
				token  = "token:"+call_app_token;
				header = 'headers: {"Authorization": '+call_app_token+'}';
				//header = 'headers: {"Authorization": '+call_app_token+'}';
			}
            $.ajax({
                type: "POST",
                url: url,
                contentType: "application/json",
                data: JSON.stringify({
                    email:appid,
					token,
                    password:secret,
                   
                }),
				header,
                dataType: 'json',
                async: false,
                success: function(msg2){
					var msg3 = JSON.parse(JSON.stringify(msg2));
                    if(msg3.success) {
                        var url2 = 'https://api-smartflo.tatateleservices.com/v1/agent';
                        //$('.followers-div').show();
                        $.ajax({
                            type: "POST",
                            url: url2,
                            contentType: "application/json",
                            data: JSON.stringify({
                                name:msg1.staff_name,
                                follow_me_number:msg1.phone
                            }),
							Accept: "application/json",
							headers: {
								 "Authorization": "Bearer "+msg3.access_token
							},
                            dataType: 'json',
                            async: false,
                            success: function(res){
								var res1 = JSON.parse(JSON.stringify(res));
                                if(res1.success) {
                                    var url3 =  admin_url+'call_settings/activateAgent';
                                    //$('.followers-div').show();
                                    $.ajax({
                                        type: "POST",
                                        url: url3,
                                        data: {
                                            id:id,
											token:msg3.access_token,
											agentid:res1.agent_id,
                                        },
                                        dataType: 'json',
                                        success: function(result){
                                            console.log(result);
                                            if(result.status == 'success') {
                                                alert_float('success', result.msg);
                                                setTimeout(function(){
                                                    window.location.reload();
                                                },1000);
                                            } else {
                                                alert_float('warning', result.msg);
                                                setTimeout(function(){
                                                    window.location.reload();
                                                },1000);
                                            }
                                        }
                                    });
                                    //alert(res.msg);
                                } else {
                                    alert_float('warning', res1.message);
                                    setTimeout(function(){
                                        window.location.reload();
                                    },1000);
                                }
                            }
                        });
                    } else {
						tataupdate_access_token('activate',id);
					}
                },
				error: function(xhr, status, error) {
					var call_app_token =  $('#call_app_token').val();
					if(call_app_token!=''){
						tataupdate_access_token('activate',id);
					}else{
						alert_float('warning', 'Invalid Credentials');
						setTimeout(function(){
							window.location.reload();
						},1000);
					}
				}
            });
        }
    });
}
function tataupdate_access_token(red_url,id){
	var url13 =  admin_url+'call_settings/updatetoken';
	$.ajax({
		type: "POST",
		url: url13,
		data: {
			token:'',
		},
		dataType: 'json',
		success: function(result){
			var msg1 = JSON.parse(JSON.stringify(result));
			if(msg1.access_token ==''){
				if(red_url == 'edit'){
					tataeditagent(1);
				}
				else if(red_url == 'add'){
					tataaddagent(1);
				}
				else if(red_url == 'activate'){
					tataactivateAgent(id,1)
				}
				else if(red_url == 'delete'){
					tatadeletAgent(id,1);
				}
			}else{
				alert_float('warning', 'Invalid Credentials');
				setTimeout(function(){
					window.location.reload();
				},1000);
			}
		}
	});
}
function tataeditagent(cur_val){
	$('#targeteditAgent').attr('disabled','disabled');
	var extension = $('#editAgentModal #agentid').val();
        var name = $('#editAgentModal #name').val();
        var phone_number = $('#editAgentModal #phone').val();
        var edit_phone = $('#editAgentModal #edit_phone1').val();
       
        var status = $('#editAgentModal #status').val();
        var extid = $('#editAgentModal #id').val();
        var staff_id = $('#editAgentModal #staff_id').val();

        var ivr_id = $('#editAgentModal #ivr_id').val();
        var ivr_details =get_ivr_details(ivr_id);
        if (typeof ivr_details.app_id == 'undefined'){
            alert_float('warning', 'IVR should be selected');
            setTimeout(function(){
                window.location.reload();
            },1000);
        }else{
            var appid = ivr_details.app_id;
            var secret = ivr_details.app_secret;
            var channel = ivr_details.channel;
        }
		var call_app_token =  $('#call_app_token').val();
		if(cur_val==''){
			if(call_app_token==''){
				var url = 'https://api-smartflo.tatateleservices.com/v1/auth/login';
			}else{
				var url = 'https://api-smartflo.tatateleservices.com/v1/auth/refresh';
			}
		}else{
			var url = 'https://api-smartflo.tatateleservices.com/v1/auth/login';
		}
		var token = header = '';
		if(call_app_token!=''){
			token  = "token:"+call_app_token;
			header = 'headers: {"Authorization": '+call_app_token+'}';
		}
		$.ajax({
			type: "POST",
			url: url,
			contentType: "application/json",
			Accept: "application/json",
			data: JSON.stringify({
				email:appid,
				token,
				password:secret
			}),
			header,
			dataType: 'json',
			async: false,
			success: function(msg){
				var msg1 = JSON.parse(JSON.stringify(msg));
				if(msg1.success) {
					// var url2 = 'https://piopiy.telecmi.com/v1/agent/status';
					var url2 = 'https://api-smartflo.tatateleservices.com/v1/agents';
					//$('.followers-div').show();
					$.ajax({
						type: "GET",
						url: url2,
						contentType: "application/json",
						dataType: 'json',
						headers: {
							 "Authorization": "Bearer "+msg1.access_token
						},
						async: false,
						success: function(res){
							var req_id = '';
							if(res.length>0) {
								for (var i = 0, j = res.length; i < j; i += 1) { 
									if( res[i].follow_me_number.includes(edit_phone)){
										req_id = res[i].id;
									}
								}
								// var url3 =  admin_url+'call_settings/updateAgent';
								var url3 = 'https://api-cloudphone.tatateleservices.com/v1/agent/'+req_id;
								//$('.followers-div').show();
								$.ajax({
									type: "PUT",
									url: url3,
									data: {
										name:name,
										follow_me_number:phone_number,
										
									},
									dataType: 'json',
									headers: {
										 "Authorization": "Bearer "+msg1.access_token
									},
									success: function(result){
									if(result.success) {
									var url3 =  admin_url+'call_settings/updateAgent';
										$.ajax({
											type: "POST",
											url: url3,
											data: {
												id:extid,
												phone_number:phone_number,
												token:msg1.access_token,
												 status:status,
												staff_id:staff_id,
												phone_country_code,phone_country_code
												
											},
											dataType: 'json',
											success: function(result1){
												if(result1.status == 'success') {
													alert_float('success', result1.msg);
													setTimeout(function(){
														window.location.reload();
													},1000);
												} else {
													alert_float('warning', result1.msg);
													setTimeout(function(){
														window.location.reload();
													},1000);
												}
											}
										});
									
									} else {
										alert_float('warning', result.message);
										setTimeout(function(){
											window.location.reload();
										},1000);
									}
								},
								error: function(result1) {
									alert_float('warning','Call forward number already exists');
									setTimeout(function(){
										window.location.reload();
									},1000);
								}
							});
						//alert(res.msg);
						} else {
							alert_float('warning', res.message);
							setTimeout(function(){
								window.location.reload();
							},1000);
						}
					}
				});
			}
			else{
			  tataupdate_access_token('edit','');
			}
		},
		error: function(msg) {
			var call_app_token =  $('#call_app_token').val();
			if(call_app_token!=''){
				tataupdate_access_token('edit','');
			}else{
				var msg1 = JSON.parse(JSON.stringify(msg));
				console.info(msg1);
				alert_float('warning', 'Invalid Credentials');
				setTimeout(function(){
					//window.location.reload();
				},1000);
			}
		}
	});
}
function tataaddagent(cur_val){
	var extid = $('#addAgentModal #ext').val();
	var name = $('#addAgentModal #name').val();
	var phone_number = $('#addAgentModal #phone').val();
   
	var status = $('#addAgentModal #status').val();
	var phone_country_code = $('#addAgentModal #phone_country_code').val();
	var extension = (100 + parseInt(extid));
    
    var ivr_id =$('#addAgentModal #ivr_id').val();
    var ivr_details =get_ivr_details(ivr_id);
    if (typeof ivr_details.app_id == 'undefined'){
        alert_float('warning', 'IVR shuold be selected');
        setTimeout(function(){
            window.location.reload();
        },1000);
    }else{
        var appid = ivr_details.app_id;
        var secret = ivr_details.app_secret;
    }
	$('#tataaddAgent').attr('disabled','disabled');
	var call_app_token =  $('#call_app_token').val();
	if(cur_val==''){
		if(call_app_token==''){
			var url = 'https://api-smartflo.tatateleservices.com/v1/auth/login';
		}else{
			var url = 'https://api-smartflo.tatateleservices.com/v1/auth/refresh';
		}
	}else{
		var url = 'https://api-smartflo.tatateleservices.com/v1/auth/login';
	}
	//$('.followers-div').show();
	var token = header = '';
	if(call_app_token!=''){
		token = "token:"+call_app_token;
		header = 'headers: {"Authorization": '+call_app_token+'}';
	}
	$.ajax({
		type: "POST",
		url: url,
		contentType: "application/json",
		Accept: "application/json",
		data: JSON.stringify({
			email:appid,
			token,
			password:secret                    
		}),
		header,
		dataType: 'json',
		async: false,
		success: function(msg){
			console.info(msg);
			var msg1 = JSON.parse(JSON.stringify(msg));
			console.info(msg1);
		  if(msg1.success) {
			var url2 = 'https://api-smartflo.tatateleservices.com/v1/agent';
			//$('.followers-div').show();
			$.ajax({
				type: "POST",
				url: url2,
				Authorization: "Bearer "+msg1.access_token,
				contentType: 'application/json',
				Accept: "application/json",
				headers: {
					 "Authorization": "Bearer "+msg1.access_token
				},
				
				data: JSON.stringify({
					name:name,
					follow_me_number:phone_number
				}),
				dataType: 'json',
				async: false,
				success: function(res){
					
					var res1 = JSON.parse(JSON.stringify(res));
					if(res1.success) {
						var url3 =  admin_url+'call_settings/saveAgent';
						//$('.followers-div').show();
						$.ajax({
							type: "POST",
							url: url3,
							data: {
								extid:extid,
								phone_number:phone_number,
								agentid:res1.agent_id,
								secret:secret,
								token:msg1.access_token,
								status:status,
                                ivr_id:ivr_id,
                                phone_country_code:phone_country_code
							},
							dataType: 'json',
							success: function(result){
								console.log(result);
								const result1 = JSON.parse(JSON.stringify(result));
								if(result1.status == 'success') {
									alert_float('success', result1.msg);
									setTimeout(function(){
										window.location.reload();
									},1000);
								} else {
									alert_float('warning', result.msg);
									setTimeout(function(){
										window.location.reload();
									},1000);
								}
							}
						});
						//alert(res.msg);
					} else {
						alert_float('warning', res1.message);
						setTimeout(function(){
							window.location.reload();
						},1000);
					}
				},
				error: function(res1) {
					var res2 = JSON.parse(JSON.stringify(res1));
					alert_float('warning', 'Call forward number already exists');
					setTimeout(function(){
						window.location.reload();
					},1000);
				}
			});
		  } else {
			tataupdate_access_token('add','');
		  }
		},
		error: function(msg) {
			var call_app_token =  $('#call_app_token').val();
			if(call_app_token!=''){
				tataupdate_access_token('add','');
			}else{
				alert_float('warning', 'Invalid Credentials');
				setTimeout(function(){
					window.location.reload();
				},1000);
			}
		}
	});

}
$(document).ready(function(){
	$('#closeaudio').on('click', function() {
        var myAudio = document.getElementById("myAudio");
        myAudio.pause();
        $('#play_record').modal('hide');
    });

    $('#addAgentModal select#staff_id').on('change', function() {
        var emp_id = this.value;
        if(emp_id) {
            var url =  admin_url+'call_settings/getEmpDetail';
            //$('.followers-div').show();
            $.ajax({
                type: "POST",
                url: url,
                data: {emp_id:emp_id},
                dataType: 'json',
                success: function(msg){
                  console.log(msg.phone);
                  //if(msg.phone) {
                    $('#addAgentModal #phone').val(msg.phone);
                    $('#addAgentModal #ext').val(emp_id);
                    $('#addAgentModal #name').val(msg.name);
                //   } else {
                //     $('#addAgentModal #phone').val('');
                //     $('#addAgentModal #name').val('');
                //   }
                }
            });
        }
    });
	
	$('#tataaddAgent').on('click', function() {
        var extid = $('#addAgentModal #ext').val();
        var name = $('#addAgentModal #name').val();
        var phone_number = $('#addAgentModal #phone').val();
       
        var status = $('#addAgentModal #status').val();
        var extension = (100 + parseInt(extid));
        // Validations
        var validate = 0;

        var ivr_details =get_ivr_details($('#ivr_id').val());
        if (typeof ivr_details.app_id == 'undefined'){
            validate = 1;
            $('#addAgentModal #ivr_id_val').html('IVR should be selected');
        }else{
            var appid = ivr_details.app_id;
            var secret = ivr_details.app_secret;
        }
        $('#addAgentModal .errmsg').html ('');
        if(!extid) {
            $('#addAgentModal #staff_val').html('Please select Staff');
            validate = 1;
        } else {
            $('#addAgentModal #staff_val').html('');
        }

        if(!phone_number) {
            $('#addAgentModal #phone_val').html('Please enter Phone number, It should have minimum 7 digits to maximum 20 digits');
            validate = 1;
        } else {
            if(phone_number.length > 6 && phone_number.length < 21) {
                $('#addAgentModal #phone_val').html('');
            } else {
                $('#addAgentModal #phone_val').html('Please enter Valid Phone number, It should have minimum 7 digits to maximum 20 digits');
                validate = 1;
            }
        }

        if(validate == 1) {
            return false;
        }

        if(appid) {
			tataaddagent('');
            } else {
            alert_float('warning', 'Please enable call settings.');
            setTimeout(function(){
                window.location.reload();
            },1000);
        }
    });
	
	$('#daffyaddAgent').on('click', function() {
        var extid = $('#addAgentModal #ext').val();
        var name = $('#addAgentModal #name').val();
        var phone_number = $('#addAgentModal #phone').val();
       
        var status = $('#addAgentModal #status').val();
        var phone_country_code = $('#addAgentModal #phone_country_code').val();
        var extension = (100 + parseInt(extid));

        // Validations
        var validate = 0;

        var ivr_id =$('#addAgentModal #ivr_id').val();
        var ivr_details =get_ivr_details(ivr_id);
        if (typeof ivr_details.app_id == 'undefined'){
            validate = 1;
            $('#addAgentModal #ivr_id_val').html('IVR should be selected');
        }else{
            var appid = ivr_details.app_id;
            var secret = ivr_details.app_secret;
        }

        $('#addAgentModal .errmsg').html ('');
        if(!extid) {
            $('#addAgentModal #staff_val').html('Please select Staff');
            validate = 1;
        } else {
            $('#daffyaddAgentModal #staff_val').html('');
        }

        if(!phone_number) {alert(2);
            $('#addAgentModal #phone_val').html('Please enter Phone number, It should have minimum 7 digits to maximum 20 digits');
            validate = 1;
        } else {
            if(phone_number.length > 6 && phone_number.length < 21) {
                $('#addAgentModal #phone_val').html('');
            } else {
                $('#addAgentModal #phone_val').html('Please enter Valid Phone number, It should have minimum 7 digits to maximum 20 digits');
                validate = 1;
            }
        }
        if(validate == 1) {
            return false;
        }

        if(appid) {
			var url3 =  admin_url+'call_settings/saveAgent';
						//$('.followers-div').show();
						$.ajax({
							type: "POST",
							url: url3,
							data: {
								extid:extid,
								phone_number:phone_number,
								agentid:0,
								secret:secret,
								token:'',
								status:status,
                                ivr_id:ivr_id,
                                phone_country_code:phone_country_code
							},
							dataType: 'json',
							success: function(result){
								console.log(result);
								const result1 = JSON.parse(JSON.stringify(result));
								if(result1.status == 'success') {
									alert_float('success', result1.msg);
									setTimeout(function(){
										window.location.reload();
									},1000);
								} else {
									alert_float('warning', result.msg);
									setTimeout(function(){
										window.location.reload();
									},1000);
								}
							}
						});
            } else {
            alert_float('warning', 'Please enable call settings.');
            setTimeout(function(){
                window.location.reload();
            },1000);
        }
    });

    function get_ivr_details(id){
        var ivr_details ={};
        if(id>0){
            $.ajax({
                type: "POST",
                url:  admin_url+'call_settings/getIvr/'+id,
                dataType: 'json',
                async : false,
                success: function(result){
                    if(result.success ==true){
                        ivr_details = result.data;
                    }
                }
            });
        }
        
        return ivr_details;
    }
    $('#addAgent').on('click', function() {
        var extid = $('#addAgentModal #ext').val();
        var name = $('#addAgentModal #name').val();
        var phone_number = $('#addAgentModal #phone').val();
        var start_time = $('#addAgentModal #starttime').val();
        var end_time = $('#addAgentModal #endtime').val();
        var status = $('#addAgentModal #status').val();
        var password = $('#addAgentModal #password').val();
        var extension = $('#addAgentModal #extension_id').val();
        var ivr_id =$('#addAgentModal #ivr_id').val();
        var ivr_details =get_ivr_details(ivr_id);
        var sms_alert = $('#addAgentModal #sms_alert').val();
        var phone_country_code = $('#addAgentModal #phone_country_code').val();
        
        $('#addAgentModal .errmsg').html ('');
        
        // Validations
        var validate = 0;
        if (typeof ivr_details.app_id == 'undefined'){
            validate = 1;
            $('#addAgentModal #ivr_id_val').html('IVR should be selected');
        }else{
            var appid = ivr_details.app_id;
            var secret = ivr_details.app_secret;
            var channel = ivr_details.channel;
            
        }
        if(extension.length != 3) {
            $('#addAgentModal #extension_id_val').html('Extension id should be three digit code');
            validate = 1;
        }
        if(!extid) {
            $('#addAgentModal #staff_val').html('Please select Staff');
            validate = 1;
        } else {
            $('#addAgentModal #staff_val').html('');
        }

        if(!phone_number) {
            $('#addAgentModal #phone_val').html('Please enter Phone number, It should have minimum 7 digits to maximum 20 digits');
            validate = 1;
        } else {
            if(phone_number.length > 6 && phone_number.length < 21) {
                $('#addAgentModal #phone_val').html('');
            } else {
                $('#addAgentModal #phone_val').html('Please enter Valid Phone number, It should have minimum 7 digits to maximum 20 digits');
                validate = 1;
            }
        }

        if(!password) {
            $('#addAgentModal #pass_val').html('Please enter Password');
            validate = 1;
        } else {
            if(password.length < 6) {
                $('#addAgentModal #pass_val').html('Password must contain minimum 6 characters');
                validate = 1;
            } else {
                $('#addAgentModal #pass_val').html('');
            }
        }

        if(!start_time) {
            $('#addAgentModal #start_val').html('Please select Start time');
            validate = 1;
        } else {
            if(parseInt(end_time) <= parseInt(start_time)) {
                $('#addAgentModal #start_val').html('Start time should be less than End time.');
                validate = 1;
            } else {
                $('#addAgentModal #start_val').html('');
            }
        }

        if(!end_time) {
            $('#addAgentModal #end_val').html('Please select End time');
            validate = 1;
        } else {
            if(parseInt(end_time) <= parseInt(start_time)) {
                $('#addAgentModal #end_val').html('End time should be greater than Start time.');
                validate = 1;
            } else {
                $('#addAgentModal #end_val').html('');
            }
        }

        if(validate == 1) {
            return false;
        }

        if(appid) {

            $('#addAgent').attr('disabled','disabled');
            if(channel =='international_softphone' || channel =='national_softphone'){
                var url = 'https://rest.telecmi.com/v2/user/add';
                phone_number_new =$('#addAgentModal #phone_code').val()+phone_number;
            }else{
                var url = 'https://piopiy.telecmi.com/v1/agent/add';
                phone_number_new =parseInt(phone_number);
            }
            //$('.followers-div').show();
            $.ajax({
                type: "POST",
                url: url,
                contentType: "application/json",
                data: JSON.stringify({
                    name:name,
                    phone_number:phone_number_new,
                    start_time:parseInt(start_time),
                    end_time:parseInt(end_time),
                    password:password,
                    extension:parseInt(extension),
                    appid:parseInt(appid),
                    secret:secret,
                    sms_alert: (sms_alert == 'true' ? true : false)
                }),
                dataType: 'json',
                async: false,
                success: function(msg){
                if(msg.status == 'success') {
                    if(channel =='international_softphone' || channel =='national_softphone'){
                        var url2 = 'https://rest.telecmi.com/v2/user/status';
                    }else{
                        var url2 = 'https://piopiy.telecmi.com/v1/agent/status';
                    }
                    //$('.followers-div').show();
                    $.ajax({
                        type: "POST",
                        url: url2,
                        contentType: "application/json",
                        data: JSON.stringify({
                            id:msg.agent.agent_id,
                            appid:parseInt(appid),
                            secret:secret,
                            status:status
                        }),
                        dataType: 'json',
                        async: false,
                        success: function(res){
                            if(res.code == 'cmi-200' || res.code == '200') {
                                var url3 =  admin_url+'call_settings/saveAgent';
                                //$('.followers-div').show();
                                $.ajax({
                                    type: "POST",
                                    url: url3,
                                    data: {
                                        extid:extid,
                                        phone_number:phone_number,
                                        start_time:start_time,
                                        end_time:end_time,
                                        password:password,
                                        agentid:msg.agent.agent_id,
                                        secret:secret,
                                        sms_alert:sms_alert,
                                        status:res.status,
                                        ivr_id:ivr_id,
                                        phone_country_code:phone_country_code,
                                    },
                                    dataType: 'json',
                                    success: function(result){
                                        console.log(result);
                                        if(result.status == 'success') {
                                            alert_float('success', result.msg);
                                            setTimeout(function(){
                                                window.location.reload();
                                            },1000);
                                        } else {
                                            alert_float('warning', result.msg);
                                            setTimeout(function(){
                                                window.location.reload();
                                            },1000);
                                        }
                                    }
                                });
                                //alert(res.msg);
                            } else {
                                alert_float('warning', res.msg);
                                setTimeout(function(){
                                    window.location.reload();
                                },1000);
                            }
                        }
                    });
                } else {
                    alert_float('warning', msg.msg);
                    setTimeout(function(){
                        window.location.reload();
                    },1000);
                }
                }
            });

            
        } else {
            alert_float('warning', 'Please enable call settings.');
            setTimeout(function(){
                window.location.reload();
            },1000);
        }
    });
    
    
    $('#editAgentModal select#staff_id').on('change', function() {
        var emp_id = this.value;
        if(emp_id) {
            var url =  admin_url+'call_settings/getEmpDetail';
            //$('.followers-div').show();
            $.ajax({
                type: "POST",
                url: url,
                data: {emp_id:emp_id},
                dataType: 'json',
                success: function(msg){
                  console.log(msg.phone);
                  if(msg.phone) {
                    $('#editAgentModal #phone').val(msg.phone);
                    $('#editAgentModal #ext').val(emp_id);
                    $('#editAgentModal #name').val(msg.name);
                  } else {
                    $('#editAgentModal #phone').val('');
                    $('#editAgentModal #name').val('');
                  }
                }
            });
        }
    });

    $('#editAgent').on('click', function() {
        var extension = $('#editAgentModal #agentid').val();
        var name = $('#editAgentModal #name').val();
        var phone_number = $('#editAgentModal #phone').val();
        var start_time = $('#editAgentModal #starttime').val();
        var end_time = $('#editAgentModal #endtime').val();
        var status = $('#editAgentModal #status').val();
        var password = $('#editAgentModal #password').val();
        
        var sms_alert = $('#editAgentModal #sms_alert').val();
        var extid = $('#editAgentModal #id').val();
        var staff_id = $('#editAgentModal #staff_id').val();
        var ivr_id = $('#editAgentModal #ivr_id').val();
        var phone_country_code = $('#editAgentModal #phone_country_code').val();
        //alert(staff_id);

// Validations
        var validate = 0;
        
        var ivr_details =get_ivr_details(ivr_id);
        if (typeof ivr_details.app_id == 'undefined'){
            validate = 1;
            $('#editAgentModal #ivr_id_val').html('IVR should be selected');
        }else{
            var appid = ivr_details.app_id;
            var secret = ivr_details.app_secret;
            var channel = ivr_details.channel;
        }
        if(!staff_id) {
            $('#editAgentModal #staff_val').html('Please select Staff');
            validate = 1;
        } else {
            $('#editAgentModal #staff_val').html('');
        }
        if($('#editAgentModal #staff_id').is(':disabled') == false){
            $.ajax({
                type: "POST",
                url: admin_url+'call_settings/validate_agent_id/'+staff_id,
                contentType: "application/json",
                dataType: 'json',
                async: false,
                success: function(msg){
                    if(msg.success ==false){
                        $('#editAgentModal #staff_val').html(msg.message);
                        validate = 1;  
                    }
                }
            });
        }
        


        if(!phone_number) {
            $('#editAgentModal #phone_val').html('Please enter Phone number, It should have minimum 7 digits to maximum 20 digits');
            validate = 1;
        } else {
            if(phone_number.length > 6 && phone_number.length < 21) {
                $('#editAgentModal #phone_val').html('');
            } else {
                $('#editAgentModal #phone_val').html('Please enter Valid Phone number, It should have minimum 7 digits to maximum 20 digits');
                validate = 1;
            }
            
        }

        if(!password) {
            $('#editAgentModal #pass_val').html('Please enter Password');
            validate = 1;
        } else {
            if(password.length < 6) {
                $('#editAgentModal #pass_val').html('Password must contain minimum 6 characters');
                validate = 1;
            } else {
                $('#editAgentModal #pass_val').html('');
            }
        }

        if(!start_time) {
            $('#editAgentModal #start_val').html('Please select Start time');
            validate = 1;
        } else {
            if(parseInt(end_time) <= parseInt(start_time)) {
                $('#editAgentModal #start_val').html('Start time should be less than End time.');
                validate = 1;
            } else {
                $('#editAgentModal #start_val').html('');
            }
        }

        if(!end_time) {
            $('#editAgentModal #end_val').html('Please select End time');
            validate = 1;
        } else {
            if(parseInt(end_time) <= parseInt(start_time)) {
                $('#editAgentModal #end_val').html('End time should be greater than Start time.');
                validate = 1;
            } else {
                $('#editAgentModal #end_val').html('');
            }
        }

        if(validate == 1) {
            return false;
        }
        if(appid) {
            $('#editAgent').attr('disabled','disabled');
            if(channel =='international_softphone' || channel =='national_softphone'){
                var url = 'https://rest.telecmi.com/v2/user/update';
                phone_number_new =$('#editAgentModal #phone_code').val()+phone_number
            }else{
                var url = 'https://piopiy.telecmi.com/v1/agent/update';
                phone_number_new =parseInt(phone_number);
            }
            //$('.followers-div').show();
            $.ajax({
                type: "POST",
                url: url,
                contentType: "application/json",
                data: JSON.stringify({
                    name:name,
                    phone_number:phone_number_new,
                    start_time:parseInt(start_time),
                    end_time:parseInt(end_time),
                    password:password,
                    id:extension,
                    appid:parseInt(appid),
                    secret:secret,
                    sms_alert: (sms_alert == 'true' ? true : false)
                }),
                dataType: 'json',
                async: false,
                success: function(msg){
                if(msg.status == 'success') {
                    if(channel =='international_softphone' || channel =='national_softphone'){
                        var url2 = 'https://rest.telecmi.com/v2/user/status';
                    }else{
                        var url2 = 'https://piopiy.telecmi.com/v1/agent/status';
                    }
                    
                    //$('.followers-div').show();
                    $.ajax({
                        type: "POST",
                        url: url2,
                        contentType: "application/json",
                        data: JSON.stringify({
                            id:extension,
                            appid:parseInt(appid),
                            secret:secret,
                            status:status
                        }),
                        dataType: 'json',
                        async: false,
                        success: function(res){
                            if(res.code == 'cmi-200' || res.code == '200') {
                                var url3 =  admin_url+'call_settings/updateAgent';
                                //$('.followers-div').show();
                                $.ajax({
                                    type: "POST",
                                    url: url3,
                                    data: {
                                        id:extid,
                                        phone_number:phone_number,
                                        start_time:start_time,
                                        end_time:end_time,
                                        password:password,
                                        agentid:extension,
                                        sms_alert:sms_alert,
                                        status:res.status,
                                        staff_id:staff_id,
                                        phone_country_code:phone_country_code,
                                    },
                                    dataType: 'json',
                                    success: function(result){
                                        if(result.status == 'success') {
                                            alert_float('success', result.msg);
                                            setTimeout(function(){
                                                window.location.reload();
                                            },1000);
                                        } else {
                                            alert_float('warning', result.msg);
                                            setTimeout(function(){
                                                window.location.reload();
                                            },1000);
                                        }
                                    }
                                });
                                //alert(res.msg);
                            } else {
                                alert_float('warning', res.msg);
                                setTimeout(function(){
                                    window.location.reload();
                                },1000);
                            }
                        }
                    });
                } else {
                    alert_float('warning', msg.msg);
                    setTimeout(function(){
                        window.location.reload();
                    },1000);
                }
                }
            });

        } else {
            alert_float('warning', 'Please enable call settings.');
            setTimeout(function(){
                window.location.reload();
            },1000);
        }
    });
	
	
	$('#daffyeditAgent').on('click', function() {
		var extension = $('#editAgentModal #agentid').val();
        var name = $('#editAgentModal #name').val();
        var phone_number = $('#editAgentModal #phone').val();
        var edit_phone = $('#editAgentModal #edit_phone1').val();
       
        var status = $('#editAgentModal #status').val();
        var extid = $('#editAgentModal #id').val();
        var staff_id = $('#editAgentModal #staff_id').val();
		var validate = 0;

        var ivr_id = $('#editAgentModal #ivr_id').val();
        var phone_country_code = $('#editAgentModal #phone_country_code').val();
        var ivr_details =get_ivr_details(ivr_id);
        if (typeof ivr_details.app_id == 'undefined'){
            validate = 1;
            $('#editAgentModal #ivr_id_val').html('IVR should be selected');
        }else{
            var appid = ivr_details.app_id;
            var secret = ivr_details.app_secret;
            var channel = ivr_details.channel;
        }

        if(!staff_id) {
            $('#editAgentModal #staff_val').html('Please select Staff');
            validate = 1;
        } else {
            $('#editAgentModal #staff_val').html('');
        }

        if(!phone_number) {
            $('#editAgentModal #phone_val').html('Please enter Phone number, It should have minimum 7 digits to maximum 20 digits');
            validate = 1;
        } else {
            if(phone_number.length > 6 && phone_number.length < 21) {
                $('#editAgentModal #phone_val').html('');
            } else {
                $('#editAgentModal #phone_val').html('Please enter Valid Phone number, It should have minimum 7 digits to maximum 20 digits');
                validate = 1;
            }
            
        }


        if(validate == 1) {
            return false;
        }

        if(appid) {
			daffyeditagent('');
		} else {
			alert_float('warning', 'Please enable call settings.');
			setTimeout(function(){
				window.location.reload();
			},1000);
		}
	});
	function daffyeditagent(cur_val){
		$('#targeteditAgent').attr('disabled','disabled');
		var extension = $('#editAgentModal #agentid').val();
        var name = $('#editAgentModal #name').val();
        var phone_number = $('#editAgentModal #phone').val();
        var edit_phone = $('#editAgentModal #edit_phone1').val();
       
        var status = $('#editAgentModal #status').val();
        var extid = $('#editAgentModal #id').val();
        var staff_id = $('#editAgentModal #staff_id').val();

        var ivr_id = $('#editAgentModal #ivr_id').val();
        var phone_country_code = $('#editAgentModal #phone_country_code').val();
        var ivr_details =get_ivr_details(ivr_id);
        if (typeof ivr_details.app_id == 'undefined'){
            alert_float('warning', 'IVR should be selected');
            setTimeout(function(){
                window.location.reload();
            },1000);
        }else{
            var appid = ivr_details.app_id;
            var secret = ivr_details.app_secret;
            var channel = ivr_details.channel;
        }
        
		var url3 =  admin_url+'call_settings/updateAgent';
		$.ajax({
			type: "POST",
			url: url3,
			data: {
				id:extid,
				phone_number:phone_number,
				token:'',
				 status:status,
				staff_id:staff_id,
                phone_country_code:phone_country_code
				
			},
			dataType: 'json',
			
			success: function(result1){
				if(result1.status == 'success') {
					alert_float('success', result1.msg);
					setTimeout(function(){
						window.location.reload();
					},1000);
				} else {
					alert_float('warning', result1.msg);
					setTimeout(function(){
						window.location.reload();
					},1000);
				}
			}
		});
	}
	$('#targeteditAgent').on('click', function() {
        var extension = $('#editAgentModal #agentid').val();
        var name = $('#editAgentModal #name').val();
        var phone_number = $('#editAgentModal #phone').val();
        var edit_phone = $('#editAgentModal #edit_phone1').val();
       
        var status = $('#editAgentModal #status').val();
        var extid = $('#editAgentModal #id').val();
        var staff_id = $('#editAgentModal #staff_id').val();
        var phone_country_code = $('#editAgentModal #phone_country_code').val();
        //alert(staff_id);

// Validations
		var validate = 0;

        var ivr_id = $('#editAgentModal #ivr_id').val();
        var ivr_details =get_ivr_details(ivr_id);
        if (typeof ivr_details.app_id == 'undefined'){
            validate = 1;
            $('#editAgentModal #ivr_id_val').html('IVR should be selected');
        }else{
            var appid = ivr_details.app_id;
            var secret = ivr_details.app_secret;
            var channel = ivr_details.channel;
        }

        if(!staff_id) {
            $('#editAgentModal #staff_val').html('Please select Staff');
            validate = 1;
        } else {
            $('#editAgentModal #staff_val').html('');
        }

        if(!phone_number) {
            $('#editAgentModal #phone_val').html('Please enter Phone number, It should have minimum 7 digits to maximum 20 digits');
            validate = 1;
        } else {
            if(phone_number.length > 6 && phone_number.length < 21) {
                $('#editAgentModal #phone_val').html('');
            } else {
                $('#editAgentModal #phone_val').html('Please enter Valid Phone number, It should have minimum 7 digits to maximum 20 digits');
                validate = 1;
            }
            
        }


        if(validate == 1) {
            return false;
        }

        if(appid) {
			tataeditagent('');
		} else {
			alert_float('warning', 'Please enable call settings.');
			setTimeout(function(){
				window.location.reload();
			},1000);
		}
	});
});