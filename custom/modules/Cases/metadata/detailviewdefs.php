<?php

// created: 2016-04-08 13:38:25
$viewdefs['Cases']['DetailView'] = array(
    'templateMeta' =>
    array(
        'form' =>
        array(
            'buttons' =>
            array(
                0 => 'EDIT',
                1 => 'DUPLICATE',
                2 => 'DELETE',
                3 => 'FIND_DUPLICATES',
                4 =>
                array(
                    'customCode' => '
                                    <input type="hidden" name="status" value="">
                                    <input type="hidden" name="isSave" value="false">  
                                    
                                    {if $fields.status.value != "Closed"} 
                                    <input title="{$APP.LBL_CLOSE_BUTTON_TITLE}"  accesskey="{$APP.LBL_CLOSE_BUTTON_KEY}"  
                                        class="button"  onclick="this.form.status.value=\'Closed\'; 
                                            this.form.action.value=\'Save\';
                                            this.form.return_module.value=\'Home\';
                                            this.form.return_action.value=\'index\';"  
                                            name="button1"  value="{$APP.LBL_CLOSE_BUTTON_TITLE}"  type="submit">
                                            {/if}',
                ),
                5 =>
                array(
                    'customCode' => '{if $fields.status.value != "pending_customer"} 
                                    <input title="{$APP.LBL_PENDING_CUSTOMER_BUTTON_TITLE}"  accesskey="{$APP.LBL_PENDING_CUSTOMER_BUTTON_TITLE}"  
                                        class="button"  onclick="this.form.status.value=\'pending_customer\'; 
                                            this.form.action.value=\'Save\';
                                            this.form.return_module.value=\'Home\';
                                            this.form.return_action.value=\'index\';"  
                                            name="button2"  value="{$APP.LBL_PENDING_CUSTOMER_BUTTON_TITLE}"  type="submit">
                                            {/if}',
                ),
                6 =>
                array(
                    'customCode' => '{if $fields.status.value != "pending_supplier"} 
                                    <input title="{$APP.LBL_PENDING_SUPPLIER_BUTTON_TITLE}"  accesskey="{$APP.LBL_PENDING_SUPPLIER_BUTTON_TITLE}"  
                                        class="button"  onclick="this.form.status.value=\'pending_supplier\'; 
                                            this.form.action.value=\'Save\';
                                            this.form.return_module.value=\'Home\';
                                            this.form.return_action.value=\'index\';"  
                                            name="button3"  value="{$APP.LBL_PENDING_SUPPLIER_BUTTON_TITLE}"  type="submit">
                                            {/if}',
                ),
            ),
        ),
        'maxColumns' => '2',
        'widths' =>
        array(
            0 =>
            array(
                'label' => '10',
                'field' => '30',
            ),
            1 =>
            array(
                'label' => '10',
                'field' => '30',
            ),
        ),
        'includes' =>
        array(
            0 =>
            array(
                'file' => 'custom/include/js/Cases/complete-wf-tasks.js',
            ),
        ),
        'useTabs' => false,
        'tabDefs' =>
        array(
            'LBL_CASE_INFORMATION' =>
            array(
                'newTab' => false,
                'panelDefault' => 'expanded',
            ),
            'LBL_AOP_CASE_UPDATES' =>
            array(
                'newTab' => false,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_ASSIGNMENT' =>
            array(
                'newTab' => false,
                'panelDefault' => 'expanded',
            ),
        ),
    ),
    'panels' =>
    array(
        'lbl_case_information' =>
        array(
            0 =>
            array(
                0 =>
                array(
                    'name' => 'case_number',
                    'label' => 'LBL_CASE_NUMBER',
                ),
                1 => '',
            ),
            1 =>
            array(
                0 => 'priority',
                1 => 'type',
            ),
            2 =>
            array(
                0 => 'status',
                1 =>
                array(
                    'name' => 'technical_c',
                    'studio' => 'visible',
                    'label' => 'LBL_TECHNICAL',
                ),
            ),
            3 =>
            array(
                0 =>
                array(
                    'name' => 'state',
                    'comment' => 'The state of the case (i.e. open/closed)',
                    'label' => 'LBL_STATE',
                ),
                1 => 'account_name',
            ),
            4 =>
            array(
                0 =>
                array(
                    'name' => 'name',
                    'label' => 'LBL_SUBJECT',
                ),
                1 =>
                array(
                    'name' => 'product_c',
                    'studio' => 'visible',
                    'label' => 'LBL_PRODUCT',
                    'customCode' => '{$PRODUCTS}',
                ),
            ),
            5 =>
            array(
                0 =>
                array(
                    'name' => 'description',
                    'customCode' => '{$DESCRIPTION}',
                ),
                1 =>
                array(
                    'name' => 'review_c',
                    'studio' => 'visible',
                    'label' => 'LBL_REVIEW',
                ),
            ),
            6 =>
            array(
                0 =>
                array(
                    'name' => 'bc_workflow_cases_name',
                    'label' => 'LBL_BC_WORKFLOW_CASES_FROM_BC_WORKFLOW_TITLE',
                ),
                1 =>
                array(
                    'name' => 'supplier_c',
                    'studio' => 'visible',
                    'label' => 'LBL_SUPPLIER',
                    'customCode' => '{$SUPPLIERS}',
                ),
            ),
            7 =>
            array(
                0 => 'resolution',
            ),
        ),
        'lbl_detailview_panel1' =>
        array(
            0 =>
            array(
                0 =>
                array(
                    'name' => 'external_office_c',
                    'studio' => 'visible',
                    'label' => 'LBL_EXTERNAL_OFFICE',
                ),
                1 =>
                array(
                    'name' => 'external_user_name_c',
                    'label' => 'LBL_EXTERNAL_USER_NAME',
                ),
            ),
        ),
        'LBL_AOP_CASE_UPDATES' =>
        array(
            0 =>
            array(
                0 =>
                array(
                    'name' => 'aop_case_updates_threaded',
                    'studio' => 'visible',
                    'label' => 'LBL_AOP_CASE_UPDATES_THREADED',
                ),
            ),
        ),
        'LBL_PANEL_ASSIGNMENT' =>
        array(
            0 =>
            array(
                0 =>
                array(
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO',
                ),
                1 =>
                array(
                    'name' => 'date_modified',
                    'label' => 'LBL_DATE_MODIFIED',
                    'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
                ),
            ),
            1 =>
            array(
                0 =>
                array(
                    'name' => 'date_entered',
                    'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
                ),
            ),
        ),
    ),
);
