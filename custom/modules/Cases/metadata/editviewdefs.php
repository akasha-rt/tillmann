<?php

// created: 2016-04-08 13:46:17
$viewdefs['Cases']['EditView'] = array(
    'templateMeta' =>
    array(
        'form' =>
        array(
            'hidden' =>
            array(
                0 => '<input type="hidden" name="external_user_name_c" id="external_user_name_c" value="{$fields.external_user_name_c.value}">',
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
                'file' => 'custom/include/js/Cases/EditView.js',
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
                    'type' => 'readonly',
                ),
                1 => 'type',
            ),
            1 =>
            array(
                0 => 'priority',
                1 =>
                array(
                    'name' => 'technical_c',
                    'studio' => 'visible',
                    'label' => 'LBL_TECHNICAL',
                ),
            ),
            2 =>
            array(
                0 => 'status',
                1 =>
                array(
                    'name' => 'product_c',
                    'studio' => 'visible',
                    'label' => 'LBL_PRODUCT',
                ),
            ),
            3 =>
            array(
                0 =>
                array(
                    'name' => 'name',
                    'displayParams' =>
                    array(
                        'size' => 75,
                        'required' => false,
                    ),
                ),
                1 =>
                array(
                    'name' => 'review_c',
                    'studio' => 'visible',
                    'label' => 'LBL_REVIEW',
                ),
            ),
            4 =>
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
                    'name' => 'bc_workflow_cases_name',
                    'label' => 'LBL_BC_WORKFLOW_CASES_FROM_BC_WORKFLOW_TITLE',
                ),
                1 =>
                array(
                    'name' => 'supplier_c',
                    'studio' => 'visible',
                    'label' => 'LBL_SUPPLIER',
                ),
            ),
            5 =>
            array(
                0 =>
                array(
                    'name' => 'description',
                    'nl2br' => true,
                ),
                1 =>
                array(
                    'name' => 'resolution',
                    'nl2br' => true,
                ),
            ),
            8 =>
            array(
                0 =>
                array(
                    'name' => 'update_text',
                    'studio' => 'visible',
                    'label' => 'LBL_UPDATE_TEXT',
                ),
                1 =>
                array(
                    'name' => 'internal',
                    'studio' => 'visible',
                    'label' => 'LBL_INTERNAL',
                ),
            ),
        ),
        'lbl_editview_panel1' =>
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
                    'name' => 'external_user_id_c',
                    'studio' => 'visible',
                    'label' => 'LBL_EXTERNAL_USER_ID',
                ),
            ),
        ),
        'LBL_PANEL_ASSIGNMENT' =>
        array(
            0 =>
            array(
                0 => 'assigned_user_name',
                1 =>
                array(
                    'name' => 'case_update_form',
                    'studio' => 'visible',
                ),
            ),
            1 =>
            array(
                0 =>
                array(
                    'name' => 'suggestion_box',
                    'label' => 'LBL_SUGGESTION_BOX',
                ),
            ),
        ),
    ),
);
