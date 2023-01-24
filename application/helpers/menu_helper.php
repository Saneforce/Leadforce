<?php

defined('BASEPATH') or exit('No direct script access allowed');

function app_init_admin_sidebar_menu_items() {
    $CI = &get_instance();
    if (has_permission('dashboard', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('dashboard', [
            'name' => _l('als_dashboard'),
            'href' => admin_url(),
            'position' => 1,
            'icon' => 'fa fa-tachometer',
        ]);
    }

    if (has_permission('leads', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('leads', [
            'name' => _l('als_leads'),
            'href' => admin_url('leads'),
            'icon' => 'fa fa-tty',
            'position' => 2,
        ]);
    }
    
    if (has_permission('projects', '', 'view') || has_permission('projects', '', 'view_own')) {
        $CI->db->where('publishstatus', '1');
        //if (!is_admin()) {
            //$get_staff_user_id = get_staff_user_id();
            //$CI->db->where('( find_in_set("'.$get_staff_user_id.'", teammembers) OR find_in_set("'.$get_staff_user_id.'", teamleader) )');
            // $this->db->where(' <> 0');
        //}
        $pipelines = $CI->db->get(db_prefix() . 'pipeline')->result_array();
        //echo $CI->db->last_query(); exit;
        //echo "<pre>"; print_r($pipelines); exit;
        //$projurl = admin_url('projects/kanban_noscroll?pipelines='.$pipelines[0]['id'].'&member=&gsearch=');
        $projurl = admin_url('projects/index_list?pipelines=&member=&gsearch=');
        if(!is_admin(get_staff_user_id())) {
            $projurl = admin_url('projects/kanban_noscroll?pipelines='.$pipelines[0]['id'].'&member='.get_staff_user_id().'&gsearch=');
			//$projurl = admin_url('projects/index_list?pipelines=&member='.get_staff_user_id().'&gsearch=');
        }
        
        $CI->app_menu->add_sidebar_menu_item('projects', [
            'name' => _l('projects'),
            'href' => $projurl,
            'icon' => 'fa fa-handshake-o',
            'position' => 3,
        ]);
    }
    if (has_permission('tasks', '', 'view') || has_permission('tasks', '', 'view_own')) {
        $CI->app_menu->add_sidebar_menu_item('tasks', [
            'name' => _l('als_tasks'),
            'href' => admin_url('tasks'),
            'icon' => 'fa fa-tasks',
            'position' => 4,
        ]);
    }

    if (has_permission('contacts', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('contacts', [
            'name' => _l('Person'),
            'slug' => 'all_contacts',
            'href' => admin_url('all_contacts'),
            'position' => 5,
            'icon' => 'fa fa-id-card-o',
        ]);
    }

    if (has_permission('customers', '', 'view') || (have_assigned_customers() || (!have_assigned_customers() && has_permission('customers', '', 'create')))) {
        $CI->app_menu->add_sidebar_menu_item('customers', [
            'name' => _l('als_clients'),
            'href' => admin_url('clients'),
            'position' => 6,
            'slug' => 'clients',
            'icon' => 'fa fa-building-o',
        ]);
    }

    if (has_permission('email', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('email', [
            'name' => 'Email',
            'slug' => 'email',
            //'href' => admin_url('tasks/emailmanagement'),
			 'href' => admin_url('company_mail/check_company_mail'),
            'position' => 7,
            'icon' => 'fa fa-envelope',
        ]);
    }

    if (has_permission('sales')) {
        $CI->app_menu->add_sidebar_menu_item('sales', [
            'collapse' => true,
            'name' => _l('als_sales'),
            'position' => 8,
            'icon' => 'fa fa-balance-scale',
        ]);
    }
    if ((has_permission('proposals', '', 'view') || has_permission('proposals', '', 'view_own')) || (staff_has_assigned_proposals() && get_option('allow_staff_view_proposals_assigned') == 1)) {
        $CI->app_menu->add_sidebar_children_item('sales', [
            'slug' => 'proposals',
            'name' => _l('proposals'),
            'href' => admin_url('proposals'),
            'position' => 5,
        ]);
    }

    // if ((has_permission('estimates', '', 'view') || has_permission('estimates', '', 'view_own')) || (staff_has_assigned_estimates() && get_option('allow_staff_view_estimates_assigned') == 1)) {
    //     $CI->app_menu->add_sidebar_children_item('sales', [
    //         'slug' => 'estimates',
    //         'name' => _l('estimates'),
    //         'href' => admin_url('estimates'),
    //         'position' => 10,
    //     ]);
    // }

    //if ((has_permission('invoices', '', 'view') || has_permission('invoices', '', 'view_own')) || (staff_has_assigned_invoices() && get_option('allow_staff_view_invoices_assigned') == 1)) {

    if ((has_permission('invoices', '', 'view') || has_permission('invoices', '', 'view_own')) || (staff_has_assigned_invoices() && get_option('allow_staff_view_invoices_assigned') == 1)) {
        $CI->app_menu->add_sidebar_children_item('sales', [
            'slug' => 'invoices',
            'name' => _l('invoices'),
            'href' => admin_url('invoices'),
            'position' => 15,
        ]);
    }

   
    if (has_permission('payments', '', 'view') || has_permission('invoices', '', 'view_own') || (get_option('allow_staff_view_invoices_assigned') == 1 && staff_has_assigned_invoices())) {
        // $CI->app_menu->add_sidebar_children_item('sales', [
        //     'slug' => 'payments',
        //     'name' => _l('payments'),
        //     'href' => admin_url('payments'),
        //     'position' => 20,
        // ]);
    }

    if (has_permission('credit_notes', '', 'view') || has_permission('credit_notes', '', 'view_own')) {
        $CI->app_menu->add_sidebar_children_item('sales', [
            'slug' => 'credit_notes',
            'name' => _l('credit_notes'),
            'href' => admin_url('credit_notes'),
            'position' => 25,
        ]);
    }

    if (has_permission('items', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('sales', [
            'slug' => 'items',
            'name' => _l('items'),
            'href' => admin_url('invoice_items'),
            'position' => 4,
        ]);
    }
	// if (has_permission('target', '', 'view')) {
    //     $CI->app_menu->add_sidebar_children_item('sales', [
    //         'slug' => 'target',
    //         'name' => _l('target'),
    //         'href' => admin_url('target/deal'),
    //         'position' => 35,
    //     ]);
    // }
	/*if (has_permission('target', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('sales', [
            'slug' => 'taxes',
            'name' => _l('taxes'),
            'href' => admin_url('taxes'),
            'position' => 40,
        ]);
    }*/

    if (has_permission('subscriptions', '', 'view') || has_permission('subscriptions', '', 'view_own')) {
        $CI->app_menu->add_sidebar_menu_item('subscriptions', [
            'name' => _l('subscriptions'),
            'href' => admin_url('subscriptions'),
            'icon' => 'fa fa-repeat',
            'position' => 15,
        ]);
    }

    if (has_permission('expenses', '', 'view') || has_permission('expenses', '', 'view_own')) {
        $CI->app_menu->add_sidebar_menu_item('expenses', [
            'name' => _l('expenses'),
            'href' => admin_url('expenses'),
            'icon' => 'fa fa-file-text-o',
            'position' => 20,
        ]);
    }
    
    // if (has_permission('products', '', 'view')) {
    //     $CI->app_menu->add_sidebar_menu_item('products', [
    //         'name' => 'Products',
    //         'slug' => 'products',
    //         'href' => admin_url('products'),
    //         'position' => 7,
    //         'icon' => 'fa fa-archive',
    //     ]);
    // }

    $CI->app_menu->add_sidebar_menu_item('sales', [
        'collapse' => true,
        'name' => 'Sales',
        'href' => admin_url('products'),
        'icon' => 'fa fa-balance-scale',
        'position' => 7,
    ]);
    // $CI->app_menu->add_sidebar_children_item('reports', [
    //     'slug' => 'sales-reports',
    //     'name' => _l('als_reports_sales_submenu'),
    //     'href' => admin_url('reports/sales'),
    //     'position' => 5,
    // ]);
    /*$CI->app_menu->add_sidebar_children_item('sales', [
        'slug' => 'Products',
        'name' => 'Products',
        'href' => admin_url('products'),
        'position' => 4,
    ]);*/
    
    
    if (has_permission('contracts', '', 'view') || has_permission('contracts', '', 'view_own')) {
        $CI->app_menu->add_sidebar_menu_item('contracts', [
            'name' => _l('contracts'),
            'href' => admin_url('contracts'),
            'icon' => 'fa fa-file',
            'position' => 25,
        ]);
    }

    if (has_permission('tickets')) {
        if ((!is_staff_member() && get_option('access_tickets_to_none_staff_members') == 1) || is_staff_member()) {
            $CI->app_menu->add_sidebar_menu_item('support', [
                'name' => _l('support'),
                'href' => admin_url('tickets'),
                'icon' => 'fa fa-ticket',
                'position' => 40,
            ]);
        }
    }
    

    if (has_permission('knowledge_base', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('knowledge-base', [
            'name' => _l('als_kb'),
            'href' => admin_url('knowledge_base'),
            'icon' => 'fa fa-folder-open-o',
            'position' => 50,
        ]);
    }



    if (has_permission('utilities')) {
        // Utilities
        $CI->app_menu->add_sidebar_menu_item('utilities', [
            'collapse' => true,
            'name' => _l('als_utilities'),
            'position' => 60,
            'icon' => 'fa fa-cogs',
        ]);

        $CI->app_menu->add_sidebar_children_item('utilities', [
            'slug' => 'media',
            'name' => _l('als_media'),
            'href' => admin_url('utilities/media'),
            'position' => 5,
        ]);

        if (has_permission('bulk_pdf_exporter', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('utilities', [
                'slug' => 'bulk-pdf-exporter',
                'name' => _l('bulk_pdf_exporter'),
                'href' => admin_url('utilities/bulk_pdf_exporter'),
                'position' => 10,
            ]);
        }

        $CI->app_menu->add_sidebar_children_item('utilities', [
            'slug' => 'calendar',
            'name' => _l('als_calendar_submenu'),
            'href' => admin_url('utilities/calendar'),
            'position' => 15,
        ]);


        if (is_admin()) {
            $CI->app_menu->add_sidebar_children_item('utilities', [
                'slug' => 'announcements',
                'name' => _l('als_announcements_submenu'),
                'href' => admin_url('announcements'),
                'position' => 20,
            ]);

            $CI->app_menu->add_sidebar_children_item('utilities', [
                'slug' => 'activity-log',
                'name' => _l('als_activity_log_submenu'),
                'href' => admin_url('utilities/activity_log'),
                'position' => 25,
            ]);

            $CI->app_menu->add_sidebar_children_item('utilities', [
                'slug' => 'ticket-pipe-log',
                'name' => _l('ticket_pipe_log'),
                'href' => admin_url('utilities/pipe_log'),
                'position' => 30,
            ]);
        }
    }
    if (has_permission('reports', '', 'view') || has_permission('reports', '', 'create')) {
       
        $CI->app_menu->add_sidebar_menu_item('reports', [
            'collapse' => true,
            'name' => _l('als_reports'),
            'href' => admin_url('reports'),
            'icon' => 'fa fa-area-chart',
            'position' => 60,
        ]);
        // $CI->app_menu->add_sidebar_children_item('reports', [
        //     'slug' => 'sales-reports',
        //     'name' => _l('als_reports_sales_submenu'),
        //     'href' => admin_url('reports/sales'),
        //     'position' => 5,
        // ]);
        /* $CI->app_menu->add_sidebar_children_item('reports', [
            'slug' => 'Deals',
            'name' => _l('projects'),
            'href' => admin_url('reports/deals'),
            'position' => 5,
        ]);
        $CI->app_menu->add_sidebar_children_item('reports', [
            'slug' => 'Activities',
            'name' => _l('tasks'),
            'href' => admin_url('reports/activities'),
            'position' => 6,
        ]); */
		$CI->app_menu->add_sidebar_children_item('reports', [
            'slug' => 'add-report',
            'name' => _l('add_report'),
            'href' => admin_url('reports/add'),
            'position' => 7,
        ]);
		$CI->app_menu->add_sidebar_children_item('reports', [
            'slug' => 'view-report',
            'name' => _l('view_report'),
            'href' => admin_url('reports/view_deal_folder'),
            'position' => 8,
        ]);
		$CI->app_menu->add_sidebar_children_item('reports', [
            'slug' => 'dashboard-report',
            'name' => _l('dashboard'),
            'href' => admin_url('dashboard/report'),
            'position' => 9,
        ]);
		$CI->app_menu->add_sidebar_children_item('reports', [
            'slug' => 'view-report',
            'name' => _l('shared_list'),
            'href' => admin_url('reports/all_share'),
            'position' => 9,
        ]);

        // $CI->app_menu->add_sidebar_children_item('reports', [
        //     'slug' => 'sales-reports',
        //     'name' => _l('als_reports_sales_submenu'),
        //     'href' => admin_url('reports/sales'),
        //     'position' => 5,
        // ]);
        // $CI->app_menu->add_sidebar_children_item('reports', [
        //     'slug' => 'expenses-reports',
        //     'name' => _l('als_reports_expenses'),
        //     'href' => admin_url('reports/expenses'),
        //     'position' => 10,
        // ]);
        // $CI->app_menu->add_sidebar_children_item('reports', [
        //     'slug' => 'expenses-vs-income-reports',
        //     'name' => _l('als_expenses_vs_income'),
        //     'href' => admin_url('reports/expenses_vs_income'),
        //     'position' => 15,
        // ]);
        // $CI->app_menu->add_sidebar_children_item('reports', [
        //     'slug' => 'leads-reports',
        //     'name' => _l('als_reports_leads_submenu'),
        //     'href' => admin_url('reports/leads'),
        //     'position' => 20,
        // ]);

        // if (is_admin()) {
        //     $CI->app_menu->add_sidebar_children_item('reports', [
        //         'slug' => 'timesheets-reports',
        //         'name' => _l('timesheets_overview'),
        //         'href' => admin_url('staff/timesheets?view=all'),
        //         'position' => 25,
        //     ]);
        // }

        // $CI->app_menu->add_sidebar_children_item('reports', [
        //     'slug' => 'knowledge-base-reports',
        //     'name' => _l('als_kb_articles_submenu'),
        //     'href' => admin_url('reports/knowledge_base_articles'),
        //     'position' => 30,
        // ]);
    }

    if (is_admin()) {
        $CI->app_menu->add_sidebar_menu_item('plugins', [
            'name' => _l('plugins'),
            'href' => admin_url('plugins'),
            'icon' => 'fa fa-plug',
            'position' => 65,
        ]);
    }

    // Setup menu
    if (has_permission('workflow', '', 'edit')) {
        $CI->app_menu->add_setup_menu_item('workflow', [
            'name' => _l('workflow'),
            'href' => admin_url('workflow'),
            //'icon' => 'fa fa-bullhorn',
            'position' => 225,
        ]);
    }
    

if (is_admin()) {
    if (has_permission('staff', '', 'view')) {
    $CI->app_menu->add_setup_menu_item('custom-fields', [
        'href' => admin_url('custom_fields'),
        'name' => _l('asc_custom_fields'),
        'position' => 2,
    ]);
}
    if (has_permission('leads')) {
            $CI->app_menu->add_setup_menu_item('leads', [
                'collapse' => true,
                'name' => _l('acs_leads'),
                'position' => 20,
            ]);
            $CI->app_menu->add_setup_children_item('leads', [
                'slug' => 'leads-sources',
                'name' => 'Source',
                'href' => admin_url('leads/sources'),
                'position' => 5,
            ]);

            $CI->app_menu->add_setup_children_item('leads', [
                'slug' => 'web-to-lead',
                'name' => _l('web_to_lead'),
                'href' => admin_url('leads/forms'),
                'position' => 5,
            ]);
    }

    if (has_permission('staff', '', 'view')) {
        $CI->app_menu->add_setup_menu_item('staff', [
            'name' => _l('als_staff'),
            'href' => admin_url('staff'),
            'position' => 3,
        ]);    
        
        $CI->app_menu->add_setup_children_item('staff', [
            'name' => _l('als_staff'),
            'href' => admin_url('staff'),
            //'icon' => 'fa fa-bullhorn',
            'position' => 5,
        ]);
            $CI->app_menu->add_setup_children_item('staff', [
                'name' => _l('AssignFollowers'),
                'href' => admin_url('AssignFollowers'),
                //'icon' => 'fa fa-bullhorn',
                'position' => 5,
            ]);
      
    
            $CI->app_menu->add_setup_children_item('staff', [
                'name' => _l('AccountTransfer'),
                'href' => admin_url('AccountTransfer'),
                //'icon' => 'fa fa-bullhorn',
                'position' => 5,
            ]);
        
    }

    if (has_permission('settings')) {
            $CI->app_menu->add_setup_menu_item('deal', [
                'collapse' => true,
                'name' => _l('deal'),
                'position' => 2,
            ]);
            $CI->app_menu->add_setup_children_item('deal', [
                'slug' => 'pipeline',
                'name' => _l('als_pipe'),
                'href' => admin_url('pipeline'),
                //'icon' => 'fa fa-bullhorn',
                'position' => 5,
            ]);
            $CI->app_menu->add_setup_children_item('deal', [
                'slug' => 'pipelinestatus',
                'name' => _l('als_pipelinestatus'),
                'href' => admin_url('pipelinestatus'),
                'position' => 5,
            ]);
    if (has_permission('ImportData', '', 'view')) {
                $CI->app_menu->add_setup_children_item('deal', [
                    'name' => _l('ImportData'),
                    'href' => admin_url('ImportData'),
                    //'icon' => 'fa fa-bullhorn',
                    'position' => 3,
                ]);
            }
            $CI->app_menu->add_setup_children_item('deal', [
                'slug' => 'deal-fields',
                'href' => admin_url('deal_fields'),
                'name' => _l('field_settings'),
                'position' => 5,
            ]);             
            $CI->app_menu->add_setup_children_item('deal', [
                'slug' => 'DealLossReasons',
                'name' => _l('loss_reason_name'),
                'href' => admin_url('DealLossReasons'),
                //'icon' => 'fa fa-bullhorn',
                'position' => 5,
            ]);        
            $CI->app_menu->add_setup_children_item('deal', [
                'slug' => 'DealRejectionReasons',
                'name' => _l('RejectionReason'),
                'href' => admin_url('DealRejectionReasons'),
                //'icon' => 'fa fa-bullhorn',
                'position' => 5,
            ]);
            
        }
            if (has_permission('customers')) {
                $CI->app_menu->add_setup_menu_item('customers', [
                    'collapse' => true,
                    'slug' => 'clients',
                    'name' => _l('clients'),
                    'position' => 30,
                ]);

                $CI->app_menu->add_setup_children_item('customers', [
                    'slug' => 'customer-groups',
                    'name' => _l('customer_groups'),
                    'href' => admin_url('clients/groups'),
                    'position' => 5,
                ]);
                $CI->app_menu->add_setup_children_item('customers', [
                    'slug' => 'customer-import',
                    'name' => _l('import_customers'),
                    'href' => admin_url('clients/import'),
                    'position' => 5,
                ]);
            }
            if (has_permission('settings')) {
                $CI->app_menu->add_setup_menu_item('activity', [
                    'collapse' => true,
                    'name' => _l('activity'),
                    'position' => 1,
                ]);
            if (has_permission('tasktype', '', 'view') || has_permission('tasktype', '', 'view_own')) {
                $CI->app_menu->add_setup_children_item('activity', [
                    'name' => _l('activity_type'),
                    'href' => admin_url('tasktype'),
                    'position' => 5,
                ]);
                }
            
                $CI->app_menu->add_setup_children_item('activity', [
                    'href' => admin_url('activity_transfer'),
                    'name' => _l('act_transfer'),
                    'position' => 5,
                ]);
            
        }
        if (has_permission('settings')) {
            $CI->app_menu->add_setup_menu_item('sales', [
                'collapse' => true,
                'name' => _l('als_sales'),
                'position' => 215,
            ]);
        
        
            if (has_permission('settings', '', 'view')) {
                $CI->app_menu->add_setup_children_item('sales', [
                    'href' => admin_url('taxes'),
                    'name' => _l('taxes'),
                    'position' => 5,
                ]);
            }

            $CI->app_menu->add_setup_children_item('sales', [
                'slug' => 'currencies',
                'name' => _l('acs_sales_currencies_submenu'),
                'href' => admin_url('currencies'),
                'position' => 5,
            ]);
            if (has_permission('settings', '', 'view')) {
                $CI->app_menu->add_setup_children_item('sales', [
                    'href' => admin_url('Invoicepdfconfig'),
                    'name' => _l('invoice_pdf_config'),
                    'position' => 5,
                    ]);
            }
        
    }

            if (has_permission('settings')) {
                $CI->app_menu->add_setup_menu_item('sms', [
                    'collapse' => true,
                    'name' => _l('sms'),
                    'position' => 215,
                ]);
                $CI->app_menu->add_setup_children_item('sms', [
                    'slug' => 'sms-templates',
                    'name' =>_l('templates'),
                    'href' => admin_url('plugin/sms'),
                    'position' => 5,
                ]);
                $CI->app_menu->add_setup_children_item('sms', [
                    'slug' => 'sms-daffytel',
                    'name' => 'Daffytel',
                    'href' => admin_url('plugin/sms/daffytel'),
                    'position' => 5,
                ]);
            }

            if (has_permission('settings')) {
                $CI->app_menu->add_setup_menu_item('email', [
                    'collapse' => true,
                    'name' => _l('email'),
                    'position' => 4,
                ]);
            
                $CI->app_menu->add_setup_children_item('email', [
                'href' => admin_url('emails'),
                'name' => _l('templates'),
                'position' => 5,
                ]);
        
    
                $CI->app_menu->add_setup_children_item('email', [
                'href' => admin_url('settings?group=company_settings'),
                'name' => _l('configuration'),
                'position' => 5,
                ]);
        }
        if (has_permission('settings', '', 'view')) {
            $CI->app_menu->add_setup_menu_item('enable_call', [
                'href' => admin_url('call_settings/enable_call'),
                'name' => _l('call_settings'),
                'position' => 6,
            ]);
        }
       
    //
    //        $CI->app_menu->add_setup_menu_item('gdpr', [
    //            'href' => admin_url('gdpr'),
    //            'name' => _l('gdpr_short'),
    //            'position' => 50,
    //        ]);

            // $CI->app_menu->add_setup_menu_item('roles', [
            //     'href' => admin_url('roles'),
            //     'name' => _l('acs_roles'),
            //     'position' => 55,
            // ]);
            // if (has_permission('designation', '', 'view') || has_permission('designation', '', 'view_own')) {
            //     $CI->app_menu->add_setup_menu_item('designation', [
            //         'name' => _l('acs_designation'),
            //         'href' => admin_url('designation'),
            //         'position' => 55,
            //     ]);
            // }
            /*             $CI->app_menu->add_setup_menu_item('api', [
            'href'     => admin_url('api'),
            'name'     => 'API',
            'position' => 65,
            ]); */
        }

	// if (has_permission('settings', '', 'view')) {
    //     $CI->app_menu->add_setup_menu_item('target', [
    //         'href' => admin_url('target'),
    //         'name' => _l('target'),
    //         'position' => 222,
    //     ]);
    // }

    if (has_permission('password_policy', '', 'view')) {
        $CI->app_menu->add_setup_menu_item('password_policy', [
            'name' => _l('password_policy'),
            'href' => admin_url('passwordpolicy'),
            'position' => 205,
        ]);
    }
	if (has_permission('settings', '', 'view')) {
        $CI->app_menu->add_setup_menu_item('reminder', [
            'href' => admin_url('reminder'),
            'name' => _l('reminder_settings'),
            'position' => 210,
        ]);
    }
    
    if (has_permission('settings', '', 'view')) {
        $CI->app_menu->add_setup_menu_item('settings', [
            'href' => admin_url('settings?group=company'),
            'name' => _l('acs_settings'),
            'position' => 220,
        ]);
    }
}
